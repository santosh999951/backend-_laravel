<?php
/**
 * PushNotifications Model containing all functions related to Push notification table
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PushNotifications
 */
class PushNotifications extends Model
{

    /**
     * Table Name
     *
     * @var $table
     */
    protected $table = 'push_notification';


}//end class
