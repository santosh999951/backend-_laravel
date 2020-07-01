<?php
/**
 * Put User Password  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests\v1_7;

use Illuminate\Contracts\Validation\Factory;
use App\Providers\AppServiceProvider;
use App\Http\Requests\BaseFormRequest;

/**
 * Class PutUserResetPasswordRequest
 */
class PutUserResetPasswordRequest extends BaseFormRequest
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
            'password'       => 'required|min:6',
            'otp_code'       => 'required|digits:4',
            'dial_code'      => 'digits_between:1,4',
        ];

    }//end rules()


     /**
      * Custom message for validation
      *
      * @return array
      */
    public function messages()
    {
        return ['email_or_phone.mailorphone' => 'Please enter valid email or phone number. '];

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
            'password'       => 'trim|base64_decode',
            'otp_code'       => 'integer',
            'dial_code'      => 'trim|default:91|integer',
        ];

    }//end filters()


}//end class
