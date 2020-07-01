<?php
/**
 * PostUserVerifyEmailTest Test containing methods related to user verifying email address.
 */

use App\Events\UserEmailVerified;
/**
 * Class PostUserVerifyEmailTest
 *
 * @group User
 */
class PostUserVerifyEmailTest extends TestCase
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
        // Mocking common queue not stop not sending email.
        $this->mocked_service = $this->mock('alias:App\Libraries\CommonQueue');

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
     * Test Unauthorized User response.
     *
     * @return void
     */
    public function testUnauthroizedResponseWithoutAuthentication()
    {
        // Execute api using without authentication.
        $url      = $this->getApiVersion().'/user/verify/email';
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testUnauthroizedResponseWithoutAuthentication()


     /**
      * Test Bad Request User response.
      *
      * @return void
      */
    public function testBadRequestResponseWithAuthentication()
    {
        $traveller = $this->createUser();
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/user/verify/email';
        // Email param should be non email for this test.
        $response = $this->actingAs($traveller)->post($url, ['email' => 'abc'], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    // /**
    // * Test valid User response with new email to verify.
    // *
    // * @return void
    // */
    // public function testValidResponseWithNewEmailWithAuthentication()
    // {
    // Event::fake();
    // $this->mocked_service->shouldReceive('pushEmail')->once()->andReturn('');
    // $traveller = $this->createUser();
    // Execute api using without authentication.
    // $url = $this->getApiVersion().'/user/verify/email';
    // $random_email = $this->createRandomEmail();
    // Update user email to this random email.
    // $this->actingAs($traveller)->post($url, ['email' => $random_email], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
    // Check status of response.
    // $this->seeStatusCode(200);
    // Event::assertDispatched(
    // UserEmailVerified::class,
    // function ($event) use ($random_email) {
    // return (
    // $event->to_email === $random_email
    // );
    // }
    // );
    // }//end testValidResponseWithNewEmailWithAuthentication()
    // **
    // * Test valid User response with existing email to verify.
    // *
    // * @return void
    // */
    // public function testValidResponseWithCurrentEmailWithAuthentication()
    // {
    // Event::fake();
    // $this->mocked_service->shouldReceive('pushEmail')->once()->andReturn('');
    // $traveller = $this->createUser(['email_verify' => 0]);
    // Execute api using without authentication.
    // $url = $this->getApiVersion().'/user/verify/email';
    // Verifying exsting unverified email.
    // $this->actingAs($traveller)->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
    // Check status of response.
    // $this->seeStatusCode(200);
    // Event::assertDispatched(
    // UserEmailVerified::class,
    // function ($event) use ($traveller) {
    // return (
    // $event->to_email === $traveller->email
    // );
    // }
    // );
    // }//end testValidResponseWithCurrentEmailWithAuthentication()
}//end class
