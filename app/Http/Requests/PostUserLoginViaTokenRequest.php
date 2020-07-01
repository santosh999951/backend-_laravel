<?php
/**
 * Login via token  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostUserLoginViaTokenRequest
 */
class PostUserLoginViaTokenRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'onetime_token' => 'required_without:auth_key|string',
            'auth_key'      => 'required_without:onetime_token|string',
        ];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Set empty string when parameter not provided.
        return [
            'onetime_token' => 'trim',
            'auth_key'      => 'trim',
        ];

    }//end filters()


}//end class
