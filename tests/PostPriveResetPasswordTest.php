<?php
/**
 * PostPriveResetPasswordTest containing methods related to  reset prive password by mail.
 */

use App\Events\UserResetPassword;

/**
 * Class PostPriveResetPasswordTest
 *
 * @group Owner
 */
class PostPriveResetPasswordTest extends TestCase
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
        // Mocking PasswordReminder.
        $this->mocked_service = $this->mock('alias:App\Models\PasswordReminder');

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
        $prive = $this->createUser(['prive_owner' => 1]);
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/prive/password/reset';
        // Passing no parameter.
        $response = $this->actingAs($prive)->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test valid User response with  email to verify.
     *
     * @return void
     */
    public function testValidResponseResetWithNumberWithAuthentication()
    {
        // Check event is fired.
        Event::fake();

         // Mocking getResetPasswordTokenByEmail.
        $this->mocked_service->shouldReceive('getResetPasswordTokenByEmail')->andReturn('abcdefghijklmnopqrstuvwxy');

        $prive = $this->createUser(['prive_owner' => 1]);
        $email = $prive->email;
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/prive/password/reset';

        // Reset password by contact no.
        $response = $this->actingAs($prive)->post($url, ['email' => $prive->email], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(200);

         // Check Json Structure.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Check Json Values.
        $this->seeJsonEquals($this->getApiValues());

        Event::assertDispatched(
            UserResetPassword::class,
            function ($event) use ($email) {
                return (
                $event->to_email === $email &&
                $event->token === 'abcdefghijklmnopqrstuvwxy' &&
                $event->dial_code === '' &&
                $event->contact === '' &&
                $event->verification_code === '' &&
                $event->role === 'prive'

                );
            }
        );

    }//end testValidResponseResetWithNumberWithAuthentication()


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
            'data' => [
                'email_sent',
                'message',
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


     /**
      * Helper function to api Values
      *
      * @return array
      */
    private function getApiValues()
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
        return [
            'status' => true,
            'data'   => [
                'email_sent' => 1,
                'message'    => 'Link sent on registered email id.',
            ],
            'error'  => [],
        ];

    }//end getApiValues()


}//end class
