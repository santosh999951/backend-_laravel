<?php
/**
 * GetTravellerPrivePropertiesTest Test containing methods related to prive property listing api
 */

use App\Libraries\Helper;

/**
 * Class GetTravellerPrivePropertiesTest
 *
 * @group Owner
 */
class GetTravellerPrivePropertiesTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Test for Logout prive.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/prive/listings';

        // Not passing required params.
        $response = $this->get($url);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Property Listing Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties(1, 2);

         // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property['properties'][0]->id);

        // Get prive Property data.
        $url = $this->getApiVersion().'/prive/listings';

        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
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
                         'property_title',
                         'properties_images' => [
                             '*' => [
                                 'image',
                                 'caption',
                             ],

                         ],
                         'url',
                         'area',
                         'city',
                         'state',
                     ],
                 ],
                 'total_count',
                 'city' => [
                     '*' => [
                         'name',
                         'selected',
                     ],
                 ],

             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
