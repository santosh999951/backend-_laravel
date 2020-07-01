<?php
/**
 * Coupon service containing coupon and wallet usage related methods
 */

namespace App\Libraries\v1_6;

use App;
use \Carbon\Carbon;
use DB;
use App\Libraries\Helper;

use App\Models\Coupon;
use App\Models\WalletTransaction;
use App\Models\CouponUsage;
use App\Models\BookingRequest;
use App\Models\User;

/**
  Contain methods related to coupons and wallet use
 */
class CouponService
{


    /**
     * Check if the access token has been revoked.
     *
     * @param array $params Contain array of data to check coupon validity.
     *
     * @return array Return array containing coupon validity status (if valid, coupon discount data)
     */
    public static function checkCouponValidity(array $params)
    {
        $gh_discount_amount   = 0;
        $host_discount_amount = 0;
        $discount_percentage  = 0;
        $coupon_details       = '';
        $total_discount       = 0;

        $coupon_code    = $params['coupon_code'];
        $property_city  = $params['property_city'];
        $property_state = $params['property_state'];
        $currency       = $params['booking_currency'];
        $booking_amount = $params['booking_amount'];
        $host_fee       = $params['host_fee'];

        $user_id            = $params['user_id'];
        $gh_commission      = $params['gh_commission'];
        $is_mobile_app      = $params['is_mobile_app'];
        $property_type      = $params['property_type'];
        $from_date          = Carbon::parse($params['from_date'])->format('Y-m-d');
        $to_date            = Carbon::parse($params['to_date'])->format('Y-m-d');
        $booking_request_id = (isset($params['booking_request_id']) === true) ? $params['booking_request_id'] : 0;

        if ($booking_amount <= 0) {
            return [
                'status'               => 0,
                'total_discount'       => 0,
                'host_discount_amount' => 0,
                'gh_discount_amount'   => 0,
                'message'              => 'Oops! This coupon is not applicable for this booking. Please refer to coupon T&C.',
            ];
        }

        // Check if coupon code is valid.
        $coupon = Coupon::where('coupon_code', '=', $coupon_code)->first();

        if ($coupon === null) {
            return [
                'status'               => 0,
                'total_discount'       => 0,
                'host_discount_amount' => 0,
                'gh_discount_amount'   => 0,
                'message'              => 'Invalid coupon. This coupon does not exist.',
            ];
        }

        $current_datetime = strtotime(Carbon::now('Asia/Kolkata')->toDateTimeString());

        if (($current_datetime >= strtotime($coupon->coupon_start_time) && $current_datetime <= strtotime($coupon->valid_till)) === false) {
            return [
                'status'               => 0,
                'total_discount'       => 0,
                'host_discount_amount' => 0,
                'gh_discount_amount'   => 0,
                'message'              => 'This coupon is no longer valid.',
            ];
        }

        if ($coupon->state !== '' || $coupon->country !== '' || $coupon->city !== '') {
            $allowed_state = explode(',', strtolower($coupon->state));
            $allowed_city  = explode(',', strtolower($coupon->city));

            // If property's state or city is valid for applied coupon.
            if (in_array(strtolower($property_city), $allowed_city) === false && in_array(strtolower($property_state), $allowed_state) === false) {
                return [
                    'status'               => 0,
                    'total_discount'       => 0,
                    'host_discount_amount' => 0,
                    'gh_discount_amount'   => 0,
                    'message'              => 'This coupon is not valid for this city/state.',
                ];
            }
        }

        // Check coupon usage.
        $coupon_used_count = CouponUsage::where('coupon_id', '=', $coupon->id)->where('booking_request_id', '!=', $booking_request_id)->count();

        if ($coupon->max_usage_count <= $coupon_used_count) {
            return [
                'status'               => 0,
                'total_discount'       => 0,
                'host_discount_amount' => 0,
                'gh_discount_amount'   => 0,
                'message'              => "This coupon's usage limit is exhausted.",
            ];
        }

        // If user logged in.
        if ($user_id !== 0) {
            $coupon_used_by_user_count = CouponUsage::where('coupon_id', '=', $coupon->id)->where('used_by', '=', $user_id)->where('booking_request_id', '!=', $booking_request_id)->count();

            if ($coupon->limit_per_user <= $coupon_used_by_user_count) {
                return [
                    'status'               => 0,
                    'total_discount'       => 0,
                    'host_discount_amount' => 0,
                    'gh_discount_amount'   => 0,
                    'message'              => "You've alreday used this coupon. Coupon usage limit exceeded.",
                ];
            }

            // Check if this is user's first booking.
            if (strtoupper($coupon_code) === 'GHNEW') {
                $booking_count = BookingRequest::where('traveller_id', $user_id)->where('booking_status', BOOKED)->count();
                if ($booking_count > 0) {
                    return [
                        'status'               => 0,
                        'total_discount'       => 0,
                        'host_discount_amount' => 0,
                        'gh_discount_amount'   => 0,
                        'message'              => 'This coupon is valid only on your first booking with GuestHouser.',
                    ];
                }
            }
        }//end if

        if ($coupon->property_type !== '') {
            $coupon_property_type = explode(',', $coupon->property_type);

            if (in_array($property_type, $coupon_property_type) === false) {
                return [
                    'status'               => 0,
                    'total_discount'       => 0,
                    'host_discount_amount' => 0,
                    'gh_discount_amount'   => 0,
                    'message'              => 'This coupon is not valid for this property type.',
                ];
            }
        }

        // Removed ESCAPE10 coupon check present in website.
        // For min nigths discounts.
        if ($coupon->min_nights > 0) {
            $total_bookings_count = round(abs(strtotime($to_date) - strtotime($from_date)) / 86400);
            if ($total_bookings_count < $coupon->min_nights) {
                return [
                    'status'               => 0,
                    'total_discount'       => 0,
                    'host_discount_amount' => 0,
                    'gh_discount_amount'   => 0,
                    'message'              => "You have to book this property for a minimum of $coupon->min_nights nights for this coupon to be valid.",
                ];
            }
        }

        // For check in and checkout.
        if ($coupon->checkin !== '0000-00-00' && $coupon->checkout !== '0000-00-00') {
            if ((strtotime($from_date) >= strtotime($coupon->checkin) && strtotime($to_date) <= strtotime($coupon->checkout)) === false) {
                return [
                    'status'               => 0,
                    'total_discount'       => 0,
                    'host_discount_amount' => 0,
                    'gh_discount_amount'   => 0,
                    'message'              => 'This coupon is not valid for this booking.',
                ];
            }
        }

        // Check app only.
        if ($coupon->is_app_only === 1 && $is_mobile_app === false) {
            return [
                'status'               => 0,
                'total_discount'       => 0,
                'host_discount_amount' => 0,
                'gh_discount_amount'   => 0,
                'message'              => 'This coupon is valid only on the GuestHouser Mobile App. Download it here: http://www.guesthouser.com/app',
            ];
        }

        // Removed FEST22 coupon check present in website.
        // For money coupon.
        if ($coupon->coupon_type === MONEY_COUPON) {
            // If money coupon then it can be applied on transaction in same currency for which coupon is created.
            if ($currency !== $coupon->currency) {
                return [
                    'status'               => 0,
                    'total_discount'       => 0,
                    'host_discount_amount' => 0,
                    'gh_discount_amount'   => 0,
                    'message'              => 'This coupon is invalid due to currency mismatch.',
                ];
            }

            // Check minimum transaction rule.
            if ($booking_amount <= $coupon->min_transaction_value) {
                return [
                    'status'               => 0,
                    'total_discount'       => 0,
                    'host_discount_amount' => 0,
                    'gh_discount_amount'   => 0,
                    'message'              => 'This coupon is valid only on a minimum booking amount of '.Helper::getFormattedMoney($coupon->min_transaction_value, $coupon->currency).'.',
                ];
            }

            // Rk for cpn discount shuld be less than booking amout.
            $total_cpn_discount = ($coupon->value + $coupon->host_value);
            if ($booking_amount <= $total_cpn_discount) {
                // Adjust it.
                $coupon->host_value = ($host_fee > $coupon->host_value) ? $coupon->host_value : $host_fee;
                $coupon->value      = ($booking_amount > $coupon->host_value) ? min(($booking_amount - $coupon->host_value), $coupon->value) : 0;
            }

            // Removed FB campaign coupons check present in website.
            $host_discount_amount = $coupon->host_value;
            $gh_discount_amount   = $coupon->value;
            $discount_percentage  = 0;
            $total_discount       = ($host_discount_amount + $gh_discount_amount);
        }//end if

        // Custom percentage type coupon.
        if ($coupon->coupon_type === PERCENTAGE_COUPON) {
            if ($coupon->max_discount > 0 && $currency !== $coupon->currency) {
                return [
                    'status'               => 0,
                    'total_discount'       => 0,
                    'host_discount_amount' => 0,
                    'gh_discount_amount'   => 0,
                    'message'              => 'This coupon is invalid due to currency mismatch.',
                ];
            }

            $gh_coupon_value      = ($booking_amount * ($coupon->value / 100));
            $host_coupon_value    = ($booking_amount * ($coupon->host_value / 100));
            $total_discount_value = ($gh_coupon_value + $host_coupon_value);

            // Check maximum applicable discount value.
            if ($coupon->max_discount > 0) {
                $total_discount_value = ($coupon->max_discount < $total_discount_value) ? $coupon->max_discount : $total_discount_value;
                $host_discount_amount = ($total_discount_value > $host_coupon_value) ? $host_coupon_value : $total_discount_value;
                $gh_discount_amount   = ($total_discount_value > $host_coupon_value) ? ($total_discount_value - $host_coupon_value) : 0;
            }

            if ($booking_amount <= $total_discount_value) {
                $host_coupon_value  = ($host_fee > $host_coupon_value) ? $host_coupon_value : $host_fee;
                $gh_discount_amount = ($booking_amount > $host_coupon_value) ? min(($booking_amount - $host_coupon_value), $gh_discount_amount) : 0;
            }

            $discount_percentage = ($coupon->value + $coupon->host_value);
            $total_discount      = ($host_discount_amount + $gh_discount_amount);
        }//end if

        if ($coupon->is_cashback_coupon === 1) {
            $coupon_details = '';

            if (($coupon->coupon_cashback_type) === PERCENTAGE_COUPON) {
                $cashback_amount = (($booking_amount * $coupon->cashback_percentage) / 100);

                $netcashback_amount = (($booking_amount - $total_discount) > $cashback_amount) ? $cashback_amount : ($booking_amount - $total_discount);

                if ($netcashback_amount > 0) {
                    $new_cashback_percentage = round((($netcashback_amount / $booking_amount) * 100), 1);
                    $coupon_details         .= '+ '.$coupon->cashback_percentage.'% cashback of booking amount.';
                }
            } else {
                // Cashback percentage represent in this case is amount not percentage.
                $cashback_amount    = $coupon->cashback_percentage;
                $netcashback_amount = (($booking_amount - $total_discount) > $cashback_amount) ? $cashback_amount : ($booking_amount - $total_discount);

                $coupon_details .= Helper::getFormattedMoney($netcashback_amount, CURRENCY_SYMBOLS[$currency]['webicon']).' cashback in wallet';
            }
        }//end if

        return [
            'status'               => 1,
            'message'              => rtrim($coupon_code.' applied successfully '.$coupon_details),
            'host_discount_amount' => $host_discount_amount,
            'gh_discount_amount'   => $gh_discount_amount,
            'total_discount'       => round($host_discount_amount + $gh_discount_amount),
            'discount_percentage'  => $discount_percentage,
            'coupon_code'          => $coupon_code,
            'coupon_id'            => $coupon->id,
        ];

    }//end checkCouponValidity()


