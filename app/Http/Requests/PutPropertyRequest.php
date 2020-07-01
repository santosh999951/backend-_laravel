<?php
/**
 * Update Property Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutPropertyRequest
 */
class PutPropertyRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'property_hash_id'       => 'required|string',

            // Property Required Info .
            'property_type'          => 'integer|min:1',
            'room_type'              => 'integer|min:1',
            'units'                  => 'integer|min:1',
            'accomodation'           => 'integer|min:1',
            'per_unit_extra_guests'  => 'integer|min:0',
            'bedrooms'               => 'integer|min:1',
            'beds'                   => 'integer|min:1',
            'bathrooms'              => 'integer|min:1',
            'title'                  => 'string|min:3|max:100',
            'currency'               => 'string|min:3|max:3',
            'per_night_price'        => 'numeric|min:1',
            'address'                => 'string|min:3',
            'area'                   => 'string',
            'city'                   => 'string',
            'state'                  => 'string',
            'country_code'           => 'string|min:2|max:2',
            'zipcode'                => 'alpha_num',
            'cancelation_policy'     => 'integer|min:1',

            // Property Non Required Info.
            'noc_status'             => 'integer|in:0,1',
            'gh_commission'          => 'numeric|min:1',
            'description'            => 'string',
            'policy_services'        => 'string',
            'house_rule'             => 'string',
            'your_space'             => 'string',
            'guest_brief'            => 'string',
            'interaction_with_guest' => 'string',
            'local_experience'       => 'string',
            'from_airport'           => 'string',
            'train_station'          => 'string',
            'bus_station'            => 'string',
            'extra_detail'           => 'string',
            'properly_title'         => 'string',
            'extra_guest_price'      => 'required_unless:per_unit_extra_guests,0|numeric|min:1',
            'per_week_price'         => 'numeric',
            'per_month_price'        => 'numeric',
            'cleaning_fee'           => 'numeric',
            'cleaning_mode'          => 'integer|in:-1,1',
            'min_nights'             => 'integer',
            'max_nights'             => 'integer',
            'check_in_time'          => 'string|date_format:H:i:s',
            'check_out_time'         => 'string|date_format:H:i:s',
            'video_link'             => 'url',
            'property_tags'          => 'string',
            'usp'                    => 'string',
            'converted_by'           => 'integer',
            'latitude'               => 'string',
            'longitude'              => 'string',
            'search_keyword'         => 'string|regex:/^[\w]*[a-zA-Z]+[\w]*-* *[\w]*/',
            'gstin'                  => 'string|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'amenities'              => 'string',
            'image_caption'          => 'json',
            'image_data'             => 'json',
            'video_data'             => 'json',

        ];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanetization Parameters and Its Default Value.
        return [
            // Property Required Info Sanitize.
            'property_type'          => 'trim|integer',
            'room_type'              => 'trim|integer',
            'units'                  => 'trim|integer',
            'accomodation'           => 'trim|integer',
            'per_unit_extra_guests'  => 'trim|integer',
            'bedrooms'               => 'trim|integer',
            'beds'                   => 'trim|integer',
            'bathrooms'              => 'trim|integer',
            'title'                  => 'escape|trim',
            'currency'               => 'escape|trim',
            'per_night_price'        => 'trim|integer',
            'gh_commission'          => 'trim|integer',
            'noc_status'             => 'trim|integer',
            'address'                => 'escape|trim',
            'area'                   => 'escape|trim',
            'city'                   => 'escape|trim',
            'state'                  => 'escape|trim',
            'country_code'           => 'escape|trim',
            'zipcode'                => 'escape|trim',
            'cancelation_policy'     => 'trim|integer',

            // Property Non Required Info Sanitize.
            'description'            => 'escape|trim',
            'policy_services'        => 'escape|trim',
            'house_rule'             => 'escape|trim',
            'your_space'             => 'escape|trim',
            'guest_brief'            => 'escape|trim',
            'interaction_with_guest' => 'escape|trim',
            'local_experience'       => 'escape|trim',
            'from_airport'           => 'escape|trim',
            'train_station'          => 'escape|trim',
            'bus_station'            => 'escape|trim',
            'extra_detail'           => 'escape|trim',
            'extra_guest_price'      => 'trim|integer',
            'per_week_price'         => 'trim|integer',
            'per_month_price'        => 'trim|integer',
            'cleaning_fee'           => 'trim|integer',
            'cleaning_mode'          => 'trim|integer',
            'min_nights'             => 'trim|integer',
            'max_nights'             => 'trim|integer',
            'check_in_time'          => 'trim',
            'check_out_time'         => 'trim',
            'video_link'             => 'trim',
            'property_tags'          => 'trim',
            'usp'                    => 'trim',
            'converted_by'           => 'trim|integer',
            'latitude'               => 'trim',
            'longitude'              => 'trim',
            'search_keyword'         => 'trim',
            'gstin'                  => 'trim',
            'amenities'              => 'trim',
            'image_caption'          => 'trim',
            'image_data'             => 'trim',
            'video_data'             => 'trim',

        ];

    }//end filters()


}//end class
