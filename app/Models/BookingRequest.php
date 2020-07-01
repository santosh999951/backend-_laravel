<?php
/**
 * Booking Request Model contain all functions related to booking requests
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;
use Carbon\Carbon;
use App\Libraries\Helper;
use App\Libraries\v1_6\ProperlyService;

/**
 * Class BookingRequest
 */
class BookingRequest extends Model
{

    /**
     * Table Name
     *
     * @var string Table Name.
     */
    protected $table = 'booking_requests';


    /**
     * Function for Foreign key mapping.
     *
     * @return object.
     */
    public function bookings()
    {
        return $this->hasOne('Bookings', 'booking_request_id');

    }//end bookings()


    /**
     * Function to get new and approved  count.
     *
     * @param integer $user_id User id.
     *
     * @return integer New and approved count.
     */
    public static function getNewAndApprovedRequestsCount(int $user_id)
    {


    }//end getNewAndApprovedRequestsCount()


    /**
     * Function to get Upcoming Booked Request.
     *
     * @param integer $host_id    Host id.
     * @param integer $request_id Request id.
     *
     * @return integer.
     */
    public static function getBookedRequestsOfHost(int $host_id, int $request_id)
    {
        return self::from('booking_requests as br')->where('br.id', $request_id)->where('br.host_id', $host_id)->where('br.booking_status', BOOKED)->count();

    }//end getBookedRequestsOfHost()


    /**
     * Function to get New Booked Awaiting Confirmation Count.
     *
     * @param integer $host_id Host id.
     *
     * @return integer.
     */
    public static function getNewBookedAwaitingConfirmationOfHost(int $host_id)
    {

      
        
        return self::from('booking_requests as br')->leftjoin(
            'booking_availability as ba',
            function ($join) use ($host_id) {
                                                        $join->on('br.id', '=', 'ba.booking_request_id');
            }
        )->where('br.host_id', '=', $host_id)->where('br.booking_status', BOOKED)->where('br.from_date', '>', Carbon::now('Asia/Kolkata'))->whereNull('ba.id')->count();

    }//end getNewBookedAwaitingConfirmationOfHost()


    /**
     * Function to get Archived booking request count by user.
     *
     * @param integer $user_id User id.
     *
     * @return integer Count.
     */
    public static function getArchivedBookingRequestsCount(int $user_id)
    {
        // phpcs:ignore
        return self::from('booking_requests as br')->where('br.traveller_id', '=', $user_id)->where('br.booking_status', '<=', REQUEST_APPROVED)->isArchivedBookingRequest(true, $user_id)->count();

    }//end getArchivedBookingRequestsCount()


    /**
     * Function to get new request count for host.
     *
     * @param integer $host_id Host id.
     *
     * @return integer New request count for host.
     */
    public static function getNewRequestsCountForHost(int $host_id)
    {
        return self::where('host_id', $host_id)->whereIn('booking_status', [NEW_REQUEST])->where('created_at', '>=', Carbon::now()->subDay()->toDateTimeString())->count();

    }//end getNewRequestsCountForHost()


    /**
     * Function to get booking count of traveller in last 24 hours.
     *
     * @param integer $traveller_id Traveller id.
     *
     * @return integer Booking Count.
     */
    public static function getLastOneDayBookingsOfTraveller(int $traveller_id)
    {
        return self::where('traveller_id', $traveller_id)->whereIn('booking_status', [BOOKED])->whereRaw(' HOUR(TIMEDIFF(NOW(),updated_at)) <= 24')->count();

    }//end getLastOneDayBookingsOfTraveller()


    /**
     * Function to get traveller checkin today count for host.
     *
     * @param integer $host_id Host id.
     *
     * @return integer Traveller checkin today count for host.
     */
    public static function getTravellerCheckinTodayCount(int $host_id)
    {
        return self::where('host_id', $host_id)->where('from_date', '=', Carbon::now('GMT')->format('Y-m-d'))->where('booking_status', '=', BOOKED)->count();

    }//end getTravellerCheckinTodayCount()


    /**
     * Function to get booking count in specific date for host.
     *
     * @param integer $host_id    Host id.
     * @param string  $start_date Start date.
     * @param string  $end_date   End date.
     *
     * @return integer booking count in specific dates foo host.
     */
    public static function getBookingCountSpecificDate(int $host_id, string $start_date, string $end_date)
    {
        return self::where('host_id', $host_id)->where('booking_status', '=', BOOKED)->filterByDates($start_date, $end_date, 'created_at')->count();

    }//end getBookingCountSpecificDate()


     /**
      * Function to get count of guests hosted by user.
      *
      * @param integer $user_id User id.
      *
      * @return integer Hosted guests count.
      */
    public static function getGuestCountServedByUserAsHost(int $user_id)
    {
        $guest_count = self::selectRaw('sum(guests) as guest_count')->where('host_id', $user_id)->where('booking_status', BOOKED)->groupBy('host_id')->first();

        if (empty($guest_count) === false) {
            return $guest_count->guest_count;
        }

        return 0;

    }//end getGuestCountServedByUserAsHost()


    /**
     * Function to get count of guests hosted by user.
     *
     * @param integer $user_id User id.
     *
     * @return integer Trips taken.
     */
    public static function getTripsTakenCountByUserAsTraveller(int $user_id)
    {
        return self::where('traveller_id', '=', $user_id)->where('booking_status', '=', BOOKED)->where('to_date', '<=', Carbon::now('GMT')->format('Y-m-d'))->count();

    }//end getTripsTakenCountByUserAsTraveller()


    /**
     * Function to get count of bookings of hosts property.
     *
     * @param integer $property_id Property id.
     *
     * @return integer Trips taken.
     */
    public static function getAllBookingCountOfHostProperty(int $property_id)
    {
        return self::where('pid', '=', $property_id)->where('booking_status', '=', BOOKED)->count();

    }//end getAllBookingCountOfHostProperty()


    /**
     * Function to get count of booking requests of hosts property.
     *
     * @param integer $property_id Property id.
     *
     * @return object Trips taken.
     */
    public static function getAllBookingRequestCountOfHostProperty(int $property_id)
    {
        return self::where('pid', '=', $property_id)->count();

    }//end getAllBookingRequestCountOfHostProperty()


    /**
     * Function to get upcoming checkin traveller.
     *
     * @param integer $host_id         Host id.
     * @param boolean $include_ongoing Include Ongoing Flag.
     *
     * @return array Upcoming checkin traveller data.
     */
    public static function getUpcomingCheckinTraveller(int $host_id, bool $include_ongoing=false)
    {
        $trips = self::from('booking_requests as br')->join('users as t', 't.id', '=', 'br.traveller_id')->select(
            'br.id as request_id',
            'br.pid as id',
            'br.guests',
            'br.units as units_consumed',
            'br.from_date',
            'br.to_date',
            'br.booking_status',
            \DB::raw("CONCAT(t.name, ' ', t.last_name) AS traveller_name")
        )->where('br.host_id', '=', $host_id)->where(
            function ($query) {
                $query->where('br.booking_status', '=', BOOKED)->orWhere('br.booking_status', '=', REQUEST_TO_CANCEL_AFTER_PAYMENT);
            }
        )->where('br.from_date', '>', Carbon::now('GMT')->format('Y-m-d'));

        if ($include_ongoing === true) {
            $trips = $trips->orWhere('br.to_date', '>=', Carbon::now('GMT')->format('Y-m-d'));
        }

        $trips = $trips->orderBy('br.from_date', 'asc')->offset(0)->limit(3)->get();

        if (count($trips) > 0) {
            return $trips->toArray();
        }

        return [];

    }//end getUpcomingCheckinTraveller()


    /**
     * Function to get upcoming checkin counts.
     *
     * @param integer $host_id         Host id.
     * @param boolean $include_ongoing Include Ongoing Flag.
     *
     * @return integer Upcoming checkin count.
     */
    public static function getUpcomingCheckinCountTraveller(int $host_id, bool $include_ongoing=false)
    {
        $trips = self::from('booking_requests as br')->join('users as t', 't.id', '=', 'br.traveller_id')->where('br.host_id', '=', $host_id)->where(
            function ($query) {
                $query->where('br.booking_status', '=', BOOKED)->orWhere('br.booking_status', '=', REQUEST_TO_CANCEL_AFTER_PAYMENT);
            }
        )->where('br.from_date', '>', Carbon::now('GMT')->format('Y-m-d'));

        if ($include_ongoing === true) {
            $trips = $trips->orWhere('br.to_date', '>=', Carbon::now('GMT')->format('Y-m-d'));
        }

        return $trips->count();

    }//end getUpcomingCheckinCountTraveller()


    /**
     * Function to get trips by user.
     *
     * @param integer $user_id User id.
     * @param integer $offset  Offset.
     * @param integer $limit   Limit.
     * @param string  $for     For Web/App.
     * @param array   $status  Status.
     * @param integer $past    Past data status.
     *
     * @return array Trips data by user.
     */
    public static function getTripsByUserId(int $user_id, int $offset, int $limit, string $for=null, array $status, int $past=0)
    {
        $now   = Carbon::now('GMT')->format('Y-m-d');
        $trips = self::from('booking_requests as br')->join('properties as p', 'p.id', '=', 'br.pid')->join('users as h', 'p.user_id', '=', 'h.id');
        $trips->join('property_type as pt', 'pt.id', '=', 'p.property_type')->join('room_type as rt', 'rt.id', '=', 'p.room_type')->join('property_stats_new as ps', 'ps.id', '=', 'p.id', 'left');
        $trips->leftjoin('bookings as b', 'b.booking_request_id', '=', 'br.id')->leftjoin('property_reviews as pr', 'pr.booking_id', '=', 'b.id')->leftJoin(
            'traveller_ratings as r',
            function ($join) {
                $join->on('r.booking_requests_id', '=', 'br.id')->whereRaw('r.rated_by = b.traveller_id');
            }
        )->select(
            'br.id as request_id',
            'br.pid as id',
            'br.guests',
            'br.units as units_consumed',
            'br.from_date',
            'br.to_date',
            'br.booking_status',
            'br.price_details',
            'p.title',
            'p.area',
            'p.city',
            'p.state',
            'p.country',
            'p.bedrooms',
            'p.property_type',
            'p.room_type',
            'p.v_lat as latitude',
            'p.v_lng as longitude',
            'pt.name as property_type_name',
            'rt.name as room_type_name',
            'ps.property_score',
            \DB::raw('RTRIM(h.name) AS host_name'),
            'h.profile_img AS host_image',
            'r.id as rating_id',
            'pr.id as review_id'
        )->where('br.traveller_id', '=', $user_id)->where('br.booking_status', '>', REQUEST_APPROVED);

        $trips->groupBy('br.id');

        if (empty($status) === false) {
            $trips = $trips->whereIn('br.booking_status', $status)->havingRaw("br.to_date < '".Carbon::now('GMT')->format('Y-m-d')."'");
        }

        if (empty($for) === false && $for === 'web') {
            $trips = $trips->isPastTrip($past)->offset($offset)->limit($limit)->get();
        } else {
            $trips = $trips->selectRaw('(CASE WHEN br.from_date >= \''.$now.'\' THEN DATEDIFF(br.from_date, \''.$now.'\') WHEN br.to_date >=\''.$now.'\' THEN DATEDIFF(br.from_date, \''.$now.'\') else 999999 end) as priority');
            $trips = $trips->orderBy('priority', 'asc')->orderBy('br.to_date', 'desc')->offset($offset)->limit($limit)->get();
        }

        if (empty($trips) === false) {
            return $trips->toArray();
        }

        return [];

    }//end getTripsByUserId()


