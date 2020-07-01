<?php
/**
 * PutUserDeleteTest Test containing methods related to Deactivate User Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class PutUserDeleteTest
 *
 * @group User
 */
class PutUserDeleteTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Unauthorized User response.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Create Demo Entry in user table for traveller .
        $traveller = $this->createUsers();

        $put_param = [
            'password' => base64_encode(111111),
        ];

        // Execute api using Put Request Method.
        $url      = $this->getApiVersion().'/user/delete';
        $response = $this->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test Missing Parameter with Authentication.
     *
     * @return void
     */
    public function testMissingParameterWithAuthentication()
    {
        // Create Demo Entry in user table for traveller .
        $traveller = $this->createUsers();

        $put_param = [];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/user/delete';
        $response = $this->actingAs($traveller[0])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testMissingParameterWithAuthentication()


    /**
     * Test Authorized User response with Request id.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Demo Entry in user table for traveller .
        $traveller = $this->createUsers();

        $put_param = [
            'password' => base64_encode(111111),
        ];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/user/delete';
        $response = $this->actingAs($traveller[0])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithAuthentication()


    /**
     * Test with invalid password.
     *
     * @return void
     */
    public function testForbiddenErrorResponseWithAuthentication()
    {
        // Create Demo Entry in user table for traveller .
        $traveller = $this->createUsers();

        $put_param = [
            'password' => base64_encode('UnitTesting'),
        ];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/user/delete';
        $response = $this->actingAs($traveller[0])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testForbiddenErrorResponseWithAuthentication()


}//end class
