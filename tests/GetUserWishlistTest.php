<?php
/**
 * GetUserWishlistTest Test containing methods related to User Wishlist Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use Illuminate\Support\Facades;

/**
 * Class GetUserWishlistTest
 *
 * @group Property
 */
class GetUserWishlistTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Without login
     *
     * @return void
     */
    public function testResponseWithoutAuthorization()
    {
        $url      = $this->getApiVersion().'/user/wishlist';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testResponseWithoutAuthorization()


    /**
     * Test With Login.
     *
     * @return void
     */
    public function testResponseWithAuthorization()
    {
        // Create Demo Entry in user table.
        $properties_details = $this->createProperties();
        $property_id        = $properties_details['properties'][0]->id;
        $property_hash_id   = Helper::encodePropertyId($property_id);
        $user_id            = $properties_details['host'];

        // Add Property to wishlist.
        $url      = $this->getApiVersion().'/user/wishlist/'.$property_hash_id;
        $response = $this->actingAs($user_id)->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/user/wishlist';
        $response = $this->actingAs($user_id)->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
         // Match Reponse json with defined json.
         $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testResponseWithAuthorization()


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
                'wishlist' => [
                    '*' => [],
                ],

            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