    /**
     * Function to get trip count by user.
     *
     * @param integer $user_id User id.
     *
     * @return array Trips count data by user.
     */
    public static function getTripCountsByUserId(int $user_id)
    {
        $trips = self::from('booking_requests as br')->join(
            'users as tr',
            function ($join) use ($user_id) {
                        $join->on('tr.id', '=', 'br.traveller_id')->where('br.traveller_id', '=', $user_id)->where('br.booking_status', '>', REQUEST_APPROVED);
            }
        )->join('properties as p', 'p.id', '=', 'br.pid');

        $total_trip_count = $trips->count();
        $past_trip_count  = $trips->where('br.to_date', '<', Carbon::now('GMT')->format('Y-m-d'))->count();

        return [
            'upcoming' => ($total_trip_count - $past_trip_count),
            'past'     => $past_trip_count,
            'total'    => $total_trip_count,
        ];

    }//end getTripCountsByUserId()


    /**
     * Function to get booking request by user.
     *
     * @param integer $user_id  User id.
     * @param integer $offset   Offset.
     * @param integer $limit    Limit.
     * @param boolean $archived Archived Data.
     *
     * @return array Booking Request data by user.
     */
    public static function getBookingRequestsByUserId(int $user_id, int $offset, int $limit, bool $archived=true)
    {
        // phpcs:ignore
        $requests = self::from('booking_requests as br')->join('properties as p', 'p.id', '=', 'br.pid')->join('users as h', 'p.user_id', '=', 'h.id')->join('property_type as pt', 'pt.id', '=', 'p.property_type')->join('room_type as rt', 'rt.id', '=', 'p.room_type')->join('property_stats_new as ps', 'ps.id', '=', 'p.id', 'left')->select(
            'br.id as request_id',
            'br.pid as id',
            'br.guests',
            'br.units as units_consumed',
            'br.from_date',
            'br.to_date',
            'br.booking_status',
            'br.created_at',
            'br.price_details',
            'br.valid_till as valid_till',
            'br.approve_till as approve_till',
            'p.title',
            'p.area',
            'p.city',
            'p.state',
            'p.country',
            'p.bedrooms',
            'p.property_type',
            'p.room_type',
            'p.v_lat as latitude',
            'p.v_lng as longitude',
            'ps.property_score',
            'pt.name as property_type_name',
            'rt.name as room_type_name',
            \DB::raw('RTRIM(h.name) AS host_name'),
            'h.profile_img AS host_image'
        )->where('br.traveller_id', '=', $user_id)->where('br.booking_status', '<=', REQUEST_APPROVED)->isArchivedBookingRequest($archived, $user_id)->orderBy('br.id', 'desc')->offset($offset)->limit($limit)->get();
        if (empty($requests) === false) {
            return $requests->toArray();
        }

        return [];

    }//end getBookingRequestsByUserId()


     /**
      * Function to add booking requests archived scope.
      *
      * @param EloquentQuery $query    Query.
      * @param boolean       $archived Archived Data.
      * @param integer       $user_id  User Id.
      *
      * @return EloquentQuery Add scope to query.
      */
    public static function scopeIsArchivedBookingRequest(EloquentQuery $query, bool $archived, int $user_id)
    {
        $max_date = self::selectRaw("date(max(convert_tz(created_at,'+00:00','+05:30'))) as date")->where('traveller_id', '=', $user_id)->where('booking_status', '<=', REQUEST_APPROVED)->first();

        $timestamp = $max_date['date'];

        if ($archived === false) {
            return $query->whereRaw("convert_tz(br.created_at,'+00:00','+05:30') >= '".$timestamp."'")->orWhereIn('br.booking_status', [REQUEST_APPROVED, NEW_REQUEST]);
        } else {
            return $query->whereRaw("convert_tz(br.created_at,'+00:00','+05:30') < '".$timestamp."'")->where('br.booking_status', '<', REQUEST_APPROVED)->whereNotIn('br.booking_status', [REQUEST_APPROVED, NEW_REQUEST]);
            ;
        }

    }//end scopeIsArchivedBookingRequest()


     /**
      * Function to add past trips.
      *
      * @param EloquentQuery $query Query.
      * @param integer       $past  Past Data.
      *
      * @return EloquentQuery Add scope to query.
      */
    public static function scopeIsPastTrip(EloquentQuery $query, int $past)
    {
        $cancelled_status_array = [
            REQUEST_TO_CANCEL_AFTER_PAYMENT,
            CANCELLED_AFTER_PAYMENT,
            CANCELLED_BY_HOST_AFTER_PAYMENT,
            NON_AVAILABILITY_REFUND,
            OVERBOOKED,
            CANCELLED_AFTER_OVERBOOKED,
            CANCEL_OFFLINE_BOOKING,
            CANCELLED_BY_GH_AFTER_PAYMENT_AS_HOST,
            CANCELLED_BY_GH_AFTER_PAYMENT_AS_TRAVELLER,
            AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_AFTER_PAYMENT,
        ];

        if ($past === 0) {
             $query->selectRaw('(CASE WHEN br.booking_status in ('.implode(',', $cancelled_status_array).') then 0 else 1 end) as priority')->where('br.to_date', '>=', Carbon::now('GMT')->format('Y-m-d'));
             return $query->orderBy('br.from_date', 'asc')->orderBy('priority', 'desc');
        } else {
            $query->selectRaw('(CASE WHEN br.booking_status in ('.implode(',', $cancelled_status_array).') then 0 else 1 end) as priority')->where('br.to_date', '<', Carbon::now('GMT')->format('Y-m-d'));
            return $query->orderBy('br.to_date', 'desc')->orderBy('priority', 'desc');
        }

    }//end scopeIsPastTrip()


     /**
      * Function to get trips by request id.
      *
      * @param integer $request_id Request id.
      * @param integer $user_id    Traveller id.
      *
      * @return array Trips
      */
    public static function getTripByRequestIdAndUserId(int $request_id, int $user_id)
    {
        // Write Better version : add users check.
        // phpcs:ignore
        return self::from('booking_requests as br')        ->join('bookings as b', 'b.booking_request_id', '=', 'br.id')        ->join('properties as p', 'p.id', '=', 'br.pid')        ->join('property_type as pt', 'pt.id', '=', 'p.property_type')        ->join('room_type as rt', 'rt.id', '=', 'p.room_type')->join('property_stats_new as ps', 'ps.id', '=', 'p.id', 'left')        ->join('users as ut', 'ut.id', '=', 'br.host_id')        ->leftjoin('property_reviews as pr', 'pr.booking_id', '=', 'b.id')        ->leftJoin(
            'traveller_ratings as r',
            function ($join) {
                $join->on('r.booking_requests_id', '=', 'br.id')->whereRaw('r.rated_by = b.traveller_id');
            }
        )->select(
            'br.id as request_id',
            'br.pid as id',
            'br.guests',
            'br.from_date',
            'br.to_date',
            'br.booking_status',
            'br.price_details',
            'br.units as units_consumed',
            'br.currency as currency',
            'b.payment_option',
            'b.balance_fee',
            'p.title',
            'p.area',
            'p.city',
            'p.state',
            'p.address',
            'p.country',
            'p.zipcode',
            'p.bedrooms',
            'p.property_type',
            'p.room_type',
            'p.deleted_at',
            'p.instant_book',
            'p.amenities',
            'p.check_in as property_check_in',
            'p.checkout as property_checkout',
            'br.cancellation_policy',
            'p.v_lat as latitude',
            'p.v_lng as longitude',
            'p.latitude as original_latitude',
            'p.longitude as original_longitude',
            'p.zipcode as property_zipcode',
            'pt.name as property_type_name',
            'rt.name as room_type_name',
            'ps.property_score',
            'b.service_fee as service_fee',
            'b.total_charged_fee as total_charged_fee',
            \DB::raw("CONCAT(ut.name, ' ', ut.last_name) AS host_name"),
            'ut.name as host_first_name',
            'ut.profile_img AS host_image',
            'ut.contact',
            'ut.dob as host_dob',
            'ut.created_at as host_created_date',
            'ut.language as host_language',
            'ut.work as host_work',
            'ut.gender as host_gender',
            'r.id as rating_id',
            'pr.id as review_id',
            'p.check_in as check_in_time'
        )->where('br.id', '=', $request_id)->where('br.traveller_id', '=', $user_id)->where('br.booking_status', '>', REQUEST_APPROVED)->get()->toArray();

    }//end getTripByRequestIdAndUserId()


