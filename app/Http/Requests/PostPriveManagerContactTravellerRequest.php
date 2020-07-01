<?php
/**
 * Post Prive Manager Call Traveller Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPriveManagerContactTravellerRequest
 */
class PostPriveManagerContactTravellerRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'request_hash_id'              => 'required|alpha_num|min:'.(HASH_LENGTH_FOR_BOOKING_REQUEST_ID),
            'is_manager_primary_contact'   => 'integer|in:0,1',
            'is_traveller_primary_contact' => 'integer|in:0,1',
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
            'request_hash_id'              => 'trim',
            'is_manager_primary_contact'   => 'trim|default:1|integer',
            'is_traveller_primary_contact' => 'trim|default:1|integer',
        ];

    }//end filters()


}//end class
