<?php
/**
 * PutHostPropertyStatusTest Test containing methods related to Host Booking Status Test case
 */

use App\Libraries\Helper;

/**
 * Class PutHostPropertyStatusTest
 *
 * @group Host
 */
class PutHostPropertyStatusTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Create Property Data.
        $create_property_data = $this->createProperties();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Form data parameters.
        $put_param = [
            'property_hash_id' => $property_hash_id,
            'property_status'  => 0,
        // Update property to offline.
        ];

        // Get response data.
        $url = $this->getApiVersion().'/host/property/status';
        $this->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
        // Create Property Data.
        $create_property_data = $this->createProperties();

        // Create User.
        $traveller = $this->createUser();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Form data parameters.
        $put_param = [
            'property_hash_id' => $property_hash_id,
            'property_status'  => 0,
        // Update property to offline.
        ];

        // Get response data.
        $url = $this->getApiVersion().'/host/property/status';

        $response = $this->actingAs($traveller)->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Test Data not found for invalid request id .
     *
     * @return void
     */
    public function testNotFoundResponseWithAuthentication()
    {
        // Create Property Data.
        $create_property_data = $this->createProperties();

        // Encode Property Hash id.
        $property_hash_id = 'ABCDEF';

        // Form data parameters.
        $put_param = [
            'property_hash_id' => $property_hash_id,
            'property_status'  => 0,
        // Update property to offline.
        ];

        // Get response data.
        $url = $this->getApiVersion().'/host/property/status';

        $response = $this->actingAs($create_property_data['host'])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testNotFoundResponseWithAuthentication()


    /**
     * Response due to Invalid Parameters with authenicated user.
     *
     * @test
     * @return void
     */
    public function testInvalidResponseWithInvalidParametersWithAuthentication()
    {
        // Create Property Data.
        $create_property_data = $this->createProperties();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Form data parameters.
        $put_param = [
            'property_hash_id' => $property_hash_id,
            'property_status'  => 2,
        // UPDATE PROPERTY WITH INVALID STATUS.
        ];

        // Get response data.
        $url = $this->getApiVersion().'/host/property/status';

        $response = $this->actingAs($create_property_data['host'])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testInvalidResponseWithInvalidParametersWithAuthentication()


    /**
     * Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Property Data.
        $create_property_data = $this->createProperties();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Form data parameters.
        $put_param = [
            'property_hash_id' => $property_hash_id,
            'property_status'  => 0,
        // Update property to offline.
        ];

        // Get response data.
        $url = $this->getApiVersion().'/host/property/status';

        $response = $this->actingAs($create_property_data['host'])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
                'property_hash_id',
                'property_status',
                'message',
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
