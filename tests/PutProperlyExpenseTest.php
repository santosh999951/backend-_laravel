<?php
/**
 * PutProperlyExpenseTest containing methods related to Properly Expense Update
 */

/**
 * Class PutProperlyExpenseTest
 *
 * @group Owner
 */
class PutProperlyExpenseTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Unauthorized response with no user
     *
     * @return void
     */
    public function testUnauthorizedRequestActionResponse()
    {
        // Create Property.
        $create_property = $this->createProperties();

        // Create Properly Expense.
        $properly_expenses = $this->addProperlyExpense($create_property['properties'][0]->id);

        // Execute api using Put Request Method.
        $url = $this->getApiVersion().'/prive/expense/'.$properly_expenses[0]->id;

        $this->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testUnauthorizedRequestActionResponse()


    /**
     * Test forbidden response with non prive owner
     *
     * @return void
     */
    public function testForbiddenRequestActionResponse()
    {
        // Create non prive owner.
        $create_non_prive_owner = $this->createUser();

        // Create Property.
        $create_property = $this->createProperties();

        // Create Properly Expense.
        $properly_expenses = $this->addProperlyExpense($create_property['properties'][0]->id);

        // Execute api using Put Request Method.
        $url = $this->getApiVersion().'/prive/expense/'.$properly_expenses[0]->id;

        $this->actingAs($create_non_prive_owner)->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testForbiddenRequestActionResponse()


    /**
     * Test Bad request response with authentication with no input params
     *
     * @return void
     */
    public function testBadRequestResponseWithAuthentication()
    {
        // Create prive owner.
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);

        // Create Property.
        $create_property = $this->createProperties();

        // Create Properly Expense.
        $properly_expenses = $this->addProperlyExpense($create_property['properties'][0]->id);

        // Execute api using Put Request Method.
        $url = $this->getApiVersion().'/prive/expense/'.$properly_expenses[0]->id;

        $this->actingAs($create_prive_owner)->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test invalid expense hash id response
     *
     * @return void
     */
    public function testInvalidExpenseHashIdRequestActionResponse()
    {
        // Create prive owner.
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);

        // Execute api using Put Request Method.
        $url = $this->getApiVersion().'/prive/expense/ABCD';

        $put_params = ['basic_amount' => 1000];

        $this->actingAs($create_prive_owner)->put($url, $put_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testInvalidExpenseHashIdRequestActionResponse()


    /**
     * Test invalid expense id response
     *
     * @return void
     */
    public function testInvalidExpenseIdRequestActionResponse()
    {
        // Create prive owner.
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);

        // Execute api using Put Request Method.
        $url = $this->getApiVersion().'/prive/expense/999999999999999';

        $put_params = ['basic_amount' => 1000];

        $this->actingAs($create_prive_owner)->put($url, $put_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(404);

    }//end testInvalidExpenseIdRequestActionResponse()


    /**
     * Test invalid property id response
     *
     * Property can be invalid due to the following reasons:
     *  - No record exists in property table
     *  - Property exists but does not belong to the requested user
     *  - Property belong to the requested user but may be in deleted or inactive state
     *
     * @return void
     */
    public function testInvalidPropertyIdRequestActionResponse()
    {
        /*
         *  To pass this test, two users are created, where one user sends request to edit expense details
         *  of the property created by another user.
         */

        // Create prive owner.
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);

        // Create Property with another prive owner.
        $create_property = $this->createProperties();

        // Create Properly Expense.
        $properly_expenses = $this->addProperlyExpense($create_property['properties'][0]->id);

        // Execute api using Put Request Method.
        $url = $this->getApiVersion().'/prive/expense/'.$properly_expenses[0]->id;

        $put_params = ['basic_amount' => 1000];

        $this->actingAs($create_prive_owner)->put($url, $put_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidPropertyIdRequestActionResponse()


    /**
     * Test bad request with expense type 'VARIABLE' response
     *
     * @return void
     */
    public function testBadRequestWithExpenseTypeVariableActionResponse()
    {
        // Create prive owner.
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);

        // Create Property with another prive owner.
        $create_property = $this->createProperties();

        // Assign properties to prive owner.
        $this->assignPropertyToOwner($create_prive_owner, $create_property['properties'][0]);

        // Create Properly Expense Type.
        $expense_type = $this->addProperlyExpenseType(PROPERLY_EXPENSE_TYPE_VARIABLE);

        // Create Properly Expense.
        $properly_expenses = $this->addProperlyExpense($create_property['properties'][0]->id, $expense_type[0]->id);

        // Execute api using Put Request Method.
        $url = $this->getApiVersion().'/prive/expense/'.$properly_expenses[0]->id;

        $put_params = ['basic_amount' => 1000];

        $e = $this->actingAs($create_prive_owner)->put($url, $put_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestWithExpenseTypeVariableActionResponse()


    /**
     * Test valid request response
     *
     * @return void
     */
    public function testValidRequestActionResponse()
    {
        // Create prive owner.
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);

        // Create Property with another prive owner.
        $create_property = $this->createProperties();

        // Assign properties to prive owner.
        $this->assignPropertyToOwner($create_prive_owner, $create_property['properties'][0]);

        // Create Properly Expense Type.
        $expense_type = $this->addProperlyExpenseType(PROPERLY_EXPENSE_TYPE_FIXED);

        // Create Properly Expense.
        $properly_expenses = $this->addProperlyExpense($create_property['properties'][0]->id, $expense_type[0]->id);

        // Execute api using Put Request Method.
        $url = $this->getApiVersion().'/prive/expense/'.$properly_expenses[0]->id;

        $put_params = ['basic_amount' => 1500];

        $this->actingAs($create_prive_owner)->put($url, $put_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Response json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidRequestActionResponse()


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
                'properly_expense_hash_id',
                'message'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()

}//end class

