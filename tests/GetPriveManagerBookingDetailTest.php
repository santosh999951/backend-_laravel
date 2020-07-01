<?php
/**
 * GetPriveManagerBookingDetailTest Test containing methods related to Prive manager booking detail api
 */

use App\Libraries\Helper;

/**
 * Class GetPriveManagerBookingDetailTest
 *
 * @group Manager
 */
class GetPriveManagerBookingDetailTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Prive manager.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId(12345);

        // Get Host Property data.
        $url      = $this->getApiVersion().'/prive/manager/booking/'.$request_hash_id;
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
        $traveller = $this->createUsers();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId(12345);

        // Get Host Property data.
        $url      = $this->getApiVersion().'/prive/manager/booking/'.$request_hash_id;
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Property Listing Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties();

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['booking-view#operations', 'notes-view#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host']);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Get Host Property data.
        $url = $this->getApiVersion().'/prive/manager/booking/'.$request_hash_id;

        $response = $this->actingAs($create_prive_manager)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
                 'property_section'     => [
                     'property_hash_id',
                     'property_type',
                     'room_type',
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
                     'property_title',
                     'property_images' => [],
                     'url',
                     'amenities' => [
                         '*' => [
                             'id',
                             'name',
                             'icon',
                             'rank',
                         ],
                     ],
                 ],
                 'booking_info_section' => [
                     'info'                => [
                         'request_hash_id',
                         'guests',
                         'extra_guest',
                         'units',
                         'bedroom',
                         'status' => [
                             'text',
                             'class',
                             'status',
                         ],
                         'checkin',
                         'checkout',
                         'checkin_formatted',
                         'checkout_formatted',
                         'guest_name',
                         'source',
                         'checkin_data' => [
                             'actual_checkin',
                             'actual_checkout',
                             'expected_checkin',
                             'expected_checkout',
                         ],
                         'notes',
                         'traveller_email',
                         'contacts' => [
                             'manager'   => [
                                 'primary',
                                 'secondary',
                             ],
                             'traveller' => [
                                 'primary',
                                 'secondary',
                             ],
                         ]
                     ],
                     'booking_amount_info' => [
                         'currency' => [
                             'webicon',
                             'non-webicon',
                             'iso_code',
                         ],
                         'total_amount',
                         'total_amount_unformatted',
                         'paid_amount',
                         'paid_amount_unformatted',
                         'pending_payment',
                         'pending_payment_amount',
                         'extra_services' => [],
                         'payment_option'
                     ],
                 ],

             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
