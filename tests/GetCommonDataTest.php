<?php
/**
 * GetCommonDataTest Test containing methods related to commondata api
 */

use Illuminate\Http\{Request};
use Illuminate\Support\Facades;

/**
 * Class GetCommonDataTest
 *
 * @group Static_data
 */
class GetCommonDataTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Without Login.
     *
     * @return void
     */
    public function testResponseWithoutAuthorization()
    {
        $url      = $this->getApiVersion().'/common-data';
        $response = $this->get($url, []);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseWithoutAuthorization()


    /**
     * Test With Login.
     *
     * @return void
     */
    public function testResponseWithAuthorization()
    {
        $url      = $this->getApiVersion().'/common-data';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
                'app_version_check',
                'new_rating_days',
                'old_rating_days',
                'chat_available',
                'chat_call_text',
                'chat' => [],
                'spotlight_list' => [
                    '*' => [
                        'property_type_name',
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
                'autocomplete_api'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
