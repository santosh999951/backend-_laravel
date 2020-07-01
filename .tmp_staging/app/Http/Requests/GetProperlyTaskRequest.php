<?php
/**
 * GetProperlyTaskRequest Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetProperlyTaskRequest
 */
class GetProperlyTaskRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type'              => 'string',
            'assigned_to'       => 'string',
            'status'            => 'string',
            'property_hash_ids' => 'string',
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
            'type'              => 'trim',
            'assigned_to'       => 'trim',
            'status'            => 'trim',
            'property_hash_ids' => 'trim',

        ];

    }//end filters()


}//end class
