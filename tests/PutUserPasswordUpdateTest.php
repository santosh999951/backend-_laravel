<?php
/**
 * PutUserPasswordUpdateTest Test containing methods related to User updating password.
 */

/**
 * Class PutUserPasswordUpdateTest
 *
 * @group User
 */
class PutUserPasswordUpdateTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Unauthorized response.
     *
     * @return void
     */
    public function testUnauthorizedActionResponse()
    {
        // Execute api using Put Request Method.
        $url      = $this->getApiVersion().'/user/password/update';
        $response = $this->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testUnauthorizedActionResponse()


    /**
     * Test BadRequest response.
     *
     * @return void
     */
    public function testBadRequestResponseWithAuthentication()
    {
        $traveller = $this->createUsers(1)[0];
        // Execute api using Put Request Method.
        $url      = $this->getApiVersion().'/user/password/update';
        $response = $this->actingAs($traveller)->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test Authorized User valid response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        $traveller = $this->createUsers(1)[0];
        // Execute api using Put Request Method.
        $url      = $this->getApiVersion().'/user/password/update';
        $response = $this->actingAs($traveller)->put($url, ['current_password' => base64_encode(111111), 'password' => base64_encode(222222)], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
