<?php
/**
 * PostProperlyExpenseTest containing methods related to Prive owner expense creation api.
 */

use App\Libraries\Helper;

/**
 * Class PostProperlyExpenseTest
 *
 * @group Owner
 */
class PostProperlyExpenseTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Unauthorized response.
     *
     * @return void
     */
    public function testUnauthorizedActionResponse()
    {
        // Execute api using Put Request Method.
        $url      = $this->getApiVersion().'/prive/expense';
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testUnauthorizedActionResponse()


    /**
     * Test BadRequest response.
     *
     * @return void
     */
    public function testBadRequestResponseWithAuthentication()
    {
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);

        // Execute api using post Request Method.
        $url = $this->getApiVersion().'/prive/expense';
        // Bad request can be of any validation reason, firstname cannt be more than 40.
        $response = $this->actingAs($create_prive_owner)->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test Authorized User Invalid response with variable expense type.
     *
     * @return void
     */
    public function testInvalidResponseWithVariableExpenseTypeAuthentication()
    {
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);
        $create_property    = $this->createProperties();

         // Assign properties to prive owner.
        $assign_property_to_owner = $this->assignPropertyToOwner($create_prive_owner, $create_property['properties'][0]);

        // Encode Property Hash id.
         $property_hash_id = Helper::encodePropertyId($create_property['properties'][0]->id);

        // Execute api using Post Request Method.
        $url = $this->getApiVersion().'/prive/expense';

        $post_params = [
            'property_hash_id' => $property_hash_id,
            'month_year'       => Carbon\Carbon::now()->format('m-Y'),
            'name'             => 'Diesel For Dg',
            'basic_amount'     => 100,
        ];

        $response = $this->actingAs($create_prive_owner)->post($url, $post_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testInvalidResponseWithVariableExpenseTypeAuthentication()


     /**
      * Test Authorized User valid response with variable expense type.
      *
      * @return void
      */
    public function testValidResponseWithVariableExpenseTypeAuthentication()
    {
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);
        $create_property    = $this->createProperties();

         // Assign properties to prive owner.
        $assign_property_to_owner = $this->assignPropertyToOwner($create_prive_owner, $create_property['properties'][0]);

        // Encode Property Hash id.
         $property_hash_id = Helper::encodePropertyId($create_property['properties'][0]->id);

         $live_property = $this->liveProperty($create_property['properties'][0]->id);

        // Execute api using Post Request Method.
        $url = $this->getApiVersion().'/prive/expense';

        $post_params = [
            'property_hash_id' => $property_hash_id,
            'month_year'       => Carbon\Carbon::now()->format('Y-m'),
            'name'             => 'Diesel For Dg',
            'basic_amount'     => 100,
            'nights'           => 10,
        ];

        $response = $this->actingAs($create_prive_owner)->post($url, $post_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithVariableExpenseTypeAuthentication()


     /**
      * Test valid Response with fixed response type.
      *
      * @return void
      */
    public function testValidResponseWithFixedExpenseTypeAuthentication()
    {
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);
        $create_property    = $this->createProperties();

         // Assign properties to prive owner.
        $assign_property_to_owner = $this->assignPropertyToOwner($create_prive_owner, $create_property['properties'][0]);

        $live_property = $this->liveProperty($create_property['properties'][0]->id);

        // Encode Property Hash id.
         $property_hash_id = Helper::encodePropertyId($create_property['properties'][0]->id);

        // Execute api using Post Request Method.
        $url = $this->getApiVersion().'/prive/expense';

        $post_params = [
            'property_hash_id' => $property_hash_id,
            'month_year'       => Carbon\Carbon::now()->format('Y-m'),
            'name'             => 'Dth',
            'basic_amount'     => 100,
        ];

        $response = $this->actingAs($create_prive_owner)->post($url, $post_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithFixedExpenseTypeAuthentication()


    /**
     * Helper function to api structure
     *
     * @return array
     */
    private function getApiStructureWithOnlyDefaultValues()
    {
        // phpcs:disable
        return [
            'status',
            'data' => [
              'message'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
