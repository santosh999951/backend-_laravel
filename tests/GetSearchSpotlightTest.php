<?php
/**
 * GetSearchSpotlightTest Test containing methods related to Serach Spotlight Test case
 */

use Illuminate\Http\{Request};
use Illuminate\Support\Facades;

/**
 * Class GetSearchSpotlightTest
 *
 * @group Search
 */
class GetSearchSpotlightTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Without login
     *
     * @return void
     */
    public function testResponseWithoutAuthorization()
    {
        $url      = $this->getApiVersion().'/search/spotlight';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(200);

         // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithoutAuthorization()


    /**
     * Test With Login.
     *
     * @return void
     */
    public function testResponseWithAuthorization()
    {
        // Create Demo Entry in user table.
        $user = $this->createUsers();

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/search/spotlight';
        $response = $this->actingAs($user[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
                '*' => [
                      // 'property_type_name',
                    'property_type',
                    'state',
                    'tag',
                    'title',
                    'city',
                    'country',
                    'country_name',
                    'v_lat',
                    'v_lng',
                    'image_url',
                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
