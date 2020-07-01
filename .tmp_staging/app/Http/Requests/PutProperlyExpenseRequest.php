<?php
/**
 * Update Properly Expense Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;


/**
 * Class PutProperlyExpenseRequest
 */
class PutProperlyExpenseRequest extends BaseFormRequest
{


    /**
     * Validation rules to be applied to the input.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'basic_amount' => 'required|min:1|regex:/^\d+(\.\d{1,2})?$/',
            'nights'       => 'sometimes|required|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
        ];

    }//end rules()


     /**
      * Custom message for validation
      *
      * @return array
      */
    public function messages()
    {
        return ['nights.min' => 'The quantity must be at least 0.01 .'];

    }//end messages()


}//end class
