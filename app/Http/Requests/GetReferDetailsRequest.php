<?php
/**
 * Get Refer details  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetReferDetailsRequest
 */
class GetReferDetailsRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'referral_code' => 'required|min:'.HASH_LENGTH_REFFERAL.'|alpha_num',
        ];

    }//end rules()


    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return ['referral_code.min' => 'Invalid referral code .'];

    }//end messages()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanitization Parameters and Its Default Value.
        return ['referral_code' => 'trim'];

    }//end filters()


}//end class
