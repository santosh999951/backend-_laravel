<?php
/**
 * Post Offers  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostOffersRequest
 */
class PostOffersRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Sequence should be required, type, min,max.
            'name'          => 'required|alpha_num|max:20',
            'title'         => 'required|max:200',
            'status'        => 'integer|in:0,1',
            'default'       => 'integer|in:0,1',
            'description'   => 'required|array',
            // In description only letters ,numbers and some special symbols are allowed.
            'description.*' => 'regex:"[A-Za-z0-9\s@#:;.]+$"',
            'images'        => 'json',
            'destination'   => 'json',
        ];

    }//end rules()


      /**
       * Custom message for validation
       *
       * @return array
       */
    public function messages()
    {
        return ['description.*.regex' => 'description can only contains letter, number and special symbol like @,#,:,;,.'];

    }//end messages()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Data Sanetization Parameters and Its Default Value.
        return [
            'name'        => 'trim',
            'title'       => 'trim',
            'status'      => 'integer|default:0',
            'default'     => 'integer|default:0',
            'destination' => 'trim|default:empty',
        ];

    }//end filters()


}//end class
