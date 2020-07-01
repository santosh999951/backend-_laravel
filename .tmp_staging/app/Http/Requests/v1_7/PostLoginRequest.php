<?php
/**
 * Post Login  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests\v1_7;

use Illuminate\Contracts\Validation\Factory;
use App\Http\Requests\BaseFormRequest;

/**
 * Class PostLoginRequest
 */
class PostLoginRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'grant_type' => 'required|in:password,otp',
            'email'      => 'required_without:contact,dial_code|email|exists:users,email',
            'contact'    => 'required_with:dial_code|digits_between:8,12|exists:users,contact',
            'dial_code'  => 'required_with:contact|digits_between:1,4',
            'otp_code'   => 'required_if:grant_type,otp|digits:4',
            'password'   => 'required_if:grant_type,password|min:6|max:255',

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
            'email.required_without'   => 'Email is required.',
            'contact.required_without' => 'Contact number required.',
            'dial_code.required_with'  => 'Dial code is required',
            'otp_code:required_if'     => 'Otp code is required.',
            'password.required_if'     => 'Password is required.',
        ];

    }//end messages()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanitization Parameters and Its Default Value.
        return [
            'grant_type' => 'trim|lowercase',
            'email'      => 'trim|lowercase',
            'contact'    => 'integer',
            'dial_code'  => 'integer',
            'otp_code'   => 'integer',
            'password'   => 'trim',
        ];

    }//end filters()


}//end class
