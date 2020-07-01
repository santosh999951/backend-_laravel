<?php
/**
 * GetProperlyExpenseTest containing methods related to Prive owner expense listing api.
 */

use App\Libraries\Helper;

/**
 * Class GetProperlyExpenseTest
 *
 * @group Owner
 */
class GetProperlyExpenseTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Unauthorized response.
     *
     * @return void
     */
    public function testUnauthorizedActionResponse()
    {
        // Execute api using get Request Method.
        $url      = $this->getApiVersion().'/prive/expense';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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

        // Execute api using get Request Method.
        $url = $this->getApiVersion().'/prive/expense';

        // Bad request can be of any validation reason, firstname cannt be more than 40.
        $response = $this->actingAs($create_prive_owner)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


     /**
      * Test Authorized User valid response.
      *
      * @return void
      */
    public function testValidResponseWithAuthentication()
    {
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);
        $create_property    = $this->createProperties();

         // Assign properties to prive owner.
        $assign_property_to_owner = $this->assignPropertyToOwner($create_prive_owner, $create_property['properties'][0]);

        $expense = $this->addProperlyExpense($create_property['properties'][0]->id);

        // Encode Property Hash id.
         $property_hash_id = Helper::encodePropertyId($create_property['properties'][0]->id);

         $live_property = $this->liveProperty($create_property['properties'][0]->id);

        // Execute api using get Request Method.
        $url = $this->getApiVersion().'/prive/expense?property_hash_id='.$property_hash_id;

        $response = $this->actingAs($create_prive_owner)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
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
                'expense' => [
                    '*' => [
                        'expense_hash_id',
                        'expense_name',
                        'type',
                        'added_on',
                        'basic_amount',
                        'nights_booked',
                        'total_amount',
                    ],
                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
