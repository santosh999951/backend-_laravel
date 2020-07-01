<?php
/**
 * RelationshipManager Model containing all functions related to admin table
 */

namespace App\Models;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
/**
 * Class RelationshipManager
 */
class RelationshipManager extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'relationship_manager';


    /**
     * Get RM count
     *
     * @param string $email Email id.
     *
     * @return integer
     */
    public static function getRMCount(string $email)
    {
        return self::select('pid')->join(
            'admin as a',
            function ($admin) use ($email) {
                $admin->where('a.email', $email)->where(
                    function ($add_condition) {
                        $add_condition->where('a.id', '=', DB::raw('relationship_manager.admin_id'))->orWhere('a.id', '=', DB::raw('relationship_manager.subtitute_admin_id'));
                    }
                );
            }
        )->groupBy('pid')->count();

    }//end getRMCount()


    /**
     * Get RM Email
     *
     * @param integer $property_id Property id.
     *
     * @return object
     */
    public static function getRMEmail(int $property_id)
    {
        return self::from('relationship_manager as rm')->join('admin as a', 'rm.admin_id', '=', 'a.id')->select('a.email as email')->where('pid', $property_id)->first();

    }//end getRMEmail()


    /**
     * Get Host RM Email
     *
     * @param integer $user_id User id.
     *
     * @return object
     */
    public static function getRMEmailOfHost(int $user_id)
    {
        return self::from('properties as p')->join(
            'relationship_manager as rm',
            function ($join) use ($user_id) {
                        $join->on('p.id', '=', 'rm.pid')->where('p.user_id', '=', $user_id);
            }
        )->join('admin as a', 'rm.admin_id', '=', 'a.id')->select('a.email as email')->first();

    }//end getRMEmailOfHost()


}//end class
