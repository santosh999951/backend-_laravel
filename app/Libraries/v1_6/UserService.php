<?php
/**
 * User Service containing methods related to user/host.
 */

namespace App\Libraries\v1_6;

use App\Models\{User, WalletTransaction, UserBillingInfo, OnetimeAccessToken , OtpContact, ProperlyDesignationUserMapping, ProperlyDesignationPidMapping, MobileAppDevice, SmsOtp};
use Carbon\Carbon;
use App\Libraries\Helper;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Contracts\Permission as PermissionContract;


/**
 * Class UserService.
 */
class UserService
{

    /**
     * Email service object for sending emails.
     *
     * @var object
     */
    protected $email_service;

     /**
      * Sms service object for sending sms.
      *
      * @var object
      */
    protected $sms_service;


    /**
     * Email service object for sending emails.
     *
     * @param EmailService $email_service Email service.
     * @param SmsService   $sms_service   Sms Service.
     *
     * @return void.
     */
    public function __construct(EmailService $email_service=null, SmsService $sms_service=null)
    {
        $this->email_service = $email_service;
        $this->sms_service   = $sms_service;

    }//end __construct()


    /**
     * Check User mobile is verified or not.
     *
     * @param User $user User object.
     *
     * @return integer
     */
    public static function isMobileVerified(User $user)
    {
        $is_mobile_verified = 1;
        if (empty($user->contact) === true || empty($user->mobile_verify) === true || empty($user->dial_code) === true) {
            $is_mobile_verified = 0;
        }

        return $is_mobile_verified;

    }//end isMobileVerified()


    /**
     * Send registration emails to user.
     *
     * @param User    $user     New user object.
     * @param integer $source   Website/google/facebook/apple.
     * @param string  $password Required if source is google/facebook/apple.
     *
     * @return boolean
     */
    public function sendUserRegistrationEmails(User $user, int $source, string $password='')
    {
        $user_name         = $user->name.' '.$user->last_name;
        $to_email          = $user->email;
        $confirmation_code = $user->confirmation_code;
        $verification_link = MAILER_SITE_URL.'/user/mailverify/?ucode='.$confirmation_code;

        if ($source === WEBSITE_SOURCE_ID || $source === EMAIL_SOURCE_ID || $source === PHONE_SOURCE_ID) {
            $this->email_service->sendWelcomeEmail($to_email, $user_name);
            $this->email_service->sendVerificationEmail($to_email, $user_name, $confirmation_code, $verification_link);
        } else if (GOOGLE_SOURCE_ID === $source) {
            $this->email_service->sendWelcomeGoogleEmail($to_email, $user_name, $password);
        } else if (FACEBOOK_SOURCE_ID === $source) {
            if (false === empty($to_email) && false === strpos($to_email, '@facebook.com')) {
                $this->email_service->sendWelcomeFacebookEmail($to_email, $user_name, $password);
                $this->email_service->sendVerificationEmail($to_email, $user_name, $confirmation_code, $verification_link);
            } else {
                 return false;
            }
        } else if (APPLE_SOURCE_ID === $source) {
            $this->email_service->sendWelcomeAppleEmail($to_email, $user_name, $password);
        } else {
            return false;
        }

        return true;

    }//end sendUserRegistrationEmails()


    /**
     * GetRmHostList.
     *
     * @param string  $email   Admin email.
     * @param integer $offset  Offset.
     * @param integer $limit   LIMIT.
     * @param integer $host_id Host Id.
     *
     * @return array
     */
    public static function getRmHostList(string $email, int $offset, int $limit, int $host_id=0)
    {
        $hostlist = User::getRmHostList($email, $offset, $limit, $host_id);
        return $hostlist;

    }//end getRmHostList()


     /**
      * Send wallet updated sms for trip and review.
      *
      * @param WalletTransaction $wallet_transaction Wallet transaction.
      * @param User              $user               User.
      *
      * @return void
      */
    public function sendWalletUpdationForTripAndReviewSms(WalletTransaction $wallet_transaction, User $user)
    {
        $dial_code = $user->dial_code;
        $to_no     = $user->contact;

        // Integration only trip and review templates.
        $amount_added    = $wallet_transaction->amount;
        $amount_currency = $wallet_transaction->currency;
        $wallet_currency = $wallet_transaction->currency;

        $wallet_balance = $user->wallet_balance;
        $added_on_date  = Carbon::now('Asia/Kolkata')->format('d-m-Y');

        $this->sms_service->sendAddedWalletMoneyForTripReviewSms($dial_code, $to_no, $amount_currency, $amount_added, $added_on_date, $wallet_currency, $wallet_balance);

    }//end sendWalletUpdationForTripAndReviewSms()


    /**
     * Send wallet updated sms for referal bonus.
     *
     * @param WalletTransaction $wallet_transaction Wallet transaction.
     * @param User              $user               User.
     *
     * @return void
     */
    public function sendWalletUpdationForReferalBonusSms(WalletTransaction $wallet_transaction, User $user)
    {
        $dial_code = $user->dial_code;
        $to_no     = $user->contact;

        // Integration only trip and review templates.
        $amount_added    = $wallet_transaction->amount;
        $amount_currency = $wallet_transaction->currency;
        $wallet_currency = $wallet_transaction->currency;

        $wallet_balance = $user->wallet_balance;

        $this->sms_service->sendAddedWalletMoneyForReferalBonusSms($dial_code, $to_no, $amount_currency, $amount_added, $wallet_currency, $wallet_balance);

    }//end sendWalletUpdationForReferalBonusSms()


