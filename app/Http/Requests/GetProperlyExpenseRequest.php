<?php
/**
 * Properly Expense  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use \Carbon\Carbon;

/**
 * Class GetProperlyExpenseRequest
 */
class GetProperlyExpenseRequest extends BaseFormRequest
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
            'month_year'       => 'date_format:Y-m|before_or_equal:'.Carbon::now()->format('Y-m'),
            'offset'           => 'integer|min:0',
            'total'            => 'integer|min:1|max:100',
        ];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanitization Parameters and Its Default Value.
        return [
            'property_hash_id' => 'trim',
            'month_year'       => 'default:'.Carbon::now()->format('Y-m'),
            'offset'           => 'integer|default:0',
            'total'            => 'integer|default:20',
        ];

    }//end filters()


}//end class
