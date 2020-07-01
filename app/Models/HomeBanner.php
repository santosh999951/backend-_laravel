<?php
/**
 * HomeBanner Model contain all functions related to banner on home.
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class HomeBanner
 */
class HomeBanner extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'home_banners';
    use SoftDeletes;


    /**
     * Helper function to add scope
     *
     * @param EloquentQuery $query Eloquent query.
     *
     * @return EloquentQuery Eloquent query.
     */
    public static function scopeActive(EloquentQuery $query)
    {
        return $query->where('status', 1);

    }//end scopeActive()


    /**
     * Helper function to get mobile banners.
     *
     * @return object Home banners.
     */
    public static function getMobileHomeBanners()
    {
        return self::active()->orderBy('id', 'desc')->get();

    }//end getMobileHomeBanners()


    /**
     * Save Home Banners.
     *
     * @param array $home_banners Array of Home banner.
     *
     * @return boolean True/false
     */
    public static function insertBannner(array $home_banners)
    {
         // Saving Home Banner.
        $homebanner              = new HomeBanner();
        $homebanner->image       = $home_banners['image'];
        $homebanner->status      = $home_banners['status'];
        $homebanner->destination = $home_banners['destination'];
        $homebanner->save();

        return $homebanner->id;

    }//end insertBannner()


    /**
     * Get Home banner
     *
     * @param integer $banner_id Id of banner.
     *
     * @return object banner_id
     */
    public static function getBanner(int $banner_id)
    {
        $banner = self::find($banner_id);
        return $banner;

    }//end getBanner()


    /**
     * Update Banner
     *
     * @param array $banner_data Array of banner values.
     *
     * @return boolean True/false
     */
    public static function updateBanner(array $banner_data)
    {
        // Updating Banner.
        self::where('id', $banner_data['id'])->update($banner_data);
        return true;

    }//end updateBanner()


    /**
     * Delete Banner from home_banners table.
     *
     * @param integer $banner_id Id of Banner which need to be deleted.
     *
     * @return boolean True/false
     */
    public static function deleteBanner(int $banner_id)
    {
        $banner = self::find($banner_id);
        // Deleting offer from offers table.
        $banner->delete();
        return true;

    }//end deleteBanner()


    /**
     * Get Bannerlist
     *
     * @return array offerlist
     */
    public static function getBanners()
    {
        $response    = [];
        $home_banner = self::select('id', 'image', 'status', 'destination')->get();

        if (empty($home_banner) === false) {
            $response = $home_banner->toArray();
        }

        return $response;

    }//end getBanners()


}//end class
