<?php
/**
 * Model containing data regarding property images
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;

/**
 * Class PropertyImage
 */
class PropertyImage extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'properties_images';


    /**
     * Get not hidden images.
     *
     * @param EloquentQuery $query Query to be passed.
     *
     * @return EloquentQuery
     */
    public static function scopeNotHidden(EloquentQuery $query)
    {
        return $query->where('is_hide', 0);

    }//end scopeNotHidden()


    /**
     * Get processed images.
     *
     * @param EloquentQuery $query Query to be passed.
     *
     * @return EloquentQuery
     */
    public static function scopeIsProcessed(EloquentQuery $query)
    {
        return $query->where('is_proccessed', 1);

    }//end scopeIsProcessed()


    /**
     * Get non deteled images.
     *
     * @param EloquentQuery $query Query to be passed.
     *
     * @return EloquentQuery
     */
    public static function scopeNotDeleted(EloquentQuery $query)
    {
        return $query->where('is_deleted', 0)->where('is_deleted_from_bulk', 0);

    }//end scopeNotDeleted()


    /**
     * Save Property Images.
     *
     * @param integer $property_id Property Id.
     * @param string  $image       Property Image Url.
     * @param string  $caption     Property Image Caption.
     * @param integer $is_hide     Image Hide status.
     * @param integer $order       Image Order to show.
     * @param integer $is_deleted  Is Deleted Status.
     *
     * @return object
     */
    public function savePropertyImage(int $property_id, string $image, string $caption, int $is_hide=0, int $order=1, int $is_deleted=0)
    {
        $existing_image = self::where('pid', $property_id)->where('image', $image)->first();

        if (empty($existing_image) === false) {
            $property_image = $existing_image;
        } else {
            $property_image        = new self;
            $property_image->pid   = $property_id;
            $property_image->image = $image;
        }

        $property_image->caption    = $caption;
        $property_image->is_hide    = $is_hide;
        $property_image->order_by   = $order;
        $property_image->is_deleted = $is_deleted;

        if ($property_image->save() === false) {
            return (object) [];
        }

        return $property_image;

    }//end savePropertyImage()


    /**
     * Get property images.
     *
     * @param array   $property_ids Input property ids.
     * @param array   $headers      Headers containing screen width and height.
     * @param integer $count        No. of images to fetch.
     *
     * @return array
     */
    public static function getPropertiesImagesByIds(array $property_ids, array $headers, int $count=0)
    {
        // Get property image data.
        $property_image_data = self::select('pid', 'image', 'caption')->whereIn('pid', $property_ids)->isProcessed()->notDeleted()->notHidden()->orderBy('order_by', 'ASC')->get()->toArray();

        $associative_property_images = [];

        // Get base_url for image (s3 path,directory) based on device type (size, connection strength).
        $base_url = Property::imageBaseUrlAsPerDeviceSizeAndConnection($headers);

        // Iterate over each property image.
        foreach ($property_image_data as $one_property_image) {
            // Propery id.
            $property_id    = $one_property_image['pid'];
            $property_image = $base_url['image_base_url'].$one_property_image['image'];
            $image_caption  = $one_property_image['caption'];

            // New property.
            if (array_key_exists($property_id, $associative_property_images) === false) {
                $associative_property_images[$property_id] = [];
            }

            // Check number of images to fetch ( count = 0 to fetch all images).
            if ($count === 0 || count($associative_property_images[$property_id]) < $count) {
                array_push($associative_property_images[$property_id], ['image' => $property_image, 'caption' => $image_caption]);
            }
        }

        // Set default image for empty image property.
        foreach (array_unique($property_ids) as $pid) {
            $property_id    = $pid;
            $property_image = S3_PROPERTY_DEFAULT_IMAGE;
            $image_caption  = '';
            if (array_key_exists($pid, $associative_property_images) === false) {
                $associative_property_images[$pid][] = [
                    'image'   => $property_image,
                    'caption' => $image_caption,
                ];
            }
        }

        return $associative_property_images;

    }//end getPropertiesImagesByIds()


    /**
     * Get property images details.
     *
     * @param integer $property_id Input property ids.
     *
     * @return array
     */
    public static function getAllPropertiesImagesDetails(int $property_id)
    {
        // Get property image data.
        $property_image_data = self::select('image', 'caption', 'is_deleted', 'order_by', 'is_hide')->where('pid', $property_id)->orderBy('order_by', 'ASC')->get()->toArray();

        $associative_property_images = [];

        $base_url = S3_PROPERTY_2X_DIR;

        // Iterate over each property image.
        foreach ($property_image_data as $one_property_image) {
            $associative_property_images[] = [
                'image'   => $base_url.$one_property_image['image'],
                'caption' => $one_property_image['caption'],
                'is_hide' => $one_property_image['is_hide'],
                'unlink'  => $one_property_image['is_deleted'],
                'order'   => $one_property_image['order_by'],
            ];
        }

        return $associative_property_images;

    }//end getAllPropertiesImagesDetails()


}//end class
