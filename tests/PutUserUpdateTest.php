<?php
/**
 * PutUserUpdateTest containing methods related to User profile updatation
 */

/**
 * Class PutUserUpdateTest
 *
 * @group User
 */
class PutUserUpdateTest extends TestCase
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
        $url      = $this->getApiVersion().'/user';
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
        $url = $this->getApiVersion().'/user';
        // Bad request can be of any validation reason, firstname cannt be more than 40.
        $response = $this->actingAs($traveller)->put($url, ['first_name' => str_random(55)], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test Authorized User valid response with nothing to udpates.
     *
     * @return void
     */
    public function testValidResponseWithNoprofileUpdationWithAuthentication()
    {
        $traveller = $this->createUsers(1)[0];
        // Execute api using Put Request Method.
        $url = $this->getApiVersion().'/user';
          // Updating nothing and checking if it return proper response.
        $response = $this->actingAs($traveller)->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Not required of checking structure as structure is badly desined for this.
        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithNoprofileUpdationWithAuthentication()


     /**
      * Test Authorized User valid response with profile updation.
      *
      * @return void
      */
    public function testValidResponseWithProfileUpdationWithAuthentication()
    {
        $traveller = $this->createUsers(1)[0];
        // Execute api using Put Request Method.
        $url = $this->getApiVersion().'/user';
        // Updating first name and then checking in response that updated resposne is returned.
        $random_name = strtolower(str_random(25));
        $response    = $this->actingAs($traveller)->put($url, ['first_name' => $random_name], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Not required of checking structure as structure is badly desined for this.
        // Check status of response.
        $this->seeStatusCode(200);

        $user = json_decode($this->response->getContent(), 1);
        $this->assertEquals(ucfirst($random_name), $user['data']['first_name']);

    }//end testValidResponseWithProfileUpdationWithAuthentication()


}//end class
