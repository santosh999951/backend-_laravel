<?php
/**
 * GetUserWalletResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetUserWalletResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetUserWalletResponse",
 * description="GetUserWalletResponse",
 * )
 * // phpcs:enable
 */
class GetUserWalletResponse extends ApiResponse
{

    /**
     * Property Wallet
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="wallet",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Wallet",
	 *     @SWG\Property(
	 *       property="balance",
	 *       type="string",
	 *       default="",
	 *       description="Property Balance"
	 *     ),
	 *     @SWG\Property(
	 *       property="currency",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Currency",
	 *         @SWG\Property(
	 *           property="webicon",
	 *           type="string",
	 *           default="",
	 *           description="Property Webicon"
	 *         ),
	 *         @SWG\Property(
	 *           property="non-webicon",
	 *           type="string",
	 *           default="",
	 *           description="Property Non-webicon"
	 *         ),
	 *         @SWG\Property(
	 *           property="iso_code",
	 *           type="string",
	 *           default="",
	 *           description="Property Iso Code"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="headline",
	 *       type="string",
	 *       default="",
	 *       description="Property Headline"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $wallet = [];

    /**
     * Property Earn More
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="earn_more",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Earn More",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="image",
	 *       type="string",
	 *       default="",
	 *       description="Property Image"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="description",
	 *       type="string",
	 *       default="",
	 *       description="Property Description"
	 *     ),
	 *     @SWG\Property(
	 *       property="button",
	 *       type="string",
	 *       default="",
	 *       description="Property Button"
	 *     ),
	 *     @SWG\Property(
	 *       property="event",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Event"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $earn_more = [];

    /**
     * Property Can Invite
     *
     * @var boolean
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="can_invite",
	 *   type="boolean",
	 *   default="false",
	 *   description="Property Can Invite"
	 * )
     * // phpcs:enable
     */
    protected $can_invite = false;

    /**
     * Property Transactions
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="transactions",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Transactions",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="type",
	 *       type="string",
	 *       default="",
	 *       description="Property Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="currency",
	 *       type="string",
	 *       default="",
	 *       description="Property Currency"
	 *     ),
	 *     @SWG\Property(
	 *       property="amount",
	 *       type="string",
	 *       default="",
	 *       description="Property Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="review_details",
	 *       type="string",
	 *       default="",
	 *       description="Property Review Details"
	 *     ),
	 *     @SWG\Property(
	 *       property="expire_on",
	 *       type="string",
	 *       default="",
	 *       description="Property Expire On"
	 *     ),
	 *     @SWG\Property(
	 *       property="description",
	 *       type="string",
	 *       default="",
	 *       description="Property Description"
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Booking Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="created_on",
	 *       type="string",
	 *       default="",
	 *       description="Property Created On"
	 *     ),
	 *     @SWG\Property(
	 *       property="currency_symbol",
	 *       type="string",
	 *       default="",
	 *       description="Property Currency Symbol"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $transactions = [];


    /**
     * Get Wallet
     *
     * @return object
     */
    public function getWallet()
    {
        return (empty($this->wallet) === false) ? $this->wallet : new \stdClass;

    }//end getWallet()


    /**
     * Set Wallet
     *
     * @param array $wallet Wallet.
     *
     * @return self
     */
    public function setWallet(array $wallet)
    {
        $this->wallet = $wallet;
        return $this;

    }//end setWallet()


    /**
     * Get Earn_more
     *
     * @return array
     */
    public function getEarnMore()
    {
        return $this->earn_more;

    }//end getEarnMore()


    /**
     * Set Earn more
     *
     * @param array $earn_more Earn more.
     *
     * @return self
     */
    public function setEarnMore(array $earn_more)
    {
        $this->earn_more = $earn_more;
        return $this;

    }//end setEarnMore()


    /**
     * Get Can_invite
     *
     * @return boolean
     */
    public function getCanInvite()
    {
        return $this->can_invite;

    }//end getCanInvite()


    /**
     * Set Can invite
     *
     * @param boolean $can_invite Can invite.
     *
     * @return self
     */
    public function setCanInvite(bool $can_invite)
    {
        $this->can_invite = $can_invite;
        return $this;

    }//end setCanInvite()


    /**
     * Get Transactions
     *
     * @return array
     */
    public function getTransactions()
    {
        return $this->transactions;

    }//end getTransactions()


    /**
     * Set Transactions
     *
     * @param array $transactions Transactions.
     *
     * @return self
     */
    public function setTransactions(array $transactions)
    {
        $this->transactions = $transactions;
        return $this;

    }//end setTransactions()


}//end class
