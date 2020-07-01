<?php
/**
 * Update Booking request Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutBookingRequest
 */
class PutBookingRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method' => 'required|string|in:'.implode(',', ALL_PAYMENT_METHODS),
            'payable_amount' => 'required|integer',
            'apply_wallet'   => 'integer|in:1,0',
            'coupon_code'    => 'string|max:255',
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
            'payment_method' => 'trim',
            'apply_wallet'   => 'integer',
            // Typecast payable_amount to integer.
            'payable_amount' => 'integer',
            'coupon_code'    => 'trim',
        ];

    }//end filters()


}//end class
