<?php
/**
 * Update Host Request Status Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use \Carbon\Carbon;

/**
 * Class PutHostRequestStatusRequest
 */
class PutHostRequestStatusRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'request_hash_id' => 'required|string',
            'status'          => 'required|integer|in:0,1',
            'reason_id'       => 'required_if:status,0|integer',
            'reason_detail'   => 'required_if:status,0|string',
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
            'request_hash_id' => 'trim',
            'status'          => 'integer',
            'reason_id'       => 'integer',
            'reason_detail'   => 'trim',
        ];

    }//end filters()


}//end class
