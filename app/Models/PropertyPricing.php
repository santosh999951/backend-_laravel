<?php
/**
 * Model containing data regarding property pricing
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\Helper;

/**
 * Class PropertyPricing
 */
class PropertyPricing extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'property_pricing';


     /**
      * Save Property Initial Pricing.
      *
      * @param array $params Property Pricing Params.
      *
      * @return object
      */
    public function savePropertyPricing(array $params)
    {
        $existing_detail = self::where('pid', $params['property_id'])->first();

        if (empty($existing_detail) === false) {
            $property_pricing = $existing_detail;
        } else {
            $property_pricing      = new self;
            $property_pricing->pid = $params['property_id'];
        }

        // Property Pricing.
        $property_pricing->per_night_price = $params['per_night_price'];
        $property_pricing->per_week_price  = $params['per_week_price'];
        $property_pricing->per_month_price = $params['per_month_price'];

        // Extra Guest Pricing.
        $property_pricing->additional_guest_count = $params['extra_guest_count'];
        $property_pricing->additional_guest_fee   = $params['extra_guest_fee'];

        // Property Commissions.
        $property_pricing->gh_commission      = $params['gh_commission'];
        $property_pricing->markup_service_fee = $params['markup_service_fee'];

        // Property Cleaning Fee Info.
        $property_pricing->cleaning_mode = $params['cleaning_mode'];
        $property_pricing->cleaning_fee  = $params['cleaning_fee'];

        if ($property_pricing->save() === false) {
            return (object) [];
        }

        return $property_pricing;

    }//end savePropertyPricing()


    /**
     * Get Property Initial Pricing.
     *
     * @param integer $property_id Property Id.
     *
     * @return array
     */
    public function getPropertyPricing(int $property_id)
    {
        $property_pricing = self::where('pid', $property_id)->first();

        return $property_pricing;

    }//end getPropertyPricing()


    /**
     * Clone Property Price.
     *
     * @param Property $property Property Model.
     * @param integer  $pid      Property Id.
     *
     * @return boolean
     */
    public static function clonePropertyPrice(Property $property, int $pid)
    {
        $property_price          = $property->property_price()->first();
        $clonepropertyprice      = $property_price->replicate();
        $clonepropertyprice->pid = $pid;
        if ($clonepropertyprice->save() === true) {
            return true;
        }

        return false;

    }//end clonePropertyPrice()


}//end class
