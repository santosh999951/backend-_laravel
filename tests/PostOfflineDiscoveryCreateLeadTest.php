<?php
/**
 * PosCreateLeadTest Test containing methods related to create offline lead.
 */

use App\Libraries\Helper;

/**
 * Class PostOfflineDiscoveryCreateLeadTest
 *
 * @group Offline_discovery
 */
class PostOfflineDiscoveryCreateLeadTest extends TestCase
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

    }//end setup()


    /**
     * Test for Logout User.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Make Post Parameters.
        $post_params = [
            'property_name'       => 'test property',
            'email'               => 'testing.host.lead.api'.str_random(4).time().'@guesthouser.com',
            'contact'             => '9999999999',
            'contact_name'        => 'test contact name',
            'country'             => 'test country',
            'state'               => 'test state',
            'city'                => 'test city',
            'latitude'            => '9999999',
            'longitude'           => '9999999',
            'tariff'              => '323232',
            'cancellation_policy' => '2',
            'total_listings'      => '3',
            'amenities'           => '2',
            'leads_status'        => '2',
            'address'             => 'test address',
            'cancellation_policy' => 'default',
            'amenities'           => 'default amenties',
            'property_type'       => 0,
            'room_type'           => 0,
            'accomodation'        => 0,
            'extra_guests'        => 0,
            'bedrooms'            => 0,
            'beds'                => 0,
            'checkin'             => '12:00:00',
            'checkout'            => '12:00:00',
            'extra_guest_price'   => 0,

        ];

        $url = $this->getApiVersion().'/offlinediscovery/createlead';
        $this->post($url, $post_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test Bad User response.
     *
     * @return void
     */
    public function testBadRequestResponseWithAuthentication()
    {
        $user = $this->createUsers();
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/offlinediscovery/createlead';
        // Not passing required params.
        $response = $this->actingAs($user[0])->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test Valid Authentication.
     *
     * @return void
     */
    public function testValidAuthentication()
    {
        $user = $this->createUser();

        // Make Post Parameters.
        $post_params = [
            'property_name'       => 'test property',
            'email'               => 'testing.host.lead.api'.str_random(4).time().'@guesthouser.com',
            'contact'             => '9999999999',
            'contact_name'        => 'test contact name',
            'country'             => 'test country',
            'state'               => 'test state',
            'city'                => 'test city',
            'latitude'            => '9999999',
            'longitude'           => '9999999',
            'tariff'              => '323232',
            'cancellation_policy' => '2',
            'total_listings'      => '3',
            'amenities'           => '2',
            'leads_status'        => '2',
            'address'             => 'test address',
            'cancellation_policy' => 'default',
            'amenities'           => 'default amenties',
            'property_type'       => 0,
            'room_type'           => 0,
            'accomodation'        => 0,
            'extra_guests'        => 0,
            'bedrooms'            => 0,
            'beds'                => 0,
            'checkin'             => '12:00:00',
            'checkout'            => '12:00:00',
            'extra_guest_price'   => 0,
            'units'               => 0,

        ];

        $url = $this->getApiVersion().'/offlinediscovery/createlead';

        // Execute Api.
        $response = $this->actingAs($user)->post($url, $post_params, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testValidAuthentication()


}//end class
