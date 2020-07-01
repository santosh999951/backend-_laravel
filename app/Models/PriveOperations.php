<?php
/**
 * Prive Operations Model contain all functions to Prive bookings
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use Helper;

/**
 * Class PriveOperations
 */
class PriveOperations extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'prive_operation';


    /**
     * Save no show reasons data
     *
     * @param integer $request_id Booking Request Id.
     * @param integer $reason_id  No Show Reason Id.
     * @param string  $comment    No Show Reson Comment..
     *
     * @return object
     */
    public function saveNoShow(int $request_id, int $reason_id, string $comment=null)
    {
        $existing_prive_operation = self::where('booking_request_id', $request_id)->first();

        if (empty($existing_prive_operation) === false) {
            $prive_operation = $existing_prive_operation;
        } else {
            $prive_operation = new self;
            $prive_operation->booking_request_id = $request_id;
        }

        $prive_operation->no_show           = 1;
        $prive_operation->no_show_reason_id = $reason_id;
        $prive_operation->no_show_comment   = $comment;

        if ($prive_operation->save() === false) {
            return (object) [];
        }

        return $prive_operation;

    }//end saveNoShow()


    /**
     * Save Operational Note data
     *
     * @param integer $request_id    Booking Request Id.
     * @param integer $note_added_by Note Added By.
     * @param string  $note          Note.
     *
     * @return object
     */
    public function saveOperationalNote(int $request_id, int $note_added_by, string $note)
    {
        $existing_prive_operation = self::where('booking_request_id', $request_id)->first();

        if (empty($existing_prive_operation) === false) {
            $prive_operation = $existing_prive_operation;
        } else {
            $prive_operation = new self;
            $prive_operation->booking_request_id = $request_id;
        }

        $prive_operation->op_note                 = $note;
        $prive_operation->op_note_last_updated    = Carbon::now()->toDateTimeString();
        $prive_operation->op_note_last_updated_by = $note_added_by;

        if ($prive_operation->save() === false) {
            return (object) [];
        }

        return $prive_operation;

    }//end saveOperationalNote()


    /**
     * Save Operational Note data
     *
     * @param integer $request_id    Booking Request Id.
     * @param integer $note_added_by Note Added By.
     * @param string  $note          Note.
     *
     * @return object
     */
    public function saveManagerialNote(int $request_id, int $note_added_by, string $note)
    {
        $existing_prive_operation = self::where('booking_request_id', $request_id)->first();

        if (empty($existing_prive_operation) === false) {
            $prive_operation = $existing_prive_operation;
        } else {
            $prive_operation = new self;
            $prive_operation->booking_request_id = $request_id;
        }

        $prive_operation->note                 = $note;
        $prive_operation->note_last_updated    = Carbon::now()->toDateTimeString();
        $prive_operation->note_last_updated_by = $note_added_by;

        if ($prive_operation->save() === false) {
            return (object) [];
        }

        return $prive_operation;

    }//end saveManagerialNote()


    /**
     * Save Expected Checkin datetime
     *
     * @param integer $request_id       Booking Request Id.
     * @param string  $checkin_datetime Checkin Datetime.
     *
     * @return object
     */
    public function saveExpectedCheckinDatetime(int $request_id, string $checkin_datetime)
    {
        $existing_prive_operation = self::where('booking_request_id', $request_id)->first();

        if (empty($existing_prive_operation) === false) {
            $prive_operation = $existing_prive_operation;
        } else {
            $prive_operation = new self;
            $prive_operation->booking_request_id = $request_id;
        }

        $prive_operation->expected_checkin = $checkin_datetime;

        if ($prive_operation->save() === false) {
            return (object) [];
        }

        return $prive_operation;

    }//end saveExpectedCheckinDatetime()


    /**
     * Save Expected Checkout datetime
     *
     * @param integer $request_id        Booking Request Id.
     * @param string  $checkout_datetime Checkout Datetime.
     *
     * @return object
     */
    public function saveExpectedCheckoutDatetime(int $request_id, string $checkout_datetime)
    {
        $existing_prive_operation = self::where('booking_request_id', $request_id)->first();

        if (empty($existing_prive_operation) === false) {
            $prive_operation = $existing_prive_operation;
        } else {
            $prive_operation = new self;
            $prive_operation->booking_request_id = $request_id;
        }

        $prive_operation->expected_checkout = $checkout_datetime;

        if ($prive_operation->save() === false) {
            return (object) [];
        }

        return $prive_operation;

    }//end saveExpectedCheckoutDatetime()


}//end class
