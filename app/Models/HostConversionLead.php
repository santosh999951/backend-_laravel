<?php
/**
 * HostConversionLead Model containing all functions related to host_conversion_leads table
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
// phpcs:disable  
/**
 * // phpcs:enable
 * Class HostConversionLead
 */
class HostConversionLead extends Model
{

    /**
     * Table Name
     *
     * @var $table
     */
    protected $table = 'host_conversion_leads';


    /**
     * Confirmed Availablity Data
     *
     * @param array $params Parameters.
     *
     * @return object
     */
    public static function saveLead(array $params)
    {
        $host_conversion_lead                = new self;
        $host_conversion_lead->user_id       = $params['user_id'];
        $host_conversion_lead->contact_name  = $params['name'];
        $host_conversion_lead->email         = $params['email'];
        $host_conversion_lead->contact       = $params['contact'];
        $host_conversion_lead->city          = $params['city'];
        $host_conversion_lead->address       = $params['address'];
        $host_conversion_lead->property_type = $params['property_type'];
        $host_conversion_lead->save();
        return $host_conversion_lead;

    }//end saveLead()


    /**
     * UpdateLeadCount
     *
     * @param integer $lead_id Lead Id.
     *
     * @return void
     */
    public static function updateLeadCount(int $lead_id)
    {
        $lead_count_update = self::find($lead_id);
        if (empty($lead_count_update) === false) {
            $lead_count_update->listing_count++;
            $lead_count_update->save();
        }

    }//end updateLeadCount()


    /**
     * Update Lead Conversion Listing Count
     *
     * @param integer $id Lead id.
     *
     * @return void
     */
    public static function updateListingCount(int $id)
    {
        $lead_count_update = self::find($id);

        $lead_count_update->listing_count = ($lead_count_update->listing_count - 1);
        $lead_count_update->save();

    }//end updateListingCount()


     /**
      * Save property lead detail.
      *
      * @param array $params Property Lead Save Parameters.
      *
      * @return object
      */
    public function savePropertyLead(array $params)
    {
          $lead = new self;

          // Lead Info.
          $lead->user_id             = $params['user_id'];
          $lead->property_name       = $params['property_name'];
          $lead->email               = $params['email'];
          $lead->contact             = $params['contact'];
          $lead->contact_name        = $params['contact_name'];
          $lead->country             = $params['country'];
          $lead->state               = $params['state'];
          $lead->city                = $params['city'];
          $lead->latitude            = $params['latitude'];
          $lead->longitude           = $params['longitude'];
          $lead->address             = $params['address'];
          $lead->room_category_info  = $params['room_category_info'];
          $lead->tariff              = $params['tariff'];
          $lead->cancellation_policy = $params['cancellation_policy'];
          $lead->amenities           = $params['amenities'];
          $lead->total_listings      = $params['total_listings'];
          $lead->leads_status        = $params['leads_status'];
          $lead->bank_details        = $params['bank_details'];
          $lead->payment_terms       = $params['payment_terms'];
          $lead->property_notes      = $params['property_notes'];
          $lead->website             = $params['website'];
          $lead->property_type       = $params['property_type'];
          $lead->room_type           = $params['room_type'];
          $lead->units               = $params['units'];
          $lead->accomodation        = $params['accomodation'];
          $lead->extra_guests        = $params['extra_guests'];
          $lead->bedrooms            = $params['bedrooms'];
          $lead->beds                = $params['beds'];
          $lead->checkin             = $params['checkin'];
          $lead->checkout            = $params['checkout'];
          $lead->price               = $params['price'];
          $lead->extra_guest_price   = $params['extra_guest_price'];
          $lead->gst_no              = $params['gst_no'];
          $lead->payee_name          = $params['payee_name'];
          $lead->branch_name         = $params['branch_name'];
          $lead->acc_no              = $params['acc_no'];
          $lead->ifsc                = $params['ifsc'];
          $lead->noc                 = $params['noc'];

          // Extra field to add.
        if ($params['leads_status'] === 2) {
            $lead->submitted_at = date('y-m-d');
        }

            $lead->source = 'app';

        if ($lead->save() === false) {
            return (object) [];
        }

            return $lead;

    }//end savePropertyLead()


    /**
     * Get Lead exist or not.
     *
     * @param integer $id Lead id.
     *
     * @return array $data  Send Array data.
     */
    public static function getLead(int $id)
    {
        $query = self::where('id', $id);
        $data  = $query->get()->toArray();
        return $data;

    }//end getLead()


    /**
     * Update lead entry with lead image profile.
     *
     * @param integer $id     Lead id.
     * @param array   $params Params.
     *
     * @return boolen
     */
    public static function updateLeadImage(int $id, array $params)
    {
        return self::where('id', $id)->update($params);

    }//end updateLeadImage()


}//end class
