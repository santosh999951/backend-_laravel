<?php
/**
 * PostUserLogoutTest Test containing methods related to logout user api
 */

/**
 * Class PostUserLogoutTest
 *
 * @group User
 */
class PostUserLogoutTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Unauthorized User logout api response.
     *
     * @return void
     */
    public function testUnauthroizedResponse()
    {
        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/user/logout';
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testUnauthroizedResponse()


    /**
     * Test Authorized User logout api response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create User.
        $user = $this->createUsers()[0];

        $url = $this->getApiVersion().'/user/logout';

        // Execute api using Post Request Method.
        $response = $this->actingAs($user)->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


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
