<?php
/**
 * Properly resend login access token api model.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetProperlyResendLoginAccessRequest
 */
class GetProperlyResendLoginAccessRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['contact' => 'required|digits_between:8,12'];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanetization Parameters and Its Default Value.
        return ['contact' => 'trim'];

    }//end filters()


}//end class
