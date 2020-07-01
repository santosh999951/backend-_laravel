<?php
/**
 * Put Home Banner  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutHomeBannerRequest
 */
class PutHomeBannerRequest extends BaseFormRequest
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
            'mobile_image_name' => 'required_without_all:status,destination|string|max:200',
            'status'            => 'required_without_all:mobile_image_name,destination|integer|in:0,1',
            'destination'       => 'required_without_all:mobile_image_name,status|json',
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
            'mobile_image_name' => 'trim',
            'status'            => 'integer',
            'destination'       => 'trim',
        ];

    }//end filters()


}//end class
