<?php
/**
 * GetSeamlessPaymentOptions contains test cases related to seamless payment options.
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use \Carbon\Carbon;

/**
 * Class GetSeamlessPaymentOptionsTest.
 */
class GetSeamlessPaymentOptionsTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Payment Options Response of logout user.
     *
     * @return void
     */
    public function testValidResponseWithoutAuthentication()
    {
        // Create Property for booking.
        $booking_request = $this->createBookingRequests();

        $booking_request_id_hash = Helper::encodeBookingRequestId($booking_request['booking_request']['id']);

        $url = $this->getApiVersion().'/booking/payment/options/'.$booking_request_id_hash;

        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        $this->seeStatusCode(401);

    }//end testValidResponseWithoutAuthentication()


    /**
     * Payment Options Response of login user with invalid booking request id.
     *
     * @return void
     */
    public function testValidResponseWithAuthenticationInvalidRequestId()
    {
        // Create Property for booking.
        $booking_request = $this->createBookingRequests();

        $booking_request_id_hash = Helper::encodeBookingRequestId($booking_request['booking_request']['id']).'123456';

        $url = $this->getApiVersion().'/booking/payment/options/'.$booking_request_id_hash;

        $response = $this->actingAs($booking_request['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(404);

        $content = json_decode($this->response->getContent(), true);

        if (isset($content['error'][0]['code']) === true && $content['error'][0]['code'] === EC_NOT_FOUND) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }

    }//end testValidResponseWithAuthenticationInvalidRequestId()


    /**
     * Payment Options Response of login user without request approved.
     *
     * @return void
     */
    public function testValidResponseWithAuthenticationRequestNotApproved()
    {
        // Create Property for booking.
        $booking_request = $this->createBookingRequests();

        $booking_request_id_hash = Helper::encodeBookingRequestId($booking_request['booking_request']['id']);

        $url = $this->getApiVersion().'/booking/payment/options/'.$booking_request_id_hash;

        $response = $this->actingAs($booking_request['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(404);

        // Check request exist in reponse.
        $content = json_decode($this->response->getContent(), true);

        if ($booking_request['booking_request']['booking_status'] === NEW_REQUEST && $content['error'][0]['code'] === EC_NOT_FOUND) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }

    }//end testValidResponseWithAuthenticationRequestNotApproved()


    /**
     * Payment Options Response of login user with request approved.
     *
     * @return void
     */
    public function testValidResponseWithAuthenticationRequestApproved()
    {
        // Create Property for booking with booking request approved.
        $booking_request = $this->createBookingRequests(REQUEST_APPROVED);

        $booking_request_id_hash = Helper::encodeBookingRequestId($booking_request['booking_request']['id']);

        $url = $this->getApiVersion().'/booking/payment/options/'.$booking_request_id_hash;

        $response = $this->actingAs($booking_request['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(200);

        // Check request exist in reponse.
        $content = json_decode($this->response->getContent(), true);

        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthenticationRequestApproved()


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
            'data' => [
                'action',
                'reason',
                'booking_status',
                'amount',
                'currency' => [
                    'webicon',
                    'non-webicon',
                    'iso_code',
                ],
                'payment_method',
                'options' => []
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
