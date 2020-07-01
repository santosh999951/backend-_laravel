<?php
/**
 * Post property Clone  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPropertyCloneRequest
 */
class PostPropertyCloneRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'property_hash_id' => 'required|alpha_num|min:'.(HASH_LENGTH_FOR_PROPERTY + 1).'|max:10',
            'converted_by'     => 'required|integer',
            'property_title'   => 'required|string',
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
            'property_hash_id' => 'trim',
            'converted_by'     => 'integer',
            'property_title'   => 'trim',
        ];

    }//end filters()


}//end class
