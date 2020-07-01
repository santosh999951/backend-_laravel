<?php
/**
 * Properly Create Team member request model.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostProperlyCreateMemberRequest
 */
class PostProperlyCreateMemberRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name'   => 'required|string',
            'last_name'    => 'string',
            'phone'        => 'required|digits_between:8,12',
            'dial_code'    => 'required|integer',
            'team_type_id' => 'required|integer',
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
            'phone'        => 'trim',
            'dial_code'    => 'trim',
            'team_type_id' => 'trim',
        ];

    }//end filters()


}//end class
