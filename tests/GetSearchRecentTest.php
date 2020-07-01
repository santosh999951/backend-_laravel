<?php
/**
 * GetSearchRecentTest Test containing methods related to Trip List Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class GetSearchRecentTest
 *
 * @group Search
 */
class GetSearchRecentTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test with logout user.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        $url      = $this->getApiVersion().'/search/recent';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test Login User response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create New Property.
        $create_property_data = $this->createProperties();

        // Create Traveller.
        $traveller = $this->createUsers();

        // Create Recently View data.
        $property_view = $this->createPropertyView($create_property_data['properties'][0], $traveller[0]);

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        $url      = $this->getApiVersion().'/search/recent';
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Check request exist in reponse.
        $content = json_decode($this->response->getContent(), true);

        if (empty($content['data']['recently_viewed_properties']) === false) {
            $this->assertEquals($property_hash_id, $content['data']['recently_viewed_properties'][0]['property_hash_id']);
        } else {
            $this->assertTrue(false);
        }

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
                'recently_viewed_properties' => [
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
                            'price_before_discount',
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
                        'property_tags' => [],
                        'url'
                    ],
                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
