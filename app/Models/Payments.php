<?php
/**
 * Model containing data regarding payment gateways
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;

/**
 * Class Payments
 */
class Payments extends Model
{

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'payments';

}//end class
