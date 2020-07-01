<?php
/**
 * Trip Review Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetTripReviewRequest
 */
class GetTripReviewRequest extends BaseFormRequest
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
            'request_hash_id' => 'min:'.(HASH_LENGTH_FOR_BOOKING_REQUEST_ID).'|alpha_num',
        ];

    }//end rules()


     /**
      * Custom message for validation
      *
      * @return array
      */
    public function messages()
    {
        return ['request_hash_id.min' => 'Invalid request hash id .'];

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
            'offset'          => 'integer|default:0',
            'total'           => 'integer|default:1',
            'request_hash_id' => 'trim',
        ];

    }//end filters()


}//end class