    /**
     * Send wallet updated sms for friend referal.
     *
     * @param WalletTransaction $wallet_transaction Wallet transaction.
     * @param User              $user               User.
     *
     * @return void
     */
    public function sendWalletUpdationForFriendReferalBonusSms(WalletTransaction $wallet_transaction, User $user)
    {
        $dial_code = $user->dial_code;
        $to_no     = $user->contact;
        $expire_on = date('d M Y', strtotime($wallet_transaction->expire_on));

        // Integration only trip and review templates.
        $amount_added    = $wallet_transaction->amount;
        $amount_currency = $wallet_transaction->currency;
        $wallet_currency = $wallet_transaction->currency;

        $wallet_balance = $user->wallet_balance;

        $this->sms_service->sendAddedWalletMoneyForFriendReferalBonusSms($dial_code, $to_no, $amount_currency, $amount_added, $wallet_currency, $wallet_balance, $expire_on);

    }//end sendWalletUpdationForFriendReferalBonusSms()


    /**
     * Send wallet updated sms for first booking bonus.
     *
     * @param WalletTransaction $wallet_transaction Wallet transaction.
     * @param User              $user               User.
     *
     * @return void
     */
    public function sendWalletUpdationForFirstBookingBonusSms(WalletTransaction $wallet_transaction, User $user)
    {
        $dial_code = $user->dial_code;
        $to_no     = $user->contact;
        $expire_on = date('d M Y', strtotime($wallet_transaction->expire_on));

        $amount_added    = $wallet_transaction->amount;
        $amount_currency = $wallet_transaction->currency;
        $wallet_currency = $wallet_transaction->currency;

        $wallet_balance = $user->wallet_balance;

        $this->sms_service->sendAddedWalletMoneyForFirstBookingBonusSms($dial_code, $to_no, $amount_currency, $amount_added, $wallet_currency, $wallet_balance, $expire_on);

    }//end sendWalletUpdationForFirstBookingBonusSms()


        /**
         * Send wallet updated sms For apply wallet money.
         *
         * @param WalletTransaction $wallet_transaction Wallet transaction.
         * @param User              $user               User.
         *
         * @return void
         */
    public function sendWalletUpdationForApplyWalletSms(WalletTransaction $wallet_transaction, User $user)
    {
        $dial_code       = $user->dial_code;
        $to_no           = $user->contact;
        $booking_hash_id = '';

        // Integration only trip and review templates.
        $amount_added    = $wallet_transaction->amount;
        $amount_currency = $wallet_transaction->currency;
        $wallet_currency = $wallet_transaction->currency;
        $booking_id      = ($wallet_transaction->booking_request_id ?? '');
        if (empty($booking_id) === false) {
            $booking_hash_id = Helper::encodeBookingRequestId($booking_id);
        }

        $wallet_balance = $user->wallet_balance;
        $added_on_date  = Carbon::now('Asia/Kolkata')->format('d-m-Y');

        $this->sms_service->sendAddedWalletMoneyForApplywalletSms($dial_code, $to_no, $amount_currency, $amount_added, $added_on_date, $wallet_currency, $wallet_balance, $booking_hash_id);

    }//end sendWalletUpdationForApplyWalletSms()


    /**
     * Send wallet updated email For Referal Bonus.
     *
     * @param WalletTransaction $wallet_transaction Wallet transaction.
     * @param User              $user               User.
     *
     * @return void
     */
    public function sendWalletUpdationForReferalBonusEmail(WalletTransaction $wallet_transaction, User $user)
    {
        $to_email       = $user->email;
        $name           = $user->name;
        $amount         = $wallet_transaction->amount;
        $currency       = $user->wallet_currency;
        $wallet_balance = $user->wallet_balance;
        $added_on_date  = Carbon::now('Asia/Kolkata')->format('d-m-Y');
        $expire_on      = date('d M Y', strtotime($wallet_transaction->expire_on));
        $subject        = "{$currency} {$amount} added in your GuestHouser wallet";

        $this->email_service->sendAddedWalletMoneyForReferalBonusEmail($to_email, $name, $currency, $amount, $wallet_balance, $added_on_date, $subject, $expire_on);

    }//end sendWalletUpdationForReferalBonusEmail()


        /**
         * Send wallet updated email For Trip and review.
         *
         * @param WalletTransaction $wallet_transaction Wallet transaction.
         * @param User              $user               User.
         * @param string            $property_title     Property Link.
         * @param string            $property_link      Property Title.
         *
         * @return void
         */
    public function sendWalletUpdationForTripAndReviewEmail(WalletTransaction $wallet_transaction, User $user, string $property_title, string $property_link)
    {
        $wallet_event   = $wallet_transaction->event;
        $to_email       = $user->email;
        $name           = $user->name;
        $amount         = $wallet_transaction->amount;
        $currency       = $user->wallet_currency;
        $wallet_balance = $user->wallet_balance;
        $added_on_date  = Carbon::now('Asia/Kolkata')->format('d-m-Y');

        // Mail Subject.
        $subject = "{$currency} {$amount} added in your wallet";

        $this->email_service->sendAddedWalletMoneyForTripReviewEmail($to_email, $name, $currency, $amount, $wallet_balance, $added_on_date, $subject, $property_link, $property_title);

    }//end sendWalletUpdationForTripAndReviewEmail()


