<?php
/**
 * GetHomeGraphTest Test containing methods related to Home graph api
 */

use App\Libraries\Helper;
use Carbon\Carbon;

/**
 * Class GetHomeGraphTest
 *
 * @group Owner
 */
class GetHomeGraphTest extends TestCase
{

    use App\Traits\FactoryHelper;


     /**
      * Test for Logout User.
      *
      * @return void
      */
    public function testValidResponseWithoutAuthentication()
    {
        // Get homegraph response data.
        $url      = $this->getApiVersion().'/prive/homegraph';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testValidResponseWithoutAuthentication()


    /**
     * Test for Traveller.
     *
     * @return void
     */
    public function testInvalidResponseForTravellerWithAuthentication()
    {
        $traveller = $this->createUsers();

        // Get Prive homegraph data.
        $url      = $this->getApiVersion().'/prive/homegraph';
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseForTravellerWithAuthentication()


     /**
      * Response due to Invalid Parameters with authenicated user.
      *
      * @test
      * @return void
      */
    public function testInvalidResponseWithInvalidParametersWithAuthentication()
    {
        // Create Prive User.
        $create_prive_user = $this->createUser(['prive_owner' => 1]);

        // Query Parameters with invalid date (month_year_from and to should be in format m-Y).
        $params = [
            'month_year_from' => Carbon::now()->format('d-m-Y'),
            'month_year_to'   => Carbon::now()->format('d-m-Y'),

        ];

        $url = $this->getApiVersion().'/prive/homegraph?'.http_build_query($params);

        $response = $this->actingAs($create_prive_user)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testInvalidResponseWithInvalidParametersWithAuthentication()


    /**
     * Test for Login User.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Booking.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], null, null, [], 3, 1);

        // Get homegraph response data.
        $url      = $this->getApiVersion().'/prive/homegraph';
        $response = $this->actingAs($create_booking['prive_owner'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


    /**
     * Test for Login User.
     *
     * @return void
     */
    public function testValidResponseWithValuesAuthentication()
    {
        // Create Booking Data.
        $create_booking                 = $this->createBookingRequests(BOOKED, ['prive' => 1], null, null, [], 3, 1);
        $properly_commission_percentage = 50;
        $host_fee = 11085.7;

        $total_host_fee = (3 * 5543);
        $currency       = 'INR';
        $start_date     = Carbon::parse($create_booking['booking_request']['from_date']);
        $end_date       = Carbon::parse($create_booking['booking_request']['to_date']);
        $no_of_nights   = '3';
        $param          = [
            'month_year_from' => Carbon::now()->format('m-Y'),
            'month_year_to'   => Carbon::now()->addMonth(3)->format('m-Y'),
        ];
        $from_date      = Carbon::createFromFormat('m-Y', $param['month_year_from'])->startOfMonth();
        $to_date        = Carbon::createFromFormat('m-Y', $param['month_year_to'])->endOfMonth();
        if (Carbon::parse($create_booking['booking_request']['from_date'])->format('m-y') !== Carbon::parse($create_booking['booking_request']['to_date'])->format('m-y')) {
            $booking_month_year = $end_date->format('m-Y');
        } else {
            $booking_month_year = $start_date->format('m-Y');
        }

        // Get homegraph response data.
        $url      = $this->getApiVersion().'/prive/homegraph?'.http_build_query($param);
        $response = $this->actingAs($create_booking['prive_owner'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        //phpcs:disable
        for ($month = $from_date; $month->lte($to_date); $month->addMonth()) {
            $all_months[$month->format('m-Y')] = [
                'month'               => $month->format('m-Y'),
                'total_income'        => Helper::getFormattedMoney(0, DEFAULT_CURRENCY),
                'total_nights_booked' => 0,
            ];
        }

         $all_months[$booking_month_year]['total_income']        = Helper::getFormattedMoney($total_host_fee, 'INR');
         $all_months[$booking_month_year]['total_nights_booked'] = $no_of_nights;

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());
        $this->seeJsonEquals($this->getApiValues($param, array_values($all_months)));

    }//end testValidResponseWithValuesAuthentication()


    /**
     * Helper function to api structure
     *
     * @return array
     */
    private function getApiStructureWithOnlyDefaultValues()
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
        return [
            'status',
            'data' => [
                'graph_data' => [
                    '*' => [
                        'month',
                        'total_income',
                        'total_nights_booked',
                    ],
                ],
                'filter',
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


    /**
     * Helper function to api response
     *
     * @param array   $filter_param          Filter Parameter.
     * @param integer $graph_data     Graph Data.
     * @return array
     */
    private function getApiValues(array $filter_param, array $graph_data)
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
        return [
            'status' => true,
            'data'   => [
                'graph_data' => $graph_data,
                'filter'     => [
                    'month_year_from' => $filter_param['month_year_from'],
                    'month_year_to'   => $filter_param['month_year_to'],
                ],
            ],
            'error'  => [],
        ];

    }//end getApiValues()


}//end class
