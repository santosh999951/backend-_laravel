<?php
/**
 * Post Prive Manager Send Payment Link Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPriveManagerSendPaymentLinkRequest
 */
class PostPriveManagerSendPaymentLinkRequest extends BaseFormRequest
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
            'contact_number'  => 'digits_between:8,12',
            'dial_code'       => 'digits_between:1,4',
            'email'           => 'email',

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
            'request_hash_id' => 'trim',
            'contact_number'  => 'integer',
            'dial_code'       => 'integer',
            'email'           => 'trim',
        ];

    }//end filters()


}//end class
