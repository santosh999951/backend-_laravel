<?php
/**
 * Put User Password Update Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use App\Providers\AppServiceProvider;

/**
 * Class PutUserPasswordUpdateRequest
 */
class PutUserPasswordUpdateRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'current_password' => 'required|min:6',
            'password'         => 'required|min:6',
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
            'current_password' => 'trim|base64_decode',
            'password'         => 'trim|base64_decode',
        ];

    }//end filters()


}//end class
