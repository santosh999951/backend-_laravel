<?php
/**
 * GetBookingShareTest Test containing methods related to Booking Share  Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class GetBookingShareTest
 *
 * @group Trips
 */
class GetBookingShareTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test with logout user.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        $url      = $this->getApiVersion().'/booking/share';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test Login User response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(1);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $url      = $this->getApiVersion().'/booking/share?request_hash_id='.$request_hash_id;
        $response = $this->actingAs($booking_request_data['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
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
                'content',
                'link',
                'share_image',
                'property_title',
                'property_desc',
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
