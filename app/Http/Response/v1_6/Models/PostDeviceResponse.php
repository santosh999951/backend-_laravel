<?php
/**
 * PostDeviceResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostDeviceResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostDeviceResponse",
 * description="PostDeviceResponse",
 * )
 * // phpcs:enable
 */
class PostDeviceResponse extends ApiResponse
{

    /**
     * Guesthouser Basic Info
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="basic_info",
	 *   type="object",
	 *   default="{}",
	 *   description="Guesthouser Basic Info",
	 *     @SWG\Property(
	 *       property="properties_count",
	 *       type="string",
	 *       default="",
	 *       description="Properties Count"
	 *     ),
	 *     @SWG\Property(
	 *       property="cities_count",
	 *       type="string",
	 *       default="",
	 *       description="Property Cities Count"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $basic_info = [];

    /**
     * Message Eg. Device successfully registered., Device successfully updated
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Message Eg. Device successfully registered., Device successfully updated"
	 * )
     * // phpcs:enable
     */
    protected $message = '';


    /**
     * Get Basic_info
     *
     * @return object
     */
    public function getBasicInfo()
    {
        return (empty($this->basic_info) === false) ? $this->basic_info : new \stdClass;

    }//end getBasicInfo()


    /**
     * Set Basic info
     *
     * @param array $basic_info Basic info.
     *
     * @return self
     */
    public function setBasicInfo(array $basic_info)
    {
        $this->basic_info = $basic_info;
        return $this;

    }//end setBasicInfo()


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


}//end class
