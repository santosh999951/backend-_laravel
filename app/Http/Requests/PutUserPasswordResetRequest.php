<?php
/**
 * Put User Password Reset Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use App\Providers\AppServiceProvider;

/**
 * Class PutUserPasswordResetRequest
 */
class PutUserPasswordResetRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token_hash' => 'required|alpha_num|size:62',
            'password'   => 'required|min:6',
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
            'token_hash' => 'trim',
            'password'   => 'trim|base64_decode',
        ];

    }//end filters()


}//end class
