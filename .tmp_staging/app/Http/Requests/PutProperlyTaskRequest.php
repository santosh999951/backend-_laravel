<?php
/**
 * Properly  Task Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use \Carbon\Carbon;

/**
 * Class PutProperlyTaskRequest
 */
class PutProperlyTaskRequest extends BaseFormRequest
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
            'assigned_to'  => 'sometimes|required|alpha_num|min:'.(HASH_LENGTH_FOR_USER),
            'description'  => 'sometimes|required|string|max:500',
            'run_at_date'  => 'sometimes|required|date_format:Y-m-d|after_or_equal:'.Carbon::now('Asia/Kolkata')->format('Y-m-d'),
            'run_at_time'  => 'sometimes|required|date_format:H:i:s',
            'type'         => 'sometimes|required|integer|in:'.TASK_TYPE_CHECKIN.','.TASK_TYPE_CHECKOUT.','.TASK_TYPE_OCCUPIED_SERVICE.','.TASK_TYPE_TURN_DOWN_SERVICE.','.TASK_TYPE_DEPARTURE_SERVICE.','.TASK_TYPE_MAINTAINENCE_SERVICE,
        ];

    }//end rules()


    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'run_at_date.after_or_equal' => 'The task date must be after or equal to '.Carbon::now('Asia/Kolkata')->format('Y-m-d'),
        ];

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
            'task_hash_id' => 'trim',
            'assigned_to'  => 'integer',
            'description'  => 'trim',
            'run_at_date'  => 'trim',
            'run_at_time'  => 'trim',
            'type'         => 'integer',
        ];

    }//end filters()


}//end class
