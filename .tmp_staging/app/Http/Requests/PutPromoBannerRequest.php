<?php
/**
 * Put Promo Banner  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutPromoBannerRequest
 */
class PutPromoBannerRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'                => 'required|integer',
            'web_image_name'    => 'required_without_all:mobile_image_name,status,name|string|max:200',
            'mobile_image_name' => 'required_without_all:web_image_name,status,name|string|max:200',
            'status'            => 'required_without_all:mobile_image_name,web_image_name,name|integer|in:0,1',
            'name'              => 'required_without_all:mobile_image_name,status,web_image_name|alpha_num|max:20',
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
            'id'                => 'integer',
            'web_image_name'    => 'trim',
            'mobile_image_name' => 'trim',
            'status'            => 'integer',
            'destination'       => 'trim',
        ];

    }//end filters()


}//end class
