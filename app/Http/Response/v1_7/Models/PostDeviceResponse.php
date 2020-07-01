<?php

/**
 * PostDeviceResponse
 */

namespace App\Http\Response\v1_7\Models;

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
	 * Get Message
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}


	/**
	 * Set Message
	 *
	 * @param string $message Message.
	 *
	 * @return self
	 */
	public function setMessage(string $message)
	{
		$this->message= $message;
		return $this;
	}
}
