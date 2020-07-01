<?php
/**
 * Post Offline Discovery Create Property Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostOfflineDiscoveryCreateLeadRequest
 */
class PostOfflineDiscoveryCreateLeadRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Mandatory Fields.
            'property_name'       => 'required|string',
            'email'               => 'required|string|email',
            'contact'             => 'required|string|digits_between:8,12',
            'contact_name'        => 'required|string',
            'country'             => 'required|string',
            'state'               => 'required|string',
            'latitude'            => 'required|string',
            'longitude'           => 'required|string',
            'tariff'              => 'required',
            'cancellation_policy' => 'required',
            'total_listings'      => 'required|integer',
            'amenities'           => 'required',
            'address'             => 'required|string',
            'checkin'             => 'string|required|date_format:H:i:s',
            'checkout'            => 'string|required|date_format:H:i:s',
            'property_type'       => 'integer|required',
            'room_type'           => 'integer|required',
            'extra_guest_price'   => 'integer|required',
            'extra_guests'        => 'integer|required',
            'bedrooms'            => 'integer|required',
            'beds'                => 'integer|required',
            'units'               => 'integer|required',
            'accomodation'        => 'integer|required',

            // Non Mandatory Fields.
            'leads_status'        => 'integer',
            'bank_details'        => 'string',
            'payment_terms'       => 'string',
            'property_notes'      => 'string',
            'website'             => 'string',
            'price'               => 'integer',
            'gst_no'              => 'string',
            'payee_name'          => 'string',
            'branch_name'         => 'string',
            'acc_no'              => 'string',
            'ifsc'                => 'string',
            'noc'                 => 'integer',
        ];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        return [
            // Mandatory Fields.
            'property_name'       => 'escape',
            'email'               => 'escape',
            'contact'             => 'escape',
            'contact_name'        => 'escape',
            'country'             => 'escape',
            'state'               => 'escape',
            'latitude'            => 'trim',
            'longitude'           => 'trim',
            'tariff'              => 'trim',
            'cancellation_policy' => 'trim',
            'total_listings'      => 'trim',
            'amenities'           => 'escape',
            'leads_status'        => 'trim|integer',
            'address'             => 'escape',
            'bank_details'        => 'trim',
            'payment_terms'       => 'trim',
            'property_notes'      => 'escape',
            'website'             => 'trim',
            'property_type'       => 'trim|integer',
            'room_type'           => 'trim|integer',
            'units'               => 'trim|integer',
            'accomodation'        => 'trim|integer',
            'extra_guests'        => 'trim|integer',
            'bedrooms'            => 'trim|integer',
            'beds'                => 'trim|integer',
            'checkin'             => 'trim',
            'checkout'            => 'trim',
            'price'               => 'trim|integer',
            'extra_guest_price'   => 'trim|integer',
            'gst_no'              => 'trim',
            'payee_name'          => 'trim',
            'branch_name'         => 'trim',
            'acc_no'              => 'trim',
            'ifsc'                => 'trim',
            'noc'                 => 'trim|integer',
        ];

    }//end filters()


}//end class
