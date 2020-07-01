<?php
/**
 * Host Property Price Calender Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use \Carbon\Carbon;

/**
 * Class GetHostPropertyPriceCalenderRequest
 */
class GetHostPropertyPriceCalenderRequest extends BaseFormRequest
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
            'start_date' => 'trim|default:'.Carbon::now()->toDateString().'|date',
            'end_date'   => 'trim|default:'.Carbon::now()->addYear(1)->toDateString().'|date',
        ];

    }//end filters()


}//end class
