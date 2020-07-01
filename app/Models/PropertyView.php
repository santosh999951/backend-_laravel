<?php
/**
 * Model containing data regarding view counts of property
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Libraries\Helper;
use Carbon\Carbon;

/**
 * Class PropertyView
 */
class PropertyView extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'property_views';

    /**
     * Variable definition.
     *
     * @var array
     */
    protected $fillable = ['updated_at'];


    /**
     * Merge all of view entries based on device id.
     *
     * @param string  $device_unique_id Device unique id.
     * @param integer $user_id          User id.
     *
     * @return boolean True/false
     */
    public static function mergePropertyViews(string $device_unique_id, int $user_id)
    {
        // Merge logged out user's data with loggedin user id.
        self::where('session_id', $device_unique_id)->update(
            [
                'user_id'    => $user_id,
                'session_id' => '',
            ]
        );

        return true;

    }//end mergePropertyViews()


    /**
     * Update view counter for property.
     *
     * @param string  $device_unique_id  Device unique id.
     * @param boolean $is_user_logged_in Check if user logged in.
     * @param integer $user_id           User id.
     * @param integer $property_id       Property id.
     * @param string  $start_date        Start date.
     * @param string  $end_date          End date.
     * @param integer $guests            Total no. of guests.
     *
     * @return null
     */
    public static function updatePropertyViewsCounter(string $device_unique_id, bool $is_user_logged_in, int $user_id, int $property_id, string $start_date, string $end_date, int $guests)
    {
        // If user is logged in.
        if ($is_user_logged_in === true) {
            $property_view = self::where('user_id', $user_id)->where('property_id', $property_id)->orderBy('updated_at', 'desc')->first();

            // If no previous view for the property by user.
            if (empty($property_view) === true) {
                $new_view              = new self;
                $new_view->ip          = Helper::getUserIpAddress();
                $new_view->user_id     = $user_id;
                $new_view->property_id = $property_id;
                $new_view->from_date   = $start_date;
                $new_view->to_date     = $end_date;
                $new_view->guests      = $guests;
                $new_view->source      = 'app';
                $new_view->session_id  = $device_unique_id;
                $new_view->save();

                return null;
            } else {
                $db_date = $property_view->updated_at;
                $diff    = ((time() - strtotime($db_date)) / 60);

                // If their is a view by user in last 30 minutes.
                if ($diff <= 30) {
                    $property_view->update(['updated_at' => Carbon::now()->toDateTimeString()]);

                    return null;
                }

                $new_view              = new self;
                $new_view->ip          = Helper::getUserIpAddress();
                $new_view->user_id     = $user_id;
                $new_view->property_id = $property_id;
                $new_view->from_date   = $start_date;
                $new_view->to_date     = $end_date;
                $new_view->guests      = $guests;
                $new_view->source      = 'app';
                $new_view->session_id  = $device_unique_id;
                $new_view->save();

                return null;
            }//end if
        } else {
            if ($device_unique_id === '') {
                $property_view = self::where('ip', Helper::getUserIpAddress())->where('user_id', 0)->where('property_id', $property_id)->orderBy('updated_at', 'desc')->first();
            } else {
                $property_view = self::where('session_id', $device_unique_id)->where('user_id', 0)->where('property_id', $property_id)->orderBy('updated_at', 'desc')->first();
            }

            // If no previous view on this property from given ip/device_id.
            if (isset($property_view) === false) {
                $new_view              = new self;
                $new_view->ip          = Helper::getUserIpAddress();
                $new_view->user_id     = 0;
                $new_view->property_id = $property_id;
                $new_view->from_date   = $start_date;
                $new_view->to_date     = $end_date;
                $new_view->guests      = $guests;
                $new_view->source      = 'app';
                $new_view->session_id  = $device_unique_id;
                $new_view->save();

                return null;
            } else {
                $db_date = $property_view->updated_at;
                $diff    = ((time() - strtotime($db_date)) / 60);

                // If their is a view by ip/device_id in last 30 minutes.
                if ($diff <= 30) {
                    $property_view->update(['updated_at' => Carbon::now()->toDateTimeString()]);

                    return null;
                }

                $new_view              = new self;
                $new_view->ip          = Helper::getUserIpAddress();
                $new_view->user_id     = 0;
                $new_view->property_id = $property_id;
                $new_view->from_date   = $start_date;
                $new_view->to_date     = $end_date;
                $new_view->guests      = $guests;
                $new_view->source      = 'app';
                $new_view->session_id  = $device_unique_id;
                $new_view->save();

                return null;
            }//end if
        }//end if

    }//end updatePropertyViewsCounter()


}//end class
