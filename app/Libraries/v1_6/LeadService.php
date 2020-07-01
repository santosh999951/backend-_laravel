<?php
/**
 * Property service containing all property releated functions
 */

namespace App\Libraries\v1_6;
use \Carbon\Carbon;
use App\Libraries\v1_6\AwsService;

use App\Models\{HostConversionLead};
use App\Libraries\Helper;

/**
 * Class PropertyService
 */
class LeadService
{

    /**
     * Property Lead Model object for Database Interaction.
     *
     * @var $lead
     */
    protected $lead;


    /**
     * Property Lead object for create lead.
     *
     * @param HostConversionLead $lead Object.
     */
    public function __construct(HostConversionLead $lead=null)
    {
        $this->lead = $lead;

    }//end __construct()


    /**
     * Create New Property.
     *
     * @param integer $user_id User id.
     * @param array   $lead    Lead data.
     *
     * @return array.
     */
    public function createLead(int $user_id, array $lead)
    {
        // Save Lead info.
        $lead_response = $this->saveLeadData($user_id, $lead);

        if (empty($lead_response) === true) {
            return [];
        }

        return $lead_response;

    }//end createLead()


    /**
     * Save Lead Data.
     *
     * @param integer $user_id   User id.
     * @param array   $lead_data Lead data.
     *
     * @return object.
     */
    public function saveLeadData(int $user_id, array $lead_data)
    {
        $lead = $this->lead->savePropertyLead(
            [
                'user_id'             => $user_id,
                'property_name'       => $lead_data['property_name'],
                'email'               => $lead_data['email'],
                'contact'             => $lead_data['contact'],
                'contact_name'        => $lead_data['contact_name'],
                'country'             => $lead_data['country'],
                'state'               => $lead_data['state'],
                'city'                => Helper::emptyOrDefault($lead_data, 'city'),
                'latitude'            => $lead_data['latitude'],
                'longitude'           => $lead_data['longitude'],
                'address'             => $lead_data['address'],
                'room_category_info'  => Helper::emptyOrDefault($lead_data, 'room_category_info', ''),
                'tariff'              => $lead_data['tariff'],
                'cancellation_policy' => $lead_data['cancellation_policy'],
                'amenities'           => $lead_data['amenities'],
                'total_listings'      => $lead_data['total_listings'],
                'leads_status'        => Helper::emptyOrDefault($lead_data, 'leads_status', 0),
                'bank_details'        => Helper::emptyOrDefault($lead_data, 'bank_details', ''),
                'payment_terms'       => Helper::emptyOrDefault($lead_data, 'payment_terms', ''),
                'property_notes'      => Helper::emptyOrDefault($lead_data, 'property_notes', ''),
                'website'             => Helper::emptyOrDefault($lead_data, 'website', ''),
                'property_type'       => Helper::emptyOrDefault($lead_data, 'property_type', ''),
                'room_type'           => Helper::emptyOrDefault($lead_data, 'room_type', 0),
                'units'               => Helper::emptyOrDefault($lead_data, 'units', 0),
                'accomodation'        => Helper::emptyOrDefault($lead_data, 'accomodation', 0),
                'extra_guests'        => Helper::emptyOrDefault($lead_data, 'extra_guests', 0),
                'bedrooms'            => Helper::emptyOrDefault($lead_data, 'bedrooms', 0),
                'beds'                => Helper::emptyOrDefault($lead_data, 'beds', 0),
                'checkin'             => Helper::emptyOrDefault($lead_data, 'checkin', '12:00:00'),
                'checkout'            => Helper::emptyOrDefault($lead_data, 'checkout', '12:00:00'),
                'price'               => Helper::emptyOrDefault($lead_data, 'price', 0),
                'extra_guest_price'   => Helper::emptyOrDefault($lead_data, 'extra_guest_price', 0),
                'gst_no'              => Helper::emptyOrDefault($lead_data, 'gst_no', ''),
                'payee_name'          => Helper::emptyOrDefault($lead_data, 'payee_name', ''),
                'branch_name'         => Helper::emptyOrDefault($lead_data, 'branch_name', ''),
                'acc_no'              => Helper::emptyOrDefault($lead_data, 'acc_no', ''),
                'ifsc'                => Helper::emptyOrDefault($lead_data, 'ifsc', ''),
                'noc'                 => Helper::emptyOrDefault($lead_data, 'noc', 0),
            ]
        );

        return $lead;

    }//end saveLeadData()


}//end class
