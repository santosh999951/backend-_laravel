<?php
/**
 * GetHomeCollectionsDetailTest Test containing methods related to Collection Detail api
 */

use App\Libraries\Helper;

/**
 * Class GetHomeCollectionsDetailTest
 *
 * @group Home
 */
class GetHomeCollectionsDetailTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Test for Logout User.
     *
     * @return void
     */
    public function testValidResponseWithoutAuthentication()
    {
        // Create Collection data.
        $create_collection_data = $this->createCollection();

        // Encode Collection id.
        $collection_hash_id = Helper::encodeCollectionId($create_collection_data['collection']->id);

        // Get Collection detail response data.
        $url = $this->getApiVersion().'/home/collections/'.$collection_hash_id;

        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithoutAuthentication()


    /**
     * Test Data not found for invalid collection id .
     *
     * @return void
     */
    public function testNotFoundResponseWithAuthentication()
    {
        // Create Demo Entry in user table for traveller and user.
        $traveller = $this->createUsers();

        // Temp Collection Hash id.
        $collection_hash_id = 'VWEPB3';
        // Hash of 999999.
        // Get Collection detail response data.
        $url = $this->getApiVersion().'/home/collections/'.$collection_hash_id;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(404);

    }//end testNotFoundResponseWithAuthentication()


    /**
     * Collection Detail Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create New Collection.
        $create_collection_data = $this->createCollection();

        // Create Traveller.
        $traveller = $this->createUsers();

        // Encode Collection id.
        $collection_hash_id = Helper::encodeCollectionId($create_collection_data['collection']->id);

        // Get Collection detail response data.
        $url = $this->getApiVersion().'/home/collections/'.$collection_hash_id.'?offset=0&total=1';

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
                'collection' => [
                    'collection_id',
                    'collection_hash_id',
                    'collection_title',
                    'collection_image',
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
                    ]
                ],
                'meta'       => [
                    'canonical_url',
                    'meta_title',
                    'keyword',
                    'meta_desc',
                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
