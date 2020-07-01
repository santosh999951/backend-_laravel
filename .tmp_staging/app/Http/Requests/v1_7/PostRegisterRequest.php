<?php
/**
 * User Register Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests\v1_7;

use Illuminate\Contracts\Validation\Factory;
use App\Http\Requests\BaseFormRequest;

/**
 * Class PostRegisterRequest
 */
class PostRegisterRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $login_sources = [
            GOOGLE_SOURCE_ID,
            FACEBOOK_SOURCE_ID,
            EMAIL_SOURCE_ID,
            PHONE_SOURCE_ID,
            APPLE_SOURCE_ID,
        ];
        $source        = (isset($this->input()['source']) === true) ? $this->input()['source'] : '';

        return [
            'source'       => 'required|numeric|in:'.implode(',', $login_sources),
            'source_value' => 'required|sourcevalue:'.$source,
            'email'        => 'sometimes:required|email|unique:users,email',
            'password'     => 'required_if:source,'.EMAIL_SOURCE_ID.'|min:6|max:255',
            'first_name'   => 'required_if:source,'.EMAIL_SOURCE_ID.','.PHONE_SOURCE_ID.','.APPLE_SOURCE_ID.'|string|max:50',
            'last_name'    => 'sometimes:required|string|max:50',
            'dial_code'    => 'required_if:source,'.PHONE_SOURCE_ID.'|numeric|digits_between:1,4',
            'otp_code'     => 'required_if:source,'.PHONE_SOURCE_ID.'|numeric|digits:4',
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
            'source_value.sourcevalue' => 'Please enter valid source type',
            'dial_code.required_if'    => 'Dial code is required',
            'otp_code.required_if'     => 'Otp code is required',

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
            'source'       => 'integer',
            'source_value' => 'trim',
            'email'        => 'trim|lowercase|default:empty',
            'password'     => 'trim|default:empty|base64_decode',
            'first_name'   => 'escape|trim|lowercase|default:empty',
            'last_name'    => 'escape|trim|lowercase|default:empty',
            'dial_code'    => 'trim|default:empty',
            'otp_code'     => 'trim|default:empty',
        ];

    }//end filters()


}//end class
