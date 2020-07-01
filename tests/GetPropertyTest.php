<?php
/**
 * GetPropertyTest Test containing methods related to Property detail api
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use Illuminate\Support\Facades;
use \Carbon\Carbon;

/**
 * Class GetPropertyTest
 *
 * @group Property
 */
class GetPropertyTest extends TestCase
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
     * Test for Logout User.
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

        // Get Peroperty detail response data.
        $url = $this->getApiVersion().'/property/'.$property_hash_id.'?guest='.$this->guests.'&units='.$this->units.'&checkin='.$checkin.'&checkout='.$checkout;

        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithoutAuthentication()


    /**
     * Property Response with authenicated user.
     *
     * @test
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
        $url = $this->getApiVersion().'/property/'.$property_hash_id.'?guest='.$this->guests.'&units='.$this->units.'&checkin='.$checkin.'&checkout='.$checkout;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


    /**
     * Test Data Disabled Property response .
     *
     * @return void
     */
    public function testDisabledPropertyResponseWithAuthentication()
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

        // Get Peroperty detail response data.
        $url = $this->getApiVersion().'/property/'.$property_hash_id.'?guest='.$this->guests.'&units='.$this->units.'&checkin='.$checkin.'&checkout='.$checkout;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testDisabledPropertyResponseWithAuthentication()


     /**
      * Test Data not found for invalid request id .
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

        // Get Peroperty detail response data.
        $url = $this->getApiVersion().'/property/'.$property_hash_id.'?guest='.$this->guests.'&units='.$this->units.'&checkin='.$checkin.'&checkout='.$checkout;

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
                'id',
                'property_hash_id',
                'checkin',
                'checkout',
                'selected_guests',
                'selected_units',
                'required_units',
                'host_id',
                'host_name',
                'host_image',
                'property_title',
                'title',
                'review_count',
                'min_nights',
                'max_nights',
                'available_units',
                'guests_per_unit',
                'about',
                'usp',
                'description' => [
                    '*' => [
                        'key',
                        'title',
                        'value',
                    ],
                ],
                'how_to_reach' => [],
                'is_wishlisted',
                'property_score',
                // As even when all images are.
                // Empty, default images is always.
                // Coming.
                'property_images' => [
                    '*' => [
                        'image',
                        'caption',
                    ],
                ],
                'property_image_count',
                'property_video' => [],
                'location' => [
                    'area',
                    'city',
                    'state',
                    'country' => [
                        'name',
                        'ccode',
                    ],
                    'location_name',
                    'latitude',
                    'longitude'
                ],
                'property_pricing' => [
                    'currency' => [
                        'webicon',
                        'non-webicon',
                        'iso_code',
                    ],
                    'cleaning_price' ,
                    'per_night_price',
                    'per_night_price_unformatted',
                    'discount',
                    'original_price',
                    'per_night_per_guest_extra_guest_price',
                    'per_night_all_guest_extra_guest_price',
                    'is_instant_bookable',
                    'per_unit_guests'
                ],
                // Atleast full payment should
                // Come what ever be the
                // Case so expecting
                // Atleast one object.
                'payment_methods' => [
                    '*' => [
                        'key',
                        'value',
                    ],
                ],
                'selected_payment_method',
                'space' => [
                    '*' => [
                        'key',
                        'name',
                        'icon_id',
                    ],
                ],
                // Should always have cancellation policy.
                'cancellation_policy' => [
                    'id',
                    'title',
                    'policy_days',
                    'desc',
                    'popup_text',
                ],
                'tags' => [],
                'amenities' => [],
                'reviews' => [],
                // Keys will come, but empty in some cases.
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
                'similar_properties' => [],
                'meta' => [
                    'meta_title',
                    'canonical_url',
                    'meta_desc',
                    'meta_image',
                ],
                'bookable',
                'enabled',
                'status',
                'prive',
                'attraction_images' => []
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
