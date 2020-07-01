<?php
/**
 * PutUserPasswordTest Test containing methods related to update password  Test case
 */

/**
 * Class PutUserPasswordTest
 *
 * @group User
 */
class PutUserPasswordTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Without Device Id
     *
     * @return void
     */
    public function testResponseWithoutDeviceId()
    {
        // Create Demo Entry in otp_contact table.
        $otp_contact = $this->createOtp();

        $token_array = [
            'reset_password_via' => $otp_contact['otp_contact'][0]['contact'],
            'password'           => base64_encode(str_random(10)),
            'otp_code'           => $otp_contact['otp_contact'][0]['otp'],
        ];
        $url         = $this->getApiVersion().'/user/password';
        $response    = $this->put($url, $token_array);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseWithoutDeviceId()


    /**
     * Test With Login BY Mobile number.
     *
     * @return void
     */
    public function testResponseWithAuthorizationByMobile()
    {
        // Create Demo Entry in otp_contact table.
        $otp_contact = $this->createOtp();

        $token_array = [
            'reset_password_via' => $otp_contact['otp_contact'][0]['contact'],
            'password'           => base64_encode(str_random(10)),
            'otp_code'           => $otp_contact['otp_contact'][0]['otp'],
        ];
        $url         = $this->getApiVersion().'/user/password';
        $response    = $this->put($url, $token_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithAuthorizationByMobile()


     /**
      * Test With Login By email.
      *
      * @return void
      */
    public function testResponseWithAuthorizationByEmail()
    {
        // Create Demo Entry in otp_contact table.
        $otp_contact = $this->createOtp();
        $email       = $otp_contact['user'][0]['email'];

        $token_array = [
            'reset_password_via' => $email,
            'password'           => base64_encode(str_random(10)),
            'otp_code'           => $otp_contact['otp_contact'][0]['otp'],
        ];
        $url         = $this->getApiVersion().'/user/password';
        $response    = $this->put($url, $token_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithAuthorizationByEmail()


    /**
     * Test BadRequest.
     *
     * @return void
     */
    public function testResponseBadRequestWithAuthorization()
    {
        $url       = $this->getApiVersion().'/user/password';
         $response = $this->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseBadRequestWithAuthorization()


    /**
     * Test Invalid Otp.
     *
     * @return void
     */
    public function testResponseInvalidOtp()
    {
        // Create Demo Entry in otp_contact table.
        $otp_contact = $this->createOtp();

        $token_array = [
            'reset_password_via' => $otp_contact['otp_contact'][0]['contact'],
            'password'           => base64_encode(str_random(10)),
            'otp_code'           => str_random(5),
        ];
        $url         = $this->getApiVersion().'/user/password';
        $response    = $this->put($url, $token_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseInvalidOtp()


    /**
     * Test Wrong Contact.
     *
     * @return void
     */
    public function testResponseWrongContact()
    {
        // Create Demo Entry in otp_contact table.
        $otp_contact = $this->createOtp();

        $token_array = [
            'reset_password_via' => rand(1111111111, 9999999999),
            'password'           => base64_encode(str_random(10)),
            'otp_code'           => str_random(5),
        ];
        $url         = $this->getApiVersion().'/user/password';
        $response    = $this->put($url, $token_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseWrongContact()


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
            'data' => ['message'],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
