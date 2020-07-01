<?php
/**
 * User update Bank Detail Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutPaymentPreferencesRequest
 */
class PutPaymentPreferencesRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payee_name'     => 'sometimes|required|string|min:3|max:50',
            'bank_name'      => 'sometimes|required|string|min:3|max:100',
            'branch_name'    => 'sometimes|required|string|min:3|max:100',
            'account_number' => 'sometimes|required|numeric|digits_between:3,20',
            'ifsc_code'      => 'sometimes|required|alpha_num|min:5|max:12',
            'address_line_1' => 'string|max:255',
            'address_line_2' => 'string|max:255',
            'country'        => 'string|max:100',
            'state'          => 'string|max:100',
            'routing_number' => 'string|max:16',
            'gstin'          => 'string|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',

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
            'payee_name'     => 'escape|trim',
            'bank_name'      => 'escape|trim|capitalize',
            'branch_name'    => 'escape|trim',
            'account_number' => 'trim',
            'ifsc_code'      => 'trim',
            'address_line_1' => 'trim',
            'address_line_2' => 'trim',
            'country'        => 'trim',
            'state'          => 'trim',
            'routing_number' => 'trim',
            'gstin'          => 'trim',
        ];

    }//end filters()


}//end class
