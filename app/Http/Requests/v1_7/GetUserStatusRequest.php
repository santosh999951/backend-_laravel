<?php
/**
 * Get User Satus Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests\v1_7;

use App\Providers\AppServiceProvider;
use App\Http\Requests\BaseFormRequest;

/**
 * Class GetUserStatusRequest
 */
class GetUserStatusRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $login_id = (isset($this->input()['login_id']) === true) ? $this->input()['login_id'] : '';
        return [
            'login_id'  => 'required|mailorphone',
            'dial_code' => 'required_if_type_numeric:login_id,'.$login_id.'|numeric|digits_between:1,4',
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
            'login_id.mailorphone'               => 'Please enter valid email or contact number.',
            'dial_code.required_if_type_numeric' => 'dial_code is required if login_id is Contact number',
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
            'login_id'  => 'trim',
            'dial_code' => 'trim|default:91',
        ];

    }//end filters()


}//end class
