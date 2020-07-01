<?php
/**
 * PutHostPropertyReviewTest Test containing methods related to Host Property reply Test case
 */

use App\Libraries\Helper;
use \Carbon\Carbon;

/**
 * Class PutHostPropertyReviewTest
 *
 * @group Host
 */
class PutHostPropertyReviewTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED);

        // Create Property Review.
        $property_review = $this->createPropertyReview($booking_request_data['bookings']);

        // Create Traveller Ratings.
        $traveller_ratings = $this->createTravellerRatings($booking_request_data['bookings']);

        // Request hash id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        // Form data parameters.
        $put_param = [
            'reply'           => 'Unit Testing - Thank You for review.',
            'request_hash_id' => $request_hash_id,
        // Update property to offline.
        ];

        // Get response data.
        $url = $this->getApiVersion().'/host/property/review';
        $this->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test for Traveller.
     *
     * @return void
     */
    public function testInvalidResponseForTravellerWithAuthentication()
    {
        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED);

        // Create Property Review.
        $property_review = $this->createPropertyReview($booking_request_data['bookings']);

        // Create Traveller Ratings.
        $traveller_ratings = $this->createTravellerRatings($booking_request_data['bookings']);

        // Request hash id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        // Form data parameters.
        $put_param = [
            'reply'           => 'Unit Testing - Thank You for review.',
            'request_hash_id' => $request_hash_id,
        ];

        // Get response data.
        $url = $this->getApiVersion().'/host/property/review';

        $response = $this->actingAs($booking_request_data['traveller'])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Test Data not found for invalid request id .
     *
     * @return void
     */
    public function testInvalidHashIdResponseWithAuthentication()
    {
        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED);

        // Create Property Review.
        $property_review = $this->createPropertyReview($booking_request_data['bookings']);

        // Create Traveller Ratings.
        $traveller_ratings = $this->createTravellerRatings($booking_request_data['bookings']);

        // Form data parameters.
        $put_param = [
            'reply'           => 'Unit Testing - Thank You for review.',
            'request_hash_id' => 'ABCDEF',
        ];

        // Get response data.
        $url      = $this->getApiVersion().'/host/property/review';
        $response = $this->actingAs($booking_request_data['host'])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testInvalidHashIdResponseWithAuthentication()


    /**
     * Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED);

        // Create Property Review.
        $property_review = $this->createPropertyReview($booking_request_data['bookings']);

        // Create Traveller Ratings.
        $traveller_ratings = $this->createTravellerRatings($booking_request_data['bookings']);

        // Request hash id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        // Form data parameters.
        $put_param = [
            'reply'           => 'Unit Testing - Thank You for review.',
            'request_hash_id' => $request_hash_id,
        ];

        // Get response data.
        $url      = $this->getApiVersion().'/host/property/review';
        $response = $this->actingAs($booking_request_data['host'])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

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
            'data' => ['message'],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
