<?php
/**
 * PutPasswordResetTest Test containing methods related to reset password verify  Test case
 */

/**
 * Class PutPasswordResetTest
 *
 * @group User
 */
class PutPasswordResetTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Without Device Id
     *
     * @return void
     */
    public function testResponseWithoutDeviceId()
    {
        // Create Demo entry in user table.
         $users = $this->createUsers();

        // Create factory seeding for password reminder.
        $email             = $users[0]->email;
        $token             = str_shuffle(TOKEN_FOR_RESET_PASSWORD);
        $password_reminder = $this->createPasswordReminderTestSeeding(['email' => $email, 'token' => $token]);

        $token = $password_reminder['token'];

        $token_array = [
            'token_hash' => $token,
            'password'   => base64_encode(str_random(10)),
        ];
        $url         = $this->getApiVersion().'/user/password/reset';
        $response    = $this->put($url, $token_array);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseWithoutDeviceId()


    /**
     * Test With Login.
     *
     * @return void
     */
    public function testResponseWithAuthorization()
    {
        $users = $this->createUsers();

        // Create factory seeding for password reminder.
        $email             = $users[0]->email;
        $token             = str_shuffle(TOKEN_FOR_RESET_PASSWORD);
        $password_reminder = $this->createPasswordReminderTestSeeding(['email' => $email, 'token' => $token]);

        $token = $password_reminder['token'];

        $token_array = [
            'token_hash' => $token,
            'password'   => base64_encode(str_random(10)),
        ];
        $url         = $this->getApiVersion().'/user/password/reset';
        $response    = $this->put($url, $token_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
        $url       = $this->getApiVersion().'/user/password/reset';
         $response = $this->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseBadRequestWithAuthorization()


    /**
     * Test InvalidPassword.
     *
     * @return void
     */
    public function testResponseInvalidPasswod()
    {
         $users = $this->createUsers();

        // Create factory seeding for password reminder.
        $email             = $users[0]->email;
        $token             = str_shuffle(TOKEN_FOR_RESET_PASSWORD);
        $password_reminder = $this->createPasswordReminderTestSeeding(['email' => $email, 'token' => $token]);

        $token = $password_reminder['token'];

        $token_array = [
            'token_hash' => $token,
            'password'   => base64_encode(str_random(5)),
        ];
        $url         = $this->getApiVersion().'/user/password/reset';
        $response    = $this->put($url, $token_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseInvalidPasswod()


    /**
     * Test Wrong Token.
     *
     * @return void
     */
    public function testResponseWrongToken()
    {
        $users = $this->createUsers();

          // Create factory seeding for password reminder.
        $email             = $users[0]->email;
        $token             = str_shuffle(TOKEN_FOR_RESET_PASSWORD);
        $password_reminder = $this->createPasswordReminderTestSeeding(['email' => $email, 'token' => $token]);

        $token = $password_reminder['token'];

        $token_array = [
            'token_hash' => str_random(62),
            'password'   => base64_encode(str_random(10)),
        ];
         $url        = $this->getApiVersion().'/user/password/reset';
         $response   = $this->put($url, $token_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

         // Check status of response.
         $this->seeStatusCode(404);

    }//end testResponseWrongToken()


     /**
      * Test Wrong Token.
      *
      * @return void
      */
    public function testResponseWrongEmail()
    {
        $token             = str_shuffle(TOKEN_FOR_RESET_PASSWORD);
        $password_reminder = $this->createPasswordReminderTestSeeding(['email' => 'testing.new.api'.strtolower(str_random(4)).'@guesthousertest.com', 'token' => $token]);

        $token = $password_reminder['token'];

        $token_array = [
            'token_hash' => $token,
            'password'   => base64_encode(str_random(10)),
        ];
         $url        = $this->getApiVersion().'/user/password/reset';
         $response   = $this->put($url, $token_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

         // Check status of response.
         $this->seeStatusCode(404);

    }//end testResponseWrongEmail()


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
