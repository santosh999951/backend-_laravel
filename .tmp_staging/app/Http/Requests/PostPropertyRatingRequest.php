<?php
/**
 * Post property rating  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPropertyRatingRequest
 */
class PostPropertyRatingRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ratings'            => 'required|json',
            'request_hash_id'    => 'required|string',
            'booking_experience' => 'required|in:1,2,3,4,5',
            'property_rating'    => 'required|in:1,2,3,4,5',
            'booking_review'     => 'string|max:1000',
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
            'ratings'            => 'trim',
            'request_hash_id'    => 'trim',
            'booking_experience' => 'trim',
            'property_rating'    => 'trim',
            'booking_review'     => 'trim|default:empty',
        ];

    }//end filters()


}//end class
