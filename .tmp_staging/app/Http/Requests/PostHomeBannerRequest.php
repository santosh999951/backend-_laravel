<?php
/**
 * Home Banner  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostHomeBannerRequest
 */
class PostHomeBannerRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mobile_image_name' => 'required|string|max:200',
            'status'            => 'required|integer|in:0,1',
            'destination'       => 'required|json',
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
            'mobile_image_name' => 'trim',
            'status'            => 'integer',
            'destination'       => 'trim',
        ];

    }//end filters()


}//end class
