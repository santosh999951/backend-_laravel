<?php
/**
 * GetUserProfileTest Test containing methods related to User Profile Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use Illuminate\Support\Facades;

/**
 * Class GetUserProfileTest
 *
 * @group User
 */
class GetUserProfileTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Without login When User id is missing for Test Bad Request.
     *
     * @return void
     */
    public function testBadRequest()
    {
        $url      = $this->getApiVersion().'/user';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        $this->seeStatusCode(400);

    }//end testBadRequest()


    /**
     * Without login for invalid fake user id Test.
     *
     * @return void
     */
    public function testBadUserIdResponse()
    {
        // Create fake user hash id.
        $user_id = strtoupper(str_random(6));

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/user?user_id='.$user_id;
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testBadUserIdResponse()


    /**
     *  Without Login, accurate userid.
     *
     * @return void
     */
    public function testValidResponseWithoutLogin()
    {
        // Create Properties.
        $created_property_data = $this->createProperties();

        $user_id = Helper::encodeUserId($created_property_data['host']['id']);

        $url      = $this->getApiVersion().'/user?user_id='.$user_id;
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithoutLogin()


    /**
     * Test for Authorised User and accurate user_id.
     *
     * @return void
     */
    public function testResponseWithAuthorization()
    {
         // Create Demo Entry in user table for traveller .
        $traveller = $this->createUsers();

        // Create Property for host.
        $created_property_data = $this->createProperties();

        $host_id = Helper::encodeUserId($created_property_data['host']['id']);

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url = $this->api_version.'/user?user_id='.$host_id;

        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(200);
        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

         // Check user exist in reponse.
        $content = json_decode($this->response->getContent(), true);

        if (empty($content['data']) === false) {
            $this->assertEquals($host_id, $content['data']['user_hash_id']);
        } else {
            $this->assertTrue(false);
        }

    }//end testResponseWithAuthorization()


    /**
     *  Test for Authorised User Without User_id.
     *
     * @return void
     */
    public function testResponseWithAuthorizationWithoutUserId()
    {
         // Create Demo Entry in user table for traveller .
        $traveller = $this->createUsers();

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/user';
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(200);
        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithAuthorizationWithoutUserId()


    /**
     * Test for Authorised User Wrong user_id.
     *
     * @return void
     */
    public function testResponseWithAuthorizationWrongUserId()
    {
        // Create Demo Entry in user table for traveller .
        $user = $this->createUsers();

        // Create fake host hash id.
        $host_id = strtoupper(str_random(6));

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/user?user_id='.$host_id;
        $response = $this->actingAs($user[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseWithAuthorizationWrongUserId()


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
                'property_listings'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
