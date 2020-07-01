<?php
/**
 * Model containing data regarding property tags
 */

namespace App\Models;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PropertyTagMapping
 */
class PropertyTagMapping extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'property_tag_maping';


     /**
      * Save property tags by property id.
      *
      * @param integer $property_id Property id.
      * @param integer $tag_id      Tag Id.
      *
      * @return object
      */
    public function savePropertyTag(int $property_id, int $tag_id)
    {
        $property_tag_mapping         = new self;
        $property_tag_mapping->pid    = $property_id;
        $property_tag_mapping->tag_id = $tag_id;

        if ($property_tag_mapping->save() === false) {
            return (object) [];
        }

        return $property_tag_mapping;

    }//end savePropertyTag()


    /**
     * Get property tags by property id.
     *
     * @param integer $property_id Property id.
     *
     * @return object
     */
    public function getPropertyTags(int $property_id)
    {
        $tags = self::select('tag_id as id')->where('pid', $property_id)->get();

        if (empty($tags) === true) {
            return [];
        }

        return array_column($tags->toArray(), 'id');

    }//end getPropertyTags()


    /**
     * Delete property tags by property id.
     *
     * @param integer $property_id Property id.
     * @param array   $tag_ids     Tag Ids.
     *
     * @return void
     */
    public function deletePropertyTags(int $property_id, array $tag_ids)
    {
        self::where('pid', $property_id)->whereIn('tag_id', $tag_ids)->delete();

    }//end deletePropertyTags()


    /**
     * Get property tags by property ids.
     *
     * @param array   $property_ids Property ids.
     * @param integer $count        No of tags to fetch.
     *
     * @return array
     */
    public static function getPropertyTagsWithColorCodingByPropertyIds(array $property_ids, int $count=0)
    {
        // Get property id and all tags of property.
        $property_tags = self::from('property_tag_maping as ptm')->whereIn('ptm.pid', $property_ids)->join('property_tags as pt', 'ptm.tag_id', '=', 'pt.id', 'inner')->select('ptm.pid as id', 'pt.tag_name as name', 'pt.id as tid')->get()->toArray();

        // Make returned array as associative array with property id as key.
        $final_property_tag_array = [];

        // Loop through all properties tags.
        foreach ($property_tags as $tag) {
            $tag_array = self::getColorCodedTag($tag['name'], $tag['tid']);
            if (array_key_exists($tag['id'], $final_property_tag_array) === false) {
                $final_property_tag_array[$tag['id']] = [];
            }

            if ($count === 0 || count($final_property_tag_array[$tag['id']]) < $count) {
                $final_property_tag_array[$tag['id']][] = $tag_array;
            }
        }

        return $final_property_tag_array;

    }//end getPropertyTagsWithColorCodingByPropertyIds()


    /**
     * Get all property tags.
     *
     * @param array $selected_tags Selected Property Tags.
     *
     * @return array
     */
    public static function getAllPropertyTags(array $selected_tags=[])
    {
        // Get property id and all tags of property.
        $property_tags = self::from('property_tags as pt')->select('pt.tag_name as name', 'pt.id as id')->get()->toArray();

        // Make returned array as associative array with property id as key.
        $final_property_tag_array = [];

        // Loop through all properties tags.
        foreach ($property_tags as $tag) {
            $final_property_tag_array[] = [
                'id'       => $tag['id'],
                'name'     => $tag['name'],
                'selected' => (in_array($tag['id'], $selected_tags) === true) ? 1 : 0,
            ];
        }

        return $final_property_tag_array;

    }//end getAllPropertyTags()


    /**
     * Get property tags by property ids.
     *
     * @param string  $tag    Tag string name in db.
     * @param integer $tag_id Tag id in db.
     *
     * @return array
     */
    private static function getColorCodedTag(string $tag, int $tag_id)
    {
        $tag_class = trim(strtolower(str_replace(' ', '-', str_replace('/', '-', $tag))));

        if (in_array($tag_class, YELLOW_TAG_ARRAY) === true) {
            $color = YELLOW_TAG_ARRAY_COLOR_CODE;
        } else if (in_array($tag_class, GREEN_TAG_ARRAY) === true) {
            $color = GREEN_TAG_ARRAY_COLOR_CODE;
        } else {
            $color = GREY_TAG_ARRAY_COLOR_CODE;
        }

        $tag_params = [
            'id'    => $tag_id,
            'class' => $tag_class,
            'text'  => $tag,
        ];
        return array_merge($tag_params, $color);

    }//end getColorCodedTag()


}//end class
