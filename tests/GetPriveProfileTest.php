<?php
/**
 * GetPriveProfileTest Test containing methods related to Prive profile api
 */

use App\Libraries\Helper;

/**
 * Class GetPriveProfileTest
 *
 * @group Owner
 */
class GetPriveProfileTest extends TestCase
{

    use App\Traits\FactoryHelper;


     /**
      * Test for Logout User.
      *
      * @return void
      */
    public function testValidResponseWithoutAuthentication()
    {
        // Get Prive Profile response data.
        $url      = $this->getApiVersion().'/prive';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testValidResponseWithoutAuthentication()


    /**
     * Test for Login User.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Traveller.
        $traveller = $this->createUsers(1, ['prive_owner' => 1]);

        // Get Prive Profile response data.
        $url      = $this->getApiVersion().'/prive';
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


      /**
       * Test for Authorised User With response values.
       *
       * @return void
       */
    public function testResponseWithValuesAuthorization()
    {
         // Create Demo Entry in user table for prive .
        $prive = $this->createUsers(1, ['prive_owner' => 1]);

        $user_id            = $prive[0]->id;
        $user_id_last_digit = (int) substr((string) $user_id, -1);

        $profile_img = 'https://d39vbwyctxz5qa.cloudfront.net/profile_pic_avatar/male/'.(($user_id_last_digit === 0) ? 10 : $user_id_last_digit ).'.png';

        // Response Data Array.
        $response_data = [
            'first_name'     => 'Unit Testing',
            'last_name'      => 'Api',
            'email'          => $prive[0]->email,
            'dial_code'      => '91',
            'contact'        => (string) $prive[0]->contact,
            'profile_image'  => $profile_img,
            'property_count' => 0,
            'aadhar_card_no' => '',
            'pan_card_no'    => '',
            'account_no'     => '',
            'ifsc_code'      => '',
        ];

        $url = $this->api_version.'/prive';

        $response = $this->actingAs($prive[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Match Reponse values json with defined json.
        $this->seeJsonEquals($this->getApiStructureWithValues($response_data));

    }//end testResponseWithValuesAuthorization()


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
                'first_name',
                'last_name',
                'email',
                'dial_code',
                'contact',
                'profile_image',
                'property_count',
                'aadhar_card_no',
                'pan_card_no',
                'account_no',
                'ifsc_code',
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


    /**
     * Helper function to api response
     *
     * @param array $response Response data.
     *
     * @return array
     */
    private function getApiStructureWithValues(array $response)
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
        return [
            'status' => true,
            'data'   => $response,
            'error'  => [],
        ];

    }//end getApiStructureWithValues()


}//end class
