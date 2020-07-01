<?php
/**
 * User Password Reset Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use App\Providers\AppServiceProvider;

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
            'reset_password_via' => 'required|mailorphone',
            'dial_code'          => 'digits_between:1,4',
        ];

    }//end rules()


     /**
      * Custom message for validation
      *
      * @return array
      */
    public function messages()
    {
        return ['reset_password_via.mailorphone' => 'Please enter valid reset_password_via '];

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
            'reset_password_via' => 'trim',
            'dial_code'          => 'integer',
        ];

    }//end filters()


}//end class
