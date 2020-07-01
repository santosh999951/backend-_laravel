<?php
/**
 * PostPriveManagerBookingOperationTest Test containing methods related to Change Booking Operation data
 */

use App\Libraries\Helper;
use Carbon\Carbon;

/**
 * Class PostPriveManagerBookingOperationTest
 *
 * @group Manager
 */
class PostPriveManagerBookingOperationTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Manager.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Post Checked in status change.
        $url      = $this->getApiVersion().'/prive/manager/booking/operation';
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


     /**
      * Test for Non Manager User.
      *
      * @return void
      */
    public function testInvalidResponseForNonManagerWithAuthentication()
    {
        $non_manager_user = $this->createUsers();

        // Get host payment preference response data.
        $url      = $this->getApiVersion().'/prive/manager/booking/operation';
        $response = $this->actingAs($non_manager_user[0])->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForNonManagerWithAuthentication()


    /**
     * Test Invalid or Missing Parameter.
     *
     * @test
     * @return void
     */
    public function testInvalidOrMissingParametersResponseWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['checkedin-time-edit#operations', 'notes-edit#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host']);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = ['request_hash_id' => $request_hash_id];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/operation';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

        $content = json_decode($this->response->getContent(), true);

        if (empty($content['error']) === false) {
            $this->assertEquals('validation_failed', $content['error'][0]['code']);
            $this->assertEquals('operational_note', $content['error'][0]['key']);
        } else {
            $this->assertTrue(false);
        }

    }//end testInvalidOrMissingParametersResponseWithAuthentication()


    /**
     * Test Operational notes Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseForOperationalNotesWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['checkedin-time-edit#operations', 'notes-edit#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host']);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = [
            'request_hash_id'  => $request_hash_id,
            'operational_note' => 'Test Operational Notes',
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/operation';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseForOperationalNotesWithAuthentication()


    /**
     * Test Managerial Notes Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseForManagerialNotesWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['checkedin-time-edit#operations', 'notes-edit#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host'], ['checkin_status' => 1]);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = [
            'request_hash_id' => $request_hash_id,
            'managerial_note' => 'Test Managerial Notes',
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/operation';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseForManagerialNotesWithAuthentication()


    /**
     * Test to save expected checkin Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testInvalidResponseForExpectedCheckinWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['checkedin-time-edit#operations', 'notes-edit#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host']);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = [
            'request_hash_id'  => $request_hash_id,
            'expected_checkin' => '13:30:00',
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/operation';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testInvalidResponseForExpectedCheckinWithAuthentication()


    /**
     * Host Payout Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseForExpectedCheckoutWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['checkedin-time-edit#operations', 'notes-edit#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host']);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = [
            'request_hash_id'   => $request_hash_id,
            'expected_checkout' => '12:00:00',
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/operation';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseForExpectedCheckoutWithAuthentication()


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
                 'request_hash_id',
                 'message',

             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
