<?php
/**
 * OfferBanner Model containing all functions related to offer_banner table
 */

namespace App\Models;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// phpcs:disable  
/**
 * // phpcs:enable
 * Class Admin
 */
class OfferBanner extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'offer_banner';
    use SoftDeletes;


    /**
     * Relationship offer_image table .
     *
     * @return object
     */
    public function offerImage()
    {
        return $this->hasMany('App\Models\OfferImage', 'offer_id');
        ;

    }//end offerImage()


    /**
     * Get Offerslist
     *
     * @param string  $offer_name Name of offer.
     * @param integer $offer_id   Id of offer.
     * @param integer $default    Default of offer.
     *
     * @return array offerlist
     */
    public static function getOffers(string $offer_name='', int $offer_id=0, int $default=0)
    {
        $select_banner_param = [
            'id',
            'name',
            'title',
            'description',
            'default as default offer',
            'status',
        ];

        $response     = [];
        $offer_banner = self::with('offerImage')->where('name', '=', $offer_name);
        if ((empty($offer_name) === false) && (empty($offer_id) === false)) {
            $response = $offer_banner->where('id', '!=', $offer_id)->count();
        } else if ((empty($offer_name) === false)) {
            $response = $offer_banner->count();
        } else if ((empty($default) === false)) {
            $response = self::where('default', '=', 1)->count();
        } else {
            $offer_banner_object = self::select($select_banner_param)->with(
                [
                    'offerImage' => function ($query) {
                        $select_image_param = [
                            'offer_id',
                            'web_image',
                            'mobile_image',
                            'sort',
                            'default',
                        ];

                        $query->select($select_image_param)->orderBy('sort', 'DESC');
                    },
                ]
            )->get();

            if (empty($offer_banner_object) === false) {
                $response = $offer_banner_object->toArray();
            }
        }//end if

        return $response;

    }//end getOffers()


    /**
     * Save Offers for offer_banner table.
     *
     * @param array $offers_data Array of offer values.
     *
     * @return boolean True/false
     */
    public static function insertOffer(array $offers_data)
    {
        // Saving offers in offers table.
        $offers              = new OfferBanner();
        $offers->name        = $offers_data['name'];
        $offers->title       = $offers_data['title'];
        $offers->description = $offers_data['description'];
        $offers->default     = $offers_data['default'];
        $offers->status      = $offers_data['status'];
        $offers->destination = $offers_data['destination'];
        $offers->save();

        return $offers->id;

    }//end insertOffer()


    /**
     * Update Offers for offer_banner table.
     *
     * @param array $offers_data Array of offer values.
     *
     * @return boolean True/false
     */
    public static function updateOffer(array $offers_data)
    {
        // Updating offers in offers table.
        self::where('id', $offers_data['id'])->update($offers_data);
        return true;

    }//end updateOffer()


    /**
     * Delete Offer from offer_banner table.
     *
     * @param integer $offer_id Id of offer which need to be deleted.
     *
     * @return boolean True/false
     */
    public static function deleteOffer(int $offer_id)
    {
        $offers = self::find($offer_id);
        // Deleting offer from offers table.
        $offers->delete();
        return true;

    }//end deleteOffer()


    /**
     * Get Offer
     *
     * @param integer $offer_id Id of offer.
     *
     * @return object offer_id
     */
    public static function getOffer(int $offer_id)
    {
        $offers = self::find($offer_id);
        return $offers;

    }//end getOffer()


    /**
     * Update disableDefault
     *
     * @return integer rows effected
     */
    public static function disableDefault()
    {
        $response = self::where('default', 1)->update(['default' => 0]);
        return $response;

    }//end disableDefault()


    /**
     * Get Offer By name
     *
     * @param string $offer_name Name of offer which needs to be fetched.
     *
     * @return array offers row
     */
    public static function getOfferByName(string $offer_name='')
    {
        $select_banner_param = [
            'id',
            'name',
            'title',
            'description',
            'default as default offer',
            'status',
            'destination',
        ];

        $response = [];
        if ((empty($offer_name) === false) && ($offer_name !== 'default')) {
            $response = self::select($select_banner_param)->with('offerImage')->where('name', '=', $offer_name)->get()->toArray();
        } else if ($offer_name === 'default') {
            $response = self::select($select_banner_param)->with('offerImage')->where('default', '=', 1)->get()->toArray();
        }

        return $response;

    }//end getOfferByName()


}//end class
