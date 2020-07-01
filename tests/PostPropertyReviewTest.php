<?php
/**
 * PostPropertyReviewTest Test containing methods related to Save Property Review Test case
 */

use App\Libraries\Helper;
use \Carbon\Carbon;

/**
 * Class PostPropertyReviewTest
 *
 * @group Property
 */
class PostPropertyReviewTest extends TestCase
{
    use App\Traits\FactoryHelper;


     /**
      * Setup.
      *
      * @return void
      */
    public function setup(): void
    {
        parent::setup();
        // Mocking common queue not stop not live sending email,notification,sms.
        $this->mocked_service = $this->mock('alias:App\Libraries\CommonQueue');

    }//end setup()


     /**
      * Tear down.
      *
      * @return void
      */
    public function tearDown(): void
    {
        $this->clearmock();

    }//end tearDown()


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
            // phpcs:ignore
            'review'          => 'Unit Testing - The interior of this property is remarkable! It is clean, furnished and well taken care of and the location makes all the major markets accessible. The host is fluent in English and French, quite a relief for all the foreign tourists. There are restaurants and local shops in the vicinity and you can go to the nearby parks for a stroll after a long day out. The hosts are approachable and serve you a hearty breakfast. Totally recommended!',
            'request_hash_id' => $request_hash_id,
        ];

        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/property/review';
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
            // phpcs:ignore
            'review'          => 'Unit Testing - The interior of this property is remarkable! It is clean, furnished and well taken care of and the location makes all the major markets accessible. The host is fluent in English and French, quite a relief for all the foreign tourists. There are restaurants and local shops in the vicinity and you can go to the nearby parks for a stroll after a long day out. The hosts are approachable and serve you a hearty breakfast. Totally recommended!',
            'request_hash_id' => $request_hash_id,
        ];

        // Create traveller .
        $traveller = $this->createUsers();

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/review';
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
            // phpcs:ignore
            'booking_review'     => 'Unit Testing - The interior of this property is remarkable! It is clean, furnished and well taken care of and the location makes all the major markets accessible. The host is fluent in English and French, quite a relief for all the foreign tourists. There are restaurants and local shops in the vicinity and you can go to the nearby parks for a stroll after a long day out. The hosts are approachable and serve you a hearty breakfast. Totally recommended!',
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

        $post_param = ['request_hash_id' => $request_hash_id];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/review';
        $response = $this->actingAs($booking_request_data['traveller'])->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testMissingParameterWithAuthentication()


    /**
     * Test Authorized User response with Request id.
     *
     * @return void
     */
    public function testValidResponseWithoutImageWithAuthentication()
    {
        Event::fake();

        // Mocking push email function ,and should  return blank.
        $this->mocked_service->shouldReceive('pushEmail')->andReturn('');

         // Mocking push sms function ,and should  return blank.
        $this->mocked_service->shouldReceive('pushSms')->andReturn('');

        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Request Hash Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $post_param = [
            // phpcs:ignore
            'review'          => 'Unit Testing - The interior of this property is remarkable! It is clean, furnished and well taken care of and the location makes all the major markets accessible. The host is fluent in English and French, quite a relief for all the foreign tourists. There are restaurants and local shops in the vicinity and you can go to the nearby parks for a stroll after a long day out. The hosts are approachable and serve you a hearty breakfast. Totally recommended!',
            'request_hash_id' => $request_hash_id,
        ];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/review';
        $response = $this->actingAs($booking_request_data['traveller'])->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithoutImageWithAuthentication()


    /**
     * Test Authorized User response with Request id.
     *
     * @return void
     */
    public function testValidResponseWithImageAndAuthentication()
    {
        Event::fake();

        // Mocking push email function ,and should  return blank.
        $this->mocked_service->shouldReceive('pushEmail')->andReturn('');

         // Mocking push sms function ,and should  return blank.
        $this->mocked_service->shouldReceive('pushSms')->andReturn('');

        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Request Hash Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $post_param = [
            // phpcs:ignore
            'review'          => 'Unit Testing - The interior of this property is remarkable! It is clean, furnished and well taken care of and the location makes all the major markets accessible. The host is fluent in English and French, quite a relief for all the foreign tourists. There are restaurants and local shops in the vicinity and you can go to the nearby parks for a stroll after a long day out. The hosts are approachable and serve you a hearty breakfast. Totally recommended!',
            'request_hash_id' => $request_hash_id,
            'review_images'   => json_encode([$this->saveImageInTempMemory('test_image.png', $booking_request_data['traveller']->id)]),
        ];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/review';
        $response = $this->actingAs($booking_request_data['traveller'])->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        array_map('unlink', glob(PROPERTY_REVIEW_IMAGE_BASE_URL.'*'));
        array_map('unlink', glob(PROPERTY_REVIEW_IMAGE_TEMP_URL.'*'));

    }//end testValidResponseWithImageAndAuthentication()


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

        // Create Property Review.
        $property_review = $this->createPropertyReview($booking_request_data['bookings']);

        // Create Traveller Ratings.
        $traveller_ratings = $this->createTravellerRatings($booking_request_data['bookings']);

        // Request Hash Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $post_param = [
            // phpcs:ignore
            'review'          => 'Unit Testing - The interior of this property is remarkable! It is clean, furnished and well taken care of and the location makes all the major markets accessible. The host is fluent in English and French, quite a relief for all the foreign tourists. There are restaurants and local shops in the vicinity and you can go to the nearby parks for a stroll after a long day out. The hosts are approachable and serve you a hearty breakfast. Totally recommended!',
            'request_hash_id' => $request_hash_id,
        ];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/review';
        $response = $this->actingAs($booking_request_data['traveller'])->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testForbiddenErrorResponseWithAuthentication()


}//end class
