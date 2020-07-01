<?php
/**
 * GetReferDetailsTest Test containing methods related to Referal code Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use Illuminate\Support\Facades;

/**
 * Class GetReferDetailsTest
 *
 * @group User
 */
class GetReferDetailsTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Without Referal Code
     *
     * @return void
     */
    public function testBadRequest()
    {
        $url      = $this->api_version.'/user/getrefererdetails?referral_code=';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequest()


    /**
     * Test With Wrong Referal Code.
     *
     * @return void
     */
    public function testResponseWrongReferalCode()
    {
        $user_referal_code = strtoupper(str_random(3));

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->api_version.'/user/getrefererdetails?referral_code='.$user_referal_code;
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseWrongReferalCode()


    /**
     * Test With accurate Referal Code.
     *
     * @return void
     */
    public function testResponseWithReferalCode()
    {
        // Create Demo Entry in user table.
        $user = $this->createUsers();

        $user_referal_code = $user[0]['referral_code'];

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->api_version.'/user/getrefererdetails?referral_code='.$user_referal_code;
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

         // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithReferalCode()


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
                'user_name',
                'user_image',
                'referral_code',
                'brief',
                'detail',
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
