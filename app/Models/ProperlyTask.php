<?php
/**
 * ProperlyTask Model containing all functions related to Properly_tasks table
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
// phpcs:disable  
/**
 * // phpcs:enable
 * Class ProperlyTask
 */
class ProperlyTask extends Model
{

    /**
     * Table Name
     *
     * @var $table
     */
    protected $table = 'properly_tasks';


    /**
     * Save Properly Tasks
     *
     * @param array $params Parameters.
     *
     * @return object
     */
    public static function saveTask(array $params)
    {
        $prive_booking_tasks                 = new self;
        $prive_booking_tasks->status         = $params['status'];
        $prive_booking_tasks->type           = $params['type'];
        $prive_booking_tasks->entity_id      = $params['entity_id'];
        $prive_booking_tasks->entity_type    = $params['entity_type'];
        $prive_booking_tasks->created_by     = $params['created_by'];
        $prive_booking_tasks->assigned_by    = $params['assigned_by'];
        $prive_booking_tasks->run_at         = $params['run_at'];
        $prive_booking_tasks->reccuring_type = $params['reccuring_type'];

        if (isset($params['description']) === true) {
            $prive_booking_tasks->description = $params['description'];
        }

        if (isset($params['assigned_to']) === true) {
            $prive_booking_tasks->assigned_to = $params['assigned_to'];
        }

        try {
            $prive_booking_tasks->save();
        } catch (QueryException $e) {
            return false;
        }

        return true;
        ;

    }//end saveTask()


    /**
     * Update Properly Tasks
     *
     * @param array $params Parameters.
     *
     * @return object
     */
    public static function updateTask(array $params)
    {
        try {
            self::where('id', $params['id'])->update($params);
        } catch (QueryException $e) {
            return false;
        }

        return true;

    }//end updateTask()


    /**
     * Check Properly Tasks detail
     *
     * @param integer $task_id Task Id.
     *
     * @return array
     */
    public static function getTaskDetails(int $task_id)
    {
        return self::select('status', 'assigned_to', 'entity_id', 'reccuring_type', 'run_at')->where('id', $task_id)->first()->toArray();

    }//end getTaskDetails()


    /**
     * Check Properly Tasks Type By Entity Id
     *
     * @param integer $entity_id Entity Id.
     * @param integer $task_type Type Of task.
     *
     * @return array
     */
    public static function checkTaskTypeByEntityId(int $entity_id, int $task_type)
    {
        $query = self::where('entity_id', $entity_id)->where('type', $task_type)->first();
        if (empty($query) === true) {
            return [];
        }

        return $query->toArray();

    }//end checkTaskTypeByEntityId()


    /**
     * Update Suspend Member Task.
     *
     * @param integer $user_id    User id.
     * @param integer $manager_id Manager id.
     * @param array   $status     Status of task.
     *
     * @return boolean
     */
    public static function updateSuspendMemberTask(int $user_id, int $manager_id, array $status)
    {
        try {
            self::where('assigned_to', $user_id)->where('assigned_by', $manager_id)->whereIn('status', $status)->update(['assigned_to' => null, 'status' => 1]);
        } catch (QueryException $e) {
            return false;
        }

        return true;

    }//end updateSuspendMemberTask()


     /**
      * Delete Properly Tasks
      *
      * @param integer $id Id.
      *
      * @return boolean
      */
    public static function deleteTask(int $id)
    {
        try {
            self::where('id', $id)->delete();
        } catch (QueryException $e) {
            return false;
        }

        return true;

    }//end deleteTask()


     /**
      * Update Properly Tasks by entity id
      *
      * @param integer $entity_id Entity Id.
      * @param integer $task_type Task Type.
      * @param array   $params    Parameters.
      *
      * @return object
      */
    public static function updateTaskByEntityIdAndType(int $entity_id, int $task_type, array $params)
    {
        try {
            self::where('entity_id', $entity_id)->where('type', $task_type)->update($params);
        } catch (QueryException $e) {
            return false;
        }

        return true;

    }//end updateTaskByEntityIdAndType()


    /**
     * Get status of team member.
     *
     * @param integer $user_id    Parameters.
     * @param integer $manager_id Parameters.
     *
     * @return array
     */
    public static function getMemberStatus(int $user_id, int $manager_id)
    {
        $query = self::select('status')->where('user_id', $user_id)->where('manager_id', $manager_id);
        $data  = $query->get()->toArray();

        if (count($data) === 0) {
            return [];
        }

        return $data;

    }//end getMemberStatus()


}//end class
