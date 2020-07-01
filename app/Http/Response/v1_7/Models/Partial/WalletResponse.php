<?php

/**
 * WalletResponse
 */

namespace App\Http\Response\v1_7\Models\Partial;

use App\Http\Response\v1_7\Models\ApiResponse;

/**
 * Class WalletResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="WalletResponse",
 * description="WalletResponse",
 * )
 * // phpcs:enable
 */
class WalletResponse extends ApiResponse
{
	/**
	 * Property Balance
	 *
	 * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="balance",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Balance"
	 * )
	 * // phpcs:enable
	 */
	protected $balance = 0;

	/**
	 * Property Currency
	 *
	 * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="currency",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Currency",
	 *     @SWG\Property(
	 *       property="webicon",
	 *       type="string",
	 *       default="",
	 *       description="Property Webicon"
	 *     ),
	 *     @SWG\Property(
	 *       property="non-webicon",
	 *       type="string",
	 *       default="",
	 *       description="Property Non-webicon"
	 *     ),
	 *     @SWG\Property(
	 *       property="iso_code",
	 *       type="string",
	 *       default="",
	 *       description="Property Iso Code"
	 *     )
	 * )
	 * // phpcs:enable
	 */
	protected $currency = [];


	/**
	 * Get Balance
	 *
	 * @return integer
	 */
	public function getBalance()
	{
		return $this->balance;
	}


	/**
	 * Set Balance
	 *
	 * @param int $balance Balance.
	 *
	 * @return self
	 */
	public function setBalance(int $balance)
	{
		$this->balance= $balance;
		return $this;
	}


	/**
	 * Get Currency
	 *
	 * @return object
	 */
	public function getCurrency()
	{
		return (empty($this->currency) === false) ? $this->currency : new \stdClass;
	}


	/**
	 * Set Currency
	 *
	 * @param array $currency Currency.
	 *
	 * @return self
	 */
	public function setCurrency(array $currency)
	{
		$this->currency= $currency;
		return $this;
	}
}
