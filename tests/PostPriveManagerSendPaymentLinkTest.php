<?php
/**
 * PostPriveManagerSendPaymentLinkTest Test containing methods related to Change Booking Operation data
 */

use App\Libraries\Helper;
use Carbon\Carbon;
use App\Events\SendBookingPaymentLink;

/**
 * Class PostPriveManagerSendPaymentLinkTest
 *
 * @group Manager
 */
class PostPriveManagerSendPaymentLinkTest extends TestCase
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
        $url      = $this->getApiVersion().'/prive/manager/booking/send-payment-link';
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
        $url      = $this->getApiVersion().'/prive/manager/booking/send-payment-link';
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
        $url = $this->getApiVersion().'/prive/manager/booking/send-payment-link';

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
     * Test with No contact detail Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseForNoContactDetailsWithAuthentication()
    {
        // Check event is fired.
        Event::fake();

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
        $url = $this->getApiVersion().'/prive/manager/booking/send-payment-link';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Decode Json Response.
        $content = json_decode($this->response->getContent());

        Event::assertDispatched(
            SendBookingPaymentLink::class,
            function ($event) use ($create_booking) {
                    return ($event->request_id === $create_booking['booking_request']->id)
                            && ($event->to_mail === $create_booking['traveller']->email)
                            && ($event->contact === (string) $create_booking['traveller']->contact)
                            && ($event->dial_code === (string) $create_booking['traveller']->dial_code);
            }
        );

    }//end testValidResponseForNoContactDetailsWithAuthentication()


    /**
     * Test with contact details Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithContactDetailsWithAuthentication()
    {
        // Check event is fired.
        Event::fake();

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

        $post_param = [
            'request_hash_id' => $request_hash_id,
            'contact_number'  => '1111111111',
            'dial_code'       => '91',
            'email'           => $create_prive_manager->email,
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/send-payment-link';

        $response = $this->actingAs($create_prive_manager)->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Decode Json Response.
        $content = json_decode($this->response->getContent());

        Event::assertDispatched(
            SendBookingPaymentLink::class,
            function ($event) use ($post_param) {
                    return ($event->request_id === Helper::decodeBookingRequestId($post_param['request_hash_id']))
                            && ($event->to_mail === $post_param['email'])
                            && ($event->contact === $post_param['contact_number'])
                            && ($event->dial_code === $post_param['dial_code']);
            }
        );

    }//end testValidResponseWithContactDetailsWithAuthentication()


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
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], $create_property['properties'][0], $create_property['host']);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        $post_param = [
            'request_hash_id' => $request_hash_id,
            'contact_number'  => '1111111111',
            'dial_code'       => '91',
            'email'           => $create_prive_manager->email,
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/send-payment-link';

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

        $post_param = [
            'request_hash_id' => $request_hash_id,
            'contact_number'  => '1111111111',
            'dial_code'       => '91',
            'email'           => $create_prive_manager->email,
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/send-payment-link';

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

        $post_param = [
            'request_hash_id' => $request_hash_id,
            'contact_number'  => '1111111111',
            'dial_code'       => '91',
            'email'           => $create_prive_manager->email,
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/prive/manager/booking/send-payment-link';

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
                 'message',

             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
