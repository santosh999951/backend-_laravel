<?php
/**
 * Helper Test containing methods related to Supperting method used in Test Case
 */

namespace App\Traits;

use Illuminate\Support\Facades;
use App\Libraries\Helper;
use Illuminate\Http\{UploadedFile};
use App\Models\{MobileAppDevice, Property, BookingRequest, Collection, User, Booking, HostConversionLead};
use \Carbon\Carbon;

/**
 * Class Helper
 */
trait FactoryHelper
{

    /**
     * User Model Namespace
     *
     * @var string
     */
    protected $user_factory = 'App\Models\User';

    /**
     * Property Model Namespace
     *
     * @var string
     */
    protected $property_factory = 'App\Models\Property';

    /**
     * Booking Request Model Namespace
     *
     * @var string
     */
    protected $booking_request_factory = 'App\Models\BookingRequest';

     /**
      * Otp Contact Model Namespace
      *
      * @var string
      */
    protected $otp_contact_factory = 'App\Models\OtpContact';

    /**
     * Sms Otp Model Namespace
     *
     * @var string
     */
    protected $sms_otp_factory = 'App\Models\SmsOtp';

    /**
     * UpdateEmail Model Namespace
     *
     * @var string
     */
    protected $update_email_factory = 'App\Models\UpdateEmail';

    /**
     * Property Pricing Model Namespace
     *
     * @var string
     */
    protected $property_pricing = 'App\Models\PropertyPricing';

    /**
     * Property Detail Model Namespace
     *
     * @var string
     */
    protected $property_detail = 'App\Models\PropertyDetail';

     /**
      * Mobile App Devices Model Namespace
      *
      * @var string
      */
    protected $mobile_app_device = 'App\Models\MobileAppDevice';

    /**
     * Booking Model Namespace
     *
     * @var string
     */
    protected $bookings_model = 'App\Models\Booking';

    /**
     * Mobile App Devices Model Namespace
     *
     * @var string
     */
    protected $payments_model = 'App\Models\Payments';

    /**
     * Collection Model Namespace
     *
     * @var string
     */
    protected $collection_model = 'App\Models\Collection';

    /**
     * CollectionPropertyMapping Model Namespace
     *
     * @var string
     */
    protected $collection_properties_mapping = 'App\Models\CollectionPropertyMapping';

    /**
     * HomeBanner Model Namespace
     *
     * @var string
     */
    protected $home_banner_model = 'App\Models\HomeBanner';

    /**
     * HomeWidget Model Namespace
     *
     * @var string
     */
    protected $home_widget_model = 'App\Models\HomeWidget';

    /**
     * PropertyView Model Namespace
     *
     * @var string
     */
    protected $property_view_model = 'App\Models\PropertyView';

    /**
     * TravellerRating Model Namespace
     *
     * @var string
     */
    protected $traveller_rating_model = 'App\Models\TravellerRating';

    /**
     * PropertyReview Model Namespace
     *
     * @var string
     */
    protected $property_review_model = 'App\Models\PropertyReview';

    /**
     * InventoryPricing Model Namespace
     *
     * @var string
     */
    protected $inventory_pricing_model = 'App\Models\InventoryPricing';

    /**
     * PasswordReminder Model
     *
     * @var string
     */
    protected $password_reminder_model = 'App\Models\PasswordReminder';

    /**
     * UserBillingInfo Model Namespace
     *
     * @var string
     */
    protected $user_billing_factory = 'App\Models\UserBillingInfo';

    /**
     * RealtionshipManager Model Namespace
     *
     * @var string
     */
    protected $relationship_manager_factory = 'App\Models\RelationshipManager';

     /**
      * Admin Model Namespace
      *
      * @var string
      */
    protected $admin_factory = 'App\Models\Admin';

     /**
      * Prive Owner Factory Namespace
      *
      * @var string
      */
    protected $prive_owner_factory = 'App\Models\PriveOwner';

     /**
      * Properly Expense Factory Namespace
      *
      * @var string
      */
    protected $properly_expense_factory = 'App\Models\ProperlyExpense';

    /**
     * Properly Expense Type Factory Namespace
     *
     * @var string
     */
    protected $properly_expense_type_factory = 'App\Models\ProperlyExpenseType';

