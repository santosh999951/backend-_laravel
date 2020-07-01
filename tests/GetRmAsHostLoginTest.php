<?php
/**
 * GetRmAsHostLoginTest Test containing methods related to rm host listing test cases.
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use \Carbon\Carbon;

/**
 * Class GetRmAsHostLoginTest
 *
 * @group Host
 */
class GetRmAsHostLoginTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        $create_property = $this->createProperties();
        $host_id         = $create_property['host']['id'];
        $host_hash_id    = Helper::encodeUserId($host_id);

        // Get response data.
        $url      = $this->getApiVersion().'/host/rmashostlogin/'.$host_hash_id;
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
        $create_property = $this->createProperties();
        $host_id         = $create_property['host']['id'];
        $host_hash_id    = Helper::encodeUserId($host_id);

        $traveller = $this->createUsers(1, ['email' => 'testing.new.api'.strtolower(str_random(4)).'@abc.com']);

        $url      = $this->getApiVersion().'/host/rmashostlogin/'.$host_hash_id;
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


     /**
      * Test for Non Rm user.
      *
      * @return void
      */
    public function testInvalidResponseForInavalidHostWithAuthentication()
    {
        $traveller    = $this->createUsers(1, ['email' => 'testing.new.api'.strtolower(str_random(4)).'@abc.com']);
        $host_hash_id = Helper::encodeUserId($traveller[0]->id);

        $url      = $this->getApiVersion().'/host/rmashostlogin/'.$host_hash_id;
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForInavalidHostWithAuthentication()


    /**
     * Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        $create_property = $this->createProperties();
        $host_id         = $create_property['host']['id'];
        $host_hash_id    = Helper::encodeUserId($host_id);
        $traveller       = $this->createUsers();

        $admin               = $this->createAdmin(['email' => $traveller[0]['email'], 'name' => $traveller[0]['name']]);
        $data                = [
            'admin_id' => $admin['id'],
            'host_id'  => $create_property['host']['id'],
            'pid'      => $create_property['properties'][0]['id'],
        ];
        $relationhip_manager = $this->craeteRealtionshipManager($data);

        // Get host home detail response data.
         $url = $this->getApiVersion().'/host/rmashostlogin/'.$host_hash_id;

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
                'access_token',
                'refresh_token',
                'token_type',
                'expires_in',
                'user_profile' => [
                    'first_name',
                    'last_name',
                    'member_since',
                    'dob',
                    'marital_status',
                    'gender',
                    'profession',
                    'email',
                    'is_email_verified',
                    'dial_code',
                    'mobile',
                    'is_mobile_verified',
                    'is_user_referred',
                    'profile_image',
                    'is_avatar_image',
                    'spoken_languages',
                    'travelled_places',
                    'description',
                    'guests_served_count',
                    'trips_count',
                    'user_currency',
                    'is_host',
                    'user_id',
                    'user_hash_id',
                    'fb_id',
                    'google_id',
                    'wallet' => [
                        'balance',
                        'currency' => [
                            'webicon',
                            'non-webicon',
                            'iso_code',
                        ]
                    ],
                        'add_listing',
                    'event',
                    'is_rm',
                    'add_email',
                    'auth_key'
                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
