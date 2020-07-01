<?php
/**
 * GetPriveHomeGraphResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPriveHomeGraphResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPriveHomeGraphResponse",
 * description="GetPriveHomeGraphResponse",
 * )
 * // phpcs:enable
 */
class GetPriveHomeGraphResponse extends ApiResponse
{

    /**
     * Graph Data
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="graph_data",
	 *   type="array",
	 *   default="[]",
	 *   description="Graph Data",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="month",
	 *       type="string",
	 *       default="",
	 *       description="Month"
	 *     ),
	 *     @SWG\Property(
	 *       property="total_income",
	 *       type="string",
	 *       default="",
	 *       description="Total Income"
	 *     ),
	 *     @SWG\Property(
	 *       property="total_nights_booked",
	 *       type="string",
	 *       default="",
	 *       description="Total Nights Booked"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $graph_data = [];

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
	 *       property="month_year_from",
	 *       type="string",
	 *       default="",
	 *       description="Month Year From"
	 *     ),
	 *     @SWG\Property(
	 *       property="month_year_to",
	 *       type="string",
	 *       default="",
	 *       description="Month Year To"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $filter = [];


    /**
     * Get Graph_data
     *
     * @return array
     */
    public function getGraphData()
    {
        return $this->graph_data;

    }//end getGraphData()


    /**
     * Set Graph data
     *
     * @param array $graph_data Graph data.
     *
     * @return self
     */
    public function setGraphData(array $graph_data)
    {
        $this->graph_data = $graph_data;
        return $this;

    }//end setGraphData()


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


}//end class
