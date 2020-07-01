<?php
/**
 * PostPropertyRatingTest Test containing methods related to Save Property Ratings Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use \Carbon\Carbon;

/**
 * Class PostPropertyRatingTest
 *
 * @group Property
 */
class PostPropertyRatingTest extends TestCase
{
    use App\Traits\FactoryHelper;


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

        // Request Hash Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $post_param = [
            'ratings'            => json_encode(['1' => '5', '2' => '5', '3' => '5', '4' => '5', '5' => '5']),
            'request_hash_id'    => $request_hash_id,
            'booking_experience' => 5,
            'property_rating'    => 5,
            'booking_review'     => 'Unit Testing - Nice Experience.',
        ];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/rating';
        $response = $this->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test Data not found for invalid Property hash id .
     *
     * @return void
     */
    public function testNotFoundResponseWithAuthentication()
    {
        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Request Hash Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $post_param = [
            'ratings'            => json_encode(['1' => '5', '2' => '5', '3' => '5', '4' => '5', '5' => '5']),
            'request_hash_id'    => $request_hash_id,
            'booking_experience' => 5,
            'property_rating'    => 5,
            'booking_review'     => 'Unit Testing - Nice Experience.',
        ];

        // Create traveller .
        $traveller = $this->createUsers();

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/rating';
        $response = $this->actingAs($traveller[0])->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(404);

    }//end testNotFoundResponseWithAuthentication()


    /**
     * Test Data not found for invalid request hash id .
     *
     * @return void
     */
    public function testInvalidRequestIdResponseWithAuthentication()
    {
        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Create fake request hash id.
        $request_hash_id = 'ABCDEF';

        $post_param = [
            'ratings'            => json_encode(['1' => '5', '2' => '5', '3' => '5', '4' => '5', '5' => '5']),
            'request_hash_id'    => $request_hash_id,
            'booking_experience' => 5,
            'property_rating'    => 5,
            'booking_review'     => 'Unit Testing - Nice Experience.',
        ];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/rating';
        $response = $this->actingAs($booking_request_data['traveller'])->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testInvalidRequestIdResponseWithAuthentication()


    /**
     * Test Missing Parameter with Authentication.
     *
     * @return void
     */
    public function testMissingParameterWithAuthentication()
    {
        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Request Hash Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $post_param = [
            'request_hash_id' => $request_hash_id,
            'booking_review'  => 'Unit Testing - Nice Experience.',
        ];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/rating';
        $response = $this->actingAs($booking_request_data['traveller'])->post($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testMissingParameterWithAuthentication()


    /**
     * Test Authorized User response with Request id.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Request Hash Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $post_param = [
            'ratings'            => json_encode(['1' => '5', '2' => '5', '3' => '5', '4' => '5', '5' => '5']),
            'request_hash_id'    => $request_hash_id,
            'booking_experience' => 5,
            'property_rating'    => 5,
            'booking_review'     => 'Unit Testing - Nice Experience.',
        ];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/rating';
        $response = $this->actingAs($booking_request_data['traveller'])->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithAuthentication()


    /**
     * Test Already submitted review response with Authentication.
     *
     * @return void
     */
    public function testForbiddenErrorResponseWithAuthentication()
    {
        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Create Traveller Ratings.
        $traveller_ratings = $this->createTravellerRatings($booking_request_data['bookings']);

        // Request Hash Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $post_param = [
            'ratings'            => json_encode(['1' => '5', '2' => '5', '3' => '5', '4' => '5', '5' => '5']),
            'request_hash_id'    => $request_hash_id,
            'booking_experience' => 5,
            'property_rating'    => 5,
            'booking_review'     => 'Unit Testing - Nice Experience.',
        ];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/rating';
        $response = $this->actingAs($booking_request_data['traveller'])->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testForbiddenErrorResponseWithAuthentication()


}//end class
