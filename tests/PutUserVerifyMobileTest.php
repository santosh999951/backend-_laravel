<?php
/**
 * PutUserVerifyMobileTest Test containing methods related to verify mobile otp Test case
 */

/**
 * Class PutUserVerifyMobileTest
 *
 * @group User
 */
class PutUserVerifyMobileTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Without login
     *
     * @return void
     */
    public function testResponseWithoutAuthorization()
    {
        // Create Demo Entry in otp_contact table.
        $otp_contact = $this->createOtp();

        $otp_array = [
            'dial_code'      => 91,
            'contact_number' => $otp_contact['otp_contact'][0]['contact'],
            'otp_code'       => $otp_contact['otp_contact'][0]['otp'],
        ];
        $url       = $this->getApiVersion().'/user/verify/mobile#';
        $response  = $this->put($url, $otp_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testResponseWithoutAuthorization()


    /**
     * Test With Login.
     *
     * @return void
     */
    public function testResponseWithAuthorization()
    {
        // Create Demo Entry in otp_contact table.
        $otp_contact = $this->createOtp();

        $otp_array = [
            'dial_code'      => 91,
            'contact_number' => $otp_contact['otp_contact'][0]['contact'],

            'otp_code'       => $otp_contact['otp_contact'][0]['otp'],
        ];
        $url       = $this->getApiVersion().'/user/verify/mobile#';
        $response  = $this->actingAs($otp_contact['user'][0])->put($url, $otp_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithAuthorization()


    /**
     * Test BadRequest.
     *
     * @return void
     */
    public function testResponseBadRequestWithAuthorization()
    {
        // Create Demo Entry in otp_contact table.
        $otp_contact = $this->createOtp();

        $url      = $this->getApiVersion().'/user/verify/mobile#';
        $response = $this->actingAs($otp_contact['user'][0])->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseBadRequestWithAuthorization()


    /**
     * Test Forbidden.
     *
     * @return void
     */
    public function testResponseForbiddenWithAuthorization()
    {
        // Create Demo Entry in otp_contact table.
         $otp_contact = $this->createOtp();

         $otp_array = [
             'dial_code'      => 91,
             'contact_number' => 8826262112,
             'otp_code'       => $otp_contact['otp_contact'][0]['otp'],
         ];

         $url      = $this->getApiVersion().'/user/verify/mobile#';
         $response = $this->actingAs($otp_contact['user'][0])->put($url, $otp_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

         // Check status of response.
         $this->seeStatusCode(403);

    }//end testResponseForbiddenWithAuthorization()


    /**
     * Test Wrong Otp.
     *
     * @return void
     */
    public function testResponseWrongOtp()
    {
        // Create Demo Entry in otp_contact table.
         $otp_contact = $this->createOtp();

         $otp_array = [
             'dial_code'      => 91,
             'contact_number' => $otp_contact['otp_contact'][0]['contact'],
             'otp_code'       => 1234,
         ];

         $url      = $this->getApiVersion().'/user/verify/mobile#';
         $response = $this->actingAs($otp_contact['user'][0])->put($url, $otp_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

         // Check status of response.
         $this->seeStatusCode(403);

    }//end testResponseWrongOtp()


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
