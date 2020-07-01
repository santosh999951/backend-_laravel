<?php
/**
 * Invite contain all functions related to inviting users.
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invite
 */
class Invite extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'invite';
}//end class