    /**
     * Function to get Booking Request data by request id.
     *
     * @param integer $request_id Request id.
     * @param integer $user_id    Traveller id.
     *
     * @return object Trips
     */
    public static function getBookingRequest(int $request_id, int $user_id)
    {
        $query = self::select(
            // Booking Request Data.
                    'br.id as request_id',
            'br.pid as id',
            'br.guests',
            'br.from_date',
            'br.to_date',
            'br.booking_status',
            'br.price_details',
            'br.units as units_consumed',
            'br.currency as currency',
            'br.cancellation_policy',
            'br.instant_book',
            'br.commission_from_host',
            'br.coa_available',
            'br.prive',
            'br.valid_till as valid_till',
            'br.approve_till as approve_till',
            'br.resend_reqest_sent as resend_request_status',
            'br.updated_at as updated_at',
            'br.created_at',
            // Booking Data.
            'b.payment_option',
            'b.balance_fee',
            'b.service_fee as service_fee',
            'b.total_charged_fee as total_charged_fee',
            // Property Data.
            'p.title',
            'p.area',
            'p.city',
            'p.state',
            'p.address',
            'p.country',
            'p.zipcode',
            'p.bedrooms',
            'p.property_type',
            'p.room_type',
            'p.deleted_at',
            'p.instant_book',
            'p.amenities',
            'p.check_in as property_check_in',
            'p.checkout as property_checkout',
            'p.v_lat as latitude',
            'p.v_lng as longitude',
            'p.latitude as original_latitude',
            'p.longitude as original_longitude',
            'p.zipcode as property_zipcode',
            'pt.name as property_type_name',
            'rt.name as room_type_name',
            'ps.property_score',
            // Host Info.
            'ut.id as host_id',
            \DB::raw("CONCAT(ut.name, ' ', ut.last_name) AS host_name"),
            'ut.name as host_first_name',
            'ut.profile_img AS host_image',
            'ut.contact',
            'ut.dob as host_dob',
            'ut.created_at as host_created_date',
            'ut.language as host_language',
            'ut.work as host_work',
            'ut.gender as host_gender',
            // Traveller Rating Info.
            'r.id as rating_id',
            'pr.id as review_id'
        );

        $query->from('booking_requests as br')->leftjoin('bookings as b', 'b.booking_request_id', '=', 'br.id')->join('properties as p', 'p.id', '=', 'br.pid')->join('property_type as pt', 'pt.id', '=', 'p.property_type');
        $query->join('room_type as rt', 'rt.id', '=', 'p.room_type')->join('property_stats_new as ps', 'ps.id', '=', 'p.id', 'left')->join('users as ut', 'ut.id', '=', 'br.host_id');
        $query->leftjoin('property_reviews as pr', 'pr.booking_id', '=', 'b.id')->leftJoin(
            'traveller_ratings as r',
            function ($join) {
                $join->on('r.booking_requests_id', '=', 'br.id')->whereRaw('r.rated_by = b.traveller_id');
            }
        )->where('br.id', '=', $request_id)->where('br.traveller_id', '=', $user_id)->groupBy('br.id');

        $query_data = $query->first();

        if (empty($query_data) === true) {
            return [];
        }

        return $query_data->toArray();

    }//end getBookingRequest()


    /**
     * Function to get trips for host by request id.
     *
     * @param integer $request_id Request id.
     *
     * @return object Trips
     */
    public static function getHostTripByRequestId(int $request_id)
    {
        $query = self::select(
            'br.id as request_id',
            'br.pid as id',
            'br.guests',
            'br.from_date',
            'br.to_date',
            'br.booking_status',
            'br.price_details',
            'br.units as units_consumed',
            'br.currency as currency',
            'b.payment_option',
            'b.balance_fee',
            'p.title',
            'p.area',
            'p.city',
            'p.state',
            'p.address',
            'p.country',
            'p.zipcode',
            'p.bedrooms',
            'p.property_type',
            'p.room_type',
            'p.deleted_at',
            'p.instant_book',
            'br.cancellation_policy',
            'p.v_lat as latitude',
            'p.v_lng as longitude',
            'p.latitude as original_latitude',
            'p.longitude as original_longitude',
            'pt.name as property_type_name',
            'rt.name as room_type_name',
            'b.service_fee as service_fee',
            'b.total_charged_fee as total_charged_fee',
            \DB::raw("CONCAT(ut.name, ' ', ut.last_name) AS traveller_name"),
            'ut.id as traveller_id',
            'ut.contact',
            'ut.gender',
            'ut.display_age',
            'ut.dob',
            'ut.email_verify',
            'ut.mobile_verify',
            'ut.language',
            'r.id as rating_id',
            'pr.id as review_id',
            'p.check_in as check_in_time',
            \DB::raw("CONCAT(h.name, ' ', h.last_name) AS host_name"),
            'h.profile_img AS host_image',
            \DB::raw('(CASE WHEN br.booking_status = '.BOOKED.' and br.from_date > now() and ba.id is null THEN 1 else 0 end) as can_confirm_booking')
        )->from('booking_requests as br');
        $query->join('bookings as b', 'b.booking_request_id', '=', 'br.id');
        $query->join('properties as p', 'p.id', '=', 'br.pid');
        $query->join('property_type as pt', 'pt.id', '=', 'p.property_type');
        $query->join('room_type as rt', 'rt.id', '=', 'p.room_type');
        $query->join('users as ut', 'ut.id', '=', 'br.traveller_id');
        $query->join('users as h', 'h.id', '=', 'br.host_id');
        $query->leftjoin('property_reviews as pr', 'pr.booking_id', '=', 'b.id');
        $query->leftJoin(
            'traveller_ratings as r',
            function ($join) {
                            $join->on('r.booking_requests_id', '=', 'br.id')->whereRaw('r.rated_by = b.traveller_id');
            }
        )->leftjoin('booking_availability as ba', 'ba.booking_request_id', '=', 'br.id');

        $trips = $query->where('br.id', '=', $request_id)->where('br.booking_status', '>', REQUEST_APPROVED)->first();

        if (empty($trips) === false) {
            $trips = $trips->toArray();
        }

        return $trips;

    }//end getHostTripByRequestId()


    /**
     * Function to get booking requests by request id.
     *
     * @param integer $request_id Request id.
     * @param integer $host_id    Id Of Host.
     *
     * @return array Booking Requests.
     */
    public static function getBookingRequestByRequestId(int $request_id, int $host_id)
    {
        $traveller_id = self::where('id', $request_id)->select('traveller_id')->first();

        if (empty($traveller_id) === false) {
            return self::getBookingRequestByRequestIdAndUserId($request_id, $traveller_id->traveller_id, false, $host_id);
        }

        return [];

    }//end getBookingRequestByRequestId()


    /**
     * Function to get booking requests by request id.
     *
     * @param integer $request_id    Request id.
     * @param integer $user_id       Traveller id.
     * @param boolean $only_requests Only_requests is set to false  if we are expecting all booking_request.
     * @param integer $host_id       Id Of Host.
     *
     * @return array Booking Requests.
     */
    public static function getBookingRequestByRequestIdAndUserId(int $request_id, int $user_id, bool $only_requests=true, int $host_id=0)
    {
        // phpcs:ignore
        $booking =  self::from('booking_requests as br')->join('properties as p', 'p.id', '=', 'br.pid')->join('property_type as pt', 'pt.id', '=', 'p.property_type')->join('room_type as rt', 'rt.id', '=', 'p.room_type') ->join('property_stats_new as ps', 'ps.id', '=', 'p.id', 'left')->join('users as ut', 'ut.id', '=', 'br.traveller_id')->join('users as uh', 'uh.id', '=', 'br.host_id')->leftjoin('booking_availability as ba', 'ba.booking_request_id', '=', 'br.id')->select(
            'br.id as request_id',
            'br.pid as property_id',
            'br.guests',
            'br.from_date',
            'br.to_date',
            'br.booking_status',
            'br.price_details',
            'br.units as units_consumed',
            'br.units',
            'br.currency',
            'br.cancellation_policy',
            'br.instant_book',
            'br.commission_from_host',
            'br.coa_available',
            'br.prive',
            'br.valid_till as valid_till',
            'br.approve_till as approve_till',
            'br.resend_reqest_sent as resend_reqest_status',
            'br.updated_at as updated_at',
            'br.created_at',
            'p.title',
            'p.area',
            'p.city',
            'p.state',
            'p.address',
            'p.country',
            'p.zipcode as property_zipcode',
            'p.zipcode',
            'p.bedrooms',
            'p.property_type',
            'p.room_type',
            'p.amenities',
            'p.check_in as property_check_in',
            'p.checkout as property_checkout',
            'p.v_lat as latitude',
            'p.v_lng as longitude',
            'pt.name as property_type_name',
            'rt.name as room_type_name',
            'ps.property_score',
            'uh.name as host_name',
            'uh.profile_img AS host_image',
            'ut.contact',
            'ut.id as traveller_id',
            'ut.gender',
            'ut.display_age',
            'ut.dob',
            'ut.mobile_verify',
            'ut.language',
            'ut.profile_img AS traveller_image',
            \DB::raw("CONCAT(ut.name, ' ', ut.last_name) AS traveller_name"),
            \DB::raw('(CASE WHEN br.booking_status = '.BOOKED.' and br.from_date > now() and ba.id is null THEN 1 else 0 end) as can_confirm_booking'),
            \DB::raw('(CASE WHEN br.booking_status = '.BOOKED.' and br.from_date > now() THEN ba.created_at else null end) as confirm_date')
        )->where('br.id', '=', $request_id)->where('br.traveller_id', $user_id);
        if ($only_requests === true) {
            $booking->where('br.booking_status', '<=', 1);
        }

        if (empty($host_id) === false) {
            $booking->where('br.host_id', '=', $host_id);
        }

        return $booking->get()->toArray();

    }//end getBookingRequestByRequestIdAndUserId()


    /**
     * Function to get booking requests by request id.
     *
     * @param integer $request_id Request id.
     * @param integer $user_id    User id.
     *
     * @return array Booking Requests.
     */
    public static function getBookingRequestForPreviewPageByUserId(int $request_id, int $user_id)
    {
        $booking_request = self::where('id', $request_id)->where('booking_status', '=', REQUEST_APPROVED)->where('traveller_id', $user_id)->first();

        return $booking_request;

    }//end getBookingRequestForPreviewPageByUserId()


