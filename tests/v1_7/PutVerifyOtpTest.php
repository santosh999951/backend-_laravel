<?php
/**
 * PutVerifyOtpTest Test containing methods related to sent otp api Test case.
 */

use \Carbon\Carbon;

/**
 * Class PutVerifyOtpTest.
 */
class PutVerifyOtpTest extends TestCase
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
        $this->url            = $this->getLatestApiVersion().'/user/verify/otp';

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
        $response = $this->put($this->url, $params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test for verify otp to user for login otp case.
     *
     * @return void
    */
    public function testBadResponseForExpireOtpWithAuthentication()
    {
        //SmsOtp entry for with otp expire time.
        $sms_params = [
            'contact'           => '919999999999', 
            'type'              => '0', 
            'status'            => '0', 
            'device_unique_id'  => $this->getDeviceUniqueId(),
            'valid_till'        => Carbon::now('GMT')->subMinutes(1)
        ];
        $sms_details = $this->createSmsOtp($sms_params);
     
        $params = [
            'dial_code'  => '91',
            'contact'    => '9999999999',
            'otp_code'   => $sms_details[0]->verification_code
        ];
       
        $this->put($this->url, $params, ['HTTP_device-type' => 'web', 'device_unique_id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(403);

    }//end testBadResponseForExpireOtpWithAuthentication()


    /**
     * Test for verify otp to user for login otp case.
     *
     * @return void
     */
    public function testValidResponseForLoginOtpWithAuthentication()
    {
        //SmsOtp entry for otp type login(0).
        $sms_details = $this->createSmsOtp(['contact' => '919999999999', 'type' => '0', 'status' => '0', 'device_unique_id' => $this->getDeviceUniqueId() ]);
     
        $params = [
            'dial_code'  => '91',
            'contact'    => '9999999999',
            'otp_code'   => $sms_details[0]->verification_code
        ];
       
        $response = $this->put($this->url, $params, ['HTTP_device-type' => 'web', 'device_unique_id' => $this->getDeviceUniqueId()]);

        print_r($response);die;
        $this->seeStatusCode(200);

    }//end testValidResponseForLoginOtpWithAuthentication()



}//end class
