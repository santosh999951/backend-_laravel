<?php
/**
 * GetProperlyFilterMemberResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetProperlyFilterMemberResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetProperlyFilterMemberResponse",
 * description="GetProperlyFilterMemberResponse",
 * )
 * // phpcs:enable
 */
class GetProperlyFilterMemberResponse extends ApiResponse
{

    /**
     * Team Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="team_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Team Count"
	 * )
     * // phpcs:enable
     */
    protected $team_count = 0;

    /**
     * Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Message"
	 * )
     * // phpcs:enable
     */
    protected $message = '';

    /**
     * Team Members
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="members",
	 *   type="array",
	 *   default="[]",
	 *   description="Team Members",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="string",
	 *       default="",
	 *       description="User hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="User Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="last_name",
	 *       type="string",
	 *       default="",
	 *       description="User Last Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="contact",
	 *       type="string",
	 *       default="",
	 *       description="User Contact"
	 *     ),
	 *     @SWG\Property(
	 *       property="role_name",
	 *       type="string",
	 *       default="",
	 *       description="Role Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="deactivated_at",
	 *       type="none",
	 *       default="00",
	 *       description="Deactivated At"
	 *     ),
	 *     @SWG\Property(
	 *       property="status",
	 *       type="string",
	 *       default="",
	 *       description="Status"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $members = [];

    /**
     * Property Filter
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="filter",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Filter",
	 *     @SWG\Property(
	 *       property="team",
	 *       type="array",
	 *       default="[]",
	 *       description="Team",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="id",
	 *           type="integer",
	 *           default="0",
	 *           description="Team Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Team Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Team Selected"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="team_filter_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Team Filter Count"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $filter = [];


    /**
     * Get Team_count
     *
     * @return integer
     */
    public function getTeamCount()
    {
        return $this->team_count;

    }//end getTeamCount()


    /**
     * Set Team count
     *
     * @param integer $team_count Team count.
     *
     * @return self
     */
    public function setTeamCount(int $team_count)
    {
        $this->team_count = $team_count;
        return $this;

    }//end setTeamCount()


    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;

    }//end getMessage()


    /**
     * Set Message
     *
     * @param string $message Message.
     *
     * @return self
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;

    }//end setMessage()


    /**
     * Get Members
     *
     * @return array
     */
    public function getMembers()
    {
        return $this->members;

    }//end getMembers()


    /**
     * Set Members
     *
     * @param array $members Members.
     *
     * @return self
     */
    public function setMembers(array $members)
    {
        $this->members = $members;
        return $this;

    }//end setMembers()


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
