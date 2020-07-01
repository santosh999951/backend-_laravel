<?php
/**
 * SmsOtp contain all functions related to otp contacts
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SmsOtp
 */
class SmsOtp extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'sms_otp';


    /**
     * Function to check if otp exists.
     *
     * @param string  $contact          Contact.
     * @param string  $device_unique_id Device Unique id.
     * @param integer $type             Type.
     * @param integer $status           Status.
     * @param integer $user_id          User id.
     *
     * @return integer Otp exists.
     */
    public static function checkIfValidOtpExists(string $contact, string $device_unique_id, int $type=OTP_TYPES['login'], int $status=0, int $user_id=0)
    {
        $check_if_valid_otp_exists = self::where('valid_till', '>', Carbon::now('Asia/Kolkata')->timezone('UTC')->toDateTimeString());
        if ($user_id !== 0) {
            $check_if_valid_otp_exists->where('user_id', $user_id);
        } else {
            $check_if_valid_otp_exists->where('contact', $contact);
        }

        $check_if_valid_otp_exists->where('type', $type)->where('status', $status)->where('device_unique_id', $device_unique_id)->orderby('id', 'DESC');
        $check_if_valid_otp_exists = $check_if_valid_otp_exists->first();

        return $check_if_valid_otp_exists;

    }//end checkIfValidOtpExists()


    /**
     * Function to generate valid otp.
     *
     * @param string  $contact          User id.
     * @param string  $device_unique_id Dial Code.
     * @param integer $type             Type.
     * @param integer $user_id          User Id.
     *
     * @return integer Otp.
     */
    public static function generateValidOtpForContact(string $contact, string $device_unique_id, int $type=OTP_TYPES['login'], int $user_id=0)
    {
        $valid_till = Carbon::now()->addMinutes(OTP_EXPIRY_TIME);

        $code            = mt_rand(1000, 9999);
        $newotp          = new self;
        $newotp->user_id = $user_id;
        $newotp->contact = $contact;
        $newotp->verification_code = $code;
        $newotp->valid_till        = $valid_till;
        $newotp->device_unique_id  = $device_unique_id;
        $newotp->type              = $type;

        if ($newotp->save() === true) {
            return $code;
        }

    }//end generateValidOtpForContact()


    /**
     * Function otp for existing otp.
     *
     * @param string  $contact Contact.
     * @param integer $type    Type.
     * @param integer $user_id User id.
     *
     * @return string Otp.
     */
    public static function getExistingOtpForUser(string $contact, int $type=OTP_TYPES['login'], int $user_id=0)
    {
        $get_otp = self::select('verification_code')->where('verification_code', '!=', '')->where('valid_till', '>', Carbon::now('GMT'));
        if ($user_id !== 0) {
            $get_otp->where('user_id', $user_id);
        } else {
            $get_otp->where('contact', $contact);
        }

        $get_otp->where('type', $type)->where('status', 0)->orderby('id', 'DESC');
        $get_otp = $get_otp->first();

        if (empty($get_otp) === false) {
            return $get_otp->verification_code;
        }

        return '';

    }//end getExistingOtpForUser()


    /**
     * Function otp match.
     *
     * @param integer $otp              Otp.
     * @param string  $contact          Contact with dial code.
     * @param string  $device_unique_id Device Unique Id.
     * @param integer $type             OTP type.
     * @param integer $status           Status.
     * @param integer $user_id          User id of user.
     *
     * @return array Otp match status.
     */
    public static function checkForOtpMatch(int $otp, string $contact, string $device_unique_id, int $type=OTP_TYPES['login'], int $status=0, int $user_id=0)
    {
        $check_if_user_exists_for_given_otp = self::select()->where('verification_code', '=', $otp);

        // Check for otp match in case of user either exist or not.
        if ($user_id === 0) {
            $check_if_user_exists_for_given_otp->where('contact', '=', $contact);
        } else {
            $check_if_user_exists_for_given_otp->where('user_id', '=', $user_id);
        }

        $check_if_user_exists_for_given_otp->where('type', $type)->where('status', $status)->where('device_unique_id', $device_unique_id)->orderby('id', 'DESC');
        $check_if_user_exists_for_given_otp = $check_if_user_exists_for_given_otp->first();

        if (empty($check_if_user_exists_for_given_otp) === false) {
            if (Carbon::now('GMT') > $check_if_user_exists_for_given_otp->valid_till) {
                $result['message'] = 'Your OTP has been expired.';
                $result['status']  = 0;
            } else {
                $result['message'] = 'Contact verified successfully.';
                $result['status']  = 1;
            }
        } else {
            $result['message'] = 'Invalid OTP entered.';
            $result['status']  = 0;
        }

        return $result;

    }//end checkForOtpMatch()


    /**
     * Function to delete previous otps .
     *
     * @param string  $contact Contact.
     * @param integer $type    OTP Type.
     *
     * @return boolean Otp match status.
     */
    public static function isOtpLimitReached(string $contact, int $type=0)
    {
        $day_limit  = Carbon::now('GMT')->subDay(1)->toDateTimeString();
        $hour_limit = Carbon::now('GMT')->subHour(1)->toDateTimeString();

        $day_limit_count = self::where('contact', $contact)->where('type', $type)->where('created_at', '>=', $day_limit)->count();

        $hour_limit_count = self::where('contact', $contact)->where('type', $type)->where('created_at', '>=', $hour_limit)->count();

        if ($day_limit_count < MAX_RESET_PASSWORD_OTP_PER_DAY && $hour_limit_count < MAX_RESET_PASSWORD_OTP_PER_HOUR) {
            return false;
        }

        return true;

    }//end isOtpLimitReached()


    /**
     * Function to check OTP Send Limit Reached, for resetting the password.
     *
     * @param integer $user_id User id.
     *
     * @return boolean OTP send limit reached or not.
     */
    public static function isOtpSendLimitReachedToResetPassword(int $user_id)
    {
        $day_limit  = Carbon::now('GMT')->subDay(1)->toDateTimeString();
        $hour_limit = Carbon::now('GMT')->subHour(1)->toDateTimeString();

        $day_limit_count  = self::where('user_id', $user_id)->where('type', OTP_TYPES['reset_password'])->where('created_at', '>=', $day_limit)->count();
        $hour_limit_count = self::where('user_id', $user_id)->where('type', OTP_TYPES['reset_password'])->where('created_at', '>=', $hour_limit)->count();

        if ($day_limit_count < MAX_RESET_PASSWORD_OTP_PER_DAY && $hour_limit_count < MAX_RESET_PASSWORD_OTP_PER_HOUR) {
            return false;
        }

        return true;

    }//end isOtpSendLimitReachedToResetPassword()


    /**
     * Function to update status after verifyin otp.
     *
     * @param array $params         Query params.
     * @param array $update_details Update params.
     *
     * @return boolean
     */
    public static function updateSmsOtpDetails(array $params, array $update_details)
    {
        $update_user_data = self::where($params)->update($update_details);

        if ($update_user_data > 0) {
            return true;
        }

        return false;

    }//end updateSmsOtpDetails()


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
        $check_if_valid_otp_exists = self::where('contact', '=', $contact)->where('verification_code', $otp)->where('type', $type)->where('status', $status)->orderby('id', 'DESC')->first();

        return $check_if_valid_otp_exists;

    }//end checkIfVerifyLoginOtpExists()


}//end class
