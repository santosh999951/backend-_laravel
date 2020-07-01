<?php
/**
 * GetHomeTest Test containing methods related to Home api
 */

use App\Libraries\Helper;

/**
 * Class GetHomeTest
 *
 * @group Home
 */
class GetHomeTest extends TestCase
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

        // Create Home Banner data.
        $create_banner_data = $this->createHomeBanner();

        // Create Home Widget data.
        $create_widget_data = $this->createHomeWidget();

        // Get Home response data.
        $url      = $this->getApiVersion().'/home';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithoutAuthentication()


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

        // Create Home Banner data.
        $create_banner_data = $this->createHomeBanner();

        // Create Home Widget data.
        $create_widget_data = $this->createHomeWidget();

        // Create Traveller.
        $traveller = $this->createUsers();

        // Create Recently View data.
        $property_view = $this->createPropertyView($create_collection_data['property'], $traveller[0]);

        // Get Collection detail response data.
        $url = $this->getApiVersion().'/home';

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        $user_structure          = $this->getUserDataStructureInApiWithAuthentication();
        $recently_view_structure = $this->getRecentlyViewDataStructureInApiWithAuthentication();
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues($user_structure, $recently_view_structure));

    }//end testValidResponseWithAuthentication()


     /**
      * Helper function to api structure
      *
      * @param array $user_structure                       User data structure in home api.
      * @param array $recently_viewed_properties_structure Recently Viewed Property data structure in home api.
      *
      * @return array
      */
    private function getApiStructureWithOnlyDefaultValues(array $user_structure=[], array $recently_viewed_properties_structure=[])
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
        return [
            'status',
            'data' => [
                'user' => $user_structure,
                'popular_cities' => [
                    '*' => [
                        'country' => [
                            'name',
                            'ccode',
                        ],
                        'state',
                        'city',
                        'latitude',
                        'longitude',
                        'property_images' => [],
                        'tag',
                        'title',
                        'home_count',
                        'url'
                    ],
                ],
                'home_videos' => [
                    '*' => [
                        'url',
                        'type',
                    ],
                ],
                'collections' => [
                    '*' => [
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
                ],
                'recently_viewed_properties' => $recently_viewed_properties_structure,
                'new_and_approved_booking_requests_count',
                'home_explore_content' => [
                    'heading',
                    'cities_sub_heading',
                ],
                'home_banner' => [
                    '*' => [
                        'id',
                        'notification_type',
                        'location',
                        'country',
                        'country_name',
                        'state',
                        'city',
                        'latitude',
                        'longitude',
                        'search_keyword' => [],
                        'check_in',
                        'check_out',
                        'guests',
                        'min_budget',
                        'max_budget',
                        'currency' => [
                            'webicon',
                            'non-webicon',
                            'iso_code',
                        ],
                        'property_type' => [],
                        'room_type' => [],
                        'amenities' => [],
                        'bedroom',
                        'tag',
                        'property_hash_id',
                        'keyword',
                        'title',
                        'promo',
                        'utm_source',
                        'utm_campaign',
                        'utm_medium',
                        'heading',
                        'property_images' => [],
                        'content',
                        'url'
                    ],
                ],
                'chat_available',
                'chat_call_text',
                'offer' => [
                    'title',
                    'desc' => [],
                    'img_url',
                    'meta' => [
                        'canonical_url',
                        'meta_title',
                        'keyword',
                        'meta_desc',
                    ]
                ],
                'meta' => [
                    'canonical_url',
                    'meta_title',
                    'keyword',
                    'meta_desc',
                ]
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


    /**
     * Helper function to user data structure
     *
     * @return array
     */
    private function getUserDataStructureInApiWithAuthentication()
    {
        return [
            'wallet' => [
                'wallet_balance',
                'wallet_currency_symbol' => [
                    'iso_code',
                    'non-webicon',
                    'webicon',
                ]
            ],
        ];

    }//end getUserDataStructureInApiWithAuthentication()


    /**
     * Helper function to Recently Viewed Structure
     *
     * @return array
     */
    private function getRecentlyViewDataStructureInApiWithAuthentication()
    {
        return [
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
        ];

    }//end getRecentlyViewDataStructureInApiWithAuthentication()


}//end class
