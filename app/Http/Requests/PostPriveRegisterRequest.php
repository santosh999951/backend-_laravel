<?php
/**
 * Prive Register Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPriveRegisterRequest
 */
class PostPriveRegisterRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $login_sources = [
            WEBSITE_SOURCE_ID,
            GOOGLE_SOURCE_ID,
        ];

        return [
            'source'       => 'required|integer|in:'.implode(',', $login_sources),
            'email'        => 'required_if:source,'.WEBSITE_SOURCE_ID.'|email',
            'password'     => 'required_if:source,'.WEBSITE_SOURCE_ID.'|min:6|max:255',
            'first_name'   => 'required_if:source,'.WEBSITE_SOURCE_ID.'|string|max:50',
            'last_name'    => 'string|max:50',
            'access_token' => 'required_if:source,'.GOOGLE_SOURCE_ID,
            'contact'      => 'integer|digits_between:8,12',
            'dial_code'    => 'required_with:contact|integer|digits_between:1,4',
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
            'email.required_if'        => 'Email is required.',
            'password.required_if'     => 'Password is required.',
            'first_name.required_if'   => 'First name is required.',
            'access_token.required_if' => 'Access token is required for Google Login.',
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
            'source'       => 'trim',
            'email'        => 'trim|lowercase',
            'password'     => 'trim|base64_decode',
            'first_name'   => 'escape|trim|lowercase',
            'last_name'    => 'escape|trim|default:empty|lowercase',
            'access_token' => 'trim',
            'contact'      => 'integer|default:empty',
            'dial_code'    => 'integer|default:empty',
        ];

    }//end filters()


}//end class
