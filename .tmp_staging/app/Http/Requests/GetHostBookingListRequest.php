<?php
/**
 * Get Host Booking List Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class GetHostBookingListRequest
 */
class GetHostBookingListRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_date'        => 'date',
            'end_date'          => 'date|after:start_date',
            'order_by'          => 'integer|in:1,2,3',
            'filter_type'       => 'integer|in:1,2,3,5',
            'offset'            => 'integer|min:0',
            'total'             => 'integer|min:1|max:100',
            'property_hash_ids' => 'string',
            'status'            => 'string',
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
            'start_date'        => 'trim|default:empty|date',
            'end_date'          => 'trim|default:empty|date',
            'order_by'          => 'default:1|integer',
            'filter_type'       => 'integer|default:empty',
            'offset'            => 'default:0|integer',
            'total'             => 'default:100|integer',
            'property_hash_ids' => 'trim|default:empty',
            'status'            => 'trim|default:empty',
        ];

    }//end filters()


}//end class
