<?php
/**
 * HomeWidget contain all functions home widgets
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;

/**
 * Class HomeWidget
 */
class HomeWidget extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'home_widgets';


    /**
     * Helper function to create scope with status equal one
     *
     * @param EloquentQuery $query Eloquent model query.
     *
     * @return EloquentQuery Active scope query
     */
    public static function scopeActive(EloquentQuery $query)
    {
        return $query->where('status', 1);

    }//end scopeActive()


    /**
     * Helper function to get home widgets.
     *
     * @return object Home Widgets.
     */
    public static function getHomeWidgets()
    {
        return self::active()->join('property_type as PropertyType', 'home_widgets.ptype_id', '=', 'PropertyType.id', 'left')->select('home_widgets.*', 'PropertyType.name as property_type_name')->where('home_widgets.type', 1)->orderBy('rank')->get();

    }//end getHomeWidgets()


}//end class
