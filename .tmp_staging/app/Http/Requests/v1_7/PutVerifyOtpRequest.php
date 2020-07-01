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
class PutVerifyOtpRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'contact'       => 'required|digits_between:8,12',
            'dial_code'     => 'required|digits_between:1,4',
            'otp_code'      => 'required|integer',
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
            'contact'       => 'Phone number of user.',
            'dial_code'     => 'Dial code of user.',
            'otp_code'      => 'Please enter otp sent to your registered contact number.',
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
            'contact'       => 'integer',
            'dial_code'     => 'integer',
            'otp_code'      => 'integer',
        ];

    }//end filters()


}//end class
