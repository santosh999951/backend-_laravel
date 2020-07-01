<?php
/**
 * Collection Model contain all functions releated to collection
 */

namespace App\Models;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;

/**
 * Class Collection
 */
class RmBookingRemark extends Model
{

     /**
      * Table Name
      *
      * @var string
      */
    protected $table = 'rm_booking_remark';


    /**
     * Save Booking Notes
     *
     * @param integer $booking_request_id Booking Request Id.
     * @param integer $prive_manager_id   Prive Manager Id.
     * @param string  $remark             Remark.
     * @param integer $type               Type Eg 1 for RM 2 for Properly.
     *
     * @return object
     */
    public static function saveBookingNotes(int $booking_request_id, int $prive_manager_id, string $remark, int $type=1)
    {
        $rm_remark = new self;
        $rm_remark->booking_request_id = $booking_request_id;
        $rm_remark->admin_id           = $prive_manager_id;
        $rm_remark->remark             = $remark;
        $rm_remark->type               = $type;
        $rm_remark->save();
        return $rm_remark;

    }//end saveBookingNotes()


    /**
     * Fetch Booking Notes
     *
     * @param integer $booking_request_id Booking Request Id.
     *
     * @return array
     */
    public static function getBookingNotes(int $booking_request_id)
    {
        $notes = self::from('rm_booking_remark as rmr')->leftJoin(
            'admin as a',
            function ($join) {
                $join->on('rmr.admin_id', '=', 'a.id')->where('rmr.type', 1);
            }
        )->leftJoin(
            'users as u',
            function ($join) {
                    $join->on('rmr.admin_id', '=', 'u.id')->where('rmr.type', 2);
            }
        )->where('booking_request_id', $booking_request_id)->whereIn('type', [1, 2]);

        $notes = $notes->select(
            'rmr.id as id',
            'rmr.remark as remark',
            'rmr.type as type',
            \DB::raw(
                "(CASE WHEN a.id is not null 
                    THEN RTRIM(CONCAT(UPPER(SUBSTRING(a.name,1,1)),LOWER(SUBSTRING(a.name,2)))) 
                WHEN u.name is not null 
                    THEN RTRIM(CONCAT(UPPER(SUBSTRING(u.name,1,1)),LOWER(SUBSTRING(u.name,2))))     
                ELSE '' end) as last_updated_by"
            ),
            \DB::raw("DATE_FORMAT(convert_tz(rmr.created_at,'+00:00','+05:30'), '%D %b %Y, %h:%i %p') as last_updated")
        )->get();

        if (empty($notes) === false) {
            return $notes->toArray();
        }

        return [];

    }//end getBookingNotes()


}//end class
