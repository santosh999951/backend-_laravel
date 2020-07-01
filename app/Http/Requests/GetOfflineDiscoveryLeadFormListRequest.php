<?php
/**
 * Offline Discovery Lead Form Field List Request model.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetOfflineDiscoveryLeadFormListRequest
 */
class GetOfflineDiscoveryLeadFormListRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];

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
