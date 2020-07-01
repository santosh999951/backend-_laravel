<?php
/**
 * Request  prepayment detail Request Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetRequestPrepaymentDetailRequest
 */
class GetRequestPrepaymentDetailRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method' => 'string|in:'.implode(',', ALL_PAYMENT_METHODS),
            'apply_wallet'   => 'in:1,0',
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
            'apply_wallet'   => 'trim|integer',
            'coupon_code'    => 'trim',
        ];

    }//end filters()


}//end class
