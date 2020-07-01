<?php
/**
 * PostUserPictureTest Test containing methods related to Save Property Review Image Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class PostUserPictureTest
 *
 * @group User
 */
class PostUserPictureTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Unauthorized User response.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        $file_param = [
            'picture' => $this->createImageObject('test_image.png'),
        ];

        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/user/picture';
        $response = $this->call('POST', $url, [], [], $file_param, $this->transformHeadersToServerVars(['HTTP_device-unique-id' => $this->getDeviceUniqueId()]));

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
        // Create User.
        $user = $this->createUsers();

        $file_param = [];

        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/user/picture';
        $response = $this->actingAs($user[0])->call('POST', $url, [], [], $file_param, $this->transformHeadersToServerVars(['HTTP_device-unique-id' => $this->getDeviceUniqueId()]));
        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testMissingParameterWithAuthentication()


    /**
     * Test Authorized User response with Request id.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create User.
        $user = $this->createUsers();

        $file_param = [
            'picture' => $this->createImageObject('test_image.png'),
        ];

        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/user/picture';
        $response = $this->actingAs($user[0])->call('POST', $url, [], [], $file_param, $this->transformHeadersToServerVars(['HTTP_device-unique-id' => $this->getDeviceUniqueId()]));

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithAuthentication()


}//end class
