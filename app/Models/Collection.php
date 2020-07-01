<?php
/**
 * Collection Model contain all functions releated to collection
 */

namespace App\Models;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;

/**
 * Class Collection
 */
class Collection extends Model
{

     /**
      * Table Name
      *
      * @var string
      */
    protected $table = 'collection';


     /**
      * Helper function to create scope with active equal one
      *
      * @param integer $collection_id Collection id.
      *
      * @return integer Collection Count.
      */
    public static function doesCollectionExist(int $collection_id)
    {
        return self::where('id', $collection_id)->get()->count();

    }//end doesCollectionExist()


    /**
     * Helper function to create scope with active equal one
     *
     * @param array $data Input Data.
     *
     * @return array Collection Data.
     */
    public static function getCollectionAndPropertyData(array $data)
    {
        // Input params.
        $collection_id   = (array_key_exists('collection_id', $data) === true) ? $data['collection_id'] : '';
        $offset          = (int) $data['offset'];
        $total           = (int) $data['total'];
        $property_total  = (isset($data['property_total']) === true ) ? (int) $data['property_total'] : 0;
        $property_offset = (isset($data['property_offset']) === true ) ? (int) $data['property_offset'] : 0;
        $guests          = DEFAULT_NUMBER_OF_UNITS;
        $bedrooms        = DEFAULT_NUMBER_OF_GUESTS;

        // Will Remove this as Request Model done.
        $offset          = ($offset < 0) ? 0 : $offset;
        $total           = ($total < 0) ? DEFAULT_NUMBER_OF_COLLECTIONS : $total;
        $property_total  = ($property_total < 0) ? DEFAULT_NUMBER_OF_COLLECTIONS_PROPERTIES : $property_total;
        $property_offset = ($property_offset < 0) ? 0 : $property_offset;

        // Where conditions.
        $where                   = '';
        $where_conditions        = [];
        $collection_where_clause = '';
        // Add collection to check.
        if (empty($collection_id) === false) {
            $collection_where_clause = 'AND c.id = '.$collection_id.' ';
        }

        // Add property limit.
        $select_rank = '';
        $from_rank   = '';
        $having_rank = '';
        if (empty($property_total) === false) {
            $select_rank = ', ( CASE c.id WHEN @curType THEN @curRow := @curRow + 1 ELSE @curRow := 1 AND @curType := c.id END ) AS rank ';
            $from_rank   = " (SELECT @curRow := 0, @curType := '') r, ";
            $having_rank = ' HAVING rank <= '.($property_offset + $property_total).' and rank >= '.($property_offset + 1).' ';
        }

        // Where conditions for inner query.
        $where_conditions[] = 'p.status = 1';
        $where_conditions[] = 'p.enabled = 1';
        $where_conditions[] = 'p.admin_score > 0';
        $where_conditions[] = 'p.deleted_at IS NULL';

        // Implode using AND.
        $where = ' where '.implode(' AND ', $where_conditions);

        // Collection limits.
        $collection_limit = ($total > 0) ? ' LIMIT '.$offset.','.$total.' ' : '';

        // Return collections data.
        // phpcs:disable
        return DB::select(
            "
            select
                c.id as collection_id,
                c.title as collection_title,
                c.collection_order,
                c.image collection_image,
                ps.property_score,
                p.id as property_id,
                p.title,
                p.area,
                p.city,
                p.state,
                p.country,
                p.currency,
                p.v_lat as latitude,
                p.v_lng as longitude,
                p.instant_book,
                p.fake_discount,
                p.cash_on_arrival,
                p.prive,
                p.cancelation_policy,
                p.accomodation,
                p.room_type,
                p.bedrooms,
                p.property_type,
                p.custom_discount,
                p.service_fee,
                p.units,
                p.min_nights,
                p.max_nights,
                pp.per_night_price,
                pp.gh_commission,
                pp.markup_service_fee,
                pp.additional_guest_count,
                pp.additional_guest_fee,
                pp.cleaning_fee,
                pp.cleaning_mode,
                pt.name as property_type_name,
                rt.name as room_type_name,
                u.id as host_id,
                u.gender as host_gender,
                u.profile_img as host_image,
                RTRIM(u.name) as host_name,
                ceil(if($guests/p.accomodation > $bedrooms/p.bedrooms, $guests/p.accomodation, $bedrooms/p.bedrooms)) as units_consumed
                ".$select_rank."
            from ".$from_rank." (
                SELECT
                    distinct(c.id),
                    c.title,
                    c.image,
                    c.collection_order as collection_order

                FROM 
                    collection AS c
                INNER join collection_property_mapping cpm 
                    ON c.id=cpm.collection_id 
                INNER join properties p 
                    ON p.id = cpm.pid 
                ".$where." and  
                    c.deleted_at IS NULL 
                    AND c.is_watermark = 1 
                ".$collection_where_clause."

                ORDER BY 
                  IF(c.collection_order = 0,99999999,c.collection_order)

                ".$collection_limit.'
            ) as c

            INNER join collection_property_mapping cpm 
                ON c.id=cpm.collection_id 

            INNER join properties p 
                ON p.id = cpm.pid 

            INNER join property_pricing pp 
                ON pp.pid = p.id

            INNER JOIN property_type pt 
                ON p.property_type = pt.id 

            INNER JOIN room_type rt 
                ON p.room_type = rt.id 

            INNER JOIN users u
                ON p.user_id = u.id

            LEFT JOIN property_stats_new ps 
                ON ps.id = p.id 

            '.$where.'

            GROUP BY c.id, p.id
            '.$having_rank.'
        '
        );
         // phpcs:enable

    }//end getCollectionAndPropertyData()


    /**
     * Function to get collection data
     *
     * @param integer $offset        Offset value.
     * @param integer $total         Total Collection Data.
     * @param string  $collection_id Collection Id.
     *
     * @return array Collection Data.
     */
    public static function getCollectionData(int $offset, int $total, string $collection_id='')
    {
        $collection_query = self::select(
            'id as collection_id',
            'title as collection_title',
            'image as collection_image',
            'collection_order as collection_order'
        )->whereNull('deleted_at')->where('is_watermark', '=', 1);
        if (empty($collection_id) === false) {
            $collection_query->where('id', '=', (int) $collection_id);
        }

        if ($total > 0) {
            $collection_query->addLimit($offset, $total);
        }

        return $collection_query->get()->toArray();

    }//end getCollectionData()


    /**
     * Add limit and offset.
     *
     * @param EloquentQuery $query  Query to be passed.
     * @param integer       $offset Offset.
     * @param integer       $limit  Limit.
     *
     * @return EloquentQuery
     */
    public static function scopeAddLimit(EloquentQuery $query, int $offset, int $limit)
    {
        return $query->offset($offset)->limit($limit);

    }//end scopeAddLimit()


}//end class
