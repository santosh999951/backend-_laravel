<?php
/**
 * Post Offline Discovery Upload lead Image Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostOfflineDiscoveryUploadLeadImageRequest
 */
class PostOfflineDiscoveryUploadLeadImageRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lead_image' => 'required',
            'lead_id'    => 'required|integer',
        ];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        return [
            'lead_image' => 'trim',
            'lead_id'    => 'trim',
        ];

    }//end filters()


}//end class
