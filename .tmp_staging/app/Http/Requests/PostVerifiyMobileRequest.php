<?php
/**
 * Verification Mobile Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostVerifiyMobileRequest
 */
class PostVerifiyMobileRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'contact_number' => 'required|digits_between:8,12',
            'dial_code'      => 'required|digits_between:1,4',
            'otp_method'     => 'required|in:1,2',
            'referral_code'  => 'min:'.HASH_LENGTH_REFFERAL.'|alpha_num',
        ];

    }//end rules()


     /**
      * Custom message for validation
      *
      * @return array
      */
    public function messages()
    {
        return ['referral_code.min' => 'Invalid referral_code.'];

    }//end messages()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanitization Parameters and Its Default Value.
        return [
            'contact_number' => 'integer',
            'dial_code'      => 'integer',
            'otp_method'     => 'integer',
            'referral_code'  => 'trim',
        ];

    }//end filters()


}//end class
