<?php
/**
 * GetCountryCodesTest Test containing methods related to countrycode Test case
 */

use Illuminate\Http\{Request};
use Illuminate\Support\Facades;

/**
 * Class GetCountryCodesTest
 *
 * @group Offline_discovery
 */
class GetOfflineDiscoveryLeadFormListTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Without Login.
     *
     * @return void
     */
    public function testResponseWithoutAuthorization()
    {
        $url      = $this->getApiVersion().'/offlinediscovery/leadformlist';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testResponseWithoutAuthorization()


    /**
     * Test With Login.
     *
     * @return void
     */
    public function testResponseWithAuthorization()
    {
        // Create Demo Entry in user table.
        $user = $this->createUser();

        $url = $this->getApiVersion().'/offlinediscovery/leadformlist';

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $response = $this->actingAs($user)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
         // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithAuthorization()


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
                'property_list'       => [
                    '*' => [
                        'id',
                        'name',
                        'selected',
                    ],
                ],
                'room_type_list'      => [
                    '*' => [
                        'id',
                        'name',
                        'selected',
                    ],
                ],
                'amenities'           => [
                    '*' => [
                        'id',
                        'cat_id',
                        'category_name',
                        'amenity_name',
                    ],
                ],
                'cancellation_policy' => [
                    '*' => [
                        'id',
                        'title',
                        'selected',
                    ],
                ],
                'message'
            ],

            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
