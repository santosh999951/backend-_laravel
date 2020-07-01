<?php
/**
 * GetUserPropertiesRequest Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetUserPropertiesRequest
 */
class GetUserPropertiesRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Validator treat every parameter as a string. so we need to typecast in integer.
            'offset'  => 'integer|min:0',
            'limit'   => 'integer|min:1',
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
        return [
            // Order of integer will be last for tycasting final value.
            'offset'  => 'integer|default:0',
            'limit'   => 'integer|default:'.DEFAULT_NUMBER_OF_PROPERTY_LISTED_BY_HOST,
            'user_id' => 'trim',
        ];

    }//end filters()


}//end class
