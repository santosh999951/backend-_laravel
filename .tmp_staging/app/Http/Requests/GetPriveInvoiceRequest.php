<?php
/**
 * Prive User Invoice List Request model.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use Carbon\Carbon;

/**
 * Class GetPriveInvoiceRequest
 */
class GetPriveInvoiceRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'property_hash_id' => 'string',
            'month_year'       => 'date_format:m-y',
            'offset'           => 'integer|min:0',
            'total'            => 'integer|min:1|max:5000',
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
            'month_year'       => 'default:'.Carbon::now()->format('m-y'),
            'offset'           => 'default:0|integer',
            'total'            => 'default:5000|integer',
        ];

    }//end filters()


}//end class
