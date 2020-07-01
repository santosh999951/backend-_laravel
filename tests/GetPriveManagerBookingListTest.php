<?php
/**
 * GetPriveManagerBookingListTest Test containing methods related to Prive manager booking list api
 */

use App\Libraries\Helper;

/**
 * Class GetPriveManagerBookingListTest
 *
 * @group Manager
 */
class GetPriveManagerBookingListTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Prive manager.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Get Host Property data.
        $url      = $this->getApiVersion().'/prive/manager/bookings';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test for Non manager.
     *
     * @return void
     */
    public function testInvalidResponseForNonManagerWithAuthentication()
    {
        $non_manager = $this->createUsers();

        // Get Host Property data.
        $url      = $this->getApiVersion().'/prive/manager/bookings';
        $response = $this->actingAs($non_manager[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForNonManagerWithAuthentication()


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
        $this->assignPermissionToUser($create_prive_manager, ['booking-view#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host']);

        // Get Host Property data.
        $url = $this->getApiVersion().'/prive/manager/bookings';

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
                 'booking_requests' => [
                     '*' => [
                         'request_hash_id',
                         'guests',
                         'amount',
                         'booking_status' => [
                             'text',
                             'class',
                             'status',
                         ],
                         'status' => [
                             'text',
                             'class',
                             'status',
                         ],
                         'checkin',
                         'checkout',
                         'checkin_formatted',
                         'checkout_formatted',
                         'property_hash_id',
                         'property_title',
                         'property_type_name',
                         'room_type_name',
                         'traveller_hash_id',
                         'traveller_name',
                         'verified',
                         'location' => [
                             'area',
                             'city',
                             'state',
                             'country' => [
                                 'name',
                                 'ccode',
                             ],
                             'location_name'
                         ],
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
                         ],
                     ],
                 ],
                 'filter'           => [
                     'start_date',
                     'end_date',
                     'properties' => [
                         '*' => [
                             'property_hash_id',
                             'title',
                             'selected',
                         ],
                     ],
                     'status' => [
                         '*' => [
                             'text',
                             'class',
                             'status',
                             'selected',
                         ],
                     ],
                     'search'
                 ],
                 'sort'             => [],
                 'booking_count',
             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
