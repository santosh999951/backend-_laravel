<?php
/**
 * Model containing data regarding properties
 */

namespace App\Models;

use DB;
use \Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Builder as EloquentQuery;
use App\Models\{PropertyPricing, User};
use App\Libraries\v1_6\ProperlyService;

use Helper;

/**
 * Class Property
 */
class Property extends Model
{
    use SoftDeletes;

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'properties';

    /**
     * Variable definition.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    /**
     * User property table relationship.
     *
     * @return object
     */
    public function user()
    {
        return $this->belongsTo(User::class);

    }//end user()


    /**
     * User property price table relationship.
     *
     * @return object
     */
    // phpcs:ignore
    public function property_price()
    {
        return $this->hasOne('App\Models\PropertyPricing', 'pid');

    }//end property_price()


    /**
     * Property detail table relationship.
     *
     * @return object
     */
    // phpcs:ignore
    public function property_details()
    {
        return $this->hasOne('App\Models\PropertyDetail', 'pid');

    }//end property_details()


    /**
     * Property detail table relationship.
     *
     * @return object
     */
    // phpcs:ignore
    public function property_photography()
    {
        return $this->hasOne('App\Models\PropertyPhotography', 'pid');

    }//end property_photography()


    /**
     * Get Property with trashed.
     *
     * @param integer $id Id.
     *
     * @return object
     */
    public static function getPropertyWithTrashed(int $id)
    {
        return self::withTrashed()->find($id);

    }//end getPropertyWithTrashed()


    /**
     * Get property object.
     *
     * @param integer $property_id Property id.
     *
     * @return Property
     */
    public static function getPropertyById(int $property_id)
    {
        return self::find($property_id);

    }//end getPropertyById()


     /**
      * Save property detail.
      *
      * @param array $params Property Save Parameters.
      *
      * @return object
      */
    public function saveProperty(array $params)
    {
        if (isset($params['property_id']) === true) {
            $property = self::find($params['property_id']);
        } else {
            $property          = new self;
            $property->user_id = $params['user_id'];
        }

        // Property Unit Info.
        $property->property_type = $params['property_type'];
        $property->room_type     = $params['room_type'];
        $property->units         = $params['units'];
        $property->accomodation  = $params['accomodation'];
        $property->bedrooms      = $params['bedrooms'];
        $property->beds          = $params['beds'];
        $property->bathrooms     = $params['bathrooms'];

        // Property Name Info.
        $property->title          = $params['title'];
        $property->new_title      = $params['title'];
        $property->properly_title = $params['properly_title'];
        $property->search_title   = $params['title'];
        $property->description    = $params['description'];

        $property->tag                = $params['tags'];
        $property->noc                = $params['noc_status'];
        $property->currency           = $params['currency'];
        $property->cancelation_policy = $params['cancelation_policy'];

        // Property Stay Info.
        $property->min_nights = $params['min_nights'];
        $property->max_nights = $params['max_nights'];
        $property->check_in   = $params['check_in_time'];
        $property->checkout   = $params['check_out_time'];
        $property->amenities  = $params['amenities'];

        // Property Location Info.
        $property->address        = $params['encripted_address'];
        $property->area           = $params['area'];
        $property->search_keyword = $params['search_keyword'];
        $property->city           = $params['city'];
        $property->state          = $params['state'];
        $property->country        = $params['country_code'];
        $property->zipcode        = $params['zipcode'];

        // Save Lat Long.
        $property->latitude  = $params['latitude'];
        $property->longitude = $params['longitude'];

        // Save Virtual lat long.
        $property->v_lat = $params['virtual_lat'];
        $property->v_lng = $params['virtual_long'];

        // Property Enabled status on basis of Host Contact and Email Verification.
        $property->enabled = $params['enabled'];

        // Admin Trackings.
        $property->by_admin     = $params['is_listed_by_admin'];
        $property->admin_id     = $params['admin_id'];
        $property->converted_by = $params['converted_by'];
        $property->gst_no       = $params['gstin'];

        // Image Videos Info.
        $property->image_caption = $params['image_caption'];
        $property->video_link    = $params['video_link'];

        $property->availability          = $params['availability'];
        $property->loaded_booking_allow  = $params['loaded_booking_allow'];
        $property->property_last_updated = Carbon::now('Asia/Kolkata')->toDateTimeString();
        $property->service_fee           = $params['service_fee'];

        // Update property Status.
        if (isset($params['status']) === true) {
            $property->status = $params['status'];
        }

        if (isset($params['last_updated_by_admin']) === true) {
            $property->last_updated_by_admin = $params['last_updated_by_admin'];
        }

        if (isset($params['accomodation_changed_by']) === true) {
            $property->accomodation_changed_by = $params['accomodation_changed_by'];
        }

        if ($property->save() === false) {
            return (object) [];
        }

        return $property;

    }//end saveProperty()


     /**
      * Save property hash id.
      *
      * @param string $hash_id Hash Id.
      *
      * @return object
      */
    public function updatePropertyHashId(string $hash_id)
    {
        $this->hash_id = $hash_id;
        $this->save();

        return $this;

    }//end updatePropertyHashId()


    /**
     * Get property detail.
     *
     * @param integer $property_id      Property id.
     * @param integer $guests           Guests.
     * @param integer $bedrooms         Bedrooms.
     * @param boolean $check_enabled    Check Enable Condition.
     * @param boolean $with_admin_score With Admin score.
     *
     * @return array
     */
    public static function getPropertyDetailsForPreviewPageById(int $property_id, int $guests, int $bedrooms, bool $check_enabled=true, bool $with_admin_score=true)
    {
        //phpcs:ignore
        $property = self::from('properties')->join('property_pricing', 'properties.id', '=', 'property_pricing.pid')->join('property_type', 'property_type.id', '=', 'properties.property_type')->join('room_type', 'room_type.id', '=', 'properties.room_type')->join('users as u', 'u.id', '=', 'properties.user_id')->join('property_stats_new', 'property_stats_new.id', '=', 'properties.id', 'left')->leftjoin('prive_manager_taggings', 'properties.id', '=', 'prive_manager_taggings.pid')->select(
            'properties.id',
            'properties.user_id',
            'properties.prive',
            'properties.cancelation_policy',
            'properties.title',
            'properties.area',
            'properties.city',
            'properties.state',
            'properties.country',
            'properties.currency',
            'properties.service_fee',
            'properties.description',
            'properties.address',
            'properties.custom_discount',
            'properties.v_lat as latitude',
            'properties.v_lng as longitude',
            'properties.instant_book',
            'properties.fake_discount',
            'properties.cash_on_arrival',
            'properties.accomodation',
            'properties.room_type',
            'properties.bedrooms',
            'properties.beds',
            'properties.bathrooms',
            'properties.property_type',
            'properties.check_in',
            'properties.checkout',
            'properties.amenities',
            'properties.min_nights',
            'properties.max_nights',
            'properties.units',
            'properties.enabled',
            'properties.status',
            'properties.description',
            'property_pricing.per_night_price',
            'property_pricing.per_week_price',
            'property_pricing.per_month_price',
            'property_pricing.additional_guest_count',
            'property_pricing.additional_guest_fee',
            'property_pricing.cleaning_mode',
            'property_pricing.cleaning_fee',
            'property_pricing.gh_commission',
            'property_pricing.markup_service_fee',
            'property_pricing.properly_commission as pc_properly_commission',
            'prive_manager_taggings.properly_commission as pmt_properly_commission',
            'property_type.name as property_type_name',
            'property_stats_new.property_score',
            'room_type.name as room_type_name',
            'u.gender',
            'u.name as host_name',
            DB::raw('RTRIM(CONCAT(u.name, " ", u.last_name)) AS host_fullname'),
            'u.dial_code as host_dial_code',
            'u.contact as host_contact',
            'u.email as host_email',
            'u.profile_img AS host_image',
            DB::raw('ceil(if('.$guests.'/properties.accomodation > '.$bedrooms.'/properties.bedrooms, '.$guests.'/properties.accomodation, '.$bedrooms.'/properties.bedrooms)) as units_consumed')
        )->where('properties.id', $property_id);

        $is_host = (Auth::check() === true) ? User::isUserHost((int) Auth::user()->id) : false;

        if ($is_host === false && $with_admin_score === true) {
            $property->isAdminScoreGreaterThanOne();
        }

        if ($check_enabled === true) {
            $property->isEnabled()->isActive();
        }

        $property = $property->first();

        return (empty($property) === false) ? $property->toArray() : [];

    }//end getPropertyDetailsForPreviewPageById()


