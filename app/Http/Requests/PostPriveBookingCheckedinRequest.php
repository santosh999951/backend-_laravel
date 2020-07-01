<?php
/**
 * Save Prive Booking Checked In status Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPriveBookingCheckedinRequest
 */
class PostPriveBookingCheckedinRequest extends BaseFormRequest
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
            'status'          => 'required|integer|in:'.PRIVE_MANAGER_CHECKEDIN.','.PRIVE_MANAGER_CHECKEDOUT.','.PRIVE_MANAGER_NO_SHOW,
            'reason_id'       => 'required_if:status,'.PRIVE_MANAGER_NO_SHOW.'|integer|in:1,2,3',
            'comment'         => 'required_if:reason_id,3',
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
            'comment'         => 'trim',
        ];

    }//end filters()


}//end class
