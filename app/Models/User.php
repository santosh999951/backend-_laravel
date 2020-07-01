<?php
/**
 * Model containing data regarding users
 */

namespace App\Models;

use DB;
use \Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use App\Libraries\v1_6\AwsService;
use App\Libraries\v1_6\UserService;
use App\Libraries\{Helper, Email};
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use App\Models\OauthRmMobileDeviceMapping;
use Spatie\Permission\Traits\HasRoles;



/**
 * Class User
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Authenticatable, Authorizable, SoftDeletes, HasRoles;

    /**
     * Variable definition.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * Variable definition.
     *
     * @var array
     */
    protected $hidden = ['password'];

    /**
     * Variable definition.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    /**
     * User device table relationship.
     *
     * @return object
     */
    public function device()
    {
        return $this->belongsTo('App\Device', 'user_id', 'id');

    }//end device()


    /**
     * User property table relationship.
     *
     * @return object
     */
    public function properties()
    {
        return $this->hasMany(Property::class);

    }//end properties()


    /**
     * User wallet trasaction table relationship.
     *
     * @return object
     */
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);

    }//end walletTransactions()


    /**
     * Find user by email/contact.
     *
     * @param string $username Email/contact.
     *
     * @return object | null
     */
    public function findForPassport(string $username)
    {
        if (empty($username) === true) {
            return null;
        }

        // Change column name whatever you use in credentials.
        $user = self::where('email', $username)->orWhere(
            function ($query) use ($username) {
                $query->where('contact', $username)->where('dial_code', '91')->where('mobile_verify', 1);
            }
        )->withTrashed()->first();

        // If user is soft deleted, restore in DB.
        if (empty($user) === false) {
            $user->activateIfTrashed();
        }

        return $user;

    }//end findForPassport()


    /**
     * Validate user password.
     *
     * @param string $password Base64 encoded password.
     *
     * @return boolean|null
     */
    public function validateForPassportPasswordGrant(string $password)
    {
        if (empty($password) === true) {
            return null;
        }

        return Hash::check(base64_decode($password), $this->password);

    }//end validateForPassportPasswordGrant()


    /**
     * Get user by referral code.
     *
     * @param string $referral_code Referral code of user.
     *
     * @return object
     */
    public static function getUserByReferralCode(string $referral_code)
    {
        return self::where('referral_code', $referral_code)->first();

    }//end getUserByReferralCode()


    /**
     * Check if user can invite other users.
     *
     * @param integer $user_id User id.
     *
     * @return boolean
     */
    public static function canUserInvite(int $user_id)
    {
        $invite_count = Invite::where('from_id', $user_id)->count();
        if ($invite_count !== 0) {
            return false;
        }

        return true;

    }//end canUserInvite()


    /**
     * Check if user is host.
     *
     * @param integer $user_id User id.
     *
     * @return boolean
     */
    public static function isUserHost(int $user_id)
    {
        $properties_count = Property::where('user_id', $user_id)->count();
        if ($properties_count !== 0) {
            return true;
        }

        return false;

    }//end isUserHost()


     /**
      * Get prive owner user.
      *
      * @param string  $email_or_phone Email/phonenumber.
      * @param integer $dial_code      Dial Code.
      *
      * @return object
      */
    public function getPriveOwner(string $email_or_phone, int $dial_code=91)
    {
        if (empty($email_or_phone) === true) {
            return (object) [];
        }

        // Change column name whatever you use in credentials.
        $user = self::where(
            function ($query) use ($email_or_phone, $dial_code) {
                $query->where('email', $email_or_phone)->orWhere(
                    function ($query) use ($email_or_phone, $dial_code) {
                        $query->where('contact', $email_or_phone)->where('dial_code', $dial_code);
                    }
                );
            }
        )->where('prive_owner', 1)->withTrashed()->first();

        // If user is soft deleted, restore in DB.
        if (empty($user) === false) {
            $user->activateIfTrashed();
        }

        return $user;

    }//end getPriveOwner()


    /**
     * Get prive manager user.
     *
     * @param string  $email_or_phone Email/Phone.
     * @param integer $dial_code      Dial Code.
     *
     * @return object
     */
    public function getPriveManager(string $email_or_phone, int $dial_code=91)
    {
        if (empty($email_or_phone) === true) {
                return (object) [];
        }

        // Change column name whatever you use in credentials.
        $user = self::where(
            function ($query) use ($email_or_phone, $dial_code) {
                    $query->where('email', $email_or_phone)->orWhere(
                        function ($query) use ($email_or_phone, $dial_code) {
                            $query->where('contact', $email_or_phone)->where('dial_code', '91');
                        }
                    );
            }
        )->where('prive_manager', 1)->withTrashed()->first();

        // If user is soft deleted, restore in DB.
        if (empty($user) === false) {
            $user->activateIfTrashed();
        }

        return $user;

    }//end getPriveManager()


    /**
     * Get user.
     *
     * @param string $user_id User id.
     *
     * @return object
     */
    public function getUser(string $user_id)
    {
        if (empty($user_id) === true) {
            return (object) [];
        }

        // Change column name whatever you use in credentials.
        $user = self::where(
            function ($query) use ($user_id) {
                $query->where('id', $user_id);
            }
        )->withTrashed()->first();

        // If user is soft deleted, restore in DB.
        if (empty($user) === true) {
            return (object) [];
        }

        return $user;

    }//end getUser()


    /**
     * Check if user is Rm.
     *
     * @param string $email Email id.
     *
     * @return boolean
     */
    public static function isUserRmByEmail(string $email)
    {
        $domain = explode('@', $email)[1];
        if (in_array($domain, ['guesthouser.com', 'properly.com']) === true) {
            // Only guesthouser mail allowed.
            if (empty($email) === false) {
                $rm_count = RelationshipManager::getRMCount($email);
                if ($rm_count !== 0) {
                    return true;
                }
            }
        }

        return false;

    }//end isUserRmByEmail()


    /**
     * Get user data.
     *
     * @param integer $user_id User id.
     * @param array   $select  Data to fetch.
     *
     * @return object
     */
    public static function getUserDataById(int $user_id, array $select=['*'])
    {
        return self::select($select)->where('id', $user_id)->first();

    }//end getUserDataById()


    /**
     * Get user data By auth key.
     *
     * @param string $auth_key Auth Key.
     *
     * @return object
     */
    public static function getUserDataByAuthKey(string $auth_key)
    {
        return self::where('auth_key', $auth_key)->first();

    }//end getUserDataByAuthKey()


    /**
     * Get user full name.
     *
     * @param array $user_ids User id array.
     *
     * @return array
     */
    public static function getUserFullNameByIds(array $user_ids)
    {
        $host = self::select('id', DB::raw('RTRIM(CONCAT(name, " ", last_name)) AS name'))->whereIn('id', $user_ids)->get()->toArray();

        $host = array_column($host, 'name', 'id');

        return $host;

    }//end getUserFullNameByIds()


    /**
     * Get user full name.
     *
     * @return string
     */
    public function getUserFullName()
    {
        return ucfirst($this->name.' '.$this->last_name);

    }//end getUserFullName()


    /**
     * Get user profile image.
     *
     * @param array $user_ids User id array.
     *
     * @return array
     */
    public static function getUserProfileImageByIds(array $user_ids)
    {
        $images = self::select('id', 'profile_img')->whereIn('id', $user_ids)->get()->toArray();

        if (count($images) === 0) {
            return [];
        }

        $images = array_column($images, 'profile_img', 'id');

        return self::processProfileImageData($images);

    }//end getUserProfileImageByIds()


    /**
     * Check if multiple users have same contact number.
     *
     * @param integer $user_id User id.
     * @param string  $phone   User contact number.
     *
     * @return boolean
     */
    public static function checkIfAnyOtherUserHasSameNumber(int $user_id, string $phone)
    {
        $count = self::where('id', '!=', $user_id)->where('contact', '=', $phone)->where('mobile_verify', '=', 1)->count();

        if ($count === 0) {
            return false;
        }

        return true;

    }//end checkIfAnyOtherUserHasSameNumber()


    /**
     * Check if multiple users have same email id.
     *
     * @param integer $user_id User id.
     * @param string  $email   User email.
     *
     * @return boolean
     */
    public static function checkIfAnyOtherUserHasSameEmail(int $user_id, string $email)
    {
        $count = self::where('id', '!=', $user_id)->where('email', '=', $email)->count();

        if ($count > 0) {
            return true;
        }

        return false;

    }//end checkIfAnyOtherUserHasSameEmail()


    /**
     * Process profile images.
     *
     * @param array $images Profile images mapped with user id as key.
     *
     * @return array
     */
    public static function processProfileImageData(array $images)
    {
        $image_array = [];

        foreach ($images as $key => $value) {
            if (empty($value) === true) {
                $image_array[$key] = '';
            } else {
                $image_array[$key] = S3_PROFILE_PIC_FOLDER_URL.$value;
            }
        }

        return $image_array;

    }//end processProfileImageData()


    /**
     * Get user by base currency.
     *
     * @param integer $user_id User id.
     *
     * @return string
     */
    public static function getUserBaseCurrency(int $user_id)
    {
        $currency = self::select('base_currency')->where('id', '=', $user_id)->first();
        if (empty($currency->base_currency) === true) {
            return DEFAULT_CURRENCY;
        } else {
            return self::getCommonCurrency($currency->base_currency);
        }

    }//end getUserBaseCurrency()


    /**
     * Update user contact.
     *
     * @param integer $user_id       User id.
     * @param string  $dialcode      Country dial code.
     * @param string  $phone         User contact number.
     * @param integer $mobile_verify Is user verified.
     *
     * @return boolean
     */
    public static function updateUserContactDetail(int $user_id, string $dialcode, string $phone, int $mobile_verify=0)
    {
        $user                = self::find($user_id);
        $user->mobile_verify = $mobile_verify;
        $user->contact       = $phone;
        $user->dial_code     = $dialcode;

        if ($user->save() === true) {
            return true;
        }

        return false;

    }//end updateUserContactDetail()


    /**
     * Make contact as verified.
     *
     * @return boolean
     */
    public function markContactVerified()
    {
        $this->mobile_verify = 1;

        if ($this->save() === true) {
            return true;
        }

        return false;

    }//end markContactVerified()


    /**
     * Update user contact.
     *
     * @return boolean
     */
    public function saveAuthKey()
    {
        $auth_token = str_random(36);

        $this->auth_key = $auth_token;

        return $this;

    }//end saveAuthKey()


    /**
     * Get user details by email id.
     *
     * @param string $email User email id.
     *
     * @return object
     */
    public static function getUserByEmail(string $email)
    {
        return self::select('id', 'mobile_verify', 'email', 'contact', 'dial_code', 'email_verify', 'password')->where('email', $email)->first();

    }//end getUserByEmail()


    /**
     * Get user details by contact number.
     *
     * @param string $contact   User contact number.
     * @param string $dial_code User Dial code.
     *
     * @return object
     */
    public static function getUserByMobileNumber(string $contact, string $dial_code='')
    {
        $query = self::select('id', 'email', 'mobile_verify', 'contact', 'dial_code')->where('contact', $contact);
        if (empty($dial_code) === false) {
            $query->where('dial_code', $dial_code);
        }

        return $query->orderBy('mobile_verify', 'DESC')->get();

    }//end getUserByMobileNumber()


    /**
     * Update user details.
     *
     * @param integer $user_id User id.
     * @param array   $data    Data to be updated.
     *
     * @return boolean
     */
    public static function updateUserDetails(int $user_id, array $data)
    {
        $update_user_data = self::where('id', $user_id)->update($data);

        if ($update_user_data > 0) {
            return true;
        }

        return false;

    }//end updateUserDetails()


    /**
     * Get user gender string.
     *
     * @param string $gender User gender character.
     *
     * @return string
     */
    public static function getGenderInDbFormat(string $gender)
    {
        switch ($gender) {
            case 'm':
                $gender = 'Male';
            break;

            case 'f':
                $gender = 'Female';
            break;

            default:
                $gender = '';
            break;
        }

        return $gender;

    }//end getGenderInDbFormat()


    /**
     * Map input keys with db column names.
     *
     * @param array $input_params Input Parameters.
     * @param array $keys         Keys sto map with db.
     *
     * @return array
     */
    public static function getInputParamsAsAssociativeArray(array $input_params, array $keys)
    {
        $data_array = [];

        foreach ($keys as $key) {
            $db_key = $key;

            if (isset($input_params[$key]) === true) {
                switch ($key) {
                    case 'first_name':
                            $db_key = 'name';
                    break;

                    case 'last_name':
                            $db_key = 'last_name';
                    break;

                    case 'profession':
                            $db_key = 'work';
                    break;

                    case 'spoken_languages':
                            $db_key = 'language';
                    break;

                    default:
                        // No default value.
                    break;
                }//end switch

                if (isset($input_params[$key]) === true) {
                    $data_array[$db_key] = $input_params[$key];
                }
            }//end if
        }//end foreach

        return $data_array;

    }//end getInputParamsAsAssociativeArray()


    /**
     * Get user by confirmation code.
     *
     * @param integer $user_id User id.
     *
     * @return string
     */
    public static function generateConfirmationCodeForEmailVerification(int $user_id)
    {
        // Generate new confirmation code string.
        $confirmation_code = str_random(36);

        $check_if_confirmation_code_exists = self::getDataByConfirmationCode($confirmation_code);

        if (empty($check_if_confirmation_code_exists) === false) {
            return self::generateConfirmationCodeForEmailVerification($user_id);
        }

        $user                    = self::find($user_id);
        $user->confirmation_code = $confirmation_code;
        if ($user->save() === false) {
            return '';
        }

        return $confirmation_code;

    }//end generateConfirmationCodeForEmailVerification()


    /**
     * Get user by confirmation code.
     *
     * @param string $confirmation_code Confirmation code text.
     *
     * @return object
     */
    public static function getDataByConfirmationCode(string $confirmation_code)
    {
        return self::select('id', 'email_verify')->where('confirmation_code', $confirmation_code)->get();

    }//end getDataByConfirmationCode()


    /**
     * Move image to s3.
     *
     * @param string $url Image url.
     *
     * @return string
     */
    public static function uploadImageToS3FromUrl(string $url) : string
    {
        // Fetch image from the url.
        try {
            $response = Helper::sendCurlRequest($url);
        } catch (\Exception $e) {
            Helper::logError($e->getMessage());
            return '';
        }

        // Random image name.
        $picture_name = mt_rand(100, 999).'_'.time().'.jpg';
        $picture_file = PROFILE_PIC_TMP_DIR.$picture_name;

        // Put the image into our server.
        file_put_contents($picture_file, $response);

        // Upload image from our server to s3.
        AwsService::putObjectInS3Bucket(
            S3_BUCKET,
            S3_PROFILE_PIC_DIR.$picture_name,
            $picture_file,
            'public-read'
        );

        // Delete downloaded image.
        unlink($picture_file);
        return $picture_name;

    }//end uploadImageToS3FromUrl()


    /**
     * Update user profile image.
     *
     * @param \Illuminate\Http\UploadedFile $picture File to uplaod.
     *
     * @return string
     */
    public function updateUploadedProfilePicture(\Illuminate\Http\UploadedFile $picture) : string
    {
        $extension = ($picture->guessExtension() ?? $picture->getClientOriginalExtension());
        $extension = strtolower($extension);

        $picture_name = $this->id.'_'.time().'.'.$extension;
        $picture_path = PROFILE_PIC_TMP_DIR.$picture_name;
        $picture->move(PROFILE_PIC_TMP_DIR, $picture_name);

        // New image path and name (for resizing).
        $new_picture_name = mt_rand(100, 999).$this->id.'_'.time().'.'.$extension;
        $new_picture_path = PROFILE_PIC_DIR.$new_picture_name;

        // Use intervention class to resize the image.
        Image::make($picture_path)->resize(DEFAULT_PROFILE_PIC_WIDTH, DEFAULT_PROFILE_PIC_HEIGHT)->save($new_picture_path);

        // Upload resized image from our server to s3.
        AwsService::putObjectInS3Bucket(
            S3_BUCKET,
            S3_PROFILE_PIC_DIR.$new_picture_name,
            $new_picture_path,
            'public-read'
        );

        // Upload original image from our server to s3.
        AwsService::putObjectInS3Bucket(
            S3_BUCKET,
            S3_RAW_PROFILE_PIC_DIR.$new_picture_name,
            $picture_path,
            'private'
        );

        // Delete image.
        unlink($new_picture_path);

        $prepend_url = S3_PROFILE_PIC_FOLDER_URL;
        // Delete image.
        unlink($picture_path);

        // Save changes to DB.
        $this->profile_img = $new_picture_name;
        $this->save();

        return $prepend_url.$new_picture_name;

    }//end updateUploadedProfilePicture()


    /**
     * Send account deactivation mail.
     *
     * @return null
     */
    public function sendAccountDeactivationEmail()
    {
        Email::send(
            [
                'template'           => 'emails.mailers.deactivate_account',
                'to'                 => $this->email,
                'email_content_vars' => [
                    'name'            => $this->name.' '.$this->last_name,
                    'tracking_params' => '',
                ],
                'subject'            => 'Account Deactivated',
            ]
        );

        return null;

    }//end sendAccountDeactivationEmail()


    /**
     * Get user wallet balance.
     *
     * @return float
     */
    public function getWalletBalance()
    {
        return $this->wallet_balance;

    }//end getWalletBalance()


    /**
     * Get user usable wallet balance.
     *
     * @return float
     */
    public function getUsableWalletBalance()
    {
        return $this->usable_wallet_balance;

    }//end getUsableWalletBalance()


    /**
     * Get user wallet currency.
     *
     * @return string
     */
    public function getWalletCurrency()
    {
        return (empty($this->wallet_currency) === false) ? $this->wallet_currency : DEFAULT_CURRENCY;

    }//end getWalletCurrency()


    /**
     * Get user latest wallet transactions.
     *
     * @return array
     */
    public function getWalletTransactions()
    {
        $transactions = $this->walletTransactions()->latest()->get(['type', 'event', 'booking_request_id', 'currency', 'amount', 'review_details', 'created_at', 'expire_on'])->all();

        $wallet_transactions = [];
        foreach ($transactions as $key => $transaction) {
            $transaction->description = WALLET_EVENTS[$transaction->event]['description'];

            $transaction->booking_id = '';
            if (in_array(WALLET_EVENTS[$transaction->event]['code'], ['TRIP_AND_REVIEW', 'APPLY_WALLET_MONEY', 'BOOKING_CANCELLATION_WALLET_CASHBACK']) === true) {
                $transaction->booking_id = Helper::encodeBookingRequestId($transaction->booking_request_id);
            }

            $transaction->expire_on = ($transaction->type === 'IN') ? \Carbon\Carbon::parse($transaction->expire_on)->timezone('Asia/Kolkata')->format('d M y') : '';

            // By default in models created_at and updated_at are carbon objects, so if u reassign/reformat it to same varrible.
            // It will try to create carbon object from reformated date and will give error, so created differnt name.
            $transaction->created_on = $transaction->created_at->timezone('Asia/Kolkata')->format('d M y');
            unset($transaction->created_at);

            $transaction->currency_symbol = Helper::getCurrencySymbol($transaction->currency);
            $transaction->amount          = Helper::getFormattedMoney($transaction->amount, Helper::getCurrencySymbol($transaction->currency));

            unset($transaction->booking_request_id);
            unset($transaction->event);

            $wallet_transactions[] = $transaction;
        }//end foreach

        return $wallet_transactions;

    }//end getWalletTransactions()


    /**
     * Get user base currency.
     *
     * @return string
     */
    public function getBaseCurrency()
    {
        return (empty($this->base_currency) === false) ? $this->base_currency : DEFAULT_CURRENCY;

    }//end getBaseCurrency()


    /**
     * Restore user and properties if user is trashed.
     *
     * @return null
     */
    public function activateIfTrashed()
    {
        if ($this->trashed() === true) {
            $this->restore();
            $this->properties()->withTrashed()->restore();

            return null;
        }

    }//end activateIfTrashed()


    /**
     * Get user profile data by user id.
     *
     * @param integer $user_id       User id.
     * @param boolean $send_auth_key Send Auth Key status.
     *
     * @return array
     */
    public static function getUserProfile(int $user_id, bool $send_auth_key=true)
    {
        // Paramters to fetch from user table.
        $get_params = [
            'id',
            'name',
            'last_name',
            'profile_img',
            'email',
            'email_verify',
            'dial_code',
            'contact',
            'mobile_verify',
            'fb_id',
            'google_id',
            'apple_id',
            'created_at',
            'dob',
            'marital_status',
            'gender',
            'work',
            'language',
            'travelled_places',
            'description',
            'base_currency',
            'wallet_currency',
            'usable_wallet_balance',
            'auth_key',
            'gender',
            'referral_by',
        ];

        // Fetch user data.
        $user = self::getUserDataById($user_id, $get_params);

        if (empty($user) === true) {
            return [];
        }

        return $user->getUserProfleViaObject($send_auth_key);

    }//end getUserProfile()


    /**
     * Get user profile data by user object.
     *
     * @param boolean $send_auth_key Send Auth Key status.
     *
     * @return array
     */
    public function getUserProfleViaObject(bool $send_auth_key=true) : array
    {
        // Process user profile image(get default avatar if image not added, get image full url).
        $gender          = (empty($this->gender) === false) ? $this->gender : 'Male';
        $profile_image   = (empty($this->profile_img) === false) ? $this->profile_img : '';
        $is_avatar_image = (empty($this->profile_img) === false) ? 0 : 1;

        $profile_image = Helper::generateProfileImageUrl($gender, $profile_image, $this->id);

        // Get count of guest hosted by user as host.
        $guests_served_count = BookingRequest::getGuestCountServedByUserAsHost($this->id);

        // Get count of trips made by user as traveller.
        $trips_by_user = BookingRequest::getTripsTakenCountByUserAsTraveller($this->id);

        // Get new request and approved requests count.
        $active_request_count = BookingRequest::getNewAndApprovedRequestsCount($this->id);
        // User currency.
        $currency = (empty($this->base_currency) === true) ? DEFAULT_CURRENCY : self::getCommonCurrency($this->base_currency);

        $wallet_currency = $this->wallet_currency;

        if (array_key_exists($wallet_currency, CURRENCY_SYMBOLS) === true) {
            $wallet_currency_symbol = CURRENCY_SYMBOLS[$wallet_currency];
        } else {
            $wallet_currency_symbol = CURRENCY_SYMBOLS[DEFAULT_CURRENCY];
        }

        // phpcs:ignore
        $is_user_referred = ($this->referral_by == '') ? 0 : 1;

        // Check if this user is RM or not.
        $is_rm = false;

        // Add email .
        $add_email = 0;

        $email  = $this->email;
        $domain = explode('@', $email)[1];
        if (in_array($domain, ['guesthouser.com', 'properly.com']) === true) {
            // Only guesthouser mail allowed.
            $is_rm = self::isUserRmByEmail($email);
        } else if ($domain === 'facebook.com' || $domain === GH_DEFAULT_EMAIL_DOMAIN) {
            $add_email = 1;
        }

        // Temporary Fix for ANDROID/IOS. We will remove IF condition After Some time.
        $request     = app('request');
        $device_type = (empty($request->headers->get('device-type')) === false && in_array($request->headers->get('device-type'), ['android', 'ios']) === true) ? $request->headers->get('device-type') : '';
        $app_version = (empty($request->headers->get('app-version')) === false) ? $request->headers->get('app-version') : '';

        if (($device_type === 'android' && $app_version < ANDROID_LATEST_VERSION) || ($device_type === 'ios' && $app_version < IOS_LATEST_VERSION)) {
            $is_host = ($is_rm === true) ? true : self::isUserHost($this->id);
        } else {
            $is_host = self::isUserHost($this->id);
        }

        $is_prive_manager = (empty($this->getPriveManager($email)) === false) ? true : false;

        // Set in case of auth key not exist.
        if (empty($this->auth_key) === true) {
            $this->saveAuthKey();
        }

        // End Temporary.
        // Output array.
        return [
            'first_name'           => ucfirst($this->name),
            'last_name'            => $this->last_name,
            'member_since'         => Carbon::parse($this->created_at)->format('d-m-Y'),
            // phpcs:ignore Squiz.PHP.DisallowBooleanStatement.Found
            'dob'                  => ($this->dob !== '0000-00-00' && $this->dob !== null) ? Carbon::parse($this->dob)->format('d-m-Y') : '',
            // phpcs:ignore Squiz.PHP.DisallowBooleanStatement.Found
            'marital_status'       => ($this->marital_status !== null) ? $this->marital_status : '',
            // phpcs:ignore Squiz.PHP.DisallowBooleanStatement.Found
            'gender'               => ($this->gender !== null && $this->gender !== 'Other') ? $this->gender : '',
            'profession'           => $this->work,
            'email'                => ($add_email === 0) ? $this->email : '',
            'is_email_verified'    => $this->email_verify,
            'dial_code'            => $this->dial_code,
            'mobile'               => $this->contact,
            'is_mobile_verified'   => UserService::isMobileVerified($this),
            'is_user_referred'     => $is_user_referred,

            'profile_image'        => $profile_image,
            'is_avatar_image'      => $is_avatar_image,
            'spoken_languages'     => $this->language,
            'travelled_places'     => $this->travelled_places,
            'description'          => ($this->description !== null) ? $this->description : '',

            'guests_served_count'  => $guests_served_count,
            'trips_count'          => $trips_by_user,
            'active_request_count' => $active_request_count,
            'user_currency'        => $currency,
            'is_host'              => $is_host,
            'user_id'              => $this->id,
            'user_hash_id'         => Helper::encodeUserId($this->id),
            'fb_id'                => $this->fb_id,
            'google_id'            => $this->google_id,
            'apple_id'             => $this->apple_id,
            'wallet'               => [
                'balance'  => (int) $this->usable_wallet_balance,
                'currency' => $wallet_currency_symbol,
            ],
            'add_listing'          => WEBSITE_URL.'/user/applogin?auth_key='.$this->auth_key.'&app=1&next_url=/properties/add?app=1',
            'event'                => 'login',
            'is_rm'                => (int) $is_rm,
            'add_email'            => $add_email,
            'auth_key'             => ($send_auth_key === true) ? $this->auth_key : '',
            'is_prive_manager'     => (int) $is_prive_manager,
        ];

    }//end getUserProfleViaObject()


    /**
     * This is to login v1.5 users with v1.6 token
     *
     * @param string  $access_token  User id.
     * @param string  $refresh_token User id.
     * @param integer $user_id       User id.
     *
     * @return string
     */
    public static function loginUser(string $access_token, string $refresh_token, int $user_id=0)
    {
        $client_id   = 'androidid123456';
        $device_type = '';

        if (empty(app('Illuminate\Http\Request')->header('device-type')) === false && in_array(app('Illuminate\Http\Request')->header('device-type'), ['android', 'ios']) === true) {
            $device_type = app('Illuminate\Http\Request')->header('device-type');
        }

        // $access_token  = substr($access_token, 0, 40);
        // $refresh_token = substr($refresh_token, 0, 40);
        $access_token     = md5($access_token);
        $refresh_token    = md5($refresh_token);
        $device_unique_id = app('Illuminate\Http\Request')->header('device-unique-id');

        if (empty($device_unique_id) === false && empty($user_id) === false) {
                MobileAppDevice::updateDeviceUser($device_unique_id, $user_id);
        }

        if (empty($device_unique_id) === false && empty($device_type) === false && empty($access_token) === false && empty($refresh_token) === false) {
            $access_token_exists = DB::table('oauth_access_tokens')->where('id', $access_token)->first();

            if (empty($access_token_exists) === false) {
                DB::table('oauth_access_tokens')->where('id', '=', $access_token)->delete();
                DB::table('oauth_refresh_tokens')->where('access_token_id', '=', $access_token)->delete();
            }

            $session_id = \DB::table('oauth_sessions')->insertGetId(
                [
                    'client_id'           => $client_id,
                    'owner_type'          => 'user',
                    'owner_id'            => $device_unique_id,
                    'client_redirect_uri' => null,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ]
            );

            \DB::table('oauth_access_tokens')->insert(
                [
                    'id'          => $access_token,
                    'session_id'  => $session_id,
                    'expire_time' => (time() + 84600),
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ]
            );

            \DB::table('oauth_refresh_tokens')->insert(
                [
                    'id'              => $refresh_token,
                    'access_token_id' => $access_token,
                    'expire_time'     => (time() + 2678400),
                    'created_at'      => Carbon::now(),
                    'updated_at'      => Carbon::now(),
                ]
            );

            if (empty($user_id) === false) {
                $is_rm = false;
                $user  = self::find($user_id);
                $email = $user->email;
                $is_rm = self::isUserRmByEmail($email);
                if ($is_rm === true) {
                    OauthRmMobileDeviceMapping::unlinkRmFromMobileDevice($user_id);
                    OauthRmMobileDeviceMapping::mapRmToHostMobileDevice($user_id, 0, $device_unique_id);
                }
            }

            return $access_token;
        }//end if

        return '';

    }//end loginUser()


    /**
     * This is to login v1.5 users
     *
     * @param string $access_token     User id.
     * @param string $refresh_token    User id.
     * @param string $device_unique_id Device unique id.
     *
     * @return array | boolean |object
     */
    public static function findOldAppLoggedInUser(string $access_token, string $refresh_token, string $device_unique_id)
    {
        // phpcs:disable
        $user = \DB::select("select user_id from oauth_refresh_tokens r join oauth_access_tokens a on a.id=r.access_token_id join oauth_sessions s on s.id=a.session_id join mobile_app_devices m on m.device_unique_id=s.owner_id  where  r.id= '".$refresh_token."'  and r.access_token_id = '".$access_token."' and s.owner_id= '".$device_unique_id."'and r.expire_time> unix_timestamp() and m.user_id!='' ");

        if (empty($user) === false && count($user) > 0) {
            $user_id = $user[0]->user_id;
            if (empty($user[0]->user_id) === false) {
                return self::find($user[0]->user_id);
            }
        }

        return false;

    }//end findOldAppLoggedInUser()


    /**
     * Get common currency.
     *
     * @param string $currency User currency.
     *
     * @return string
     */
    public static function getCommonCurrency(string $currency)
    {
        return DEFAULT_CURRENCY;

    }//end getCommonCurrency()


    /**
     * Update Booking Credits.
     *
     * @param integer $user_id User id.
     *
     * @return boolean
     */
    public static function updateBookingCredits(int $user_id)
    {
        $user                  = self::find($user_id);
        $user->booking_credits = 0.00;

        if ($user->save() === true) {
            return true;
        }

        return false;

    }//end updateBookingCredits()


   /** getRmHostList() provide the host list of give rm.
     *
     * @param string $rm_email     Rm email.
     * @param int $offset    Offset.
     * @param int $limit LIMIT.
     * @param int $host_id Host Id.
     *
     * @return array
     */
    public static function getRmHostList(string $rm_email, int $offset =0, int $limit=10 , int $host_id =0){
        $rm_host = self::select('users.id', 'users.name','users.last_name','users.email',\DB::raw('count(p.id) as prop_count'))->from('users')
            ->join('properties as p','users.id' ,'=','p.user_id')
            ->join('relationship_manager as rm' , 'p.id' , '=', 'rm.pid')
            ->join('admin as a', function($admin) use ($rm_email){
                $admin->where('a.email', $rm_email)->where(function($add_condition){
                    $add_condition->where('a.id', '=', DB::raw('rm.admin_id'))->orWhere('a.id', '=', DB::raw('rm.subtitute_admin_id'));
                });
            });

            if(empty($host_id) === false) {
            $rm_host->where('users.id','=',$host_id);
            }
            return $rm_host->offset($offset)->limit($limit)->groupBy('users.id')->orderBy('users.name', 'ASC')->get()->toArray();  
    } //end getRmHostList()


      /**
     * Update user contact.
     *
     * @param integer $user_id       User id.
     * @param string  $primary_contact      Primary Contact.
     * @param string  $secondary_contact      Secondary Contact
     * @param integer $mobile_verify Is user verified.
     *
     * @return boolean
     */
    public static function updateUserPrimaryContactDetail(int $user_id, string $primary_contact, string $secondary_contact, int $mobile_verify=0)
    {
        $user                = self::find($user_id);
        $user->mobile_verify = $mobile_verify;
        $user->contact       = $primary_contact;
        $user->secondry_contact = $secondary_contact;

        if ($user->save() === true) {
            return true;
        }

        return false;

    }//end updateUserPrimaryContactDetail()

    /**
      * Save user detail.
      *
      * @param array $params User Save Parameters.
      *
      * @return object
    */
    public static function createUserProfile(array $params){
       // dd($params['email']);
        $user                    = new self;
        $user->email             = $params['email'];
        $user->password          = $params['password'];
        $user->name              = $params['name'];
        $user->last_name         = $params['last_name'];
        $user->contact           = $params['contact'];
        $user->dial_code         = $params['dial_code'];
        $user->ip_address        = $params['ip_address'];
        $user->country           = $params['country'];
        $user->state             = $params['state'];
        $user->city              = $params['city'];
        $user->wallet_currency   = $params['wallet_currency'];
        $user->confirmation_code = $params['confirmation_code'];
        $user->auth_key          = $params['auth_key'];
        $user->signup_source     = $params['signup_source'];
        $user->signup_method     = $params['signup_method'];
        $user->profile_img       = $params['profile_img'];
        $user->base_currency     = $params['base_currency'];
        $user->prive_manager     = $params['prive_manager'];

        if ($user->save()) {
            $user['id'] = $user->id;
            return $user;
        }
        return (object) [];

    }

    /**
     * Get Trashed User By email id.
     *
     * @param string $email_or_phone User Email or Phone.
     *
     * @return object
     */
    public static function getUserWithTrashedByEmailOrPhone(string $email_or_phone,string $dialcode ='91')
    {
        if (empty($email_or_phone) === true) {
            return (object) [];
        }

        // Change column name whatever you use in credentials.
        $user = self::where(
            function ($query) use ($email_or_phone,$dialcode) {
                $query->where('email', $email_or_phone)->orWhere(
                    function ($query) use ($email_or_phone , $dialcode) {
                        $query->where('contact', $email_or_phone)->where('dial_code', $dialcode);
                    }
                );
            }
        )->withTrashed()->first();
        
        return $user;

    }//end getUserWithTrashedByEmailOrPhone()

     /**
     * Get Trashed User By Facebook id.
     *
     * @param string $facebook_id Facebook Id.
     *
     * @return object
     */
    public static function getUserWithTrashedByFacebookId(string $facebook_id)
    {
        $user = self::where('fb_id', $facebook_id)->withTrashed()->first();

        return $user;

    }//end getUserWithTrashedByFacebookId()

    /**
     * Get Trashed User By Apple id.
     *
     * @param string $apple_id Apple Id.
     *
     * @return object
     */
    public static function getUserWithTrashedByAppleId(string $apple_id)
    {
        $user = self::where('apple_id', $apple_id)->withTrashed()->first();

        return $user;

    }//end getUserWithTrashedByAppleId()


      /**
     * Create User.
     *
     * @param array $profile User Data.
     *
     * @return object
     */
    public static function createUser(array $profile)
    {
        //print_r($profile);die;
        // Create New User By Merging User location info data.
        // Required data (email, first_name, last_name).
        $user = new self;

        // User Required fillable data.
        $user->email     = $profile['email'];
        $user->name      = $profile['name'];
        $user->contact   = $profile['contact'];
        $user->dial_code = $profile['dial_code'];

        // User Profile Info.
        $user->password      = $profile['password'];
        $user->last_name     = $profile['last_name'];
        $user->gender        = $profile['gender'];
        $user->base_currency = $profile['base_currency'];
        $user->profile_img   = $profile['profile_img'];
        $user->dob           = (empty($profile['dob']) === false) ? $profile['dob'] : null;

        // Social Info.
        $user->google_id = $profile['google_id'];
        $user->fb_id     = $profile['fb_id'];

        // Register Tracking data.
        $user->signup_method = $profile['signup_method'];
        $user->signup_source = $profile['signup_source'];

        // User Identification data Eg Verified or not.
        $user->email_verify = $profile['email_verify'];

        $user->mobile_verify = $profile['mobile_verify'];
        // User Location Info data vi Ip Address.
        $user->ip_address      = $profile['ip_address'];
        $user->country         = $profile['country'];
        $user->state           = $profile['state'];
        $user->city            = $profile['city'];
        $user->wallet_currency = $profile['wallet_currency'];

        // Merge Auth and Confirmation code for old Api (need to remove column from db).
        $user->confirmation_code = $profile['confirmation_code'];
        $user->auth_key          = $profile['auth_key'];
        $user->last_activity      = Carbon::now();

        // Persist User data.
        $user->save();

        return $user;

    }//end createUser()


     /**
     * Update user Info.
     *
     * @param array $data Data to be updated.
     *
     * @return boolean
     */
    public function updateProfile(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        $this->save();

        return $this;

    }//end updateProfile()

}//end class
