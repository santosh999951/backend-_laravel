<?php
/**
 * Cancellation Policy Model contain all functions releated to cancellation polices of property
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CancellationPolicy
 */
class CancellationPolicy extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'cancellation_policy';


     /**
      * Helper function to create scope with active equal one
      *
      * @param array $cancellation_policy_ids Array of cancelltion polices.
      *
      * @return array Cancellation policy details.
      */
    public static function getCancellationPoliciesByIds(array $cancellation_policy_ids)
    {
        $cancellation_policies = self::whereIn('id', $cancellation_policy_ids)->select('id', 'title', 'policy_days', 'desc', 'popup_text')->get();

        // Collection id as key.
        return collect($cancellation_policies)->keyBy('id')->toArray();

    }//end getCancellationPoliciesByIds()


     /**
      * Helper function to get All Cancellation Policy
      *
      * @param integer $selected_cancellation_policy Selected cancelltion polices.
      *
      * @return array Cancellation policy details.
      */
    public static function getAllCancellationPolicies(int $selected_cancellation_policy=0)
    {
        $cancellation_policies = self::select('id', 'title', 'policy_days')->get()->toArray();

        // Cancellation Policy.
        $response_cancellation_policy = [];
        foreach ($cancellation_policies as $cancellation_policy) {
            $response_cancellation_policy[] = [
                'id'       => $cancellation_policy['id'],
                'title'    => $cancellation_policy['title'],
                'selected' => ($cancellation_policy['id'] === $selected_cancellation_policy) ? 1 : 0,
            ];
        }

        return $response_cancellation_policy;

    }//end getAllCancellationPolicies()


}//end class
