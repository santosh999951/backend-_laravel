<?php
/**
 * PostPriveLoginTest Test containing methods related to api with prive owner login.
 */

use App\Libraries\Helper;

/**
 * Class PostPriveLoginTest
 *
 * @group Owner
 */
class PostPriveLoginTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Setup.
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();

    }//end setup()


    /**
     * Test Bad User response.
     *
     * @return void
     */
    public function testBadRequestResponseWithoutAuthentication()
    {
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/prive/login';
        // Not passing required params.
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithoutAuthentication()


     /**
      * Test Invalid Prive Owner response.
      *
      * @return void
      */
    public function testInvalidPriveOwnerResponse()
    {
        // Create Default User.
        $user = $this->createUser();

        // Make Post Parameters.
        $post_params = [
            'email'    => $user->email,
            'password' => base64_encode(111111),
            'source'   => 1,
        ];

        // Make Url.
        $url = $this->getApiVersion().'/prive/login';

        // Execute Api.
        $this->post($url, $post_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidPriveOwnerResponse()


    /**
     * Test Invalid Prive Owner response.
     *
     * @return void
     */
    public function testInvalidAuthentication()
    {
        // Create Default User.
        $user = $this->createUser(['prive_owner' => 1]);

        // Make Post Parameters.
        $post_params = [
            'email'    => $user->email,
            // Make Invalid Password.
            'password' => base64_encode(str_random(5)),
            'source'   => 1,
        ];

        // Make Url.
        $url = $this->getApiVersion().'/prive/login';

        // Execute Api.
        $this->post($url, $post_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidAuthentication()


    /**
     * Test Invalid Prive Owner response.
     *
     * @return void
     */
    public function testValidAuthentication()
    {
        // Create Default User.
        $user = $this->createUser(['prive_owner' => 1]);

        // Make Post Parameters.
        $post_params = [
            'email'    => $user->email,
            // Make Invalid Password.
            'password' => base64_encode(111111),
            'source'   => 1,
        ];

        // Make Url.
        $url = $this->getApiVersion().'/prive/login';

        // Execute Api.
        $this->post($url, $post_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Check request exist in reponse.
        $content = json_decode($this->response->getContent(), true);

        if (empty($content['data']['user_profile']) === false) {
            $this->assertEquals(ucfirst($user->name), $content['data']['user_profile']['name']);
            $this->assertEquals(Helper::encodeUserId($user->id), $content['data']['user_profile']['user_hash_id']);
        } else {
            $this->assertTrue(false);
        }

    }//end testValidAuthentication()


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
