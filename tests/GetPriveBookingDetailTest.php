<?php
/**
 * GetPriveBookingDetailTest Test containing methods related to Prive Booking Detail api
 */

use App\Libraries\Helper;
use Carbon\Carbon;

/**
 * Class GetPriveBookingDetailTest
 *
 * @group Owner
 */
class GetPriveBookingDetailTest extends TestCase
{

    use App\Traits\FactoryHelper;


     /**
      * Test for Logout User.
      *
      * @return void
      */
    public function testValidResponseWithoutAuthentication()
    {
        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], null, null, [], 1, 1);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Get Prive Booking Detail response data.
        $url      = $this->getApiVersion().'/prive/booking/'.$request_hash_id;
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
         // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], null, null, [], 1, 1);

          // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Get Prive Booking Detail response data.
        $url      = $this->getApiVersion().'/prive/booking/'.$request_hash_id;
        $response = $this->actingAs($create_booking['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Response due to Invalid Parameters with authenicated user.
     *
     * @return void
     */
    public function testInvalidResponseWithInvalidParametersWithAuthentication()
    {
        // Create Booking.
        $create_prive_user = $this->createUser(['prive_owner' => 1]);

        $request_hash_id = 'ABCDEF';

        $url = $this->getApiVersion().'/prive/booking/'.$request_hash_id;

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
        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], null, null, [], 1, 1);

          // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Get Prive Booking Detail response data.
        $url      = $this->getApiVersion().'/prive/booking/'.$request_hash_id;
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
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], null, null, [], 1, 1);

        // Booking Request Data.
        $booking = $create_booking['booking_request'];

        // Static Properly %.
        $properly_commission_percentage = 50;

        $markup_service_fee = 0;
        $service_fee        = 2302;

         // Calculate GH Commission from Host.
        $gh_commission_from_host = 0;

        $ota_fees = ($markup_service_fee + $service_fee + $gh_commission_from_host);

        $total_discount = 0;

         // Calculate Amount.
        $host_amount         = 11085.7;
        $properly_commission = 5543;
        $net_prive_amount    = ($host_amount - $properly_commission);

        $gst_component = 3870.49;

        // Get Booking Info.
        $booking_info = [
            'property_name'  => 'Vila',
            'guests'         => 1,
            'checkin'        => Carbon::parse($booking['from_date'])->format('dS M Y'),
            'checkout'       => Carbon::parse($booking['to_date'])->format('dS M Y'),
            'units'          => 1,
            'room'           => 1,
            //phpcs:ignore
            'guest_name'    => 'Unit Testing'.' '.'Api',
            'host_fee'       => '11085.7',
            'extra_guest'    => 0,
            'extra_services' => [],
        ];
        $booking_info['properties_images'][] = [
            'image'   => 'https://d39vbwyctxz5qa.cloudfront.net/assets/images/no_property.png',
            'caption' => '',
        ];

        $invoice = [
            'invoice_header' => [
                [
                    'key'   => 'Booking Amount',
                    'value' => Helper::getFormattedMoney(11085.7, 'INR'),
                    'show'  => 1,
                ],
            ],
            'invoice_middle' => [
                [
                    'key'   => 'Platform Fees',
                    'value' => '+'.Helper::getFormattedMoney($ota_fees, 'INR'),
                    'show'  => ($ota_fees > 0) ? 1 : 0,
                ],
                [
                    'key'   => 'GST',
                    'value' => '+'.Helper::getFormattedMoney($gst_component, 'INR'),
                    'show'  => ($gst_component > 0) ? 1 : 0,
                ],
                [
                    'key'   => 'Effective Discount',
                    'value' => '-'.Helper::getFormattedMoney($total_discount, 'INR'),
                    'show'  => ($total_discount > 0) ? 1 : 0,
                ],
                [
                    'key'   => 'Properly Commission',
                    'value' => '-'.Helper::getFormattedMoney($properly_commission, 'INR'),
                    'show'  => ($properly_commission > 0) ? 1 : 0,
                ],

            ],
            'invoice_footer' => [
                [
                    'key'       => 'Net Amount Paid',
                    'value'     => Helper::getFormattedMoney($net_prive_amount, 'INR'),
                    'raw_value' => $net_prive_amount,
                    'color'     => '#2a4469',
                    'show'      => 1,
                ],
            ],

        ];
        $content = [
            'booking_info' => $booking_info,
            'invoice'      => $invoice,

        ];

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Get Prive Booking Detail response data.
        $url      = $this->getApiVersion().'/prive/booking/'.$request_hash_id;
        $response = $this->actingAs($create_booking['prive_owner'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        $this->seeJsonEquals($this->getApiValues($content));

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
                'booking_info',
                'invoice'       => [
                    'invoice_header' => [
                        '*' => [
                            'key',
                            'value',
                            'show',
                        ],
                    ],
                    'invoice_middle' => [
                        '*' => [
                            'key',
                            'value',
                            'show',
                        ],
                    ],
                    'invoice_footer' => [
                        '*' => [
                            'key',
                            'value',
                            'raw_value',
                            'color',
                            'show',
                        ],
                    ],
                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


    /**
     * Helper function to api response
     *
     * @param array $content Result Data.
     *
     * @return array
     */
    private function getApiValues(array $content)
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
        return [
            'status' => true,
            'data'   => $content,
            'error'  => [],
        ];

    }//end getApiValues()


}//end class
