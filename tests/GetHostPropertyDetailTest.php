<?php
/**
 * GetHostPropertyDetailTest Test containing methods related to Porperty Detail Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class GetHostPropertyDetailTest
 *
 * @group Host
 */
class GetHostPropertyDetailTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Create Property Data.
        $create_property_data = $this->createProperties();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Get response data.
        $url      = $this->getApiVersion().'/host/property/'.$property_hash_id;
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test for Traveller.
     *
     * @return void
     */
    public function testInvalidResponseForTravellerWithAuthentication()
    {
         // Create Property Data.
        $create_property_data = $this->createProperties();

        // Create User.
        $traveller = $this->createUser();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Get response data.
        $url      = $this->getApiVersion().'/host/property/'.$property_hash_id;
        $response = $this->actingAs($traveller)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Test Data not found for invalid hash id .
     *
     * @return void
     */
    public function testInvalidHashIdResponseWithAuthentication()
    {
         // Create Property Data.
        $create_property_data = $this->createProperties();

        // Encode Property Hash id.
        $property_hash_id = 'ABCDEF';

        // Get response data.
        $url      = $this->getApiVersion().'/host/property/'.$property_hash_id;
        $response = $this->actingAs($create_property_data['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testInvalidHashIdResponseWithAuthentication()


    /**
     * Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
         // Create Property Data.
        $create_property_data = $this->createProperties();

        // Create User.
        $traveller = $this->createUser();

        // Encode Property Hash id.
        $property_hash_id = Helper::encodePropertyId($create_property_data['properties'][0]->id);

        // Get response data.
        $url = $this->getApiVersion().'/host/property/'.$property_hash_id;

        $response = $this->actingAs($create_property_data['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


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
                'property_tile' => [
                    'property_hash_id',
                    'property_type_name',
                    'room_type_name',
                    'host_name',
                    'host_image',
                    'location' => [
                        'area',
                        'city',
                        'state',
                        'country' => [
                            'name',
                            'ccode',
                        ],
                        'location_name',
                        'latitude',
                        'longitude'
                    ],
                    'prices' => [
                        'currency' => [
                            'webicon',
                            'non-webicon',
                            'iso_code',
                        ],
                        'per_night_price'
                    ],
                    'title',
                    'property_title',
                    'property_images' => [
                        '*' => [
                            'image',
                            'caption',
                        ],
                    ],
                    'url',
                    'last_updated',
                    'calendar_last_updated',
                    'property_enable',
                    'property_status',
                    'booking_count',
                    'avg_response_time',
                    'edit_listing'
                ],
                'review_data'   => [
                    'review' => [],
                    'traveller_score',
                    'new_count',
                    'total_count'
                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
