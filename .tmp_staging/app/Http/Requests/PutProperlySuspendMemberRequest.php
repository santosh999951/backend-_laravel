<?php
/**
 * Properly Manager suspend member request api model.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutProperlySuspendMemberRequest
 */
class PutProperlySuspendMemberRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['user_hash_id' => 'required|string'];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanetization Parameters and Its Default Value.
        return ['user_hash_id' => 'trim'];

    }//end filters()


}//end class
