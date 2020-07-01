<?php
/**
 * GetCountryCodesTest Test containing methods related to countrycode Test case
 */

use Illuminate\Http\{Request};
use Illuminate\Support\Facades;

/**
 * Class GetCountryCodesTest
 *
 * @group Static_data
 */
class GetCountryCodesTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Without Login.
     *
     * @return void
     */
    public function testResponseWithoutAuthorization()
    {
        $url      = $this->getApiVersion().'/countrycodes';
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

        $url      = $this->getApiVersion().'/countrycodes';
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
                'country_codes' => [
                    '*' => [
                        'country_code',
                        'country_name',
                    ],
                ],
            ],

            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
