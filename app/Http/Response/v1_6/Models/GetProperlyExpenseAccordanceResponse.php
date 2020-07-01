<?php
/**
 * GetProperlyExpenseAccordanceResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetProperlyExpenseAccordanceResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetProperlyExpenseAccordanceResponse",
 * description="GetProperlyExpenseAccordanceResponse",
 * )
 * // phpcs:enable
 */
class GetProperlyExpenseAccordanceResponse extends ApiResponse
{

    /**
     * Accordance
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="accordance",
	 *   type="array",
	 *   default="[]",
	 *   description="Accordance",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="month",
	 *       type="string",
	 *       default="",
	 *       description="Month"
	 *     ),
	 *     @SWG\Property(
	 *       property="formatted_month",
	 *       type="string",
	 *       default="",
	 *       description="Formatted Month"
	 *     ),
	 *     @SWG\Property(
	 *       property="pl",
	 *       type="integer",
	 *       default="0",
	 *       description="Pl"
	 *     ),
	 *     @SWG\Property(
	 *       property="pl_formatted",
	 *       type="string",
	 *       default="",
	 *       description="Pl Formatted"
	 *     ),
	 *     @SWG\Property(
	 *       property="percentage",
	 *       type="string",
	 *       default="",
	 *       description="Percentage"
	 *     ),
	 *     @SWG\Property(
	 *       property="type",
	 *       type="string",
	 *       default="",
	 *       description="Type"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $accordance = [];

    /**
     * Accordance Month
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="accordance_month",
	 *   type="string",
	 *   default="",
	 *   description="Accordance Month"
	 * )
     * // phpcs:enable
     */
    protected $accordance_month = '';

    /**
     * Property Title
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_title",
	 *   type="string",
	 *   default="",
	 *   description="Property Title"
	 * )
     * // phpcs:enable
     */
    protected $property_title = '';


    /**
     * Get Accordance
     *
     * @return array
     */
    public function getAccordance()
    {
        return $this->accordance;

    }//end getAccordance()


    /**
     * Set Accordance
     *
     * @param array $accordance Accordance.
     *
     * @return self
     */
    public function setAccordance(array $accordance)
    {
        $this->accordance = $accordance;
        return $this;

    }//end setAccordance()


    /**
     * Get Accordance_month
     *
     * @return string
     */
    public function getAccordanceMonth()
    {
        return $this->accordance_month;

    }//end getAccordanceMonth()


    /**
     * Set Accordance month
     *
     * @param string $accordance_month Accordance month.
     *
     * @return self
     */
    public function setAccordanceMonth(string $accordance_month)
    {
        $this->accordance_month = $accordance_month;
        return $this;

    }//end setAccordanceMonth()


    /**
     * Get Property_title
     *
     * @return string
     */
    public function getPropertyTitle()
    {
        return $this->property_title;

    }//end getPropertyTitle()


    /**
     * Set Property title
     *
     * @param string $property_title Property title.
     *
     * @return self
     */
    public function setPropertyTitle(string $property_title)
    {
        $this->property_title = $property_title;
        return $this;

    }//end setPropertyTitle()


}//end class
