<?php
/**
 * Save Host Reply On Traveller Review Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutHostReviewReplyRequest
 */
class PutHostReviewReplyRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reply'           => 'required|string',
            'request_hash_id' => 'required|string',
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
            'reply'           => 'trim',
            'request_hash_id' => 'trim',
        ];

    }//end filters()


}//end class
