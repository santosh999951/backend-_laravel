<?php
/**
 * PostHostPaymentPreferencesTest Test containing methods related to Add Host Bank Details
 */

use App\Libraries\Helper;

/**
 * Class PostHostPaymentPreferencesTest
 *
 * @group Host
 */
class PostHostPaymentPreferencesTest extends TestCase
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
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
        $response = $this->actingAs($traveller[0])->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Test Invalid or Missing Parameter.
     *
     * @test
     * @return void
     */
    public function testInvalidOrMissingParametersResponseWithAuthentication()
    {
        // Create Properties Data.
        $create_property = $this->createProperties();

        $post_param = [
            'payee_name'     => 'Unit Testing',
            'bank_name'      => 'Test Bank',
            'branch_name'    => 'Testing',
            // Send Account Number Alphabet instead of numeric.
            'account_number' => 'Testing',
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/host/payment/preferences';

        $response = $this->actingAs($create_property['host'])->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

        $content = json_decode($this->response->getContent(), true);

        if (empty($content['error']) === false) {
            $this->assertEquals('validation_failed', $content['error'][0]['code']);
            $this->assertEquals('account_number', $content['error'][0]['key']);

            $this->assertEquals('validation_failed', $content['error'][1]['code']);
            $this->assertEquals('ifsc_code', $content['error'][1]['key']);
        } else {
            $this->assertTrue(false);
        }

    }//end testInvalidOrMissingParametersResponseWithAuthentication()


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

        $post_param = [
            'payee_name'     => 'Unit Testing',
            'bank_name'      => 'Test Bank',
            'branch_name'    => 'Testing',
            'account_number' => '111111111111',
            'ifsc_code'      => 'TESTTING',
        ];

        // Get host payment preference response data.
        $url = $this->getApiVersion().'/host/payment/preferences';

        $response = $this->actingAs($create_property['host'])->post($url, $post_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(201);
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
                 'message'

             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
