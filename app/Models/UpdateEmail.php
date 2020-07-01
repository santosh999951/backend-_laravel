<?php
/**
 * Model containing data regarding update user email token
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UpdateEmail
 */
class UpdateEmail extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'change_usersmail';

    /**
     * Variable definition.
     *
     * @var boolean
     */
    public $timestamps = false;


    /**
     * Generate a new confirmation code for email verification of user.
     *
     * @param integer $user_id User id to send confirmation code.
     * @param string  $email   User email.
     *
     * @return string True/false
     */
    public static function generateConfirmationCodeForEmailVerification(int $user_id, string $email)
    {
        // Generate new confirmation code string.
        $confirmation_code = str_random(36);

        $check_if_confirmation_code_exists = self::getDataByConfirmationCode($confirmation_code);

        if (empty($check_if_confirmation_code_exists) === false) {
            return self::generateConfirmationCodeForEmailVerification($user_id, $email);
        }

        $check_for_update_email_token = self::find($user_id);

        // If there is already an entry for user id.
        if (empty($check_for_update_email_token) === false) {
            $check_for_update_email_token->confirmation_code = $confirmation_code;

            if ($check_for_update_email_token->save() === false) {
                return '';
            }
        } else {
            $generate_update_email_token                    = new self;
            $generate_update_email_token->user_id           = $user_id;
            $generate_update_email_token->email             = $email;
            $generate_update_email_token->confirmation_code = $confirmation_code;

            if ($generate_update_email_token->save() === false) {
                return '';
            }
        }

        return $confirmation_code;

    }//end generateConfirmationCodeForEmailVerification()


    /**
     * Get email and user_id data by email verification confirmation code.
     *
     * @param string $confirmation_code Email confirmation code.
     *
     * @return object
     */
    public static function getDataByConfirmationCode(string $confirmation_code)
    {
        return self::select('user_id', 'email')->where('confirmation_code', $confirmation_code)->get();

    }//end getDataByConfirmationCode()


    /**
     * Delete email verification token for user.
     *
     * @param integer $user_id User id for which email tokens to be deleted.
     *
     * @return boolean True/false
     */
    public static function deleteEmailVerificationToken(int $user_id)
    {
        return self::where('user_id', $user_id)->delete();

    }//end deleteEmailVerificationToken()


}//end class
