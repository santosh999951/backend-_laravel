<?php
/**
 * PostRegisterRequestTest containing methods related to User Signup Test case
 */

use App\Http\Requests\PostRegisterRequest;

/**
 * Class PostRegisterRequestTest
 */
class PostRegisterRequestTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Check Sanitized Data.
     *
     * @return void
     */
    public function test_sanitized_data()
    {
        $attributes = [
            'source'        => '0',
            'currency'      => ' inr ',
            'referral_code' => ' RYLMNZ ',
            'device_type'   => 'web',
            'email'         => ' Testing.New.api@guesthouser.com ',
            'password'      => '  '.base64_encode(111111),
            'first_name'    => ' <script> App Testing </script> ',
            'last_name'     => ' <span> App Testing </span> ',
            'gender'        => 'male ',
            'access_token'  => ' kasdjhksagfksfgtywebrkjfdgtv',

        ];

        $request = new PostRegisterRequest();
        $request->replace($attributes);
        $request->sanitize();
        $input = $request->input();
        $this->assertEquals(0, $input['source']);
        $this->assertEquals('INR', $input['currency']);
        $this->assertEquals('RYLMNZ', $input['referral_code']);
        $this->assertEquals('web', $input['device_type']);
        $this->assertEquals('testing.new.api@guesthouser.com', $input['email']);
        $this->assertEquals('111111', $input['password']);
        $this->assertEquals('app testing', $input['first_name']);
        $this->assertEquals('app testing', $input['last_name']);
        $this->assertEquals('Male', $input['gender']);
        $this->assertEquals('kasdjhksagfksfgtywebrkjfdgtv', $input['access_token']);

    }//end test_sanitized_data()


    /**
     * Invalid Source test.
     *
     * @return void
     */
    public function test_invalid_source_validation_failed()
    {
        $attributes = ['source' => 10];

        $request = new PostRegisterRequest();
        $request->replace($attributes);
        $request->sanitize();
        $validator = Validator::make($request->input(), $request->rules(), $request->messages());

        $passes = $validator->fails();
        $this->assertEquals(true, $passes);

        $failed_attribute = $validator->failed();
        $this->assertEquals($failed_attribute['source']['In'], [WEBSITE_SOURCE_ID, GOOGLE_SOURCE_ID, FACEBOOK_SOURCE_ID, APPLE_SOURCE_ID]);

    }//end test_invalid_source_validation_failed()


    /**
     * Website signup parameters pass.
     *
     * @return void
     */
    public function test_website_signup_with_only_required_parameters_validation_passses()
    {
        $attributes = [
            'source'     => WEBSITE_SOURCE_ID,
            'email'      => 'testing.new.api'.str_random(4).'@guesthouser.com',
            'password'   => base64_encode(str_random(6)),
            'first_name' => 'Albert',
        ];

        $request = new PostRegisterRequest();
        $request->replace($attributes);
        $request->sanitize();
        $validator = Validator::make($request->input(), $request->rules(), $request->messages());

        $passes = $validator->passes();
        $this->assertEquals(true, $passes);

    }//end test_website_signup_with_only_required_parameters_validation_passses()


    /**
     * Website signup parameters fail.
     *
     * @return void
     */
    public function test_website_signup_without_required_parameters_validation_fails()
    {
        $attributes = [
            'access_token' => str_random(125),
            'source'       => WEBSITE_SOURCE_ID,
        ];

        $request = new PostRegisterRequest();
        $request->replace($attributes);
        $request->sanitize();
        $validator = Validator::make($request->input(), $request->rules(), $request->messages());
        $passes    = $validator->fails();
        $this->assertEquals(true, $passes);

        $failed_attribute = $validator->failed();
        $this->assertEquals($failed_attribute['email']['RequiredIf'], ['source', WEBSITE_SOURCE_ID]);
        $this->assertEquals($failed_attribute['password']['RequiredIf'], ['source', WEBSITE_SOURCE_ID]);
        $this->assertEquals($failed_attribute['first_name']['RequiredIf'], ['source', WEBSITE_SOURCE_ID, APPLE_SOURCE_ID]);

    }//end test_website_signup_without_required_parameters_validation_fails()


    /**
     * Google signup parameters pass.
     *
     * @return void
     */
    public function test_google_signup_with_only_required_parameters_validation_passses()
    {
        $attributes = [
            'source'       => GOOGLE_SOURCE_ID,
            'access_token' => str_random(125),
        ];

        $request = new PostRegisterRequest();
        $request->replace($attributes);
        $request->sanitize();
        $validator = Validator::make($request->input(), $request->rules(), $request->messages());

        $passes = $validator->passes();
        $this->assertEquals(true, $passes);

    }//end test_google_signup_with_only_required_parameters_validation_passses()


    /**
     * Google signup parameters fail.
     *
     * @return void
     */
    public function test_google_signup_without_required_parameters_validation_fails()
    {
        $attributes = ['source' => GOOGLE_SOURCE_ID];

        $request = new PostRegisterRequest();
        $request->replace($attributes);
        $request->sanitize();
        $validator = Validator::make($request->input(), $request->rules(), $request->messages());
        $passes    = $validator->fails();
        $this->assertEquals(true, $passes);

        $failed_attribute = $validator->failed();

        $this->assertEquals($failed_attribute['access_token']['RequiredIf'], ['source', GOOGLE_SOURCE_ID, FACEBOOK_SOURCE_ID, APPLE_SOURCE_ID]);

    }//end test_google_signup_without_required_parameters_validation_fails()


    /**
     * Facebook signup parameters pass.
     *
     * @return void
     */
    public function test_facebook_signup_with_only_required_parameters_validation_passses()
    {
        $attributes = [
            'source'       => FACEBOOK_SOURCE_ID,
            'access_token' => str_random(125),
        ];

        $request = new PostRegisterRequest();
        $request->replace($attributes);
        $request->sanitize();
        $validator = Validator::make($request->input(), $request->rules(), $request->messages());

        $passes = $validator->passes();
        $this->assertEquals(true, $passes);

    }//end test_facebook_signup_with_only_required_parameters_validation_passses()


    /**
     * Facebook signup parameters fail.
     *
     * @return void
     */
    public function test_facebook_signup_without_required_parameters_validation_fails()
    {
        $attributes = ['source' => FACEBOOK_SOURCE_ID];

        $request = new PostRegisterRequest();
        $request->replace($attributes);
        $request->sanitize();
        $validator = Validator::make($request->input(), $request->rules(), $request->messages());
        $passes    = $validator->fails();
        $this->assertEquals(true, $passes);

        $failed_attribute = $validator->failed();

        $this->assertEquals($failed_attribute['access_token']['RequiredIf'], ['source', GOOGLE_SOURCE_ID, FACEBOOK_SOURCE_ID, APPLE_SOURCE_ID]);

    }//end test_facebook_signup_without_required_parameters_validation_fails()


    /**
     * Apple signup parameters pass.
     *
     * @return void
     */
    public function test_apple_signup_with_only_required_parameters_validation_passses()
    {
        $attributes = [
            'source'       => APPLE_SOURCE_ID,
            'access_token' => str_random(125),
            'first_name'   => 'unit',
        ];

        $request = new PostRegisterRequest();
        $request->replace($attributes);
        $request->sanitize();
        $validator = Validator::make($request->input(), $request->rules(), $request->messages());

        $passes = $validator->passes();
        $this->assertEquals(true, $passes);

    }//end test_apple_signup_with_only_required_parameters_validation_passses()


    /**
     * Apple signup parameters fail.
     *
     * @return void
     */
    public function test_apple_signup_without_required_parameters_validation_fails()
    {
        $attributes = ['source' => APPLE_SOURCE_ID];

        $request = new PostRegisterRequest();
        $request->replace($attributes);
        $request->sanitize();
        $validator = Validator::make($request->input(), $request->rules(), $request->messages());
        $passes    = $validator->fails();
        $this->assertEquals(true, $passes);

        $failed_attribute = $validator->failed();

        $this->assertEquals($failed_attribute['access_token']['RequiredIf'], ['source', GOOGLE_SOURCE_ID, FACEBOOK_SOURCE_ID, APPLE_SOURCE_ID]);

    }//end test_apple_signup_without_required_parameters_validation_fails()


}//end class
