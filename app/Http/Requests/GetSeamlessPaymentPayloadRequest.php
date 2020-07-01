<?php
/**
 * Seamless Payment Payload Model.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetSeamlessPaymentPayloadRequest
 */
class GetSeamlessPaymentPayloadRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // No params.
        return [];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanitization Parameters and Its Default Value.
        return [];

    }//end filters()


}//end class
