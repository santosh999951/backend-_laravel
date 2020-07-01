<?php
/**
 * Put  User verify otp Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests\v1_7;

use App\Providers\AppServiceProvider;
use App\Http\Requests\BaseFormRequest;

/**
 * Class PutVerifyOtpRequest
 */
class PutVerifyForgotOtpRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email_or_phone' => 'required|mailorphone',
            'dial_code'      => 'digits_between:1,4',
            'otp_code'       => 'required|integer',
        ];

    }//end rules()


     /**
      * Custom message for validation
      *
      * @return array
      */
    public function messages()
    {
        return [
            'email_or_phone' => 'Phone number ir email of user.',
            'dial_code'      => 'Dial code of user.',
            'otp_code'       => 'Please enter otp sent to your registered contact number.',
        ];

    }//end messages()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanetization Parameters and Its Default Value.
        return [
            'email_or_phone' => 'trim',
            'dial_code'      => 'integer',
            'otp_code'       => 'integer',
        ];

    }//end filters()


}//end class
