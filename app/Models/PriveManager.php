<?php
/**
 * PriveManager Model containing all functions related to Prive manager table
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PriveManager.
 */
class PriveManager extends Model
{

    /**
     * Table Name
     *
     * @var $table
     */
    protected $table = 'prive_manager';


    /**
     * Save Prive Manager
     *
     * @param array $params Parameters.
     *
     * @return object
     */
    public static function savePriveManager(array $params)
    {
        $prive_manager              = new self;
        $prive_manager->pid         = $params['pid'];
        $prive_manager->user_id     = $params['user_id'];
        $prive_manager->status      = $params['status'];
        $prive_manager->assigned_by = $params['assigned_by'];

        try {
            $prive_manager->save();
        } catch (QueryException $e) {
            return false;
        }

        return true;
        ;

    }//end savePriveManager()


    /**
     * Update Properly Team Member status
     *
     * @param array $params Parameters.
     *
     * @return object
     */
    public static function updateStatus(array $params)
    {
        try {
            self::where('user_id', $params['user_id'])->update($params);
        } catch (QueryException $e) {
            return false;
        }

        return true;

    }//end updateStatus()


    /**
     * Save Prive Manager Team Member.
     *
     * @param integer $prive_id Parameters.
     * @param integer $user_id  Parameters.
     *
     * @return array
     */
    public static function getTeamMemberToSuspend(int $prive_id, int $user_id)
    {
        $query = self::where('assigned_by', $prive_id)->where('user_id', $user_id)->where('status', PROPERLY_TEAM_MEMBER_ACTIVE);
        $data  = $query->get()->toArray();

        if (count($data) === 0) {
            return [];
        }

        return $data;

    }//end getTeamMemberToSuspend()


    /**
     * Save Properly Team.
     *
     * @param integer $prive_id Parameters.
     * @param integer $user_id  Parameters.
     *
     * @return array
     */
    public static function getSuspendedMember(int $prive_id, int $user_id)
    {
        $query = self::where('assigned_by', $prive_id)->where('user_id', $user_id)->where('status', PROPERLY_TEAM_MEMBER_SUSPEND);
        $data  = $query->get()->toArray();

        if (count($data) === 0) {
            return [];
        }

        return $data;

    }//end getSuspendedMember()


    /**
     * Get status of team member.
     *
     * @param integer $user_id Parameters.
     *
     * @return boolean
     */
    public static function getMemberStatus(int $user_id)
    {
        $query = self::select('status')->where('user_id', $user_id);
        $data  = $query->get()->toArray();

        if (count($data) === 0) {
            return [];
        }

        return $data;

    }//end getMemberStatus()


}//end class
