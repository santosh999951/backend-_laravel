<?php
/**
 * Home Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetHomeRequest
 */
class GetHomeRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'collections_offset'         => 'integer|min:0',
            'collections_total'          => 'integer|min:1|max:100',
            'collections_property_total' => 'integer|min:1|max:100',
            'offer'                      => 'string|max:50',
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
            'collections_offset'         => 'integer|default:0',
            'collections_total'          => 'integer|default:'.DEFAULT_NUMBER_OF_COLLECTIONS,
            'collections_property_total' => 'integer|default:'.DEFAULT_NUMBER_OF_COLLECTIONS_PROPERTIES,
            'offer'                      => 'trim|default:default',
        ];

    }//end filters()


}//end class
