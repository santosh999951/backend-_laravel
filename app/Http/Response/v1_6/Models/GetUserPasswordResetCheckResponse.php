<?php
/**
 * Response Model for User Password Reset Check BY token API
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetUserPasswordResetCheckResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetUserPasswordResetCheckResponse",
 * description="Response Model for User Password Reset Check BY token API",
 * )
 * // phpcs:enable
 */
class GetUserPasswordResetCheckResponse extends ApiResponse
{

    /**
     * Is Valid User for Update Password
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_valid",
	 *   type="integer",
	 *   default="0",
	 *   description="Is Valid User for Update Password"
	 * )
     * // phpcs:enable
     */
    protected $is_valid = 0;


    /**
     * Get Is_valid
     *
     * @return integer
     */
    public function getIsValid()
    {
        return $this->is_valid;

    }//end getIsValid()


    /**
     * Set Is valid
     *
     * @param integer $is_valid Is valid.
     *
     * @return self
     */
    public function setIsValid(int $is_valid)
    {
        $this->is_valid = $is_valid;
        return $this;

    }//end setIsValid()


}//end class
