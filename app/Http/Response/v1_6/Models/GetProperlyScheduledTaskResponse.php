<?php
/**
 * GetProperlyScheduledTaskResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetProperlyScheduledTaskResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetProperlyScheduledTaskResponse",
 * description="GetProperlyScheduledTaskResponse",
 * )
 * // phpcs:enable
 */
class GetProperlyScheduledTaskResponse extends ApiResponse
{

    /**
     * Tasks
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="tasks",
	 *   type="array",
	 *   default="[]",
	 *   description="Tasks",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="task_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Task Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="entity_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Entity Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="task_status",
	 *       type="object",
	 *       default="{}",
	 *       description="Task Status",
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="color_code",
	 *           type="string",
	 *           default="",
	 *           description="Color Code"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="task_type",
	 *       type="string",
	 *       default="",
	 *       description="Task Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="task_date",
	 *       type="string",
	 *       default="",
	 *       description="Task Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="task_date_time_formatted",
	 *       type="string",
	 *       default="",
	 *       description="Task Date Time Formatted"
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_name",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="assigned_to",
	 *       type="string",
	 *       default="",
	 *       description="Assigned To"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Guests"
	 *     ),
	 *     @SWG\Property(
	 *       property="reccuring_type",
	 *       type="string",
	 *       default="",
	 *       description="Reccuring Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="can_update",
	 *       type="integer",
	 *       default="0",
	 *       description="Can Update"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $tasks = [];

    /**
     * Assigned To
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="assigned_to",
	 *   type="array",
	 *   default="[]",
	 *   description="Assigned To",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="string",
	 *       default="",
	 *       description="Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="role",
	 *       type="string",
	 *       default="",
	 *       description="Role"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $assigned_to = [];


    /**
     * Get Tasks
     *
     * @return array
     */
    public function getTasks()
    {
        return $this->tasks;

    }//end getTasks()


    /**
     * Set Tasks
     *
     * @param array $tasks Tasks.
     *
     * @return self
     */
    public function setTasks(array $tasks)
    {
        $this->tasks = $tasks;
        return $this;

    }//end setTasks()


    /**
     * Get Assigned_to
     *
     * @return array
     */
    public function getAssignedTo()
    {
        return $this->assigned_to;

    }//end getAssignedTo()


    /**
     * Set Assigned to
     *
     * @param array $assigned_to Assigned to.
     *
     * @return self
     */
    public function setAssignedTo(array $assigned_to)
    {
        $this->assigned_to = $assigned_to;
        return $this;

    }//end setAssignedTo()


}//end class