     /**
      * Send wallet updated email For Friend Referal Bonus.
      *
      * @param WalletTransaction $wallet_transaction Wallet transaction.
      * @param User              $user               User.
      * @param string            $referal_name       Referal Name.
      *
      * @return void
      */
    public function sendWalletUpdationForFriendReferalBonusEmail(WalletTransaction $wallet_transaction, User $user, string $referal_name)
    {
        $to_email       = $user->email;
        $name           = $user->name;
        $amount         = $wallet_transaction->amount;
        $currency       = $user->wallet_currency;
        $wallet_balance = $user->wallet_balance;
        $added_on_date  = Carbon::now('Asia/Kolkata')->format('d-m-Y');
        $expire_on      = date('d M Y', strtotime($wallet_transaction->expire_on));

        $subject = "{$currency} {$amount} added in your GuestHouser wallet";

        $this->email_service->sendAddedWalletMoneyForFriendReferalBonusEmail($to_email, $name, $currency, $amount, $wallet_balance, $added_on_date, $subject, $expire_on, $referal_name);

    }//end sendWalletUpdationForFriendReferalBonusEmail()


    /**
     * Send wallet updated email For  Referal First Booking Bonus.
     *
     * @param WalletTransaction $wallet_transaction Wallet transaction.
     * @param User              $user               User.
     * @param string            $referal_name       Referal Name.
     *
     * @return void
     */
    public function sendWalletUpdationForReferalFirstBookingBonusEmail(WalletTransaction $wallet_transaction, User $user, string $referal_name)
    {
        $to_email       = $user->email;
        $name           = $user->name;
        $amount         = $wallet_transaction->amount;
        $currency       = $user->wallet_currency;
        $wallet_balance = $user->wallet_balance;
        $added_on_date  = Carbon::now('Asia/Kolkata')->format('d-m-Y');
        $expire_on      = date('d M Y', strtotime($wallet_transaction->expire_on));

        $subject = "{$currency} {$amount} added in your GuestHouser wallet";

        $this->email_service->sendAddedWalletMoneyForFirstBookingBonusEmail($to_email, $name, $currency, $amount, $wallet_balance, $added_on_date, $subject, $expire_on, $referal_name);

    }//end sendWalletUpdationForReferalFirstBookingBonusEmail()


    /**
     * Send wallet updated email when user used wallet money.
     *
     * @param WalletTransaction $wallet_transaction Wallet transaction.
     * @param User              $user               User.
     * @param string            $property_title     Property Title.
     * @param string            $property_link      Property Link.
     *
     * @return void
     */
    public function sendWalletUpdationForApplyWalletMoneyEmail(WalletTransaction $wallet_transaction, User $user, string $property_title, string $property_link)
    {
        $to_email       = $user->email;
        $name           = $user->name;
        $amount         = $wallet_transaction->amount;
        $currency       = $user->wallet_currency;
        $wallet_balance = $user->wallet_balance;
        $added_on_date  = Carbon::now('Asia/Kolkata')->format('d-m-Y');

        $subject = "{$currency} {$amount} deducted from your wallet";

        $this->email_service->sendUpdateWalletMoneyForApplyWalletEmail($to_email, $name, $currency, $amount, $wallet_balance, $added_on_date, $subject, $property_title, $property_link);

    }//end sendWalletUpdationForApplyWalletMoneyEmail()


    /**
     * Send Verification emails to user.
     *
     * @param string $to_email          Email id of user.
     * @param string $user_name         Name of user.
     * @param string $confirmation_code Mail Verification Code.
     * @param string $verification_link Mail Verification Link.
     *
     * @return boolean
     */
    public function sendUserVerificationEmail(string $to_email, string $user_name, string $confirmation_code, string $verification_link)
    {
        $this->email_service->sendVerificationEmail($to_email, $user_name, $confirmation_code, $verification_link);
        return true;

    }//end sendUserVerificationEmail()


    /**
     * Send sendProperlySupportEmail.
     *
     * @param string $subject Subject.
     * @param string $message Message.
     *
     * @return boolean
     */
    public function sendProperlySupportEmail(string $subject, string $message)
    {
        $this->email_service->sendProperlySupportEmail($subject, $message);
        return true;

    }//end sendProperlySupportEmail()


    /**
     * Get Profile.
     *
     * @param User $user User model.
     *
     * @return array
     */
    public function getProfile(User $user) : array
    {
        return $user->getUserProfleViaObject();

    }//end getProfile()


    /**
     * Get prive owner User.
     *
     * @param string  $email_or_phone Email/phone.
     * @param integer $dial_code      Dial Code.
     *
     * @return object
     */
    public function getPriveOwner(string $email_or_phone, int $dial_code=91)
    {
        $user = new User;
        return $user->getPriveOwner($email_or_phone, $dial_code);

    }//end getPriveOwner()


    /**
     * Get prive manager User.
     *
     * @param string  $email_or_phone Email/Phone.
     * @param integer $dial_code      Dial Code.
     *
     * @return object
     */
    public function getPriveManager(string $email_or_phone, int $dial_code=91)
    {
        $user = new User;
        return $user->getPriveManager($email_or_phone, $dial_code);

    }//end getPriveManager()


