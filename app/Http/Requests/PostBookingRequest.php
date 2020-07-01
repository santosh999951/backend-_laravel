<?php
/**
 * Create Booking request Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostBookingRequest
 */
class PostBookingRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method'   => 'required|string|in:'.implode(',', ALL_PAYMENT_METHODS),
            'checkin'          => 'required|date|after_or_equal:today',
            'checkout'         => 'required|date|after:start_date',
            'guests'           => 'required|integer|min:1|max:100',
            'units'            => 'required|integer|min:1|max:100',
            'property_hash_id' => 'required|alpha_num|min:'.(HASH_LENGTH_FOR_PROPERTY + 1).'|max:10',
            'payable_amount'   => 'required|numeric',
            'apply_wallet'     => 'integer|in:1,0',
            'coupon_code'      => 'string|max:255',
            'force_create'     => 'integer|in:0,1',
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
            'payment_method'   => 'trim',
            'checkin'          => 'trim|date',
            'checkout'         => 'trim|date',
            'guests'           => 'integer',
            'units'            => 'integer',
            'property_hash_id' => 'trim',
            'apply_wallet'     => 'integer|default:0',
            'coupon_code'      => 'trim|default:empty',
            'force_create'     => 'default:0|integer',
        ];

    }//end filters()


}//end class
