<?php
/**
 * GetSeamlessPaymentPaylod contains test cases related to seamless payment payload details.
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use \Carbon\Carbon;

/**
 * Class GetSeamlessPaymentPayloadTest.
 */
class GetSeamlessPaymentPayloadTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Payment Payload Response of logout user.
     *
     * @return void
     */
    public function testValidResponseWithoutAuthentication()
    {
        // Create Property for booking.
        $booking_request = $this->createBookingRequests();

        $request_hash_id = Helper::encodeBookingRequestId($booking_request['booking_request']['id']);

        $payload_params = [
            'code'   => 'GH_VISA',
            'source' => 'web',
            'origin' => '',
        ];

        $url = $this->getApiVersion().'/booking/payment/payload/'.$request_hash_id.http_build_query($payload_params);

        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        $this->seeStatusCode(401);

    }//end testValidResponseWithoutAuthentication()


    /**
     * Payment Payload Response of login user with invalid booking request id.
     *
     * @return void
     */
    public function testValidResponseWithAuthenticationInvalidRequestId()
    {
        // Create Property for booking.
        $booking_request = $this->createBookingRequests();

        $booking_request_id_hash = Helper::encodeBookingRequestId($booking_request['booking_request']['id']).'123456';

        $url = $this->getApiVersion().'/booking/payment/payload/'.$booking_request_id_hash;

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
     * Payment Payload Response of login user without request approved.
     *
     * @return void
     */
    public function testValidResponseWithAuthenticationRequestNotApproved()
    {
        // Create Property for booking.
        $booking_request = $this->createBookingRequests();

        $booking_request_id_hash = Helper::encodeBookingRequestId($booking_request['booking_request']['id']);

        $url = $this->getApiVersion().'/booking/payment/payload/'.$booking_request_id_hash;

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
     * Payment Payload Response of login user.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Property for booking.
        $booking_request = $this->createBookingRequests(REQUEST_APPROVED);

        $booking_request_hash_id = Helper::encodeBookingRequestId($booking_request['booking_request']['id']);

        $url = $this->getApiVersion().'/booking/payment/payload/'.$booking_request_hash_id;

        $response = $this->actingAs($booking_request['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        $content = json_decode($this->response->getContent(), true);

        $this->seeStatusCode(200);
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
            'data' => [
                'payload' => [
                    'action',
                    'key',
                    'hash',
                    'txnid',
                    'amount',
                    'firstname',
                    'lastname',
                    'email',
                    'phone',
                    'productinfo',
                    'surl',
                    'furl',
                    'drop_category',
                    'bankcode',
                ],
                'extra_payload',
                'booking_info' => [
                    'booking_status',
                    'amount',
                    'currency',
                    'gateway',
                ]
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
