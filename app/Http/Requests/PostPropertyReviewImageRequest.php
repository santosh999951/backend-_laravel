<?php
/**
 * Post property review image Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPropertyReviewImageRequest
 */
class PostPropertyReviewImageRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['review_image' => 'required|max:10240|mimes:jpeg,png,jpg'];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanitization Parameters and Its Default Value.
        return ['review_image' => 'trim'];

    }//end filters()


}//end class