    /**
     * Get User.
     *
     * @param string $user_id User id.
     *
     * @return object
     */
    public function getUser(string $user_id)
    {
        $user = new User;
        return $user->getUser($user_id);

    }//end getUser()


    /**
     * Validate User Password.
     *
     * @param User   $user                       User.
     * @param string $password_in_base64_encoded Password in base64 encoded format.
     *
     * @return boolean
     */
    public function validatePassword(User $user, string $password_in_base64_encoded)
    {
        return $user->validateForPassportPasswordGrant($password_in_base64_encoded);

    }//end validatePassword()


    /**
     * Save User Bank Details.
     *
     * @param array $bank_details User Bank Details.
     *
     * @return array
     */
    public function saveUserBankDetails(array $bank_details)
    {
        $user_billing_info = new UserBillingInfo;

        $billing_info = $user_billing_info->addBankDetail($bank_details);

        return $billing_info;

    }//end saveUserBankDetails()


    /**
     * Validate One time access token.
     *
     * @param string $onetime_token One time access token.
     *
     * @return object
     */
    public function validateOnetimeAccessToken(string $onetime_token)
    {
        $validate_token_data = new OnetimeAccessToken;
        return $validate_token_data->validateToken($onetime_token);

    }//end validateOnetimeAccessToken()


    /**
     * Validate Auth key Token.
     *
     * @param string $auth_key One time access token.
     *
     * @return object
     */
    public function validateAuthKeyToken(string $auth_key)
    {
        $user = new User;
        return $user->getUserDataByAuthKey($auth_key);

    }//end validateAuthKeyToken()


    /**
     * Send Reset Password Link to user email.
     *
     * @param string $to_email             Email id of user.
     * @param string $reset_password_token Reset Password Token.
     * @param string $role                 Role of User.
     *
     * @return boolean
     */
    public function sendUserResetPasswordLinkEmail(string $to_email, string $reset_password_token, string $role)
    {
        if (empty($to_email) === true) {
            return false;
        }

        $this->email_service->sendResetPasswordLinkEmail($to_email, $reset_password_token, $role);
        return true;

    }//end sendUserResetPasswordLinkEmail()


    /**
     * Send Reset Password otp to user phone.
     *
     * @param string $dial_code         Dial Code.
     * @param string $contact           Contact.
     * @param string $verification_code Verification Code.
     *
     * @return boolean
     */
    public function sendUserResetPasswordOtpSms(string $dial_code, string $contact, string $verification_code)
    {
        if (empty($dial_code) === true || empty($contact) === true) {
            return false;
        }

        $this->sms_service->sendUserResetPasswordOtp($dial_code, $contact, $verification_code);
        return true;

    }//end sendUserResetPasswordOtpSms()


    /**
     * Send Reset Password otp to user's email.
     *
     * @param string $to_email Email id of user.
     * @param string $otp      OTP Code.
     * @param string $role     Role of User.
     *
     * @return boolean
     */
    public function sendUserResetPasswordOtpEmail(string $to_email, string $otp, string $role)
    {
        if (empty($to_email) === true) {
            return false;
        }

        $this->email_service->sendResetPasswordOtpEmail($to_email, $otp, $role);

        return true;

    }//end sendUserResetPasswordOtpEmail()


    /**
     * Send access url with sms for properly url.
     *
     * @param string $login_url Login biturl.
     * @param string $dial_code Dial Code.
     * @param string $contact   Contact.
     *
     * @return void
     */
    public function sendLoginUrlwithSms(string $login_url, string $dial_code, string $contact)
    {
        if (empty($dial_code) === true || empty($contact) === true) {
            return;
        }

        if (empty($login_url) === true) {
            return;
        }

        $this->sms_service->sendLoginUrlLinkSms($login_url, $dial_code, $contact);

    }//end sendLoginUrlwithSms()


    /**
     * Create New User.
     *
     * @param array $user_data User data.
     *
     * @return array.
     */
    public function createUser(array $user_data)
    {
        // Save User info.
        $user_response = $this->saveUserData($user_data);

        if (empty($user_response) === true) {
            return [];
        }

        return $user_response;

    }//end createUser()


    /**
     * Save New User.
     *
     * @param array $user User data.
     *
     * @return object.
     */
    public function saveUserData(array $user)
    {
        // Randomly generated keys.
        $confirmation_code  = str_random(36);
        $auth_token         = str_random(36);
        $generated_password = str_random(6);

        // Get user ip and location.
        $ip_address    = Helper::getUserIpAddress();
        $user_location = Helper::getLocationByIp($ip_address);

        $user = User::createUserProfile(
            [
                'email'             => (isset($user['email']) === true) ? $user['email'] : $user['contact'].'@facebook.com',
                'password'          => (isset($user['password']) === true) ? $user['email'] : Hash::make($generated_password),
                'name'              => $user['name'],
                'last_name'         => $user['last_name'],
                'contact'           => $user['contact'],
                'dial_code'         => (isset($user['dial_code']) === true) ? $user['dial_code'] : '91',
                'ip_address'        => json_encode($user_location),
                'country'           => (isset($user['country']) === true) ? $user['country'] : $user_location['country_code'],
                'state'             => (isset($user['state']) === true) ? $user['state'] : $user_location['state_name'],
                'city'              => (isset($user['city']) === true) ? $user['city'] : $user_location['city'],
                'wallet_currency'   => (isset($user['wallet_currency']) === true) ? $user['wallet_currency'] : 'INR',
                'confirmation_code' => $confirmation_code,
                'auth_key'          => $auth_token,
                'signup_source'     => (isset($user['signup_source']) === true) ? $user['signup_source'] : 'website',
                'signup_method'     => (isset($user['signup_method']) === true) ? $user['signup_method'] : 'website',
                'profile_img'       => (isset($user['profile_img']) === true) ? $user['profile_img'] : '',
                'base_currency'     => (isset($user['base_currency']) === true) ? $user['base_currency'] : 'rupees',
                'prive_manager'     => (isset($user['prive_manager']) === true) ? $user['prive_manager'] : 0,
            ]
        );

        return $user;

    }//end saveUserData()


