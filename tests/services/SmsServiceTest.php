<?php
/**
 * SmsServiceTest containing tests for SmsService.
 */

use App\Libraries\Helper;
use Carbon\Carbon;
use App\Libraries\v1_6\SmsService;
use App\Jobs\SendSms;
use Illuminate\Support\Facades\Queue;

/**
 * Class SmsServiceTest
 *
 * @group Services
 */
class SmsServiceTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Setup.
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();
        // Sms service mock, Use this to call functions.
        $this->mocked_sms_service = $this->mock(SmsService::class);
        Queue::fake();

    }//end setup()


    /**
     * Test it calls send sms with proper msg.
     *
     * @return void
     */
    public function test_function_send_added_wallet_money_for_trip_review_sms_should_call_sendsms()
    {
        $user = $this->createUser();

        $dial_code = $user->dial_code;
        $to_no     = $user->contact;

        $amount_currency = DEFAULT_CURRENCY;
        $amount_added    = 1000;
        $added_on_date   = '25-02-2019';
        $wallet_currency = DEFAULT_CURRENCY;
        $wallet_balance  = 1000;
        $sender_id       = DEFAULT_SMS_SENDER_ID;

        // phpcs:ignore
        $msg = "We have added $amount_currency $amount_added in your wallet for your accommodation review. The balance in your wallet as of $added_on_date is $wallet_currency $wallet_balance";

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendAddedWalletMoneyForTripReviewSms($dial_code, $to_no, $amount_currency, $amount_added, $added_on_date, $wallet_currency, $wallet_balance);

    }//end test_function_send_added_wallet_money_for_trip_review_sms_should_call_sendsms()


    /**
     * Test it calls send sms with proper msg.
     *
     * @return void
     */
    public function test_function_send_added_wallet_money_for_referal_sms_should_call_sendsms()
    {
        $user = $this->createUser();

        $dial_code = $user->dial_code;
        $to_no     = $user->contact;

        $amount_currency = DEFAULT_CURRENCY;
        $amount_added    = 1000;
        $wallet_currency = DEFAULT_CURRENCY;
        $wallet_balance  = 1000;

        // phpcs:ignore
        $msg = "Yay! GuestHouser has added $amount_currency $amount_added to your wallet. Your current balance is $wallet_currency $wallet_balance  Visit www.guesthouser.com and start exploring!";
         $sender_id = 'GHBOOK';

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendAddedWalletMoneyForReferalBonusSms($dial_code, $to_no, $amount_currency, $amount_added, $wallet_currency, $wallet_balance);

    }//end test_function_send_added_wallet_money_for_referal_sms_should_call_sendsms()


    /**
     * Test it calls send sms with proper msg.
     *
     * @return void
     */
    public function test_function_send_added_wallet_money_for_friend_referal_sms_should_call_sendsms()
    {
        $user = $this->createUser();

        $dial_code = $user->dial_code;
        $to_no     = $user->contact;

        $amount_currency = DEFAULT_CURRENCY;
        $amount_added    = 1000;
        $wallet_currency = DEFAULT_CURRENCY;
        $wallet_balance  = 1000;
        $expire_on       = Carbon::now('GMT')->addMonths(6)->toDateTimeString();

        // phpcs:ignore
        $msg = "Yay! GuestHouser has added $amount_currency $amount_added to your wallet. Your current balance is $wallet_currency $wallet_balance and will expire on $expire_on. Keep referring! ";
        $sender_id = 'GHBOOK';

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendAddedWalletMoneyForFriendReferalBonusSms($dial_code, $to_no, $amount_currency, $amount_added, $wallet_currency, $wallet_balance, $expire_on);

    }//end test_function_send_added_wallet_money_for_friend_referal_sms_should_call_sendsms()


    /**
     * Test it calls send sms with proper msg.
     *
     * @return void
     */
    public function test_function_send_added_wallet_money_for_first_booking_bonus_sms_should_call_sendsms()
    {
        $user = $this->createUser();

        $dial_code = $user->dial_code;
        $to_no     = $user->contact;

        $amount_currency = DEFAULT_CURRENCY;
        $amount_added    = 1000;
        $wallet_currency = DEFAULT_CURRENCY;
        $wallet_balance  = 1000;
        $expire_on       = Carbon::now('GMT')->addMonths(6)->toDateTimeString();

        // phpcs:ignore
        $msg = "Yay! GuestHouser has added $amount_currency $amount_added to your wallet. Your current balance is $wallet_currency $wallet_balance and will expire on $expire_on. Keep referring! ";
        $sender_id = 'GHBOOK';

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendAddedWalletMoneyForFirstBookingBonusSms($dial_code, $to_no, $amount_currency, $amount_added, $wallet_currency, $wallet_balance, $expire_on);

    }//end test_function_send_added_wallet_money_for_first_booking_bonus_sms_should_call_sendsms()


        /**
         * Test it calls send sms with proper msg.
         *
         * @return void
         */
    public function test_function_send_update_wallet_money_for_apply_wallet_sms_should_call_sendsms()
    {
        $booking            = $this->createBookingRequests(BOOKED);
        $user               = $booking['traveller'];
        $booking_request    = $booking['booking_request'];
        $booking_request_id = $booking_request->id;
        if (empty($booking_request_id) === false) {
            $booking_hash_id = Helper::encodeBookingRequestId($booking_request_id);
        }

        $dial_code = $user->dial_code;
        $to_no     = $user->contact;

        $amount_currency = DEFAULT_CURRENCY;
        $amount_added    = 1000;
        $added_on_date   = '25-02-2019';
        $wallet_currency = DEFAULT_CURRENCY;
        $wallet_balance  = 1000;

        // phpcs:ignore
        $msg = "$amount_currency $amount_added has been deducted from your wallet against your accommodation booking. Booking ID: $booking_hash_id. The balance in your wallet as of $added_on_date is $wallet_currency $wallet_balance";
        $sender_id = 'GHBOOK';

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendAddedWalletMoneyForApplywalletSms($dial_code, $to_no, $amount_currency, $amount_added, $added_on_date, $wallet_currency, $wallet_balance, $booking_hash_id);

    }//end test_function_send_update_wallet_money_for_apply_wallet_sms_should_call_sendsms()


    /**
     * Test send sms function push msg to queue.
     *
     * @return void
     */
    public function test_send_sms_it_should_queue_msg()
    {
        $user = $this->createUser();

        $dial_code = $user->dial_code;
        $to_no     = $user->contact;

        $amount_currency = DEFAULT_CURRENCY;
        $amount_added    = 1000;
        $added_on_date   = '25-02-2019';
        $wallet_currency = DEFAULT_CURRENCY;
        $wallet_balance  = 1000;
        $sender_id       = DEFAULT_SMS_SENDER_ID;

        // phpcs:ignore
        $msg = "We have added $amount_currency $amount_added in your wallet for your accommodation review. The balance in your wallet as of $added_on_date is $wallet_currency $wallet_balance";

        $this->mocked_sms_service->sendSms($dial_code, $to_no, $msg, $sender_id);

        Queue::assertPushedOn(
            COMMUNICATION_QUEUE,
            SendSms::class,
            function ($job) use ($dial_code, $to_no, $msg, $sender_id) {
                   return ($dial_code == $job->dial_code
                        && $to_no == $job->to_no
                        && $msg == $job->msg
                        && $sender_id == $job->sender_id
                    );
            }
        );

    }//end test_send_sms_it_should_queue_msg()


    /**
     * Test New Booking Request Sms to Host.
     *
     * @return void
     */
    public function test_function_send_create_new_request_sms_to_host()
    {
        $create_booking_request = $this->createBookingRequests();

        $dial_code           = $create_booking_request['host']->dial_code;
        $to_no               = $create_booking_request['host']->contact;
        $property_title      = Helper::shortPropertyTitle($create_booking_request['properties']->title);
        $request_hash_id     = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');
        $guests              = $create_booking_request['booking_request']->guests;
        $units               = $create_booking_request['booking_request']->units;
        $formatted_amount    = Helper::getFormattedMoney(13424.49, 'INR', true, false);
        $expiry_time         = '15 min';
        $sender_id           = BOOKING_SMS_SENDER_ID;

        // phpcs:ignore
        $msg = "Hi, you have a new booking request ($request_hash_id) for \"$property_title\" $formatted_check_in to $formatted_check_out, $guests ".(($guests > 1) ? "guests " : "guest ").", $units ".(($units > 1) ? "units " : "unit ").", $formatted_amount. Please respond within $expiry_time to avoid auto-expiry.";

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendCreateNewRequestSmsToHost($dial_code, $to_no, $request_hash_id, $property_title, $formatted_check_in, $formatted_check_out, $guests, $units, $formatted_amount, $expiry_time);

    }//end test_function_send_create_new_request_sms_to_host()


    /**
     * Test Cancel Booking Request Sms to Host.
     *
     * @return void
     */
    public function test_function_send_cancel_booking_sms_to_host()
    {
        $create_booking_request = $this->createBookingRequests();

        $dial_code       = $create_booking_request['host']->dial_code;
        $to_no           = $create_booking_request['host']->contact;
        $property_title  = Helper::shortPropertyTitle($create_booking_request['properties']->title);
        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $sender_id       = BOOKING_SMS_SENDER_ID;

        // phpcs:ignore
        $msg = "Hi, we are sorry to inform you that the booking request ($request_hash_id) for property $property_title has been cancelled by the guest.";

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendCancelBookingSmsToHost($dial_code, $to_no, $request_hash_id, $property_title);

    }//end test_function_send_cancel_booking_sms_to_host()


    /**
     * Test Cancel Booking Request Sms to Guest.
     *
     * @return void
     */
    public function test_function_send_cancel_booking_sms_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();

        $dial_code       = $create_booking_request['traveller']->dial_code;
        $to_no           = $create_booking_request['traveller']->contact;
        $property_title  = Helper::shortPropertyTitle($create_booking_request['properties']->title);
        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $sender_id       = BOOKING_SMS_SENDER_ID;

        // phpcs:ignore
        $msg = "Hi, your cancellation request $request_hash_id for the property $property_title has been approved. Refund will be processed as per our cancellation policy.";

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendCancelBookingSmsToGuest($dial_code, $to_no, $request_hash_id, $property_title);

    }//end test_function_send_cancel_booking_sms_to_guest()


    /**
     * Test Approved Booking Request Sms to Guest.
     *
     * @return void
     */
    public function test_function_send_booking_request_approved_sms_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();

        $dial_code       = $create_booking_request['traveller']->dial_code;
        $to_no           = $create_booking_request['traveller']->contact;
        $property_title  = Helper::shortPropertyTitle($create_booking_request['properties']->title);
        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $expiry_time     = '15 min';
        $sender_id       = BOOKING_SMS_SENDER_ID;
        $payment_url     = 'bitly.com/2sCMgzTY';

        // phpcs:ignore
        $msg = "Hi, Your booking request ($request_hash_id) for the property $property_title has been approved by the host. Please make the payment within $expiry_time to complete your booking. Pay here: $payment_url ";

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendBookingRequestApprovedSmsToGuest($dial_code, $to_no, $request_hash_id, $property_title, $expiry_time, $payment_url);

    }//end test_function_send_booking_request_approved_sms_to_guest()


    /**
     * Test Rejected Booking Request Sms to Guest.
     *
     * @return void
     */
    public function test_function_send_booking_request_rejected_sms_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();

        $dial_code       = $create_booking_request['traveller']->dial_code;
        $to_no           = $create_booking_request['traveller']->contact;
        $property_title  = Helper::shortPropertyTitle($create_booking_request['properties']->title);
        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $sender_id       = BOOKING_SMS_SENDER_ID;

        // phpcs:ignore
        $msg = "Sorry! Your booking request ($request_hash_id) for property $property_title could not be approved by the Host.Explore GuestHouser.com for more options!";

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendBookingRequestRejectedSmsToGuest($dial_code, $to_no, $request_hash_id, $property_title);

    }//end test_function_send_booking_request_rejected_sms_to_guest()


    /**
     * Test Partial Booking Sms to Guest.
     *
     * @return void
     */
    public function test_function_send_partial_booking_sms_to_guest()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $dial_code       = $create_booking_request['traveller']->dial_code;
        $to_no           = $create_booking_request['traveller']->contact;
        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $payable_amount  = Helper::getFormattedMoney(13424.49, 'INR', true, false);
        $sender_id       = BOOKING_SMS_SENDER_ID;

        // phpcs:ignore
        $msg = "Thank you for paying the remainder of your booking amount $payable_amount online for booking ID $request_hash_id. There is no payable amount remaining.";

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendPartialBookingSmsToGuest($dial_code, $to_no, $request_hash_id, $payable_amount);

    }//end test_function_send_partial_booking_sms_to_guest()


    /**
     * Test Booking Sms to Guest.
     *
     * @return void
     */
    public function test_function_send_booking_sms_to_guest()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $dial_code           = $create_booking_request['traveller']->dial_code;
        $to_no               = $create_booking_request['traveller']->contact;
        $traveller_name      = $create_booking_request['traveller']->getUserFullName();
        $host_name           = $create_booking_request['host']->getUserFullName();
        $request_hash_id     = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $payable_amount      = Helper::getFormattedMoney(13424.49, 'INR', true, false);
        $property_title      = Helper::shortPropertyTitle($create_booking_request['properties']->title);
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');
        $guests              = $create_booking_request['booking_request']->guests;
        $units               = $create_booking_request['booking_request']->units;
        $sender_id           = BOOKING_SMS_SENDER_ID;

        // phpcs:ignore
        $msg = "Congratulations $traveller_name! You have successfully booked $property_title, hosted by $host_name. REQUEST_ID: $request_hash_id, $formatted_check_in to $formatted_check_out, $guests".(($guests > 1) ? " guests " : " guest ").", $units".(($units > 1) ? " units " : " unit ").", Amount- $payable_amount.";

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendBookingSmsToGuest(
            $dial_code,
            $to_no,
            $request_hash_id,
            $payable_amount,
            $traveller_name,
            $host_name,
            $property_title,
            $formatted_check_in,
            $formatted_check_out,
            $guests,
            $units
        );

    }//end test_function_send_booking_sms_to_guest()


    /**
     * Test Property Direction Sms to Guest.
     *
     * @return void
     */
    public function test_function_send_property_direction_sms_to_guest()
    {
        $dial_code      = '91';
        $to_no          = '8989898989';
        $property_title = 'Testing Property';
        $direction_url  = 'https://www.guesthouser.com';
        $sender_id      = DEFAULT_SMS_SENDER_ID;

        // phpcs:ignore
        $msg = "Hi explorer! Find your way to $property_title by following this link $direction_url for directions. If you need further assistance, call us at ".GH_CONTACT_NUMBER;

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendPropertyDirectionSmsToGuest($dial_code, $to_no, $direction_url, $property_title);

    }//end test_function_send_property_direction_sms_to_guest()


    /**
     * Test Booking Sms to Host.
     *
     * @return void
     */
    public function test_function_send_booking_sms_to_host()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $dial_code           = $create_booking_request['traveller']->dial_code;
        $to_no               = $create_booking_request['traveller']->contact;
        $request_hash_id     = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');
        $guests              = $create_booking_request['booking_request']->guests;
        $units               = $create_booking_request['booking_request']->units;
        $sender_id           = BOOKING_SMS_SENDER_ID;

        // phpcs:ignore
        $msg = "Hi! We have received a booking for your property. Booking ID $request_hash_id, check in $formatted_check_in to check out $formatted_check_out, no. of ".(($guests > 1) ? 'guests' : 'guest')." $guests, no. of ".(($units > 1) ? 'units' : 'unit')." $units.";

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendBookingSmsToHost(
            $dial_code,
            $to_no,
            $request_hash_id,
            $formatted_check_in,
            $formatted_check_out,
            $guests,
            $units
        );

    }//end test_function_send_booking_sms_to_host()


    /**
     * Test Reset Password otp sms.
     *
     * @return void
     */
    public function test_function_send_user_reset_password_otp()
    {
        $dial_code = '91';
        $to_no     = '8989898989';
        $otp       = random_int(1000, 9999);
        $sender_id = DEFAULT_SMS_SENDER_ID;

        // phpcs:ignore
        $msg = "<#> Dear user, your password reset code is: ".$otp."\n".OTP_VERIFICATION_HASH;

        $this->mocked_sms_service->shouldReceive('sendSms')->with($dial_code, $to_no, $msg, $sender_id)->once()->andReturn('');
        $this->mocked_sms_service->sendUserResetPasswordOtp($dial_code, $to_no, $otp);

    }//end test_function_send_user_reset_password_otp()


}//end class
