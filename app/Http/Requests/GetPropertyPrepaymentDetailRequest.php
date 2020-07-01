<?php
/**
 * Property  prepayment detail Request Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetPropertyPrepaymentDetailRequest
 */
class GetPropertyPrepaymentDetailRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'checkin'        => 'date|after_or_equal:today',
            'checkout'       => 'date|after:checkin',
            'guests'         => 'integer|min:0|max:100',
            'units'          => 'integer|min:0|max:100',
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
            'guests'         => 'integer|default:0',
            'units'          => 'integer|default:0',
            'checkin'        => 'trim|default:empty',
            'checkout'       => 'trim|default:empty',
            'payment_method' => 'trim|default:empty',
            'apply_wallet'   => 'integer|default:0',
            'coupon_code'    => 'trim|default:empty',

        ];

    }//end filters()


}//end class
