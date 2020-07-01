<?php
/**
 * GetUserWishlistRequest Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetUserWishlistRequest
 */
class GetUserWishlistRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
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
        // Data Sanitization Parameters and Its Default Value.
        return [
            'offset' => 'integer|default:0',
            'total'  => 'integer|default:10',
        ];

    }//end filters()


}//end class
