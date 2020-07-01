<?php
/**
 * GetSimilarPropertiesTest Test containing methods related to Similar Property Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use Illuminate\Support\Facades;
use \Carbon\Carbon;

/**
 * Class GetSimilarPropertiesTest
 *
 * @group Property
 */
class GetSimilarPropertiesTest extends TestCase
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
     * Test With Login.
     *
     * @return void
     */
    public function testResponseWithAuthorization()
    {
        // Create Demo Entry in user table.
        $user = $this->createUsers();

        // Create New Property.
        $create_property_data = $this->createProperties();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Property Checkin Checkout dates.
        $checkin  = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->format('d-m-Y');
        $checkout = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('d-m-Y');

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/similar/'.$property_hash_id.'?guest='.$this->guests.'&units='.$this->units.'&checkin='.$checkin.'&checkout='.$checkout;
        $response = $this->actingAs($user[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

         // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithAuthorization()


    /**
     * Test Without Login.
     *
     * @return void
     */
    public function testResponseWithoutAuthorization()
    {
        // Create New Property.
        $create_property_data = $this->createProperties();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Property Checkin Checkout dates.
        $checkin  = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->format('d-m-Y');
        $checkout = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('d-m-Y');

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/similar/'.$property_hash_id.'?guest='.$this->guests.'&units='.$this->units.'&checkin='.$checkin.'&checkout='.$checkout;
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

         // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithoutAuthorization()


    /**
     * Test Disable Property .
     *
     * @return void
     */
    public function testResponseDisableProperty()
    {
        // Create New Property.
        $create_property_data = $this->createProperties(0);

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Property Checkin Checkout dates.
        $checkin  = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->format('d-m-Y');
        $checkout = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('d-m-Y');

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/similar/'.$property_hash_id.'?guest='.$this->guests.'&units='.$this->units.'&checkin='.$checkin.'&checkout='.$checkout;
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(404);

    }//end testResponseDisableProperty()


    /**
     * Test Invalid Property .
     *
     * @return void
     */
    public function testResponseBadRequest()
    {
        // Encode Property Hash id.
        $property_hash_id = 'ABCDEF';

        // Property Checkin Checkout dates.
        $checkin  = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->format('d-m-Y');
        $checkout = Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->format('d-m-Y');

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/property/similar/'.$property_hash_id.'?guest='.$this->guests.'&units='.$this->units.'&checkin='.$checkin.'&checkout='.$checkout;
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseBadRequest()


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
                'properties' => [
                    '*' => [
                        'property_id',
                        'property_hash_id',
                        'property_score',
                        'property_type_name',
                        'room_type_name',
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
                            'location_name',
                            'latitude',
                            'longitude'
                        ],
                        'accomodation',
                        'min_units_required',
                        'total_accomodation',
                        'is_liked_by_user',
                        'prices' => [
                            'display_discount',
                            'smart_discount' => [
                                'header',
                                'discount',
                                'footer',
                            ],
                            'final_currency' => [
                                'webicon',
                                'non-webicon',
                                'iso_code',
                            ],
                            'price_after_discount',
                            'price_after_discount_unformatted',
                            'price_before_discount'
                        ],
                        'payment_methods' => [
                            'instant_book',
                            'cash_on_arrival',
                        ],

                        'title',
                        'property_title',
                        'property_images' => [
                            '*' => [
                                'image',
                                'caption',
                            ],
                        ],
                        'property_videos_available',
                        'property_tags',
                        'url'
                    ],
                ],

            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
