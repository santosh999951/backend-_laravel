<?php
/**
 *  Generate Otp Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests\v1_7;

use App\Providers\AppServiceProvider;
use App\Http\Requests\BaseFormRequest;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostGenerateOtpRequest
 */
class PostGenerateOtpRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'contact'    => 'required|digits_between:8,12',
            'dial_code'  => 'required|digits_between:1,4',
            'otp_method' => 'required|integer|in:1,2',
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
            'contact_number' => 'integer',
            'dial_code'      => 'integer',
            'otp_method'     => 'integer',
        ];

    }//end filters()


}//end class
