<?php
/**
 * User Profile Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetUserProfileRequest
 */
class GetUserProfileRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'min:'.(HASH_LENGTH_FOR_USER + 1).'|alpha_num',
        ];

    }//end rules()


    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return ['user_id.min' => 'Invalid user id .'];

    }//end messages()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanitization Parameters and Its Default Value.
        return ['user_id' => 'trim'];

    }//end filters()


}//end class
