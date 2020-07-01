<?php
/**
 * Update Host Property Status Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutHostPropertyStatusRequest
 */
class PutHostPropertyStatusRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'property_status'  => 'required|integer|in:0,1',
            'property_hash_id' => 'required|string',
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
            'property_status'  => 'integer',
            'property_hash_id' => 'trim',
        ];

    }//end filters()


}//end class
