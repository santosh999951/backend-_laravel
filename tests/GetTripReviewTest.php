<?php
/**
 * GetTripReviewTest Test containing methods related to Booking Pending Reviews Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use \Carbon\Carbon;

/**
 * Class GetTripReviewTest
 *
 * @group Property
 */
class GetTripReviewTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test for invalid Property hash id .
     *
     * @return void
     */
    public function testBadRequestResponseWithAuthentication()
    {
        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Create fake request hash id.
        $request_hash_id = 'ABCDEF';

        // Get Property review response data.
        $url = $this->getApiVersion().'/trip/review?request_hash_id='.$request_hash_id;

        $response = $this->actingAs($booking_request_data['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test Unauthorized User response.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/trip/review';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test Authorized User response without Request id.
     *
     * @return void
     */
    public function testValidResponseWithoutRequestIdWithAuthentication()
    {
        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/trip/review';
        $response = $this->actingAs($booking_request_data['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithoutRequestIdWithAuthentication()


    /**
     * Test Authorized User response with Request id.
     *
     * @return void
     */
    public function testValidResponseWithRequestIdWithAuthentication()
    {
        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Request Hash Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/trip/review?request_hash_id='.$request_hash_id;
        $response = $this->actingAs($booking_request_data['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithRequestIdWithAuthentication()


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
                'review_text',
                'rating_params' => [
                    '*' => [
                        'id',
                        'title',
                    ],
                ],
                'bookings' => [
                    '*' => [
                        'booking_request_id',
                        'property_section' => [
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
                        'review_pending',
                        'rating_pending'
                    ],
                ]
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
