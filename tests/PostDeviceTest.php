<?php
/**
 * PostDeviceTest Test containing methods related to Register Device
 */

/**
 * Class PostDeviceTest
 *
 * @group Device
 */
class PostDeviceTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test with Parameter Validation.
     *
     * @return void
     */
    public function testVlidationFailStatus()
    {
        $url      = $this->getApiVersion().'/device';
        $response = $this->post($url);

        $this->seeStatusCode(400);

    }//end testVlidationFailStatus()


    /**
     * Create new device Entry.
     *
     * @return void
     */
    public function testCreateStatus()
    {
        // Generate Device Unique Id.
        $device_unique_id = '5de786a7-019b-4e08-836b-'.substr(md5(mt_rand()), 0, 12);

        $url      = $this->getApiVersion().'/device';
        $response = $this->post($url, ['device_unique_id' => $device_unique_id], ['HTTP_device-type' => 'web']);

        $this->seeStatusCode(201);

    }//end testCreateStatus()


    /**
     * Update existing device data.
     *
     * @return void
     */
    public function testRegisterStatus()
    {
        // Register Device to create existing entry.
        $this->registerDevice();

        $url      = $this->getApiVersion().'/device';
        $response = $this->post($url, ['device_unique_id' => $this->device_unique_id, 'country' => 'IN'], ['HTTP_device-type' => 'web']);

        $this->seeStatusCode(200);

    }//end testRegisterStatus()


}//end class
