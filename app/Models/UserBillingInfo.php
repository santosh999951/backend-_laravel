<?php
/**
 * UserBillingInfo Model contain all functions to User Billing
 */

namespace App\Models;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

use Helper;

/**
 * Class UserBillingInfo
 */
class UserBillingInfo extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'users_billing_info';


    /**
     * Get Bank Detail of user.
     *
     * @param integer $user User id.
     *
     * @return array
     */
    public static function getUserBankDetail(int $user)
    {
        $billing_data = self::select(
            'payee_name',
            'address1',
            'address2',
            'payee_country',
            'payee_state',
            'bank_name',
            'branch_name',
            'account_number',
            'ifsc_code',
            'routing_no',
            'gstin'
        )->where('user_id', '=', $user)->first();

        if (empty($billing_data) === true) {
            return [];
        }

        return $billing_data->toArray();

    }//end getUserBankDetail()


    /**
     * Update Bank Detail.
     *
     * @param integer $user_id  User Id.
     * @param array   $data     Bank detail data.
     * @param integer $admin_id Admin Id.
     *
     * @return boolean true/false
     */
    public static function updateBankDetail(int $user_id, array $data, int $admin_id=0)
    {
        $billing_info = self::where('user_id', '=', $user_id)->first();

        if (empty($billing_info) === true) {
            return false;
        }

        if (isset($data['payee_name']) === true) {
            $billing_info->payee_name = $data['payee_name'];
        }

        if (isset($data['bank_name']) === true) {
            $billing_info->bank_name = $data['bank_name'];
        }

        if (isset($data['branch_name']) === true) {
            $billing_info->branch_name = $data['branch_name'];
        }

        if (isset($data['account_number']) === true) {
            $billing_info->account_number = $data['account_number'];
        }

        if (isset($data['ifsc_code']) === true) {
            $billing_info->ifsc_code = $data['ifsc_code'];
        }

        if (isset($data['address_line_1']) === true) {
            $billing_info->address1 = $data['address_line_1'];
        }

        if (isset($data['address_line_2']) === true) {
            $billing_info->address2 = $data['address_line_2'];
        }

        if (isset($data['country']) === true) {
            $billing_info->payee_country = $data['country'];
        }

        if (isset($data['state']) === true) {
            $billing_info->payee_state = $data['state'];
        }

        if (isset($data['routing_number']) === true) {
            $billing_info->routing_no = $data['routing_number'];
        }

        if (isset($data['gstin']) === true) {
            $billing_info->gstin = $data['gstin'];
        }

        if ($admin_id > 0) {
            $billing_info->last_edited_by = $admin_id;
        }

        if ($billing_info->save() === true) {
            return true;
        }

        return false;

    }//end updateBankDetail()


    /**
     * Add Bank Detail.
     *
     * @param array $bank_details Bank detail data.
     *
     * @return array
     */
    public function addBankDetail(array $bank_details)
    {
        $billing_info = self::where('user_id', '=', $bank_details['user_id'])->first();

        if (empty($billing_info) === false) {
            return [];
        }

        $billing_info = new self;

        // Save Payee Details.
        $billing_info->user_id       = $bank_details['user_id'];
        $billing_info->payee_name    = $bank_details['payee_name'];
        $billing_info->address1      = $bank_details['address_line_1'];
        $billing_info->address2      = $bank_details['address_line_2'];
        $billing_info->payee_country = $bank_details['country'];
        $billing_info->payee_state   = $bank_details['state'];

        // Save Payee Bank Details.
        $billing_info->bank_name      = $bank_details['bank_name'];
        $billing_info->branch_name    = $bank_details['branch_name'];
        $billing_info->account_number = $bank_details['account_number'];
        $billing_info->ifsc_code      = $bank_details['ifsc_code'];
        $billing_info->routing_no     = $bank_details['routing_number'];
        $billing_info->gstin          = $bank_details['gstin'];
        $last_edited_by               = $bank_details['admin_id'];

        if ($billing_info->save() === true) {
            return [
                'payee_name'     => $billing_info->payee_name,
                'bank_name'      => $billing_info->bank_name,
                'branch_name'    => $billing_info->branch_name,
                'account_number' => $billing_info->account_number,
                'ifsc_code'      => $billing_info->ifsc_code,
            ];
        }

        return [];

    }//end addBankDetail()


}//end class
