<?php
/**
 * PostOfflineDiscoveryUploadLeadImageTest Test containing methods related to Save Property Review Image Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class PostOfflineDiscoveryUploadLeadImageTest
 *
 * @group Offline_discovery
 */
class PostOfflineDiscoveryUploadLeadImageTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Unauthorized User response.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        $lead = $this->createConversionLead();

        $image      = $this->createImageObject('test_image.png');
        $file_param = [
            'lead_id' => $lead[0]->id,
        ];

        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/offlinediscovery/leaduploadimage';
        $response = $this->call('POST', $url, $file_param, [], ['lead_image' => $image], $this->transformHeadersToServerVars(['HTTP_device-unique-id' => $this->getDeviceUniqueId()]));

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
        // Create Lead.
        $user = $this->createUser();

        $file_param = [];

        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/offlinediscovery/leaduploadimage';
        $response = $this->actingAs($user)->call('POST', $url, [], [], $file_param, $this->transformHeadersToServerVars(['HTTP_device-unique-id' => $this->getDeviceUniqueId()]));
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
        $user = $this->createUser();

        // Create Lead.
        $lead = $this->createConversionLead();

        $image      = $this->createImageObject('test_image.png');
        $file_param = [
            'lead_id' => $lead[0]->id,
        ];

        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/offlinediscovery/leaduploadimage';
        $response = $this->actingAs($user)->call('POST', $url, $file_param, [], ['lead_image' => $image], $this->transformHeadersToServerVars(['HTTP_device-unique-id' => $this->getDeviceUniqueId()]));
        $content  = json_decode($response, true);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithAuthentication()


}//end class
