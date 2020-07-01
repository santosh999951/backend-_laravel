<?php
/**
 * UserServiceTest containing tests for UserService
 */

use App\Libraries\v1_6\{UserService, EmailService, SmsService};
use Carbon\Carbon;

/**
 * Class UserServiceTest
 *
 * @group Services
 */
class UserServiceTest extends TestCase
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

        $this->mocked_email_service = $this->mock(EmailService::class);
        $this->mocked_sms_service   = $this->mock(SmsService::class);
        Queue::fake();

    }//end setup()


    /**
     * Tear down.
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->clearmock();

    }//end tearDown()


    /**
     * Test for emails sent when user registers via website.
     *
     * @return void
     */
    public function test_send_website_registration_emails()
    {
        $user              = $this->createUser();
        $user_name         = $user->name.' '.$user->last_name;
        $verification_link = MAILER_SITE_URL.'/user/mailverify/?ucode='.$user->confirmation_code;

        $this->mocked_email_service->shouldReceive('sendWelcomeEmail')->once()->with($user->email, $user_name);

        $this->mocked_email_service->shouldReceive('sendVerificationEmail')->once()->with($user->email, $user_name, $user->confirmation_code, $verification_link);

        $user_service = new UserService($this->mocked_email_service);
        $user_service->sendUserRegistrationEmails($user, WEBSITE_SOURCE_ID);

    }//end test_send_website_registration_emails()


    /**
     * Test for emails sent when user registers via google.
     *
     * @return void
     */
    public function test_send_google_registration_emails()
    {
        $user      = $this->createUser();
        $user_name = $user->name.' '.$user->last_name;

        $this->mocked_email_service->shouldReceive('sendWelcomeGoogleEmail')->once()->with($user->email, $user_name, '111111');

        $user_service = new UserService($this->mocked_email_service);
        $user_service->sendUserRegistrationEmails($user, GOOGLE_SOURCE_ID, '111111');

    }//end test_send_google_registration_emails()


    /**
     * Test for emails sent when user registers via facebook.
     *
     * @return void
     */
    public function test_send_facebook_registration_emails()
    {
        $user              = $this->createUser();
        $user_name         = $user->name.' '.$user->last_name;
        $verification_link = MAILER_SITE_URL.'/user/mailverify/?ucode='.$user->confirmation_code;

        $this->mocked_email_service->shouldReceive('sendWelcomeFacebookEmail')->once()->with($user->email, $user_name, '111111');

        $this->mocked_email_service->shouldReceive('sendVerificationEmail')->once()->with($user->email, $user_name, $user->confirmation_code, $verification_link);

        $user_service = new UserService($this->mocked_email_service);
        $user_service->sendUserRegistrationEmails($user, FACEBOOK_SOURCE_ID, '111111');

    }//end test_send_facebook_registration_emails()


    /**
     * Test for emails sent when user registers via facebook and email is empty.
     *
     * @return void
     */
    public function test_facebook_registration_empty_email()
    {
        $user      = $this->createUser();
        $user_name = $user->name.' '.$user->last_name;

        $this->mocked_email_service->shouldNotReceive('sendWelcomeFacebookEmail');
        $this->mocked_email_service->shouldNotReceive('sendVerificationEmail');

        $user_service = new UserService($this->mocked_email_service);
        $user->email  = '';
        $this->assertFalse($user_service->sendUserRegistrationEmails($user, FACEBOOK_SOURCE_ID, '111111'));
        $user->email = '1234@facebook.com';
        $this->assertFalse($user_service->sendUserRegistrationEmails($user, FACEBOOK_SOURCE_ID, '111111'));

    }//end test_facebook_registration_empty_email()


    /**
     * Test for emails sent when registration source is invalid.
     *
     * @return void
     */
    public function test_invalid_source_registration_emails()
    {
        $user      = $this->createUser();
        $user_name = $user->name.' '.$user->last_name;

        $this->mocked_email_service->shouldNotReceive('sendWelcomeEmail');
        $this->mocked_email_service->shouldNotReceive('sendWelcomeGoogleEmail');
        $this->mocked_email_service->shouldNotReceive('sendWelcomeFacebookEmail');
        $this->mocked_email_service->shouldNotReceive('sendWelcomeAppleEmail');
        $this->mocked_email_service->shouldNotReceive('sendVerificationEmail');

        $user_service = new UserService($this->mocked_email_service);
        $this->assertFalse($user_service->sendUserRegistrationEmails($user, 400, '111111'));

    }//end test_invalid_source_registration_emails()


    /**
     * Test for Sms sent When User add Review.
     *
     * @return void
     */
    public function test_wallet_updated_sms_for_trip_review()
    {
        $user               = $this->createUser();
        $wallet_transaction = $this->createWalletTransaction($user, TRIP_AND_REVIEW);

        $dial_code       = $user->dial_code;
        $to_no           = $user->contact;
        $amount_currency = $wallet_transaction->currency;
        $amount_added    = $wallet_transaction->amount;
        $added_on_date   = Carbon::now('Asia/Kolkata')->format('d-m-Y');
        $wallet_currency = $user->wallet_currency;
        $wallet_balance  = $user->wallet_balance;

        $this->mocked_sms_service->shouldReceive('sendAddedWalletMoneyForTripReviewSms')->once()->with(
            $dial_code,
            $to_no,
            $amount_currency,
            $amount_added,
            $added_on_date,
            $wallet_currency,
            $wallet_balance
        );

        $user_service = new UserService($this->mocked_email_service, $this->mocked_sms_service);
        $user_service->sendWalletUpdationForTripAndReviewSms($wallet_transaction, $user);

    }//end test_wallet_updated_sms_for_trip_review()


    /**
     * Test for emails sent when user verify email.
     *
     * @return void
     */
    public function test_send_verification_emails()
    {
        $user              = $this->createUser();
        $user_name         = $user->name.' '.$user->last_name;
        $verification_link = MAILER_SITE_URL.'/user/mailverify/?ucode='.$user->confirmation_code;
        $this->mocked_email_service->shouldReceive('sendVerificationEmail')->once()->with($user->email, $user_name, $user->confirmation_code, $verification_link);
        $user_service = new UserService($this->mocked_email_service);
        $user_service->sendUserVerificationEmail($user->email, $user_name, $user->confirmation_code, $verification_link);

    }//end test_send_verification_emails()


      /**
       * Test for emails sent when user update wallet.
       *
       * @return void
       */
    public function test_wallet_updated_email_for_trip_review()
    {
        $request            = $this->createBookingRequests(BOOKED);
        $user               = $request['traveller'];
        $property           = $request['properties'];
        $wallet_transaction = $this->createWalletTransaction($user, TRIP_AND_REVIEW);

        $to_email       = $user->email;
        $name           = $user->name;
        $currency       = $wallet_transaction->currency;
        $amount         = $wallet_transaction->amount;
        $added_on_date  = Carbon::now('Asia/Kolkata')->format('d-m-Y');
        $wallet_balance = $user->wallet_balance;

        // Mail_subject.
        $subject = "{$currency} {$amount} added in your wallet";

        // Mail_message.
        $property_link  = MAILER_SITE_URL.'/properties/rooms/'.$property->id;
        $property_title = $property->title;

        $this->mocked_email_service->shouldReceive('sendAddedWalletMoneyForTripReviewEmail')->once()->with(
            $to_email,
            $name,
            $currency,
            $amount,
            $wallet_balance,
            $added_on_date,
            $subject,
            $property_link,
            $property_title
        );

        $user_service = new UserService($this->mocked_email_service, $this->mocked_sms_service);
        $user_service->sendWalletUpdationForTripAndReviewEmail($wallet_transaction, $user, $property_title, $property_link);

    }//end test_wallet_updated_email_for_trip_review()


    /**
     * Test for emails sent when user Signup using referal code.
     *
     * @return void
     */
    public function test_wallet_updated_email_for_referal_bonus()
    {
        $user               = $this->createUser();
        $wallet_transaction = $this->createWalletTransaction($user, REFERRAL_BONUS);

        $to_email       = $user->email;
        $name           = $user->name;
        $currency       = $wallet_transaction->currency;
        $amount         = $wallet_transaction->amount;
        $added_on_date  = Carbon::now('Asia/Kolkata')->format('d-m-Y');
        $wallet_balance = $user->wallet_balance;
        $expire_on      = date('d M Y', strtotime($wallet_transaction->expire_on));

        // Mail_subject.
        $subject = "{$currency} {$amount} added in your GuestHouser wallet";

        $this->mocked_email_service->shouldReceive('sendAddedWalletMoneyForReferalBonusEmail')->once()->with(
            $to_email,
            $name,
            $currency,
            $amount,
            $wallet_balance,
            $added_on_date,
            $subject,
            $expire_on
        );
        $user_service = new UserService($this->mocked_email_service, $this->mocked_sms_service);
        $user_service->sendWalletUpdationForReferalBonusEmail($wallet_transaction, $user);

    }//end test_wallet_updated_email_for_referal_bonus()


    /**
     * Test for sms sent for referal bonus.
     *
     * @return void
     */
    public function test_wallet_updated_sms_for_referal_bonus()
    {
        $user               = $this->createUser();
        $wallet_transaction = $this->createWalletTransaction($user, REFERRAL_BONUS);

        $dial_code       = $user->dial_code;
        $to_no           = $user->contact;
        $amount_currency = $wallet_transaction->currency;
        $amount_added    = $wallet_transaction->amount;
        $wallet_currency = $user->wallet_currency;
        $wallet_balance  = $user->wallet_balance;

        $this->mocked_sms_service->shouldReceive('sendAddedWalletMoneyForReferalBonusSms')->once()->with(
            $dial_code,
            $to_no,
            $amount_currency,
            $amount_added,
            $wallet_currency,
            $wallet_balance
        );

        $user_service = new UserService($this->mocked_email_service, $this->mocked_sms_service);
        $user_service->sendWalletUpdationForReferalBonusSms($wallet_transaction, $user);

    }//end test_wallet_updated_sms_for_referal_bonus()


    /**
     * Test for emails sent when user Signup using referal code.
     *
     * @return void
     */
    public function test_wallet_updated_email_for_friend_referal_bonus()
    {
        $user               = $this->createUser();
        $wallet_transaction = $this->createWalletTransaction($user, FRIEND_REFERRAL_BONUS);

        $to_email          = $user->email;
        $name              = $user->name;
        $currency          = $wallet_transaction->currency;
        $amount            = $wallet_transaction->amount;
        $added_on_date     = Carbon::now('Asia/Kolkata')->format('d-m-Y');
        $wallet_balance    = $user->wallet_balance;
        $referal_user_name = str_random(8);
        $expire_on         = date('d M Y', strtotime($wallet_transaction->expire_on));

        // Mail_subject.
        $subject = "{$currency} {$amount} added in your GuestHouser wallet";

        $this->mocked_email_service->shouldReceive('sendAddedWalletMoneyForFriendReferalBonusEmail')->once()->with(
            $to_email,
            $name,
            $currency,
            $amount,
            $wallet_balance,
            $added_on_date,
            $subject,
            $expire_on,
            $referal_user_name
        );
        $user_service = new UserService($this->mocked_email_service, $this->mocked_sms_service);
        $user_service->sendWalletUpdationForFriendReferalBonusEmail($wallet_transaction, $user, $referal_user_name);

    }//end test_wallet_updated_email_for_friend_referal_bonus()


     /**
      * Test for Sms for friend referal bonus.
      *
      * @return void
      **/
    public function test_wallet_updated_sms_for_friend_referal_bonus()
    {
        $user               = $this->createUser();
        $wallet_transaction = $this->createWalletTransaction($user, FRIEND_REFERRAL_BONUS);

        $dial_code       = $user->dial_code;
        $to_no           = $user->contact;
        $amount_currency = $wallet_transaction->currency;
        $amount_added    = $wallet_transaction->amount;
        $wallet_currency = $user->wallet_currency;
        $wallet_balance  = $user->wallet_balance;
        $expire_on       = date('d M Y', strtotime($wallet_transaction->expire_on));

        $this->mocked_sms_service->shouldReceive('sendAddedWalletMoneyForFriendReferalBonusSms')->once()->with(
            $dial_code,
            $to_no,
            $amount_currency,
            $amount_added,
            $wallet_currency,
            $wallet_balance,
            $expire_on
        );
        $user_service = new UserService($this->mocked_email_service, $this->mocked_sms_service);
        $user_service->sendWalletUpdationForFriendReferalBonusSms($wallet_transaction, $user);

    }//end test_wallet_updated_sms_for_friend_referal_bonus()


    /**
     * Test for Sms for first Booking.
     *
     * @return void
     */
    public function test_wallet_updated_sms_for_referal_first_booking_bonus()
    {
        $request            = $this->createBookingRequests(BOOKED);
        $user               = $request['traveller'];
        $wallet_transaction = $this->createWalletTransaction($user, REFERRAL_FIRST_BOOKING_BONUS);

        $dial_code       = $user->dial_code;
        $to_no           = $user->contact;
        $amount_currency = $wallet_transaction->currency;
        $amount_added    = $wallet_transaction->amount;
        $wallet_currency = $user->wallet_currency;
        $wallet_balance  = $user->wallet_balance;
        $expire_on       = date('d M Y', strtotime($wallet_transaction->expire_on));

        $this->mocked_sms_service->shouldReceive('sendAddedWalletMoneyForFirstBookingBonusSms')->once()->with(
            $dial_code,
            $to_no,
            $amount_currency,
            $amount_added,
            $wallet_currency,
            $wallet_balance,
            $expire_on
        );
        $user_service = new UserService($this->mocked_email_service, $this->mocked_sms_service);
        $user_service->sendWalletUpdationForFirstBookingBonusSms($wallet_transaction, $user);

    }//end test_wallet_updated_sms_for_referal_first_booking_bonus()


    /**
     * Test for emails sent when user Signup using referal code.
     *
     * @return void
     */
    public function test_wallet_updated_email_for_referal_first_booking_bonus()
    {
        $request            = $this->createBookingRequests(BOOKED);
        $user               = $request['traveller'];
        $wallet_transaction = $this->createWalletTransaction($user, REFERRAL_FIRST_BOOKING_BONUS);
        $to_email           = $user->email;
        $name               = $user->name;
        $currency           = $wallet_transaction->currency;
        $amount             = $wallet_transaction->amount;
        $added_on_date      = Carbon::now('Asia/Kolkata')->format('d-m-Y');
        $wallet_balance     = $user->wallet_balance;
        $referal_user_name  = str_random(8);
        $expire_on          = date('d M Y', strtotime($wallet_transaction->expire_on));

        // Mail_subject.
        $subject = "{$currency} {$amount} added in your GuestHouser wallet";

        $this->mocked_email_service->shouldReceive('sendAddedWalletMoneyForFirstBookingBonusEmail')->once()->with(
            $to_email,
            $name,
            $currency,
            $amount,
            $wallet_balance,
            $added_on_date,
            $subject,
            $expire_on,
            $referal_user_name
        );
        $user_service = new UserService($this->mocked_email_service, $this->mocked_sms_service);
        $user_service->sendWalletUpdationForReferalFirstBookingBonusEmail($wallet_transaction, $user, $referal_user_name);

    }//end test_wallet_updated_email_for_referal_first_booking_bonus()


    /**
     * Test for emails sent when user Apply Wallet money at the time of payment.
     *
     * @return void
     */
    public function test_wallet_updated_email_for_apply_wallet_bonus()
    {
        $request            = $this->createBookingRequests(BOOKED);
        $property           = $request['properties'];
        $user               = $request['traveller'];
        $wallet_transaction = $this->createWalletTransaction($user, APPLY_WALLET_MONEY);
        $to_email           = $user->email;
        $name               = $user->name;
        $currency           = $wallet_transaction->currency;
        $amount             = $wallet_transaction->amount;
        $added_on_date      = Carbon::now('Asia/Kolkata')->format('d-m-Y');
        $wallet_balance     = $user->wallet_balance;
        $referal_user_name  = str_random(8);
        $property_title     = $property->title;
        $property_link      = MAILER_SITE_URL.'/properties/rooms/'.$property->id;

         // Mail_subject.
        $subject = "{$currency} {$amount} deducted from your wallet";

        $this->mocked_email_service->shouldReceive('sendUpdateWalletMoneyForApplyWalletEmail')->once()->with(
            $to_email,
            $name,
            $currency,
            $amount,
            $wallet_balance,
            $added_on_date,
            $subject,
            $property_title,
            $property_link
        );
        $user_service = new UserService($this->mocked_email_service, $this->mocked_sms_service);
        $user_service->sendWalletUpdationForApplyWalletMoneyEmail($wallet_transaction, $user, $property_title, $property_link);

    }//end test_wallet_updated_email_for_apply_wallet_bonus()


    /**
     * Test for Add User Bank Details.
     *
     * @return void
     **/
    public function test_save_user_bank_details()
    {
        // Create Properties Data.
        $create_property = $this->createProperties();

        $bank_details = [
            'user_id'        => $create_property['host']->id,
            'payee_name'     => 'Unit Testing',
            'bank_name'      => 'Test Bank',
            'branch_name'    => 'Testing',
            'account_number' => '111111111111',
            'ifsc_code'      => 'TESTTING',
            'address_line_1' => '',
            'address_line_2' => '',
            'country'        => '',
            'state'          => '',
            'routing_number' => '',
            'gstin'          => '',
            'admin_id'       => 0,
        ];

        $user_service   = new UserService;
        $billing_detail = $user_service->saveUserBankDetails($bank_details);

        if (empty($billing_detail) === false) {
            $this->assertEquals($bank_details['payee_name'], $billing_detail['payee_name']);
            $this->assertEquals($bank_details['bank_name'], $billing_detail['bank_name']);
            $this->assertEquals($bank_details['branch_name'], $billing_detail['branch_name']);
            $this->assertEquals($bank_details['account_number'], $billing_detail['account_number']);
            $this->assertEquals($bank_details['ifsc_code'], $billing_detail['ifsc_code']);
        } else {
            $this->assertTrue(false);
        }

    }//end test_save_user_bank_details()


    /**
     * Test for emails sent to properly support.
     *
     * @return void
     */
    public function test_properly_support_email()
    {
        $subject = 'Unit Testing Apis';
        $message = 'Unit Testing Apis';

        $this->mocked_email_service->shouldReceive('sendProperlySupportEmail')->once()->with(
            $subject,
            $message
        );
        $user_service = new UserService($this->mocked_email_service);
        $user_service->sendProperlySupportEmail($subject, $message);

    }//end test_properly_support_email()


    /**
     * Test for emails sent when user registers via apple.
     *
     * @return void
     */
    public function test_send_apple_registration_emails()
    {
        $user      = $this->createUser();
        $user_name = $user->name.' '.$user->last_name;

        $this->mocked_email_service->shouldReceive('sendWelcomeAppleEmail')->once()->with($user->email, $user_name, '111111');

        $user_service = new UserService($this->mocked_email_service);
        $user_service->sendUserRegistrationEmails($user, APPLE_SOURCE_ID, '111111');

    }//end test_send_apple_registration_emails()


}//end class
