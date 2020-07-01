<?php
/**
 * GetHostPropertiesRequest Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetHostPropertiesRequest
 */
class GetHostPropertiesRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'offset'          => 'integer|min:0',
            'total'           => 'integer|min:1|max:100',
            'property_id'     => 'string',
            'property_type'   => 'string',
            'property_status' => 'string',
            'city'            => 'string',
        ];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanetization Parameters and Its Default Value.
        return [
            'offset'          => 'default:0|integer',
            'total'           => 'default:100|integer',
            'property_id'     => 'trim',
            'property_type'   => 'trim',
            'property_status' => 'trim',
            'city'            => 'trim',
        ];

    }//end filters()


}//end class
