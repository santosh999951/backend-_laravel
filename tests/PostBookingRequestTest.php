<?php
/**
 * PostBookingRequestTest Test containing methods related to Post Booking Request Test case
 */

use App\Libraries\Helper;
use \Carbon\Carbon;
use App\Events\CreateBookingRequest;

/**
 * Class PostBookingRequestTest
 *
 * @group Request
 */
class PostBookingRequestTest extends TestCase
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
        $properties       = $this->createProperties();
        $property_hash_id = Helper::encodePropertyId($properties['properties'][0]->id);
        $param            = [
            'checkin'          => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->toDateString(),
            'checkout'         => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->toDateString(),
            'property_hash_id' => $property_hash_id,
            'guests'           => 1,
            'units'            => 1,
            'payment_method'   => 'full_payment',
            'payable_amount'   => 18105.92,
        ];
        $url              = $this->getApiVersion().'/booking/request';
        $response         = $this->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test Login User response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Check event is fired.
        Event::fake();

        $user             = $this->createUsers();
        $properties       = $this->createProperties();
        $property_hash_id = Helper::encodePropertyId($properties['properties'][0]->id);
        $param            = [
            'checkin'          => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->toDateString(),
            'checkout'         => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->toDateString(),
            'property_hash_id' => $property_hash_id,
            'guests'           => 1,
            'units'            => 1,
            'payment_method'   => 'full_payment',
            'payable_amount'   => 18105.92,
        ];

        $url      = $this->getApiVersion().'/booking/request';
        $response = $this->actingAs($user[0])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status code.
        $this->seeStatusCode(200);

        // // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        $content = json_decode($this->response->getContent());

        Event::assertDispatched(
            CreateBookingRequest::class,
            function ($event) use ($content) {
                return ($event->booking_request->id === Helper::decodeBookingRequestId($content->data->request_id));
            }
        );

    }//end testValidResponseWithAuthentication()


    /**
     * Test Invalid Hash Id.
     *
     * @return void
     */
    public function testInvalidHashId()
    {
        $user             = $this->createUsers();
        $properties       = $this->createProperties();
        $property_hash_id = Helper::encodePropertyId($properties['properties'][0]->id);
        $param            = [
            'checkin'          => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->toDateString(),
            'checkout'         => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->toDateString(),
            'property_hash_id' => 'ABCDEF',
            'guests'           => 1,
            'units'            => 1,
            'payment_method'   => 'full_payment',
            'payable_amount'   => 18105.92,
        ];

        $url      = $this->getApiVersion().'/booking/request';
        $response = $this->actingAs($user[0])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status code.
        $this->seeStatusCode(400);

    }//end testInvalidHashId()


    /**
     * Test Non Verified Mobile.
     *
     * @return void
     */
    public function testNonVerifiedMobile()
    {
        $user             = $this->createUsers(1, ['mobile_verify' => 0]);
        $properties       = $this->createProperties();
        $property_hash_id = Helper::encodePropertyId($properties['properties'][0]->id);
        $param            = [
            'checkin'          => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->toDateString(),
            'checkout'         => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->toDateString(),
            'property_hash_id' => $property_hash_id,
            'guests'           => 1,
            'units'            => 1,
            'payment_method'   => 'full_payment',
            'payable_amount'   => 18105.92,
        ];

        $url      = $this->getApiVersion().'/booking/request';
        $response = $this->actingAs($user[0])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status code.
        $this->seeStatusCode(403);

    }//end testNonVerifiedMobile()


    /**
     * Test Property not found.
     *
     * @return void
     */
    public function testPropertyNotFound()
    {
        $user             = $this->createUsers();
        $properties       = $this->createProperties(0);
        $property_hash_id = Helper::encodePropertyId($properties['properties'][0]->id);
        $param            = [
            'checkin'          => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->toDateString(),
            'checkout'         => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->toDateString(),
            'property_hash_id' => $property_hash_id,
            'guests'           => 1,
            'units'            => 1,
            'payment_method'   => 'full_payment',
            'payable_amount'   => 18105.92,
        ];

        $url      = $this->getApiVersion().'/booking/request';
        $response = $this->actingAs($user[0])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status code.
        $this->seeStatusCode(404);

    }//end testPropertyNotFound()


    /**
     * Test Invalid Payable amount.
     *
     * @return void
     */
    public function testInvalidPayableAmount()
    {
        $user             = $this->createUsers();
        $properties       = $this->createProperties();
        $property_hash_id = Helper::encodePropertyId($properties['properties'][0]->id);
        $param            = [
            'checkin'          => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->toDateString(),
            'checkout'         => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->toDateString(),
            'property_hash_id' => $property_hash_id,
            'guests'           => 1,
            'units'            => 1,
            'payment_method'   => 'full_payment',
            'payable_amount'   => 192141.49,
        ];

        $url      = $this->getApiVersion().'/booking/request';
        $response = $this->actingAs($user[0])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
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
        $user             = $this->createUsers();
        $properties       = $this->createProperties();
        $property_hash_id = Helper::encodePropertyId($properties['properties'][0]->id);
        $param            = [
            'checkin'          => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->toDateString(),
            'checkout'         => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->toDateString(),
            'property_hash_id' => $property_hash_id,
            'guests'           => 1,
            'units'            => 1,
            'payment_method'   => 'coa_payment',
            'payable_amount'   => 192141.49,
        ];

        $url      = $this->getApiVersion().'/booking/request';
        $response = $this->actingAs($user[0])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
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
        $user     = $this->createUsers();
        $url      = $this->getApiVersion().'/booking/request';
        $response = $this->actingAs($user[0])->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status code.
        $this->seeStatusCode(400);

    }//end testMissingParameter()


    /**
     * Test testOldRequest.
     *
     * @return void
     */
    public function testOldRequest()
    {
        // $user = $this->createUsers();
        $booking_request  = $this->createBookingRequests();
        $property_hash_id = Helper::encodePropertyId($booking_request['properties']['id']);
        $user             = $booking_request['traveller'];
        $param            = [
            'checkin'          => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->toDateString(),
            'checkout'         => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->toDateString(),
            'property_hash_id' => $property_hash_id,
            'guests'           => 1,
            'units'            => 1,
            'payment_method'   => 'full_payment',
            'payable_amount'   => 18105.92,
            'force_create'     => 0,
        ];

        $url      = $this->getApiVersion().'/booking/request';
        $response = $this->actingAs($user)->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status code.
        $this->seeStatusCode(200);

    }//end testOldRequest()


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
                'valid',
                'message',
                'msg_code',
                'booking_status',
                'request_id',
                'instant_book',
                'payment_url',
                'payment_gateway_method',
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