    /**
     * Check if the access token has been revoked.
     *
     * @param float   $payable_amount  Total payable amount.
     * @param string  $currency        Booking currency code string.
     * @param integer $user_id         User id.
     * @param float   $wallet_balance  User usable wallet balance.
     * @param string  $wallet_currency User wallet currency.
     *
     * @return array Wallet validity status and message.
     */
    public static function checkWalletDiscount(float $payable_amount, string $currency, int $user_id, float $wallet_balance, string $wallet_currency)
    {
        $op = [
            'amount'          => 0,
            'message'         => '',
            'status'          => 0,
            'currency'        => $wallet_currency,
            'currency_symbol' => helper::getCurrencySymbol($wallet_currency),
        ];

        if ($wallet_balance <= 0) {
            $op['message'] = 'No wallet balance available.';
            return $op;
        }

        if ($payable_amount <= 0) {
            $op['message'] = 'Wallet can not be applied.';
            return $op;
        }

        if ($wallet_currency !== $currency) {
            $op['message'] = 'Wallet money only applicable for payment in '.$wallet_currency;
            return $op;
        }

        $max_applicable_wallet_money = self::applicableWalletBalance($payable_amount, $wallet_balance, $user_id, $wallet_currency);

        $op['status']  = 1;
        $op['amount']  = $max_applicable_wallet_money;
        $op['message'] = 'Wallet money applied successfully';

        return $op;

    }//end checkWalletDiscount()


