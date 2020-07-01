<?php
/**
 * PostUserVerifyMobileBySendOtpTest Test containing methods related to sent otp api Test case
 */

/**
 * Class PostUserVerifyMobileBySendOtpTest
 *
 * @group User
 */
class PostUserVerifyMobileBySendOtpTest extends TestCase
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
        $this->url            = $this->getApiVersion().'/user/verify/mobile';

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
     * Test for send otp to user.
     *
     * @return void
     */
    public function testValidesponseWithAuthentication()
    {
        $user = $this->createUser();
        $this->mocked_service->shouldReceive('sendOtp')->once()->andReturn(['status' => 1, 'msg' => 'sent successfully']);

        $this->actingAs($user)->post($this->url, ['contact_number' => '12345678', 'dial_code' => '91', 'otp_method' => 1, 'referral_code' => ''], ['HTTP_device-type' => 'web', 'device_unique_id' => $this->getDeviceUniqueId()]);
         $this->seeStatusCode(200);

    }//end testValidesponseWithAuthentication()


}//end class
