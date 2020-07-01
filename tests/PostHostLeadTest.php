<?php
/**
 * PostHostLeadTest Test containing methods related to api host lead.
 */

use App\Libraries\Helper;

/**
 * Class PostHostLeadTest
 *
 * @group Host
 */
class PostHostLeadTest extends TestCase
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
            'name'          => 'Unit Testing Host',
            'contact'       => rand(1111111111, 9999999999),
            'email'         => 'testing.host.lead.api'.str_random(4).time().'@guesthouser.com',
            'property_type' => rand(1, 24),
            'address'       => str_random(36),
            'city'          => str_random(36),
        ];

        $url = $this->getApiVersion().'/host/lead';
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
        $user = $this->createUsers();
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/host/lead';
        // Not passing required params.
        $response = $this->actingAs($user[0])->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
        $user = $this->createUsers();

        // Make Post Parameters.
        $post_params = [
            'name'          => 'Unit Testing Host',
            'contact'       => rand(1111111111, 9999999999),
            'email'         => 'testing.host.lead.api'.str_random(4).time().'@guesthouser.com',
            'property_type' => rand(1, 24),
            'address'       => str_random(36),
            'city'          => str_random(36),
        ];

        // Make Url.
        $url = $this->getApiVersion().'/host/lead';

        // Execute Api.
        $this->actingAs($user[0])->post($url, $post_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

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


}//end class