     /**
      * Generate and send otp to user phone.
      *
      * @param array   $user       User.
      * @param integer $otp_method Otp Method.
      *
      * @return array
      */
    public function generateAndSendOtp(array $user, int $otp_method)
    {
         // Check if a valid otp exists for user.
        $check_if_valid_otp_exists = OtpContact::checkIfValidOtpExists($user['id'], $user['dial_code'], $user['contact']);

        // Get valid otp for user.
        if (empty($check_if_valid_otp_exists) === false) {
            $otp_code = OtpContact::getExistingOtpForUser($user['id'], $user['dial_code'], $user['contact'], 0);
        } else {
            $otp_code = OtpContact::generateValidOtpForUser($user['id'], $user['dial_code'], $user['contact'], 0);
        }

        $contact = $user['dial_code'].$user['contact'];

        switch ($otp_method) {
            case 1:
                $view = view('sms.mobile_verification', ['verification_code' => $otp_code]);
                $msg  = $view->render();

                // Send Otp For Login.
                $result = SmsService::sendOtp($contact, $msg, VERIFY_SMS_SENDER_ID);
                Helper::logInfo('PROCESS SMS : Send otp sms via SmsService. Contact <<<<<<<<<<< '.$contact.' >>>>>>>>>>>>');
                $response['message'] = 'OTP code has been successfully sent to '.$user['contact'];
            break;

            case 2:
                $result = SmsService::call($contact, $otp_code);
                Helper::logInfo('PROCESS CALL : Otp via call in SmsService. Contact <<<<<<<<<<< '.$contact.' >>>>>>>>>>>>');
                $response['message'] = 'You will receive a call shortly on '.$user['contact'];
            break;

            case 3:
                $login_url = Helper::getShortUrl(PROPERLY_URL.'?phone='.$user['contact']);
                $view      = view('sms.user_loginurl_otp_sms', ['verification_code' => $otp_code, 'login_url' => $login_url]);
                $msg       = $view->render();

                // Send Otp For Login.
                $result = SmsService::sendOtp($contact, $msg, VERIFY_SMS_SENDER_ID);
                Helper::logInfo('PROCESS SMS : Send otp sms via SmsService. Contact <<<<<<<<<<< '.$contact.' >>>>>>>>>>>>');
                $response['message'] = 'OTP code has been successfully sent to '.$user['contact'];
            break;

            // phpcs:ignore
            default:
            break;
        }//end switch

        if ($result['status'] === 1) {
            $response['status'] = 1;
            return $response;
        } else {
            $response['status'] = 0;
        }

        return $response;

    }//end generateAndSendOtp()


    /**
     * Verify User send otp.
     *
     * @param integer $user_id   User Id.
     * @param string  $otp       Otp Code.
     * @param string  $phone     Phone Number of user.
     * @param string  $dial_code Dial Code.
     *
     * @return boolean|array
     */
    public function verifyUserOtp(int $user_id, string $otp, string $phone, string $dial_code)
    {
        // Validate if provided otp matches one in db.
        $check_if_otp_matches_user = OtpContact::checkForOtpMatch($user_id, $otp, $dial_code, $phone);

        if ($check_if_otp_matches_user['status'] === 1) {
            $mobile_verify  = 1;
            $verify_contact = User::updateUserContactDetail($user_id, $dial_code, $phone, $mobile_verify);

            // Delete otp details if user verified successfully.
             OtpContact::deletePreviousOtps($dial_code, $phone);
            return true;
        } else {
            return $check_if_otp_matches_user;
        }

    }//end verifyUserOtp()


    /**
     * Verify User send otp.
     *
     * @param User   $user             User.
     * @param string $otp              Otp Code.
     * @param string $phone            Phone Number of user.
     * @param string $dial_code        Dial Code.
     * @param string $device_unique_id Device Unique Id.
     *
     * @return boolean|array
     */
    public function checkOtpStatus(User $user, string $otp, string $phone, string $dial_code, string $device_unique_id)
    {
        // Validate if provided otp matches one in db.
        $check_if_otp_matches_user = SmsOtp::checkForOtpMatch($otp, $dial_code.$phone, $device_unique_id, OTP_TYPES['login'], 1);

        if ($check_if_otp_matches_user['status'] === 1) {
            if ($user->mobile_verify !== 1) {
                $user->markContactVerified();
            }

            return true;
        } else {
            return $check_if_otp_matches_user;
        }

    }//end checkOtpStatus()


    /**
     * Add User id corresponsing to device.
     *
     * @param integer $user_id          User Id.
     * @param string  $device_unique_id Device Unique Id.
     *
     * @return void
     */
    public function addUserDevice(int $user_id, string $device_unique_id) : void
    {
        // Set user id corresponding to device id.
        $mobile_app_device = MobileAppDevice::updateDeviceUser($device_unique_id, $user_id);

        if (empty($mobile_app_device) === false) {
            MobileAppDevice::updateDeviceTypeForIos($device_unique_id);
        }

    }//end addUserDevice()


