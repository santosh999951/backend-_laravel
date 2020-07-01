<?php
/**
 * PostPropertyReviewImageTest Test containing methods related to Save Property Review Image Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use \Carbon\Carbon;

/**
 * Class PostPropertyReviewImageTest
 *
 * @group Property
 */
class PostPropertyReviewImageTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Unauthorized User response.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Request Hash Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $file_param = [
            'review_image' => $this->createImageObject('test_image.png'),
        ];

        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/property/review/image';
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
        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Request Hash Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $file_param = [];

        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/property/review/image';
        $response = $this->actingAs($booking_request_data['traveller'])->call('POST', $url, [], [], $file_param, $this->transformHeadersToServerVars(['HTTP_device-unique-id' => $this->getDeviceUniqueId()]));
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
        $booking_extra_param = [
            'from_date' => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 2))->toDateString(),
            'to_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 1))->toDateString(),
        ];

        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED, $booking_extra_param);

        // Request Hash Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $file_param = [
            'review_image' => $this->createImageObject('test_image.png'),
        ];

        // Execute api using Post Request Method.
        $url      = $this->getApiVersion().'/property/review/image';
        $response = $this->actingAs($booking_request_data['traveller'])->call('POST', $url, [], [], $file_param, $this->transformHeadersToServerVars(['HTTP_device-unique-id' => $this->getDeviceUniqueId()]));

        $response_content = json_decode($this->response->getContent());

        // Check status of response.
        $this->seeStatusCode(200);

        $this->assertObjectHasAttribute('picture', $response_content->data);

        $uploaded = base_path('public').'/review_images/temp_images/'.$response_content->data->picture;
        $this->assertFileExists($uploaded);
        array_map('unlink', glob(PROPERTY_REVIEW_IMAGE_TEMP_URL.'*'));

    }//end testValidResponseWithAuthentication()


}//end class
