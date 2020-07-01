<?php
/**
 * Post property review  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPropertyReviewRequest
 */
class PostPropertyReviewRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'review'          => 'required|min:120|max:1000',
            'request_hash_id' => 'required|string',
            'review_images'   => 'json',
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
            'review'          => 'trim',
            'request_hash_id' => 'trim',
            'review_images'   => 'trim|default:empty',
        ];

    }//end filters()


}//end class
