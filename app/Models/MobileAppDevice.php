<?php
/**
 * MobileAppDevice contain all functions related to Mobile App Devices.
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;

/**
 * Class Invite
 */
class MobileAppDevice extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'mobile_app_devices';


    /**
     * Function to add user data with this.
     *
     * @return object User.
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');

    }//end user()


    /**
     * Helper function to create scope with status equal one
     *
     * @param string $device_unique_id Device Unique id.
     *
     * @return object Devices.
     */
    public static function getDeviceByDeviceUniqueId(string $device_unique_id)
    {
        return self::where('device_unique_id', $device_unique_id)->first();

    }//end getDeviceByDeviceUniqueId()


    /**
     * Helper function to update last active
     *
     * @param string $device_unique_id Device Unique id.
     *
     * @return integer Devices.
     */
    public static function updateLastActive(string $device_unique_id)
    {
        return self::where('device_unique_id', $device_unique_id)->update(['last_active' => Carbon::now()]);

    }//end updateLastActive()


    /**
     * Helper function to update device user
     *
     * @param string  $device_unique_id Device Unique id.
     * @param integer $user_id          User id.
     *
     * @return integer Devices.
     */
    public static function updateDeviceUser(string $device_unique_id, int $user_id)
    {
        return self::where('device_unique_id', $device_unique_id)->update(['user_id' => $user_id, 'last_active' => Carbon::now(), 'last_login' => Carbon::now(), 'status' => 1]);

    }//end updateDeviceUser()


    /**
     * Helper function to get all devices of user
     *
     * @param integer $user User id.
     *
     * @return object Devices.
     */
    public static function getUserDevice(int $user)
    {
        $devices = self::where('user_id', '=', $user)->where('device_id', '!=', '')->select('device_id', 'device_type')->get();

        if (empty($devices) === true) {
            return [];
        }

        $device_data = $devices->toArray();

        $device_ids_mapped_with_type = [];

        foreach ($device_data as $device) {
            $device_type = strtolower($device['device_type']);

            if (in_array($device_type, ['android', 'iphone']) === true) {
                $device_ids_mapped_with_type[$device['device_id']] = $device_type;
            }
        }

        return $device_ids_mapped_with_type;

    }//end getUserDevice()


    /**
     * Helper function to update device status
     *
     * @param array $device_ids Device Ids.
     *
     * @return void.
     */
    public static function updateDeviceStatus(array $device_ids)
    {
        // Group device ids.
        $device_ids = array_chunk($device_ids, 100);

        foreach ($device_ids as $device) {
            self::whereIn('device_id', $device_ids)->update(['status' => 0]);
        }

    }//end updateDeviceStatus()


    /**
     * Helper function to update device type
     *
     * @param string $device_unique_id Device Unique Id.
     *
     * @return object Devices.
     */
    public static function updateDeviceTypeForIos(string $device_unique_id)
    {
        return self::where('device_unique_id', $device_unique_id)->where('device_type', 'ios')->update(['device_type' => 'iPhone']);

    }//end updateDeviceTypeForIos()


}//end class
