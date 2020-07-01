<?php
/**
 * OnetimeAccessToken Model containing all functions related to one time access token table
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Admin
 */
class OnetimeAccessToken extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'onetime_access_token';


    /**
     * Encode a string with URL-safe Base64.
     *
     * @param string $input The string you want encoded.
     *
     * @return string The base64 encode of what you passed in.
     */
    public static function urlsafeB64Encode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));

    }//end urlsafeB64Encode()


    /**
     * Make Token.
     *
     * @param integer $user_id            User Id.
     * @param integer $admin_id           Admin Id.
     * @param integer $expire_time_in_min Token Expire time.
     *
     * @return object
     */
    public static function makeToken(int $user_id, int $admin_id, int $expire_time_in_min=15)
    {
        $time = time();

        $payload = md5(str_random(60)).'_'.($time + ($expire_time_in_min * 60));

        $existing_token = self::where('user_id', $user_id)->where('admin_id', $admin_id)->where('expire_time', '>', $time)->first();

        if (empty($existing_token) === false) {
            return $existing_token;
        }

        $token = static::urlsafeB64Encode(json_encode($payload));

        $ota_token              = new self;
        $ota_token->token       = $token;
        $ota_token->admin_id    = $admin_id;
        $ota_token->user_id     = $user_id;
        $ota_token->expire_time = ($time + ($expire_time_in_min * 60));

        if ($ota_token->save() === false) {
            return (object) [];
        }

        return $ota_token;

    }//end makeToken()


    /**
     * Validate Token.
     *
     * @param string $token Token.
     *
     * @return array
     */
    public function validateToken(string $token)
    {
        $time           = time();
        $existing_token = self::where('token', $token)->where('expire_time', '>', $time)->first();

        if (empty($existing_token) === true) {
            return [];
        }

        return $existing_token->toArray();

    }//end validateToken()


}//end class
