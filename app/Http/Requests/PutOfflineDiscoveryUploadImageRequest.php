<?php
/**
 * Post Offline Discovery Create Property Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostOffersRequest
 */
class PutOfflineDiscoveryUploadImageRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'auth_key'    => 'required',
            'lead'        => 'required',
            'image_count' => 'required',
        ];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        return [];

    }//end filters()


}//end class
