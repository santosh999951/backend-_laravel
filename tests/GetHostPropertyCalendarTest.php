<?php
/**
 * GetHostPropertyCalendarTest Test containing methods related to Host property price calender api
 */

use App\Libraries\Helper;

/**
 * Class GetHostPropertyCalendarTest
 *
 * @group Host
 */
class GetHostPropertyCalendarTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Get property price calender  data.
        $url      = $this->getApiVersion().'/host/property/calendar';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test for Traveller.
     *
     * @return void
     */
    public function testInvalidResponseForTravellerWithAuthentication()
    {
        $traveller = $this->createUsers();

        // Get property price calender  data.
        $url      = $this->getApiVersion().'/host/property/calendar';
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Property price calender Response with authenicated user.

     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create properties.
        $create_property = $this->createProperties();

        // Create Inventory Pricing.
        $inventory_pricing = $this->createInventoryPricing($create_property['properties'][0]);

         // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property['properties'][0]->id);

        // Get host properties price calender  response data.
        $url = $this->getApiVersion().'/host/property/calendar/'.$property_hash_id;

        $response = $this->actingAs($create_property['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


     /**
      * Property price calender Response without property hash id.
      *
      * @test
      * @return void
      */
    public function testInvalidResponseWithAuthenticationWithoutPropertyId()
    {
        // Create properties.
        $create_property = $this->createProperties();

        // Get host properties price calender  response data.
        $url = $this->getApiVersion().'/host/property/calendar';

        $response = $this->actingAs($create_property['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testInvalidResponseWithAuthenticationWithoutPropertyId()


    /**
     * Property price calender Response without property hash id.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthenticationWrongPropertyId()
    {
        // Create properties.
        $create_property = $this->createProperties();

         // Encode Property Hash id.
        $property_hash_id = 'ABCDEF';

        // Get host properties price calender  response data.
        $url = $this->getApiVersion().'/host/property/calendar/'.$property_hash_id;

        $response = $this->actingAs($create_property['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testValidResponseWithAuthenticationWrongPropertyId()


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
                 'property_tile' => [
                     'property_hash_id',
                     'property_type_name',
                     'room_type_name',
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
                         'longitude',
                     ],
                     'accomodation',
                     'title',
                     'url',
                     'prices' => [
                         'currency' => [
                             'webicon',
                             'non-webicon',
                             'iso_code',
                         ],
                         'per_night_price'

                     ]
                 ],
                 'default'       => [
                     'price',
                     'extra_guest_cost',
                     'is_available',
                     'available_units',
                     'booked_units',
                     'blocked_units',
                     'instant_book',
                     'gh_commission',
                     'service_fee',
                     'markup_service_fee',
                 ],
                 'exception'     => [],
             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
