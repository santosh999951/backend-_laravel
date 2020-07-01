<?php
/**
 * Prive User Login Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostPriveLoginRequest
 */
class PostPriveLoginRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'    => 'required|email',
            'password' => 'required',
            // Source 1 for Prive Owner and 2 for Prive Manager.
            'source'   => 'required|in:1,2',
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
            'email'    => 'trim|lowercase',
            'password' => 'trim',
            // Set Default login as Prive Owner.
            'source'   => 'integer',
        ];

    }//end filters()


}//end class
