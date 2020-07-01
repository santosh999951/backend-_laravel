<?php
/**
 * OtpContact contain all functions related to otp contacts
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OtpContact
 */
class OtpContact extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'otp_contact';


    /**
     * Function to check if otp exists.
     *
     * @param integer $user_id  User id.
     * @param string  $dialcode Dial Code.
     * @param string  $contact  Contact.
     *
     * @return integer Otp exists.
     */
    public static function checkIfValidOtpExists(int $user_id, string $dialcode, string $contact)
    {
        $check_if_valid_otp_exists = self::where('user_id', '=', $user_id)->where('dialcode', '=', $dialcode)->where('contact', '=', $contact)->where('otp', '!=', '')->where('valid_till', '>', Carbon::now('GMT'))->orderby('id', 'DESC')->count();

        return $check_if_valid_otp_exists;

    }//end checkIfValidOtpExists()


    /**
     * Function to generate valid otp.
     *
     * @param integer $user_id        User id.
     * @param string  $dialcode       Dial Code.
     * @param string  $contact        Contact.
     * @param integer $contact_change Contact Change.
     *
     * @return integer Otp.
     */
    public static function generateValidOtpForUser(int $user_id, string $dialcode, string $contact, int $contact_change=1)
    {
        $valid_till = Carbon::now('GMT')->addMinute(OTP_EXPIRY_TIME);

        $code                   = mt_rand(1000, 9999);
        $newotp                 = new self;
        $newotp->user_id        = $user_id;
        $newotp->dialcode       = $dialcode;
        $newotp->contact        = $contact;
        $newotp->otp            = $code;
        $newotp->contact_change = $contact_change;
        $newotp->valid_till     = $valid_till;

        if ($newotp->save() === true) {
            return $code;
        }

    }//end generateValidOtpForUser()


    /**
     * Function otp for existing otp.
     *
     * @param integer $user_id  User id.
     * @param string  $dialcode Dial Code.
     * @param string  $contact  Contact.
     *
     * @return string Otp.
     */
    public static function getExistingOtpForUser(int $user_id, string $dialcode, string $contact)
    {
        $get_otp = self::select('otp')->where('user_id', '=', $user_id)->where('dialcode', '=', $dialcode)->where('contact', '=', $contact)->where('otp', '!=', '')->where('valid_till', '>', Carbon::now('GMT'))->orderby('id', 'DESC')->first();

        if (empty($get_otp) === false) {
            return $get_otp->otp;
        }

    }//end getExistingOtpForUser()


     /**
      * Function otp match .
      *
      * @param integer $user_id  User id.
      * @param integer $otp      Otp.
      * @param string  $dialcode Dial Code.
      * @param string  $contact  Contact.
      *
      * @return array Otp match status.
      */
    public static function checkForOtpMatch(int $user_id, int $otp, string $dialcode, string $contact)
    {
        $check_if_user_exists_for_given_otp = self::select()->where('user_id', '=', $user_id)->where('dialcode', '=', $dialcode)->where('contact', '=', $contact)->where('otp', '=', $otp)->orderby('id', 'DESC')->first();

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
     * @param string $dialcode Dial Code.
     * @param string $contact  Contact.
     *
     * @return boolean Otp match status.
     */
    public static function deletePreviousOtps(string $dialcode, string $contact)
    {
        $check_if_contact_deleted = self::where('dialcode', '=', $dialcode)->where('contact', '=', $contact)->delete();

        if ($check_if_contact_deleted > 0) {
            return true;
        }

        return false;

    }//end deletePreviousOtps()


    /**
     * Function to delete previous otps .
     *
     * @param string  $contact Contact.
     * @param integer $user_id User id.
     *
     * @return boolean Otp match status.
     */
    public static function isOtpLimitReached(string $contact, int $user_id)
    {
        $day_limit  = Carbon::now('GMT')->subDay(1)->toDateTimeString();
        $hour_limit = Carbon::now('GMT')->subHour(1)->toDateTimeString();

        $day_limit_count = self::where('contact', $contact)->where('user_id', $user_id)->where('created_at', '>=', $day_limit)->count();

        $hour_limit_count = self::where('contact', $contact)->where('user_id', $user_id)->where('created_at', '>=', $hour_limit)->count();

        if ($day_limit_count < MAX_RESET_PASSWORD_OTP_PER_DAY && $hour_limit_count < MAX_RESET_PASSWORD_OTP_PER_HOUR) {
            return false;
        }

        return true;

    }//end isOtpLimitReached()


}//end class
