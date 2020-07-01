<?php
/**
 * Search recent Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetSearchRecentRequest
 */
class GetSearchRecentRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        // Integer becoz it will check now min and max value.otherwise it will treat parameter as a string.
            'offset' => 'integer|min:0',
            'total'  => 'integer|min:1|max:100',
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
            'offset' => 'default:0|integer',
            'total'  => 'default:5|integer',
        ];

    }//end filters()


}//end class
