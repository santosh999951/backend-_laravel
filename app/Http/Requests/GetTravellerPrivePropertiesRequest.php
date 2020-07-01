<?php
/**
 * Traveller Prive  Property  listing Request Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use \Carbon\Carbon;

/**
 * Class GetTravellerPrivePropertiesRequest
 */
class GetTravellerPrivePropertiesRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'city'   => 'sometimes|required',
            'offset' => 'integer|min:0',
            'total'  => 'integer|min:1',
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
            'city'   => 'trim|lowercase',
            'offset' => 'trim|default:0',
            'total'  => 'trim|default:10',
        ];

    }//end filters()


}//end class
