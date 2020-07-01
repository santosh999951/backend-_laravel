<?php
/**
 * PutMobileLoginTest containing methods related to login using phone number
 */

use App\Libraries\Helper;
/**
 * Class PostMobileLoginTest
 *
 * @group User
 */
class PutMobileLoginTest extends TestCase
{

    use App\Traits\FactoryHelper;


     /**
      * Test Bad request User response.
      *
      * @return void
      */
    public function testBadRequestResponseWithAuthentication()
    {
        // Execute api.
        $url = $this->getApiVersion().'/user/login/mobile';
        // Passing no parameter.
        $response = $this->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test valid response when number is not verified.
     *
     * @return void
     */
    public function testValidResponseResetWithNumberNotVerifiedWithAuthentication()
    {
        // Create Demo Entry in otp_contact table.
        $otp_contact = $this->createOtp(['mobile_verify' => 0]);
        $user        = $otp_contact['user'][0];

        $otp_array = [
            'dial_code'      => 91,
            'contact_number' => $otp_contact['otp_contact'][0]['contact'],

            'otp_code'       => $otp_contact['otp_contact'][0]['otp'],
        ];
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/user/login/mobile';

        $response = $this->put($url, $otp_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check request exist in reponse.
        $content = json_decode($this->response->getContent(), true);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testValidResponseResetWithNumberNotVerifiedWithAuthentication()


    /**
     * Test valid when number is verified.
     *
     * @return void
     */
    public function testValidResponseResetWithNumberVerifiedWithAuthentication()
    {
        // Create Demo Entry in otp_contact table.
        $otp_contact = $this->createOtp();
        $user        = $otp_contact['user'][0];

        $otp_array = [
            'dial_code'      => 91,
            'contact_number' => $otp_contact['otp_contact'][0]['contact'],

            'otp_code'       => $otp_contact['otp_contact'][0]['otp'],
        ];
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/user/login/mobile';

        $response = $this->put($url, $otp_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check request exist in reponse.
        $content = json_decode($this->response->getContent(), true);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());
        if (empty($content['data']['user_profile']) === false) {
            $this->assertEquals(ucfirst($user['name']), $content['data']['user_profile']['first_name']);
            $this->assertEquals(Helper::encodeUserId($user['id']), $content['data']['user_profile']['user_hash_id']);
        } else {
            $this->assertTrue(false);
        }

    }//end testValidResponseResetWithNumberVerifiedWithAuthentication()


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
                'user_profile'     => [
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
                    'active_request_count',
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
                    'auth_key',
                    'host_token',
                ],
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
