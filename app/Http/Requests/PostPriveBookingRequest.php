<?php
/**
 * Create Prive Booking request Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPriveBookingRequest
 */
class PostPriveBookingRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'checkin'          => 'required|date|after_or_equal:today',
            'checkout'         => 'required|date|after:start_date',
            'guests'           => 'required|integer|min:1|max:100',
            'extra_guest'      => 'integer|max:100',
            'units'            => 'required|integer|min:1|max:100',
            'property_hash_id' => 'required|alpha_num|min:'.(HASH_LENGTH_FOR_PROPERTY + 1).'|max:10',
            'first_name'       => 'required|string|max:50',
            'last_name'        => 'string|max:50',
            'contact'          => 'required|integer|digits_between:8,12',
            'dial_code'        => 'required_with:contact|numeric|digits_between:1,4',
            'email'            => 'required|email',
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
            'checkin'          => 'trim|date',
            'checkout'         => 'trim|date',
            'guests'           => 'integer',
            'extra_guests'     => 'integer|default:0',
            'units'            => 'integer',
            'property_hash_id' => 'trim',
            'first_name'       => 'trim',
            'last_name'        => 'trim|default:empty',
            'contact'          => 'integer',
            'dial_code'        => 'integer',
            'email'            => 'trim',
        ];

    }//end filters()


}//end class
