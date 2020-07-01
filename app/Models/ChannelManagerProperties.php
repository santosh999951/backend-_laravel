<?php
/**
 * ChannelManagerProperties Model containing all functions related to ChannelManagerProperties table
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ChannelManagerProperties
 */
class ChannelManagerProperties extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'channel_manager_properties';


    /**
     * Get bcom data
     *
     * @param integer $property_id Property id.
     *
     * @return object
     */
    public static function getBcomDataByProperty(int $property_id)
    {
        return self::where('pid', $property_id)->where('cm_code', CM_BCOM)->where('create_property', 5)->first();

    }//end getBcomDataByProperty()


    /**
     * Get Airbnb data
     *
     * @param integer $property_id Property id.
     *
     * @return array
     */
    public static function getAirbnbDataByProperty(int $property_id)
    {
        $channel_manager_properties = self::where('pid', $property_id)->where('cm_code', CM_AIRBNB)->where('active_cm', 1)->where('sync_enable_manual', 1)->whereIn('rate_sync_status', [0, 1]);
        $channel_manager_properties = $channel_manager_properties->select('pid', 'cm_code', 'id as cmp_id', 'account_id')->get();

        if (empty($channel_manager_properties) === false) {
            return $channel_manager_properties->toArray();
        }

        return [];

    }//end getAirbnbDataByProperty()


    /**
     * Save bcom data
     *
     * @param array   $custom_data     Custom Data.
     * @param integer $create_property Create Property Count.
     *
     * @return object
     */
    public function saveCustomData(array $custom_data, int $create_property)
    {
        $this->custom_data     = json_encode($custom_data, true);
        $this->create_property = $create_property;
        $this->save();

        return $this;

    }//end saveCustomData()


}//end class
