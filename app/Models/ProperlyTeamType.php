<?php
/**
 * Properly Role model
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
// phpcs:disable  
/**
 * // phpcs:enable
 * Class ProperlyTeam
 */
class ProperlyTeamType extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'properly_team_type';


    /**
     * Get Role Details.
     *
     * @return array
     */
    public static function getTeamDetails()
    {
        return self::select('id', 'team_name')->get()->toArray();

    }//end getTeamDetails()


}//end class
