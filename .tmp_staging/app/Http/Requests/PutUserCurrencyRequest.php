<?php
/**
 * User Currency  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutUserCurrencyRequest
 */
class PutUserCurrencyRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'currency' => 'required|in:'.implode(',', array_keys(CURRENCY_SYMBOLS)),
        ];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Set empty string when parameter not provided.
        return ['currency' => 'trim'];

    }//end filters()


}//end class
