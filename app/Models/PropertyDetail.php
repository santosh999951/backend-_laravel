<?php
/**
 * Model containing data regarding property details
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class PropertyDetail
 */
class PropertyDetail extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'property_details';

     /**
      * Timestamp.
      *
      * @var boolean
      */
    public $timestamps = false;


    /**
     * Save Property Details
     *
     * @param array $params Property Details Params.
     *
     * @return object
     */
    public function savePropertyDetails(array $params)
    {
        $existing_detail = self::where('pid', $params['property_id'])->first();

        if (empty($existing_detail) === false) {
            $property_details = $existing_detail;
        } else {
            $property_details          = new self;
            $property_details->user_id = $params['user_id'];
            $property_details->pid     = $params['property_id'];
        }

        // Property House Rule and service policy.
        $property_details->policy_services = $params['policy_services'];
        $property_details->your_space      = $params['your_space'];
        $property_details->house_rule      = $params['house_rule'];

        // Property Experience.
        $property_details->guest_brief            = $params['guest_brief'];
        $property_details->interaction_with_guest = $params['interaction_with_guest'];
        $property_details->local_experience       = $params['local_experience'];

        // Property Navigation details.
        $property_details->from_airport  = $params['from_airport'];
        $property_details->train_station = $params['train_station'];
        $property_details->bus_station   = $params['bus_station'];

        // Extra Info about Property.
        $property_details->extra_detail = $params['extra_detail'];

        // Property USP.
        $property_details->usp = $params['usp'];

        if ($property_details->save() === false) {
            return (object) [];
        }

        return $property_details;

    }//end savePropertyDetails()


    /**
     * Get Property Details
     *
     * @param integer $property_id Property Id.
     *
     * @return object
     */
    public function getPropertyDetails(int $property_id)
    {
        $property_details = self::where('pid', $property_id)->first();

        return $property_details;

    }//end getPropertyDetails()


    /**
     * Get all property details.
     *
     * @param integer $property_id Property id.
     *
     * @return array
     */
    public static function getAllDetailsOfProperty(int $property_id)
    {
        $details = self::where('pid', $property_id)->first();

        if (empty($details) === true) {
            return [
                'space'                  => '',
                'house_rules'            => '',
                'extra_details'          => '',
                'policy_services'        => '',
                'guest_brief'            => '',
                'local_experience'       => '',
                'interaction_with_guest' => '',
                'how_to_reach'           => [],
                'usp'                    => '',
            ];
        }

        $how_to_reach = [];

        if (empty($details->from_airport) === false) {
            $how_to_reach[] = [
                'key'   => 'airport',
                'value' => $details->from_airport,
                'icon'  => CDN_URL.HOW_TO_REACH_PROPERTY_URL.'airport_icon.png',
            ];
        }

        if (empty($details->train_station) === false) {
            $how_to_reach[] = [
                'key'   => 'train_station',
                'value' => $details->train_station,
                'icon'  => CDN_URL.HOW_TO_REACH_PROPERTY_URL.'railway_station_icon.png',
            ];
        }

        if (empty($details->bus_station) === false) {
            $how_to_reach[] = [
                'key'   => 'bus_station',
                'value' => $details->bus_station,
                'icon'  => CDN_URL.HOW_TO_REACH_PROPERTY_URL.'bus_stand_icon.png',
            ];
        }

        return [
            'space'                  => trim($details->your_space),
            'house_rules'            => trim($details->house_rule),
            'extra_details'          => trim($details->extra_detail),
            'policy_services'        => trim($details->policy_services),
            'guest_brief'            => trim($details->guest_brief),
            'local_experience'       => trim($details->local_experience),
            'interaction_with_guest' => trim($details->interaction_with_guest),
            'how_to_reach'           => $how_to_reach,
            // phpcs:ignore
            'usp'                      => trim($details->USP)
        ];

    }//end getAllDetailsOfProperty()


     /**
      * Clone Property Detail.
      *
      * @param Property $property Property Model.
      * @param integer  $pid      Property Id.
      *
      * @return boolean
      */
    public static function clonePropertyDetail(Property $property, int $pid)
    {
        $property_details         = $property->property_details()->first();
        $clonepropertydetail      = $property_details->replicate();
        $clonepropertydetail->pid = $pid;
        if ($clonepropertydetail->save() === true) {
            return true;
        }

        return false;

    }//end clonePropertyDetail()


}//end class
