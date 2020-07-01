<?php
/**
 * PostLoginOldAppUserTest Test containing methods related to api with old login accesstokens.
 */

/**
 * Class PostLoginOldAppUserTest
 *
 * @group User
 */
class PostLoginOldAppUserTest extends TestCase
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

    }//end setup()


    /**
     * Test Bad User response.
     *
     * @return void
     */
    public function testBadRequestResponseWithoutAuthentication()
    {
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/user/loginoldappuser';
        // Not passing required params.
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithoutAuthentication()


     /**
      * Test valid User response with existing email to verify.
      *
      * @return void
      */
    public function testValidResponseWithCurrentEmailWithoutAuthentication()
    {
        $traveller = $this->createUser();

        $device_unique_id = str_random(25);
        $device           = $this->registerDevice(['user_id' => $traveller->id, 'device_unique_id' => $device_unique_id])[0];
        // Inserting into old oauth token table.
           $tokens = $this->createLoggedInAccessOldUserAccessTokens($device_unique_id);

        // Execute api using without authentication.
        $url = $this->getApiVersion().'/user/loginoldappuser';

        // Sending old access tokens, refresh tokens.
        $this->post($url, ['access_token' => $tokens['access_token'], 'refresh_token' => $tokens['refresh_token'], 'device_unique_id' => $device_unique_id], ['HTTP_device-unique-id' => $device_unique_id]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithCurrentEmailWithoutAuthentication()


}//end class
