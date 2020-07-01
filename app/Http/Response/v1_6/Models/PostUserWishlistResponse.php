<?php
/**
 * Response Model for add Property to User Wishlist
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostUserWishlistResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostUserWishlistResponse",
 * description="Response Model for add Property to User Wishlist",
 * )
 * // phpcs:enable
 */
class PostUserWishlistResponse extends ApiResponse
{

    /**
     * Property Hash Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_hash_id",
	 *   type="string",
	 *   default="",
	 *   description="Property Hash Id"
	 * )
     * // phpcs:enable
     */
    protected $property_hash_id = '';

    /**
     * Property Is Already In Wishlist
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_already_in_wishlist",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Is Already In Wishlist"
	 * )
     * // phpcs:enable
     */
    protected $is_already_in_wishlist = 0;

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
     * Get Property_hash_id
     *
     * @return string
     */
    public function getPropertyHashId()
    {
        return $this->property_hash_id;

    }//end getPropertyHashId()


    /**
     * Set Property hash id
     *
     * @param string $property_hash_id Property hash id.
     *
     * @return self
     */
    public function setPropertyHashId(string $property_hash_id)
    {
        $this->property_hash_id = $property_hash_id;
        return $this;

    }//end setPropertyHashId()


    /**
     * Get Is_already_in_wishlist
     *
     * @return integer
     */
    public function getIsAlreadyInWishlist()
    {
        return $this->is_already_in_wishlist;

    }//end getIsAlreadyInWishlist()


    /**
     * Set Is already in wishlist
     *
     * @param integer $is_already_in_wishlist Is already in wishlist.
     *
     * @return self
     */
    public function setIsAlreadyInWishlist(int $is_already_in_wishlist)
    {
        $this->is_already_in_wishlist = $is_already_in_wishlist;
        return $this;

    }//end setIsAlreadyInWishlist()


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
