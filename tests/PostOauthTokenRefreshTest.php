<?php
/**
 * PostOauthTokenRefreshTest Test containing methods related oauth Refresh token
 */

/**
 * Class PostOauthTokenRefreshTest
 *
 * @group Oauth
 */
class PostOauthTokenRefreshTest extends TestCase
{
    use App\Traits\FactoryHelper;

    /**
     * Default client_id
     *
     * @var integer
     */
    protected $client_id = 2;

    /**
     * Default Client Secret
     *
     * @var string
     */
    protected $client_secret = 'egOc9ewzDzpuLJqGJhHTQKB3XdRnCEBE9YcZgYlH';

    /**
     * Default Grant Type
     *
     * @var string
     */
    protected $grant_type = 'refresh_token';

    /**
     * Default Scope
     *
     * @var string
     */
    protected $scope = '*';


    /**
     * Test with Authorization.
     *
     * @return void
     */
    public function testResponseWithValidParam()
    {
        // Create Demo Entry in user table.
        $user     = $this->createUsers();
        $email    = $user[0]['email'];
        $password = base64_encode(111111);

        // Param for oauth token api.
        $param    = [
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type'    => 'password',
            'scopes'        => $this->scope,
            'username'      => $email,
            'password'      => $password,
        ];
        $url      = '/oauth/token';
        $response = $this->post($url, $param);

        // Get refresh token for refresh token api.
        $content       = json_decode($this->response->getContent(), true);
        $refresh_token = $content['refresh_token'];

        $url = '/oauth/token#refresh';

        // Parameter for refresh token api.
        $param    = [
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type'    => $this->grant_type,
            'scopes'        => $this->scope,
            'refresh_token' => $refresh_token,
        ];
        $response = $this->post($url, $param);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testResponseWithValidParam()


    /**
     * Test when parameter are missing.
     *
     * @return void
     */
    public function testResponseWithMissingParameter()
    {
        $url = '/oauth/token#refresh';

        // Pass blank array.
        $response = $this->post($url, []);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseWithMissingParameter()


    /**
     * Test when Wrong parameter are passing.
     *
     * @return void
     */
    public function testResponseWithWrongParameter()
    {
        // Generate random refresh token.
        $refresh_token = str_random(10);

        // Param.
        $param    = [
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type'    => $this->grant_type,
            'scopes'        => $this->scope,
            'refresh_token' => $refresh_token,
        ];
        $url      = '/oauth/token#refresh';
        $response = $this->post($url, $param);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testResponseWithWrongParameter()


}//end class
