<?php
/**
 * PostLeadRequest model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostLeadRequest
 */
class PostLeadRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => 'required|max:50',
            'email'         => 'email',
            'contact'       => 'required|integer|digits_between:8,12',
            'property_type' => 'integer',
            'address'       => 'string|max:255',
            'city'          => 'required|string|max:40',
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
            'name'          => 'trim',
            'email'         => 'trim|default:empty',
            'contact'       => 'integer',
            'property_type' => 'integer|default:empty',
            'address'       => 'trim|default:empty',
            'city'          => 'trim',
        ];

    }//end filters()


}//end class
