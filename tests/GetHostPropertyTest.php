<?php
/**
 * GetHostPropertyTest Test containing methods related to Host property listing api
 */

use App\Libraries\Helper;

/**
 * Class GetHostPropertyTest
 *
 * @group Host
 */
class GetHostPropertyTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Get Host Property data.
        $url      = $this->getApiVersion().'/host/property';
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

        // Get Host Property data.
        $url      = $this->getApiVersion().'/host/property';
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Property Listing Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties();

         // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property['properties'][0]->id);

        // Get Host Property data.
        $url = $this->getApiVersion().'/host/property';

        $response = $this->actingAs($create_property['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
                 'properties'     => [
                     '*' => [
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
                             'location_name',
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
                         'url',
                         'last_updated',
                         'show_manage_calender',
                         'property_status',
                         'property_enable',
                         'prices' => [
                             'currency' => [
                                 'webicon',
                                 'non-webicon',
                                 'iso_code',
                             ],
                             'per_night_price'
                         ]
                     ],
                 ],
                 'cities'         => [],
                 'status_list'    => [],
                 'property_types' => [],

             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
