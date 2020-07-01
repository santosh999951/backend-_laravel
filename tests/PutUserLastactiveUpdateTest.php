<?php
/**
 * PutUserLastactiveUpdateTest Test containing methods related to User Active on Application Test case
 */

use Illuminate\Http\{Request};

/**
 * Class PutUserLastactiveUpdateTest
 *
 * @group User
 */
class PutUserLastactiveUpdateTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Unauthorized User response.
     *
     * @return void
     */
    public function testUnauthorizedActionResponse()
    {
        // Execute api using Put Request Method.
        $url      = $this->getApiVersion().'/user/lastactive/update';
        $response = $this->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testUnauthorizedActionResponse()


    /**
     * Test Authorized User response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create User.
        $traveller = $this->createUsers();

        // Execute api using Put Request Method.
        $url      = $this->getApiVersion().'/user/lastactive/update';
        $response = $this->actingAs($traveller[0])->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithAuthentication()


}//end class
