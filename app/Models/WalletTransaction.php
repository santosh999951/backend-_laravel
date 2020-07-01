<?php
/**
 * Model containing data regarding wallet transactions
 */

namespace App\Models;

use DB;

use \Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Libraries\Helper;
use App\Models\User;
use App\Libraries\v1_6\UserService;

use App\Libraries\CommonQueue;

use App\Events\WalletUpdation;
/**
 * Class WalletTransaction
 */
class WalletTransaction extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'wallet_transactions';

    /**
     * Variable definition.
     *
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * User wallet table relationship.
     *
     * @return object
     */
    public function user()
    {
        return $this->belongsTo(User::class);

    }//end user()


    /**
     * Add money to user wallet.
     *
     * @param integer $event  Event for which amount is to be added.
     * @param array   $params Params required for wallet table entry.
     *
     * @return array
     */
    public static function addWalletMoney(int $event, array $params)
    {
        $user = User::find($params['user_id']);

        $wallet_money = self::calculateAmountToAdd($user->wallet_currency, $user->wallet_balance, $event, $params);

        if ($wallet_money['add_money'] === 0) {
            return [
                'success'      => 0,
                'currency'     => $user->wallet_currency,
                'amount_added' => 0,
                'message'      => 'Sorry! Your wallet balance have reached a maximum limit.',
            ];
        }

        $property_id = ($params['property_id'] ?? 0);

        $user->wallet_balance        = ($user->wallet_balance + $wallet_money['add_money']);
        $user->usable_wallet_balance = ($user->usable_wallet_balance + $wallet_money['add_money']);
        $user->save();

        $transaction_type   = 'IN';
        $transaction_source = 'app';

        $wallet_transaction                = new self;
        $wallet_transaction->user_id       = $params['user_id'];
        $wallet_transaction->type          = $transaction_type;
        $wallet_transaction->amount        = $wallet_money['add_money'];
        $wallet_transaction->usable_amount = $wallet_money['add_money'];
        $wallet_transaction->event         = $event;
        $wallet_transaction->expire_on     = Carbon::now('GMT')->addMonths(6)->toDateTimeString();
        $wallet_transaction->currency      = $user->wallet_currency;
        $wallet_transaction->source        = $transaction_source;

        if (isset($params['request_id']) === true) {
            $wallet_transaction->booking_request_id = $params['request_id'];
        }

        if (isset($wallet_money['review_details']) === true) {
            $wallet_transaction->review_details = json_encode($wallet_money['review_details']);
        }

        if (isset($params['from_id']) === true) {
            $wallet_transaction->from_id = $params['from_id'];
        }

        $wallet_transaction->save();
        $property_link  = '';
        $property_title = '';

        if (isset($params['property_id']) === true) {
            $property       = Property::find($params['property_id']);
            $property_link  = MAILER_SITE_URL.'/properties/rooms/'.$property->id;
            $property_title = $property->title;
        }

        if (isset($params['referral_user_id']) === true) {
            $referral_user = User::find($params['referral_user_id']);
            $referal_name  = ucfirst($referral_user->name);
        } else {
            $referal_name = '';
        }

        // Integrating event and listner for wallet mails.
        if (in_array($event, [TRIP_AND_REVIEW, REFERRAL_BONUS, FRIEND_REFERRAL_BONUS, REFERRAL_FIRST_BOOKING_BONUS]) === true) {
            $wallet_updation_event = new WalletUpdation($wallet_transaction, $user, $referal_name, $property_link, $property_title);
            Event::dispatch($wallet_updation_event);
        }

        // Write code for mailers and sms for transaction.
        return [

            'success'      => 1,
            'currency'     => $user->wallet_currency,
            'amount_added' => $wallet_money['add_money'],
            'message'      => 'Wallet money added successfully.',
        ];

    }//end addWalletMoney()


    /**
     * Add money to user wallet.
     *
     * @param string  $wallet_currency User wallet currency.
     * @param float   $wallet_balance  User wallet balance.
     * @param integer $event           Amount for which amount to add.
     * @param array   $params          Params required for wallet table entry.
     *
     * @return array
     */
    public static function calculateAmountToAdd(string $wallet_currency, float $wallet_balance, int $event, array $params)
    {
        $add_money      = 0;
        $wallet_money   = 0;
        $review_details = [];

        switch ($event) {
            case INVITE_PHONE_CONTACT:
            case REFERRAL_BONUS:
            case FRIEND_REFERRAL_BONUS:
            case REFERRAL_FIRST_BOOKING_BONUS:
                    $wallet_money = $params['wallet_money'];
            break;

            case COUPON_CASHBACK:
                $cashback_currency = $params['currency'];
                $wallet_money      = $params['amount'];
            break;

            case EARLYBIRD_BOOKING_CASHBACK:
                $cashback_currency = $params['pay_currency'];
                $wallet_money      = $params['amount'];
            break;

            case TRIP_AND_REVIEW:
                $booking_request = BookingRequest::find($params['request_id']);
                $price_details   = json_decode($booking_request->price_details);
                $amount          = Helper::convertPriceToCurrentCurrency($price_details->currency_code, $price_details->payable_amount, $wallet_currency);

                $max_cashback_wallet_currency = Helper::convertPriceToCurrentCurrency('INR', MAX_CASHBACK_FOR_REVIEW, $wallet_currency);
                $cashback = round(($amount * REVIEW_CASHBACK_PERCENTAGE / 100), 2);
                $cashback = ($cashback > $max_cashback_wallet_currency) ? $max_cashback_wallet_currency : $cashback;

                $review_amount = Helper::convertPriceToCurrentCurrency('INR', $params['wallet_money'], $wallet_currency);
                $wallet_money  = $review_amount;

                $review_details = [
                    'review_money' => $review_amount,
                    'cashback'     => $cashback,
                ];
            break;

            default:
                $add_money      = 0;
                $wallet_money   = 0;
                $review_details = [];
            break;
        }//end switch

        $max_wallet_amount = constant('MAXIMUM_WALLET_MONEY_'.$wallet_currency);

        if ($wallet_balance >= $max_wallet_amount) {
            return [
                'add_money'      => 0,
                'review_details' => $review_details,
            ];
        }

        if (($wallet_balance + $wallet_money) > $max_wallet_amount) {
            return [
                'add_money'      => ($max_wallet_amount - $wallet_balance),
                'review_details' => $review_details,
            ];
        } else {
            return [
                'add_money'      => $wallet_money,
                'review_details' => $review_details,
            ];
        }

    }//end calculateAmountToAdd()


    /**
     * Deduct money from user's waller in case of bookingor expiry
     *
     * @param array $params Remove wallet params.
     *
     * @return void.
     */
    public static function removeWalletMoney(array $params)
    {
        $event              = 'OUT';
        $user               = User::withTrashed()->find($params['user_id']);
        $wallet_transaction = new WalletTransaction;
        switch ($params['event']) {
            case APPLY_WALLET_MONEY:
                $wallet_transaction->user_id            = $params['user_id'];
                $wallet_transaction->booking_request_id = $params['request_id'];
                $wallet_transaction->type               = $event;
                $wallet_transaction->amount             = $params['amount'];
                $wallet_transaction->event              = APPLY_WALLET_MONEY;
                $wallet_transaction->source             = 'web';
                $wallet_transaction->usable_amount      = 0;
                $wallet_transaction->currency           = $user->wallet_currency;
                $wallet_transaction->save();

                $user->wallet_balance = ($user->wallet_balance - $params['amount']);
                $user->save();

                // Update separate chunks balance.
                self::updateRemovableAmount($params['user_id'], $params['amount']);
            break;

            default:
                // Empty case for now.
            break;
        }//end switch

        if ($params['event'] === APPLY_WALLET_MONEY) {
            $wallet_updation_event = new WalletUpdation($wallet_transaction, $user, '', $params['property_link'], $params['property_title']);
            Event::dispatch($wallet_updation_event);
        }

    }//end removeWalletMoney()


    /**
     * Update removable amount.
     *
     * @param string $user_id User.
     * @param float  $amount  Amount.
     *
     * @return void
     */
    public static function updateRemovableAmount(string $user_id, float $amount)
    {
        $transactions      = self::where('user_id', $user_id)->where('expire', 0)->orderBy('expire_on', 'asc')->get();
        $amount_left       = $amount;
        $update_array      = [];
        $transaction_count = count($transactions);
        for ($i = 0; $i < $transaction_count; $i++) {
            if (empty($amount_left) === true) {
                break;
            }

            //phpcs:ignore
            $used_amount    = ($transactions[$i]->usable_amount - $amount_left) >= 0 ? $amount_left : $transactions[$i]->usable_amount;
            $amount_left   -= $used_amount;
            $update_array[] = [
                'id'            => $transactions[$i]->id,
                'usable_amount' => ($transactions[$i]->usable_amount - $used_amount),
            ];
        }

        // Update entries.
        $updated_count = count($update_array);
        for ($i = 0; $i < $updated_count; $i++) {
            self::where('id', $update_array[$i]['id'])->update(
                ['usable_amount' => $update_array[$i]['usable_amount']]
            );
        }

    }//end updateRemovableAmount()


    /**
     * Add wallet money on referral sigup.
     *
     * @param User $user User.
     *
     * @return void
     */
    public static function creditWalletMoneyOnReferralSignUp(User $user)
    {
        $referal_wallet = self::where('user_id', '=', $user->id)->where('event', '=', REFERRAL_BONUS)->where('from_id', '=', $user->referral_by)->get()->toArray();
        if (empty($referal_wallet) === true) {
            $details['user_id']          = $user->id;
            $details['from_id']          = $user->referral_by;
            $details['wallet_money']     = MAX_TOTAL_MONEY_FOR_REFERRAL;
            $details['referral_user_id'] = $user->id;
            self::addWalletMoney(REFERRAL_BONUS, $details);
        }

        $referal_wallet_friend = self::where('user_id', '=', $user->referral_by)->where('event', '=', FRIEND_REFERRAL_BONUS)->where('from_id', '=', $user->id)->get()->toArray();
        if (empty($referal_wallet_friend) === true) {
            $userreferdata = User::find($user->referral_by);
            //phpcs:ignore
            $sum_wallet_money = self::where('user_id', '=', $user->referral_by)->whereBetween('created_at', [date('Y-m-d', strtotime(date('Y-m-d h:i:s').' -1 month')), date('Y-m-d h:i:s')])->select(DB::raw('sum(amount) as totalamount'))->get()->toArray();
            $sum_wallet_money[0]['totalamount'] = $sum_wallet_money[0]['totalamount'];
            if ($sum_wallet_money[0]['totalamount'] < MAXIMUM_WALLET_MONEY_EARNED_MONTH) {
                $max_referr_amount                   = self::where('user_id', '=', $user->referral_by)->where('event', '=', FRIEND_REFERRAL_BONUS)->select(DB::raw('sum(amount) as totalamount'))->get()->toArray();
                $max_referr_amount[0]['totalamount'] = $max_referr_amount[0]['totalamount'];

                if ($max_referr_amount[0]['totalamount'] < MAXIMUM_WALLET_MONEY_EARNED_FOR_REFERRER) {
                    if ((MAXIMUM_WALLET_MONEY_EARNED_FOR_REFERRER - $max_referr_amount[0]['totalamount']) >= MAX_MONEY_FOR_FRIEND_BONUS) {
                        $walletmoney = MAX_MONEY_FOR_FRIEND_BONUS;
                    } else {
                        $walletmoney = (MAXIMUM_WALLET_MONEY_EARNED_FOR_REFERRER - $max_referr_amount[0]['totalamount']);
                    }

                    $details['user_id']          = $user->referral_by;
                    $details['from_id']          = $user->id;
                    $details['wallet_money']     = $walletmoney;
                    $details['referral_user_id'] = $user->id;

                    self::addWalletMoney(FRIEND_REFERRAL_BONUS, $details);
                }
            }
        }//end if

    }//end creditWalletMoneyOnReferralSignUp()


    /**
     * Add referral money to user wallet.
     *
     * @param integer $traveller_id Referral user id.
     * @param integer $request_id   Booking Request id.
     *
     * @return void.
     */
    public static function giveReferralMoney(int $traveller_id, int $request_id)
    {
        $bookingcount = Booking::where('traveller_id', '=', $traveller_id)->count();
        if ($bookingcount === 1) {
            $userdata = User::find($traveller_id);
            if ($userdata->referral_by !== '') {
                $userreferdata  = User::find($userdata->referral_by);
                $sumwalletmoney = self::where('user_id', '=', $userdata->referral_by)->whereBetween(
                    'created_at',
                    [
                        date(
                            'Y-m-d',
                            strtotime(date('Y-m-d h:i:s').' -1 month')
                        ),
                        date('Y-m-d h:i:s'),
                    ]
                )->select(DB::raw('sum(amount) as totalamount'))->get()->toArray();

                if ($userreferdata->wallet_currency === 'USD') {
                    $sumwalletmoney[0]['totalamount'] = Helper::convertPriceToCurrentCurrency('USD', $sumwalletmoney[0]['totalamount'], 'INR');
                    ;
                }

                if ($sumwalletmoney[0]['totalamount'] < MAXIMUM_WALLET_MONEY_EARNED_MONTH) {
                    $max_referr_amount = self::where('user_id', '=', $userdata->referral_by)->where('event', '=', REFERRAL_FIRST_BOOKING_BONUS)->select(DB::raw('sum(amount) as totalamount'))->get()->toArray();

                    if ($userreferdata->wallet_currency === 'USD') {
                        $max_referr_amount[0]['totalamount'] = Helper::convertPriceToCurrentCurrency('USD', $max_referr_amount[0]['totalamount'], 'INR');
                        ;
                    }

                    if ($max_referr_amount[0]['totalamount'] < MAXIMUM_WALLET_MONEY_EARNED_FOR_REFERRER_BOOKING) {
                        if ((MAXIMUM_WALLET_MONEY_EARNED_FOR_REFERRER_BOOKING - $max_referr_amount[0]['totalamount']) >= MAX_MONEY_FOR_FIRST_BOOKING) {
                            $money = MAX_MONEY_FOR_FIRST_BOOKING;
                        } else {
                            $money = (MAXIMUM_WALLET_MONEY_EARNED_FOR_REFERRER_BOOKING - $max_referr_amount[0]['totalamount']);
                        }

                        $details['user_id']          = $userdata->referral_by;
                        $details['request_id']       = $request_id;
                        $details['wallet_money']     = $money;
                        $details['referral_user_id'] = $traveller_id;

                        self::addWalletMoney(REFERRAL_FIRST_BOOKING_BONUS, $details);
                    }
                }//end if
            }//end if
        }//end if

    }//end giveReferralMoney()


}//end class
