<?php
/**
 * Admin Model containing all functions related to admin table
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
// phpcs:disable  
/**
 * // phpcs:enable
 * Class Admin
 */
class Admin extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'admin';


    /**
     * Get admin data
     *
     * @param string $email Email id.
     *
     * @return object
     */
    public static function getAdmin(string $email)
    {
        return self::where('email', '=', $email)->first();

    }//end getAdmin()


    /**
     * Get admin data
     *
     * @param integer $id Admin id.
     *
     * @return object
     */
    public static function getAdminById(int $id)
    {
        return self::find($id);

    }//end getAdminById()


    /**
     * Check admin data By id
     *
     * @param integer $id Admin id.
     *
     * @return boolean
     */
    public static function isAdmin(int $id)
    {
        $admin = self::find($id);

        if (empty($admin) === true) {
            return false;
        }

        return true;

    }//end isAdmin()


}//end class
