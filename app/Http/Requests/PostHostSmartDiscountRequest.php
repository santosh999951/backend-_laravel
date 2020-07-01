<?php
/**
 * Host Smart Discounts Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostHostSmartDiscountRequest
 */
class PostHostSmartDiscountRequest extends BaseFormRequest
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
            'status'           => 'required|integer|in:0,1',
            'discounts'        => 'required|json',
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
            'status'           => 'trim|integer',
            'discounts'        => 'trim',
        ];

    }//end filters()


}//end class
