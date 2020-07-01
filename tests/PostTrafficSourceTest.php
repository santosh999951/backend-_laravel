<?php
/**
 * PostTrafficSourceTest Test containing methods related to Traffic source
 */

/**
 * Class PostTrafficSourceTest
 *
 * @group Device
 */
class PostTrafficSourceTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test without Authorization.
     *
     * @return void
     */
    public function testResponseWithoutAuthorization()
    {
        $param    = [
            'source'   => str_random(6),
            'medium'   => str_random(6),
            'campaign' => str_random(6),
        ];
        $url      = $this->getApiVersion().'/device/trafficsource';
        $response = $this->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testResponseWithoutAuthorization()


    /**
     * Test with Authorization.
     *
     * @return void
     */
    public function testResponseWithAuthorization()
    {
        $user     = $this->createUsers();
        $param    = [
            'source'   => str_random(6),
            'medium'   => str_random(6),
            'campaign' => str_random(6),
        ];
        $url      = $this->getApiVersion().'/device/trafficsource';
        $response = $this->actingAs($user[0])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testResponseWithAuthorization()


    /**
     * Test when parameter are missing.
     *
     * @return void
     */
    public function testResponseWithMissingParameter()
    {
        $user     = $this->createUsers();
        $url      = $this->getApiVersion().'/device/trafficsource';
        $response = $this->actingAs($user[0])->post($url, [], []);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseWithMissingParameter()


    /**
     * Test when parameter type are differnt.
     *
     * @return void
     */
    public function testResponseWithBadRequest()
    {
        $user     = $this->createUsers();
        $param    = [
            'source'   => rand(0, 100),
            'medium'   => rand(0, 100),
            'campaign' => rand(0, 100),
        ];
        $url      = $this->getApiVersion().'/device/trafficsource';
        $response = $this->actingAs($user[0])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseWithBadRequest()


}//end class