    /**
     * Get enabled properties.
     *
     * @param EloquentQuery $query Query to be passed.
     *
     * @return EloquentQuery
     */
    public static function scopeIsEnabled(EloquentQuery $query)
    {
        return $query->where('enabled', 1);

    }//end scopeIsEnabled()


    /**
     * Get active properties.
     *
     * @param EloquentQuery $query Query to be passed.
     *
     * @return EloquentQuery
     */
    public static function scopeIsActive(EloquentQuery $query)
    {
        return $query->where('properties.status', 1);

    }//end scopeIsActive()


    /**
     * Get positive admin score properties.
     *
     * @param EloquentQuery $query Query to be passed.
     *
     * @return EloquentQuery
     */
    public static function scopeIsAdminScoreGreaterThanOne(EloquentQuery $query)
    {
        return $query->where('admin_score', '>', 0);

    }//end scopeIsAdminScoreGreaterThanOne()


    /**
     * Add limit and offset.
     *
     * @param EloquentQuery $query  Query to be passed.
     * @param integer       $offset Offset.
     * @param integer       $limit  Limit.
     *
     * @return EloquentQuery
     */
    public static function scopeAddLimit(EloquentQuery $query, int $offset, int $limit)
    {
        return $query->offset($offset)->limit($limit);

    }//end scopeAddLimit()


    /**
     * Check if property exists.
     *
     * @param integer $property_id      Property id.
     * @param boolean $with_admin_score With admin score.
     *
     * @return integer
     */
    public static function checkIfPropertyExistsById(int $property_id, bool $with_admin_score=true)
    {
        $query = self::where('id', '=', $property_id);

        $is_host = (Auth::check() === true) ? User::isUserHost((int) Auth::user()->id) : false;

        if ($is_host === false && $with_admin_score === true) {
            $query->isAdminScoreGreaterThanOne();
        }

        return $query->count();

    }//end checkIfPropertyExistsById()


    /**
     * Get Property Calendar last updated dated.
     *
     * @param integer $property_id Property id.
     *
     * @return string
     */
    public static function getProeprtyCalendarLastUpdated(int $property_id)
    {
        $query = self::select(
            DB::raw(
                '(CASE WHEN MAX(cl.created_at) > MAX(cl.updated_at) 
                                            THEN MAX(cl.created_at) 
                                        ELSE MAX(cl.updated_at) END) as last_calendar_update'
            )
        )->from('properties')->join('calendar_log as cl', 'cl.pid', '=', 'properties.id')->where('properties.id', '=', $property_id)->first();
        if (empty($query) === true) {
            return '';
        }

