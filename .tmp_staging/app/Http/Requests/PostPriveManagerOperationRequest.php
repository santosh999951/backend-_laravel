<?php
/**
 * Post Prive Manager Operation Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPriveManagerOperationRequest
 */
class PostPriveManagerOperationRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'request_hash_id'   => 'required|alpha_num|min:'.(HASH_LENGTH_FOR_BOOKING_REQUEST_ID),
            'operational_note'  => 'required_without_all:managerial_note,expected_checkin,expected_checkout|string',
            'managerial_note'   => 'required_without_all:operational_note,expected_checkin,expected_checkout|string',
            'expected_checkin'  => 'required_without_all:operational_note,managerial_note,expected_checkout|string|date_format:H:i:s',
            'expected_checkout' => 'required_without_all:operational_note,managerial_note,expected_checkin|string|date_format:H:i:s',
        ];

    }//end rules()


    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'expected_checkin.date_format'  => 'Expected checkin should be in HH:MM:SS Format',
            'expected_checkout.date_format' => 'Expected checkin should be in HH:MM:SS Format',
        ];

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
            'request_hash_id'   => 'trim',
            'operational_note'  => 'escape|trim',
            'managerial_note'   => 'escape|trim',
            'expected_checkin'  => 'trim',
            'expected_checkout' => 'trim',
        ];

    }//end filters()


}//end class
