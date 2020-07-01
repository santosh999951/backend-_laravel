<?php
/**
 * GetOfflineDiscoverySearchTest Test containing methods related to PopularSearch Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use Illuminate\Support\Facades;

/**
 * Class GetPopularSearchTest
 *
 * @group Offline_discovery
 */
class GetOfflineDiscoverySearchTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Without login
     *
     * @return void
     */
    public function testResponseWithoutAuthorization()
    {
        $url      = $this->getApiVersion().'/offlinediscovery/search';
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

        $search_params = ['property_name' => 'new test'];

        $url = $this->getApiVersion().'/offlinediscovery/search?'.http_build_query($search_params);

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
                'property_list' => [
                    '*' => [
                        'status',
                        'image',
                        'contact',
                        'id',
                        'name',
                        'title',
                        'area',
                        'city',
                        'state',
                        'country',
                    ],

                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
