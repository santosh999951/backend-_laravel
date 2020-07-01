<?php
/**
 * GetPrePaymentTest Test containing methods related to Pre-Payment Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use \Carbon\Carbon;

/**
 * Class GetPrePaymentTest
 *
 * @group Prepayment
 */
class GetPrePaymentTest extends TestCase
{
    use App\Traits\FactoryHelper;

     /**
      * Default guest count
      *
      * @var integer
      */
    protected $guests = 1;

    /**
     * Default unit
     *
     * @var integer
     */
    protected $units = 1;

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
        // Create New Property.
        $create_property_data = $this->createProperties();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Property Checkin Checkout dates.
        $checkin  = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->format('d-m-Y');
        $checkout = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('d-m-Y');

        // Get Pre-payment detail response data.
        $url = $this->getApiVersion().'/prepayment/'.$property_hash_id.'?guest='.$this->guests.'&units='.$this->units.'&checkin='.$checkin.'&checkout='.$checkout.'&apply_prePaywallet='.$this->apply_wallet.'&payment_method='.$this->payment_method;

        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithoutAuthentication()


    /**
     * Test Data not found for disable property.
     *
     * @return void
     */
    public function testNotFoundResponseWithAuthentication()
    {
        // Create New Property.
        $create_property_data = $this->createProperties(0);

        // Create Traveller.
        $traveller = $this->createUsers();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Property Checkin Checkout dates.
        $checkin  = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->format('d-m-Y');
        $checkout = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('d-m-Y');

        // Get Pre-payment detail response data.
        $url = $this->getApiVersion().'/prepayment/'.$property_hash_id.'?guest='.$this->guests.'&units='.$this->units.'&checkin='.$checkin.'&checkout='.$checkout.'&apply_prePaywallet='.$this->apply_wallet.'&payment_method='.$this->payment_method;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(404);

    }//end testNotFoundResponseWithAuthentication()


    /**
     * Test Data not found for invalid property id .
     *
     * @return void
     */
    public function testBadRequestResponseWithAuthentication()
    {
        // Create Demo Entry in user table for traveller and user.
        $traveller = $this->createUsers();

        // Temp Hash id.
        $property_hash_id = 'ABCDEF';

        // Property Checkin Checkout dates.
        $checkin  = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->format('d-m-Y');
        $checkout = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('d-m-Y');

        // Get Pre-payment detail response data.
        $url = $this->getApiVersion().'/prepayment/'.$property_hash_id.'?guest='.$this->guests.'&units='.$this->units.'&checkin='.$checkin.'&checkout='.$checkout.'&apply_prePaywallet='.$this->apply_wallet.'&payment_method='.$this->payment_method;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Pre-Property Response with authenicated user.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create New Property.
        $create_property_data = $this->createProperties();

        // Create Traveller.
        $traveller = $this->createUsers();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Property Checkin Checkout dates.
        $checkin  = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->format('d-m-Y');
        $checkout = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('d-m-Y');

        // Get Peroperty detail response data.
        $url = $this->getApiVersion().'/prepayment/'.$property_hash_id.'?guest='.$this->guests.'&units='.$this->units.'&checkin='.$checkin.'&checkout='.$checkout.'&apply_prePaywallet='.$this->apply_wallet.'&payment_method='.$this->payment_method;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
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
                    'selected_units',
                    'selected_guests',
                    'min_nights',
                    'max_nights',
                    'available_units',
                    'guests_per_unit',
                    'instant_book',
                    'bookable_as_unit'
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
                        // 'label',
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
