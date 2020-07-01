<?php
/**
 *  Booking request resend Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostBookingRequestResendRequest
 */
class PostBookingRequestResendRequest extends BaseFormRequest
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
        return ['request_hash_id' => 'trim'];

    }//end filters()


}//end class
