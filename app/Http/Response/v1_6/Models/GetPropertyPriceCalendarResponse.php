<?php
/**
 * Response Model For Property Price Calendar Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPropertyPriceCalendarResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPropertyPriceCalendarResponse",
 * description="Response Model For Property Price Calendar Api",
 * )
 * // phpcs:enable
 */
class GetPropertyPriceCalendarResponse extends ApiResponse
{

    /**
     * Default Property Inventory
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="default",
	 *   type="object",
	 *   default="{}",
	 *   description="Default Property Inventory",
	 *     @SWG\Property(
	 *       property="currency",
	 *       type="string",
	 *       default="",
	 *       description="Property Currency"
	 *     ),
	 *     @SWG\Property(
	 *       property="price",
	 *       type="string",
	 *       default="",
	 *       description="Property Price"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_available",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Is Available Status in 0,1"
	 *     ),
	 *     @SWG\Property(
	 *       property="available_units",
	 *       type="string",
	 *       default="",
	 *       description="Number of Available Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="min_nights",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Min Nights stay"
	 *     ),
	 *     @SWG\Property(
	 *       property="max_nights",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Max Night stay"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests_per_unit",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Guests Per Unit"
	 *     ),
	 *     @SWG\Property(
	 *       property="currency_plus_symbol",
	 *       type="string",
	 *       default="",
	 *       description="Currency Plus Symbol"
	 *     ),
	 *     @SWG\Property(
	 *       property="extra_guest_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Extra Guest Count"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $default = [];

    /**
     * Exceptional Data of Inventory where Price and Availability not same as default
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="exception",
	 *   type="object",
	 *   default="{}",
	 *   description="Exceptional Data of Inventory where Price and Availability not same as default",
	 *     @SWG\Property(
	 *       property="2019-05-28",
	 *       type="object",
	 *       default="{}",
	 *       description="Property 2019-05-28",
	 *         @SWG\Property(
	 *           property="price",
	 *           type="string",
	 *           default="",
	 *           description="Property Price"
	 *         ),
	 *         @SWG\Property(
	 *           property="is_available",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Is Available"
	 *         ),
	 *         @SWG\Property(
	 *           property="available_units",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Available Units"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $exception = [];


    /**
     * Get Default
     *
     * @return object
     */
    public function getDefault()
    {
        return (empty($this->default) === false) ? $this->default : new \stdClass;

    }//end getDefault()


    /**
     * Set Default
     *
     * @param array $default Default.
     *
     * @return self
     */
    public function setDefault(array $default)
    {
        $this->default = $default;
        return $this;

    }//end setDefault()


    /**
     * Get Exception
     *
     * @return object
     */
    public function getException()
    {
        return (empty($this->exception) === false) ? $this->exception : new \stdClass;

    }//end getException()


    /**
     * Set Exception
     *
     * @param array $exception Exception.
     *
     * @return self
     */
    public function setException(array $exception)
    {
        $this->exception = $exception;
        return $this;

    }//end setException()


}//end class
