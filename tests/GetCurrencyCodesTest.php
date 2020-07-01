<?php
/**
 * GetCurrencyCodesTest Test containing methods related to currencycode Test case
 */

use Illuminate\Http\{Request};
use Illuminate\Support\Facades;

/**
 * Class GetCurrencyCodesTest
 *
 * @group Static_data
 */
class GetCurrencyCodesTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Without Login.
     *
     * @return void
     */
    public function testResponseWithoutAuthorization()
    {
        $url      = $this->getApiVersion().'/currencycodes';
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

        $url      = $this->getApiVersion().'/currencycodes';
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
                'selected_currency',
                'currency_codes' => [
                    '*' => [
                        'code',
                        'symbol',
                    ],
                ]
            ],

            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
