<?php
/**
 * PromoBanner Model contain all functions related to banner on promo.
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PromoBanner
 */
class PromoBanner extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'promo_banners';
    use SoftDeletes;


    /**
     * Save Promo Banners.
     *
     * @param array $promo_banners Array of Promo banner.
     *
     * @return boolean True/false
     */
    public static function insertBannner(array $promo_banners)
    {
         // Saving Home Banner.
        $promobanner               = new PromoBanner();
        $promobanner->name         = $promo_banners['name'];
        $promobanner->mobile_image = $promo_banners['mobile_image'];
        $promobanner->web_image    = $promo_banners['web_image'];
        $promobanner->status       = $promo_banners['status'];
        $promobanner->save();

        return $promobanner->id;

    }//end insertBannner()


    /**
     * Get Promo banner
     *
     * @param integer $banner_id   Id of banner.
     * @param string  $banner_name Name of banner.
     *
     * @return mixed
     */
    public static function getBanner(int $banner_id, string $banner_name)
    {
        $promo_banner = self::where('name', '=', $banner_name);
        if ((empty($banner_name) === false) && (empty($banner_id) === false)) {
            $response = $promo_banner->where('id', '!=', $banner_id)->count();
        } else if ((empty($banner_name) === false)) {
            $response = $promo_banner->count();
        } else {
            $response = self::find($banner_id);
        }

        return $response;

    }//end getBanner()


    /**
     * Update Promo Banner
     *
     * @param array $banner_data Array of banner values.
     *
     * @return boolean True/false
     */
    public static function updatePromoBanner(array $banner_data)
    {
        // Updating Banner.
        self::where('id', $banner_data['id'])->update($banner_data);
        return true;

    }//end updatePromoBanner()


    /**
     * Delete Promo Banner from promo_banners table.
     *
     * @param integer $banner_id Id of Banner which need to be deleted.
     *
     * @return boolean True/false
     */
    public static function deletePromoBanner(int $banner_id)
    {
        $banner = self::find($banner_id);
        // Deleting banner from promo_banners table.
        $banner->delete();
        return true;

    }//end deletePromoBanner()


    /**
     * Get Bannerlist
     *
     * @param integer $promo_id Id of Banner which need to be fetched.
     *
     * @return array response
     */
    public static function getPromoBanners(int $promo_id=0)
    {
        $response     = [];
        $promo_banner = self::select('name', 'id', 'mobile_image', 'web_image', 'status');
        if (empty($promo_id) === false) {
            $promo_banner = $promo_banner->where('status', 1)->where('id', $promo_id);
        }

        $promo_banner = $promo_banner->get();

        if (empty($promo_banner) === false) {
            $response = $promo_banner->toArray();
        }

        return $response;

    }//end getPromoBanners()


}//end class