    /**
     * Prive manager taggings Type Factory Namespace
     *
     * @var string
     */
    protected $prive_manager_taggings_factory = 'App\Models\PriveManagerTagging';

    /**
     * WalletTransaction Model Namespace
     *
     * @var string
     */
    protected $wallet_transaction_model = 'App\Models\WalletTransaction';

    /**
     * PriveManager Model Namespace
     *
     * @var string
     */
    protected $prive_manager_model = 'App\Models\PriveManager';

    /**
     * App Version default value
     *
     * @var string
     */
    protected $api_version = 'v1.6';

     /**
      * App latest Version
      *
      * @var string
      */
    protected $latest_api_version = 'v1.7';

    /**
     * Device Unique default value
     *
     * @var string
     */
    protected $device_unique_id = '5de786a7-019b-4e08-836b-a8cdcc1c29e2';

    /**
     * HostConversionLead Model Namespace
     *
     * @var string
     */
    protected $host_conversion_lead_model = 'App\Models\HostConversionLead';

    /**
     * ProperlyDesignationPidMapping Model Namespace
     *
     * @var string
     */
    protected $properly_designation_pid_mapping = 'App\Models\ProperlyDesignationPidMapping';

    /**
     * ProperlyDesignationUserMapping Model Namespace
     *
     * @var string
     */
    protected $properly_designation_user_mapping = 'App\Models\ProperlyDesignationUserMapping';


    /**
     * Create users.
     *
     * @param integer $count       Number of users to create.
     * @param array   $user_params User params to overide.
     *
     * @return object
     */
    protected function createUsers(int $count=1, array $user_params=[])
    {
        return factory($this->user_factory, $count)->create($user_params);

    }//end createUsers()


    /**
     * Create users billing.
     *
     * @param integer $user_id User Id.
     *
     * @return object
     */
    protected function createUserBilling(int $user_id)
    {
        $user_billing = factory($this->user_billing_factory, 1)->create(['user_id' => $user_id]);
        return [
            'user_billing' => $user_billing[0],
        ];

    }//end createUserBilling()


    /**
     * Create Otp.
     *
     * @param array $params Params.
     *
     * @return array
     */
    protected function createOtp(array $params=[])
    {
        $host = $this->createUsers(1, $params);

        $otp_contact = factory($this->otp_contact_factory, 1)->create(['user_id' => $host[0]->id, 'contact' => $host[0]->contact]);

        return [
            'otp_contact' => $otp_contact,
            'user'        => $host,
        ];

    }//end createOtp()


    /**
     * Create Email Token.
     *
     * @return array
     */
    protected function createToken()
    {
        $host = $this->createUsers(1, ['email_verify' => 0]);

        $token_details = factory($this->update_email_factory, 1)->create(['user_id' => $host[0]->id, 'email' => $host[0]->email]);
        return [
            'token_details' => $token_details,
            'user'          => $host,
        ];

    }//end createToken()


    /**
     * Create user.
     *
     * @param array $user_params User params to overide.
     *
     * @return object
     */
    protected function createUser(array $user_params=[])
    {
        return $this->createUsers(1, $user_params)[0];

    }//end createUser()


    /**
     * Create Properties.
     *
     * @param integer $enabled     Property Enabled status.
     * @param integer $count       Number of proeprty to create.
     * @param array   $extra_param Extra Param.
     *
     * @return array
     */
    protected function createProperties(int $enabled=1, int $count=1, array $extra_param=[])
    {
        $properties = [];

        // Create Demo Entry in user table for host.
        $host = $this->createUsers(1);

        while (($count--) > 0) {
            // Make a test property.
            $property = factory($this->property_factory, 1)->create(array_merge(['user_id' => $host[0]->id, 'admin_score' => $enabled, 'prive' => 1], $extra_param));

            // Set Pricing data in Property pricing and full-fill foreign key constrain.
            $property->each(
                function ($property) {
                    $property_price = factory($this->property_pricing, 1)->make();
                    $property->property_price()->save($property_price[0]);

                    // Create Porperty Detail.
                    factory($this->property_detail, 1)->create(['pid' => $property->id, 'user_id' => $property->user_id]);
                }
            );

            $properties[] = $property[0];
        }

        return [
            'host'       => $host[0],
            'properties' => $properties,
        ];

    }//end createProperties()