    /**
     * Function to get booking requests by request id and traveller id.
     *
     * @param integer $request_id   Request id.
     * @param integer $traveller_id Traveller id.
     *
     * @return array Booking Requests.
     */
    public static function getBookingRequestForPreviewPageByUser(int $request_id, int $traveller_id)
    {
        $booking_request = self::where('id', $request_id)->where('booking_status', '=', REQUEST_APPROVED)->where('traveller_id', '=', $traveller_id)->first();

        return $booking_request;

    }//end getBookingRequestForPreviewPageByUser()


     /**
      * Function to get booking requests by request id and traveller for cancellation.
      *
      * @param integer $request_id   Request id.
      * @param integer $traveller_id Traveller id.
      *
      * @return object Booking Requests.
      */
    public static function getBookingRequestForCancellation(int $request_id, int $traveller_id)
    {
        // phpcs:ignore
        return self::select(
            'booking_requests.id as id',
            'b.id as booking_id',
            'b.service_fee as service_fee',
            'booking_requests.booking_status',
            'booking_requests.price_details',
            'booking_requests.pid as pid',
            'booking_requests.guests as guests',
            'booking_requests.from_date as from_date',
            'booking_requests.to_date as to_date',
            'booking_requests.units as units',
            'booking_requests.currency as currency',
            'b.total_charged_fee as total_charged_fee',
            'booking_requests.last_edited_by as last_edited_by',
            'booking_requests.cancellation_policy as cancellation_policy_id',
            't.name as traveller_name',
            't.dial_code as traveller_dial_code',
            't.contact as traveller_contact',
            \DB::raw("RTRIM(CONCAT(h.name, ' ', h.last_name)) AS host_name"),
            'h.email as host_email',
            'h.dial_code as host_dial_code',
            'h.contact as host_contact',
            'p.title as property_title',
            'p.check_in as check_in_time',
            'booking_requests.host_id'
        )->where('booking_requests.id', $request_id)->where('booking_requests.traveller_id', $traveller_id)->leftJoin(
            'bookings as b',
            function ($join) {
                $join->on('booking_requests.id', '=', 'b.booking_request_id');
            }
        )->join('users as t', 'booking_requests.traveller_id', '=', 't.id')->join('users as h', 'booking_requests.host_id', '=', 'h.id')->join('properties as p', 'p.id', '=', 'booking_requests.pid')->first();

    }//end getBookingRequestForCancellation()


    /**
     * Add date range filter.
     *
     * @param EloquentQuery $query      Query to be passed.
     * @param string        $start_date Start date of date range filter.
     * @param string        $end_date   End date of date range filter.
     * @param string        $sort       Filter to check for date range.
     *
     * @return EloquentQuery
     */
    public static function scopeFilterByDates(EloquentQuery $query, string $start_date, string $end_date, string $sort)
    {
        if (empty($start_date) === false && empty($end_date) === false) {
            return $query->where($sort, '>=', $start_date)->where($sort, '<=', $end_date);
        }

    }//end scopeFilterByDates()


    /**
     * Add property id filter.
     *
     * @param EloquentQuery $query        Query to be passed.
     * @param array         $property_ids Array containing filtered property ids.
     *
     * @return EloquentQuery
     */
    public static function scopeFilterByPropertyId(EloquentQuery $query, array $property_ids)
    {
        if (count($property_ids) > 0) {
            return $query->whereIn('br.pid', $property_ids);
        }

    }//end scopeFilterByPropertyId()


    /**
     * Add booking status filter.
     *
     * @param EloquentQuery $query          Query to be passed.
     * @param array         $booking_status Array containing filtered booking status.
     *
     * @return EloquentQuery
     */
    public static function scopeFilterByBookingStatus(EloquentQuery $query, array $booking_status)
    {
        if (count($booking_status) > 0) {
            return $query->whereIn('br.booking_status', $booking_status);
        }

    }//end scopeFilterByBookingStatus()


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
     * Function to get booking list for host.
     *
     * @param integer $host_id               Host id.
     * @param string  $start_date            Start date for date range.
     * @param string  $end_date              End date for date range.
     * @param array   $property_ids          Property ids array for filter.
     * @param array   $booking_status        Booking status array for filter.
     * @param integer $offset                Offset.
     * @param integer $limit                 Limit.
     * @param integer $order_by              Order by filter.
     * @param array   $host_status_array     Host Status Array.
     * @param string  $search_string         Search String For Search Api.
     * @param boolean $awaiting_confirmation Awaiting Confirmation.
     * @param string  $sort_type             Sort Type ASC, DESC.
     *
     * @return array.
     */
    public static function getHostBookings(
        int $host_id,
        string $start_date,
        string $end_date,
        array $property_ids,
        array $booking_status,
        int $offset,
        int $limit,
        int $order_by=1,
        array $host_status_array=[],
        string $search_string='',
        bool $awaiting_confirmation=false,
        string $sort_type='DESC'
    ) {
        if ($order_by === 1) {
            $sort = 'br.created_at';
        } else if ($order_by === 2) {
            $sort = 'br.from_date';
        } else {
            $sort = 'br.to_date';
        }

        $query = self::from('booking_requests as br')->join(
            'properties as p',
            function ($join) use ($host_id) {
                                                            $join->on('p.id', '=', 'br.pid')->where('br.host_id', '=', $host_id);
            }
        )->join('room_type as rt', 'rt.id', '=', 'p.room_type')->join('property_type as pt', 'pt.id', '=', 'p.property_type');
        $query->join('users as h', 'h.id', '=', 'br.host_id')->join('users as t', 't.id', '=', 'br.traveller_id')->select(
            'br.created_at',
            'br.updated_at',
            'br.id',
            'br.approve_till',
            'br.pid',
            'br.booking_status',
            'br.from_date',
            'br.to_date',
            'br.price_details',
            'br.guests',
            'br.units',
            'br.valid_till',
            'br.commission_from_host',
            'p.title',
            'p.country',
            'p.city',
            'p.state',
            'p.area',
            'p.room_type',
            'p.search_keyword',
            'p.latitude',
            'p.longitude',
            'p.deleted_at',
            't.name AS guest_name',
            't.profile_img AS guest_image',
            't.gender AS guest_gender',
            't.id AS guest_id',
            'h.name AS host_name',
            'h.profile_img AS host_image',
            'h.gender AS host_gender',
            'h.id AS host_id',
            'rt.name AS room_type_name',
            'pt.name AS property_type_name'
        )->where(
            function ($query) {
                                $query->where('br.booking_status', '>', REQUEST_APPROVED);
                                $query->orWhere(
                                    function ($query1) {
                                        $query1->where('br.booking_status', '<=', REQUEST_APPROVED);
                                        $query1->where('br.created_at', '>=', Carbon::now()->subDay()->toDateTimeString());
                                    }
                                );
            }
        )->filterByPropertyId($property_ids)->filterByDates($start_date, $end_date, $sort)->filterByBookingStatus($booking_status);

        if (count($host_status_array) > 0 && (in_array(HOST_BOOKING_CONFIRMED, $host_status_array) === false) && (in_array(HOST_BOOKING_COMPLETED, $host_status_array) === true)) {
            $query->whereRaw('DATEDIFF(br.to_date, now())<0');
        } else if (count($host_status_array) > 0 && (in_array(HOST_BOOKING_COMPLETED, $host_status_array) === false) && (in_array(HOST_BOOKING_CONFIRMED, $host_status_array) === true)) {
            $query->whereRaw('DATEDIFF(br.to_date, now())>=0');
        }

        if ($awaiting_confirmation === true) {
            $query->leftjoin('booking_availability as ba', 'ba.booking_request_id', '=', 'br.id')->where('br.booking_status', '=', BOOKED)->whereNull('ba.id');
        }

        // Booking Search Data Clause.
        if ($search_string !== '') {
            $query->where(
                function ($search) use ($search_string) {
                    $search->where('br.hash_id', 'like', $search_string.'%')->orWhere('t.name', 'like', $search_string.'%')->orWhere('t.last_name', 'like', $search_string.'%');
                }
            );
        }

        $query->orderBy(\DB::raw('(CASE WHEN br.booking_status < 1 THEN (CASE WHEN br.booking_status = -1 then 0 WHEN br.booking_status=0 then -1 ELSE br.booking_status END) ELSE -1000 END)'), 'desc');
        $query_data = $query->orderBy($sort, $sort_type)->addLimit($offset, $limit)->get()->toArray();

        return $query_data;

    }//end getHostBookings()