     /**
      *  Check User is trashed By email id.
      *
      * @param string  $email_or_phone Email Id or phone number.
      * @param string  $dial_code      Dial Code  Dial code.
      * @param integer $source         Registration source that should be in (WEBSITE_SOURCE_ID, GOOGLE_SOURCE_ID, FACEBOOK_SOURCE_ID).
      * @param string  $social_id      Social Id For Google Signup, Facebook Signup).
      *
      * @return boolean
      */
    public function exists(string $email_or_phone, string $dial_code, int $source=WEBSITE_SOURCE_ID, string $social_id='') : ?User
    {
        // Get Trashed user by Email.
        $user = User::getUserWithTrashedByEmailOrPhone($email_or_phone, $dial_code);

        // Check by Facebook Id.
        if (empty($user) === true && $source === FACEBOOK_SOURCE_ID) {
            $user = User::getUserWithTrashedByFacebookId($social_id);
        }

        // Check by Apple Id.
        if (empty($user) === true && $source === APPLE_SOURCE_ID) {
            $user = User::getUserWithTrashedByAppleId($social_id);
        }

        return $user;

    }//end exists()


    /**
     * Activate User is trashed.
     *
     * @param User $user User.
     *
     * @return User $user
     */
    public function activateIfTrashed(User $user) : ?User
    {
        if (empty($user) === false) {
            $user->activateIfTrashed();
        }

        return $user;

    }//end activateIfTrashed()


     /**
      * Update User data.
      *
      * @param User  $user   User Instance.
      * @param array $params User data.
      *
      * @return User $user
      */
    public function update(User $user, array $params) : User
    {
        if (empty($user) === false) {
            // Update User Details.
            $user = $user->updateProfile($params);
        }

        return $user;

    }//end update()


     /**
      * Register User data.
      *
      * @param array   $params    User data.
      * @param integer $source    Registration source that should be in (WEBSITE_SOURCE_ID, GOOGLE_SOURCE_ID, FACEBOOK_SOURCE_ID).
      * @param string  $social_id Social Id For Google Signup, Facebook Signup).
      *
      * @return array
      */
    public function register(array $params, int $source=WEBSITE_SOURCE_ID, string $social_id='') : array
    {
        // Social Ids.
        $google_id = '';
        $fb_id     = '';
        $dob       = '';
        $apple_id  = '';

        // Signup Method.
        $signup_method = 'email';

        // Email Verify status.
        $email_verify = 0;

        $mobile_verify = 0;

        // Encode password.
        // Password should me encoded in table.
        $validated_data['password'] = Hash::make($params['password']);

        // Update Validated params for Social Login.
        if ($source === GOOGLE_SOURCE_ID) {
            // Set Social Id as Google Id.
            // Email Verify should be 1 for Gmail Login.
            $google_id     = $social_id;
            $email_verify  = 1;
            $signup_method = 'google';
        } else if ($source === FACEBOOK_SOURCE_ID) {
            // Set Social Id as Facebook Id.
            // Email Verify should be 1 when Email provided otherwise should be 0.
            $fb_id         = $social_id;
            $email_verify  = (strpos($params['email'], '@facebook.com') !== false) ? 0 : 1;
            $signup_method = 'facebook';
            $dob           = (empty($params['dob']) === false) ? $params['dob'] : '';
        } else if ($source === PHONE_SOURCE_ID) {
            $signup_method = 'phone';
            $mobile_verify = 1;
        } else if ($source === APPLE_SOURCE_ID) {
            $apple_id      = $social_id;
            $signup_method = 'apple';
            $email_verify  = $params['email_verify'];
        }//end if

        // Get user ip and location data.
        // Merge these data in user data for basic info.
        $ip_address    = Helper::getUserIpAddress();
        $user_location = Helper::getLocationByIp($ip_address);

        // Create user.
        $user = User::createUser(
            [
                // User Required data.
                'email'             => $params['email'],
                'name'              => $params['name'],
                'gender'            => $params['gender'],
                'contact'           => $params['contact'],
                'dial_code'         => $params['dial_code'],

                // User profile Info.
                'password'          => $params['password'],
                'last_name'         => $params['last_name'],
                'base_currency'     => $params['base_currency'],
                'profile_img'       => $params['profile_img'],

                // Social Info.
                'google_id'         => $google_id,
                'fb_id'             => $fb_id,
                'dob'               => $dob,

                // Register Tracking data.
                'signup_method'     => $signup_method,
                'signup_source'     => $params['signup_source'],

                // User Identification data.
                'email_verify'      => $email_verify,

                'mobile_verify'     => $mobile_verify,

                // User personal info via Location.
                'ip_address'        => $ip_address,
                'country'           => $user_location['country_code'],
                'state'             => $user_location['state_name'],
                'city'              => $user_location['city'],
                'wallet_currency'   => $user_location['currency'],

                // Merge Auth and Confirmation code of old Api.
                // We will remove in new Database structure.
                'confirmation_code' => str_random(36),
                'auth_key'          => str_random(36),

            ]
        );

        if (empty($user) === true) {
            return [
                'status' => false,
                'data'   => [],
                'error'  => [
                    'msg'   => 'database_issue',
                    'value' => [],
                ],
            ];
        }

        // Update User Referral Code.
        $referral_code = Helper::generateReferralCode($user->id);
        $user          = $this->update($user, ['referral_code' => $referral_code]);

        return [
            'status' => true,
            'data'   => $user,
            'error'  => [],
        ];

    }//end register()


