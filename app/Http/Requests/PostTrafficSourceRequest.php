<?php
/**
 * Traffic source  Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostTrafficSourceRequest
 */
class PostTrafficSourceRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'source'   => 'string|max:255',
            'medium'   => 'string|max:255',
            'campaign' => 'string|max:255',
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
            'source'   => 'trim|default:empty',
            'medium'   => 'trim|default:empty',
            'campaign' => 'trim|default:empty',
        ];

    }//end filters()


}//end class
