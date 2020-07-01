<?php
/**
 * Model containing data regarding oauth tokens
 */

namespace App\Models;

use DateTime;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PassportMemory
 */
class PassportMemory extends Model
{


    /**
     * Check if access token is revoked.
     *
     * @param string $access_token Access token.
     *
     * @return integer
     */
    public static function isAccessTokenRevoked(string $access_token)
    {
        if (Redis::exists('AT:'.$access_token) === true) {
            // Key exists.
            if (json_decode(Redis::get('AT:'.$access_token))->revoked !== 1) {
                // Token not revoked.
                return 0;
            }
        }

        return 1;

    }//end isAccessTokenRevoked()


    /**
     * Check if access token has expired.
     *
     * @param string $access_token Access token.
     *
     * @return integer
     */
    public static function isAccessTokenExpired(string $access_token)
    {
        if (Redis::exists('AT:'.$access_token) === true) {
            // Key exists.
            $expires_at = new DateTime(json_decode(Redis::get('AT:'.$access_token))->expires_at->date);
            $now        = new DateTime();
            if ($expires_at > $now) {
                // Token not expired.
                return 0;
            }
        }

        return 1;

    }//end isAccessTokenExpired()


    /**
     * Set access token data.
     *
     * @param array $access_token_data Set access token data.
     *
     * @return null
     */
    public static function setGeneratedAccessTokenData(array $access_token_data)
    {
        $key = 'AT:'.$access_token_data['access_token'];
        Redis::set($key, json_encode($access_token_data));
        Redis::expire($key, PASSPORT_ACCESS_TOKEN_TTL);

        return null;

    }//end setGeneratedAccessTokenData()


    /**
     * Get access token data.
     *
     * @param string $access_token Access token.
     *
     * @return array
     */
    public static function getAccessTokenData(string $access_token)
    {
        return json_decode(Redis::get('AT:'.$access_token));

    }//end getAccessTokenData()


    /**
     * Set refresh tooken data.
     *
     * @param array $refresh_token_data Refresh access token.
     *
     * @return null;
     */
    public static function setGeneratedRefreshTokenData(array $refresh_token_data)
    {
        $key = 'RT:'.$refresh_token_data['refresh_token'];
        Redis::set($key, json_encode($refresh_token_data));
        Redis::expire($key, PASSPORT_ACCESS_TOKEN_TTL);

        return null;

    }//end setGeneratedRefreshTokenData()


    /**
     * Get user data.
     *
     * @param integer $user_id User id.
     *
     * @return array
     */
    public static function getUserData(int $user_id)
    {
        return unserialize(Redis::get('User:'.$user_id));

    }//end getUserData()


    /**
     * Set user data.
     *
     * @param integer $user_id   User id.
     * @param User    $user_data User data to be set.
     *
     * @return null
     */
    public static function setUserData(int $user_id, User $user_data)
    {
        Redis::set('User:'.$user_id, serialize($user_data));

        return null;

    }//end setUserData()


}//end class
