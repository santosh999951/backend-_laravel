<?php
/**
 * Get Host Property Reviews Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetHostPropertyReviewsRequest
 */
class GetHostPropertyReviewsRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'property_hash_id' => 'alpha_num|min:'.(HASH_LENGTH_FOR_PROPERTY + 1).'|max:10',
            'filter_type'      => 'integer|in:'.HOST_FILTER_NEW_GUEST_REVIEW,
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
        // Data Sanetization Parameters and Its Default Value.
        return [
            'property_hash_id' => 'trim|default:empty',
            'filter_type'      => 'integer|default:empty',
            'offset'           => 'default:0|integer',
            'total'            => 'default:100|integer',
        ];

    }//end filters()


}//end class
