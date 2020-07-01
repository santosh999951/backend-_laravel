<?php
/**
 * Model containing data regarding property tile tracking stats
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class PropertyTileStat
 */
class PropertyTileStat extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'property_tile_stats_1';

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table_secondary = 'property_tile_stats_2';

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $master_table = 'property_tile_stats_3';


    /**
     * Get available property types.
     *
     * @return string
     */
    public static function getWorkingTableName()
    {
        $hour = (int) Carbon::now('GMT')->format('H');
        if (($hour % 2) === 0) {
            // Even hour.
            //phpcs:ignore
            $table = (new self)->table;
        } else {
            //phpcs:ignore
            $table = (new self)->table_secondary;
        }

        return $table;

    }//end getWorkingTableName()


}//end class
