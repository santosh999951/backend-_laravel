<?php
/**
 * IvrContactMapping Model containing all functions related to Ammyo Connect call table
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class IvrContactMapping
 */
class IvrContactMapping extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'ivrcontact_number_mapping';


     /**
      * Function to save Calling Contact Mappings.
      *
      * @param integer $did_number   Did Number.
      * @param integer $request_id   Booking Request Id.
      * @param string  $contact_from Contact From.
      * @param string  $contact_to   Contact To.
      *
      * @return object.
      */
    public function saveContactMapping(int $did_number, int $request_id, string $contact_from, string $contact_to)
    {
        $existing_data = self::where('contact_from', $contact_from)->where('contact_to', $contact_to)->where('enabled', 1)->first();

        if (empty($existing_data) === false) {
            return $existing_data;
        }

        $mappings               = new self;
        $mappings->did_number   = $did_number;
        $mappings->contact_from = $contact_from;
        $mappings->contact_to   = $contact_to;
        $mappings->booking_request_id = $request_id;
        $mappings->enabled            = 1;

        if ($mappings->save() === false) {
            return (object) [];
        }

        return $mappings;

    }//end saveContactMapping()


}//end class
