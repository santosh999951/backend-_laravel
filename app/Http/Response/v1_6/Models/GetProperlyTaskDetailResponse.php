<?php
/**
 * GetProperlyTaskDetailResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetProperlyTaskDetailResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetProperlyTaskDetailResponse",
 * description="GetProperlyTaskDetailResponse",
 * )
 * // phpcs:enable
 */
class GetProperlyTaskDetailResponse extends ApiResponse
{

    /**
     * Property Task Section
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="task_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Task Section",
	 *     @SWG\Property(
	 *       property="task_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Task Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="entity_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Entity Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="task_status",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Task Status",
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Property Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="color_code",
	 *           type="string",
	 *           default="",
	 *           description="Property Color Code"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="task_type",
	 *       type="string",
	 *       default="",
	 *       description="Property Task Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="task_date",
	 *       type="string",
	 *       default="",
	 *       description="Property Task Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="task_time",
	 *       type="string",
	 *       default="",
	 *       description="Property Task Time"
	 *     ),
	 *     @SWG\Property(
	 *       property="task_date_time_formatted",
	 *       type="string",
	 *       default="",
	 *       description="Property Task Date Time Formatted"
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_name",
	 *       type="string",
	 *       default="",
	 *       description="Property Traveller Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="assigned_to",
	 *       type="string",
	 *       default="",
	 *       description="Property Assigned To"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Guests"
	 *     ),
	 *     @SWG\Property(
	 *       property="reccuring_type",
	 *       type="string",
	 *       default="",
	 *       description="Property Reccuring Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="can_update",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Can Update"
	 *     ),
	 *     @SWG\Property(
	 *       property="description",
	 *       type="string",
	 *       default="",
	 *       description="Property Description"
	 *     ),
	 *     @SWG\Property(
	 *       property="can_status_edit",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Can Status Edit"
	 *     ),
	 *     @SWG\Property(
	 *       property="can_desc_edit",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Can Desc Edit"
	 *     ),
	 *     @SWG\Property(
	 *       property="can_allocate",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Can Allocate"
	 *     ),
	 *     @SWG\Property(
	 *       property="can_time_edit",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Can Time Edit"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $task_section = [];

    /**
     * Property Assigned To
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="assigned_to",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Assigned To",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="string",
	 *       default="",
	 *       description="Property Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Property Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="role",
	 *       type="string",
	 *       default="",
	 *       description="Property Role"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $assigned_to = [];


    /**
     * Get Task_section
     *
     * @return object
     */
    public function getTaskSection()
    {
        return (empty($this->task_section) === false) ? $this->task_section : new \stdClass;

    }//end getTaskSection()


    /**
     * Set Task section
     *
     * @param array $task_section Task section.
     *
     * @return self
     */
    public function setTaskSection(array $task_section)
    {
        $this->task_section = $task_section;
        return $this;

    }//end setTaskSection()


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
