<?php
/**
 * GetHostPayoutsTest Test containing methods related to Host payout api
 */

use App\Libraries\Helper;

/**
 * Class GetHostPayoutsTest
 *
 * @group Host
 */
class GetHostPayoutsTest extends TestCase
{

    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Get host payout response data.
        $url      = $this->getApiVersion().'/host/payouts';
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

        // Get host payout response data.
        $url      = $this->getApiVersion().'/host/payouts';
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
        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED);

        // Get host payout response data.
        $url = $this->getApiVersion().'/host/payouts';

        $response = $this->actingAs($create_booking['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


    /**
     * Host Payout Response with authenicated user With Invalid date.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthenticationWithInvalidParameter()
    {
        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED);

        // Invalid start_date and end_date.
        $start_date = '1234';
        $end_date   = '1234';

        // Get host payout response data.
        $url = $this->getApiVersion().'/host/payouts?start_date='.$start_date.'&end_date='.$end_date;

        $response = $this->actingAs($create_booking['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testValidResponseWithAuthenticationWithInvalidParameter()


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
                 'payout_history' => [
                     '*' => [
                         'booking_requests_id',
                         'booking_amount',
                         'settled_amount',
                         'pending_amount',
                         'checkin_date',
                         'checkin_formatted',
                         'booking_status' => [
                             'text',
                             'class',
                             'color_code',
                             'status',
                             'header_text',
                         ]
                     ],
                 ],
                 'due_amount'
             ],
             'error'
         ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
