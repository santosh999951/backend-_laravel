<?php
/**
 * OauthRmMobileDeviceMapping Model contain all functions related to rm mobile devices
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * Class OauthRmMobileDeviceMapping
 */
class OauthRmMobileDeviceMapping extends Model
{
    //phpcs:disable


    use SoftDeletes;

    /**
     * deleted_at properties
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'oauth_rm_mobile_device_mapping';


    public static function mapRmToHostMobileDevice($rm_user_id, $host_id=0, $device_unique_id=0)
    {
        $rmMapping = self::where('device_unique_id', '=', $device_unique_id)->first();
        if ($rmMapping) {
            $row = $rmMapping;
        } else {
            $row = new OauthRmMobileDeviceMapping;
        }

        $row->device_unique_id = $device_unique_id;
        $row->host_id          = $host_id;
        $row->user_id          = $rm_user_id;
        // rm id from users table
        $row->save();
        return true;

    }//end mapRmToHostMobileDevice()


    public static function unlinkRmFromMobileDevice($rm_id)
    {
        return self::where('user_id', '=', $rm_id)->delete();

    }//end unlinkRmFromMobileDevice()


}//end class
