<?php
/**
 * GetUserPasswordResetTokenValidTest Test containing methods related to validating reset password token.
 */

/**
 * Class GetUserPasswordResetTokenValidTest
 *
 * @group User
 */
class GetUserPasswordResetTokenValidTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test valid token.
     *
     * @return void
     */
    public function testValidResponseWithoutAuthentication()
    {
        // Create factory seeding for password reminder.
        $password_reminder = $this->createPasswordReminderTestSeeding();

        $token = $password_reminder['token'];
        $url   = $this->getApiVersion().'/user/password/'.$token;

        // Execute the api.
        $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Test Valid Code and Structure.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Test if token is valid.
        $response       = json_decode($this->response->getContent(), 1);
        $is_token_valid = $response['data']['is_valid'];

        $this->assertEquals(1, $is_token_valid);

    }//end testValidResponseWithoutAuthentication()


    /**
     * Test invalid token Input.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        $invalid_token = str_random(20);
        $url           = $this->getApiVersion().'/user/password/'.$invalid_token;

        // Execute the api.
        $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

         // Test Valid Code and Structure.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Test if token is invalid.
        $response       = json_decode($this->response->getContent(), 1);
        $is_token_valid = $response['data']['is_valid'];

        $this->assertEquals(0, $is_token_valid);

    }//end testInvalidResponseWithoutAuthentication()


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
            'data' => ['is_valid'],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
