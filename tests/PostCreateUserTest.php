<?php
/**
 * PostCreateUserTest Test containing methods related to Create User test case
 */

use App\Libraries\Helper;
use App\Events\UserRegistered;
use Illuminate\Http\{Request};
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\{Hash};
use \Torann\GeoIP\Facades\GeoIP as GeoIP;

/**
 * Class PostCreateUserTest
 *
 * @group User
 */
class PostCreateUserTest extends TestCase
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
        // Mocking common queue not stop not live sending email,notification,sms.
        $this->mocked_service        = $this->mock('alias:App\Libraries\CommonQueue');
        $this->mocked_service_helper = $this->mock('alias:App\Libraries\Helper');

    }//end setup()


     /**
      * Tear down.
      *
      * @return void
      */
    public function tearDown(): void
    {
        $this->clearmock();

    }//end tearDown()


    /**
     * Source Website
     *
     * @return void
    //
     */
    public function testResponseSourceWebsite()
    {
        // Check event is fired.
        Event::fake();

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

        $this->mocked_service_helper->shouldReceive('writeDBQueriesToFile')->andReturn('');

        $param = [
            'email'      => 'testing.new.api'.strtolower(str_random(4)).'@guesthouser.com',
            'password'   => base64_encode(111111),
            'first_name' => 'Unit Testing',
            'last_name'  => 'Api',
            'gender'     => 'Male',
            'source'     => WEBSITE_SOURCE_ID,
        ];

        // Create User.
        $url      = $this->getApiVersion().'/user';
        $response = $this->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(201);
        Event::assertDispatched(
            UserRegistered::class,
            function ($event) use ($param) {
                return (
                $event->user->email === $param['email'] &&
                $event->source === WEBSITE_SOURCE_ID
                );
            }
        );

    }//end testResponseSourceWebsite()


     /**
      * Source Google
      *
      * @return void
      */
    public function testResponseSourceGoogle()
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

        // // Mocking getLocationByIp ,and should  return $location.
        $this->mocked_service_helper->shouldReceive('getLocationByIp')->andReturn($location);

        // // Mocking generateProfileImageUrl,and should  return blank.
        $this->mocked_service_helper->shouldReceive('generateProfileImageUrl')->andReturn('');

         // Mocking encodeUserId ,and should  return blank.
        $this->mocked_service_helper->shouldReceive('encodeUserId')->andReturn('');

         // Mocking userIp address ,and should  return Static Data ABCDEF.
        $this->mocked_service_helper->shouldReceive('generateReferralCode')->andReturn('ABCDEF');

         // MockingsendCurlRequest,and should  return fb_data.
         $this->mocked_service_helper->shouldReceive('getGoogleSignUpProfile')->once()->andReturn($google_data);

         $this->mocked_service_helper->shouldReceive('writeDBQueriesToFile')->andReturn('');

        $param = [
            'source'       => GOOGLE_SOURCE_ID,
            'access_token' => str_random(30),
        ];

        // Create User.
        $url      = $this->getApiVersion().'/user';
        $response = $this->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(201);
        Event::assertDispatched(
            UserRegistered::class,
            function ($event) use ($google_data) {
                return (
                $event->user->email === $google_data['email'] &&
                $event->source === GOOGLE_SOURCE_ID
                );
            }
        );

    }//end testResponseSourceGoogle()


     /**
      * Source Facebook
      *
      * @return void
      */
    public function testResponseSourceFacebook()
    {
        Event::fake();

        $fb_data = [
            'id'          => str_random(15),
            'email'       => 'testing.new.api'.str_random(4).'@guesthouser.com',
            'name'        => 'unit',
            'last_name'   => 'testing',
            'birthday'    => '',
            'currency'    => '',
            'profile_img' => '',
            'gender'      => '',
        ];

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
        $this->mocked_service_helper->shouldReceive('getFacebookSignUpProfile')->once()->andReturn($fb_data);

        $this->mocked_service_helper->shouldReceive('writeDBQueriesToFile')->andReturn('');

        $param = [
            'source'       => FACEBOOK_SOURCE_ID,
            'access_token' => str_random(30),
        ];

        // Create User.
        $url      = $this->getApiVersion().'/user';
        $response = $this->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(201);
        Event::assertDispatched(
            UserRegistered::class,
            function ($event) use ($fb_data) {
                return (
                $event->user->email === $fb_data['email'] &&
                $event->source === FACEBOOK_SOURCE_ID
                );
            }
        );

    }//end testResponseSourceFacebook()


    /**
     * Source Apple
     *
     * @return void
     */
    public function testResponseSourceApple()
    {
        Event::fake();

        $apple_data = [
            'id'    => str_random(15),
            'email' => 'testing.new.api'.str_random(4).'@guesthouser.com',
        ];

        $location = $this->getLocation();

        // Mocking push email function, and should return blank.
        $this->mocked_service->shouldReceive('pushEmail')->andReturn('');

        // Mocking userIp address, and should return blank.
        $this->mocked_service_helper->shouldReceive('getUserIpAddress')->andReturn('');

        // Mocking getLocationByIp, and should return $location.
        $this->mocked_service_helper->shouldReceive('getLocationByIp')->andReturn($location);

        // Mocking generateProfileImageUrl, and should return blank.
        $this->mocked_service_helper->shouldReceive('generateProfileImageUrl')->andReturn('');

        // Mocking encodeUserId, and should return blank.
        $this->mocked_service_helper->shouldReceive('encodeUserId')->andReturn('');

        // Mocking userIp address, and should return Static Data ABCDEF.
        $this->mocked_service_helper->shouldReceive('generateReferralCode')->andReturn('ABCDEF');

        // Mocking sendCurlRequest, and should return apple_data.
        $this->mocked_service_helper->shouldReceive('getAppleSignUpProfile')->once()->andReturn($apple_data);

        // Mocking write DB Queries.
        $this->mocked_service_helper->shouldReceive('writeDBQueriesToFile')->andReturn('');

        $param = [
            'source'       => APPLE_SOURCE_ID,
            'access_token' => str_random(30),
            'first_name'   => 'unit',
        ];

        // Create User.
        $url = $this->getApiVersion().'/user';
        $this->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(201);
        Event::assertDispatched(
            UserRegistered::class,
            function ($event) use ($apple_data) {
                return (
                    $event->user->email === $apple_data['email'] &&
                    $event->source === APPLE_SOURCE_ID
                );
            }
        );

    }//end testResponseSourceApple()


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

        $this->mocked_service_helper->shouldReceive('writeDBQueriesToFile')->andReturn('');
        // Create User.
        $url      = $this->getApiVersion().'/user';
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseBadRequestWithAuthorization()


    /**
     * Test For Duplicate Resoponse.
     *
     * @return void
     */
    public function testResponseDuplicateUser()
    {
        $location = $this->getLocation();
            // Mocking userIp address ,and should  return blank.
        $this->mocked_service_helper->shouldReceive('getUserIpAddress')->andReturn('');

        // // Mocking getLocationByIp ,and should  return $location.
        $this->mocked_service_helper->shouldReceive('getLocationByIp')->andReturn($location);

             // Mocking userIp address ,and should  return Static Data ABCDEF.
        $this->mocked_service_helper->shouldReceive('generateReferralCode')->andReturn('ABCDEF');

        $this->mocked_service_helper->shouldReceive('writeDBQueriesToFile')->andReturn('');

         // Create Demo Entry in user table.
        $user   = $this->createUsers();
         $param = [
             'email'      => $user[0]->email,
             'password'   => Hash::make(111111),
             'first_name' => 'Unit Testing',
             'last_name'  => 'Api',
             'gender'     => 'Male',
             'source'     => WEBSITE_SOURCE_ID,
         ];

         $url      = $this->getApiVersion().'/user';
         $response = $this->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

         // Check status of response.
         $this->seeStatusCode(403);

    }//end testResponseDuplicateUser()


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
