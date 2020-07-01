<?php
/**
 * PutHostBookingStatusResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PutHostBookingStatusResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PutHostBookingStatusResponse",
 * description="PutHostBookingStatusResponse",
 * )
 * // phpcs:enable
 */
class PutHostBookingStatusResponse extends ApiResponse
{

    /**
     * Booking Status
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_status",
	 *   type="object",
	 *   default="{}",
	 *   description="Booking Status",
	 *     @SWG\Property(
	 *       property="text",
	 *       type="string",
	 *       default="",
	 *       description="Booking Status Text"
	 *     ),
	 *     @SWG\Property(
	 *       property="class",
	 *       type="string",
	 *       default="",
	 *       description="Booking Status Class"
	 *     ),
	 *     @SWG\Property(
	 *       property="color_code",
	 *       type="string",
	 *       default="",
	 *       description="Booking Status Color Code"
	 *     ),
	 *     @SWG\Property(
	 *       property="status",
	 *       type="integer",
	 *       default="0",
	 *       description="Booking Status Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="header_text",
	 *       type="string",
	 *       default="",
	 *       description="Booking Request Header Text"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $booking_status = [];

    /**
     * Response Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Response Message"
	 * )
     * // phpcs:enable
     */
    protected $message = '';


    /**
     * Get Booking_status
     *
     * @return object
     */
    public function getBookingStatus()
    {
        return (empty($this->booking_status) === false) ? $this->booking_status : new \stdClass;

    }//end getBookingStatus()


    /**
     * Set Booking status
     *
     * @param array $booking_status Booking status.
     *
     * @return self
     */
    public function setBookingStatus(array $booking_status)
    {
        $this->booking_status = $booking_status;
        return $this;

    }//end setBookingStatus()


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
