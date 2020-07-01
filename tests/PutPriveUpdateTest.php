<?php
/**
 * PutPriveUpdateTest containing methods related to Prive profile updatation
 */

/**
 * Class PutPriveUpdateTest
 *
 * @group Owner
 */
class PutPriveUpdateTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test Unauthorized response.
     *
     * @return void
     */
    public function testUnauthorizedActionResponse()
    {
        // Execute api using Put Request Method.
        $url      = $this->getApiVersion().'/prive';
        $response = $this->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testUnauthorizedActionResponse()


    /**
     * Test BadRequest response.
     *
     * @return void
     */
    public function testBadRequestResponseWithAuthentication()
    {
        $traveller = $this->createUsers(1, ['prive_owner' => 1]);
        // Execute api using Put Request Method.
        $url = $this->getApiVersion().'/prive';
        // Bad request can be of any validation reason, firstname cannt be more than 40.
        $response = $this->actingAs($traveller[0])->put($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


     /**
      * Test Authorized User valid response with profile updation.
      *
      * @return void
      */
    public function testValidResponseWithProfileUpdationWithAuthentication()
    {
        $traveller = $this->createUsers(1, ['prive_owner' => 1]);
        // Execute api using Put Request Method.
        $url = $this->getApiVersion().'/prive';

        $response = $this->actingAs($traveller[0])->put($url, ['contact' => rand(1111111111, 9999999999)], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Match Reponse values json with defined json.
        $this->seeJsonEquals($this->getApiStructureWithValues());

    }//end testValidResponseWithProfileUpdationWithAuthentication()


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
            'data' => [],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


    /**
     * Helper function to api response
     *
     * @return array
     */
    private function getApiStructureWithValues()
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
        return [
            'status' => true,
            'data'   => ['message' => 'User details updated successfully.'],
            'error'  => [],
        ];

    }//end getApiStructureWithValues()


}//end class
