<?php
/**
 * Put Properly Task Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PutProperlyTaskStatusRequest
 */
class PutProperlyTaskStatusRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'task_hash_id' => 'required|alpha_num|min:'.(HASH_LENGTH_FOR_TASK),
            'status'       => 'required|integer|in:'.PRIVE_TASK_OPEN.','.PRIVE_TASK_TODO.','.PRIVE_TASK_PENDING.','.PRIVE_TASK_COMPLETED,
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
            'task_hash_id' => 'trim',
            'status'       => 'integer',
        ];

    }//end filters()


}//end class
