<?php
/**
 * PostPriveManagerBookingCheckedInStatusTest Test containing methods related to Change Booking CheckedIn Status
 */

use App\Libraries\Helper;
use \Carbon\Carbon;

/**
 * Class PostPriveManagerBookingCheckedInStatusTest
 *
 * @group Manager
 */
class PostPriveManagerBookingCheckedInStatusTest extends TestCase
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
        $url      = $this->getApiVersion().'/prive/manager/booking/status';
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
        $url      = $this->getApiVersion().'/prive/manager/booking/status';
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
        $this->assignPermissionToUser($create_prive_manager, ['checked-status-edit#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host']);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = ['request_hash_id' => $request_hash_id];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/status';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

        $content = json_decode($this->response->getContent(), true);

        if (empty($content['error']) === false) {
            $this->assertEquals('validation_failed', $content['error'][0]['code']);
            $this->assertEquals('status', $content['error'][0]['key']);
        } else {
            $this->assertTrue(false);
        }

    }//end testInvalidOrMissingParametersResponseWithAuthentication()


    /**
     * Host Payout Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseForCheckinBookingWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['checked-status-edit#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        $from_date = Carbon::createFromTimestamp(time())->toDateString();
        $to_date   = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 2))->toDateString();

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1, 'from_date' => $from_date, 'to_date' => $to_date], $create_property['properties'][0], $create_property['host']);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = [
            'request_hash_id' => $request_hash_id,
            'status'          => PRIVE_MANAGER_CHECKEDIN,
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/status';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseForCheckinBookingWithAuthentication()


    /**
     * Host Payout Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseForCheckoutBookingWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['checked-status-edit#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host'], ['checkin_status' => 1]);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = [
            'request_hash_id' => $request_hash_id,
            'status'          => PRIVE_MANAGER_CHECKEDOUT,
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/status';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseForCheckoutBookingWithAuthentication()


    /**
     * Host Payout Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testInvalidResponseForMarkNoShowWithoutReasonBookingWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['checked-status-edit#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host']);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = [
            'request_hash_id' => $request_hash_id,
            'status'          => PRIVE_MANAGER_NO_SHOW,
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/status';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testInvalidResponseForMarkNoShowWithoutReasonBookingWithAuthentication()


    /**
     * Host Payout Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseForMarkNoShowWithReasonBookingWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['checked-status-edit#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host']);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = [
            'request_hash_id' => $request_hash_id,
            'status'          => PRIVE_MANAGER_NO_SHOW,
            'reason_id'       => 1,
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/status';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseForMarkNoShowWithReasonBookingWithAuthentication()


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
