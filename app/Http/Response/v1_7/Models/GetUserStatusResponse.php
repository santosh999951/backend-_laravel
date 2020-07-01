<?php
/**
 * GetUserStatusResponse
 */

namespace App\Http\Response\v1_7\Models;

/**
 * Class GetUserStatusResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetUserStatusResponse",
 * description="GetUserStatusResponse",
 * )
 * // phpcs:enable
 */
class GetUserStatusResponse extends ApiResponse
{

    /**
     * User Status
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="status",
	 *   type="string",
	 *   default="",
	 *   description="User Status"
	 * )
     * // phpcs:enable
     */
    protected $status = '';


    /**
     * Get Status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;

    }//end getStatus()


    /**
     * Set Status
     *
     * @param string $status Status.
     *
     * @return self
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
        return $this;

    }//end setStatus()


}//end class
