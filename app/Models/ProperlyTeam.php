<?php
/**
 * Properly Team table
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\QueryException;
use TheSeer\Tokenizer\Exception;
use PHPUnit\Framework\MockObject\BadMethodCallException;

// phpcs:disable  
/**
 * // phpcs:enable
 * Class ProperlyTeam
 */
class ProperlyTeam extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'properly_team';


    /**
     * Save Properly Team
     *
     * @param array $params Parameters.
     *
     * @return object
     */
    public static function saveTeam(array $params)
    {
        $properly_team               = new self;
        $properly_team->manager_id   = $params['manager_id'];
        $properly_team->user_id      = $params['user_id'];
        $properly_team->team_type_id = $params['team_type_id'];
        $properly_team->status       = $params['status'];

        try {
            $properly_team->save();
        } catch (QueryException $e) {
            return false;
        }

        return true;

    }//end saveTeam()


    /**
     * Update Properly Team
     *
     * @param array $params Parameters.
     *
     * @return object
     */
    public static function updateTeam(array $params)
    {
        try {
            self::where('user_id', $params['user_id'])->update($params);
        } catch (QueryException $e) {
            return false;
        }

        return true;

    }//end updateTeam()


    /**
     * Update Properly Team
     *
     * @param integer $id     User id.
     * @param array   $params Parameters.
     *
     * @return array
     */
    public static function getMemberFilterResult(int $id, array $params=[])
    {
        $member_status = [
            PROPERLY_TEAM_MEMBER_INVITE,
            PROPERLY_TEAM_MEMBER_ACTIVE,
            PROPERLY_TEAM_MEMBER_SUSPEND,
           // PROPERLY_TEAM_MEMBER_DELETE,.
        ];
        // Need to replace with config.
        $query = DB::table('properly_team')->leftjoin('users', 'properly_team.user_id', '=', 'users.id');
        $query->leftjoin('properly_team_type', 'properly_team.team_type_id', '=', 'properly_team_type.id');
        $query->select('users.id', 'users.name', 'users.last_name', 'users.contact', 'properly_team_type.team_name', 'properly_team.status');

        if (isset($params['filter_id']) === true) {
            $filter     = $params['filter_id'];
            $filter_ids = explode(',', $filter);
            $query->where('properly_team.manager_id', $id)->whereIn('properly_team.team_type_id', $filter_ids);
        } else if (isset($params['team_user_id']) === true) {
            $query->where('properly_team.user_id', $params['team_user_id']);
        } else {
            $query->where('properly_team.manager_id', $id);
        }

        if (isset($params['status']) === true) {
            $query->where('properly_team.status', $params['status']);
        } else {
            $query->whereIn('properly_team.status', $member_status);
        }

        $data = $query->orderBy('properly_team.status')->orderBy('properly_team_type.team_name')->orderBy('users.name')->get()->toArray();

        if (count($data) === 0) {
            return [];
        }

        return $data;

    }//end getMemberFilterResult()


    /**
     * Check Properly Team Member Status.
     *
     * @param array $params Input Params.
     *
     * @return boolean
     */
    public static function checkMemberStatus(array $params)
    {
        $query = self::where($params);
        $data  = $query->get()->toArray();

        if (count($data) === 0) {
            return false;
        }

        return true;

    }//end checkMemberStatus()


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
            self::where('user_id', $params['user_id'])->where('manager_id', $params['manager_id'])->update($params);
        } catch (QueryException $e) {
            return false;
        }

        return true;

    }//end updateStatus()


        /**
         * Save Prive Manager Team Member.
         *
         * @param integer $manager_id Parameters.
         * @param integer $user_id    Parameters.
         *
         * @return array
         */
    public static function getTeamMemberToSuspend(int $manager_id, int $user_id)
    {
        $query = self::where('manager_id', $manager_id)->where('user_id', $user_id)->where('status', PROPERLY_TEAM_MEMBER_ACTIVE);
        $data  = $query->get()->toArray();

        if (count($data) === 0) {
            return [];
        }

        return $data;

    }//end getTeamMemberToSuspend()


    /**
     * Save Properly Team.
     *
     * @param integer $manager_id Parameters.
     * @param integer $user_id    Parameters.
     *
     * @return array
     */
    public static function getSuspendedMember(int $manager_id, int $user_id)
    {
        $query = self::where('manager_id', $manager_id)->where('user_id', $user_id)->where('status', PROPERLY_TEAM_MEMBER_SUSPEND);
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
        $data  = $query->first()->toArray();

        if (count($data) === 0) {
            return [];
        }

        return $data;

    }//end getMemberStatus()


    /**
     * Update Properly Team Member status on login.
     *
     * @param array $params Parameters.
     *
     * @return object
     */
    public static function updateStatusOnLogin(array $params)
    {
        try {
            self::where('user_id', $params['user_id'])->update($params);
        } catch (QueryException $e) {
            return false;
        }

        return true;

    }//end updateStatusOnLogin()


    /**
     * Check Properly Team Member Status.
     *
     * @param array $params Input Params.
     *
     * @return array
     */
    public static function getAddedMember(array $params)
    {
        $query = self::where($params);
        $data  = $query->get()->toArray();

        if (count($data) === 0) {
            return [];
        }

        return $data;

    }//end getAddedMember()


}//end class
