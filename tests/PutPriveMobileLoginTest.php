<?php
/**
 * PutPriveMobileLoginTest containing methods related to login using phone number
 */

use App\Libraries\Helper;
/**
 * Class PutPriveMobileLoginTest
 *
 * @group Owner
 */
class PutPriveMobileLoginTest extends TestCase
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
        $url = $this->getApiVersion().'/prive/login/mobile';
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
        $otp_contact = $this->createOtp(['prive_owner' => 1, 'mobile_verify' => 0]);
        $user        = $otp_contact['user'][0];

        $otp_array = [
            'dial_code'      => 91,
            'contact_number' => $otp_contact['otp_contact'][0]['contact'],

            'otp_code'       => $otp_contact['otp_contact'][0]['otp'],
            'source'         => 1,
        ];
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/prive/login/mobile';

        $response = $this->put($url, $otp_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check request exist in reponse.
        $content = json_decode($this->response->getContent(), true);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidResponseResetWithNumberNotVerifiedWithAuthentication()


    /**
     * Test valid when number is verified for prive owner.
     *
     * @return void
     */
    public function testValidResponsePriveOwnerWithNumberVerifiedWithAuthentication()
    {
        $mobile_verify = 1;

        // Create Demo Entry in otp_contact table.
        $otp_contact = $this->createOtp(['prive_owner' => 1]);
        $user        = $otp_contact['user'][0];

        $otp_array = [
            'dial_code'      => 91,
            'contact_number' => $otp_contact['otp_contact'][0]['contact'],

            'otp_code'       => $otp_contact['otp_contact'][0]['otp'],
            'source'         => 1,
        ];
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/prive/login/mobile';

        $response = $this->put($url, $otp_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check request exist in reponse.
        $content = json_decode($this->response->getContent(), true);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());
        if (empty($content['data']['user_profile']) === false) {
            $this->assertEquals(ucfirst($user['name']), $content['data']['user_profile']['name']);
            $this->assertEquals(Helper::encodeUserId($user['id']), $content['data']['user_profile']['user_hash_id']);
        } else {
            $this->assertTrue(false);
        }

    }//end testValidResponsePriveOwnerWithNumberVerifiedWithAuthentication()


        /**
         * Test valid when number is verified fo prive manager.
         *
         * @return void
         */
    public function testValidResponsePriveManagerWithNumberVerifiedWithAuthentication()
    {
        $mobile_verify = 1;

        // Create Demo Entry in otp_contact table.
        $otp_contact = $this->createOtp(['prive_manager' => 1]);
        $user        = $otp_contact['user'][0];

        $otp_array = [
            'dial_code'      => 91,
            'contact_number' => $otp_contact['otp_contact'][0]['contact'],

            'otp_code'       => $otp_contact['otp_contact'][0]['otp'],
            'source'         => 2,
        ];
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/prive/login/mobile';

        $response = $this->put($url, $otp_array, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check request exist in reponse.
        $content = json_decode($this->response->getContent(), true);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());
        if (empty($content['data']['user_profile']) === false) {
            $this->assertEquals(ucfirst($user['name']), $content['data']['user_profile']['name']);
            $this->assertEquals(Helper::encodeUserId($user['id']), $content['data']['user_profile']['user_hash_id']);
        } else {
            $this->assertTrue(false);
        }

    }//end testValidResponsePriveManagerWithNumberVerifiedWithAuthentication()


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
                    'name',
                    'profile_image',
                    'user_hash_id',
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
