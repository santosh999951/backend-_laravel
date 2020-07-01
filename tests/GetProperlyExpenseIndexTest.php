<?php
/**
 * GetProperlyExpenseIndexTest containing methods related to Prive owner expense index api.
 */

use App\Libraries\Helper;
use \Carbon\Carbon;

/**
 * Class GetProperlyExpenseIndexTest
 *
 * @group Owner
 */
class GetProperlyExpenseIndexTest extends TestCase
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
        $url      = $this->getApiVersion().'/prive/expense/index';
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
        $url = $this->getApiVersion().'/prive/expense/index';

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
        $create_booking = $this->createBookingRequests(BOOKED, ['from_date' => Carbon::now()->toDateString(), 'to_date' => Carbon::now()->addDay('1')->toDateString(), 'prive' => 1], null, null, [], 1, 1);

        $expense = $this->addProperlyExpense($create_booking['properties']['id']);

        // Encode Property Hash id.
         $property_hash_id = Helper::encodePropertyId($create_booking['properties']['id']);

         $live_property = $this->liveProperty($create_booking['properties']['id']);

        // Execute api using Get Request Method.
        $url = $this->getApiVersion().'/prive/expense/index?property_hash_id='.$property_hash_id.'&month_year='.Carbon::now()->format('Y-m');

        $response = $this->actingAs($create_booking['prive_owner'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
                'expense'              => [
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
                'expense_distrubution' => [
                    'distribution' => [
                        '*' => [
                            'percentage',
                            'expense_name',
                            'amount',
                        ],

                    ],
                    'pl',
                ],
                'expense_type'         => [
                    '*' => [
                        'name',
                        'type',
                    ],
                ],
                'suggestions',
                'property_title',
                'property_live_date',
                'total'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