    /**
     * Get user Profile.
     *
     * @param integer $user_id User Id.
     *
     * @return object
     */
    public function getUserProfile(int $user_id)
    {
        return User::getUserProfile($user_id);

    }//end getUserProfile()


     /**
      * Get user details by contact number.
      *
      * @param string $contact   User contact number.
      * @param string $dial_code User Dial code.
      *
      * @return object
      */
    public static function getUserByMobileNumber(string $contact, string $dial_code='')
    {
        return User::getUserByMobileNumber($contact, $dial_code);

    }//end getUserByMobileNumber()


    /**
     * Get user details by email.
     *
     * @param string $email User email.
     *
     * @return object
     */
    public static function getUserByEmail(string $email)
    {
        return User::getUserByEmail($email);

    }//end getUserByEmail()


     /**
      * Check If User Is prive owner or not
      *
      * @param User $user User.
      *
      * @return boolean
      */
    public function isPriveOwner(User $user)
    {
        if ($user->prive_owner === 1) {
            return true;
        }

        return false;

    }//end isPriveOwner()


    /**
     * Check If User Is prive Manager or not
     *
     * @param User $user User.
     *
     * @return boolean
     */
    public function isPriveManager(User $user)
    {
        if ($user->prive_manager === 1) {
            return true;
        }

        return false;

    }//end isPriveManager()


    /**
     * Get User Roles and permisssion.
     *
     * @param User $user User object.
     *
     * @return array.
     */
    public function getUserRolesPermissions(User $user)
    {
        $user_permissions = $user->getAllPermissions();

        $filtered_permissions = [];

        foreach ($user_permissions as $value) {
            $product = explode('#', $value['name'])[1];

            if (isset($filtered_permissions[$product]) === false) {
                $filtered_permissions[$product] = [];
            }

            $filtered_permissions[$product][] = [
                'name' => explode('#', $value['name'])[0],
            ];
        }

        $user_role = $user->roles->toArray();

        $filtered_roles = [];

        foreach ($user_role as $key => $value) {
            $product = explode('#', $value['name'])[1];

            if (isset($filtered_roles[$product]) === false) {
                $filtered_roles[$product] = [];
            }

            $filtered_roles[$product][] = [
                'name' => explode('#', $value['name'])[0],
            ];
        }

        return [
            'permissions' => $filtered_permissions,
            'roles'       => $filtered_roles,
        ];

    }//end getUserRolesPermissions()


    /**
     * Assign role to user.
     *
     * @param integer $user_id User id.
     * @param integer $role_id Role Id.
     *
     * @return void.
     */
    public function assignRoleToUser(int $user_id, int $role_id)
    {
        $user_object = new User;
        $user        = $user_object->getUser($user_id);
        $user->assignRole($role_id);

    }//end assignRoleToUser()


    /**
     * Assign permission to user.
     *
     * @param integer $user_id       User id.
     * @param integer $permission_id Permission Id.
     *
     * @return void.
     */
    public function assignPermissionToUser(int $user_id, int $permission_id)
    {
        $user_object = new User;
        $user        = $user_object->getUser($user_id);
        $user->givePermissionTo($permission_id);

    }//end assignPermissionToUser()


    /**
     * Assign properly designation to user.
     *
     * @param integer $user_id        User id.
     * @param integer $designation_id Designation Id.
     *
     * @return void.
     */
    public function assignProperlyDesignationToUser(int $user_id, int $designation_id)
    {
        $designation_mapping = new ProperlyDesignationUserMapping;
        $designation_mapping->assignDesignation($designation_id, $user_id);

    }//end assignProperlyDesignationToUser()


    /**
     * Assign properly designation to properties.
     *
     * @param integer $designation_id Designation Id.
     * @param array   $properties     Properties.
     *
     * @return void.
     */
    public function assignProperlyDesignationToProperty(int $designation_id, array $properties)
    {
        $designation_mapping = new ProperlyDesignationPidMapping;
        $designation_mapping->assignProperty($designation_id, $properties);

    }//end assignProperlyDesignationToProperty()


    /**
     * Assign roles and permissions.
     *
     * @param string $role_id     Role Id.
     * @param array  $permissions Permissions.
     *
     * @return boolean
     */
    public function assignPermissionsToRole(string $role_id, array $permissions)
    {
        $role_class = app(RoleContract::class);

        if (is_numeric($role_id) === true) {
            $role = $role_class::findById($role_id, 'api');
        } else {
            $role = $role_class::findByName($role_id, 'api');
        }

        if (empty($role) === true) {
            return false;
        }

        $role->givePermissionTo(...$permissions);

        return true;

    }//end assignPermissionsToRole()


