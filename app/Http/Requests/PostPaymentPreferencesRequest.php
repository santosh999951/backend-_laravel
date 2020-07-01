<?php
/**
 * User Add Bank Detail Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPaymentPreferencesRequest
 */
class PostPaymentPreferencesRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payee_name'     => 'required|string|min:3|max:50',
            'bank_name'      => 'required|string|min:3|max:100',
            'branch_name'    => 'required|string|min:3|max:100',
            'account_number' => 'required|numeric|digits_between:7,20',
            'ifsc_code'      => 'required|string|min:5|max:12',
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
            'address_line_1' => 'escape|trim|default:empty',
            'address_line_2' => 'escape|trim|default:empty',
            'country'        => 'escape|trim|default:empty',
            'state'          => 'escape|trim|default:empty',
            'routing_number' => 'escape|trim|default:empty',
            'gstin'          => 'escape|trim|default:empty',
        ];

    }//end filters()


}//end class
