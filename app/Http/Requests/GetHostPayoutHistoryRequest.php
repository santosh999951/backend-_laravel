<?php
/**
 * Get Host Payout History Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use \Carbon\Carbon;

/**
 * Class GetHostPayoutHistoryRequest
 */
class GetHostPayoutHistoryRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date|after:start_date',
            'status'     => 'in:1,2',
            'offset'     => 'integer|min:0',
            'total'      => 'integer|min:0',
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
            'start_date' => 'trim|default:empty|date',
            'end_date'   => 'trim|default:empty|date',
            'status'     => 'integer',
            'offset'     => 'trim|default:0|integer',
            'total'      => 'trim|default:50|integer',
        ];

    }//end filters()


}//end class
