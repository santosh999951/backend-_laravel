<?php
/**
 * GetProperlyTaskResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetProperlyTaskResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetProperlyTaskResponse",
 * description="GetProperlyTaskResponse",
 * )
 * // phpcs:enable
 */
class GetProperlyTaskResponse extends ApiResponse
{

    /**
     * Tasks
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="tasks",
	 *   type="object",
	 *   default="{}",
	 *   description="Tasks",
	 *     @SWG\Property(
	 *       property="yesterday",
	 *       type="object",
	 *       default="{}",
	 *       description="Yesterday",
	 *         @SWG\Property(
	 *           property="list",
	 *           type="array",
	 *           default="[]",
	 *           description="List",
	 *           @SWG\Items(
	 *             type="object",
	 *             @SWG\Property(
	 *               property="task_hash_id",
	 *               type="string",
	 *               default="",
	 *               description="Task Hash Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="entity_hash_id",
	 *               type="string",
	 *               default="",
	 *               description="Entity Hash Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_status",
	 *               type="object",
	 *               default="{}",
	 *               description="Task Status",
	 *                 @SWG\Property(
	 *                   property="text",
	 *                   type="string",
	 *                   default="",
	 *                   description="Text"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="color_code",
	 *                   type="string",
	 *                   default="",
	 *                   description="Color Code"
	 *                 )
	 *             ),
	 *             @SWG\Property(
	 *               property="title",
	 *               type="string",
	 *               default="",
	 *               description="Title"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_type",
	 *               type="string",
	 *               default="",
	 *               description="Task Type"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_date",
	 *               type="string",
	 *               default="",
	 *               description="Task Date"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_time",
	 *               type="string",
	 *               default="",
	 *               description="Task Time"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_date_time_formatted",
	 *               type="string",
	 *               default="",
	 *               description="Task Date Time Formatted"
	 *             ),
	 *             @SWG\Property(
	 *               property="traveller_name",
	 *               type="string",
	 *               default="",
	 *               description="Traveller Name"
	 *             ),
	 *             @SWG\Property(
	 *               property="assigned_to",
	 *               type="string",
	 *               default="",
	 *               description="Assigned To"
	 *             ),
	 *             @SWG\Property(
	 *               property="guests",
	 *               type="integer",
	 *               default="0",
	 *               description="Guests"
	 *             ),
	 *             @SWG\Property(
	 *               property="reccuring_type",
	 *               type="string",
	 *               default="",
	 *               description="Reccuring Type"
	 *             ),
	 *             @SWG\Property(
	 *               property="can_update",
	 *               type="integer",
	 *               default="0",
	 *               description="Can Update"
	 *             ),
	 *             @SWG\Property(
	 *               property="description",
	 *               type="string",
	 *               default="",
	 *               description="Property Description"
	 *             )
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="total",
	 *           type="integer",
	 *           default="0",
	 *           description="Total"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="today",
	 *       type="object",
	 *       default="{}",
	 *       description="Today",
	 *         @SWG\Property(
	 *           property="list",
	 *           type="array",
	 *           default="[]",
	 *           description="List",
	 *           @SWG\Items(
	 *             type="object",
	 *             @SWG\Property(
	 *               property="task_hash_id",
	 *               type="string",
	 *               default="",
	 *               description="Task Hash Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="entity_hash_id",
	 *               type="string",
	 *               default="",
	 *               description="Entity Hash Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_status",
	 *               type="object",
	 *               default="{}",
	 *               description="Task Status",
	 *                 @SWG\Property(
	 *                   property="text",
	 *                   type="string",
	 *                   default="",
	 *                   description="Text"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="color_code",
	 *                   type="string",
	 *                   default="",
	 *                   description="Color Code"
	 *                 )
	 *             ),
	 *             @SWG\Property(
	 *               property="title",
	 *               type="string",
	 *               default="",
	 *               description="Title"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_type",
	 *               type="string",
	 *               default="",
	 *               description="Task Type"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_date",
	 *               type="string",
	 *               default="",
	 *               description="Task Date"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_time",
	 *               type="string",
	 *               default="",
	 *               description="Task Time"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_date_time_formatted",
	 *               type="string",
	 *               default="",
	 *               description="Task Date Time Formatted"
	 *             ),
	 *             @SWG\Property(
	 *               property="traveller_name",
	 *               type="string",
	 *               default="",
	 *               description="Traveller Name"
	 *             ),
	 *             @SWG\Property(
	 *               property="assigned_to",
	 *               type="string",
	 *               default="",
	 *               description="Assigned To"
	 *             ),
	 *             @SWG\Property(
	 *               property="guests",
	 *               type="integer",
	 *               default="0",
	 *               description="Guests"
	 *             ),
	 *             @SWG\Property(
	 *               property="reccuring_type",
	 *               type="string",
	 *               default="",
	 *               description="Reccuring Type"
	 *             ),
	 *             @SWG\Property(
	 *               property="can_update",
	 *               type="integer",
	 *               default="0",
	 *               description="Can Update"
	 *             ),
	 *             @SWG\Property(
	 *               property="description",
	 *               type="string",
	 *               default="",
	 *               description="Property Description"
	 *             )
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="total",
	 *           type="integer",
	 *           default="0",
	 *           description="Total"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="tomorrow",
	 *       type="object",
	 *       default="{}",
	 *       description="Tomorrow",
	 *         @SWG\Property(
	 *           property="list",
	 *           type="array",
	 *           default="[]",
	 *           description="List",
	 *           @SWG\Items(
	 *             type="object",
	 *             @SWG\Property(
	 *               property="task_hash_id",
	 *               type="string",
	 *               default="",
	 *               description="Task Hash Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="entity_hash_id",
	 *               type="string",
	 *               default="",
	 *               description="Entity Hash Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_status",
	 *               type="object",
	 *               default="{}",
	 *               description="Task Status",
	 *                 @SWG\Property(
	 *                   property="text",
	 *                   type="string",
	 *                   default="",
	 *                   description="Text"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="color_code",
	 *                   type="string",
	 *                   default="",
	 *                   description="Color Code"
	 *                 )
	 *             ),
	 *             @SWG\Property(
	 *               property="title",
	 *               type="string",
	 *               default="",
	 *               description="Title"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_type",
	 *               type="string",
	 *               default="",
	 *               description="Task Type"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_date",
	 *               type="string",
	 *               default="",
	 *               description="Task Date"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_time",
	 *               type="string",
	 *               default="",
	 *               description="Task Time"
	 *             ),
	 *             @SWG\Property(
	 *               property="task_date_time_formatted",
	 *               type="string",
	 *               default="",
	 *               description="Task Date Time Formatted"
	 *             ),
	 *             @SWG\Property(
	 *               property="traveller_name",
	 *               type="string",
	 *               default="",
	 *               description="Traveller Name"
	 *             ),
	 *             @SWG\Property(
	 *               property="assigned_to",
	 *               type="string",
	 *               default="",
	 *               description="Assigned To"
	 *             ),
	 *             @SWG\Property(
	 *               property="guests",
	 *               type="integer",
	 *               default="0",
	 *               description="Guests"
	 *             ),
	 *             @SWG\Property(
	 *               property="reccuring_type",
	 *               type="string",
	 *               default="",
	 *               description="Reccuring Type"
	 *             ),
	 *             @SWG\Property(
	 *               property="can_update",
	 *               type="integer",
	 *               default="0",
	 *               description="Can Update"
	 *             ),
	 *             @SWG\Property(
	 *               property="description",
	 *               type="string",
	 *               default="",
	 *               description="Property Description"
	 *             )
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="total",
	 *           type="integer",
	 *           default="0",
	 *           description="Total"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $tasks = [];

    /**
     * Filter
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="filter",
	 *   type="object",
	 *   default="{}",
	 *   description="Filter",
	 *     @SWG\Property(
	 *       property="status",
	 *       type="array",
	 *       default="[]",
	 *       description="Status",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="status",
	 *           type="integer",
	 *           default="0",
	 *           description="Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Selected"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="type",
	 *       type="array",
	 *       default="[]",
	 *       description="Type",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="status",
	 *           type="integer",
	 *           default="0",
	 *           description="Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Selected"
	 *         ),
	 *         @SWG\Property(
	 *           property="autogenerated",
	 *           type="integer",
	 *           default="0",
	 *           description="Autogenerated"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="properties",
	 *       type="array",
	 *       default="[]",
	 *       description="Properties",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="property_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Property Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Selected"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="assigned_to",
	 *       type="array",
	 *       default="[]",
	 *       description="Assigned To",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="id",
	 *           type="string",
	 *           default="",
	 *           description="Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="role",
	 *           type="string",
	 *           default="",
	 *           description="Role"
	 *         ),
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Selected"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $filter = [];


    /**
     * Get Tasks
     *
     * @return object
     */
    public function getTasks()
    {
        return (empty($this->tasks) === false) ? $this->tasks : new \stdClass;

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
     * Get Filter
     *
     * @return object
     */
    public function getFilter()
    {
        return (empty($this->filter) === false) ? $this->filter : new \stdClass;

    }//end getFilter()


    /**
     * Set Filter
     *
     * @param array $filter Filter.
     *
     * @return self
     */
    public function setFilter(array $filter)
    {
        $this->filter = $filter;
        return $this;

    }//end setFilter()


}//end class
