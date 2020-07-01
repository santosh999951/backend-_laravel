<?php
/**
 * Prive User Booking List Request model.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use Carbon\Carbon;

/**
 * Class GetPriveManagerBookingsRequest
 */
class GetPriveManagerBookingsRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_date'       => 'date',
            'end_date'         => 'date|after_or_equal:start_date',
            'offset'           => 'integer|min:0',
            'total'            => 'integer|min:0|max:150',
            'sort_by'          => 'integer|in:'.PRIVE_BOOKING_SORT_BY_CHECKIN.','.PRIVE_BOOKING_SORT_BY_CHECKOUT.','.PRIVE_BOOKING_SORT_BY_AMOUNT,
            'sort_order'       => 'string|in:ASC,DESC',
            // PRIVE_MANAGER_UPCOMING, PRIVE_MANAGER_CHECKEDIN, PRIVE_MANAGER_CHECKEDOUT, PRIVE_MANAGER_NO_SHOW, PRIVE_MANAGER_COMPLETED, PRIVE_MANAGER_CANCELLED.
            'status'           => 'string',
            'property_hash_id' => 'string',
            'enable_count'     => 'integer|in:0,1',
            'search'           => 'string|min:1',
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
            'start_date'       => 'trim|date',
            'end_date'         => 'trim|date',
            'offset'           => 'trim|default:0|integer',
            'total'            => 'trim|default:150|integer',
            'sort_by'          => 'trim|integer',
            'sort_order'       => 'trim',
            'status'           => 'trim',
            'property_hash_id' => 'trim',
            'enable_count'     => 'trim|default:0|integer',
            'search'           => 'trim|escape',
        ];

    }//end filters()


}//end class
