<?php
/**
 * Login old app user  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostUserLoginoldappuserRequest
 */
class PostUserLoginoldappuserRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'access_token'     => 'required|string',
            'refresh_token'    => 'required|string',
            'device_unique_id' => 'required',
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
            'access_token'     => 'trim',
            'refresh_token'    => 'trim',
            'device_unique_id' => 'trim',
        ];

    }//end filters()


}//end class
