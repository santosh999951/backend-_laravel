<?php
/**
 * GetHostPaymentPreferencesTest Test containing methods related to Get Host Payment Preferences
 */

use App\Libraries\Helper;

/**
 * Class GetHostPaymentPreferencesTest
 *
 * @group Host
 */
class GetHostPaymentPreferencesTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Get host payment preference response data.
        $url      = $this->getApiVersion().'/host/payment/preferences';
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

        // Get host payment preference response data.
        $url      = $this->getApiVersion().'/host/payment/preferences';
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Host Payout Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties();

        // Create User Billing Info.
        $user_billing = $this->createUserBilling($create_property['host']->id);

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/host/payment/preferences';

        $response = $this->actingAs($create_property['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
                 'bank_details' => [
                     'payee_name',
                     'bank_name',
                     'branch_name',
                     'account_number',
                     'ifsc_code',
                 ],
             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
