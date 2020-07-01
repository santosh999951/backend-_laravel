<?php
/**
 * GetUserPropertiesTest Test containing methods related to User Profile Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use Illuminate\Support\Facades;

/**
 * Class GetUserPropertiesTest
 *
 * @group Property
 */
class GetUserPropertiesTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Without login
     *
     * @return void
     */
    public function testBadRequest()
    {
        $url      = $this->getApiVersion().'/user/properties';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequest()


    /**
     * Test With Login.
     *
     * @return void
     */
    public function testResponseWithAuthorization()
    {
        $user = $this->createUsers();
        // Create Demo Entry in user table.
        $properties_details = $this->createProperties();
        $property_id        = $properties_details['properties'][0]->id;
        $property_hash_id   = Helper::encodePropertyId($property_id);
        $host_id            = Helper::encodeUserId($properties_details['host']['id']);

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/user/properties?user_id='.$host_id;
        $response = $this->actingAs($user[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(200);

         // Match Reponse json with defined json.
         $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithAuthorization()


    /**
     * Test With testResponseBadHostId.
     *
     * @return void
     */
    public function testResponseBadHostId()
    {
        $user = $this->createUsers();
        // Create Demo Entry in user table.
        $host_id = strtoupper(str_random(6));

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/user/properties?user_id='.$host_id;
        $response = $this->actingAs($user[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseBadHostId()


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