    /**
     * Function to get prive booking list.
     *
     * @param integer $prive_owner_id Prive Owner id.
     * @param integer $property_id    Property id.
     * @param integer $offset         Offset.
     * @param integer $total          Total.
     * @param integer $sort           Sort Type.
     * @param string  $sort_order     Sorting Order.
     * @param string  $start_date     Start date for date range.
     * @param string  $end_date       End date for date range.
     * @param integer $booking_status Booking status  for filter.
     *
     * @return array.
     */
    public function getPriveBookings(int $prive_owner_id, int $property_id, int $offset, int $total, int $sort, string $sort_order, string $start_date, string $end_date, int $booking_status)
    {
        $query = self::select(
            'br.id',
            'br.booking_status',
            'br.from_date',
            'br.to_date',
            'br.price_details',
            'br.guests',
            'br.units',
            'br.valid_till',
            'br.commission_from_host',
            'p.title',
            \DB::raw("CONCAT(t.name, ' ', t.last_name) AS guest_name"),
            'b.host_fee',
            'p.currency',
            'br.bedroom',
            'br.properly_commission'
        )->from('prive_owner as po')->join(
            'properties as p',
            function ($join) use ($prive_owner_id, $property_id) {
                $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->join(
            'booking_requests as br',
            function ($join) {
                    $join->on('br.pid', '=', 'p.id')->where('br.booking_status', '>=', BOOKED);
            }
        )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->join('room_type as rt', 'rt.id', '=', 'p.room_type')->join('property_type as pt', 'pt.id', '=', 'p.property_type')->join('property_pricing as pp', 'pp.pid', '=', 'p.id');
        $query->join('users as t', 't.id', '=', 'br.traveller_id')->where('br.prive', 1);

        if (empty($booking_status) === false) {
            if ($booking_status === BOOKED) {
                $query->whereIn('br.booking_status', [BOOKED, OVERBOOKED]);
            } else {
                $query->whereNotIn('br.booking_status', [BOOKED, OVERBOOKED]);
            }
        }

        if (empty($property_id) === false) {
            $query->where('po.pid', '=', $property_id);
        }

        if (empty($start_date) === false && empty($end_date) === false) {
            $query->where('br.from_date', '>=', $start_date)->where('br.from_date', '<=', $end_date);
        }

        // Sorting.
        if ($sort === 1) {
            $sort = 'br.from_date';
        } else if ($sort === 2) {
            $sort = 'br.to_date';
        } else if ($sort === 3) {
            $sort = 'CAST(b.host_fee as unsigned)';
        }

        $query_data = $query->orderByRaw($sort.' '.$sort_order)->addLimit($offset, $total)->get();
        if (empty($query_data) === false) {
            return $query_data->toArray();
        }

        return [];

    }//end getPriveBookings()


    /**
     * Function to get prive manager booking list.
     *
     * @param integer $prive_manager_id Prive Manager id.
     * @param integer $offset           Offset.
     * @param integer $total            Total.
     * @param array   $sort             Sort data.
     * @param array   $filters          Filters data.
     * @param string  $search           Search query.
     *
     * @return array.
     */
    public function getPriveManagerBookings(int $prive_manager_id, int $offset, int $total, array $sort=[], array $filters=[], string $search='')
    {
        $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');

        $properly_service = new ProperlyService;

        $user_properties = $properly_service->getUserProperties($prive_manager_id);

        if (empty($user_properties) === true) {
            return [
                'data'  => [],
                'count' => 0,
            ];
        }

        $query = self::select(
            'br.id',
            'br.booking_status',
            'br.from_date',
            'br.to_date',
            'br.price_details',
            'br.guests',
            'br.units',
            'br.valid_till',
            'br.commission_from_host',
            'br.created_at',
            'br.updated_at',
            'p.id as property_id',
            'p.title',
            'p.properly_title',
            'p.country',
            'p.city',
            'p.state',
            'p.area',
            't.name AS guest_name',
            'b.host_fee',
            'b.payment_option',
            \DB::raw('(b.coa_to_be_collected - b.coa_received) as balance_fee'),
            'p.currency',
            'b.checkin_status',
            'b.checkout_status',
            'b.no_show',
            't.name AS guest_name',
            't.last_name AS guest_last_name',
            't.id AS guest_id',
            't.contact as traveller_primary_contact',
            't.secondry_contact as traveller_secondary_contact',
            't.email as traveller_email',
            \DB::raw('(CASE WHEN t.email_verify = 1 and t.mobile_verify = 1 THEN 1 ELSE 0 END) as guest_verified'),
            'upm.contact as manager_primary_contact',
            'upm.secondry_contact as manager_secondary_contact',
            'rt.name AS room_type_name',
            'pt.name AS property_type_name',
            'op.expected_checkin AS expected_checkin_datetime',
            'op.expected_checkout AS expected_checkout_datetime',
            'op.no_show_reason_id as no_show_reason_id',
            \DB::raw(
                '(CASE WHEN br.booking_status not in ('.BOOKED.','.OVERBOOKED.') then '.PRIVE_MANAGER_CANCELLED."
            WHEN br.to_date >= '".$today."' and b.checkin_status = 0 and b.checkout_status = 0 THEN ".PRIVE_MANAGER_UPCOMING."
            WHEN br.to_date > '".$today."' and b.checkin_status = 1 and b.checkout_status = 0 then ".PRIVE_MANAGER_CHECKEDIN."
            WHEN br.to_date <= '".$today."' and b.checkin_status = 1 and b.checkout_status = 0 then ".PRIVE_MANAGER_CHECKEDOUT.'
            WHEN b.no_show = 1 and b.checkin_status = 0 then '.PRIVE_MANAGER_NO_SHOW.'
            WHEN b.checkin_status = 1 and b.checkout_status = 1 then '.PRIVE_MANAGER_COMPLETED.'
            else '.PRIVE_MANAGER_UPCOMING.' end) as checkedin_status'
            )
        )->from('properties as p')->join(
            'booking_requests as br',
            function ($join) use ($user_properties) {
                    $join->on('br.pid', '=', 'p.id')->whereIn('p.id', $user_properties)->where('br.booking_status', '>=', BOOKED);
            }
        )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->join('room_type as rt', 'rt.id', '=', 'p.room_type')->join('property_type as pt', 'pt.id', '=', 'p.property_type');

        $query->leftJoin('prive_operation as op', 'op.booking_request_id', '=', 'br.id');

        $query->join('users as t', 't.id', '=', 'br.traveller_id')->join(
            'users as upm',
            function ($upm_join) use ($prive_manager_id) {
                $upm_join->where('upm.id', $prive_manager_id);
            }
        )->where('br.prive', 1);

         // Apply Filterss.
        if (empty($filters) === false) {
            // Filter by dates.
            if (empty($filters['start_date']) === false && empty($filters['end_date']) === false) {
                $query->where(
                    function ($query) use ($filters) {
                        $query->where(
                            function ($query) use ($filters) {
                                $query->where('br.from_date', '<=', $filters['start_date'])->where('br.to_date', '>=', $filters['start_date']);
                            }
                        )->orWhere(
                            function ($query) use ($filters) {
                                    $query->where('br.from_date', '>=', $filters['start_date'])->where('br.to_date', '<=', $filters['end_date']);
                            }
                        )->orWhere(
                            function ($query) use ($filters) {
                                    $query->where('br.from_date', '<=', $filters['end_date'])->where('br.to_date', '>=', $filters['end_date']);
                            }
                        );
                    }
                );
            }

            // Filters By Checkedin status.
            if (empty($filters['status']) === false) {
                $query->havingRaw('checkedin_status in ('.implode(',', $filters['status']).')');
            }

            // Filter by properties array.
            if (empty($filters['property_ids']) === false) {
                $query->whereIn('p.id', $filters['property_ids']);
            }
        }//end if

        // Apply Search string.
        if (empty($search) === false) {
            $search = strtolower($search);
            $query->where(
                function ($search_query) use ($search) {
                    $search_query->where('br.hash_id', $search)->orWhere(\DB::raw("LOWER(CONCAT( t.name,  ' ', t.last_name ))"), 'LIKE', "$search%")->orWhere('p.id', $search);
                }
            );
        }

        // Add sorting.
        if (empty($sort) === false) {
            switch ($sort['sort_by']) {
                case PRIVE_BOOKING_SORT_BY_CHECKIN:
                    $query->orderBy('br.from_date', $sort['order']);
                break;

                case PRIVE_BOOKING_SORT_BY_CHECKOUT:
                    $query->orderBy('br.to_date', $sort['order']);
                break;

                case PRIVE_BOOKING_SORT_BY_AMOUNT:
                    $query->orderBy(\DB::raw('CAST(b.host_fee AS UNSIGNED)'), $sort['order']);
                break;

                default:
                    // No Code.
                break;
            }
        }

        $total_bookings = count($query->get());
        $query_data     = $query->addLimit($offset, $total)->get()->toArray();

        return [
            'data'  => $query_data,
            'count' => $total_bookings,
        ];

    }//end getPriveManagerBookings()


    /**
     * Function to get prive manager booking detail.
     *
     * @param integer $prive_manager_id      Prive Manager id.
     * @param integer $request_id            Booking Request Id.
     * @param boolean $ongoing_upcoming_trip Ongoing Upcoming Trip.
     *
     * @return array.
     */
    public function getPriveManagerBookingDetail(int $prive_manager_id, int $request_id, bool $ongoing_upcoming_trip=false)
    {
        $today = Carbon::now()->format('Y-m-d');

        $properly_service = new ProperlyService;

        $user_properties = $properly_service->getUserProperties($prive_manager_id);

        if (empty($user_properties) === true) {
            return [];
        }

        $query = self::select(
            'br.id',
            'br.booking_status',
            'br.from_date',
            'br.to_date',
            'br.price_details',
            'br.guests',
            'br.units',
            'br.valid_till',
            'br.commission_from_host',
            'br.created_at',
            'br.updated_at',
            'br.bedroom',
            \DB::raw(
                "if(br.offline_source like '%booking.com%','BOOKING.COM', 
           if(br.offline_source like '%Airbnb%','Airbnb',
           if(br.offline_source like '%Expedia%','Expedia',
           if(br.offline_source like '%TripAdvisor%','TripAdvisor',
           if(br.offline_source like '%GH%','GH',
           if(br.offline_source like '%prive_owner%','Prive Owner',
           if(br.offline_source is NULL or br.offline_source = '', br.source, offline_source)))))) ) as source"
            ),
            'br.assigned_to',
            'p.id as property_id',
            'p.title',
            'p.properly_title',
            'p.country',
            'p.city',
            'p.state',
            'p.area',
            'p.room_type',
            'p.search_keyword',
            'p.latitude',
            'p.longitude',
            'p.currency',
            'p.instant_book',
            'p.amenities',
            'p.check_in as property_checkin_time',
            'p.checkout as property_checkout_time',
            'b.host_fee',
            'b.checkin_status',
            'b.checkout_status',
            'b.checkin_date as actual_checkin_date',
            'b.checkout_date as actual_checkout_date',
            'b.payment_option',
            \DB::raw('(b.coa_to_be_collected - b.coa_received) as balance_fee'),
            'b.checkin_date as actual_checkin_datetime',
            'b.checkout_date as actual_checkout_datetime',
            't.name AS guest_name',
            't.last_name AS guest_last_name',
            't.id AS guest_id',
            't.dial_code as traveller_dial_code',
            't.contact as traveller_primary_contact',
            't.secondry_contact as traveller_secondary_contact',
            't.email as traveller_email',
            \DB::raw('(CASE WHEN t.email_verify = 1 and t.mobile_verify = 1 THEN 1 ELSE 0 END) as guest_verified'),
            'upm.dial_code as manager_dial_code',
            'upm.contact as manager_primary_contact',
            'upm.secondry_contact as manager_secondary_contact',
            'rt.name AS room_type_name',
            'pt.name AS property_type_name',
            'op.expected_checkin AS expected_checkin_datetime',
            'op.expected_checkout AS expected_checkout_datetime',
            'op.note as managerial_note',
            'op.note_last_updated as managerial_note_last_updated',
            'unm.name as managerial_note_last_updated_by',
            'op.op_note as operational_note',
            'op.op_note_last_updated as operational_note_last_updated',
            'uno.name as operational_note_last_updated_by',
            \DB::raw('(CASE WHEN op.no_show is not null THEN op.no_show ELSE b.no_show END) as no_show'),
            'op.no_show_reason_id as no_show_reason_id',
            'op.no_show_comment as no_show_reason_comment',
            \DB::raw(
                '(CASE WHEN br.booking_status not in ('.BOOKED.','.OVERBOOKED.') then '.PRIVE_MANAGER_CANCELLED."
            WHEN br.to_date >= '".$today."' and b.checkin_status = 0 and b.checkout_status = 0 THEN ".PRIVE_MANAGER_UPCOMING."
            WHEN br.to_date > '".$today."' and b.checkin_status = 1 and b.checkout_status = 0 then ".PRIVE_MANAGER_CHECKEDIN."
            WHEN br.to_date <= '".$today."' and b.checkin_status = 1 and b.checkout_status = 0 then ".PRIVE_MANAGER_CHECKEDOUT.'
            WHEN b.no_show = 1 and b.checkin_status = 0 then '.PRIVE_MANAGER_NO_SHOW.'
            WHEN b.checkin_status = 1 and b.checkout_status = 1 then '.PRIVE_MANAGER_COMPLETED.'
            else '.PRIVE_MANAGER_UPCOMING.' end) as checkedin_status'
            )
        )->from('properties as p')->join(
            'booking_requests as br',
            function ($join) use ($user_properties) {
                    $join->on('br.pid', '=', 'p.id')->whereIn('p.id', $user_properties)->where('br.booking_status', '>=', BOOKED);
            }
        )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->join('room_type as rt', 'rt.id', '=', 'p.room_type')->join('property_type as pt', 'pt.id', '=', 'p.property_type');
        $query->leftJoin('prive_operation as op', 'op.booking_request_id', '=', 'br.id')->leftJoin('users as unm', 'op.note_last_updated_by', '=', 'unm.id')->leftJoin('users as uno', 'op.op_note_last_updated_by', '=', 'uno.id');

