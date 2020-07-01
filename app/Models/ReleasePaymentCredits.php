<?php
/**
 * Model containing data regarding Released Payment Credits
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReleasePaymentCredits
 */
class ReleasePaymentCredits extends Model
{

    /**
     * Variable definition.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'release_payment_credits';

}//end class
