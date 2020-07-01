<?php
/**
 * PutUserCurrencyTest Test containing methods related to Update User Currency Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class PutUserCurrencyTest
 *
 * @group User
 */
class PutUserCurrencyTest extends TestCase
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

        $put_param = ['currency' => 'INR'];

        // Execute api using Put Request Method.
        $url      = $this->getApiVersion().'/user/currency';
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
        $url      = $this->getApiVersion().'/user/currency';
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

        $put_param = ['currency' => 'INR'];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/user/currency';
        $response = $this->actingAs($traveller[0])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithAuthentication()


}//end class
