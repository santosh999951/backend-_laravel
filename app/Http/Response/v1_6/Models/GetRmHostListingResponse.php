<?php
/**
 * GetRmHostListingResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetRmHostListingResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetRmHostListingResponse",
 * description="GetRmHostListingResponse",
 * )
 * // phpcs:enable
 */
class GetRmHostListingResponse extends ApiResponse
{

    /**
     * Host List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="host_list",
	 *   type="array",
	 *   default="[]",
	 *   description="Host List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="host_id",
	 *       type="string",
	 *       default="",
	 *       description="Host Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Host Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="email",
	 *       type="string",
	 *       default="",
	 *       description="Host Email"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Host Property Count"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $host_list = [];


    /**
     * Get Host_list
     *
     * @return array
     */
    public function getHostList()
    {
        return $this->host_list;

    }//end getHostList()


    /**
     * Set Host list
     *
     * @param array $host_list Host list.
     *
     * @return self
     */
    public function setHostList(array $host_list)
    {
        $this->host_list = $host_list;
        return $this;

    }//end setHostList()


}//end class
