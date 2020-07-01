<?php
/**
 * PutBookingRequestTest Test containing methods related to Update Booking Request Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use \Carbon\Carbon;

/**
 * Class PutBookingRequestTest
 *
 * @group Request
 */
class PutBookingRequestTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test with logout user.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Create Property for booking.
        $booking_request_data = $this->createBookingRequests();
        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);
        $param           = [
            'payment_method' => 'full_payment',
            'payable_amount' => 18105.92,
        ];
        $url             = $this->getApiVersion().'/booking/request/'.$request_hash_id;
        $response        = $this->put($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test Login User response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Property for booking.
        $booking_request_data = $this->createBookingRequests(REQUEST_APPROVED);
        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);
        $user            = $booking_request_data['traveller'];
        $param           = [
            'payment_method' => 'full_payment',
            'payable_amount' => 18105.92,
        ];

        $url      = $this->getApiVersion().'/booking/request/'.$request_hash_id;
        $response = $this->actingAs($user)->put($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status code.
        $this->seeStatusCode(200);

    }//end testValidResponseWithAuthentication()


    /**
     * Test Invalid Hash Id.
     *
     * @return void
     */
    public function testInvalidHashId()
    {
        $user            = $this->createUsers();
        $booking_hash_id = 'ABCDEF';
        $param           = [
            'payment_method' => 'full_payment',
            'payable_amount' => 19214.49,
        ];

        $url      = $this->getApiVersion().'/booking/request/'.$booking_hash_id;
        $response = $this->actingAs($user[0])->put($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status code.
        $this->seeStatusCode(400);

    }//end testInvalidHashId()


    /**
     * Test Invalid Hash Id With Status New Request.
     *
     * @return void
     */
    public function testNewBookingRequest()
    {
        // Create Property for booking.
        $booking_request_data = $this->createBookingRequests();
        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $user = $booking_request_data['traveller'];

        $param = [
            'payment_method' => 'full_payment',
            'payable_amount' => 19214.49,
        ];

        $url      = $this->getApiVersion().'/booking/request/'.$request_hash_id;
        $response = $this->actingAs($user)->put($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status code.
        $this->seeStatusCode(404);

    }//end testNewBookingRequest()


    /**
     * Test Invalid Payable amount.
     *
     * @return void
     */
    public function testInvalidPayableAmount()
    {
         // Create Booking request.
        $booking_request_data = $this->createBookingRequests(REQUEST_APPROVED);
        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $user  = $booking_request_data['traveller'];
        $param = [
            'payment_method' => 'full_payment',
            'payable_amount' => 192141.49,
        ];

        $url      = $this->getApiVersion().'/booking/request/'.$request_hash_id;
        $response = $this->actingAs($user)->put($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status code.
        $this->seeStatusCode(400);

    }//end testInvalidPayableAmount()


    /**
     * Test testPaymentMehodNotAvailable.
     *
     * @return void
     */
    public function testPaymentMehodNotAvailable()
    {
        // Create Booking request.
        $booking_request_data = $this->createBookingRequests(REQUEST_APPROVED);
        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $user  = $booking_request_data['traveller'];
        $param = [
            'payment_method' => 'coa_payment',
            'payable_amount' => 192141.49,
        ];

        $url      = $this->getApiVersion().'/booking/request/'.$request_hash_id;
        $response = $this->actingAs($user)->put($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status code.
        $this->seeStatusCode(400);

    }//end testPaymentMehodNotAvailable()


    /**
     * Test testMissingParameter.
     *
     * @return void
     */
    public function testMissingParameter()
    {
        // Create Booking request.
        $booking_request_data = $this->createBookingRequests(REQUEST_APPROVED);
        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $user     = $booking_request_data['traveller'];
        $url      = $this->getApiVersion().'/booking/request/'.$request_hash_id;
        $response = $this->actingAs($user)->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status code.
        $this->seeStatusCode(400);

    }//end testMissingParameter()


}//end class
