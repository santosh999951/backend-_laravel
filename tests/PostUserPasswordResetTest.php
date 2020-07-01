<?php
/**
 * PostUserPasswordResetTest containing methods related to intiate reset user password by mail or sms.
 */

use App\Events\UserResetPassword;

/**
 * Class PostUserPasswordResetTest
 *
 * @group User
 */
class PostUserPasswordResetTest extends TestCase
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
        $traveller = $this->createUser();
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/user/password/reset';
        // Passing no parameter.
        $response = $this->actingAs($traveller)->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test valid User response with new email to verify.
     *
     * @return void
     */
    public function testValidResponseResetWithNumberWithAuthentication()
    {
        // Check event is fired.
        Event::fake();

        $traveller = $this->createUser();
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/user/password/reset';

        // Reset password by contact no.
        $this->actingAs($traveller)->post($url, ['reset_password_via' => $traveller->contact], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        Event::assertDispatched(UserResetPassword::class);

    }//end testValidResponseResetWithNumberWithAuthentication()


     /**
      * Test valid User response with reset password by email.
      *
      * @return void
      */
    public function testValidResponseResetWithEmailWithAuthentication()
    {
        // Check event is fired.
        Event::fake();

        $traveller = $this->createUser();
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/user/password/reset';

        // Reset password by email.
        $this->actingAs($traveller)->post($url, ['reset_password_via' => $traveller->email], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        Event::assertDispatched(UserResetPassword::class);

    }//end testValidResponseResetWithEmailWithAuthentication()


}//end class
