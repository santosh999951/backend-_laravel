<?php
/**
 * PostWishlistPropertyTest Test containing methods related to Add property Wishlist Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use Illuminate\Support\Facades;

/**
 * Class PostWishlistPropertyTest
 *
 * @group Property
 */
class PostWishlistPropertyTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Without login
     *
     * @return void
     */
    public function testResponseWithoutAuthorization()
    {
        // Create property.
        $properties_details = $this->createProperties();
        $property_id        = $properties_details['properties'][0]->id;
        $property_hash_id   = Helper::encodePropertyId($property_id);

        // Add Property to wishlist.
        $url      = $this->getApiVersion().'/user/wishlist/'.$property_hash_id;
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
        $properties_details = $this->createProperties();
        $property_id        = $properties_details['properties'][0]->id;
        $property_hash_id   = Helper::encodePropertyId($property_id);
        $user_id            = $properties_details['host'];

        // Add Property to wishlist.
        $url      = $this->getApiVersion().'/user/wishlist/'.$property_hash_id;
        $response = $this->actingAs($user_id)->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

    }//end testResponseWithAuthorization()


    /**
     * Test BadRequest.
     *
     * @return void
     */
    public function testResponseBadRequestWithAuthorization()
    {
        // Sample property_hash_id.
        $property_hash_id = 'ABCDEF';

        // Create Demo Entry in user table.
        $user = $this->createUsers();

        // Add Property to wishlist.
        $url      = $this->getApiVersion().'/user/wishlist/'.$property_hash_id;
        $response = $this->actingAs($user[0])->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testResponseBadRequestWithAuthorization()


    /**
     * Test Not Found.
     *
     * @return void
     */
    public function testResponseNotFoundWithAuthorization()
    {
        // Sample Property_hash_id.
        $property_hash_id = Helper::encodePropertyId('123456');

        // Create Demo Entry in user table.
        $user = $this->createUsers();

        // Add Property to wishlist.
        $url      = $this->getApiVersion().'/user/wishlist/'.$property_hash_id;
        $response = $this->actingAs($user[0])->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(404);

    }//end testResponseNotFoundWithAuthorization()


}//end class