        $query->join('users as t', 't.id', '=', 'br.traveller_id');
        $query->join(
            'users as upm',
            function ($upm_join) use ($prive_manager_id) {
                $upm_join->where('upm.id', $prive_manager_id);
            }
        )->where('br.prive', 1)->where('br.id', '=', $request_id);

        // Fetch only upcoming or ongoing trip.
        if ($ongoing_upcoming_trip === true) {
            $query->havingRaw('checkedin_status in ('.PRIVE_MANAGER_UPCOMING.','.PRIVE_MANAGER_CHECKEDIN.','.PRIVE_MANAGER_CHECKEDOUT.')');
        }

        $query_data = $query->first();

        if (empty($query_data) === true) {
            return [];
        }

        return $query_data->toArray();

    }//end getPriveManagerBookingDetail()


    /**
     * Function to get sharable content data.
     *
     * @param integer $booking_id Booking request id.
     * @param integer $user_id    User id.
     *
     * @return array.
     */
    public static function getFbShareDataByTravellerId(int $booking_id, int $user_id)
    {
        return self::from('booking_requests as br')->join('users as t', 't.id', '=', 'br.traveller_id')->join('properties as p', 'p.id', '=', 'br.pid')->join('property_type as pt', 'pt.id', '=', 'p.property_type')->select(
            't.name as username',
            'br.pid as property_id',
            'p.city',
            'p.state',
            'p.property_images',
            'p.property_type',
            'pt.name as pname',
            'p.title',
            'p.description'
        )->where('br.id', '=', $booking_id)->where('br.traveller_id', '=', $user_id)->first()->toArray();

    }//end getFbShareDataByTravellerId()


    /**
     * Get Booking Request data.
     *
     * @param integer $booking_request_id User id.
     * @param array   $select             Data to fetch.
     *
     * @return object
     */
    public static function getBookingRequestById(int $booking_request_id, array $select)
    {
        return self::select($select)->where('id', $booking_request_id)->first();

    }//end getBookingRequestById()


    /**
     * Function to get prive Graph Data list.
     *
     * @param integer $prive_owner_id Prive Owner id.
     * @param string  $start_date     Start Date.
     * @param string  $end_date       End Date.
     *
     * @return array.
     */
    public function getGraphDataOnCheckin(int $prive_owner_id, string $start_date, string $end_date)
    {
        $query = self::select(
            'p.currency',
            'br.properly_commission',
            \DB::raw('sum(b.host_fee -((b.host_fee * br.properly_commission)/100)) as host_actual_amount'),
            \DB::raw("DATE_FORMAT(br.from_date,'%m-%Y') as month"),
            \DB::raw('sum(DATEDIFF(br.to_date, br.from_date)) as total_nights')
        )->from('prive_owner as po')->join(
            'properties as p',
            function ($join) use ($prive_owner_id) {
                $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->join(
            'booking_requests as br',
            function ($join) {
                    $join->on('br.pid', '=', 'p.id')->where('br.booking_status', '=', BOOKED);
            }
        )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->join('property_pricing as pp', 'pp.pid', '=', 'p.id')->where('br.prive', 1);

        $query->where('br.from_date', '>=', $start_date)->where('br.from_date', '<=', $end_date);

         $query->groupBy(\DB::raw('YEAR(br.from_date)'))->groupBy(\DB::raw('MONTH(br.from_date)'));

        $query_data = $query->get();
        if (empty($query_data) === false) {
            return $query_data->toArray();
        }

        return [];

    }//end getGraphDataOnCheckin()


     /**
      * Function to get prive Graph Data list.
      *
      * @param integer $prive_owner_id                  Prive Owner id.
      * @param string  $start_date                      Start Date.
      * @param string  $end_date                        End Date.
      * @param string  $invoices_on_checkout_start_date Invoice checkout start date.
      *
      * @return array.
      */
    public function getGraphDataOnCheckout(int $prive_owner_id, string $start_date, string $end_date, string $invoices_on_checkout_start_date)
    {
        $query = self::select(
            'p.currency',
            'br.properly_commission',
            \DB::raw('sum(b.host_fee -((b.host_fee * br.properly_commission)/100)) as host_actual_amount'),
            \DB::raw("DATE_FORMAT(br.to_date,'%m-%Y') as month"),
            \DB::raw('sum(DATEDIFF(br.to_date, br.from_date)) as total_nights')
        )->from('prive_owner as po')->join(
            'properties as p',
            function ($join) use ($prive_owner_id) {
                $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->join(
            'booking_requests as br',
            function ($join) {
                    $join->on('br.pid', '=', 'p.id')->where('br.booking_status', '=', BOOKED);
            }
        )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->join('property_pricing as pp', 'pp.pid', '=', 'p.id')->where('br.prive', 1);

        $query->where('br.from_date', '>=', $invoices_on_checkout_start_date);
        $query->where('br.to_date', '>=', $start_date)->where('br.to_date', '<=', $end_date);
        $query->groupBy(\DB::raw('YEAR(br.to_date)'))->groupBy(\DB::raw('MONTH(br.to_date)'));

        $query_data = $query->get();
        if (empty($query_data) === false) {
            return $query_data->toArray();
        }

        return [];

    }//end getGraphDataOnCheckout()


    /**
     * Get Booking Request data by request_id.
     *
     * @param integer $prive_owner_id  User id.
     * @param integer $request_hash_id Request hash id.
     *
     * @return array
     */
    public static function gePriveBookingByRequestId(int $prive_owner_id, int $request_hash_id)
    {
                $query = self::select(
                    'br.id',
                    'br.pid',
                    'br.booking_status',
                    'br.from_date',
                    'br.to_date',
                    'br.price_details',
                    'br.guests',
                    'br.units as units_consumed',
                    'br.bedroom as room',
                    'br.commission_from_host',
                    'p.title',
                    \DB::raw("CONCAT(t.name, ' ', t.last_name) AS guest_name"),
                    'b.host_fee',
                    'p.currency',
                    'br.properly_commission'
                )->from('prive_owner as po')->join(
                    'properties as p',
                    function ($join) use ($prive_owner_id) {
                        $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
                    }
                )->join(
                    'booking_requests as br',
                    function ($join) {
                        $join->on('br.pid', '=', 'p.id')->where('br.booking_status', '>=', BOOKED);
                    }
                )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->join('property_pricing as pp', 'pp.pid', '=', 'p.id');
        $query->join('users as t', 't.id', '=', 'br.traveller_id')->where('br.prive', 1);

        $query_data = $query->where('br.id', '=', $request_hash_id)->first();

        if (empty($query_data) === false) {
            return $query_data->toArray();
        }

        return [];

    }//end gePriveBookingByRequestId()


    /**
     * Function to get prive invoice list by checkin.
     *
     * @param integer $prive_owner_id  Prive Owner id.
     * @param string  $month_year_from Month_year_from.
     * @param string  $month_year_to   Month_year_to.
     * @param integer $offset          Offset.
     * @param integer $total           Total.
     * @param array   $property_ids    Property ids.
     *
     * @return array.
     */
    public function getInvoiceListByCheckIn(int $prive_owner_id, string $month_year_from, string $month_year_to, int $offset, int $total, array $property_ids)
    {
        $query = self::select(
            'br.id',
            'br.booking_status',
            'br.from_date as invoice_date',
            'br.price_details',
            'br.guests',
            'p.title',
            \DB::raw("CONCAT(t.name, ' ', t.last_name) AS guest_name"),
            'p.currency',
            'b.host_fee as host_amount',
            'br.properly_commission',
            \DB::raw("DATE_FORMAT(br.from_date,'%m') as month")
        )->from('prive_owner as po')->join(
            'properties as p',
            function ($join) use ($prive_owner_id) {
                $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->join(
            'booking_requests as br',
            function ($join) {
                    $join->on('br.pid', '=', 'p.id')->where('br.booking_status', '=', BOOKED);
            }
        )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->join('property_pricing as pp', 'pp.pid', '=', 'p.id');
        $query->join('users as t', 't.id', '=', 'br.traveller_id')->where('br.prive', 1);

        $query->where('br.from_date', '<=', $month_year_to)->where('br.from_date', '>=', $month_year_from);
        if (empty($property_ids) === false) {
            $query->whereIn('po.pid', $property_ids);
        }

        $query_data['all_data']        = $query->get()->toArray();
        $query_data['data_pagination'] = $query->offset($offset)->limit($total)->get()->toArray();

        return $query_data;

    }//end getInvoiceListByCheckIn()


    /**
     * Function to get prive invoice list by checkout.
     *
     * @param integer $prive_owner_id                  Prive Owner id.
     * @param string  $month_year_from                 Month_year_from.
     * @param string  $month_year_to                   Month_year_to.
     * @param string  $invoices_on_checkout_start_date Invoice checkout start date.
     * @param integer $offset                          Offset.
     * @param integer $total                           Total.
     * @param array   $property_ids                    Property ids.
     *
     * @return array.
     */
    public function getInvoiceListByCheckOut(int $prive_owner_id, string $month_year_from, string $month_year_to, string $invoices_on_checkout_start_date, int $offset, int $total, array $property_ids)
    {
        $query = self::select(
            'br.id',
            'br.booking_status',
            'br.to_date as invoice_date',
            'br.price_details',
            'br.guests',
            'p.title',
            \DB::raw("CONCAT(t.name, ' ', t.last_name) AS guest_name"),
            'p.currency',
            'b.host_fee as host_amount',
            'br.properly_commission',
            \DB::raw("DATE_FORMAT(br.to_date,'%m') as month")
        )->from('prive_owner as po')->join(
            'properties as p',
            function ($join) use ($prive_owner_id) {
                $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->join(
            'booking_requests as br',
            function ($join) {
                    $join->on('br.pid', '=', 'p.id')->where('br.booking_status', '=', BOOKED);
            }
        )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->join('property_pricing as pp', 'pp.pid', '=', 'p.id');
        $query->join('users as t', 't.id', '=', 'br.traveller_id')->where('br.prive', 1);

        $query->where('br.from_date', '>=', $invoices_on_checkout_start_date);
        $query->where('br.to_date', '<=', $month_year_to)->where('br.to_date', '>=', $month_year_from);

        if (empty($property_ids) === false) {
            $query->whereIn('po.pid', $property_ids);
        }

        $query_data['all_data']        = $query->get()->toArray();
        $query_data['data_pagination'] = $query->offset($offset)->limit($total)->get()->toArray();

        return $query_data;

    }//end getInvoiceListByCheckOut()


     /**
      * Function to get prive booking count.
      *
      * @param integer $prive_owner_id Prive Owner id.
      * @param string  $start_date     Start Date.
      * @param string  $end_date       End Date.
      * @param integer $property_id    Property id.
      * @param integer $booking_status Status of Booking.
      *
      * @return integer.
      */
    public static function getPriveBookingCount(int $prive_owner_id, string $start_date, string $end_date, int $property_id=0, int $booking_status=0)
    {
            $query = self::from('prive_owner as po')->join(
                'properties as p',
                function ($join) use ($prive_owner_id) {
                    $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
                }
            )->join(
                'booking_requests as br',
                function ($join) {
                    $join->on('br.pid', '=', 'p.id')->where('br.booking_status', '>=', BOOKED);
                }
            )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->where('br.prive', 1);
        if (empty($start_date) === false && empty($end_date) === false) {
            $query->where('br.from_date', '>=', $start_date)->where('br.from_date', '<=', $end_date);
        }

        if (empty($booking_status) === false) {
            if ($booking_status === BOOKED) {
                $query->whereIn('br.booking_status', [BOOKED, OVERBOOKED]);
            } else {
                $query->whereNotIn('br.booking_status', [BOOKED, OVERBOOKED]);
            }
        }

        if (empty($property_id) === false) {
            $query->where('po.pid', '=', $property_id);
        }

        $query_data = $query->count();

        return $query_data;

    }//end getPriveBookingCount()


     /**
      * Function to get upcoming trips by user.
      *
      * @param integer $user_id User id.
      * @param integer $offset  Offset.
      * @param integer $limit   Limit.
      * @param array   $status  Status.
      *
      * @return array Trips data by user.
      */
    public static function getUpcomingTrips(int $user_id, int $offset, int $limit, array $status)
    {
        $now   = Carbon::now('GMT')->format('Y-m-d');
        $trips = self::from('booking_requests as br')->join('properties as p', 'p.id', '=', 'br.pid')->join('users as h', 'p.user_id', '=', 'h.id');
        $trips->join('property_type as pt', 'pt.id', '=', 'p.property_type')->join('room_type as rt', 'rt.id', '=', 'p.room_type')->join('property_stats_new as ps', 'ps.id', '=', 'p.id', 'left')->select(
            'br.id as request_id',
            'br.pid as id'
        )->where('br.traveller_id', '=', $user_id)->where('br.booking_status', '>', REQUEST_APPROVED);
        if (empty($status) === false) {
            $trips = $trips->whereIn('br.booking_status', $status);
        }

        $trips = $trips->where('br.to_date', '>=', Carbon::now('GMT')->format('Y-m-d'))->offset($offset)->limit($limit)->orderBy('br.from_date', 'asc')->orderBy('priority', 'desc')->get();
        ;

        if (count($trips) > 0) {
            return $trips->toArray();
        }

        return [];

    }//end getUpcomingTrips()


    /**
     * Function to get properly  booking task list.
     *
     * @param integer $logged_in_user_id Logged In user id.
     * @param array   $filter            Filter data.
     * @param integer $request_id        Request_id.
     *
     * @return array.
     */
    public static function getProperlyTaskList(int $logged_in_user_id, array $filter=[], int $request_id=0)
    {
        $yesterday = Carbon::yesterday()->format('Y-m-d H:i:s');
        $tomorrow  = Carbon::tomorrow()->format('Y-m-d H:i:s');

        $query = self::select(
            'br.guests',
            'p.title',
            'p.id',
            \DB::raw("CONCAT(t.name, ' ', t.last_name) AS traveller_name"),
            't.id AS guest_id',
            'plt.status AS task_status',
            'plt.id AS task_id',
            'plt.entity_id as booking_request_id',
            'plt.type AS task_type',
            'plt.run_at AS task_date_time',
            'plt.description AS description',
            \DB::raw("CONCAT(team.name, ' ', team.last_name) AS assigned_to"),
            \DB::raw('(CASE WHEN plt.reccuring_type = 1  THEN "RECCURING" else "NOT_RECCURING" end) as reccuring_type'),
            \DB::raw('(CASE WHEN plt.status = 4  THEN 0 else 1 end) as can_update')
        )->from('properties as p')->join(
            'booking_requests as br',
            function ($join) {
                    $join->on('br.pid', '=', 'p.id')->where('br.booking_status', '>=', BOOKED);
            }
        )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->join('room_type as rt', 'rt.id', '=', 'p.room_type')->join('property_type as pt', 'pt.id', '=', 'p.property_type');

        $query->join('properly_tasks as plt', 'plt.entity_id', '=', 'br.id');

        $query->join('users as t', 't.id', '=', 'br.traveller_id');
        $query->leftjoin('users as team', 'team.id', '=', 'plt.assigned_to');

        if (empty($request_id) === false) {
            return $query->where('br.id', $request_id)->orderBy('plt.status', 'desc')->orderBy('plt.run_at', 'asc')->get()->toArray();
        }

        $query->where(\DB::raw('date(plt.run_at)'), '>=', $yesterday)->where(\DB::raw('date(plt.run_at)'), '<=', $tomorrow);

        if (empty($filter['status']) === false) {
            $query->whereIn('plt.status', $filter['status']);
        }

        if (empty($filter['assigned_to']) === false) {
            $query->whereIn('plt.assigned_to', $filter['assigned_to']);
        }

        if (empty($filter['type']) === false) {
            $query->whereIn('plt.type', $filter['type']);
        }

        if (empty($filter['property_ids']) === false) {
                $query->whereIn('br.pid', $filter['property_ids']);
        }

        $query_data = $query->orderBy('plt.status', 'desc')->orderBy('plt.run_at', 'asc')->get()->toArray();

        return $query_data;

    }//end getProperlyTaskList()


    /**
     * Function to get properly task detail.
     *
     * @param integer $task_id Task Id.
     *
     * @return array.
     */
    public static function getProperlyTaskDetail(int $task_id)
    {
        $query = self::select(
            'br.guests',
            \DB::raw("(CASE WHEN p.properly_title IS NOT NULL and p.properly_title != '' THEN p.properly_title ELSE p.title END) as title"),
            'p.id',
            \DB::raw("CONCAT(t.name, ' ', t.last_name) AS traveller_name"),
            't.id AS guest_id',
            'plt.id AS task_id',
            'plt.status AS task_status',
            'plt.entity_id as booking_request_id',
            'plt.type AS task_type',
            'plt.run_at AS task_date_time',
            'plt.description AS description',
            'plt.assigned_to as assigned_to_id',
            \DB::raw("CONCAT(team.name, ' ', team.last_name) AS assigned_to"),
            \DB::raw('(CASE WHEN plt.reccuring_type = 1  THEN "RECCURING" else "NOT_RECCURING" end) as reccuring_type'),
            \DB::raw('(CASE WHEN plt.status = 4  THEN 0 else 1 end) as can_update')
        )->from('properly_tasks as plt')->join('booking_requests as br', 'plt.entity_id', '=', 'br.id')->join('properties as p', 'br.pid', '=', 'p.id')->join('users as t', 't.id', '=', 'br.traveller_id');

        $query = $query->leftjoin('users as team', 'team.id', '=', 'plt.assigned_to')->where('plt.id', $task_id)->where('br.booking_status', '>=', BOOKED)->first();

        if (empty($query) === true) {
            return [];
        }

        return $query->toArray();

    }//end getProperlyTaskDetail()


     /**
      * Function to get Booking Request Notification Count.
      *
      * @param integer $user_id User id.
      *
      * @return array Booking Request data by user.
      */
    public static function getBookingRequestNotifications(int $user_id)
    {
        $current_date_time = Carbon::now('GMT')->addSeconds(-60)->toDateTimeString();

        $booking_requests = self::select(
            'br.id',
            'br.booking_status',
            'br.updated_at',
            'br.to_date',
            'br.from_date',
            'br.guests',
            'br.units',
            'p.id as property_id',
            'p.area',
            'p.title',
            'p.city',
            'p.state',
            'p.country',
            'p.property_images',
            'rst.created_at as rst_created_at',
            'rst.updated_at as rst_updated_at'
        )->from('booking_requests as br')->join('properties as p', 'br.pid', '=', 'p.id')->join('request_status_tracking as rst', 'rst.booking_request_id', '=', 'br.id');
        $booking_requests = $booking_requests->where('br.traveller_id', $user_id)->where('rst.updated_at', '>', $current_date_time)->orderBy('br.id', 'DESC')->orderBy('rst.updated_at', 'DESC')->groupBy('br.id')->first();

        if (empty($booking_requests) === true) {
             return [];
        }

        return $booking_requests->toArray();

    }//end getBookingRequestNotifications()


    /**
     * Function to get Prive Booking Nights.
     *
     * @param string  $start_date     Start Date.
     * @param string  $end_date       End Date.
     * @param integer $prive_owner_id Prive Owner id.
     *
     * @return integer Booking Request count.
     */
    public static function getPriveBookedNights(string $start_date, string $end_date, int $prive_owner_id)
    {
        $query = self::select(
            \DB::raw('sum(DATEDIFF(br.to_date, br.from_date)) as total_nights')
        )->from('prive_owner as po')->join(
            'properties as p',
            function ($join) use ($prive_owner_id) {
                $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->join(
            'booking_requests as br',
            function ($join) {
                    $join->on('br.pid', '=', 'p.id')->where('br.booking_status', '=', BOOKED);
            }
        )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->join('property_pricing as pp', 'pp.pid', '=', 'p.id')->where('br.prive', 1)->where('offline_source', 'prive_owner');

        $query->where('br.from_date', '>=', $start_date)->where('br.from_date', '<=', $end_date);

        $query_data = $query->first()->toArray();

        return $query_data['total_nights'];

    }//end getPriveBookedNights()


    /**
     * Function to get Booking of all bookings whose checkedin after 3 days.
     *
     * @return array Booking Request data.
     */
    public static function getProperlyCheckin()
    {
        $date  = Carbon::now('GMT')->addDays(3)->format('Y-m-d');
        $today = Carbon::now('GMT')->format('Y-m-d');

        $query = self::select(\DB::raw('distinct(br.id)'), 'br.from_date', 'p.check_in as property_checkin_time', 'op.expected_checkin AS expected_checkin_datetime')->from('properties as p')->join(
            'booking_requests as br',
            function ($join) use ($date, $today) {
                    $join->on('br.pid', '=', 'p.id')->whereIn('br.booking_status', [BOOKED, OVERBOOKED])->where('br.from_date', '>=', $today)->where('br.from_date', '<=', $date);
            }
        );
        $query->leftJoin('prive_operation as op', 'op.booking_request_id', '=', 'br.id');
        $query->leftJoin(
            'properly_tasks as pt',
            function ($join) {
                    $join->on('pt.entity_id', '=', 'br.id')->where('pt.type', '=', 1);
            }
        );

        $query->whereNull('pt.id');
        return $query->get()->toArray();

    }//end getProperlyCheckin()


     /**
      * Function to get Cancelled task.
      *
      * @return array Booking Request data.
      */
    public static function getProperlyCanceledTask()
    {
        $date  = Carbon::now('GMT')->addDays(3)->format('Y-m-d');
        $today = Carbon::now('GMT')->format('Y-m-d');
        //phpcs:ignore
        return self::select('pt.id')->from('properly_tasks as pt')->join('booking_requests as br', 'br.id', 'pt.entity_id')->whereNotIn('br.booking_status', [BOOKED, OVERBOOKED])->whereNull('pt.deleted_at')->whereNull('pt.assigned_to')->where('br.from_date', '>=', $today)->where('br.from_date', '<=', $date)->get()->toArray();

    }//end getProperlyCanceledTask()


      /**
       * Function to get monthly payable amount against a property.
       *
       * @param integer $prive_owner_id Prive Owner id.
       * @param integer $property_id    Property id.
       * @param string  $start_date     Start Date.
       * @param string  $end_date       End Date.
       *
       * @return array total payable amount monthwise.
       */
    public static function getMonthlyPayableAmount(int $prive_owner_id, int $property_id, string $start_date, string $end_date)
    {
        $query = self::select(
            \DB::raw("DATE_FORMAT(br.to_date,'%Y-%m') as month"),
            \DB::raw('sum(br.payable_amount) as payable_amount'),
            \DB::raw('sum(b.gst_amount) as gst_amount'),
            \DB::raw('sum(b.host_fee) as host_fee'),
            \DB::raw('sum(b.service_fee) as service_fee'),
            \DB::raw('sum(b.markup_fee) as markup_fee'),
            \DB::raw('sum(b.gh_commission_from_host) as gh_commission_from_host'),
            \DB::raw('sum(b.host_fee * br.properly_commission/100) as properly_share'),
            'br.currency as currency'
        )->from('prive_owner as po')->join(
            'properties as p',
            function ($join) use ($prive_owner_id) {
                    $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->join(
            'booking_requests as br',
            function ($join) {
                    $join->on('br.pid', '=', 'p.id')->where('br.booking_status', '=', BOOKED);
            }
        )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->join('property_pricing as pp', 'pp.pid', '=', 'p.id')->where('br.prive', 1);
        $query = $query->where('po.pid', $property_id)->where('br.to_date', '>=', $start_date)->where('br.to_date', '<=', $end_date)->groupBy(\DB::raw('YEAR(br.to_date)'))->groupBy(\DB::raw('MONTH(br.to_date)'))->get();

        if (empty($query) === false) {
            return $query->toArray();
        }

            return [];

    }//end getMonthlyPayableAmount()


     /**
      * Function to get prive property monthly booking amount.
      *
      * @param integer $property_id Property id.
      * @param string  $start_date  Start Date.
      *
      * @return object total payable amount monthwise.
      */
    public static function getPrivePropertiesMonthlyBookingAmount(int $property_id, string $start_date)
    {
        $end_date             = Carbon::parse($start_date)->endOfMonth()->format('Y-m-d');
        $end_date_add_one_day = Carbon::parse($end_date)->addDay();

        // phpcs:disable
        $query = \DB::select(
            "select
        Sum((pnl.payable_amount/pnl.total_nights)*pnl.no_of_nights)             AS payable_amount,
        Sum((pnl.gst_amount/pnl.total_nights)*pnl.no_of_nights)             AS gst_amount,
        Sum((pnl.service_fee/pnl.total_nights)*pnl.no_of_nights)             AS service_fee,
        Sum((pnl.markup_fee/pnl.total_nights)*pnl.no_of_nights)             AS markup_fee,
          Sum((pnl.host_fee/pnl.total_nights)*pnl.no_of_nights)             AS host_fee,
        Sum((pnl.gh_commission_from_host/pnl.total_nights)*pnl.no_of_nights)             AS gh_commission_from_host,
        sum(((pnl.host_fee * pnl.properly_commission/100)/pnl.total_nights)*pnl.no_of_nights) as properly_share
        from
        (select
        (CASE WHEN (br.from_date <= '".$start_date."' and br.to_date >='".$end_date."')
        THEN DATEDIFF('".$end_date."','".$start_date."')
        when '".$start_date."' between br.from_date and br.to_date
        then DATEDIFF(br.to_date, '".$start_date."')
        when br.from_date >='".$start_date."' and br.to_date <= '".$end_date."'
        then DATEDIFF(br.to_date, br.from_date)
        when '".$end_date."' between br.from_date and br.to_date
        then DATEDIFF('".$end_date_add_one_day."', br.from_date)
        else 'not found'
        end
        ) no_of_nights,
        DATEDIFF(br.to_date, br.from_date) total_nights,
        br.from_date,
        br.to_date,
        br.id,
        br.payable_amount,
        b.gst_amount,
        b.host_fee,
        b.service_fee,
        b.markup_fee,
        b.gh_commission_from_host,
        br.properly_commission,
        `br`.`currency` AS `currency`
        from booking_requests br
        join bookings b on br.id=b.booking_request_id
        join property_pricing pp on br.pid=pp.pid
        where br.prive=1 and br.booking_status= 1 and br.pid=".$property_id." and (br.from_date between '".$start_date."' and '".$end_date."' or br.to_date between '".$start_date."' and '".$end_date."' or (br.from_date <='".$start_date."' and br.to_date >='".$end_date."'))) as pnl"
        );

        // phpcs:enable

        return $query;

    }//end getPrivePropertiesMonthlyBookingAmount()


     /**
      * Get Invoice End Month year
      *
      * @param integer $prive_owner_id Prive Owner id.
      *
      * @return array
      */
    public static function getInvoiceEndMonthYear(int $prive_owner_id)
    {
        $query = self::select(
            \DB::raw("DATE_FORMAT(br.to_date,'%Y-%m') as end_month_year"),
        )->from('prive_owner as po')->join(
            'properties as p',
            function ($join) use ($prive_owner_id) {
                    $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->join(
            'booking_requests as br',
            function ($join) {
                    $join->on('br.pid', '=', 'p.id')->where('br.booking_status', '=', BOOKED);
            }
        )->join('bookings as b', 'b.booking_request_id', '=', 'br.id')->where('br.prive', 1);
        $query = $query->orderBy('br.to_date', 'desc')->first();
        if (empty($query) === false) {
            return $query->toArray();
        }

        return [];

    }//end getInvoiceEndMonthYear()


}//end class
