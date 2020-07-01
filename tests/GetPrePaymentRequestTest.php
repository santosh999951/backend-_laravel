<?php
/**
 * GetPrePaymentRequestTest Test containing methods related to Pre-Payment Request Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use \Carbon\Carbon;

/**
 * Class GetPrePaymentRequestTest
 *
 * @group Prepayment
 */
class GetPrePaymentRequestTest extends TestCase
{
    use App\Traits\FactoryHelper;

    /**
     * Default Apply wallet value
     *
     * @var integer
     */
    protected $apply_wallet = 0;

    /**
     * Default Payment Method
     *
     * @var string
     */
    protected $payment_method = 'full_payment';


    /**
     * PrePayment Response of logout user.
     *
     * @return void
     */
    public function testValidResponseWithoutAuthentication()
    {
        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        // Booking Checkin Checkout dates.
        $checkin  = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->format('d-m-Y');
        $checkout = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('d-m-Y');

        // Get Pre-payment Request  response data.
        $url = $this->getApiVersion().'/prepayment/request/'.$request_hash_id.'?checkin='.$checkin.'&checkout='.$checkout.'&apply_prePaywallet='.$this->apply_wallet.'&payment_method='.$this->payment_method;

        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testValidResponseWithoutAuthentication()


    /**
     * PrePayment Response of login user.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(REQUEST_APPROVED);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        // Booking Checkin Checkout dates.
        $checkin  = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->format('d-m-Y');
        $checkout = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('d-m-Y');

        // Get Pre-payment Request detail response data.
        $url = $this->getApiVersion().'/prepayment/request/'.$request_hash_id.'?checkin='.$checkin.'&checkout='.$checkout.'&apply_prePaywallet='.$this->apply_wallet.'&payment_method='.$this->payment_method;

        $response = $this->actingAs($booking_request_data['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(200);

        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


    /**
     * Test Data not found when login and booking request are of different user.
     *
     * @return void
     */
    public function testNotFoundResponseWithAuthentication()
    {
        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(REQUEST_APPROVED);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        // Create Traveller.
        $traveller = $this->createUsers();

        // Property Checkin Checkout dates.
        $checkin  = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->format('d-m-Y');
        $checkout = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('d-m-Y');

        // Get Pre-payment Request detail response data.
        $url = $this->getApiVersion().'/prepayment/request/'.$request_hash_id.'?checkin='.$checkin.'&checkout='.$checkout.'&apply_prePaywallet='.$this->apply_wallet.'&payment_method='.$this->payment_method;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check Status of response.
        $this->seeStatusCode(404);

    }//end testNotFoundResponseWithAuthentication()


    /**
     * Test Data not found for invalid Request id .
     *
     * @return void
     */
    public function testBadRequestResponseWithAuthentication()
    {
        // Create Demo Entry in user table for traveller and user.
        $traveller = $this->createUsers();

        // Temp request Hash id.
        $request_hash_id = 'ABCDEF';

        // Property Checkin Checkout dates.
        $checkin  = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->format('d-m-Y');
        $checkout = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('d-m-Y');

        // Get Pre-payment Request detail response data.
        $url = $this->getApiVersion().'/prepayment/request/'.$request_hash_id.'?checkin='.$checkin.'&checkout='.$checkout.'&apply_prePaywallet='.$this->apply_wallet.'&payment_method='.$this->payment_method;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


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
                'property_section' => [
                    'tile' => [
                        'property_id',
                        'property_hash_id',
                        'property_type',
                        'room_type',
                        'property_score',
                        'host_name',
                        'host_image',
                        'location' => [
                            'area',
                            'city',
                            'state',
                            'country' => [
                                'name',
                                'ccode',
                            ],
                            'location_name'  ,
                            'latitude',
                            'longitude',
                        ],
                        'title',
                        'property_title',
                        'property_images' => [
                            '*' => [
                                'image',
                                'caption',
                            ],
                        ],
                        'url'
                    ],
                    'start_date',
                    'end_date',
                    'required_units',
                    'guests',
                    'min_nights',
                    'max_nights',
                    'available_units',
                    'guests_per_unit',
                    'instant_book'
                ],
                'invoice' => [
                    'invoice_header' => [
                        '*' => [
                            'key',
                            'sub_key',
                            'value',
                            'show',
                        ],
                    ],
                    'invoice_middle' => [
                        '*' => [
                            'key',
                            'sub_key',
                            'value',
                            'show',
                        ],
                    ],
                    'invoice_footer' => [
                        '*' => [
                            'key',
                            'sub_key',
                            'value',
                            'show',
                               // 'bold',
                               // 'size'
                        ],
                    ],
                    'selected_payment_method',
                    'selected_payment_method_text',
                    'currency',
                    'currency_code'
                ],
                'payment_methods' => [
                    '*' => [
                        'key',
                        'title',
                        'description',
                        'sub_description',
                        'popup_text',
                        'payable_amount',
                        'payable_now',
                        'payable_later',
                        'payable_later_before',
                        'icon',
                    ],
                ],
                'discount_section' => [
                    'wallet'   => [
                        'wallet_money',
                        'applicable',
                        'wallet_currency_symbol',
                    ],
                    'coupon'   => ['applicable'],
                    'discount' => [
                        'discount_type',
                        'discount',
                        'discount_code',
                        'discount_message',
                        'discount_valid',
                    ],
                ],
                'footer_data' => [
                    'footer'    => [
                        'title',
                        'sub',
                        'button_text',
                        'final_amount',
                    ],
                    'left_div'  => [
                        'title',
                        'text',
                    ],
                    'right_div' => [
                        'title',
                        'text',
                    ],
                ],
                'user_section' => [
                    'is_mobile_verified',
                    'is_user_referred',
                ],
                'cancellation_section' => [
                    'cancellation_policy_info' => [],
                'url'

                ],
                'misconception',
                'misconception_code'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
