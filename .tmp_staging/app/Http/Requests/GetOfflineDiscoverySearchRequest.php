<?php
/**
 * Offline Discovery Search  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetOfflineDiscoverySearchRequest
 */
class GetOfflineDiscoverySearchRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'         => 'string|email',
            'contact'       => 'string|digits_between:8,12',
            'property_name' => 'string',
            'state'         => 'string',
            'city'          => 'string',
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
            'email'         => 'escape|default:empty',
            'contact'       => 'escape|default:empty',
            'property_name' => 'escape|default:empty',
            'state'         => 'escape|default:empty',
            'city'          => 'escape|default:empty',
            'country'       => 'escape|default:empty',
        ];

    }//end filters()


}//end class
