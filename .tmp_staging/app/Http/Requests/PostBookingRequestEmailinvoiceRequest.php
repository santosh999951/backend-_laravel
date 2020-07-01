<?php
/**
 * Booking Request Email Invoice Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostBookingRequestEmailinvoiceRequest
 */
class PostBookingRequestEmailinvoiceRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['request_hash_id' => 'required|alpha_num|min:5'];

    }//end rules()


    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return ['request_hash_id.min' => 'Request hash id is invalid.'];

    }//end messages()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanitization Parameters and Its Default Value.
        return ['request_hash_id' => 'trim'];

    }//end filters()


}//end class
