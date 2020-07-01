<?php
/**
 * User Register Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

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
            WEBSITE_SOURCE_ID,
            GOOGLE_SOURCE_ID,
            FACEBOOK_SOURCE_ID,
            APPLE_SOURCE_ID,
        ];

        return [
            'source'        => 'required|numeric|in:'.implode(',', $login_sources),
            'currency'      => 'string',
            'referral_code' => 'string',
            'device_type'   => 'string',
            'email'         => 'required_if:source,'.WEBSITE_SOURCE_ID.'|email',
            'password'      => 'required_if:source,'.WEBSITE_SOURCE_ID.'|min:6|max:255',
            'first_name'    => 'required_if:source,'.WEBSITE_SOURCE_ID.','.APPLE_SOURCE_ID.'|string|max:50',
            'last_name'     => 'string|max:50',
            'gender'        => 'string|in:Male,Female',
            'access_token'  => 'required_if:source,'.GOOGLE_SOURCE_ID.','.FACEBOOK_SOURCE_ID.','.APPLE_SOURCE_ID,
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
            'access_token.required_if' => 'Access token or Id token is required for Google, Facebook or Apple Login.',
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
            'source'        => 'integer',
            'currency'      => 'trim|uppercase|default:'.DEFAULT_CURRENCY,
            'referral_code' => 'trim|default:empty',
            'device_type'   => 'trim|default:'.DEFAULT_DEVICE_TYPE,
            'email'         => 'trim|lowercase|default:empty',
            'password'      => 'trim|default:empty|base64_decode',
            'first_name'    => 'escape|trim|lowercase|default:empty',
            'last_name'     => 'escape|trim|lowercase|default:empty',
            // In old app value of gender is going null in string so we are
            // using default_in filter i.e if value other then Male and Female are coming
            // then set default value Male.
            'gender'        => 'trim|capitalize|default_in:Male,Female',
            'access_token'  => 'trim||default:empty',
        ];

    }//end filters()


}//end class
