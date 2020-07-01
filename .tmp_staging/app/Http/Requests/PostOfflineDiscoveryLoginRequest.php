<?php
/**
 * Offline Discovery Login Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostOfflineDiscoveryLoginRequest
 */
class PostOfflineDiscoveryLoginRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'device_type'  => 'string',
            'access_token' => 'required',
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
            'device_type'  => 'trim|default:'.DEFAULT_DEVICE_TYPE,
            'access_token' => 'trim||default:empty',
        ];

    }//end filters()


}//end class
