<?php
/**
 * Property Address Model containing all functions related to Properties Address table
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Property Address
 */
class PropertyAddress extends Model
{

    /**
     * Table Name
     *
     * @var $table
     */
    protected $table = 'properties_address';


    /**
     * Save Property Address
     *
     * @param integer $property_id Property id.
     * @param string  $address     Property Address.
     *
     * @return object
     */
    public static function savePropertyAddress(int $property_id, string $address)
    {
        $property_address = new self;

        $property_address->pid     = $property_id;
        $property_address->address = $address;

        if ($property_address->save() === false) {
            return (object) [];
        }

        return $property_address;

    }//end savePropertyAddress()


    /**
     * Update Property Address
     *
     * @param integer $property_id Property id.
     * @param string  $address     Property Address.
     *
     * @return object
     */
    public static function updatePropertyAddress(int $property_id, string $address)
    {
        $property_address = self::where('pid', $property_id)->first();

        $property_address->address = $address;

        if ($property_address->save() === false) {
            return (object) [];
        }

        return $property_address;

    }//end updatePropertyAddress()


}//end class
