<?php
/**
 * PostAppFeedbackTest Test containing methods related to Android/Ios Application Feedback Test case
 */

use Illuminate\Http\{Request};

/**
 * Class PostAppFeedbackTest
 *
 * @group User
 */
class PostAppFeedbackTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Missing Parameter with Authentication.
     *
     * @return void
     */
    public function testMissingParameterWithAuthentication()
    {
        $post_param = [];

        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/user/appfeedback';
        $response = $this->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testMissingParameterWithAuthentication()


    /**
     * Test Authorized User response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        $post_param = [
            'rating'  => 5,
            'message' => 'Unit Testing App Feedback',
        ];

        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/user/appfeedback';
        $response = $this->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseWithAuthentication()


}//end class
