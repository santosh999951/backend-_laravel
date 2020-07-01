<?php
/**
 * SuccessHttpResponse
 */

namespace App\Http\Response\v1_7\Models;

/**
 * Class SuccessHttpResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="SuccessHttpResponse",
 * description="SuccessHttpResponse",
 * )
 * // phpcs:enable
 */
class SuccessHttpResponse extends ApiResponse
{

    /**
     * Success Status
     *
     * @var boolean
     * // phpcs:disable
     * @SWG\Property(
     *   property="status",
     *   type="boolean",
     *   default="false",
     *   description="Success Status"
     * )
     * // phpcs:enable
     */
    protected $status = false;

    /**
     * Success Data
     *
     * @var array
     * // phpcs:disable
     * @SWG\Property(
     *   property="data",
     *   type="object",
     *   default="{}",
     *   description="Success Data",
     * )
     * // phpcs:enable
     */
    protected $data = [];

    /**
     * Success Error
     *
     * @var array
     * // phpcs:disable
     * @SWG\Property(
     *   property="error",
     *   type="array",
     *   default="[]",
     *   description="Success Error",
     *   @SWG\Items(
     *     type="object",
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
