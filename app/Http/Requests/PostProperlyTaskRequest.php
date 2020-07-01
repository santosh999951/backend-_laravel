<?php
/**
 * PostProperlyTaskRequest model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;
use Carbon\Carbon;

/**
 * Class PostProperlyTaskRequest
 */
class PostProperlyTaskRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'entity_id'   => 'required|alpha_num|min:'.(HASH_LENGTH_FOR_BOOKING_REQUEST_ID),
            'type'        => 'required|integer|in:'.TASK_TYPE_OCCUPIED_SERVICE.','.TASK_TYPE_TURN_DOWN_SERVICE.','.TASK_TYPE_DEPARTURE_SERVICE.','.TASK_TYPE_MAINTAINENCE_SERVICE,
            'assigned_to' => 'sometimes|required|alpha_num|min:'.(HASH_LENGTH_FOR_USER),
            'run_at_date' => 'required|date_format:Y-m-d|after_or_equal:'.Carbon::now('Asia/Kolkata')->format('Y-m-d'),
            'run_at_time' => 'required|date_format:H:i:s',
            'description' => 'required|string|max:500',
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
        // Data Sanitization Parameters and Its Default Value.
        return [
            'entity_id'   => 'trim',
            'type'        => 'integer',
            'assigned_to' => 'integer',
            'run_at_date' => 'trim',
            'run_at_time' => 'trim',
            'description' => 'trim',
        ];

    }//end filters()


}//end class
