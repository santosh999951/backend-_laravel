<?php
/**
 * Post Properly expense Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use \Carbon\Carbon;

/**
 * Class PostProperlyExpenseRequest
 */
class PostProperlyExpenseRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'property_hash_id' => 'required|alpha_num|min:'.(HASH_LENGTH_FOR_PROPERTY + 1).'|max:10',
            'month_year'       => 'required|date_format:Y-m|before_or_equal:'.Carbon::now()->format('Y-m'),
            'name'             => 'required|min:1|max:50',
            'basic_amount'     => 'required|numeric|min:1|regex:/^\d+(\.\d{1,2})?$/',
            'nights'           => 'sometimes|required|numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
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


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanetization Parameters and Its Default Value.
        return [
            'property_hash_id' => 'trim',
            'month_year'       => 'trim',
            'name'             => 'trim',
        ];

    }//end filters()


}//end class