    /**
     * Generate and send otp to user phone.
     *
     * @param array   $user             User.
     * @param integer $otp_method       Otp Method.
     * @param string  $device_unique_id Device unique id.
     * @param integer $otp_type         Otp type.
     *
     * @return array
     */
    public function generateOtp(array $user, int $otp_method, string $device_unique_id, int $otp_type)
    {
        // Check if a valid otp exists for user.
        $check_if_valid_otp_exists = SmsOtp::checkIfValidOtpExists($user['contact'], $device_unique_id, $otp_type);

        // Get valid otp for user.
        if (empty($check_if_valid_otp_exists) === false) {
            $otp_code = SmsOtp::getExistingOtpForUser($user['contact'], $otp_type);
        } else {
            $otp_code = SmsOtp::generateValidOtpForContact($user['contact'], $device_unique_id, $otp_type);
        }

        $contact = $user['contact'];

        switch ($otp_method) {
            case 1:
                $view = view('sms.mobile_verification', ['verification_code' => $otp_code]);
                $msg  = $view->render();

                // Send Otp For Login.
                $result = SmsService::sendOtp($contact, $msg, VERIFY_SMS_SENDER_ID);
                Helper::logInfo('PROCESS SMS : Send otp sms via SmsService. Contact <<<<<<<<<<< '.$contact.' >>>>>>>>>>>>');
                $response['message'] = 'OTP code has been successfully sent to '.$user['contact'];
            break;

            case 2:
                $result = SmsService::call($contact, $otp_code);
                Helper::logInfo('PROCESS CALL : Otp via call in SmsService. Contact <<<<<<<<<<< '.$contact.' >>>>>>>>>>>>');
                $response['message'] = 'You will receive a call shortly on '.$user['contact'];
            break;

            case 3:
                $login_url = Helper::getShortUrl(PROPERLY_URL.'?phone='.$user['contact']);
                $view      = view('sms.user_loginurl_otp_sms', ['verification_code' => $otp_code, 'login_url' => $login_url]);
                $msg       = $view->render();

                // Send Otp For Login.
                $result = SmsService::sendOtp($contact, $msg, VERIFY_SMS_SENDER_ID);
                Helper::logInfo('PROCESS SMS : Send otp sms via SmsService. Contact <<<<<<<<<<< '.$contact.' >>>>>>>>>>>>');
                $response['message'] = 'OTP code has been successfully sent to '.$user['contact'];
            break;

            // phpcs:ignore
            default:
            break;
        }//end switch

        if ($result['status'] === 1) {
            $response['status'] = 1;
            return $response;
        } else {
            $response['status'] = 0;
        }

        return $response;

    }//end generateOtp()


    /**
     * Function to check if otp exists.
     *
     * @param string  $contact          Contact.
     * @param string  $device_unique_id Device Unique id.
     * @param integer $type             Type.
     * @param integer $status           Status.
     *
     * @return integer Otp exists.
     */
    public static function checkIfValidOtpExists(string $contact, string $device_unique_id, int $type=0, int $status=0)
    {
        $check_if_valid_otp_exists = SmsOtp::checkIfValidOtpExists($contact, $device_unique_id, $type, $status);

        return $check_if_valid_otp_exists;

    }//end checkIfValidOtpExists()


    /**
     * Verify User send otp.
     *
     * @param string  $otp              Otp Code.
     * @param string  $contact          Phone Number of user with dial code.
     * @param string  $device_unique_id Device unique id of user.
     * @param integer $otp_type         Otp type 0 for login and 1 for forgot password.
     * @param integer $user_id          User id of user default 0.
     *
     * @return boolean|array
     */
    public function verifyOtp(string $otp, string $contact, string $device_unique_id, int $otp_type, int $user_id=0)
    {
        // Validate if provided otp matches one in db.
        $check_if_otp_matches_user = SmsOtp::checkForOtpMatch($otp, $contact, $device_unique_id, $otp_type);

        if ($check_if_otp_matches_user['status'] === 1) {
            // Query params in where clause.
            $params = [
                'verification_code' => $otp,
                'type'              => $otp_type,
                'contact'           => $contact,
                'device_unique_id'  => $device_unique_id,
            ];

            $update_details = ['status' => '1'];
            $verify_contact = SmsOtp::updateSmsOtpDetails($params, $update_details);

            // Return 0 for login and 1 for forgot password otp.
            return true;
        } else {
            return $check_if_otp_matches_user;
        }

    }//end verifyOtp()


    /**
     * Function to check if otp exists.
     *
     * @param string  $contact          Contact.
     * @param string  $otp              Otp.
     * @param string  $device_unique_id Device Unique id.
     * @param integer $type             Type.
     * @param integer $status           Status.
     *
     * @return integer Otp exists.
     */
    public static function checkIfVerifyLoginOtpExists(string $contact, string $otp, string $device_unique_id, int $type=0, int $status=0)
    {
        $check_if_valid_otp_exists = SmsOtp::checkIfVerifyLoginOtpExists($contact, $otp, $device_unique_id, $type, $status);

        return $check_if_valid_otp_exists;

    }//end checkIfVerifyLoginOtpExists()


    /**
     * Function to verify contact of existing user.
     *
     * @param string $contact   Contact.
     * @param string $dial_code Dial Code.
     *
     * @return void.
     */
    public static function verifyContact(string $contact, string $dial_code)
    {
        $user = self::getUserByMobileNumber($contact, $dial_code);

        if (empty($user) === true || (isset($user[0]) === true && $user[0]->mobile_verify === 1)) {
            return;
        }

        (isset($user[0]) === true) ? User::updateUserContactDetail($user[0]->id, $user[0]->dial_code, $user[0]->contact, 1) : '';

    }//end verifyContact()


}//end class