    /**
     * Create Inventory.
     *
     * @param Property $property Property Model Object.
     *
     * @return array
     */
    protected function createInventoryPricing(Property $property)
    {
        $inventory_pricing = factory($this->inventory_pricing_model, 1)->create(['pid' => $property->id]);

        return $inventory_pricing[0];

    }//end createInventoryPricing()


    /**
     * Create booking request.
     *
     * @param integer  $booking_status Booking Request Status.
     * @param array    $param          Booking Request Param.
     * @param Property $property       Property.
     * @param User     $host           Host.
     * @param array    $booking_param  Booking Param.
     * @param integer  $count          Number of Count Param.
     * @param integer  $prive_owner    Prive Owner Param.
     *
     * @return array
     */
    protected function createBookingRequests(int $booking_status=NEW_REQUEST, array $param=[], Property $property=null, User $host=null, array $booking_param=[], int $count=1, int $prive_owner=0)
    {
        $bookings           = [];
        $create_prive_owner = [];

        if (empty($property) === true && empty($host) === true) {
            // Create Property for booking.
            $creted_property_data = $this->createProperties();
        } else {
            $creted_property_data['properties'] = [$property];
            $creted_property_data['host']       = $host;
        }

        // Create prive owner and assigned property.
        if ($prive_owner === 1) {
            $create_prive_owner = $this->createUser(['prive_owner' => 1]);
            foreach ($creted_property_data['properties'] as $value) {
                $assign_property_to_owner = $this->assignPropertyToOwner($create_prive_owner, $value);
            }
        }

        // Create Traveller for Booking Requests.
        $traveller = $this->createUsers(1);

        // Create Demo Booking Request by Generated Fake Property id , Traveller Id and Host Id.
        $request_data_to_create = [
            'pid'                  => $creted_property_data['properties'][0]->id,
            'host_id'              => $creted_property_data['host']->id,
            'traveller_id'         => $traveller[0]->id,
            'booking_request_code' => '',
            'booking_status'       => $booking_status,
        ];

        if (empty($param) === false) {
            $request_data_to_create = array_merge($request_data_to_create, $param);
        }

        while (($count--) > 0) {
            $booking_request = factory($this->booking_request_factory, 1)->create($request_data_to_create);
            if ($booking_status === BOOKED) {
                $bookings = $this->createBooking($booking_request[0], $booking_param);
            }

            $booking_request_list[] = $booking_request[0];
            $booking_list[]         = $bookings;
        }

        // For the case when we want to set updated at for a booking but following code
        // gonna update the updated_at column
        $update_args = [];
        if (empty($param) === false && empty($param['updated_at']) === false) {
            $update_args = ['updated_at' => $param['updated_at']];
        }

        // Adding hash id while creating booking request.
        BookingRequest::where('id', $booking_request[0]['id'])->update(
            array_merge(
                $update_args,
                [
                    'hash_id' => Helper::encodeBookingRequestId($booking_request[0]['id']),
                ]
            )
        );

        return [
            'host'                 => $creted_property_data['host'],
            'properties'           => $creted_property_data['properties'][0],
            'traveller'            => $traveller[0],
            'booking_request'      => $booking_request[0],
            'bookings'             => $bookings,
            'booking_request_list' => $booking_request_list,
            'booking_list'         => $booking_list,
            'property_list'        => $creted_property_data['properties'],
            'prive_owner'          => $create_prive_owner,
        ];

    }//end createBookingRequests()


    /**
     * Create booking.
     *
     * @param BookingRequest $request Booking Request Model object.
     * @param array          $param   Booking Request Param.
     *
     * @return object
     */
    protected function createBooking(BookingRequest $request, array $param=[])
    {
        // Create Payment dummy data.
        $payment = factory($this->payments_model, 1)->create(['booking_request_id' => $request->id]);

        // Create Booking parameters.
        $booking_data_to_create = [
            'pid'                => $request->pid,
            'host_id'            => $request->host_id,
            'traveller_id'       => $request->traveller_id,
            'booking_request_id' => $request->id,
            'from_date'          => $request->from_date,
            'to_date'            => $request->to_date,
            'units'              => $request->units,
            'recieved_currency'  => $request->currency,
            'payment_id'         => $payment[0]->id,

        ];
        $bookings = factory($this->bookings_model, 1)->create(array_merge($booking_data_to_create, $param));

        return $bookings[0];

    }//end createBooking()


