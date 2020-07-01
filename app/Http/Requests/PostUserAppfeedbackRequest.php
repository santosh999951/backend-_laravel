<?php
/**
 * App Feedback  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostUserAppfeedbackRequest
 */
class PostUserAppfeedbackRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'rating'  => 'required|in:1,2,3,4,5',
            'message' => 'string|max:200',
        ];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Set empty string when parameter not provided.
        return [
            'rating'  => 'integer',
            'message' => 'escape|trim',
        ];

    }//end filters()


}//end class
