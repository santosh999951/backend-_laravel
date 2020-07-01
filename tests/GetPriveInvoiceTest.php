<?php
/**
 * GetPriveInvoiceTest Test containing methods related to Invoice api
 */

use App\Libraries\Helper;
use Carbon\Carbon;

/**
 * Class GetPriveInvoiceTest
 *
 * @group Owner
 */
class GetPriveInvoiceTest extends TestCase
{
   //phpcs:disable

    use App\Traits\FactoryHelper;


     /**
      * Test for Logout User.
      *
      * @return void
      */
    public function testValidResponseWithoutAuthentication()
    {
        // Get Prive Invoice response data.
        $url      = $this->getApiVersion().'/prive/invoice';
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

        // Get Prive Invoice response data.
        $url      = $this->getApiVersion().'/prive/invoice';
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

        // Query Parameters with invalid date (end date should be greater than start_date).
        $params = [
            'month_year' => Carbon::now()->format('d-m-Y'),
        ];

        $url = $this->getApiVersion().'/prive/invoice?'.http_build_query($params);

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

        // Get Prive Invoice response data.
        $url      = $this->getApiVersion().'/prive/invoice';
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
    public function testValidResponseValuesWithAuthentication()
    {
        // Creating 3 Booking.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], null, null, [], 3, 1);

                // Properly Percentage.
        $properly_commission_percentage = 50;
        $invoice  = [];
        $host_fee = 11085.7;

        $host_fee_booking = 5543;

        // Total 3 booking So total host fee will be 3 times of host_fee_booking.
        $total_host_fee = (3 * $host_fee_booking);
        foreach ($create_booking['booking_request_list'] as $key => $value) {
            $invoice['request_id'] = Helper::encodeBookingRequestId($value['id']);
            //phpcs:ignore
            $invoice['guest_name']   = 'Unit Testing'.' '.'Api';
            $invoice['guests']       = 1;
            $invoice['invoice_date'] = carbon::parse($value['to_date'])->format('dS M Y');
            $invoice['currency']     = 'INR';
            $invoice['host_amount']  = Helper::getFormattedMoney($host_fee_booking, 'INR');
            $invoice['title']        = 'Vila';
            $invoice_data[]          = $invoice;
            $month_year_filter       = $value['to_date'];
        }

        $filter     = Carbon::parse($month_year_filter)->format('m-y');
        $properties = $create_booking['property_list'];
        foreach ($properties as $key => $value) {
            $properties_data['selected']         = 0;
            $properties_data['title']            = $value['id'].' • Vila';
            $property_hash_id                    = Helper::encodePropertyId($value['id']);
            $properties_data['property_hash_id'] = $property_hash_id;
            $property_list[] = $properties_data;

        }

        $data            = [
            'invoice'      => $invoice_data,
            'total_amount' => Helper::getFormattedMoney($total_host_fee, 'INR'),
        ];
        $month_year_list = [
            'start_month_year' => '2019-01',
            'end_month_year'   => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('Y-m'),
        ];

        $filters = [
            'month_year' => $filter,
            'properties' => $property_list,
        ];

        // Get Prive Invoice response data.
        $url      = $this->getApiVersion().'/prive/invoice?month_year='.$filter;
        $response = $this->actingAs($create_booking['prive_owner'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(200);

        // Check response structure.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Check Response values.
        $this->seeJsonEquals($this->checkJsonArray($data, $filters, $month_year_list));

    }//end testValidResponseValuesWithAuthentication()


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
                'invoice' => [
                    'total_amount',
                    'invoice' => [
                        '*' => [
                            'request_id',
                            'guest_name',
                            'guests',
                            'invoice_date',
                            'currency',
                            'host_amount',
                            'title',
                        ],
                    ],
                ],
                'filter',
                'total',
                'month_year_list'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


    /**
     * Helper function to check api response
     *
     * @param array $data            Data.
     * @param array $filter          Filter data.
     * @param array $month_year_list Month Year List.
     *
     * @return array
     */
    private function checkJsonArray(array $data, array $filter, array $month_year_list)
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
         return [
             'status' => true,
             'data'   => [
                 'invoice'         => $data,
                 'filter'          => $filter,
                 'total'           => count($data['invoice']),
                 'month_year_list' => $month_year_list,
             ],
             'error'  => [],
         ];

    }//end checkJsonArray()


}//end class
