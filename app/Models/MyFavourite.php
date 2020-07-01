<?php
/**
 * MyFavourite contain all functions related to user's wishlist
 */

namespace App\Models;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MyFavourite
 */
class MyFavourite extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'myfavourite';


    /**
     * Function to get user favourite properties.
     *
     * @param integer $user_id User id.
     *
     * @return object Users wishlisted properties.
     */
    public static function getUserFavouriteProperties(int $user_id)
    {
        return self::where('user_id', $user_id)->get();

    }//end getUserFavouriteProperties()


    /**
     * Function to removes property from user wishlist.
     *
     * @param integer $user_id     User id.
     * @param integer $property_id Property id.
     *
     * @return boolean Property unwishlisted or not.
     */
    public static function removePropertyFromUserWishlist(int $user_id, int $property_id)
    {
        return self::select('property_id')->where('user_id', $user_id)->where('property_id', '=', $property_id)->delete();

    }//end removePropertyFromUserWishlist()


    /**
     * Function to add a property to user wishlist.
     *
     * @param integer $user_id     User id.
     * @param integer $property_id Property id.
     *
     * @return boolean Property wishlisted or not.
     */
    public static function addPropertyToUserWishlist(int $user_id, int $property_id)
    {
        $property              = new self;
        $property->user_id     = $user_id;
        $property->property_id = $property_id;
        if ($property->save() === true) {
            return true;
        } else {
            return false;
        }

    }//end addPropertyToUserWishlist()


    /**
     * Function to check if property has been wishlisted by user.
     *
     * @param integer $pid     Property id.
     * @param integer $user_id User id.
     *
     * @return object Property wishlisted or not.
     */
    public static function checkIfPropertyIsInWishlist(int $pid, int $user_id)
    {
        return self::where('user_id', $user_id)->where('property_id', $pid)->first();

    }//end checkIfPropertyIsInWishlist()


    /**
     * Function to get user favourite properties with all the property details (like stats, pricing).
     *
     * @param integer $user_id  User id.
     * @param integer $guests   Guests.
     * @param integer $bedrooms Bedrooms.
     * @param integer $offset   Offset for records.
     * @param integer $total    Total records to fetch.
     *
     * @return array Property wishlisted or not.
     */
    public static function getWishlistedPropertiesOfUser(int $user_id, int $guests, int $bedrooms, int $offset=0, int $total=10)
    {
        // phpcs:disable Generic.Files.LineLength
        $favourite_list = self::join('properties as p', 'myfavourite.property_id', '=', 'p.id')->join('property_pricing as pp', 'myfavourite.property_id', '=', 'pp.pid')->join('property_type as pt', 'pt.id', '=', 'p.property_type')->join('room_type as rt', 'rt.id', '=', 'p.room_type')->join('users as u', 'p.user_id', '=', 'u.id')->join('property_stats_new as ps', 'ps.id', '=', 'p.id', 'left')->select(
            'p.id',
            'p.prive',
            'p.cancelation_policy',
            'p.title',
            'p.area',
            'p.city',
            'p.state',
            'p.country',
            'p.currency',
            'p.service_fee',
            'p.v_lat as latitude',
            'p.v_lng as longitude',
            'p.instant_book',
            'p.fake_discount',
            'p.cash_on_arrival',
            'pp.per_night_price',
            'pp.additional_guest_fee',
            'pp.additional_guest_count',
            'p.units',
            'p.min_nights',
            'p.max_nights',
            'pp.gh_commission',
            'pp.markup_service_fee',
            'pp.cleaning_fee',
            'pp.cleaning_mode',
            'p.custom_discount',
            'p.accomodation',
            'p.room_type',
            'p.bedrooms',
            'p.property_type',
            'pt.name as property_type_name',
            'ps.property_score',
            'rt.name as room_type_name',
            'u.id as host_id',
            'u.gender as host_gender',
            'u.profile_img as host_image',
            DB::raw('ceil(if('.$guests.'/p.accomodation > '.$bedrooms.'/p.bedrooms, '.$guests.'/p.accomodation, '.$bedrooms.'/p.bedrooms)) as units_consumed, RTRIM(u.name) as host_name')
        )->where('myfavourite.user_id', '=', $user_id)->where('p.deleted_at', null)->where('p.enabled', 1)->where('p.status', 1)->where('p.admin_score', '>', 0)->orderBy('myfavourite.id', 'desc');

        if ($total > 0) {
            if ($offset > 0) {
                $favourite_list = $favourite_list->skip($offset);
            }

            $favourite_list = $favourite_list->take($total);
        }

        $favourite_list = $favourite_list->get()->toArray();
        // phpcs:enable

        return $favourite_list;

    }//end getWishlistedPropertiesOfUser()


    /**
     * Function to get list of properties user has liked from a property array.
     *
     * @param integer $user_id      User id.
     * @param array   $property_ids Property ids.
     *
     * @return array Array of users wishlisted properties.
     */
    public static function getUserWishlistedPropertiesFromPropertyIds(int $user_id, array $property_ids)
    {
        return self::select('property_id')->where('user_id', $user_id)->whereIn('property_id', $property_ids)->get()->toArray();

    }//end getUserWishlistedPropertiesFromPropertyIds()


}//end class
