<?php
/**
 * GetPrivePropertyTest Test containing methods related to prive property listing api
 */

use App\Libraries\Helper;

/**
 * Class GetPrivePropertyTest
 *
 * @group Owner
 */
class GetPrivePropertyTest extends TestCase
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
        $url = $this->getApiVersion().'/prive/property';

        // Not passing required params.
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

        // Get Prive Property data.
        $url      = $this->getApiVersion().'/prive/property';
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Property Listing Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);
        // Create Properties Data.
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);
        $create_property    = $this->createProperties();

        // Assign properties to prive owner.
        $assign_property_to_owner = $this->assignPropertyToOwner($create_prive_owner, $create_property['properties'][0]);

         // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property['properties'][0]->id);

        // Get prive Property data.
        $url = $this->getApiVersion().'/prive/property';

        $response = $this->actingAs($create_prive_owner)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


    /**
     * Property Listing Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithValuesAuthentication()
    {
         // Create Properties Data.
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);
        $create_property    = $this->createProperties(1, 2);
        $property_listings  = $create_property['properties'];
        foreach ($property_listings as $value) {
            $assign_property_to_owner = $this->assignPropertyToOwner($create_prive_owner, $value);
        }

           // Get property ids (unique) listed by prive.
        $property_ids = array_unique(array_column($property_listings, 'id'));
        foreach ($property_ids as $key => $value) {
            $properties_images[$value][0]['image']   = 'https://d39vbwyctxz5qa.cloudfront.net/assets/images/no_property.png';
            $properties_images[$value][0]['caption'] = '';
        }

         // Get first property image to display.
        foreach ($property_listings as $key => $one_property) {
            $property_hash_id                    = Helper::encodePropertyId($one_property['id']);
            $properties_data['property_hash_id'] = $property_hash_id;
            $properties_data['properties_images'] = $properties_images[$one_property['id']];
            $properties_data['property_title']    = 'Vila';
            $properties_data['url']               = WEBSITE_URL.'/rooms/'.$property_hash_id;
            $properties_data['units']             = '10';
            $properties_data['city']              = 'Vagator';
            $properties_data['id']                = $one_property['id'];
            $properties_data['state']             = 'Goa';
            $properties_data['status']            = 1;
            $properties_data['per_night_price']   = Helper::getFormattedMoney(13042, 'INR');
            $property_list[] = $properties_data;
        }

        // Get prive Property data.
        $url = $this->getApiVersion().'/prive/property';

        $response = $this->actingAs($create_prive_owner)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());
        $this->seeJsonEquals($this->getApiValues($property_list));

    }//end testValidResponseWithValuesAuthentication()


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
                         'units',
                         'city',
                         'id',
                         'state',
                         'per_night_price'
                     ],
                 ],
                 'total_count',

             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


    /**
     * Helper function to api response
     *
     * @param array $properties Properties data.
     *
     * @return array
     */
    private function getApiValues(array $properties)
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
        return [
            'status' => true,
            'data'   => [
                'properties'  => $properties,
                'total_count' => count($properties),
            ],
            'error'  => [],
        ];

    }//end getApiValues()


}//end class
