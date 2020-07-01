<?php
/**
 * OfferService containing methods to related to BannerOffers
 */

namespace App\Libraries\v1_6;

use App\Models\OfferBanner;
use App\Models\OfferImage;
use App\Models\HomeBanner;
use App\Models\PromoBanner;

/**
 * Class OfferService
 */
class OfferService
{


    /**
     * Get Offerslist
     *
     * @param string  $offer_name Name of offer.
     * @param integer $offer_id   Id of offer.
     * @param integer $default    Default value of offer.
     *
     * @return array offerlist
     */
    public static function getOffers(string $offer_name='', int $offer_id=0, int $default=0)
    {
        $response = OfferBanner::getOffers($offer_name, $offer_id, $default);
        return $response;

    }//end getOffers()


    /**
     * InsertOffers
     *
     * @param array $offers Array of offer values.
     *
     * @return boolean True/false
     */
    public static function insertOffer(array $offers)
    {
        $response = OfferBanner::insertOffer($offers);
        return $response;

    }//end insertOffer()


    /**
     * UpdateOffers
     *
     * @param array $offers_data All parameters which need to be updated.
     *
     * @return boolean True/false
     */
    public static function updateOffer(array $offers_data)
    {
        $response = OfferBanner::updateOffer($offers_data);
        return $response;

    }//end updateOffer()


    /**
     * DeleteOffers
     *
     * @param integer $offers Id of offer which needs to be deleted.
     *
     * @return boolean True/false
     */
    public static function deleteOffer(int $offers)
    {
        $response = OfferBanner::deleteOffer($offers);
        return $response;

    }//end deleteOffer()


    /**
     * Get Offer
     *
     * @param integer $offer_id Id of offer which needs to be deleted.
     *
     * @return array offers row
     */
    public static function getOffer(int $offer_id)
    {
        $response = OfferBanner::getOffer($offer_id);
        return $response;

    }//end getOffer()


    /**
     * Get Offer By name
     *
     * @param string $offer_name Name of offer which needs to be fetched.
     *
     * @return array offers row
     */
    public static function getOfferByName(string $offer_name)
    {
        $response = OfferBanner::getOfferByName($offer_name);
        return $response;

    }//end getOfferByName()


     /**
      * InsertOfferImage
      *
      * @param array $offer_images_data Offers Array.
      *
      * @return boolean True/false
      */
    public static function insertOfferImage(array $offer_images_data)
    {
        $response = OfferImage::insertImage($offer_images_data);
        return $response;

    }//end insertOfferImage()


     /**
      * UpdateOfferImages
      *
      * @param array $offers_image_data Offers Array.
      *
      * @return boolean True/false
      */
    public static function updateOfferImage(array $offers_image_data)
    {
        $response = OfferImage::updateImage($offers_image_data);
        return $response;

    }//end updateOfferImage()


    /**
     * DeleteImageWithOfferId
     *
     * @param integer $offers Id of offer images which needs to be deleted.
     *
     * @return boolean True/false
     */
    public static function deleteImageWithOfferId(int $offers)
    {
        $response = OfferImage::deleteImageWithOfferId($offers);
        return $response;

    }//end deleteImageWithOfferId()


    /**
     * DeleteImages
     *
     * @param array $offers Id of offer images which needs to be deleted.
     *
     * @return boolean True/false
     */
    public static function deleteMultipleOfferImage(array $offers)
    {
        $response = OfferImage::deleteMultipleImage($offers);
        return $response;

    }//end deleteMultipleOfferImage()


    /**
     * Get getDefaultImages
     *
     * @param integer $offer_id OfferId of Image.
     * @param integer $id       Id of Image.
     *
     * @return array offerlist
     */
    public static function getDefaultImages(int $offer_id, int $id=0)
    {
        $response = OfferImage::getDefaultImages($offer_id, $id);
        return $response;

    }//end getDefaultImages()


     /**
      * Update Default to 0.
      *
      * @param integer $offer_id Offer_id of Image.
      * @param array   $data     Default value of images.

      * @return integer
      */
    public static function disableDefault(int $offer_id, array $data)
    {
        $response = OfferImage::disableDefault($offer_id, $data);
        return $response;

    }//end disableDefault()


     /**
      * Update Default to 0.
      *
      * @return integer
      */
    public static function disableOfferDefault()
    {
        $response = OfferBanner::disableDefault();
        return $response;

    }//end disableOfferDefault()


    /**
     * InsertHomeBanner
     *
     * @param array $home_banners Array of Home Banners.
     *
     * @return boolean True/false
     */
    public static function insertHomeBanner(array $home_banners)
    {
        $response = HomeBanner::insertBannner($home_banners);
        return $response;

    }//end insertHomeBanner()


    /**
     * Get HomeBanner
     *
     * @param integer $banner_id Id of Banner.
     *
     * @return array banner row
     */
    public static function getHomeBanner(int $banner_id=0)
    {
        $response = HomeBanner::getBanner($banner_id);
        return $response;

    }//end getHomeBanner()


    /**
     * UpdateBanner
     *
     * @param array $banner_data All parameters which need to be updated.
     *
     * @return boolean True/false
     */
    public static function updateBanner(array $banner_data)
    {
        $response = HomeBanner::updateBanner($banner_data);
        return $response;

    }//end updateBanner()


     /**
      * DeleteBanner
      *
      * @param integer $banner_id Id of banner which needs to be deleted.
      *
      * @return boolean True/false
      */
    public static function deleteBanner(int $banner_id)
    {
        $response = HomeBanner::deleteBanner($banner_id);
        return $response;

    }//end deleteBanner()


    /**
     * Get BannerList
     *
     * @return array bannerlist
     */
    public static function getBanners()
    {
        $response = HomeBanner::getBanners();
        return $response;

    }//end getBanners()


    /**
     * InsertPromoBanner
     *
     * @param array $promo_banners Array of Home Banners.
     *
     * @return boolean True/false
     */
    public static function insertPromoBanner(array $promo_banners)
    {
        $response = PromoBanner::insertBannner($promo_banners);
        return $response;

    }//end insertPromoBanner()


    /**
     * Get PromoBanner
     *
     * @param integer $banner_id   Id of Banner.
     * @param string  $banner_name Name of Banner.
     *
     * @return mixed
     */
    public static function getPromoBanner(int $banner_id=0, string $banner_name='')
    {
        $response = PromoBanner::getBanner($banner_id, $banner_name);
        return $response;

    }//end getPromoBanner()


    /**
     * UpdatePromoBanner
     *
     * @param array $banner_data All parameters which need to be updated.
     *
     * @return boolean True/false
     */
    public static function updatePromoBanner(array $banner_data)
    {
        $response = PromoBanner::updatePromoBanner($banner_data);
        return $response;

    }//end updatePromoBanner()


     /**
      * DeletePromoBanner
      *
      * @param integer $banner_id Id of banner which needs to be deleted.
      *
      * @return boolean True/false
      */
    public static function deletePromoBanner(int $banner_id)
    {
        $response = PromoBanner::deletePromoBanner($banner_id);
        return $response;

    }//end deletePromoBanner()


    /**
     * Get promo BannerList
     *
     * @param integer $promo_id Id of promo which needs to be fetched.
     *
     * @return array bannerlist
     */
    public static function getPromoBanners(int $promo_id=0)
    {
        $response = PromoBanner::getPromoBanners($promo_id);
        return $response;

    }//end getPromoBanners()


}//end class
