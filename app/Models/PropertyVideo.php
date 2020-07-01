<?php
/**
 * Model containing data regarding property videos
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Builder as EloquentQuery;

/**
 * Class PropertyVideo
 */
class PropertyVideo extends Model
{
    use SoftDeletes;

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'property_videos';


    /**
     * Get active property videos.
     *
     * @param EloquentQuery $query Query to be passed.
     *
     * @return EloquentQuery
     */
    public static function scopeActive(EloquentQuery $query)
    {
        return $query->where('status', 1);

    }//end scopeActive()


    /**
     * Save Property Videos.
     *
     * @param integer $property_id   Property Id.
     * @param string  $video_url     Property Video Url.
     * @param string  $thumbnail_url Thumbnail image.
     * @param integer $status        Video status.
     *
     * @return object
     */
    public function savePropertyVideo(int $property_id, string $video_url, string $thumbnail_url, int $status)
    {
        $existing_video = self::where('pid', $property_id)->first();

        if (empty($existing_video) === false) {
            $property_video = $existing_video;
        } else {
            $property_video      = new self;
            $property_video->pid = $property_id;
        }

        $property_video->name      = $video_url;
        $property_video->status    = $status;
        $property_video->thumbnail = $thumbnail_url;

        if ($property_video->save() === false) {
            return (object) [];
        }

        return $property_video;

    }//end savePropertyVideo()


    /**
     * Delete Property Videos.
     *
     * @param integer $property_id Property Id.
     * @param string  $video_url   Property Video Url.
     *
     * @return object
     */
    public function deletePropertyVideo(int $property_id, string $video_url)
    {
        return self::where('pid', $property_id)->where('name', $video_url)->delete();

    }//end deletePropertyVideo()


    /**
     * Get property videos by id.
     *
     * @param array $property_ids Array of property ids for which videos to fetch.
     *
     * @return array
     */
    public static function getPropertyVideosByPropertyIds(array $property_ids)
    {
        $video_array     = [];
        $property_videos = self::select('name', 'thumbnail', 'pid')->whereIn('pid', $property_ids)->active()->get();

        // Property videos.
        foreach ($property_videos as $property_video) {
            $property_id = $property_video->pid;

            // Create array for new property id.
            if (array_key_exists($property_id, $video_array) === true) {
                $video_array[$property_id] = [];
            }

            // Video data.
            $video_array[$property_id][] = [
                'url'       => PROPERTY_VIDEO_RESOLUTION['720'].$property_video->name,
                'thumbnail' => (empty($property_video->thumbnail) === false) ? PROPERTY_VIDEO_THUMBNAIL_RESOLUTION['thumbs'].$property_video->thumbnail : S3_PROPERTY_DEFAULT_THUMBNAIL_IMAGE,
                'type'      => 1,
            ];
        }

        return $video_array;

    }//end getPropertyVideosByPropertyIds()


    /**
     * Get property video details.
     *
     * @param integer $property_id Input property ids.
     *
     * @return array
     */
    public static function getAllPropertiesVideosDetails(int $property_id)
    {
        // Get property video data.
        $property_video_data = self::select('name', 'thumbnail', 'status')->where('pid', $property_id)->get()->toArray();

        $video_array = [];

        // Iterate over each property image.
        foreach ($property_video_data as $one_property_video) {
             // Video data.
            $video_array[] = [
                'url'       => PROPERTY_VIDEO_RESOLUTION['720'].$one_property_video['name'],
                'thumbnail' => PROPERTY_VIDEO_THUMBNAIL_RESOLUTION['thumbs'].$one_property_video['thumbnail'],
                'status'    => $one_property_video['status'],
            ];
        }

        return $video_array;

    }//end getAllPropertiesVideosDetails()


}//end class
