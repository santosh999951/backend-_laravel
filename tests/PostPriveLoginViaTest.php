<?php
/**
 * PostPriveLoginViaTest containing methods related to login via api.
 */

/**
 * Class PostPriveLoginViaTest
 *
 * @group Owner
 */
class PostPriveLoginViaTest extends TestCase
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
        $url = $this->getApiVersion().'/prive/loginvia/';
        // Passing no parameter.
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test Invalid Prive Owner/manager response.
     *
     * @return void
     */
    public function testInvalidParamResponse()
    {
        // Create Default User.
        $user = $this->createUser();

        // Make Post Parameters.
        $post_params = [
            'login_via' => str_random(10),
        ];

        // Make Url.
        $url = $this->getApiVersion().'/prive/loginvia';

        // Execute Api.
        $this->post($url, $post_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testInvalidParamResponse()


    /**
     * Test valid User response for owner.
     *
     * @return void
     */
    public function testValidResponseWithLoginViaContactForOwnerAuthentication()
    {
        // Create Default User.
        $user = $this->createUser(['prive_owner' => 1]);

        $this->mocked_service->shouldReceive('sendOtp')->once()->andReturn(['status' => 1, 'msg' => 'sent successfully']);

        // Execute api using with authentication.
        $url = $this->getApiVersion().'/prive/loginvia';

        $this->post($url, ['login_via' => $user->contact, 'dial_code' => $user->dial_code, 'source' => 1], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithLoginViaContactForOwnerAuthentication()


    /**
     * Test valid User response for manager.
     *
     * @return void
     */
    public function testValidResponseWithLoginViaContactForManagerAuthentication()
    {
        // Create Default User.
        $user = $this->createUser(['prive_manager' => 1]);

        $this->mocked_service->shouldReceive('sendOtp')->once()->andReturn(['status' => 1, 'msg' => 'sent successfully']);

        // Execute api using with authentication.
        $url = $this->getApiVersion().'/prive/loginvia';

        $this->post($url, ['login_via' => $user->contact, 'dial_code' => $user->dial_code, 'source' => 2], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithLoginViaContactForManagerAuthentication()


        /**
         * Test valid User response.
         *
         * @return void
         */
    public function testValidResponseWithLoginViaEmailForOwnerAuthentication()
    {
        // Create Default User.
        $user = $this->createUser(['prive_owner' => 1]);

        // Execute api using with authentication.
        $url = $this->getApiVersion().'/prive/loginvia';

        $this->post($url, ['login_via' => $user->email, 'source' => 1], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithLoginViaEmailForOwnerAuthentication()


    /**
     * Test valid User response email for manager.
     *
     * @return void
     */
    public function testValidResponseWithLoginViaEmailForManagerAuthentication()
    {
        // Create Default User.
        $user = $this->createUser(['prive_manager' => 1]);

        // Execute api using with authentication.
        $url = $this->getApiVersion().'/prive/loginvia';

        $this->post($url, ['login_via' => $user->email, 'source' => 2], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithLoginViaEmailForManagerAuthentication()


}//end class
