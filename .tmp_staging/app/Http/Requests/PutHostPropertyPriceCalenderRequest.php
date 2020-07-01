<?php
/**
 * Host Property Price Calender Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutHostPropertyPriceCalenderRequest
 */
class PutHostPropertyPriceCalenderRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'property_hash_id' => 'required|string',
            'start_date'       => 'required|date|after_or_equal:today',
            'end_date'         => 'required|date|after_or_equal:today|after_or_equal:start_date',
            'is_available'     => 'integer|in:0,1',
            'per_night_price'  => 'integer|min:1',
            'extra_guest_cost' => 'integer|min:1',
            'available_units'  => 'integer|min:0',
            'instant_book'     => 'integer|in:0,1',
            'x_plus_5'         => 'integer|in:0,1',
            'gh_commission'    => 'integer|min:0',
            'discount_type'    => 'required_with:discount|integer|min:0',
            'discount_days'    => 'required_if:discount_type,2|string',
            'discount'         => 'required_with:discount_type|integer|min:0',
            'admin_id'         => 'integer|min:0',
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
            'property_hash_id' => 'trim',
            'start_date'       => 'trim|date',
            'end_date'         => 'trim|date',
            'is_available'     => 'integer',
            'per_night_price'  => 'integer',
            'extra_guest_cost' => 'integer',
            'available_units'  => 'integer',
            'instant_book'     => 'integer',
            'x_plus_5'         => 'integer',
            'gh_commission'    => 'integer',
            'discount_type'    => 'integer',
            'discount_days'    => 'trim',
            'discount'         => 'integer',
            'admin_id'         => 'default:0|integer',
        ];

    }//end filters()


}//end class
