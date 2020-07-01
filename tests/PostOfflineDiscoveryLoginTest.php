<?php
/**
 * PostOfflineDiscoveryLoginTest Test containing methods related to api with offline discovery user login.
 */

use App\Libraries\Helper;
use App\Events\UserRegistered;
use \Torann\GeoIP\Facades\GeoIP as GeoIP;

/**
 * Class PostOfflineDiscoveryLoginTest
 *
 * @group Offline_discovery
 */
class PostOfflineDiscoveryLoginTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Setup.
     *
     * @return void
     */
    public function setup() : void
    {
        parent::setup();
        // Mocking common queue not stop not live sending email,notification,sms.
        $this->mocked_service        = $this->mock('alias:App\Libraries\CommonQueue');
        $this->mocked_service_helper = $this->mock('alias:App\Libraries\Helper');

    }//end setup()


    /**
     * Tear down.
     *
     * @return void
     */
    public function tearDown() : void
    {
        $this->clearmock();

    }//end tearDown()


    /**
     * Source Google
     *
     * @return void
     */
    public function testResponseGoogle()
    {
        Event::fake();

        $google_data = [
            'id'          => str_random(21),
            'email'       => 'testing.new.api'.strtolower(str_random(4)).'@guesthouser.com',
            'name'        => 'unit',
            'last_name'   => 'testing',
            'profile_img' => '',
        ];

        // Get Location.
        $location = $this->getLocation();
        // Mocking push email function ,and should  return blank.
        $this->mocked_service->shouldReceive('pushEmail')->andReturn('');
        // Mocking userIp address ,and should  return blank.
        $this->mocked_service_helper->shouldReceive('getUserIpAddress')->andReturn('');
        // Mocking getLocationByIp ,and should  return $location.
        $this->mocked_service_helper->shouldReceive('getLocationByIp')->andReturn($location);
        // Mocking generateProfileImageUrl,and should  return blank.
        $this->mocked_service_helper->shouldReceive('generateProfileImageUrl')->andReturn('');
        // Mocking encodeUserId ,and should  return blank.
         $this->mocked_service_helper->shouldReceive('encodeUserId')->andReturn('');
        // Mocking userIp address ,and should  return Static Data ABCDEF.
         $this->mocked_service_helper->shouldReceive('generateReferralCode')->andReturn('ABCDEF');
        // MockingsendCurlRequest,and should  return fb_data.
         $this->mocked_service_helper->shouldReceive('getGoogleSignUpProfile')->once()->andReturn($google_data);
         // MockingWriteDBQueriesTofile, and should return true.
         $this->mocked_service_helper->shouldReceive('writeDBQueriesToFile');
        $param = [
            'access_token' => str_random(30),
        ];

        // Create User.
        $url      = $this->getApiVersion().'/offlinediscovery/login';
        $response = $this->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testResponseGoogle()


    /**
     * Test BadRequest.
     *
     * @return void
     */
    public function testResponseBadRequestWithAuthorization()
    {
        $location = $this->getLocation();
            // Mocking userIp address ,and should  return blank.
        $this->mocked_service_helper->shouldReceive('getUserIpAddress')->andReturn('');

        // // Mocking getLocationByIp ,and should  return $location.
        $this->mocked_service_helper->shouldReceive('getLocationByIp')->andReturn($location);

        // MockingWriteDBQueriesTofile, and should return true.
        $this->mocked_service_helper->shouldReceive('writeDBQueriesToFile');

        // Create User.
        $url      = $this->getApiVersion().'/offlinediscovery/login';
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseBadRequestWithAuthorization()


    /**
     * Get Location.
     *
     * @return $location
     */
    private function getLocation()
    {
        $ip_address = '127.0.0.0';
        $location   = GeoIP::getLocation($ip_address);
        $location   = [
            'country_code' => (empty($location->getAttribute('iso_code')) === false && $location->getAttribute('iso_code') !== 'NA') ? $location->getAttribute('iso_code') : '',
            'country'      => (empty($location->getAttribute('country')) === false && $location->getAttribute('country') !== 'NA') ? $location->getAttribute('country') : '',
            'city'         => (empty($location->getAttribute('city')) === false && $location->getAttribute('city') !== 'NA') ? $location->getAttribute('city') : '',
            'state'        => (empty($location->getAttribute('state')) === false && $location->getAttribute('state') !== 'NA') ? $location->getAttribute('state') : '',
            'state_name'   => (empty($location->getAttribute('state_name')) === false && $location->getAttribute('state_name') !== 'NA') ? $location->getAttribute('state_name') : '',
            'postal_code'  => (empty($location->getAttribute('postal_code')) === false && $location->getAttribute('postal_code') !== 'NA') ? $location->getAttribute('postal_code') : '',
            'lat'          => (empty($location->getAttribute('lat')) === false && $location->getAttribute('lat') !== 'NA') ? $location->getAttribute('lat') : '',
            'lon'          => (empty($location->getAttribute('lon')) === false && $location->getAttribute('lon') !== 'NA') ? $location->getAttribute('lon') : '',
            'currency'     => (empty($location->getAttribute('currency')) === false && $location->getAttribute('currency') !== 'NA') ? $location->getAttribute('currency') : '',
        ];
        return $location;

    }//end getLocation()


}//end class
