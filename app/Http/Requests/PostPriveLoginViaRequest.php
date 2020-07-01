<?php
/**
 * Post prive loginvia Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use App\Providers\AppServiceProvider;

/**
 * Class PostPriveLoginViaRequest
 */
class PostPriveLoginViaRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $loginvia = (isset($this->input()['login_via']) === true) ? $this->input()['login_via'] : '';
        return [
            'login_via' => 'required|mailorphone',
            // Source 1 for Prive Owner and 2 for Prive Manager.
            'source'    => 'required|in:1,2',
            'dial_code' => 'required_if_type_numeric:loginvia,'.$loginvia.'|numeric|digits_between:1,4',
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
            'login_via.mailorphone'              => 'Please enter valid Email or Contact number.',
            'dial_code.required_if_type_numeric' => 'dial_code is required if loginvia is Contact number',
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
            'login_via' => 'trim',
            'source'    => 'integer',
            'dial_code' => 'trim',
        ];

    }//end filters()


}//end class
