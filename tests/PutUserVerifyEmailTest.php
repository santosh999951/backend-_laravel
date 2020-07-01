<?php
/**
 * PutUserVerifyEmailTest Test containing methods related to verify Email  Test case
 */

/**
 * Class PutUserVerifyEmailTest
 *
 * @group User
 */
class PutUserVerifyEmailTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Without Device Id
     *
     * @return void
     */
    public function testResponseWithoutDeviceId()
    {
        // Create Demo Entry in change_usermail table.
        $token = $this->createToken();

        $token_array = [
            'confirmation_code' => $token['token_details'][0]['confirmation_code'],
        ];
        $url         = $this->getApiVersion().'/user/verify/email';
        $response    = $this->put($url, $token_array);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseWithoutDeviceId()


    /**
     * Test With Login For Existing User.
     *
     * @return void
     */
    public function testResponseWithAuthorizationExistingUser()
    {
        // Create Demo Entry in change_usermail table.
        $token = $this->createToken();

        $token_array = [
            'confirmation_code' => $token['token_details'][0]['confirmation_code'],
        ];
        $url         = $this->getApiVersion().'/user/verify/email';
        $response    = $this->put($url, $token_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithAuthorizationExistingUser()


    /**
     * Test With Login For New User.
     *
     * @return void
     */
    public function testResponseWithAuthorizationNewUser()
    {
        // Create Demo Entry in user table with email_verify=0.
        $token = $this->createUsers(1, ['email_verify' => 0]);

        $token_array = [
            'confirmation_code' => $token[0]['confirmation_code'],
        ];
        $url         = $this->getApiVersion().'/user/verify/email';
        $response    = $this->put($url, $token_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithAuthorizationNewUser()


    /**
     * Test BadRequest.
     *
     * @return void
     */
    public function testResponseBadRequestWithAuthorization()
    {
        $url      = $this->getApiVersion().'/user/verify/email';
        $response = $this->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseBadRequestWithAuthorization()


    /**
     * Test Wrong Token.
     *
     * @return void
     */
    public function testResponseWrongToken()
    {
         $token_array = [
             'confirmation_code' => str_random(36),
         ];
         $url         = $this->getApiVersion().'/user/verify/email';
         $response    = $this->put($url, $token_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

         // Check status of response.
         $this->seeStatusCode(400);

    }//end testResponseWrongToken()


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
