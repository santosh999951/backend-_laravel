<?php
/**
 * GetBookingRequestTest Test containing methods related to Booking Request  List Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class GetBookingRequestTest
 *
 * @group Request
 */
class GetBookingRequestTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test with logout user.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        $url      = $this->getApiVersion().'/booking/request';
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
        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $url      = $this->getApiVersion().'/booking/request';
        $response = $this->actingAs($booking_request_data['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Check request exist in reponse.
        $content = json_decode($this->response->getContent(), true);

        if (empty($content['data']['requests']) === false) {
            $this->assertEquals($request_hash_id, $content['data']['requests'][0]['request_hash_id']);
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
                'requests' => [
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
                        'booking_amount',
                        'booking_amount_unformatted',
                        'booking_status' => [
                            'text',
                            'class',
                            'color_code',
                            'status',
                            'header_text',
                        ],
                        'created_at'
                    ],

                ],

                'archived_request_count',
                'active_request_count',
                'updated_offset',
                'limit'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
