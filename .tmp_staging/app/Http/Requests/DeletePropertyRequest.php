<?php
/**
 * Host Property Delete Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class DeletePropertyRequest
 */
class DeletePropertyRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'property_hash_id' => 'required|string',
            'admin_id'         => 'integer',
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
            'property_hash_id' => 'trim',
            'admin_id'         => 'integer',
        ];

    }//end filters()


}//end class
