<?php
/**
 * GetPriveManagerPropertiesTest Test containing methods related to Prive manager property listing api
 */

use App\Libraries\Helper;

/**
 * Class GetPriveManagerPropertiesTest
 *
 * @group Manager
 */
class GetPriveManagerPropertiesTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Prive manager.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Get Host Property data.
        $url      = $this->getApiVersion().'/prive/manager/property';
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
        $url      = $this->getApiVersion().'/prive/manager/property';
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

        // Create Prive Manager.
        $create_prive_manager = $this->createUser(['prive_manager' => 1]);

        // Assign permission to view booking.
        $this->assignPermissionToUser($create_prive_manager, ['booking-view#operations']);

        // Assign property to manager.
        $this->assignPropertyToManager($create_prive_manager, $create_property['properties'][0]);

        // Get Host Property data.
        $url = $this->getApiVersion().'/prive/manager/property';

        $response = $this->actingAs($create_prive_manager)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
                 'properties' => [
                     '*' => [
                         'property_hash_id',
                         'property_images' => [],
                         'property_title',
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
                         'url',
                         'units',
                         'per_night_price',
                         'occupancy_rate',
                     ],
                 ],
                 'total'
             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
