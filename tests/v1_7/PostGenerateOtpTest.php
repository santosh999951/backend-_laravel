<?php
/**
 * PostGeneratedOtpTest Test containing methods related to sent otp api Test case
 */

/**
 * Class PostGenerateOtpTest.
 */
class PostGenerateOtpTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Setup.
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();
        $this->mocked_service = $this->mock('alias:App\Libraries\v1_6\SmsService');
        $this->url            = $this->getLatestApiVersion().'/user/generate/otp';;

    }//end setup()


    /**
     * Tear down.
     *
     * @return void
     */
    public function tearDown(): void
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
        $params = [];
        // Passing no parameter.
        $response = $this->post($this->url, $params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test for send otp to user.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
      
        $sms_details = $this->createSmsOtp(['contact' => '919999999999', 'type' => '0', 'status' => '0', 'device_unique_id' => $this->getDeviceUniqueId() ]);
        
        $params = [
            'contact'       => '9999999999',
            'dial_code'     => '91',
            'otp_method'    => '1',
            'otp_type'      =>  $sms_details[0]->type
        ];

        $this->mocked_service->shouldReceive('sendOtp')->once()->andReturn(['status' => 1, 'msg' => 'sent successfully']);
        
        $response = $this->post($this->url, $params, ['HTTP_device-type' => 'web', 'device_unique_id' => $this->getDeviceUniqueId()]);
        
        $content = json_decode($this->response->getContent(), true);
        //Check status of response.
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
            "status",
            "data" => [
                "dial_code",
                "contact",
                "sms_sender_ids",
            ],
            'message',
            "error"
            
        ];

    }//end getApiStructureWithOnlyDefaultValues()

}//end class
