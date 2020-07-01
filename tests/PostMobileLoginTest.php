<?php
/**
 * PostMobileLoginTest containing methods related to login using phone number
 */

/**
 * Class PostMobileLoginTest
 *
 * @group User
 */
class PostMobileLoginTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Setup.
     *
     * @return void
     */
    public function setup():void
    {
        parent::setup();
        $this->mocked_service = $this->mock('alias:App\Libraries\v1_6\SmsService');

    }//end setup()


    /**
     * Tear down.
     *
     * @return void
     */
    public function tearDown():void
    {
        $this->clearmock();

    }//end tearDown()


     /**
      * Test Bad request User response.
      *
      * @return void
      */
    public function testBadRequestResponseWithAuthentication()
    {
        // Execute api.
        $url = $this->getApiVersion().'/user/login/mobile';
        // Passing no parameter.
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test valid User response.
     *
     * @return void
     */
    public function testValidResponseResetWithNumberWithAuthentication()
    {
        $traveller = $this->createUser();
        $this->mocked_service->shouldReceive('sendOtp')->once()->andReturn(['status' => 1, 'msg' => 'sent successfully']);

        // Execute api using with authentication.
        $url = $this->getApiVersion().'/user/login/mobile';

        $this->post($url, ['contact_number' => $traveller->contact, 'dial_code' => $traveller->dial_code, 'otp_method' => 1], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseResetWithNumberWithAuthentication()


}//end class
