<?php
/**
 * PostPriveContactUsTest Test containing methods related to api contact us.
 */

use App\Libraries\Helper;
use App\Events\ContactUs;

/**
 * Class PostHostLeadTest
 *
 * @group Owner
 */
class PostPriveContactUsTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test for Logout User.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Make Post Parameters.
        $post_params = [
            'subject' => 'Unit Testing Host',
            'message' => 'Unit Testing Host',
        ];

        $url = $this->getApiVersion().'/prive/contactus';
        $this->post($url, $post_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test Bad User response.
     *
     * @return void
     */
    public function testBadRequestResponseWithAuthentication()
    {
        $create_prive_user = $this->createUser(['prive_owner' => 1]);
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/prive/contactus';
        // Not passing required params.
        $response = $this->actingAs($create_prive_user)->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test Valid Authentication.
     *
     * @return void
     */
    public function testValidAuthentication()
    {
        Event::fake();
        $user = $this->createUser(['prive_owner' => 1]);

        // Make Post Parameters.
        $post_params = [
            'subject' => 'Unit Testing Host',
            'message' => 'Unit Testing Host',
        ];

        // Make Url.
        $url = $this->getApiVersion().'/prive/contactus';

        // Execute Api.
        $response = $this->actingAs($user)->post($url, $post_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Check Json Structure.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Check Json Values.
        $this->seeJsonEquals($this->getApiValues());

        Event::assertDispatched(
            ContactUs::class,
            function ($event) use ($post_params) {
                return (
                $event->subject === $post_params['subject'] &&
                $event->message === $post_params['message']
                );
            }
        );

    }//end testValidAuthentication()


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
            'data' => [],
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
            'data'   => ['message' => 'Email successfully sent'],
            'error'  => [],
        ];

    }//end getApiValues()


}//end class
