<?php
/**
 * GetRmHostListingTest Test containing methods related to rm host listing test cases.
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use \Carbon\Carbon;

/**
 * Class GetRmHostListingTest
 *
 * @group Host
 */
class GetRmHostListingTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Get response data.
        $url      = $this->getApiVersion().'/host/rmhostlisting';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test for Non Rm user.
     *
     * @return void
     */
    public function testInvalidResponseForTravellerWithAuthentication()
    {
        $traveller = $this->createUsers(1, ['email' => 'testing.new.api'.str_random(4).'@abc.com']);

        // Get response data.
        $url      = $this->getApiVersion().'/host/rmhostlisting';
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
          // Create Properties Data.
        $create_property = $this->createProperties();
        $host            = $create_property['host'];
        $traveller       = $this->createUsers();

        $admin               = $this->createAdmin(['email' => $traveller[0]['email'], 'name' => $traveller[0]['name']]);
        $data                = [
            'admin_id' => $admin['id'],
            'host_id'  => $create_property['host']['id'],
            'pid'      => $create_property['properties'][0]['id'],
        ];
        $relationhip_manager = $this->craeteRealtionshipManager($data);

        // Get response data.
        $url = $this->getApiVersion().'/host/rmhostlisting';

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
                'host_list' => [
                    '*' => [
                        'host_id',
                        'name',
                        'email',
                        'property_count',
                    ],
                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
