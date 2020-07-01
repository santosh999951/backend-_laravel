<?php
/**
 * Post Offline Discovery Create Property Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostOffersRequest
 */
class PutOfflineDiscoveryCreatePropertyRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'auth_key'            => 'required',
            'property_name'       => 'required',
            'email'               => 'required',
            'contact'             => 'required',
            'contact_name'        => 'required',
            'country'             => 'required',
            'state'               => 'required',
            'latitude'            => 'required',
            'longitude'           => 'required',
            'tariff'              => 'required',
            'cancellation_policy' => 'required',
            'total_listings'      => 'required',
            'amenities'           => 'required',
            'image_count'         => 'required',
            'phone'               => 'required',
        ];

    }//end rules()


      /**
       * Custom message for validation
       *
       * @return array
       */
    public function messages()
    {
        return ['description.*.regex' => 'description can only contains letter, number and special symbol like @,#,:,;,.'];

    }//end messages()


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
