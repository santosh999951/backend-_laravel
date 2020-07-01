<?php
/**
 * GetPriveInvoiceResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPriveInvoiceResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPriveInvoiceResponse",
 * description="GetPriveInvoiceResponse",
 * )
 * // phpcs:enable
 */
class GetPriveInvoiceResponse extends ApiResponse
{

    /**
     * Invoice
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="invoice",
	 *   type="object",
	 *   default="{}",
	 *   description="Invoice",
	 *     @SWG\Property(
	 *       property="total_amount",
	 *       type="string",
	 *       default="",
	 *       description="Property Total Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="invoice",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Invoice",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="request_id",
	 *           type="string",
	 *           default="",
	 *           description="Property Request Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="guest_name",
	 *           type="string",
	 *           default="",
	 *           description="Property Guest Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="guests",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Guests"
	 *         ),
	 *         @SWG\Property(
	 *           property="invoice_date",
	 *           type="string",
	 *           default="",
	 *           description="Property Invoice Date"
	 *         ),
	 *         @SWG\Property(
	 *           property="currency",
	 *           type="string",
	 *           default="",
	 *           description="Property Currency"
	 *         ),
	 *         @SWG\Property(
	 *           property="host_amount",
	 *           type="string",
	 *           default="",
	 *           description="Property Host Amount"
	 *         ),
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Property Title"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $invoice = [];

    /**
     * Filter
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="filter",
	 *   type="object",
	 *   default="{}",
	 *   description="Filter",
	 *     @SWG\Property(
	 *       property="month_year",
	 *       type="string",
	 *       default="",
	 *       description="Month Year"
	 *     ),
	 *     @SWG\Property(
	 *       property="properties",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Properties",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="property_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Property Property Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Property Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Selected"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $filter = [];

    /**
     * Total
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="total",
	 *   type="integer",
	 *   default="0",
	 *   description="Total"
	 * )
     * // phpcs:enable
     */
    protected $total = 0;

    /**
     * Month Year List
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="month_year_list",
	 *   type="object",
	 *   default="{}",
	 *   description="Month Year List",
	 *     @SWG\Property(
	 *       property="start_month_year",
	 *       type="string",
	 *       default="",
	 *       description="Start Month Year"
	 *     ),
	 *     @SWG\Property(
	 *       property="end_month_year",
	 *       type="string",
	 *       default="",
	 *       description="End Month Year"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $month_year_list = [];


    /**
     * Get Invoice
     *
     * @return object
     */
    public function getInvoice()
    {
        return (empty($this->invoice) === false) ? $this->invoice : new \stdClass;

    }//end getInvoice()


    /**
     * Set Invoice
     *
     * @param array $invoice Invoice.
     *
     * @return self
     */
    public function setInvoice(array $invoice)
    {
        $this->invoice = $invoice;
        return $this;

    }//end setInvoice()


    /**
     * Get Filter
     *
     * @return object
     */
    public function getFilter()
    {
        return (empty($this->filter) === false) ? $this->filter : new \stdClass;

    }//end getFilter()


    /**
     * Set Filter
     *
     * @param array $filter Filter.
     *
     * @return self
     */
    public function setFilter(array $filter)
    {
        $this->filter = $filter;
        return $this;

    }//end setFilter()


    /**
     * Get Total
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;

    }//end getTotal()


    /**
     * Set Total
     *
     * @param integer $total Total.
     *
     * @return self
     */
    public function setTotal(int $total)
    {
        $this->total = $total;
        return $this;

    }//end setTotal()


    /**
     * Get Month_year_list
     *
     * @return object
     */
    public function getMonthYearList()
    {
        return (empty($this->month_year_list) === false) ? $this->month_year_list : new \stdClass;

    }//end getMonthYearList()


    /**
     * Set Month year list
     *
     * @param array $month_year_list Month year list.
     *
     * @return self
     */
    public function setMonthYearList(array $month_year_list)
    {
        $this->month_year_list = $month_year_list;
        return $this;

    }//end setMonthYearList()


}//end class
