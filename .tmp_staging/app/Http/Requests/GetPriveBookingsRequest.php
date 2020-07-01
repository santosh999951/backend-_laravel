<?php
/**
 * Prive User Booking List Request model.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use Carbon\Carbon;

/**
 * Class GetPriveBookingsRequest
 */
class GetPriveBookingsRequest extends BaseFormRequest
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
            'start_date'       => 'date',
            'end_date'         => 'date|after_or_equal:start_date',
            'offset'           => 'integer|min:0',
            'total'            => 'integer|min:1',
            // 1 for checkin, 2 checkout, 3 booking amount,
            'sort'             => 'integer|in:1,2,3',
            'sort_order'       => 'string|in:ASC,DESC',
            // 1 Booked 2 cancelled.
            'status'           => 'integer|in:1,2',
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
            'property_hash_id' => 'trim',
            'start_date'       => 'trim|default:'.Carbon::now()->toDateString().'|date',
            'end_date'         => 'trim|default:'.Carbon::now()->addMonth(1)->toDateString().'|date',
            'offset'           => 'default:0|integer',
            'total'            => 'default:100|integer',
            'sort_order'       => 'trim|default:ASC',
            // Default sorting on the basis of checkin date.
            'sort'             => 'integer|default:1',
            'status'           => 'integer',
        ];

    }//end filters()


}//end class
