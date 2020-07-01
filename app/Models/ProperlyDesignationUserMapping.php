<?php
/**
 * Model containing data regarding properly designation user mapping
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;
use DB;

/**
 * Class ProperlyDesignationUserMapping
 */
class ProperlyDesignationUserMapping extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'properly_designation_user_mapping';


    /**
     * Assign designation to user.
     *
     * @param integer $designation_id Designation Id.
     * @param integer $user_id        User id.
     *
     * @return object
     */
    public function assignDesignation(int $designation_id, int $user_id)
    {
        $designation = self::where('designation_id', $designation_id)->where('user_id', $user_id)->first();

        if (empty($designation) === true) {
            return self::insert(['designation_id' => $designation_id, 'user_id' => $user_id]);
        }

        return $designation;

    }//end assignDesignation()


    /**
     * Get User Properties
     *
     * @param integer $user_id User id.
     *
     * @return array
     */
    public static function getUserProperties(int $user_id)
    {
        $properties = self::select('properly_designation_pid_mapping.property_id')->from('properly_designation_user_mapping')->join(
            'properly_designations as node',
            function ($node) use ($user_id) {
                $node->on('properly_designation_user_mapping.designation_id', '=', 'node.id')->where('properly_designation_user_mapping.user_id', $user_id);
            }
        )->join(
            'properly_designations as child',
            function ($child) {
                    $child->whereBetween('child.lft', [DB::raw('node.lft'), DB::raw('node.rgt')]);
            }
        )->join('properly_designation_pid_mapping', 'properly_designation_pid_mapping.designation_id', '=', 'child.id')->distinct()->pluck('properly_designation_pid_mapping.property_id');

        if (empty($properties) === true) {
            return [];
        }

        return $properties->toArray();

    }//end getUserProperties()


}//end class
