<?php
/**
 * PutHostPaymentPreferencesTest Test containing methods related to Update Host Payment Preferences
 */

use App\Libraries\Helper;

/**
 * Class PutHostPaymentPreferencesTest
 *
 * @group Host
 */
class PutHostPaymentPreferencesTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Update host payment preference response data.
        $url      = $this->getApiVersion().'/host/payment/preferences';
        $response = $this->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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

        // Update host payment preference response data.
        $url      = $this->getApiVersion().'/host/payment/preferences';
        $response = $this->actingAs($traveller[0])->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
        $billing_id   = $user_billing['user_billing']->id;

        $param = [
            'billing_id'     => $billing_id,
            'payee_name'     => str_random(5),
            'bank_name'      => str_random(5),
            'branch_name'    => str_random(5),
            'account_number' => rand(111111, 111111111),
            'ifsc_code'      => str_random(7),
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/host/payment/preferences';

        $response = $this->actingAs($create_property['host'])->put($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


    /**
     * Host Payout Response with authenicated user With Invalid Parameter.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthenticationWithInvalidParameter()
    {
        // Create Properties Data.
        $create_property = $this->createProperties();

        // Create User Billing Info.
        $user_billing = $this->createUserBilling($create_property['host']->id);
        $billing_id   = $user_billing['user_billing']->id;

        $param = [
            'billing_id'     => $billing_id,
            'payee_name'     => str_random(2),
            'bank_name'      => str_random(5),
            'branch_name'    => str_random(5),
            'account_number' => rand(111111, 111111111),
            'ifsc_code'      => str_random(7),
        ];
        // Get host payment preference response data.
        $url = $this->getApiVersion().'/host/payment/preferences';

        $response = $this->actingAs($create_property['host'])->put($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testValidResponseWithAuthenticationWithInvalidParameter()


     /**
      * Host Payout Response with authenicated user With Invalid User.
      *
      * @test
      * @return void
      */
    public function testValidResponseWithAuthenticationWithInvalidUser()
    {
        // Create Properties Data.
        $create_property = $this->createProperties();

        // Create User Billing Info.
        $user_billing = $this->createUserBilling('10');
        $billing_id   = $user_billing['user_billing']->id;

        $param = [
            'billing_id'     => $billing_id,
            'payee_name'     => str_random(4),
            'bank_name'      => str_random(5),
            'branch_name'    => str_random(5),
            'account_number' => rand(111111, 111111111),
            'ifsc_code'      => str_random(7),
        ];
        // Get host payment preference response data.
        $url = $this->getApiVersion().'/host/payment/preferences';

        $response = $this->actingAs($create_property['host'])->put($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(400);

    }//end testValidResponseWithAuthenticationWithInvalidUser()


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
            'data' => ['message'],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