        return $query->toArray()['last_calendar_update'];

    }//end getProeprtyCalendarLastUpdated()


    /**
     * Get image base url.
     *
     * @param array $headers Headers array containing screen size.
     *
     * @return array
     */
    public static function imageBaseUrlAsPerDeviceSizeAndConnection(array $headers)
    {
        // Device size params.
        $width           = (isset($headers['screen_width']) === true) ? (int) $headers['screen_width'] : 0;
        $height          = (isset($headers['screen_height']) === true) ? (int) $headers['screen_height'] : 0;
        $image_optimized = (isset($headers['image_optimized']) === true) ? $headers['image_optimized'] : 0;
        $connection_type = (isset($headers['connection_type']) === true) ? $headers['connection_type'] : '';
        $device_type     = (empty($headers['device-type']) === false) ? $headers['device-type'][0] : ((empty($headers['device_type']) === false) ? $headers['device_type'] : '');

        $image_type = (isset($headers['image_type']) === true) ? $headers['image_type'] : '';
        // Possible connection types.
        $slow_connections = [
            'CDMA',
            '1xRTT',
            'EDGE',
            'GPRS',
            '2g',
        ];

        switch ($image_type) {
            case 'attraction':
                    // Width array.
                    $width_array = [
                        S3_ATTRACTION_1X_DIR   => 320,
                        S3_ATTRACTION_1_5X_DIR => 480,
                        S3_ATTRACTION_2X_DIR   => 640,
                        S3_ATTRACTION_3X_DIR   => 960,
                    ];

                    // Property base url.
                    if ($device_type === 'website') {
                        $base_url = ($image_optimized === 1) ? S3_ATTRACTION_2X_DIR : S3_ATTRACTION_3X_DIR;
                    } else {
                        $base_url = ($image_optimized === 1) ? S3_ATTRACTION_1X_DIR : S3_ATTRACTION_2X_DIR;
                    }

                    // Set base url based on screen size.
                    if ($width > 0) {
                        foreach ($width_array as $key => $value) {
                            if ($value >= $width) {
                                $base_url = $key;
                                break;
                            }
                        }
                    }

                    // Set base url to min size when connection is slow.
                    if (in_array($connection_type, $slow_connections) === true) {
                        $base_url = ($device_type === 'website') ? S3_ATTRACTION_3X_DIR : S3_ATTRACTION_1X_DIR;
                    }
            break;

            default:
                // Width array.
                $width_array = [
                    S3_PROPERTY_1X_DIR   => 320,
                    S3_PROPERTY_1_5X_DIR => 480,
                    S3_PROPERTY_2X_DIR   => 640,
                    S3_PROPERTY_3X_DIR   => 960,
                ];

                // Property base url.
                if ($device_type === 'website') {
                    $base_url = ($image_optimized === 1) ? S3_PROPERTY_2X_DIR : S3_PROPERTY_3X_DIR;
                } else {
                    $base_url = ($image_optimized === 1) ? S3_PROPERTY_1X_DIR : S3_PROPERTY_2X_DIR;
                }

                // Set base url based on screen size.
                if ($width > 0) {
                    foreach ($width_array as $key => $value) {
                        if ($value >= $width) {
                            $base_url = $key;
                            break;
                        }
                    }
                }

                // Set base url to min size when connection is slow.
                if (in_array($connection_type, $slow_connections) === true) {
                    $base_url = ($device_type === 'website') ? S3_PROPERTY_3X_DIR : S3_PROPERTY_1X_DIR;
                }
            break;
        }//end switch

        // Return cdn property base url and screen details.
        return [
            'width'           => $width,
            'height'          => $height,
            'image_optimized' => $image_optimized,
            'connection_type' => $connection_type,
            'image_base_url'  => $base_url,
        ];

    }//end imageBaseUrlAsPerDeviceSizeAndConnection()


    /**
     * Get recently searched properties.
     *
     * @param integer $user_id    Discount     User id.
     * @param string  $start_date Start date.
     * @param string  $end_date   End date.
     * @param integer $bedroom    Bedrooms.
     * @param string  $currency   Currency.
     * @param integer $guests     Guests.
     * @param integer $days       Days.
     * @param integer $offset     Offset.
     * @param integer $limit      Limit.
     *
     * @return array
     */
    public static function getRecentlySearchedProperties(int $user_id, string $start_date, string $end_date, int $bedroom, string $currency, int $guests, int $days, int $offset, int $limit)
    {
        //phpcs:disable
        // Max used (in order by) for temporary fix while other column of property view not in used. 
        $recently_viewed_properties = DB::select(
            "
            select 
                ps.property_score,
                p.id,
                p.title,
                p.area,
                p.city,
                p.state,
                p.country,
                p.currency,
                p.prive,
                p.cancelation_policy,
                p.v_lat as latitude,
                p.v_lng as longitude,
                p.instant_book,
                p.fake_discount,
                p.cash_on_arrival,
                p.min_nights,
                p.max_nights,
                p.accomodation,
                p.room_type,
                p.bedrooms,
                p.property_type,
                p.units,
                p.custom_discount,
                p.service_fee,
                pp.per_night_price,
                pp.gh_commission,
                pp.markup_service_fee,
                pp.additional_guest_count,
                pp.additional_guest_fee,
                pp.cleaning_fee,
                pp.cleaning_mode,
                pt.name as property_type_name,
                rt.name as room_type_name,
                u.id as host_id,
                u.gender as host_gender,
                u.profile_img as host_image,
                RTRIM(u.name) as host_name,
                ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms)) as units_consumed
            FROM properties p
            INNER JOIN property_pricing pp 
                ON p.id = pp.pid
            INNER JOIN currency_conversion cc 
                ON p.currency = cc.currency_code
            INNER JOIN property_type pt 
                ON p.property_type = pt.id
            INNER JOIN room_type rt
                ON p.room_type = rt.id
            INNER JOIN users u
                ON p.user_id = u.id
            LEFT JOIN property_stats_new ps 
                ON ps.id=p.id
            LEFT JOIN property_views pv
                ON pv.user_id = $user_id                  
            WHERE pv.user_id = '".$user_id."'
                AND pv.property_id = p.id
            GROUP BY pv.property_id
            ORDER BY max(pv.updated_at) desc, pv.updated_at desc
            LIMIT ".$limit ." 
            OFFSET ".$offset 
          );
        //phpcs:enable
        return $recently_viewed_properties;

    }//end getRecentlySearchedProperties()


    /**
     * Calculate final price.
     *
     * @param float   $final_rate       Payable amount.
     * @param integer $discount_percent Discount percentage.
     * @param integer $ceil_result      Take the ceiling value.
     * @param integer $service_fee      Total service fee.
     * @param integer $extra_guest      Total extra guests.
     *
     * @return mixed
     */
    public static function calculateFinalRate(float $final_rate, int $discount_percent, int $ceil_result=0, int $service_fee=0, int $extra_guest=0)
    {
        // ((100 + 1) * 0.85 * 1.15).
        $final_value = ((($final_rate + $extra_guest) * ((100 - $discount_percent) / 100)) / ((100 - $service_fee) * 100));

        return ($ceil_result === 1) ? ceil($final_value) : $final_value;

    }//end calculateFinalRate()


    /**
     * Get properties by location.
     *
     * @param integer $additional_guest_count Extra guests allowed.
     * @param integer $accomodation           Total guests allowed.
     *
     * @return array
     */
    public static function getBaseAndAdditionalGuestCount(int $additional_guest_count, int $accomodation)
    {
        // Additional_guest_count - number of people that can live in specified price.
        // Accomodation - total number of guests that can live in room.
        $response = [
            'base_guest_count'  => 0,
            'extra_guest_count' => 0,
            'total_guest_count' => 0,
        ];

        // Set base and extra guest count.
        $response['base_guest_count']  = ($additional_guest_count > 0) ? $additional_guest_count : $accomodation;
        $response['extra_guest_count'] = ($additional_guest_count > 0) ? ($accomodation - $additional_guest_count) : 0;
        $response['total_guest_count'] = $accomodation;

        return $response;

    }//end getBaseAndAdditionalGuestCount()


    /**
     * Get properties by location.
     *
     * @param array $data Location data to get properties.
     *
     * @return array
     */
    public static function getPropertiesAsPerLocation(array $data)
    {
        // Collect params.
        $country = $data['country'];
        $state   = $data['state'];
        $city    = (array_key_exists('city', $data) === true) ? $data['city'] : '';

        // Make query.
        $query = self::where('country', '=', $country)->where('state', '=', $state);

        // Add city.
        if (empty($city) === false) {
            $query->where('city', $city);
        }

        return $query->isActive()->isEnabled()->where('search_keyword', '!=', '')->select('search_keyword', DB::raw('count(*) as total'), 'city', 'area')->orderby('search_keyword', 'ASC')->groupBy('search_keyword')->distinct()->get()->toArray();

    }//end getPropertiesAsPerLocation()


    /**
     * Get properties except current city.
     *
     * @param array $data Location data to get properties.
     *
     * @return array
     */
    public static function getPropertiesExceptCurrentCity(array $data)
    {
        // Collect params.
        $country = $data['country'];
        $state   = $data['state'];
        $city    = (array_key_exists('city', $data) === true) ? $data['city'] : '';

        $query = self::select('city')->isActive()->isEnabled()->isAdminScoreGreaterThanOne()->distinct();

        // Make query.
        $query->where('country', '=', $country)->where('state', '=', $state);

        $locations = $query->get();

        if (empty($locations) === true) {
            return [];
        }

        return $locations->toArray();

    }//end getPropertiesExceptCurrentCity()


    /**
     * Get property title.
     *
     * @param array $property Property related data to get title.
     *
     * @return string
     */
    public static function propertyTitle(array $property)
    {
        // Params needed - room_type, room_type_name, bedrooms, property_type, city, units_consumed, title.
        if (in_array($property['room_type'], ROOM_TYPE) === true) {
            if ($property['room_type'] === 1) {
                $title = 'Entire '.$property['bedrooms'].'-bedroom '.strtolower($property['property_type']).' in '.ucfirst($property['city']);
            } else {
                if ($property['units_consumed'] > 1) {
                    $title = '1 '.strtolower($property['room_type_name']).'s in '.ucfirst($property['city']);
                } else {
                    $title = strtolower($property['room_type_name']).' in '.ucfirst($property['city']);
                }
            }
        } else {
            $title = trim(ucwords($property['title']));
        }//end if

        return $title;

    }//end propertyTitle()


    /**
     * Get user's properties.
     *
     * @param integer $user_id  User id.
     * @param integer $guests   No. of guests.
     * @param integer $bedrooms No. of bedrooms.
     * @param integer $offset   Offset.
     * @param integer $limit    Limit.
     *
     * @return array
     */
    public static function getUserProperties(int $user_id, int $guests, int $bedrooms, int $offset=0, int $limit=3)
    {
        //phpcs:disable
        $listings = self::from('properties')->join('property_pricing', 'property_pricing.pid', '=', 'properties.id')->join('property_type', 'property_type.id', '=', 'properties.property_type')->join('room_type', 'room_type.id', '=', 'properties.room_type')->join('users as u', 'properties.user_id', '=', 'u.id')->join('property_stats_new', 'property_stats_new.id', '=', 'properties.id', 'left')->select(
            'properties.id',
            'properties.user_id',
            'properties.prive',
            'properties.cancelation_policy',
            'properties.title',
            'properties.area',
            'properties.city',
            'properties.state',
            'properties.country',
            'properties.currency',
            'properties.service_fee',
            'properties.v_lat as latitude',
            'properties.v_lng as longitude',
            'properties.instant_book',
            'properties.fake_discount',
            'properties.cash_on_arrival',
            'properties.accomodation',
            'properties.room_type',
            'properties.bedrooms',
            'properties.property_type',
            'properties.custom_discount',
            'properties.units',
            'properties.min_nights',
            'properties.max_nights',
            'property_pricing.per_night_price',
            'property_pricing.additional_guest_count',
            'property_pricing.additional_guest_fee',
            'property_pricing.cleaning_fee',
            'property_pricing.cleaning_mode',
            'property_pricing.gh_commission',
            'property_pricing.markup_service_fee',
            'property_type.name as property_type_name',
            'property_stats_new.property_score',
            'room_type.name as room_type_name',
            'u.id as host_id',
            'u.gender as host_gender',
            'u.profile_img as host_image',
            DB::raw('ceil(if('.$guests.'/properties.accomodation > '.$bedrooms.'/properties.bedrooms, '.$guests.'/properties.accomodation, '.$bedrooms.'/properties.bedrooms)) as units_consumed, RTRIM(u.name) as host_name')
        )->where('properties.user_id', '=', $user_id)->isActive()->isEnabled()->isAdminScoreGreaterThanOne()->orderBy('properties.id', 'DESC')->offset($offset)->limit($limit)->get();
        //phpcs:enable

        if (empty($listings) === true) {
            return [];
        }

        return $listings->toArray();

    }//end getUserProperties()


    /**
     * Get similar properties.
     *
     * @param array $data Data required to fetch similar properties.
     *
     * @return array
     */
    public static function getSimilarProperties(array $data)
    {
        $user_currency_details = CurrencyConversion::where('currency_code', '=', $data['currency'])->first();
        $user_currency_factor  = $user_currency_details->exchange_rate;

        $min_price = round(((85 / 100) * $data['payable_amount']));
        $max_price = round(((115 / 100) * $data['payable_amount']));

         // phpcs:disable
        $custom_rate = "(
                (
                  1 - (
                    if(
                      ip.smart_discount,
                      if(
                        ip.smart_discount > ip.discount,
                        ip.smart_discount,
                        ip.discount
                      ),
                      ip.discount
                    )
                  )/100
                )
                *(((ceil(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms))) * ip.price + (if(pp.additional_guest_count, if(".$data['guests']." - pp.additional_guest_count * (ceil(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms))) > 0, ".$data['guests']." - pp.additional_guest_count * (ceil(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms))), 0),0)) * if(ip.extra_guest_cost,ip.extra_guest_cost,0)) ))";
        // phpcs:enable

         // phpcs:ignore
        $actual_rate_param = "((ceil(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms))) * pp.per_night_price + (if(pp.additional_guest_count, if(".$data['guests']." - pp.additional_guest_count * (ceil(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms))) > 0, ".$data['guests']." - pp.additional_guest_count * (ceil(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms))), 0),0)) * pp.additional_guest_fee + if(pp.cleaning_mode = 1,if(pp.cleaning_fee/".$data['days'].",pp.cleaning_fee/".$data['days'].",0),if(".$data['days']." = 0,0,pp.cleaning_fee)))";

         // phpcs:ignore
        $orig_custom_rate = "((ceil(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms))) * ip.price + (if(pp.additional_guest_count, if(".$data['guests']." - pp.additional_guest_count * (ceil(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms))) > 0, ".$data['guests']." - pp.additional_guest_count * (ceil(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms))), 0),0)) * if(ip.extra_guest_cost,ip.extra_guest_cost,0) )";

        $select_params[] = '';

         // phpcs:ignore
        $final_rate = " ((ceil(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms))) * pp.per_night_price + (if(pp.additional_guest_count, if(".$data['guests']." - pp.additional_guest_count * (ceil(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms))) > 0, ".$data['guests']." - pp.additional_guest_count * (ceil(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms))), 0),0)) * pp.additional_guest_fee + if(pp.cleaning_mode = 1,if(pp.cleaning_fee/".$data['days'].",pp.cleaning_fee/".$data['days'].",0),if(".$data['days']." = 0,0,pp.cleaning_fee))) ";

        // phpcs:ignore
        $select_params[] = " ceil(((((".$data['days']."-if(ip.custom_price_days,ip.custom_price_days,0)) * $final_rate/(100 - p.service_fee))*100 + ( (".$data['days']."-if(ip.custom_price_days, ip.custom_price_days, 0)) * ( ($final_rate) * (pp.markup_service_fee/100) )) + if(ip.custom_rate,ip.custom_rate,0))/cc.exchange_rate)*$user_currency_factor/".$data['days'].") as final_rate ";

        // phpcs:disable
         $select_params[] = " ceil
        (
          ((
            (".$data['days']."-if(ip.custom_price_days,ip.custom_price_days,0)
          )
           * $final_rate
        + ( 
            (".$data['days']."
              -
                if(ip.custom_price_days, ip.custom_price_days, 0)
            ) 
            * 
            ( 
              ($final_rate) * (pp.markup_service_fee/100) 
            )
          )
       + if(ip.custom_rate_without_service_fee,ip.custom_rate_without_service_fee,0)
       )
       /cc.exchange_rate
      )*$user_currency_factor/".$data['days'].") as final_rate_without_service_fee ";

        $similar_properties = self::select(
                                        'p.search_keyword',
                                        'p.cash_on_arrival',
                                        'p.total_booking_count',
                                        'p.admin_score',
                                        'p.id',
                                        'p.user_id',
                                        'p.min_nights',
                                        'p.max_nights',
                                        'p.property_type',
                                        'p.search_title as title',
                                        'p.area',
                                        'p.city',
                                        'p.state',
                                        'p.country',
                                        'p.property_images',
                                        'p.currency',
                                        'p.instant_book',
                                        'p.fake_discount',
                                        'p.latitude',
                                        'p.longitude',
                                        'p.v_lat',
                                        'p.v_lng',
                                        'p.accomodation',
                                        'p.room_type',
                                        'p.custom_discount',
                                        'p.bedrooms',
                                        'ps.property_score',
                                        'ps.booking_request_count',
                                        'ps.views_count',
                                        'pp.per_night_price',
                                        'cc.exchange_rate',
                                        'rt.name as room_type_name',
                                        'pt.name as property_type_name',
                                        'ip.avg_per_night_price',
                                        'ip.min_units',
                                        'ip.host_discount',
                                        'ip.smart_discount',

                                        'host.id as host_id',
                                        'host.gender as host_gender',
                                        'host.profile_img as host_image'
                                    )
                                    ->selectRaw(
                                        "
                                         RTRIM(host.name) as host_name,
                                         CEIL(if(".$data['guests']."/p.accomodation > ".$data['units']."/p.bedrooms, ".$data['guests']."/p.accomodation, ".$data['units']."/p.bedrooms)) as units_consumed,
                                         IF(p.instant_book < ip.is_instant_booking, p.instant_book,ip.is_instant_booking) AS instant_book,
                                         IF(0,NULL,avg_response_time) AS avg_response_time,
                                         IF(0/p.bedrooms >= 1,0/p.bedrooms,1+(1 - 0/p.bedrooms)) AS bedroom_score,
                                         (6371 * acos (cos ( radians('".$data['latitude']."') ) * cos( radians(p.latitude) ) * cos( radians(p.longitude) - radians( '".$data['longitude']."') ) + sin ( radians( '".$data['latitude']."') ) * sin( radians(p.latitude) ))) as distance,
                                         ceil(((((".$data['days']."-if(ip.custom_price_days,ip.custom_price_days,0)) * $final_rate/(100 - p.service_fee))*100 + ( (".$data['days']."-if(ip.custom_price_days, ip.custom_price_days, 0)) * ( ($final_rate) * (pp.markup_service_fee/100) )) + if(ip.custom_rate,ip.custom_rate,0))/cc.exchange_rate)*$user_currency_factor/".$data['days'].") as final_rate,

                                         ceil
                                            (
                                              ((
                                                (".$data['days']."-if(ip.custom_price_days,ip.custom_price_days,0)
                                              )
                                               * $final_rate
                                            + ( 
                                                (".$data['days']."
                                                  -
                                                    if(ip.custom_price_days, ip.custom_price_days, 0)
                                                ) 
                                                * 
                                                ( 
                                                  ($final_rate) * (pp.markup_service_fee/100) 
                                                )
                                              )
                                           + if(ip.custom_rate_without_service_fee,ip.custom_rate_without_service_fee,0)
                                           )
                                           /cc.exchange_rate
                                          )*$user_currency_factor/".$data['days'].") as final_rate_without_service_fee,


                                          IF((ip.host_discount > 0 OR ip.smart_discount > 0), (ip.host_discount +  ip.smart_discount - (ip.host_discount * ip.smart_discount)/100), p.fake_discount) as fake_discount,
                                          ceil(((((".$data['days']."-if(ip.custom_price_days,ip.custom_price_days,0)) * $actual_rate_param/(100 - p.service_fee))*100 + ( (".$data['days']."-if(ip.custom_price_days, ip.custom_price_days, 0)) * ( ($actual_rate_param) * (pp.markup_service_fee/100)) ) + if(ip.orig_custom_rate,ip.orig_custom_rate,0))/cc.exchange_rate)*$user_currency_factor/".$data['days'].") as actual_rate,
                                          ceil((((".$data['days']."-if(ip.custom_price_days,ip.custom_price_days,0)) * $actual_rate_param + ( (".$data['days']."-if(ip.custom_price_days, ip.custom_price_days, 0)) * ( ($actual_rate_param) * (pp.markup_service_fee/100)) ) + if(ip.orig_custom_rate_without_service_fee,ip.orig_custom_rate_without_service_fee,0))/cc.exchange_rate)*$user_currency_factor/".$data['days'].") as actual_rate_without_service_fee
                                          "
                                    )
                                    ->from('properties as p')->withTrashed()
                                    ->join('property_pricing as pp', 'pp.pid', '=', 'p.id')
                                    ->join('currency_conversion as cc', 'cc.currency_code', '=', 'p.currency')
                                    ->leftjoin('property_stats_new as ps', 'ps.id', '=', 'p.id')
                                    ->leftjoin('property_type as pt', 'pt.id', '=', 'p.property_type')
                                    ->leftjoin('room_type as rt', 'rt.id', '=', 'p.room_type')
                                    ->leftjoin('users as host', 'host.id', '=', 'p.user_id')
                                    ->leftjoin(DB::Raw("(
                                        select 
                                        ip.pid,
                                        ip.date, 
                                        if(min(is_available) > 0 ,min(available_units * is_available),0 ) as min_units, 
                                        if(min(is_available) > 0 ,min(booked_units * is_available),0 ) as booked_units, 
                                        group_concat(ip.instant_booking) as instant_booking_concat,
                                        min(ip.instant_booking) as is_instant_booking,
                                        ip.discount, 
                                        max(discount) as host_discount,
                                        ip.service_fee, 
                                        avg(ip.extra_guest_cost) AS avg_extra_guest_cost,
                                         avg(((ip.price/(100-ip.service_fee))*100)*(100-ip.discount)/100) as avg_per_night_price, 
                                        count(*) AS custom_price_days, 
                                        sum(ip.price) AS toal_custom_price, 

                                        sum(ip.extra_guest_cost) AS total_custom_avg_extra_guest_cost, 
                                        if(sum(booked_units), sum(booked_units), 0) as total_booked_units,
                                        sum($custom_rate /  (1 - (if(ip.service_fee,ip.service_fee,p.service_fee))/100)+  (($custom_rate)* (  ip.markup_service_fee/100 ))) as custom_rate ,

                                        max(smart_discount) as smart_discount ,
                                        sum($orig_custom_rate / (1 - (if(ip.service_fee,ip.service_fee,p.service_fee))/100) + (($orig_custom_rate ) * (  ip.markup_service_fee/100 ))) as orig_custom_rate,
                                        sum($custom_rate +  (($custom_rate)* (  ip.markup_service_fee/100 ))) as custom_rate_without_service_fee ,
                                         sum($orig_custom_rate  + (($orig_custom_rate ) * (  ip.markup_service_fee/100 ))) as orig_custom_rate_without_service_fee
                                    from inventory_pricing ip
                                    left join properties p on p.id = ip.pid
                                    left join property_pricing pp on p.id = pp.pid      
                                    where 
                                        ip.date >='".$data['start_date']."' 
                                        and 
                                        ip.date < '".$data['end_date']."' 
                                        and p.country = '".$data['country']."' 
                                        and p.state = '".$data['state']."' 
                                        group by ip.pid  
                                     ) ip "), "p.id" ,"=" ,"ip.pid")
                                    ->where('p.country', $data['country'])
                                    ->where('p.state', $data['state'])
                                    ->where('p.min_nights', '<=', $data['days'])
                                    ->where('p.max_nights', '>=', $data['days'])
                                    ->where('p.property_type', $data['property_type'])
                                    ->where('p.latitude', '!=', 0)
                                    ->where('p.longitude', '!=', 0)
                                    ->where('p.enabled', 1)
                                    ->where('admin_score', '!=', 0)
                                    ->where('status', 1)
                                    ->where('p.id', '!=', $data['property_id'])
                                    ->whereNull('p.deleted_at')
                                    ->whereRaw("ceil((((".$data['days']."-if(ip.custom_price_days,ip.custom_price_days,0)) * $actual_rate_param + ( (".$data['days']."-if(ip.custom_price_days, ip.custom_price_days, 0)) * ( ($actual_rate_param) * (pp.markup_service_fee/100)) ) + if(ip.custom_rate_without_service_fee,ip.custom_rate_without_service_fee,0))/cc.exchange_rate)*$user_currency_factor/".$data['days'].") BETWEEN $min_price AND $max_price"
                                    )
                                    ->having('distance', '<', 5)
                                    ->orderByRaw('distance, p.total_booking_count DESC, booking_request_count DESC, views_count DESC, p.admin_score DESC, -avg_response_time ASC')
                                    ->addLimit($data['offset'], $data['limit'])
                                    ->get();
        //phpcs:enable
        return (empty($similar_properties) === false) ? $similar_properties->toArray() : [];

    }//end getSimilarProperties()


    /**
     * Get host properties.
     *
     * @param integer $user            User.
     * @param integer $offset          Offset.
     * @param integer $limit           Limit.
     * @param integer $property_id     Property_id.
     * @param array   $property_type   Property_type.
     * @param array   $city            City.
     * @param array   $property_status Property_status.
     *
     * @return array
     */
    public static function getHostProperties(int $user, int $offset, int $limit, int $property_id, array $property_type=[], array $city=[], array $property_status=[])
    {
        //phpcs:disable
        $listings = self::from('properties')->join('property_pricing', 'property_pricing.pid', '=', 'properties.id')->join('property_type', 'property_type.id', '=', 'properties.property_type')->join('property_status', 'property_status.status_id', '=', 'properties.status')->join('room_type', 'room_type.id', '=', 'properties.room_type')->join('users as u', 'properties.user_id', '=', 'u.id')->join('property_stats_new', 'property_stats_new.id', '=', 'properties.id', 'left')->select(
            'properties.id',
            'properties.prive',
            'properties.cancelation_policy',
            'properties.title',
            'properties.area',
            'properties.city',
            'properties.state',
            'properties.country',
            'properties.currency',
            'properties.service_fee',
            'properties.v_lat as latitude',
            'properties.v_lng as longitude',
            'properties.instant_book',
            'properties.fake_discount',
            'properties.cash_on_arrival',
            'properties.accomodation',
            'properties.room_type',
            'properties.bedrooms',
            'properties.property_type',
            'properties.custom_discount',
            'properties.units',
            'properties.min_nights',
            'properties.max_nights',
            'property_pricing.per_night_price',
            'property_pricing.additional_guest_count',
            'property_pricing.additional_guest_fee',
            'property_pricing.cleaning_fee',
            'property_pricing.cleaning_mode',
            'property_pricing.gh_commission',
            'property_pricing.markup_service_fee',
            'property_type.name as property_type_name',
            'property_stats_new.property_score',
            'room_type.name as room_type_name',
            'properties.property_last_updated as last_updated',
            'properties.enabled',
            'properties.status',
            'property_status.status_title as status_text',
            'u.gender as host_gender',
            'u.profile_img AS host_image',
            'u.name as host_name',
            DB::raw('ceil(if('.DEFAULT_NUMBER_OF_GUESTS.'/properties.accomodation > '.DEFAULT_NUMBER_OF_UNITS.'/properties.bedrooms, '.DEFAULT_NUMBER_OF_GUESTS.'/properties.accomodation, '.DEFAULT_NUMBER_OF_UNITS.'/properties.bedrooms)) as units_consumed')
        )->where('properties.user_id', '=', $user);
        if(empty($property_id) === false) {
            $listings->where('properties.id', '=', $property_id);
        }
        if(empty($property_type) === false) {
            $listings->whereIn('properties.property_type', $property_type);
        }
        if(empty($property_status) === false) {
            $listings->where(
                function($query) use ($property_status){
                    if(in_array(ONLINE, $property_status) === true){
                        $query->where('properties.status',  APPROVED_REVIEW)->where('properties.enabled' ,1);
                    }

                    if(in_array(OFFLINE, $property_status) === true){
                        $query->orWhere('properties.status',  APPROVED_REVIEW)->where('properties.enabled' ,0);
                    }

                    unset($property_status[ONLINE]);
                    unset($property_status[OFFLINE]);

                    $query->orWhereIn('properties.status', $property_status);
                }
            );
        }

        if(empty($city) === false) {
            $listings->whereIn('properties.city',  $city);
        }
        $listings = $listings->orderBy('properties.enabled', 'DESC')->orderBy('properties.status', 'DESC')->orderBy('properties.property_last_updated', 'DESC')->orderBy('properties.id', 'DESC')->offset($offset)->limit($limit)->get();
        //phpcs:enable

        if (empty($listings) === true) {
            return [];
        }

        return $listings->toArray();

    }//end getHostProperties()


    /**
     * Get Prive properties.
     *
     * @param integer $prive_owner_id Prive Owner id.
     * @param integer $offset         Offset.
     * @param integer $limit          Limit.
     * @param boolean $active         Active.
     *
     * @return array
     */
    public static function getPriveProperties(int $prive_owner_id, int $offset=0, int $limit=100, bool $active=true)
    {
        //phpcs:disable
        $listings = self::from('properties')->join('property_pricing', 'property_pricing.pid', '=', 'properties.id')->join(
            'prive_owner as po',
            function ($join) use ($prive_owner_id) {
                $join->on('po.pid', '=', 'properties.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->select(
            'properties.id',
            'properties.title',
            'property_pricing.per_night_price',
            'properties.units',
            'properties.city',
            'properties.state',
            'properties.currency',
            'properties.status',
            'properties.enabled'

        );
        if($active === true) {
            $listings->isEnabled()->isActive();
        }
        $listings = $listings->orderBy('properties.enabled', 'DESC')->orderBy('properties.status', 'DESC')->orderBy('properties.property_last_updated', 'DESC')->orderBy('properties.id', 'DESC')->offset($offset)->limit($limit)->get();

        if (empty($listings) === true) {
            return [];
        }

        return $listings->toArray();

    }//end getPriveProperties()

    /**
     * Get Prive Manager properties.
     *
     * @param integer $prive_manager_id Prive Manager id.
     * @param integer $offset         Offset.
     * @param integer $limit          Limit.
     * @param boolean $active         Active.
     *
     * @return array
     */
    public function getPriveManagerProperties(int $prive_manager_id, int $offset=0, int $limit=1000, bool $active=true)
    {

        $properly_service = new ProperlyService;

        $user_properties = $properly_service->getUserProperties($prive_manager_id);

        // From Prive Manager.
        $listings = self::from('properties')->join('property_pricing as pp', function($join) use ($user_properties) {
            $join->on('pp.pid', '=', 'properties.id')->whereIn('properties.id', $user_properties);
        });

        // Select Data.
        $listings->select(
            'properties.id',
            'properties.title',
            'properties.properly_title',
            'pp.per_night_price',
            'properties.units',
            'properties.area',
            'properties.city',
            'properties.state',
            'properties.country',
            'properties.currency',
            'properties.latitude',
            'properties.longitude'
        );

        // Get Active Properties.
        if($active === true) {
            $listings->isEnabled();
        }
        
        // Set Order.
        $listings = $listings->where('properties.status',1)->orderBy('properties.enabled', 'DESC')->orderBy('properties.status', 'DESC')->orderBy('properties.id', 'ASC')->offset($offset)->limit($limit)->get();

        if (empty($listings) === true) {
            return [];
        }

        return $listings->toArray();

    }//end getPriveManagerProperties()

    /**
     * Get Prive Manager properties count.
     *
     * @param integer $prive_manager_id Prive Manager id.
     * @param boolean $active         Active.
     *
     * @return array
     */
    public function getPriveMangerPropertyCounts(int $prive_manager_id, bool $active=true)
    {

        $properly_service = new ProperlyService;

        $user_properties = $properly_service->getUserProperties($prive_manager_id);
        
        // From Prive Manager.
        $listings = self::from('properties')->whereIn('properties.id', $user_properties);
        
        // Get Active Properties.
        if($active === true) {
            $listings->isEnabled()->isActive();
        }

        return $listings->count();

    }//end getPriveMangerPropertyCounts()

    /**
     * Get host properties by id.
     *
     * @param integer $pid     Property id.
     * @param integer $host_id Host id.
     *
     * @return array
     */
    public static function getHostPropertyById(int $pid, int $host_id)
    {
        $property = self::select(
            'properties.id',
            'properties.title',
            'properties.area',
            'properties.city',
            'properties.state',
            'properties.country',
            'properties.currency',
            'properties.service_fee',
            'properties.v_lat as latitude',
            'properties.v_lng as longitude',
            'properties.instant_book',
            'properties.cash_on_arrival',
            'properties.accomodation',
            'properties.room_type',
            'properties.bedrooms',
            'properties.beds',
            'properties.bathrooms',
            'properties.property_type',
            'properties.amenities',
            'properties.min_nights',
            'properties.max_nights',
            'properties.units',
            'properties.description',
            'property_pricing.per_night_price',
            'property_pricing.per_week_price',
            'property_pricing.per_month_price',
            'property_pricing.additional_guest_count',
            'property_pricing.additional_guest_fee',
            'property_pricing.cleaning_mode',
            'property_pricing.cleaning_fee',
            'property_pricing.gh_commission',
            'property_pricing.markup_service_fee',
            'property_type.name as property_type_name',
            'room_type.name as room_type_name',
            'properties.property_last_updated as last_updated',
            'properties.enabled',
            'properties.status',
            'u.gender as host_gender',
            'u.profile_img AS host_image',
            'u.name as host_name',
            'u.auth_key as auth_key',
            'property_status.status_title as status_text',
            DB::raw('(CASE WHEN properties.avg_response_time > 0 THEN properties.avg_response_time ELSE 0 END) as avg_response_time'),
            'properties.total_booking_count as booking_count',
            // phpcs:ignore
            DB::raw('ceil(if('.DEFAULT_NUMBER_OF_GUESTS.'/properties.accomodation > '.DEFAULT_NUMBER_OF_UNITS.'/properties.bedrooms, '.DEFAULT_NUMBER_OF_GUESTS.'/properties.accomodation, '.DEFAULT_NUMBER_OF_UNITS.'/properties.bedrooms)) as units_consumed')
            // phpcs:ignore
        )->from('properties')->join('property_pricing', 'properties.id', '=', 'property_pricing.pid')->join('property_status', 'property_status.status_id', '=', 'properties.status')->join('property_type', 'property_type.id', '=', 'properties.property_type')->join('room_type', 'room_type.id', '=', 'properties.room_type')->join('users as u', 'properties.user_id', '=', 'u.id');

        $property = $property->where('properties.id', $pid)->where('properties.user_id', $host_id)->first();

        if (empty($property) === true) {
            return [];
        }

        return $property->toArray();

    }//end getHostPropertyById()

    /**
     * Get host listing property detail by id.
     *
     * @param integer $pid     Property id.
     * @param integer $host_id Host id.
     *
     * @return array
     */
    public function getHostListingPropertyById(int $pid, int $host_id)
    {
        $property = self::select(
            'properties.id',
            'properties.property_type',
            'properties.room_type',
            'properties.units',
            'properties.accomodation',
            'properties.bedrooms',
            'properties.beds',
            'properties.bathrooms',
            'properties.title',
            'properties.noc as noc_status',
            'properties.currency',
            'properties.cancelation_policy',
            'properties.min_nights',
            'properties.max_nights',
            'properties.check_in',
            'properties.checkout',
            'properties.amenities',
            'properties.address',
            'properties.area',
            'properties.city',
            'properties.state',
            'properties.country',
            'properties.search_keyword',
            'properties.zipcode',
            'properties.latitude',
            'properties.longitude',
            'properties.property_last_updated as last_updated',
            'properties.enabled',
            'properties.status',
            'properties.gst_no as gstin',
            'properties.video_link',
            'properties.tag',

            // Properties Detail Data.
            'property_details.policy_services',
            'property_details.your_space',
            'property_details.house_rule',
            'property_details.guest_brief',
            'property_details.interaction_with_guest',
            'property_details.local_experience',
            'property_details.from_airport',
            'property_details.train_station',
            'property_details.bus_station',
            'property_details.extra_detail',
            'property_details.usp',

            // Property Pricing Data.
            'property_pricing.per_night_price',
            'property_pricing.per_week_price',
            'property_pricing.per_month_price',
            'property_pricing.additional_guest_count',
            'property_pricing.additional_guest_fee',
            'property_pricing.gh_commission',
            'property_pricing.markup_service_fee',
            'property_pricing.cleaning_mode',
            'property_pricing.cleaning_fee'
        )->from('properties')->join('property_details', 'properties.id', '=', 'property_details.pid')->join('property_pricing', 'properties.id', '=', 'property_pricing.pid');

        $property = $property->where('properties.id', $pid)->where('properties.user_id', $host_id)->first();

        if (empty($property) === true) {
            return [];
        }

        return $property->toArray();

    }//end getHostListingPropertyById()


    /**
     * Check host properties exits.
     *
     * @param integer $pid     Property id.
     * @param integer $host_id Host id.
     *
     * @return array
     */
    public static function checkHostPropertyExist(int $pid, int $host_id)
    {
        $property = self::from('properties')->join('users as u', 'properties.user_id', '=', 'u.id')->where('properties.id', $pid)->where('properties.user_id', $host_id)->count();

        if ($property === 1) {
            return true;
        }

        return false;

    }//end checkHostPropertyExist()


    /**
     * Update host properties enabled status.
     *
     * @param integer $pid             Property id.
     * @param integer $host_id         Host id.
     * @param integer $property_status Property status.
     * @param integer $admin           Admin id.
     *
     * @return object
     */
    public static function updateHostPropertyStatus(int $pid, int $host_id, int $property_status, int $admin=0)
    {
        $property = self::where('id', $pid)->where('user_id', $host_id)->where('enabled', '!=', $property_status)->first();

        if (empty($property) === true) {
            return $property;
        }

        $property->enabled               = $property_status;
        $property->last_updated_by_admin = $admin;
        $property->save();

        return $property;

    }//end updateHostPropertyStatus()


    /**
     * Update properties last update time.
     *
     * @param integer $id Property id.
     *
     * @return integer
     */
    public static function updatePropertyLastUpdate(int $id)
    {
        return self::where('id', $id)->update(['property_last_updated' => Carbon::now('Asia/Kolkata')->toDateTimeString()]);

    }//end updatePropertyLastUpdate()


    /**
     * Clone Property.
     *
     * @param Property $property       Property Model.
     * @param string   $property_title Property title.
     * @param integer  $converted_by   Admin Id.
     *
     * @return boolean|integer
     */
    public static function cloneProperty(Property $property, string $property_title, int $converted_by)
    {
        $cloneproperty = $property->replicate();

        $cloneproperty->title         = $property_title;
        $cloneproperty->status        = 0;
        $cloneproperty->feather_given = 0;
        $cloneproperty->enabled       = 0;

        $cloneproperty->converted_by = $converted_by;
        $cloneproperty->admin_score  = 0;
        $cloneproperty->by_admin     = 0;
        $cloneproperty->admin_id     = 0;
        ;
        $cloneproperty->property_images     = '[]';
        $cloneproperty->avg_response_time   = '';
        $cloneproperty->total_booking_count = 0;
        $cloneproperty->instant_book        = 0;
        $cloneproperty->video_link          = '';

        if ($cloneproperty->save() === true) {
            return $cloneproperty;
        }

        return false;

    }//end cloneProperty()


    /**
     * Delete property.
     *
     * @param integer $id         Property id.
     * @param integer $deleted_by Deleted By id.
     *
     * @return object
     */
    public static function deleteProperty(int $id, int $deleted_by)
    {
        $property = self::where('id', $id)->first();

        // Update Deleted Data.
        $property->deleted_by = $deleted_by;
        $property->status     = PROPERTY_STATUS_DELETED;
        $property->save();

        // Delete Property.
        $property->delete();

        return $property;

    }//end deleteProperty()


    /**
     * GetAll city of host.
     *
     * @param integer $id Host id.
     *
     * @return object
     */
    public static function getAllCity(int $id)
    {
        return self::select('city')->distinct()->where('user_id', $id)->orderBy('city', 'ASC')->get()->toArray();

    }//end getAllCity()
    

   /**
     * Get Prive properties count.
     *
     * @param integer $id Property id.
     * @param boolean $active Property id.
     *
     * @return integer
     */
    public static function getPrivePropertiescount(int $prive_owner_id , bool $active = true) {

         $listings = self::from('properties')->join(
            'prive_owner as po',
            function ($join) use ($prive_owner_id) {
                $join->on('po.pid', '=', 'properties.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        );
        if($active === true) {
            $listings->isEnabled()->isActive();
        }
        $listings = $listings->count();

        return $listings;
    } // end getPrivePropertiescount()

    /**
     * Get Prive property detail.
     *
     * @param integer $prive_owner_id Prive Owner Id. 
     * @param integer $property_id   Property id.
     * @param integer $guests        Guests.
     * @param integer $bedrooms      Bedrooms.
     * @param boolean $check_enabled Check Enable Condition.
     *
     * @return array
     */
    public static function getPrivePropertyDetailsForPreviewPageById(int $prive_owner_id , int $property_id, int $guests, int $bedrooms, bool $check_enabled=true)
    {
        //phpcs:ignore
        $property = self::from('properties')->join('property_pricing', 'properties.id', '=', 'property_pricing.pid')->join('property_type', 'property_type.id', '=', 'properties.property_type')->join('room_type', 'room_type.id', '=', 'properties.room_type')->join('users as u', 'u.id', '=', 'properties.user_id')->join('property_stats_new', 'property_stats_new.id', '=', 'properties.id', 'left')->leftjoin('prive_manager_taggings', 'properties.id', '=', 'prive_manager_taggings.pid')->join(
            'prive_owner as po',
            function ($join) use ($prive_owner_id , $property_id) {
                $join->on('po.pid', '=', 'properties.id')->where('po.user_id', '=', $prive_owner_id)->where('po.pid', '=', $property_id)->whereNull('po.deleted_at');
            }
        )->select(
            'properties.id',
            'properties.user_id',
            'properties.prive',
            'properties.cancelation_policy',
            'properties.title',
            'properties.area',
            'properties.city',
            'properties.state',
            'properties.country',
            'properties.currency',
            'properties.service_fee',
            'properties.description',
            'properties.address',
            'properties.custom_discount',
            'properties.v_lat as latitude',
            'properties.v_lng as longitude',
            'properties.instant_book',
            'properties.fake_discount',
            'properties.cash_on_arrival',
            'properties.accomodation',
            'properties.room_type',
            'properties.bedrooms',
            'properties.beds',
            'properties.bathrooms',
            'properties.property_type',
            'properties.check_in',
            'properties.checkout',
            'properties.amenities',
            'properties.min_nights',
            'properties.max_nights',
            'properties.units',
            'properties.enabled',
            'properties.status',
            'properties.description',
            'property_pricing.per_night_price',
            'property_pricing.per_week_price',
            'property_pricing.per_month_price',
            'property_pricing.additional_guest_count',
            'property_pricing.additional_guest_fee',
            'property_pricing.cleaning_mode',
            'property_pricing.cleaning_fee',
            'property_pricing.gh_commission',
            'property_pricing.markup_service_fee',
            'property_pricing.properly_commission as pc_properly_commission',
            'prive_manager_taggings.properly_commission as pmt_properly_commission',
            'property_type.name as property_type_name',
            'property_stats_new.property_score',
            'room_type.name as room_type_name',
            'u.gender',
            'u.name as host_name',
            DB::raw('RTRIM(CONCAT(u.name, " ", u.last_name)) AS host_fullname'),
            'u.dial_code as host_dial_code',
            'u.contact as host_contact',
            'u.email as host_email',
            'u.profile_img AS host_image',
            DB::raw('ceil(if('.$guests.'/properties.accomodation > '.$bedrooms.'/properties.bedrooms, '.$guests.'/properties.accomodation, '.$bedrooms.'/properties.bedrooms)) as units_consumed')
        );

        $is_host = (Auth::check() === true) ? User::isUserHost((int) Auth::user()->id) : false;

        if ($is_host === false) {
            $property->isAdminScoreGreaterThanOne();
        }

        if ($check_enabled === true) {
            $property->isEnabled()->isActive();
        }

        $property = $property->first();

        return (empty($property) === false) ? $property->toArray() : [];

    }//end getPrivePropertyDetailsForPreviewPageById()

    /**
     * Get Traveller Prive properties.
     *
     * @param integer $offset         Offset.
     * @param integer $limit          Limit.
     * @param array $city         Prive City.
     *
     * @return array
     */
    public static function getTravellerPriveProperties(int $offset=0, int $limit=100,  array $city = [])
    {
        //phpcs:disable
        $listings = self::from('properties')->select(
            'properties.id',
            'properties.title',
            'properties.city',
            'properties.state',
            'properties.area'
        );
    
        if(empty($city) === false) {
            $listings->whereIn('properties.city', $city);
        }

        $listings = $listings->isEnabled()->isActive()->where('properties.prive',1)->orderBy('properties.enabled', 'DESC')->orderBy('properties.status', 'DESC')->orderBy('properties.property_last_updated', 'DESC')->orderBy('properties.id', 'DESC')->offset($offset)->limit($limit)->get();

        if (empty($listings) === true) {
            return [];
        } 

        return $listings->toArray();

    }//end getTravellerPriveProperties()

    /**
     * Get All citywise property count of Prive.
     *
     * @return array
     */
    public  static function getPriveCityWisePropertyCount()
    {
        return self::select('city', DB::raw('count(id) as property_count'))->where('prive', 1)->isEnabled()->isActive()->groupBy('city')->get()->toArray();

    }// end  getPriveCityWisePropertyCount()

    /**
     * Get User lastest property.
     * 
     * @params $user_id User Id.
     * 
     * @return array
     */
    public static function getPropertyId(int $user_id){
        $data = self::select('id')->where('user_id',$user_id)->where('status', 1)->first();
        
        $pid = ($data !== null) ? $data->id : 0;
        return $pid;

    }


    /**
     * Get ODA Search result.
     * 
     * @param array $params Params.
     * 
     * @return array 
     */
    public static function getODASearchResult(array $params){
        
       
        $email             = $params['email'];
        $contact           = $params['contact'];
        $state             = $params['state'];
        $city              = $params['city'];
       // $country           = $params['country'];
        $property_name     = $params['property_name'];

        $query       = 'select SQL_CALC_FOUND_ROWS ps.status_title as status, pi.image, u.contact, p.id as id, u.name, p.title,p.area,p.city, p.state, p.country
            from properties p 
            left join users u on u.id = p.user_id
            left join property_status ps on ps.status_id = p.status 
            left join (select pid, image from properties_images limit 1) as pi on pi.pid = p.id
            where 1=1 ';
        $where_query = '';

        if ($contact !== '' || $email !== '') {
            if ($contact !== '') {
                $where_query .= " and  u.contact = '$contact' ";
            }

            if ($email !== '') {
                $where_query .= " and  u.email = '$email' ";
            }
        } else if ($property_name !== '') {
            $where_query .= " and p.title like '%".$property_name."%' ";
        }

        // if ($country !== '') {
        //     $where_query .= " and p.country = '$country' ";
        // }

        if ($state !== '') {
            $where_query .= " and p.state = '$state' ";
        }

        if ($city !== '') {
            $where_query .= " and p.city = '$city' ";
        }

        if ($contact !== '' || $email !== '') {
            $where_query .= ' LIMIT 0,100 ';
        } else {
            $where_query .= ' LIMIT 0,50 ';
        }

        $data    = [];
        $message = '';
        if ($where_query !== '') {
            $final_query = $query.$where_query;
            $data        = DB::select($final_query);
            $count       = DB::select('SELECT FOUND_ROWS() cnt');

            if ($count[0]->cnt <= 50) {
                $message = $count[0]->cnt.' results found.';
            } else {
                $message = 'More than 50 results found.';
            }
        }
        return ['data' => $data, 'message' => $message];
    }


     /**
      * Get Prive owner property by id
      *
      * @param integer $prive_owner_id Prive Owner id to fetch expenses for.
      * @param integer $property_id    Property id to fetch property description for.
      *
      * @return array.
      */
    public static function getPriveOwnerPropertyById(int $prive_owner_id , int $property_id) {

         $listings = self::select(
            'properties.id' , 'title'
        )->from('properties')->join('property_pricing', 'property_pricing.pid', '=', 'properties.id')->join(
            'prive_owner as po',
            function ($join) use ($prive_owner_id) {
                $join->on('po.pid', '=', 'properties.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->where('properties.id', $property_id)->first();

        if (empty($listings) === true) {
            return [];
        }

        return $listings->toArray();

    }
    
     /**
     * Get Live Properties by owner Id.
     *
     * @param array   $prive_owner_id Prive Owner Id.
     *
     * @return array
     */
    public static function getLivePropertiesByOwnerId(int $prive_owner_id)
    {

        // From Prive Manager.
        $listings = self::from('prive_owner as po');
        
        // Property Join.
        $listings->join('properties', function($join) use ($prive_owner_id) {
            $join->on('po.pid', '=', 'properties.id')->where('po.user_id', $prive_owner_id);
        });
        
        // Property Pricing Join.
        $listings->join('prive_manager_taggings as pmt','pmt.pid','properties.id');

        // Select Data.
        $listings->select(
            'properties.id',
            'properties.title'
        );

        // Get Active Properties.
        $listings->isEnabled()->isActive();
        
        // Set Order.
        $listings = $listings->orderBy('properties.enabled', 'DESC')->orderBy('properties.status', 'DESC')->orderBy('properties.property_last_updated', 'DESC')->orderBy('properties.id', 'DESC')->get();

        if (empty($listings) === true) {
            return [];
        }

        return $listings->toArray();

    }//end getLivePropertiesByOwnerId()


    /**
     * Get Prive properties.
     *
     *
     * @return array
     */
    public static function getAllPriveProperties()
    {
        //phpcs:disable
        $listings = self::from('properties')->select(
            'properties.id',
            'created_at',
        );
        $listings = $listings->where('prive',1)->isEnabled()->isActive()->orderBy('properties.enabled', 'DESC')->get();

        if (empty($listings) === true) {
            return [];
        }

        return $listings->toArray();

    }//end getPriveProperties()

    /**
     * Get Prive properties.
     *
     *
     * @return array
     */
    public static function getAllPrivePropertiesByBookingingData()
    {
        //phpcs:disable
        $listings = self::from('properties')->select(
            'properties.id',
            \DB::raw('(CASE WHEN pmt.contract_start_date is null THEN "2019-08-01" else pmt.contract_start_date end) as created_at')
        );
         $listings->leftjoin('booking_requests as br', function($join) {
            $join->on('properties.id', '=', 'br.pid')->where(\DB::raw('Date(br.created_at)'), '>=','2019-08-01')->where('br.prive',1);
        });

         $listings->leftjoin('prive_manager_taggings as pmt', function($join) {
            $join->on('pmt.pid', '=', 'properties.id')->where('pmt.status','ACTIVE');
        });

        
        $listings = $listings->where('properties.prive',1)->groupBy('br.pid')->get();


        if (empty($listings) === true) {
            return [];
        }

        return $listings->toArray();

    }//end getAllPrivePropertiesByBookingingData()

}//end class
