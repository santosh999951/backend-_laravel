<?php
/**
 * DeleteWishlistPropertyTest Test containing methods related to delete property from Wishlist Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use Illuminate\Support\Facades;

/**
 * Class DeleteWishlistPropertyTest
 *
 * @group Property
 */
class DeleteWishlistPropertyTest extends TestCase
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

        // Delete Property from wishlist.
        $url      = $this->getApiVersion().'/user/wishlist/'.$property_hash_id;
        $response = $this->delete($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
        // Create property.
        $properties_details = $this->createProperties();
        $property_id        = $properties_details['properties'][0]->id;
        $property_hash_id   = Helper::encodePropertyId($property_id);
        $user_id            = $properties_details['host'];

        // Add Property to wishlist.
        $url      = $this->getApiVersion().'/user/wishlist/'.$property_hash_id;
        $response = $this->actingAs($user_id)->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Delete Property from wishlist.
        $url      = $this->getApiVersion().'/user/wishlist/'.$property_hash_id;
        $response = $this->actingAs($user_id)->delete($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
        $property_hash_id = 'ABCDEF';
        $user             = $this->createUsers();

        // Add Property to wishlist.
        $url      = $this->getApiVersion().'/user/wishlist/'.$property_hash_id;
        $response = $this->actingAs($user[0])->delete($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
        $property_hash_id = Helper::encodePropertyId('123456');
        $user             = $this->createUsers();

        // Add Property to wishlist.
        $url      = $this->getApiVersion().'/user/wishlist/'.$property_hash_id;
        $response = $this->actingAs($user[0])->delete($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(404);

    }//end testResponseNotFoundWithAuthorization()


     /**
      * Test Response when property not in wishlist.
      *
      * @return void
      */
    public function testResponseNotPresentInWishlist()
    {
        // Create property.
        $properties_details = $this->createProperties();
        $property_id        = $properties_details['properties'][0]->id;
        $property_hash_id   = Helper::encodePropertyId($property_id);
        $user_id            = $properties_details['host'];

        // Delete Property from wishlist.
        $url      = $this->getApiVersion().'/user/wishlist/'.$property_hash_id;
        $response = $this->actingAs($user_id)->delete($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testResponseNotPresentInWishlist()


}//end class