    /**
     * Check if the access token has been revoked.
     *
     * @param float   $payable_amount  Booking payable amount.
     * @param float   $wallet_balance  Wallet balance amount.
     * @param integer $user_id         User id.
     * @param string  $wallet_currency User wallet currency.
     *
     * @return float Return applicable wallet amount.
     */
    public static function applicableWalletBalance(float $payable_amount, float $wallet_balance, int $user_id, string $wallet_currency)
    {
        // Set maximum applicable wallet amount for a payment.
        $max_applicable_wallet_money = floor((MAX_REDEEMABALE_WALLET_MONEY_PERCENTAGE / 100) * $payable_amount);
        $money = ($max_applicable_wallet_money <= $wallet_balance) ? $max_applicable_wallet_money : $wallet_balance;

        $money = Helper::convertPriceToCurrentCurrency($wallet_currency, $money, 'INR');
        $money = ($money > DAILY_WALLET_USAGE_LIMIT) ? DAILY_WALLET_USAGE_LIMIT : $money;

        $referal_wallet = WalletTransaction::where('user_id', '=', $user_id)->where('created_at', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 1 DAY)'))->where('type', '=', 'OUT')->select(DB::raw('sum(amount) as used_amount'))->first();

        if (empty($referal_wallet) === false && empty($referal_wallet->used_amount) === false) {
            $applicable_wallet_amount = Helper::convertPriceToCurrentCurrency($wallet_currency, $referal_wallet->used_amount, 'INR');

            if ($referal_wallet->used_amount >= DAILY_WALLET_USAGE_LIMIT) {
                $applicable_wallet_amount = 0;
            } else {
                $applicable_wallet_amount = (DAILY_WALLET_USAGE_LIMIT - $referal_wallet->used_amount >= $money) ? $money : (DAILY_WALLET_USAGE_LIMIT - $referal_wallet->used_amount);
            }
        } else {
            $applicable_wallet_amount = $money;
        }

        if ($wallet_currency !== 'INR') {
            $applicable_wallet_amount = Helper::convertPriceToCurrentCurrency('INR', $money, $wallet_currency);
        } else {
            $applicable_wallet_amount = $money;
        }

        return $applicable_wallet_amount;

    }//end applicableWalletBalance()


    /**
     * Get released payment amount.
     *
     * @param User $user User Model.
     *
     * @return float Return released payment.
     */
    public static function getReleasedPayment(User $user)
    {
        if (empty($user) === true) {
            return 0;
        }

        $released_credits = $user->booking_credits;
        if ($released_credits > 0) {
            return $released_credits;
        }

        return 0;

    }//end getReleasedPayment()


}//end class
