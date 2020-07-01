<?php
/**
 * GetUserStatusTest containing methods related to user status api.
 */

/**
 * Class PostLoginSignupViaTest
 */
class GetUserStatusTest extends TestCase
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
        $url = $this->getLatestApiVersion().'/user/status';
        // Passing no parameter.
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test Invalid response.
     *
     * @return void
     */
    public function testInvalidParamResponse()
    {
        // Make Get Parameters.
        $params = ['login_id' => str_random(10)];

        // Make Url.
        $url = $this->getLatestApiVersion().'/user/status?'.http_build_query($params);
        // Execute Api.
        $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testInvalidParamResponse()


    /**
     * Test valid User response  with status via contact.
     *
     * @return void
     */
    public function testValidResponseWithStatusViaContact()
    {
        // Create Default User.
        $user = $this->createUsers();

        $params = [
            'login_id'  => $user[0]->contact,
            'dial_code' => 91,
        ];

        // Execute api using with authentication.
        $url = $this->getLatestApiVersion().'/user/status?'.http_build_query($params);

        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        
        $content = json_decode($this->response->getContent(), true);
        // Check status of response.
        $this->seeStatusCode(200);

        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithStatusViaContact()


    /**
     * Test valid response with status via email.
     *
     * @return void
     */
    public function testValidResponseWithStatusViaEmail()
    {
        // Create Default User.
        $user = $this->createUsers();

        // Execute api using with authentication.
        $params = [
            'login_id' => $user[0]->email,
        ];

        // Execute api using with authentication.
        $url = $this->getLatestApiVersion().'/user/status?'.http_build_query($params);

        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
       
        $content = json_decode($this->response->getContent(), true);
        // Check status of response.
        $this->seeStatusCode(200);

        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithStatusViaEmail()


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
                'status'
            ],
            'message',
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
