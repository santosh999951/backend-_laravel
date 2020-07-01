<?php
/**
 * GetHostPropertyReviewTest Test containing methods related to Property Review Test case
 */

/**
 * Class GetHostPropertyReviewTest
 *
 * @group Host
 */
class GetHostPropertyReviewTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Get Home response data.
        $url      = $this->getApiVersion().'/host/property/review';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
        $traveller = $this->createUser();

        // Get Home response data.
        $url      = $this->getApiVersion().'/host/property/review';
        $response = $this->actingAs($traveller)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


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

        // Get host home detail response data.
        $url = $this->getApiVersion().'/host/property/review';

        $response = $this->actingAs($booking_request_data['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
            'data' => [
                'reviews' => [
                    '*' => [
                        'request_hash_id',
                        'guests',
                        'host_name',
                        'host_image',
                        'property_rating',
                        'traveller_id',
                        'traveller_name',
                        'review_date',
                        'comment',
                        'reply',
                        'nights',
                        'review_images' => [],
                        'traveller_image',
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
                        ]
                    ],
                ],
                'updated_offset',
                'limit'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