    /**
     * Register device in Mobile App Devices.
     *
     * @param array $mobile_data Data for Register Device.
     *
     * @return obejct
     */
    protected function registerDevice(array $mobile_data=[])
    {
        if (empty($mobile_data) === true) {
            $mobile_data = ['device_unique_id' => $this->device_unique_id];
        }

        return factory($this->mobile_app_device, 1)->create($mobile_data);

    }//end registerDevice()


    /**
     * Get Device Unique Id.
     *
     * @return string
     */
    protected function getDeviceUniqueId()
    {
        // Fetch row with same device unique id.
        $mobile_app_devices = MobileAppDevice::getDeviceByDeviceUniqueId($this->device_unique_id);

        if (empty($mobile_app_devices) === true) {
            $mobile_app_devices = $this->registerDevice();
            $mobile_app_devices = $mobile_app_devices[0];
        }

        return $mobile_app_devices->device_unique_id;

    }//end getDeviceUniqueId()


    /**
     * Get Api Current Version.
     *
     * @return string
     */
    protected function getApiVersion()
    {
        return $this->api_version;

    }//end getApiVersion()


    /**
     * Create Collection.
     *
     * @return object
     */
    protected function createCollection()
    {
        $collection = factory($this->collection_model, 1)->create();

        // Create Property for collection.
        $created_property_data = $this->createProperties();

        // Map Collections and Property in CollectionPropertyMapping.
        $collection_mappings = $this->createCollectionPropertyMapping($collection[0], $created_property_data['properties'][0]);

        return [
            'collection'                  => $collection[0],
            'property'                    => $created_property_data['properties'][0],
            'collection_property_mapping' => $collection_mappings,
        ];

    }//end createCollection()


    /**
     * Create Collection Properties Mappings.
     *
     * @param Collection $collection Collection Model Object.
     * @param Property   $property   Property Model Object.
     *
     * @return object
     */
    protected function createCollectionPropertyMapping(Collection $collection, Property $property)
    {
        $collection_mappings = factory($this->collection_properties_mapping, 1)->create(['collection_id' => $collection->id, 'pid' => $property->id]);
        return $collection_mappings[0];

    }//end createCollectionPropertyMapping()


    /**
     * Create Home Banner Dummy data.
     *
     * @return object
     */
    protected function createHomeBanner()
    {
        $home_banner = factory($this->home_banner_model, 1)->create();
        return $home_banner[0];

    }//end createHomeBanner()


    /**
     * Create Home Widget Dummy data.
     *
     * @return object
     */
    protected function createHomeWidget()
    {
        $home_widget = factory($this->home_widget_model, 1)->create();
        return $home_widget[0];

    }//end createHomeWidget()


    /**
     * Create Property View data.
     *
     * @param Property $property Proeprty Model Object.
     * @param User     $user     User Model Object.
     *
     * @return object
     */
    protected function createPropertyView(Property $property, User $user)
    {
        $property_view = factory($this->property_view_model, 1)->create(['user_id' => $user->id, 'property_id' => $property->id]);
        return $property_view[0];

    }//end createPropertyView()


    /**
     * Create Property Review data.
     *
     * @param Booking $booking Booking Model Object.
     *
     * @return object
     */
    protected function createPropertyReview(Booking $booking)
    {
        $property_review = factory($this->property_review_model, 1)->create(['pid' => $booking->pid, 'traveller_id' => $booking->traveller_id, 'host_id' => $booking->host_id, 'booking_id' => $booking->id]);
        return $property_review[0];

    }//end createPropertyReview()


    /**
     * Create Traveller Rating data.
     *
     * @param Booking $booking Booking Model Object.
     *
     * @return object
     */
    protected function createTravellerRatings(Booking $booking)
    {
        $traveller_ratings = [];
        for ($rating_number = 1; $rating_number < 6; $rating_number++) {
            $traveller_ratings[] = (factory($this->traveller_rating_model, 1)->create(
                [
                    'property_id'         => $booking->pid,
                    'rated_by'            => $booking->traveller_id,
                    'rating_param'        => $rating_number,
                    'booking_requests_id' => $booking->booking_request_id,
                ]
            ))[0];
        }

        return $traveller_ratings;

    }//end createTravellerRatings()


