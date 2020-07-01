<?php
/**
 * OfferImage Model containing all functions related to offer_image table
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OfferImage
 */
class OfferImage extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'offer_images';
    use SoftDeletes;


    /**
     * Relationship offer_banner table .
     *
     * @return object
     */
    public function offerBanner()
    {
        return $this->belongsTo('App\Models\OfferBanner');

    }//end offerBanner()


    /**
     * Save Offer Images for offer_image table.
     *
     * @param array $offer_image Offers Array.
     *
     * @return boolean True/false
     */
    public static function insertImage(array $offer_image)
    {
        $offers               = new OfferImage();
        $offers->mobile_image = $offer_image['mobile_image'];
        $offers->web_image    = $offer_image['web_image'];
        $offers->default      = $offer_image['default'];
        $offers->sort         = $offer_image['sort'];
        $offers->offer_id     = $offer_image['offer_id'];

        // Saving offers in offer_image table.
        $offers->save();
        return true;

    }//end insertImage()


    /**
     * Update Offers for offer_image table.
     *
     * @param array $offers_data Offers Array.
     *
     * @return boolean True/false
     */
    public static function updateImage(array $offers_data)
    {
        // Updating offers in offer_image table.
        self::where('id', $offers_data['id'])->where('offer_id', $offers_data['offer_id'])->update($offers_data);
        return true;

    }//end updateImage()


    /**
     * Delete Offer from offer_image table.
     *
     * @param array $offers_data Offer Array.
     *
     * @return boolean True/false
     */
    public static function deleteImage(array $offers_data)
    {
        $offers = self::find($offers_data['id']);
        if (empty($offers) === true) {
            return false;
        }

        // Deleting offer from offers table.
        $offers->delete();

        return true;

    }//end deleteImage()


    /**
     * Delete Offer from offer_image table.
     *
     * @param array $offers_ids Offer Array.
     *
     * @return boolean True/false
     */
    public static function deleteMultipleImage(array $offers_ids)
    {
        // Deleting offer from offer_images table.
        self::whereIn('id', $offers_ids)->delete();
        return true;

    }//end deleteMultipleImage()


    /**
     * Delete Offer Image from offer_image table.
     *
     * @param integer $offer_id Offer id .
     *
     * @return boolean True/false
     */
    public static function deleteImageWithOfferId(int $offer_id)
    {
        self::select('offer_id')->where('offer_id', '=', $offer_id)->delete();
        return true;

    }//end deleteImageWithOfferId()


    /**
     * Get getDefaultImages
     *
     * @param integer $offer_id OfferId of Image.
     * @param integer $id       Id of Image.
     *
     * @return object offerlist
     */
    public static function getDefaultImages(int $offer_id, int $id=0)
    {
        $images = self::where('offer_id', $offer_id)->where('default', 1);
        if (empty($id) === false) {
            $images = $images->where('id', '!=', $id);
        }

        $response = $images->get();
        return $response;

    }//end getDefaultImages()


     /**
      * Update disableDefault
      *
      * @param integer $offer_id Offer_id of Image.
      * @param array   $data     Array of default of Image.
      *
      * @return integer
      */
    public static function disableDefault(int $offer_id, array $data)
    {
        $response = self::where('offer_id', $offer_id)->update($data);
        return $response;

    }//end disableDefault()


}//end class
