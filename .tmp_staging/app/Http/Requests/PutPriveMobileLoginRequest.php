<?php
/**
 * Put prive Mobile Login  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

/**
 * Class PutPriveMobileLoginRequest
 */
class PutPriveMobileLoginRequest extends BaseFormRequest
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
            'otp_code'       => 'required|digits:4',
             // Source 1 for Prive Owner and 2 for Prive Manager.
            'source'         => 'required|in:1,2',
        ];

    }//end rules()


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
            'otp_code'       => 'integer',
            'source'         => 'integer',
        ];

    }//end filters()


}//end class
