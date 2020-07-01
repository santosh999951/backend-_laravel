<?php
/**
 * GetCurrencycodesResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetCurrencycodesResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetCurrencycodesResponse",
 * description="GetCurrencycodesResponse",
 * )
 * // phpcs:enable
 */
class GetCurrencycodesResponse extends ApiResponse
{

    /**
     * Property Selected Currency
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="selected_currency",
	 *   type="string",
	 *   default="",
	 *   description="Property Selected Currency"
	 * )
     * // phpcs:enable
     */
    protected $selected_currency = '';

    /**
     * Property Currency Codes
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="currency_codes",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Currency Codes",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="code",
	 *       type="string",
	 *       default="",
	 *       description="Property Code"
	 *     ),
	 *     @SWG\Property(
	 *       property="symbol",
	 *       type="string",
	 *       default="",
	 *       description="Property Symbol"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $currency_codes = [];


    /**
     * Get Selected_currency
     *
     * @return string
     */
    public function getSelectedCurrency()
    {
        return $this->selected_currency;

    }//end getSelectedCurrency()


    /**
     * Set Selected currency
     *
     * @param string $selected_currency Selected currency.
     *
     * @return self
     */
    public function setSelectedCurrency(string $selected_currency)
    {
        $this->selected_currency = $selected_currency;
        return $this;

    }//end setSelectedCurrency()


    /**
     * Get Currency_codes
     *
     * @return array
     */
    public function getCurrencyCodes()
    {
        return $this->currency_codes;

    }//end getCurrencyCodes()


    /**
     * Set Currency codes
     *
     * @param array $currency_codes Currency codes.
     *
     * @return self
     */
    public function setCurrencyCodes(array $currency_codes)
    {
        $this->currency_codes = $currency_codes;
        return $this;

    }//end setCurrencyCodes()


}//end class
