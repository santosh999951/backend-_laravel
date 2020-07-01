<?php
/**
 * Put offers Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutOffersRequest
 */
class PutOffersRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'id'            => 'required|integer',
            'name'          => 'required_without_all:title,status,default,imagedata,description,destination|alpha_num|max:20',
            'title'         => 'required_without_all:name,status,default,imagedata,description,destination,name|max:200',
            'status'        => 'required_without_all:title,name,default,imagedata,description,destination|integer|in:0,1',
            'default'       => 'required_without_all:title,status,imagedata,description,destination,name|integer|in:0,1',
            'imagedata'     => 'required_without_all:title,status,default,description,destination,name|json',
            'description'   => 'required_without_all:title,status,default,imagedata,destination,name|array',
            // In description only letters ,numbers and some special symbols are allowed.
            'description.*' => 'regex:"[A-Za-z0-9\s@#:;.]+$"',
            'destination'   => 'required_without_all:title,status,default,imagedata,description,name|json',

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
            'id'      => 'integer',
            'name'    => 'trim',
            'title'   => 'trim',
            'status'  => 'integer',
            'default' => 'integer',
        ];

    }//end filters()


}//end class
