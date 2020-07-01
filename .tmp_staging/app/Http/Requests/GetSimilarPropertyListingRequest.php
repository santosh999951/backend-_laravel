<?php
/**
 * Similar  Property  listing Request Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use \Carbon\Carbon;

/**
 * Class GetSimilarPropertyListingRequest
 */
class GetSimilarPropertyListingRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'guests'   => 'integer|min:1|max:100',
            'units'    => 'integer|min:1|max:100',
            'checkin'  => 'date|after_or_equal:today',
            'checkout' => 'date|after:checkin',
            'offset'   => 'integer|min:0',
            'total'    => 'integer|min:1|max:100',
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
            'guests'   => 'integer|default:'.DEFAULT_NUMBER_OF_GUESTS,
            'units'    => 'integer|default:'.DEFAULT_NUMBER_OF_UNITS,
            'checkin'  => 'trim|default:'.Carbon::createFromTimestamp(time() + BOOKING_DEFAULT_START_DAYS_FROM_TODAY_IN_SECONDS),
            'checkout' => 'trim|default:'.Carbon::createFromTimestamp(time() + BOOKING_DEFAULT_END_DAYS_FROM_TODAY_IN_SECONDS),
            'offset'   => 'integer|default:0',
            'total'    => 'integer|default:10',
        ];

    }//end filters()


}//end class
