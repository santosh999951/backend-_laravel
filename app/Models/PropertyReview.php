<?php
/**
 * Model containing data regarding property reviews
 */

namespace App\Models;

use DB;
use Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;
use App\Libraries\Helper;

/**
 * Class PropertyReview
 */
class PropertyReview extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'property_reviews';


    /**
     * Get Property review for booking.
     *
     * @param integer $booking_id Booking id.
     *
     * @return boolean True/false
     */
    public static function getReviewForBooking(int $booking_id)
    {
        return self::where('booking_id', '=', $booking_id)->first();

    }//end getReviewForBooking()


    /**
     * Add limit and offset to fetch property reviews.
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
     * Get property reviews.
     *
     * @param integer $pid    Property id.
     * @param integer $offset Offset.
     * @param integer $limit  Limit.
     *
     * @return array
     */
    public static function getPropertyReviewData(int $pid, int $offset=0, int $limit=DEFAULT_LIMIT_FOR_PROPERTY_REVIEWS)
    {
        //phpcs:ignore
        $query = self::select(
            'pr.comments',
            'b.to_date as created_at',
            'pr.images',
            DB::raw('(CASE WHEN `u`.`profile_img` is not null then `u`.`profile_img` else "" end ) as profile_img'),
            'pr.traveller_id',
            'b.from_date',
            'b.to_date',
            'br.guests',
            'u.gender'
        // phpcs:ignore
        )->selectRaw('RTRIM(u.name) as traveller_name, AVG(r.rating) as property_rating')->from('property_reviews as pr')->leftJoin('bookings as b', 'b.id', '=', 'pr.booking_id')->leftJoin('booking_requests as br', 'br.id', '=', 'b.booking_request_id')->leftJoin('users as u', 'u.id', '=', 'pr.traveller_id')->leftJoin('users as h', 'h.id', '=', 'pr.host_id')->leftjoin('traveller_ratings as r', 'r.booking_requests_id', '=', 'br.id')->where('pr.pid', $pid)->where('r.property_id', $pid)->where('r.enabled', 1)->whereNull('u.deleted_at')->Where(
            function ($query) {
                $query->where('pr.status', APPROVED_REVIEW)->orWhere('pr.traveller_id', PROPERTY_REVIEW_ADMIN_ID);
            }
        )->groupBy('pr.booking_id')->orderByRaw('property_rating DESC, CHAR_LENGTH(comments) DESC');
        $output['total_review_count'] = count($query->get());
        $output['reviews']            = $query->addLimit($offset, $limit)->get()->toArray();
        return $output;

    }//end getPropertyReviewData()


    /**
     * Get host property reviews.
     *
     * @param integer $host_id      Host User id.
     * @param array   $filter_param Filter Param.
     * @param integer $offset       Offset.
     * @param integer $limit        Limit.
     *
     * @return array
     */
    public static function getHostPropertyReviews(int $host_id, array $filter_param=[], int $offset=0, int $limit=DEFAULT_LIMIT_FOR_PROPERTY_REVIEWS)
    {
        //phpcs:ignore
        $query = self::select(
            'pr.comments',
            'pr.reply',
            'pr.created_at',
            'pr.images',
            DB::raw('(CASE WHEN `t`.`profile_img` is not null then `t`.`profile_img` else "" end ) as profile_img'),
            'pr.traveller_id',
            't.gender',
            'br.id as request_id',
            'br.pid as id',
            'br.units as units_consumed',
            'br.booking_status',
            'b.from_date',
            'b.to_date',
            'br.guests',
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
            DB::raw('RTRIM(h.name) as host_name'),
            'h.profile_img AS host_image'
        )->selectRaw('RTRIM(t.name) as traveller_name, ROUND(AVG(tr.rating), 1) as property_rating')->from('property_reviews as pr')->join('bookings as b', 'b.id', '=', 'pr.booking_id');
        $query->join('booking_requests as br', 'br.id', '=', 'b.booking_request_id')->join('properties as p', 'p.id', '=', 'br.pid');
        $query->join('property_type as pt', 'pt.id', '=', 'p.property_type')->join('room_type as rt', 'rt.id', '=', 'p.room_type')->join('users as t', 't.id', '=', 'pr.traveller_id')->join('users as h', 'h.id', '=', 'pr.host_id');
        $query->leftjoin('traveller_ratings as tr', 'tr.booking_requests_id', '=', 'br.id');
        $query->where('pr.host_id', $host_id)->where('tr.enabled', 1)->where('br.booking_status', '>', REQUEST_APPROVED);

        if (empty($filter_param) === false) {
            if (isset($filter_param['property_id']) === true) {
                $query->where('p.id', $filter_param['property_id']);
            }

            if (isset($filter_param['reply']) === true && $filter_param['reply'] === 0) {
                $query->where('pr.status', NEW_REVIEW);
            }
        }

        $query->groupBy('b.id')->orderBy('pr.created_at', 'DESC');

        $output['reviews'] = $query->addLimit($offset, $limit)->get()->toArray();

        return $output;

    }//end getHostPropertyReviews()


    /**
     * Get property reviews count by traveller.
     *
     * @param integer $pid Property id.
     *
     * @return array
     */
    public static function getPropertyReviewCount(int $pid)
    {
        //phpcs:ignore
        $query = self::selectRaw(
            'count(distinct pr.booking_id) total'
        )->from('property_reviews as pr')->join('bookings as b', 'b.id', '=', 'pr.booking_id')->join('booking_requests as br', 'br.id', '=', 'b.booking_request_id')->join('traveller_ratings as r', 'r.booking_requests_id', '=', 'br.id');
        $query->where('pr.pid', $pid)->where('r.enabled', 1)->where('br.booking_status', '>', REQUEST_APPROVED);
        $total_review_count = ($query->get()->toArray())[0]['total'];
        $new_review_count   = ($query->where('pr.status', NEW_REVIEW)->get()->toArray())[0]['total'];

        $output['total_review_count'] = $total_review_count;
        $output['new_review_count']   = $new_review_count;

        return $output;

    }//end getPropertyReviewCount()


    /**
     * Get property awating reply count by host.
     *
     * @param integer $host_id Host id.
     *
     * @return array
     */
    public static function getAwatingReplyCountByHost(int $host_id)
    {
        $query = self::selectRaw(
            'count(distinct pr.booking_id) total'
        )->from('property_reviews as pr')->join('bookings as b', 'b.id', '=', 'pr.booking_id')->join('traveller_ratings as r', 'r.booking_requests_id', '=', 'b.booking_request_id')->where('pr.host_id', $host_id)->where('r.enabled', 1);
        $query->where('pr.status', NEW_REVIEW);

        $total_review_count = ($query->get()->toArray())[0]['total'];

        return $total_review_count;

    }//end getAwatingReplyCountByHost()


    /**
     * Save property review.
     *
     * @param array $data Property review data.
     *
     * @return boolean true/false
     */
    public static function addPropertyReview(array $data)
    {
        // Get all vars as individuals.
        extract($data);

        // Saving review in property review table.
        $property_review               = new self;
        $property_review->pid          = $data['property_id'];
        $property_review->traveller_id = $data['user_id'];
        $property_review->host_id      = $data['host_id'];
        $property_review->comments     = $data['review'];
        $property_review->booking_id   = $data['booking_id'];
        $property_review->images       = json_encode($data['review_images']);

        try {
            $property_review->save();
        } catch (QueryException $e) {
            return false;
        }

        return true;

    }//end addPropertyReview()


    /**
     * Save Review reply.
     *
     * @param array $data Review reply data.
     *
     * @return boolean true/false
     */
    public static function addReviewReply(array $data)
    {
        try {
            // Saving review reply in property review table.
            self::where('booking_id', $data['booking_id'])->where('host_id', $data['host_id'])->update(['reply' => $data['reply'], 'updated_at' => Carbon::now(), 'status' => APPROVED_REVIEW]);
        } catch (QueryException $e) {
            Helper::logError('<<<<---- Review Reply not saved by host---->>>>.', ['Booking Id' => $data['booking_id'], 'Host Id' => $data['host_id'], 'Reply' => $data['reply']]);
            return false;
        }

        return true;

    }//end addReviewReply()


}//end class