    /**
     * Create Image object data.
     *
     * @param string $name File name with extension.
     *
     * @return object
     */
    protected function createImageObject(string $name)
    {
        return UploadedFile::fake()->image($name, 600, 600);

    }//end createImageObject()


    /**
     * Create Image object data.
     *
     * @param string  $name    File name with extension.
     * @param integer $user_id User Id.
     *
     * @return object
     */
    protected function saveImageInTempMemory(string $name, int $user_id)
    {
        $uploaded_image = $this->createImageObject($name);

        // Get original file name.
        $file_name = $uploaded_image->getClientOriginalName();

        // Get file extension.
        $extension = Helper::getImageExtension($file_name);

        $image_dir = PROPERTY_REVIEW_IMAGE_TEMP_URL;

        // New image name.
        $new_image_name = rand(100, 999).'_'.$user_id.'_'.time().'.'.$extension;

        // Move image to temp directory.
        $uploaded_image->move($image_dir, $new_image_name);

        return $new_image_name;

    }//end saveImageInTempMemory()


     /**
      * Create random Email
      *
      * @return object
      */
    protected function createRandomEmail()
    {
        return 'testing.new.api'.str_random(8).'@guesthouser.com';

    }//end createRandomEmail()


    /**
     * Mock a class, must call clearmockery in teardown.
     *
     * @param string  $class Class alias name.
     * @param boolean $full  Optional. Whether you want to mock the full class.
     *
     * @return $mock
     */
    protected function mock(string $class, bool $full=false)
    {
        if ($full === false) {
            $mock = \Mockery::mock($class)->makePartial();
        } else {
            $mock = \Mockery::mock($class);
        }

        $this->app->instance($class, $mock);
        return $mock;

    }//end mock()


    /**
     * Clear mockery instance.
     *
     * @return void
     */
    protected function clearmock()
    {
        \Mockery::close();

    }//end clearmock()


    /**
     * Create Password reminder/reset token seeding
     *
     * @param array $param Params for overriding.
     *
     * @return object
     */
    protected function createPasswordReminderTestSeeding(array $param=[])
    {
        return factory($this->password_reminder_model, 1)->create($param)[0];

    }//end createPasswordReminderTestSeeding()


    /**
     * Create old app login users. This is temporary solution.
     *
     * @param string $device_unique_id Device Unique Id.
     *
     * @return object
     */
    protected function createLoggedInAccessOldUserAccessTokens(string $device_unique_id)
    {
        $time        = Carbon::now('GMT')->toDateTimeString();
        $expire_time = (time() + 86400);

        // Creating session.
        \DB::statement("insert into oauth_sessions (`client_id`, `owner_type`, `owner_id`, `client_redirect_uri`, `created_at`, `updated_at`) values('androidid123456', 'user', '".$device_unique_id."', '', '$time', '$time')");

        $session_id = \DB::getPdo()->lastInsertId();

        // Creating access token based on session.
        $random_token = str_random(25).time();
        \DB::statement("insert into oauth_access_tokens (`id`, `session_id`, `expire_time`, `created_at`, `updated_at`)  values('".$random_token."', ".$session_id.', '.$expire_time.", '".$time."', '".$time."')");

        // Creating refresh token based upon access token.
        $random_refresh_token = str_random(25).time();
        \DB::statement("insert into oauth_refresh_tokens (`id`, `access_token_id`, `expire_time`, `created_at`, `updated_at`)  values('".$random_refresh_token."', '".$random_token."', ".$expire_time.", '".$time."', '".$time."')");

        return [
            'access_token'  => $random_token,
            'refresh_token' => $random_refresh_token,
        ];

    }//end createLoggedInAccessOldUserAccessTokens()


    /**
     * Create Realtionship Manager seeding
     *
     * @param array $param Params for overriding.
     *
     * @return object
     */
    protected function craeteRealtionshipManager(array $param=[])
    {
        return factory($this->relationship_manager_factory, 1)->create($param)[0];

    }//end craeteRealtionshipManager()


