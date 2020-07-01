<?php
/**
 * Model containing data regarding password reminders
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class PasswordReminder
 */
class PasswordReminder extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'password_reminders';

    /**
     * Variable definition.
     *
     * @var boolean
     */
    public $timestamps = false;


    /**
     * Boot function.
     *
     * @return null;
     */
    public static function boot()
    {
        parent::boot();

        static::creating(
            function ($model) {
                    $model->setCreatedAt($model->freshTimestamp());
            }
        );

        return null;

    }//end boot()


    /**
     * Get reset password token associated with email id.
     *
     * @param string $email Email of user.
     *
     * @return string
     */
    public static function getResetPasswordTokenByEmail(string $email)
    {
        $token       = '';
        $reset_token = self::select('token')->where('email', $email)->orderBy('created_at', 'DESC')->first();
        if (empty($reset_token) === true) {
            return $token;
        }

        $token = $reset_token->token;
        return $token;

    }//end getResetPasswordTokenByEmail()


    /**
     * Get email for entered token.
     *
     * @param string $token Password token of user.
     *
     * @return string
     */
    public static function getEmailByToken(string $token)
    {
        $email      = '';
        $day_limit  = Carbon::now('GMT')->subDay(1)->toDateTimeString();
        $user_email = self::select('email')->where('token', $token)->where('created_at', '>', $day_limit)->orderBy('created_at', 'DESC')->first();

        if (empty($user_email) === true) {
            return $email;
        }

        $email = $user_email->email;

        return $email;

    }//end getEmailByToken()


    /**
     * Generate password token.
     *
     * @param string $email Email of user.
     *
     * @return string
     */
    public static function generateResetPasswordTokenForEmail(string $email)
    {
        $token = '';
        // Generate new token string.
        $token = str_shuffle(TOKEN_FOR_RESET_PASSWORD);

        $new_token        = new self;
        $new_token->email = $email;
        $new_token->token = $token;
        if ($new_token->save() === false) {
            return $token;
        }

        return $token;

    }//end generateResetPasswordTokenForEmail()


    /**
     * Delete password token.
     *
     * @param string $email Email of user.
     *
     * @return boolean
     */
    public static function deletePasswordToken(string $email)
    {
        return self::where('email', $email)->delete();

    }//end deletePasswordToken()


}//end class
