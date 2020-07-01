<?php
/**
 * User Password Reset Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests\v1_7;

use Illuminate\Contracts\Validation\Factory;
use App\Providers\AppServiceProvider;
use App\Http\Requests\BaseFormRequest;
/**
 * Class PostUserPasswordResetRequest
 */
class PostUserPasswordResetRequest extends BaseFormRequest
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
        ];

    }//end rules()


     /**
      * Custom message for validation
      *
      * @return array
      */
    public function messages()
    {
        return ['email_or_phone.mailorphone' => 'Please enter valid email or phone number.'];

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
        ];

    }//end filters()


}//end class
