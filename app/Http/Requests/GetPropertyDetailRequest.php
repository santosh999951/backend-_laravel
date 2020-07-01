<?php
/**
 * PropertyDetailRequest Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetPropertyDetailRequest
 */
class GetPropertyDetailRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'guests'   => 'integer|min:0|max:100',
            'units'    => 'integer|min:0|max:100',
            'checkin'  => 'date',
            'checkout' => 'date',
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
            'guests'   => 'integer|default:0',
            'units'    => 'integer|default:0',
            'checkin'  => 'trim|default:empty',
            'checkout' => 'trim|default:empty',
        ];

    }//end filters()


}//end class