    /**
     * Create Admin seeding
     *
     * @param array $param Params for overriding.
     *
     * @return object
     */
    protected function createAdmin(array $param=[])
    {
        return factory($this->admin_factory, 1)->create($param)[0];

    }//end createAdmin()


    /**
     * Create wallet transaction for user.
     *
     * @param User    $user  User Model Object.
     * @param integer $event Event.
     *
     * @return object
     */
    protected function createWalletTransaction(User $user, int $event=TRIP_AND_REVIEW)
    {
        $wallet_transaction = factory($this->wallet_transaction_model, 1)->create(['user_id' => $user->id, 'event' => $event]);
        return $wallet_transaction[0];

    }//end createWalletTransaction()


     /**
      * Get Api latest Version.
      *
      * @return string
      */
    protected function getLatestApiVersion()
    {
        return $this->latest_api_version;

    }//end getLatestApiVersion()


    /**
     * Create Host Conversion Lead.
     *
     * @return object
     */
    protected function createConversionLead()
    {
        return factory($this->host_conversion_lead_model, 1)->create();

    }//end createConversionLead()


    /**
     * Assign Property to prive Owner.
     *
     * @param User     $user     User Model Object.
     * @param Property $property Property object.
     *
     * @return object
     */
    protected function assignPropertyToOwner(User $user, Property $property)
    {
        $prive_owner = factory($this->prive_owner_factory, 1)->create(['user_id' => $user->id, 'pid' => $property->id]);
        return $prive_owner[0];

    }//end assignPropertyToOwner()


    /**
     * Add properly Expense.
     *
     * @param integer      $property_id     Property id.
     * @param integer|null $expense_type_id Properly expense type id.
     *
     * @return object
     */
    protected function addProperlyExpense(int $property_id, int $expense_type_id=null)
    {
        $data = ['pid' => $property_id];
        if (null !== $expense_type_id) {
            $data['expense_type_id'] = $expense_type_id;
        }

        $prive_expense = factory($this->properly_expense_factory, 2)->create($data);
        return $prive_expense;

    }//end addProperlyExpense()


    /**
     * Add properly Expense Type
     *
     * @param integer $expense_type Properly expense type,
     * always either PROPERLY_EXPENSE_TYPE_FIXED or PROPERLY_EXPENSE_TYPE_VARIABLE.
     *
     * @return object|boolean
     */
    protected function addProperlyExpenseType(int $expense_type)
    {
        if (false === (PROPERLY_EXPENSE_TYPE_FIXED === $expense_type || PROPERLY_EXPENSE_TYPE_VARIABLE === $expense_type)) {
            return false;
        }

        $prive_expense = factory($this->properly_expense_type_factory, 1)->create(['type' => $expense_type]);

        return $prive_expense;

    }//end addProperlyExpenseType()


    /**
     * Live Property.
     *
     * @param integer $property_id Property id.
     *
     * @return object
     */
    protected function liveProperty(int $property_id)
    {
        $data = ['pid' => $property_id];

        $prive_manager_tagging = factory($this->prive_manager_taggings_factory, 1)->create($data);
        return $prive_manager_tagging;

    }//end liveProperty()


    /**
     * Assign Property to prive manager.
     *
     * @param User     $user     User Model Object.
     * @param Property $property Property object.
     *
     * @return void
     */
    protected function assignPropertyToManager(User $user, Property $property)
    {
        $assign_designation = factory($this->properly_designation_user_mapping, 1)->create(['designation_id' => 2, 'user_id' => $user->id]);

        $assign_property_to_designation = factory($this->properly_designation_pid_mapping, 1)->create(['designation_id' => 2, 'property_id' => $property->id]);

    }//end assignPropertyToManager()


    /**
     * Assign Permission to user.
     *
     * @param User  $user       User Model Object.
     * @param array $permission Permission object.
     *
     * @return void
     */
    protected function assignPermissionToUser(User $user, array $permission)
    {
        foreach ($permission as $value) {
            $user->givePermissionTo($value);
        }

    }//end assignPermissionToUser()


    /**
     * Create sms Otp.
     *
     * @param array $params Params.
     *
     * @return array
     */
    protected function createSmsOtp(array $params=[])
    {
        $otp_contact = factory($this->sms_otp_factory, 1)->create($params);

        return $otp_contact;

    }//end createSmsOtp()


} // end class
