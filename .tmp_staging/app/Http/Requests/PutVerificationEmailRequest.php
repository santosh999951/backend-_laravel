<?php
/**
 * Put Verification Email Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutVerificationEmailRequest
 */
class PutVerificationEmailRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['confirmation_code' => 'required|string|size:36'];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanitization Parameters and Its Default Value.
        return ['confirmation_code' => 'trim'];

    }//end filters()


}//end class
