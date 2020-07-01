<?php
/**
 * Upload Promotional Images  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPromotionalImageRequest
 */
class PostPromotionalImageRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $allowed_extension = implode(',', WATERMARK_ALLOWDED_IMAGES_EXTENSION);

        return [
            'mobile_image' => 'required_without:web_image|max:'.WATERMARK_ALLOWDED_IMAGES_SIZE.'|mimes:'.$allowed_extension.'',
            'web_image'    => 'required_without:mobile_image|max:'.WATERMARK_ALLOWDED_IMAGES_SIZE.'|mimes:'.$allowed_extension.'',
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
            // Use to return file type in $request->input().
            // Now we can get file type in $request->input().
            'mobile_image' => 'file:mobile_image',
            'web_image'    => 'file:web_image',
        ];

    }//end filters()


}//end class
