<?php
/**
 * Cancel Booking request Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostBookingRequestCancelRequest
 */
class PostBookingRequestCancelRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'request_hash_id' => 'required|alpha_num|min:'.(HASH_LENGTH_FOR_BOOKING_REQUEST_ID),
            'request_status'  => 'required|integer',
            'reason_id'       => 'required|integer',
            'reason_detail'   => 'string',
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
            'request_status'  => 'integer',
            'reason_id'       => 'integer',
            'reason_detail'   => 'trim',
        ];

    }//end filters()


}//end class
