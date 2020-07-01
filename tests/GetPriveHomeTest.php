<?php
/**
 * GetHomeTest Test containing methods related to Home api
 */

use App\Libraries\Helper;
use Carbon\Carbon;

/**
 * Class GetHomeTest
 *
 * @group Owner
 */
class GetPriveHomeTest extends TestCase
{

    use App\Traits\FactoryHelper;


     /**
      * Test for Logout User.
      *
      * @return void
      */
    public function testValidResponseWithoutAuthentication()
    {
        // Get Home response data.
        $url      = $this->getApiVersion().'/prive/home';
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

        // Get Prive Home Response data.
        $url      = $this->getApiVersion().'/prive/home';
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Test for Login User.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Booking.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], null, null, [], 2, 1);

        // Get Home response data.
        $url      = $this->getApiVersion().'/prive/home';
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
        $param = [
            'from_date' => Carbon::now()->format('Y-m-d'),
            'to_date'   => Carbon::now()->addDays(1)->format('Y-m-d'),
        ];

        // Create Booking Request.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], null, null, [], 2, 1);

        $host_fee           = 11085.7;
        $booking_month_year = Carbon::parse($param['from_date'])->format('m-Y');
        $properly_commission_percentage = 50;
        $total_booked_nights            = 2;
        $total_income                   = (2 * 5543);
        $properties_count               = [
            'total'               => 2,
            'active_properties'   => 2,
            'inactive_properties' => 0,
        ];
        $start_of_year                  = Carbon::parse(Carbon::now()->startOfYear()->format('Y-m-d'));
        $end_of_year                    = Carbon::parse(Carbon::now()->endOfYear()->format('Y-m-d'));
         //phpcs:disable
        for ($month = $start_of_year; $month->lte($end_of_year); $month->addMonth()) {
            $all_months[$month->format('m-Y')] = [
                'month'               => $month->format('m-Y'),
                'total_income'        => Helper::getFormattedMoney(0, DEFAULT_CURRENCY),
                'total_nights_booked' => 0,
            ];
        } 
         $all_months[$booking_month_year]['total_income']        = Helper::getFormattedMoney($total_income, 'INR');
         $all_months[$booking_month_year]['total_nights_booked'] = '2';

        foreach ($create_booking['booking_request_list'] as $key => $value) {
            $booking['request_hash_id'] = Helper::encodeBookingRequestId($value['id']);
            $booking['guest_name']      = 'Unit Testing'.' '.'Api';
            $booking['guests']          = 1;
            $booking['amount']          = Helper::getFormattedMoney(5543, 'INR');
            $booking['booking_status']  = [
                'text'   => 'Booked',
                'class'  => 'booked',
                'status' => 1,
            ];
            $booking['checkin']         = carbon::parse($value['from_date'])->format('dS M Y');
            $booking['checkout']        = carbon::parse($value['to_date'])->format('dS M Y');
            $booking['title']           = 'Vila';
            $booking['units']           = 1;
            $booking['room']            = 1;
            $booking_data[]             = $booking;
        }

        $property_listings = $create_booking['property_list'];
        foreach ($property_listings as $key => $one_property) {
            $property_hash_id                    = Helper::encodePropertyId($one_property['id']);
            $properties_data['property_hash_id'] = $property_hash_id;
            $properties_data['properties_images'][] = [
                'image'=>'https://d39vbwyctxz5qa.cloudfront.net/assets/images/no_property.png',
                'caption' => '',

            ];
            $properties_data['property_title']    = 'Vila';
            $properties_data['url']               = MSITE_URL.'/property/'.$property_hash_id;
            $properties_data['units']             = 1;
            $properties_data['city']              = 'Vagator';
            $properties_data['state']             = 'Goa';
            $properties_data['per_night_price']   = Helper::getFormattedMoney(13042, 'INR');
            $property_list[] = $properties_data;
        }
        $response_data   = [
            'total_booked_nights' => $total_booked_nights,
            'total_income'        => $total_income,
            'properties_count'    => $properties_count,
            'graph_data'          => $all_months,
            'upcoming_bookings'   => $booking_data,
            'properties'          => $property_list,
        ];

        // Get Home response data.
        $url      = $this->getApiVersion().'/prive/home';
        $response = $this->actingAs($create_booking['prive_owner'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());
        $this->getApiValues($response_data);

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
                'total_booked_nights',
                'total_income',
                'properties_count',
                'graph_data' => [
                    '*' => [
                        'month',
                        'total_income',
                        'total_nights_booked',
                    ],

                ],
                'upcoming_bookings' => [
                    '*' => [
                        'request_hash_id',
                        'guest_name',
                        'guests',
                        'amount',
                        'booking_status',
                        'checkin',
                        'checkout',
                        'title',
                        'units',
                        'room',
                    ],

                ],
                'properties' => [
                    '*' => [
                        'property_hash_id',
                        'properties_images',
                        'property_title',
                        'url',
                        'units',
                        'city',
                        'state',
                        'per_night_price',
                    ],

                ] ,

            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


     /**
      * Helper function to api response
      *
      * @param array $response_data Response Data.
      *
      * @return array
      */
    private function getApiValues(array $response_data)
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
        return [
            'status' => true,
            'data'   => $response_data,
            'error'  => [],
        ];

    }//end getApiValues()


}//end class
