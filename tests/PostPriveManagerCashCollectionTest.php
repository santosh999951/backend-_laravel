<?php
/**
 * PostPriveManagerCashCollectionTest Test containing methods related to Booking Cash Collection data
 */

use App\Libraries\Helper;
use Carbon\Carbon;

/**
 * Class PostPriveManagerCashCollectionTest
 *
 * @group Manager
 */
class PostPriveManagerCashCollectionTest extends TestCase
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
        $url      = $this->getApiVersion().'/prive/manager/booking/cash-collect';
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
        $url      = $this->getApiVersion().'/prive/manager/booking/cash-collect';
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
        $this->assignPermissionToUser($create_prive_manager, ['collect-payment#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host']);

        $post_param = [];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/cash-collect';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

        $content = json_decode($this->response->getContent(), true);

        if (empty($content['error']) === false) {
            $this->assertEquals('validation_failed', $content['error'][0]['code']);
            $this->assertEquals('request_hash_id', $content['error'][0]['key']);
        } else {
            $this->assertTrue(false);
        }

    }//end testInvalidOrMissingParametersResponseWithAuthentication()


    /**
     * Test with contact details Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['collect-payment#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host'], ['coa_to_be_collected' => 1000]);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = ['request_hash_id' => $request_hash_id];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/cash-collect';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


    /**
     * Test Invalid due to no balance fee Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testInalidResponseDueToNoBalanceFeeWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['collect-payment#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host'], ['coa_to_be_collected' => 0]);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = ['request_hash_id' => $request_hash_id];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/cash-collect';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(404);

    }//end testInalidResponseDueToNoBalanceFeeWithAuthentication()


    /**
     * Test Invalid Non Booked status Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testInalidResponseForNonBookedWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['collect-payment#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(REQUEST_APPROVED, ['prive' => 1], $create_property['properties'][0], $create_property['host']);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = ['request_hash_id' => $request_hash_id];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/cash-collect';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(404);

    }//end testInalidResponseForNonBookedWithAuthentication()


    /**
     * Test Invalid For Request not exists Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testInalidResponseForRequestIdNotExistWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 1, ['prive' => 1]);

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['collect-payment#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Create Booking Data.
        $create_booking = $this->createBookingRequests(REQUEST_APPROVED);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = ['request_hash_id' => $request_hash_id];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/cash-collect';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(404);

    }//end testInalidResponseForRequestIdNotExistWithAuthentication()


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
                 'bank_name',
                 'account_number',
                 'ifsc_code',

             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
