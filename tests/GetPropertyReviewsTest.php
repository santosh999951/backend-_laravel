<?php
/**
 * GetPropertyReviewsTest Test containing methods related to BookingRequestDetails Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class GetPropertyReviewsTest
 *
 * @group Property
 */
class GetPropertyReviewsTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Data not found for invalid Property hash id .
     *
     * @return void
     */
    public function testNotFoundResponseWithAuthentication()
    {
        // Create New Property.
        $create_property_data = $this->createProperties(0);

        // Create Traveller.
        $traveller = $this->createUsers();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Get Property review response data.
        $url = $this->getApiVersion().'/property/reviews/'.$property_hash_id;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(404);

    }//end testNotFoundResponseWithAuthentication()


     /**
      * Test Data not found for invalid Property hash id .
      *
      * @return void
      */
    public function testBadRequestResponseWithAuthentication()
    {
        // Create travellerr.
        $traveller = $this->createUsers();

        // Temp Hash id.
        $property_hash_id = 'ABCDEF';

        // Get Peroperty Review response data.
        $url = $this->getApiVersion().'/property/reviews/'.$property_hash_id;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test Authorized User response.
     *
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

        // Encode Property Hash Id.
        $property_hash_id = Helper::encodePropertyId($booking_request_data['properties']->id);

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/reviews/'.$property_hash_id;
        $response = $this->actingAs($booking_request_data['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


    /**
     * Test Unauthorized User response.
     *
     * @return void
     */
    public function testValidResponseWithoutAuthentication()
    {
        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED);

        // Create Property Review.
        $property_review = $this->createPropertyReview($booking_request_data['bookings']);

        // Create Traveller Ratings.
        $traveller_ratings = $this->createTravellerRatings($booking_request_data['bookings']);

        // Encode Property Hash Id.
        $property_hash_id = Helper::encodePropertyId($booking_request_data['properties']->id);

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/reviews/'.$property_hash_id;
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithoutAuthentication()


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
                'reviews' => [
                    '*' => [
                        'guests',
                        'property_rating',
                        'traveller_id',
                        'traveller_name',
                        'review_date',
                        'comment',
                        'nights',
                        'review_images' => [],
                        'traveller_image'
                    ],
                ],
                'updated_offset',
                'limit'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
