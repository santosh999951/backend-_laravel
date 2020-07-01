<?php
/**
 * User Update Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutUpdateUserRequest
 */
class PutUpdateUserRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'first_name'       => 'sometimes|required|string|max:50|min:1',
            'last_name'        => 'string|max:50',
            'gender'           => 'string|in:Male,Female',
            'marital_status'   => 'string|in:Single,Married,Family',
            'profession'       => 'string|max:90',
            'spoken_languages' => 'string|max:90',
            'travelled_places' => 'string|max:190',
        ];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Set empty string when parameter not provided.
        return [
            'first_name'       => 'escape|trim|lowercase',
            'last_name'        => 'escape|trim|lowercase',
            'gender'           => 'trim|capitalize',
            'marital_status'   => 'trim',
            'profession'       => 'escape|trim',
            'spoken_languages' => 'escape|trim',
            'travelled_places' => 'escape|trim',

        ];

    }//end filters()


}//end class
