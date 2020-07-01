<?php
/**
 * GetPropertyPriceCalendarTest Test containing methods related to Property Pricing Calendar detail api
 */

use App\Libraries\Helper;

/**
 * Class GetPropertyPriceCalendarTest
 *
 * @group Property
 */
class GetPropertyPriceCalendarTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Test for Logout User.
     *
     * @return void
     */
    public function testValidResponseWithoutAuthentication()
    {
        // Create New Property.
        $create_property_data = $this->createProperties();

        // Create Inventory Pricing.
        $inventory_pricing = $this->createInventoryPricing($create_property_data['properties'][0]);

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Get Peroperty detail response data.
        $url = $this->getApiVersion().'/property/price/calendar/'.$property_hash_id;

        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithoutAuthentication()


    /**
     * Pricing Calendar Response with authenicated user.
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

        // Create Inventory Pricing.
        $inventory_pricing = $this->createInventoryPricing($create_property_data['properties'][0]);

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Get Peroperty detail response data.
        $url = $this->getApiVersion().'/property/price/calendar/'.$property_hash_id;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


    /**
     * Test Data not found for invalid property id .
     *
     * @return void
     */
    public function testDisabledResponseWithAuthentication()
    {
        // Create New Property.
        $create_property_data = $this->createProperties(0);

        // Create Traveller.
        $traveller = $this->createUsers();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Get Peroperty detail response data.
        $url = $this->getApiVersion().'/property/price/calendar/'.$property_hash_id;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testDisabledResponseWithAuthentication()


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

        // Get Peroperty detail response data.
        $url = $this->getApiVersion().'/property/price/calendar/'.$property_hash_id;

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
                'default'   => [
                    'currency',
                    'price',
                    'is_available',
                    'available_units',
                    'min_nights',
                    'max_nights',
                    'guests_per_unit',
                    'currency_plus_symbol',
                ],
                'exception' => [
                    '*' => [
                        'price',
                        'is_available',
                        'available_units',
                    ],
                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
