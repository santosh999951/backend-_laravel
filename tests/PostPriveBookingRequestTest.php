<?php
/**
 * PostPriveBookingRequestTest Test containing methods related to Post prive Booking Request Test case
 */

use App\Libraries\Helper;
use \Carbon\Carbon;

/**
 * Class PostPriveBookingRequestTest
 *
 * @group Owner
 */
class PostPriveBookingRequestTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test with logout user.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Create Property for booking.
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);
        $create_property    = $this->createProperties();

        // Assign properties to prive owner.
        $assign_property_to_owner = $this->assignPropertyToOwner($create_prive_owner, $create_property['properties'][0]);

        $property_hash_id = Helper::encodePropertyId($create_property['properties'][0]->id);
        $param            = [
            'checkin'          => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->toDateString(),
            'checkout'         => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->toDateString(),
            'property_hash_id' => $property_hash_id,
            'guests'           => 1,
            'units'            => 1,
            'payment_method'   => 'full_payment',
            'first_name'       => 'Unit Testing',
            'dial_code'        => 91,
            'email'            => 'testing.new.api'.strtolower(str_random(4)).time().'@guesthouser.com',
            'contact'          => 1111111111,

        ];
        $url      = $this->getApiVersion().'/prive/booking';
        $response = $this->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test Login User response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
         // Create Property for booking.
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);
        $create_property    = $this->createProperties();

        // Assign properties to prive owner.
        $assign_property_to_owner = $this->assignPropertyToOwner($create_prive_owner, $create_property['properties'][0]);
        $live_property            = $this->liveProperty($create_property['properties'][0]->id);

        $property_hash_id = Helper::encodePropertyId($create_property['properties'][0]->id);
        $param            = [
            'checkin'          => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->toDateString(),
            'checkout'         => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->toDateString(),
            'property_hash_id' => $property_hash_id,
            'guests'           => 1,
            'units'            => 1,
            'first_name'       => 'Unit Testing',
            'dial_code'        => 91,
            'email'            => 'testing.new.api'.strtolower(str_random(4)).time().'@guesthouser.com',
            'contact'          => 1111111111,

        ];

        $url      = $this->getApiVersion().'/prive/booking';
        $response = $this->actingAs($create_prive_owner)->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status code.
        $this->seeStatusCode(200);

        // // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


    /**
     * Test Invalid Hash Id.
     *
     * @return void
     */
    public function testInvalidHashId()
    {
          // Create Property for booking.
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);
        $create_property    = $this->createProperties();

        // Assign properties to prive owner.
        $assign_property_to_owner = $this->assignPropertyToOwner($create_prive_owner, $create_property['properties'][0]);
        $property_hash_id         = Helper::encodePropertyId($create_property['properties'][0]->id);
        $param                    = [
            'checkin'          => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 14))->toDateString(),
            'checkout'         => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 15))->toDateString(),
            'property_hash_id' => 'ABCDEF',
            'guests'           => 1,
            'units'            => 1,
            'first_name'       => 'Unit Testing',
            'dial_code'        => 91,
            'email'            => 'testing.new.api'.strtolower(str_random(4)).time().'@guesthouser.com',
            'contact'          => 1111111111,

        ];

        $url      = $this->getApiVersion().'/prive/booking';
        $response = $this->actingAs($create_prive_owner)->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status code.
        $this->seeStatusCode(400);

    }//end testInvalidHashId()


    /**
     * Test testMissingParameter.
     *
     * @return void
     */
    public function testMissingParameter()
    {
        $create_prive_owner = $this->createUser(['prive_owner' => 1]);
        $create_property    = $this->createProperties();

        // Assign properties to prive owner.
        $assign_property_to_owner = $this->assignPropertyToOwner($create_prive_owner, $create_property['properties'][0]);
        $url      = $this->getApiVersion().'/prive/booking';
        $response = $this->actingAs($create_prive_owner)->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status code.
        $this->seeStatusCode(400);

    }//end testMissingParameter()


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
                'message',
                'request_id',
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
