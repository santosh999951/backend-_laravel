<?php
/**
 * PostPriveManagerBookingCashCollectResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostPriveManagerBookingCashCollectResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostPriveManagerBookingCashCollectResponse",
 * description="PostPriveManagerBookingCashCollectResponse",
 * )
 * // phpcs:enable
 */
class PostPriveManagerBookingCashCollectResponse extends ApiResponse
{

    /**
     * Property Request Hash Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="request_hash_id",
	 *   type="string",
	 *   default="",
	 *   description="Property Request Hash Id"
	 * )
     * // phpcs:enable
     */
    protected $request_hash_id = '';

    /**
     * Property Bank Name
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="bank_name",
	 *   type="string",
	 *   default="",
	 *   description="Property Bank Name"
	 * )
     * // phpcs:enable
     */
    protected $bank_name = '';

    /**
     * Property Account Number
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="account_number",
	 *   type="string",
	 *   default="",
	 *   description="Property Account Number"
	 * )
     * // phpcs:enable
     */
    protected $account_number = '';

    /**
     * Property Ifsc Code
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="ifsc_code",
	 *   type="string",
	 *   default="",
	 *   description="Property Ifsc Code"
	 * )
     * // phpcs:enable
     */
    protected $ifsc_code = '';


    /**
     * Get Request_hash_id
     *
     * @return string
     */
    public function getRequestHashId()
    {
        return $this->request_hash_id;

    }//end getRequestHashId()


    /**
     * Set Request hash id
     *
     * @param string $request_hash_id Request hash id.
     *
     * @return self
     */
    public function setRequestHashId(string $request_hash_id)
    {
        $this->request_hash_id = $request_hash_id;
        return $this;

    }//end setRequestHashId()


    /**
     * Get Bank_name
     *
     * @return string
     */
    public function getBankName()
    {
        return $this->bank_name;

    }//end getBankName()


    /**
     * Set Bank name
     *
     * @param string $bank_name Bank name.
     *
     * @return self
     */
    public function setBankName(string $bank_name)
    {
        $this->bank_name = $bank_name;
        return $this;

    }//end setBankName()


    /**
     * Get Account_number
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->account_number;

    }//end getAccountNumber()


    /**
     * Set Account number
     *
     * @param string $account_number Account number.
     *
     * @return self
     */
    public function setAccountNumber(string $account_number)
    {
        $this->account_number = $account_number;
        return $this;

    }//end setAccountNumber()


    /**
     * Get Ifsc_code
     *
     * @return string
     */
    public function getIfscCode()
    {
        return $this->ifsc_code;

    }//end getIfscCode()


    /**
     * Set Ifsc code
     *
     * @param string $ifsc_code Ifsc code.
     *
     * @return self
     */
    public function setIfscCode(string $ifsc_code)
    {
        $this->ifsc_code = $ifsc_code;
        return $this;

    }//end setIfscCode()


}//end class
