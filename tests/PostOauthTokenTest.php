<?php
/**
 * PostOauthTokenTest Test containing methods related oauth token
 */

/**
 * Class PostOauthTokenTest
 *
 * @group Oauth
 */
class PostOauthTokenTest extends TestCase
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
    protected $grant_type = 'password';

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
        $user     = $this->createUsers();
        $email    = $user[0]['email'];
        $password = base64_encode(111111);
        $param    = [
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type'    => $this->grant_type,
            'scopes'        => $this->scope,
            'username'      => $email,
            'password'      => $password,
        ];
        $url      = '/oauth/token';
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
        $url      = '/oauth/token';
        $response = $this->post($url, []);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseWithMissingParameter()


    /**
     * Test when wrong parameter are passing.
     *
     * @return void
     */
    public function testResponseWithWrongParameter()
    {
        $email    = 'testing.'.str_random(4).'@guesthouser.com';
        $password = 111111;
        $param    = [
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type'    => $this->grant_type,
            'scopes'        => $this->scope,
            'username'      => $email,
            'password'      => $password,
        ];
        $url      = '/oauth/token';
        $response = $this->post($url, $param);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testResponseWithWrongParameter()


}//end class
