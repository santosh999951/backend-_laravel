<?php
/**
 * ErrorHttpResponse
 */

namespace App\Http\Response\v1_7\Models;

/**
 * Class ErrorHttpResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="ErrorHttpResponse",
 * description="ErrorHttpResponse",
 * )
 * // phpcs:enable
 */
class ErrorHttpResponse extends ApiResponse
{

    /**
     * Error Status
     *
     * @var boolean
     * // phpcs:disable
     * @SWG\Property(
     *   property="status",
     *   type="boolean",
     *   default="false",
     *   description="Error Status"
     * )
     * // phpcs:enable
     */
    protected $status = false;

    /**
     * Error Data
     *
     * @var array
     * // phpcs:disable
     * @SWG\Property(
     *   property="data",
     *   type="object",
     *   default="{}",
     *   description="Error Data",
     * )
     * // phpcs:enable
     */
    protected $data = [];

    /**
     * Error
     *
     * @var array
     * // phpcs:disable
     * @SWG\Property(
     *   property="error",
     *   type="array",
     *   default="[]",
     *   description="Error",
     *   @SWG\Items(
     *     type="object",
     *     @SWG\Property(
     *       property="code",
     *       type="string",
     *       default="",
     *       description="Error Code"
     *     ),
     *     @SWG\Property(
     *       property="key",
     *       type="string",
     *       default="",
     *       description="Error Key"
     *     ),
     *     @SWG\Property(
     *       property="message",
     *       type="string",
     *       default="",
     *       description="Error Message"
     *     )
     *   )
     * )
     * // phpcs:enable
     */
    protected $error = [];


    /**
     * Get Status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;

    }//end getStatus()


    /**
     * Set Status
     *
     * @param boolean $status Status.
     *
     * @return self
     */
    public function setStatus(bool $status)
    {
        $this->status = $status;
        return $this;

    }//end setStatus()


    /**
     * Get Data
     *
     * @return object
     */
    public function getData()
    {
        return (empty($this->data) === false) ? $this->data : new \stdClass;

    }//end getData()


    /**
     * Set Data
     *
     * @param array $data Data.
     *
     * @return self
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;

    }//end setData()


    /**
     * Get Error
     *
     * @return array
     */
    public function getError()
    {
        return $this->error;

    }//end getError()


    /**
     * Set Error
     *
     * @param array $error Error.
     *
     * @return self
     */
    public function setError(array $error)
    {
        $this->error = $error;
        return $this;

    }//end setError()


}//end class
