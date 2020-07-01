<?php
/**
 * Admin review Model containing all functions related to admin_reviews table
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// phpcs:disable  
/** 
 * // phpcs:enable
 * Class Admin Review
 */
class AdminReview extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'admin_review';


    /**
     * Get admin reviews
     *
     * @param integer $pid Property id.
     *
     * @return object
     */
    public static function getAdminReivewDetails(int $pid)
    {
        // phpcs:ignore
        $admin_review = self::select('ar.comments', 'ar.created_at', 'ar.images', \DB::raw('(CASE WHEN `aru`.`image` is not null then `aru`.`image` else "" end ) as profile_img'))->selectRaw('? as traveller_id, ? as from_date, ? as to_date, ? as guests, ? as gender, aru.name as traveller_name, ar.rating as property_rating', ['', '0000-00-00', '0000-00-00', '', ''])->from('admin_review as ar')->leftJoin('admin_review_users as aru', 'aru.id', '=', 'ar.review_user_id')->where('ar.pid', '=', $pid);

        return $admin_review;

    }//end getAdminReivewDetails()


}//end class
