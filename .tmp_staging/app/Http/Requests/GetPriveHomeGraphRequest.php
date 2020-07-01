<?php
/**
 * Prive  Home Graph  Request  model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use \Carbon\Carbon;

/**
 * Class GetPriveHomeGraphRequest
 */
class GetPriveHomeGraphRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'month_year_from' => 'date_format:m-Y',
            'month_year_to'   => 'after_or_equal:month_year_from|date_format:m-Y',

        ];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanatization Parameters and Its Default Value.
        return [
            'month_year_from' => 'default:'.Carbon::now()->format('m-Y'),
            'month_year_to'   => 'default:'.Carbon::now()->format('m-Y'),
        ];

    }//end filters()


}//end class
