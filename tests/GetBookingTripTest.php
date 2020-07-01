<?php
/**
 * GetBookingTrip Test containing methods related to Trip List Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class GetBookingTripTest
 *
 * @group Trip
 */
class GetBookingTripTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test with logout user.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        $url      = $this->getApiVersion().'/booking/trip';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test Login User response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Trip..
        $booking_request_data = $this->createBookingRequests(BOOKED);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $url      = $this->getApiVersion().'/booking/trip';
        $response = $this->actingAs($booking_request_data['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Check request exist in reponse.
        $content = json_decode($this->response->getContent(), true);

        // Verify Request hash id.
        if (empty($content['data']['trips']) === false) {
            $this->assertEquals($request_hash_id, $content['data']['trips'][0]['request_hash_id']);
        } else {
            $this->assertTrue(false);
        }

    }//end testValidResponseWithAuthentication()


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
                'trips' => [
                    '*' => [
                        'request_id',
                        'request_hash_id',
                        'property_tile' => [
                            'property_id',
                            'property_hash_id',
                            'property_type',
                            'room_type',
                            'property_score',
                            'host_name',
                            'host_image',
                            'location' => [
                                'area',
                                'city',
                                'state',
                                'country' => [
                                    'name',
                                    'ccode',
                                ],
                                'location_name',
                                'latitude',
                                'longitude'
                            ],
                            'title',
                            'property_title',
                            'property_images' => [
                                '*' => [
                                    'image',
                                    'caption',
                                ],
                            ],
                            'url'
                        ],
                        'timeline_status',
                        'booking_status' => [
                            'text',
                            'class',
                            'status',
                            'header_text',
                        ],
                        'trip_status',
                        'checkin_checkout',
                        'checkin',
                        'guests'
                    ],

                ],

                'past_trip_count',
                'total_trip_count',
                'updated_offset',
                'limit'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
