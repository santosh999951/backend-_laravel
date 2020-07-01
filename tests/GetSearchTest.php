<?php
/**
 * GetSearchTest Test containing methods related to Search Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class GetSearchTest
 *
 * @group Search
 */
class GetSearchTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test with logout user.
     *
     * @return void
     */
    public function testValidResponseWithoutAuthentication()
    {
        $properties = $this->createProperties();

        $search_params = [
            'lat' => '28.4594965',
            'lng' => '77.02663830000006',
        ];

        $url      = $this->getApiVersion().'/search?'.http_build_query($search_params);
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->_getApiStructureWithOnlyDefaultValues($this->_getPropertyListStructure()));

    }//end testValidResponseWithoutAuthentication()


    /**
     * Test Login User response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Properties.
        $properties = $this->createProperties();

        // Create User.
        $traveller = $this->createUser();

        $search_params = [
            'lat' => '28.4594965',
            'lng' => '77.02663830000006',
        ];

        $url      = $this->getApiVersion().'/search?'.http_build_query($search_params);
        $response = $this->actingAs($traveller)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->_getApiStructureWithOnlyDefaultValues($this->_getPropertyListStructure()));

    }//end testValidResponseWithAuthentication()


    /**
     * Test Stay page response.
     *
     * @return void
     */
    public function testNotFoundStayPageWithAuthentication()
    {
        // Create Properties.
        $properties = $this->createProperties();

        // Create User.
        $traveller = $this->createUser();

        $search_params = ['slug' => 'stay-in-goa-and-manali'];

        $url      = $this->getApiVersion().'/search?'.http_build_query($search_params);
        $response = $this->actingAs($traveller)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(404);

    }//end testNotFoundStayPageWithAuthentication()


    /**
     * Test Stay page response.
     *
     * @return void
     */
    public function testValidResponseStayPageWithAuthentication()
    {
        // Create Properties.
        $properties = $this->createProperties();

        // Create User.
        $traveller = $this->createUser();

        $search_params = ['slug' => 'stay-in-goa'];

        $url      = $this->getApiVersion().'/search?'.http_build_query($search_params);
        $response = $this->actingAs($traveller)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->_getApiStructureWithOnlyDefaultValues($this->_getPropertyListStructure()));

    }//end testValidResponseStayPageWithAuthentication()


    /**
     * Test Stay page response.
     *
     * @return void
     */
    public function testNotFoundResponseWithOtherStayPageWithAuthentication()
    {
        // Create Properties.
        $properties = $this->createProperties();

        // Create User.
        $traveller = $this->createUser();

        $search_params = ['slug' => 'villa-in-goa'];

        $url      = $this->getApiVersion().'/search?'.http_build_query($search_params);
        $response = $this->actingAs($traveller)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(404);

    }//end testNotFoundResponseWithOtherStayPageWithAuthentication()


    /**
     * Test Stay page response.
     *
     * @return void
     */
    public function testValidResponseWithOtherStayPageWithAuthentication()
    {
        // Create Properties.
        $properties = $this->createProperties();

        // Create User.
        $traveller = $this->createUser();

        $search_params = ['slug' => 'villas-in-goa'];

        $url      = $this->getApiVersion().'/search?'.http_build_query($search_params);
        $response = $this->actingAs($traveller)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->_getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithOtherStayPageWithAuthentication()


     /**
      * Helper function to api structure
      *
      * @param array $property_structure Property Structure.
      *
      * @return array
      */
     // phpcs:ignore
    private function _getApiStructureWithOnlyDefaultValues(array $property_structure=[])
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
        return [
            'status',
            'data' => [
                'filters' => [
                    'min_budget',
                    'max_budget',
                    'slider_min_value',
                    'slider_max_value',
                    'budget_currency' => [
                        'webicon',
                        'non-webicon',
                        'iso_code',
                    ],
                    'property_types' => [
                        '*' => [
                            'id',
                            'name',
                            'link',
                            'show',
                            'selected',
                        ],
                    ],
                    'location_tags' => [],
                    'search_location' => [],
                    'popular_similar_locations' => [],
                    'amenities' => [
                        '*' => [
                            'id',
                            'amenity_name',
                        ],
                    ],
                    'checkin',
                    'checkout',
                    'guests'
                ],
                'properties_list' => $property_structure,
                'total_properties_count',
                'search_address_data' => [
                    'area',
                    'city',
                    'state',
                    'country',
                    'country_name',
                    'lat',
                    'search_keyword' => [],
                    'location'
                ],
                'filter_cards_in_properties' => [
                    'filter_card_type',
                    'filter_card_repetition' => []
                ],
                'promo_banners' => [],
                'seo_content' => [],
                'meta' => []
            ],
            'error'
        ];

    }//end _getApiStructureWithOnlyDefaultValues()


    /**
     * Helper function to property list structure
     *
     * @return array
     */
    // phpcs:ignore
    private function _getPropertyListStructure()
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
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

    }//end _getPropertyListStructure()


}//end class
