<?php
/**
 * Post Promo Banner  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPromoBannerRequest
 */
class PostPromoBannerRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'web_image_name'    => 'required_without:mobile_image_name|string|max:200',
            'mobile_image_name' => 'required_without:web_image_name|string|max:200',
            'status'            => 'required|integer|in:0,1',
            'name'              => 'required|alpha_num|max:20',
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
            'web_image_name'    => 'trim',
            'mobile_image_name' => 'trim',
            'status'            => 'integer',
            'name'              => 'trim',
        ];

    }//end filters()


}//end class
