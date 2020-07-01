<?php
/**
 * Single point for sending emails
 */

namespace App\Libraries\v1_6;

use App\Jobs\SendEmail;
use App\Libraries\Helper;
use Illuminate\Support\Facades\Queue;
use \Carbon\Carbon;

/**
 * Class EmailService. All email template code is housed in here.
 */
class EmailService
{


    /**
     * Dispatches email on communication queue.
     *
     * @param array $mail Email data.
     *
     * @return void
     */
    public function send(array $mail)
    {
        // Mailable job.
        $job = new SendEmail($mail);
        Queue::pushOn(COMMUNICATION_QUEUE, $job);

    }//end send()


    /**
     * Welcome email for new users.
     *
     * @param string $to_email  Email id of user.
     * @param string $user_name Name of user.
     *
     * @return boolean
     */
    public function sendWelcomeEmail(string $to_email, string $user_name)
    {
        $mailer_name = 'registration_welcome';

        $view_data = [
            'user_name'       => $user_name,
            'tracking_params' => '',
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);

    }//end sendWelcomeEmail()


    /**
     * Welcome email for new users registered via google.
     *
     * @param string $to_email  Email id of user.
     * @param string $user_name Name of user.
     * @param string $password  Password.
     *
     * @return boolean
     */
    public function sendWelcomeGoogleEmail(string $to_email, string $user_name, string $password)
    {
        $mailer_name = 'registration_welcome_google';
        $view_data   = [
            'to_email'        => $to_email,
            'user_name'       => $user_name,
            'password'        => $password,
            'tracking_params' => '',
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendWelcomeGoogleEmail()


    /**
     * Welcome email for new users registered via facebook.
     *
     * @param string $to_email  Email id of user.
     * @param string $user_name Name of user.
     * @param string $password  Password.
     *
     * @return boolean
     */
    public function sendWelcomeFacebookEmail(string $to_email, string $user_name, string $password)
    {
        $mailer_name = 'registration_welcome_fb';
        $view_data   = [
            'to_email'        => $to_email,
            'user_name'       => $user_name,
            'password'        => $password,
            'tracking_params' => '',

        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);

        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);

    }//end sendWelcomeFacebookEmail()


    /**
     * Verification email for new/updating email users.
     *
     * @param string $to_email          Email id of user.
     * @param string $user_name         Name of user.
     * @param string $confirmation_code Mail Verification Code.
     * @param string $verification_link Mail Verification Link.
     *
     * @return boolean
     */
    public function sendVerificationEmail(string $to_email, string $user_name, string $confirmation_code, string $verification_link)
    {
        $mailer_name = 'registration_verify_email';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'user_name'         => $user_name,
            'ucode'             => $confirmation_code,
            'verification_link' => $verification_link,
            'tracking_params'   => '',
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendVerificationEmail()


    /**
     * New Booking Request Email for Host.
     *
     * @param string  $to_email            Email id of user.
     * @param string  $host_name           Host Name.
     * @param string  $traveller_name      Traveller Name.
     * @param string  $property_hash_id    Property Hash Id.
     * @param string  $property_title      Property Title.
     * @param string  $property_image      Property Image.
     * @param string  $request_hash_id     Request Hash Id.
     * @param string  $formatted_amount    Booking Amount.
     * @param integer $guests              Guests.
     * @param string  $formatted_check_in  Checkin Date.
     * @param string  $formatted_check_out Checkout Date.
     * @param string  $accept_hash         Accept Request Hash for Accept Url.
     * @param string  $reject_hash         Reject Request Hash For Reject Url.
     * @param string  $expiry_time         Expiry Time.
     *
     * @return boolean
     */
    public function sendNewRequestEmailToHost(
        string $to_email,
        string $host_name,
        string $traveller_name,
        string $property_hash_id,
        string $property_title,
        string $property_image,
        string $request_hash_id,
        string $formatted_amount,
        int $guests,
        string $formatted_check_in,
        string $formatted_check_out,
        string $accept_hash,
        string $reject_hash,
        string $expiry_time
    ) {
        $mailer_name = 'new_request_email';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'host_name'        => $host_name,
            'traveller_name'   => $traveller_name,
            'property_title'   => $property_title,
            'property_url'     => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
            'property_image'   => $property_image,
            'check_in'         => $formatted_check_in,
            'check_out'        => $formatted_check_out,
            'guests'           => $guests,
            'formatted_amount' => $formatted_amount,
            'request_hash_id'  => $request_hash_id,
            'request_url'      => MAILER_SITE_URL.'/host/booking/'.$request_hash_id,
            'accept_url'       => MAILER_SITE_URL.'/host/booking/'.$request_hash_id,
            'reject_url'       => MAILER_SITE_URL.'/host/booking/'.$request_hash_id,
            'expiry_time'      => $expiry_time,

        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendNewRequestEmailToHost()


    /**
     * Booking Cancel Email to Host.
     *
     * @param string $to_email            Email id of user.
     * @param string $host_name           Host Name.
     * @param string $traveller_name      Traveller Name.
     * @param string $property_hash_id    Property Hash Id.
     * @param string $property_title      Property Title.
     * @param string $property_image      Property Image.
     * @param string $request_hash_id     Request Hash Id.
     * @param string $guests              Guests.
     * @param string $formatted_check_in  Checkin Date.
     * @param string $formatted_check_out Checkout Date.
     *
     * @return boolean
     */
    public function sendCancelBookingRequestEmailToHost(
        string $to_email,
        string $host_name,
        string $traveller_name,
        string $property_hash_id,
        string $property_title,
        string $property_image,
        string $request_hash_id,
        string $guests,
        string $formatted_check_in,
        string $formatted_check_out
    ) {
        $mailer_name = 'cancel_booking_request_to_host';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'host_name'       => $host_name,
            'traveller_name'  => $traveller_name,
            'property_title'  => $property_title,
            'property_url'    => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
            'property_image'  => $property_image,
            'check_in'        => $formatted_check_in,
            'check_out'       => $formatted_check_out,
            'guests'          => $guests,
            'request_hash_id' => $request_hash_id,
            'request_url'     => MAILER_SITE_URL.'/host/booking/'.$request_hash_id,

        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendCancelBookingRequestEmailToHost()


    /**
     * Booking Cancel Email to Guest.
     *
     * @param string $to_email            Email id of user.
     * @param string $traveller_name      Traveller Name.
     * @param string $property_hash_id    Property Hash Id.
     * @param string $property_title      Property Title.
     * @param string $property_image      Property Image.
     * @param string $request_hash_id     Request Hash Id.
     * @param string $guests              Guests.
     * @param string $formatted_check_in  Checkin Date.
     * @param string $formatted_check_out Checkout Date.
     *
     * @return boolean
     */
    public function sendCancelBookingRequestEmailToGuest(
        string $to_email,
        string $traveller_name,
        string $property_hash_id,
        string $property_title,
        string $property_image,
        string $request_hash_id,
        string $guests,
        string $formatted_check_in,
        string $formatted_check_out
    ) {
        $mailer_name = 'cancel_booking_request_to_guest';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'traveller_name'  => $traveller_name,
            'property_title'  => $property_title,
            'property_url'    => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
            'property_image'  => $property_image,
            'check_in'        => $formatted_check_in,
            'check_out'       => $formatted_check_out,
            'guests'          => $guests,
            'request_hash_id' => $request_hash_id,
            'request_url'     => MAILER_SITE_URL.'/user/trip/'.$request_hash_id,

        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendCancelBookingRequestEmailToGuest()


    /**
     * Booking Cancel Email to Customer Support.
     *
     * @param array  $to_email                Emails Array.
     * @param string $request_hash_id         Request Hash Id.
     * @param string $formatted_refund_amount Formatted Refund Amount.
     *
     * @return boolean
     */
    public function sendCancelBookingRequestEmailToCustomerSupport(array $to_email, string $request_hash_id, string $formatted_refund_amount)
    {
        $mailer_name = 'cancel_booking_request_to_customer_support';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'request_hash_id'         => $request_hash_id,
            'formatted_refund_amount' => $formatted_refund_amount,

        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendCancelBookingRequestEmailToCustomerSupport()


    /**
     * Booking Request Approved Email for Traveller.
     *
     * @param string  $to_email            Email id of user.
     * @param string  $traveller_name      Traveller Name.
     * @param string  $property_hash_id    Property Hash Id.
     * @param string  $property_title      Property Title.
     * @param string  $property_image      Property Image.
     * @param string  $request_hash_id     Request Hash Id.
     * @param string  $formatted_amount    Booking Amount.
     * @param integer $guests              Guests.
     * @param integer $units               Units.
     * @param string  $formatted_check_in  Checkin Date.
     * @param string  $formatted_check_out Checkout Date.
     * @param string  $cancel_hash         Cancel Request Hash for Cancel Url.
     * @param string  $expiry_time         Expiry Time.
     *
     * @return boolean
     */
    public function sendBookingRequestApprovedEmailToGuest(
        string $to_email,
        string $traveller_name,
        string $property_hash_id,
        string $property_title,
        string $property_image,
        string $request_hash_id,
        string $formatted_amount,
        int $guests,
        int $units,
        string $formatted_check_in,
        string $formatted_check_out,
        string $cancel_hash,
        string $expiry_time
    ) {
        $mailer_name = 'request_approved_email';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'traveller_name'   => $traveller_name,
            'property_title'   => $property_title,
            // phpcs:ignore
            'property_url'     => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id.'?start_date='.Carbon::parse($formatted_check_in)->format('d-m-Y').'&end_date='.Carbon::parse($formatted_check_out)->format('d-m-Y').'&guests='.$guests.'&units='.$units.'&type=request',
            'property_image'   => $property_image,
            'check_in'         => $formatted_check_in,
            'check_out'        => $formatted_check_out,
            'guests'           => $guests,
            'formatted_amount' => $formatted_amount,
            'request_hash_id'  => $request_hash_id,
            'request_url'      => MAILER_SITE_URL.'/user/request/'.$request_hash_id,
            'payment_url'      => MAILER_SITE_URL.'/user/request/'.$request_hash_id,
            'cancel_url'       => MAILER_SITE_URL.'/user/request/'.$request_hash_id,
            'expiry_time'      => $expiry_time,
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendBookingRequestApprovedEmailToGuest()


    /**
     * Booking Request Rejected Email for Traveller.
     *
     * @param string $to_email         Email id of user.
     * @param string $traveller_name   Traveller Name.
     * @param string $property_hash_id Property Hash Id.
     * @param string $property_title   Property Title.
     * @param array  $similar_property Similar Properties Data.
     * @param string $search_url       Search Url for similar Property.
     *
     * @return boolean
     */
    public function sendBookingRequestRejectedEmailToGuest(string $to_email, string $traveller_name, string $property_hash_id, string $property_title, array $similar_property=[], string $search_url='')
    {
        $mailer_name = 'request_rejected_email';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'traveller_name'   => $traveller_name,
            'property_title'   => $property_title,
            'property_url'     => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id.'?utm_source=booking_mailer&utm_medium=email&utm_campaign=property_suggestions&type=request',
            'similar_property' => $similar_property,
            'search_url'       => $search_url,

        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        if (count($similar_property) >= 3) {
            $mailer['subject'] .= " But here's more.";
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendBookingRequestRejectedEmailToGuest()


    /**
     * Email for adding money for trip review.
     *
     * @param string $to_mail         To mail.
     * @param string $name            Name.
     * @param string $wallet_currency Wallet currency.
     * @param float  $amount_added    Amount added.
     * @param float  $wallet_balance  Wallet balance.
     * @param string $added_on_date   Added date.
     * @param string $subject         Subject of mail.
     * @param string $property_link   Property Link.
     * @param string $property_title  Property Title.
     *
     * @return boolean
     */
    public function sendAddedWalletMoneyForTripReviewEmail(string $to_mail, string $name, string $wallet_currency, float $amount_added, float $wallet_balance, string $added_on_date, string $subject, string $property_link, string $property_title)
    {
        $mailer_name = 'wallet_trip_and_review';

        $view_data = [
            'amount'          => $amount_added,
            'name'            => $name,
            'wallet_currency' => $wallet_currency,
            'date'            => $added_on_date,
            'wallet_balance'  => $wallet_balance,
            'property_link'   => $property_link,
            'property_title'  => $property_title,
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_mail,
            'subject'   => $subject,
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];
        $this->send($mail);
        return true;

    }//end sendAddedWalletMoneyForTripReviewEmail()


    /**
     * Partial Booking Email for Traveller.
     *
     * @param string $to_email            Email id of user.
     * @param string $traveller_name      Traveller Name.
     * @param string $property_hash_id    Property Hash Id.
     * @param string $property_title      Property Title.
     * @param string $property_image      Property Image.
     * @param string $request_hash_id     Request Hash Id.
     * @param string $guests              Guests.
     * @param string $formatted_check_in  Checkin Date.
     * @param string $formatted_check_out Checkout Date.
     * @param string $invoice_url         Invoice Url.
     * @param string $invoice_name        Invoice Name.
     *
     * @return boolean
     */
    public function sendPartialBookingEmailToTraveller(
        string $to_email,
        string $traveller_name,
        string $property_hash_id,
        string $property_title,
        string $property_image,
        string $request_hash_id,
        string $guests,
        string $formatted_check_in,
        string $formatted_check_out,
        string $invoice_url,
        string $invoice_name
    ) {
        $mailer_name = 'partial_booked_guest';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'traveller_name'  => $traveller_name,
            'property_title'  => $property_title,
            'property_url'    => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
            'property_image'  => $property_image,
            'check_in'        => $formatted_check_in,
            'check_out'       => $formatted_check_out,
            'guests'          => $guests,
            'request_hash_id' => $request_hash_id,
            'invoice_url'     => MAILER_ASSETS_URL.'/payment/invoice/'.$request_hash_id.'?type=trip',
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'   => $to_email,
            'subject'    => $mailer['subject'],
            'view'       => $mailer['view'],
            'view_data'  => array_merge($mailer['images'], $view_data),
            'attachment' => [
                'url'  => $invoice_url,
                'name' => $invoice_name,
            ],
        ];

        $this->send($mail);
        return true;

    }//end sendPartialBookingEmailToTraveller()


    /**
     * Fully Booking Email for Traveller.
     *
     * @param string $to_email            Email id of user.
     * @param string $traveller_name      Traveller Name.
     * @param string $property_hash_id    Property Hash Id.
     * @param string $property_title      Property Title.
     * @param string $property_image      Property Image.
     * @param string $request_hash_id     Request Hash Id.
     * @param string $guests              Guests.
     * @param string $formatted_check_in  Checkin Date.
     * @param string $formatted_check_out Checkout Date.
     * @param string $invoice_url         Invoice Url.
     * @param string $invoice_name        Invoice Name.
     *
     * @return boolean
     */
    public function sendFullyBookingEmailToTraveller(
        string $to_email,
        string $traveller_name,
        string $property_hash_id,
        string $property_title,
        string $property_image,
        string $request_hash_id,
        string $guests,
        string $formatted_check_in,
        string $formatted_check_out,
        string $invoice_url,
        string $invoice_name
    ) {
        $mailer_name = 'booked_guest';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'traveller_name'  => $traveller_name,
            'property_title'  => $property_title,
            'property_url'    => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
            'property_image'  => $property_image,
            'check_in'        => $formatted_check_in,
            'check_out'       => $formatted_check_out,
            'guests'          => $guests,
            'request_hash_id' => $request_hash_id,
            'invoice_url'     => MAILER_ASSETS_URL.'/payment/invoice/'.$request_hash_id.'?type=trip',
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'   => $to_email,
            'subject'    => $mailer['subject'],
            'view'       => $mailer['view'],
            'view_data'  => array_merge($mailer['images'], $view_data),
            'attachment' => [
                'url'  => $invoice_url,
                'name' => $invoice_name,
            ],
        ];

        $this->send($mail);
        return true;

    }//end sendFullyBookingEmailToTraveller()


    /**
     * Email for adding money for Referal Bonus.
     *
     * @param string $to_mail         To mail.
     * @param string $name            Name.
     * @param string $wallet_currency Wallet currency.
     * @param float  $amount_added    Amount added.
     * @param float  $wallet_balance  Wallet balance.
     * @param string $added_on_date   Added date.
     * @param string $subject         Subject of mail.
     * @param string $expire_on       Expiry Of wallet Balance.
     *
     * @return boolean
     */
    public function sendAddedWalletMoneyForReferalBonusEmail(string $to_mail, string $name, string $wallet_currency, float $amount_added, float $wallet_balance, string $added_on_date, string $subject, string $expire_on)
    {
        $mailer_name = 'wallet_referal_bonus';

        $view_data = [
            'amount'          => $amount_added,
            'name'            => $name,
            'wallet_currency' => $wallet_currency,
            'date'            => $added_on_date,
            'wallet_balance'  => $wallet_balance,
            'expire_on'       => $expire_on,
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_mail,
            'subject'   => $subject,
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];
         $this->send($mail);
         return true;

    }//end sendAddedWalletMoneyForReferalBonusEmail()


    /**
     * Partial Booking Email for Host.
     *
     * @param string $to_email            Email id of user.
     * @param string $host_name           Host Name.
     * @param string $traveller_name      Traveller Name.
     * @param string $property_hash_id    Property Hash Id.
     * @param string $property_title      Property Title.
     * @param string $property_image      Property Image.
     * @param string $request_hash_id     Request Hash Id.
     * @param string $guests              Guests.
     * @param string $formatted_check_in  Checkin Date.
     * @param string $formatted_check_out Checkout Date.
     * @param string $invoice_url         Invoice Url.
     * @param string $invoice_name        Invoice Name.
     *
     * @return boolean
     */
    public function sendPartialBookingEmailToHost(
        string $to_email,
        string $host_name,
        string $traveller_name,
        string $property_hash_id,
        string $property_title,
        string $property_image,
        string $request_hash_id,
        string $guests,
        string $formatted_check_in,
        string $formatted_check_out,
        string $invoice_url,
        string $invoice_name
    ) {
        $mailer_name = 'partial_booked_host';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'host_name'       => $host_name,
            'traveller_name'  => $traveller_name,
            'property_title'  => $property_title,
            'property_url'    => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
            'property_image'  => $property_image,
            'check_in'        => $formatted_check_in,
            'check_out'       => $formatted_check_out,
            'guests'          => $guests,
            'request_hash_id' => $request_hash_id,
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'   => $to_email,
            'subject'    => $mailer['subject'],
            'view'       => $mailer['view'],
            'view_data'  => array_merge($mailer['images'], $view_data),
            'attachment' => [
                'url'  => $invoice_url,
                'name' => $invoice_name,
            ],
        ];

        $this->send($mail);
        return true;

    }//end sendPartialBookingEmailToHost()


    /**
     * Fully Booking Email for Host.
     *
     * @param string $to_email            Email id of user.
     * @param string $host_name           Host Name.
     * @param string $traveller_name      Traveller Name.
     * @param string $property_hash_id    Property Hash Id.
     * @param string $property_title      Property Title.
     * @param string $property_image      Property Image.
     * @param string $request_hash_id     Request Hash Id.
     * @param string $guests              Guests.
     * @param string $formatted_check_in  Checkin Date.
     * @param string $formatted_check_out Checkout Date.
     * @param string $invoice_url         Invoice Url.
     * @param string $invoice_name        Invoice Name.
     *
     * @return boolean
     */
    public function sendFullyBookingEmailToHost(
        string $to_email,
        string $host_name,
        string $traveller_name,
        string $property_hash_id,
        string $property_title,
        string $property_image,
        string $request_hash_id,
        string $guests,
        string $formatted_check_in,
        string $formatted_check_out,
        string $invoice_url,
        string $invoice_name
    ) {
        $mailer_name = 'booked_host';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'host_name'       => $host_name,
            'traveller_name'  => $traveller_name,
            'property_title'  => $property_title,
            'property_url'    => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
            'property_image'  => $property_image,
            'check_in'        => $formatted_check_in,
            'check_out'       => $formatted_check_out,
            'guests'          => $guests,
            'request_hash_id' => $request_hash_id,
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'   => $to_email,
            'subject'    => $mailer['subject'],
            'view'       => $mailer['view'],
            'view_data'  => array_merge($mailer['images'], $view_data),
            'attachment' => [
                'url'  => $invoice_url,
                'name' => $invoice_name,
            ],
        ];

        $this->send($mail);
        return true;

    }//end sendFullyBookingEmailToHost()


    /**
     * Email for adding money for Friend Referal Bonus.
     *
     * @param string $to_mail         To mail.
     * @param string $name            Name.
     * @param string $wallet_currency Wallet currency.
     * @param float  $amount_added    Amount added.
     * @param float  $wallet_balance  Wallet balance.
     * @param string $added_on_date   Added date.
     * @param string $subject         Subject of mail.
     * @param string $expire_on       Expiry Of wallet Balance.
     * @param string $referal_name    Referal Name.
     *
     * @return boolean
     */
    public function sendAddedWalletMoneyForFriendReferalBonusEmail(
        string $to_mail,
        string $name,
        string $wallet_currency,
        float $amount_added,
        float $wallet_balance,
        string $added_on_date,
        string $subject,
        string $expire_on,
        string $referal_name
    ) {
        $mailer_name = 'wallet_friend_referal_bonus';

        $view_data = [
            'amount'          => $amount_added,
            'name'            => $name,
            'wallet_currency' => $wallet_currency,
            'date'            => $added_on_date,
            'wallet_balance'  => $wallet_balance,
            'expire_on'       => $expire_on,
            'referal_name'    => $referal_name,
        ];
        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_mail,
            'subject'   => $subject,
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];
        $this->send($mail);
        return true;

    }//end sendAddedWalletMoneyForFriendReferalBonusEmail()


    /**
     * Send Booked Payout Email to Host.
     *
     * @param string $to_email         Email id of user.
     * @param string $host_name        Host Name.
     * @param string $property_hash_id Property Hash Id.
     * @param string $property_title   Property Title.
     *
     * @return boolean
     */
    public function sendBookedPayoutDetailToHost(
        string $to_email,
        string $host_name,
        string $property_hash_id,
        string $property_title
    ) {
        $mailer_name = 'booked_payout_information';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'host_name'      => $host_name,
            'property_title' => $property_title,
            'property_url'   => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id.'?type=trip',
            'payout_url'     => MAILER_SITE_URL.'/payout/payouthistory#add?type=trip',
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendBookedPayoutDetailToHost()


    /**
     * Email for adding money for First Booking Bonus.
     *
     * @param string $to_mail         To mail.
     * @param string $name            Name.
     * @param string $wallet_currency Wallet currency.
     * @param float  $amount_added    Amount added.
     * @param float  $wallet_balance  Wallet balance.
     * @param string $added_on_date   Added date.
     * @param string $subject         Subject of mail.
     * @param string $expire_on       Expiry Of wallet Balance.
     * @param string $referal_name    Referal Name.
     *
     * @return boolean
     */
    public function sendAddedWalletMoneyForFirstBookingBonusEmail(string $to_mail, string $name, string $wallet_currency, float $amount_added, float $wallet_balance, string $added_on_date, string $subject, string $expire_on, string $referal_name)
    {
        $mailer_name = 'wallet_referal_first_booking_bonus';

        $view_data = [
            'amount'          => $amount_added,
            'name'            => $name,
            'wallet_currency' => $wallet_currency,
            'date'            => $added_on_date,
            'wallet_balance'  => $wallet_balance,
            'expire_on'       => $expire_on,
            'referal_name'    => $referal_name,
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_mail,
            'subject'   => $subject,
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];
        $this->send($mail);
        return true;

    }//end sendAddedWalletMoneyForFirstBookingBonusEmail()


    /**
     * Send Booking Email to Admin.
     *
     * @param array   $to_email          Email id of user.
     * @param string  $traveller_name    Traveller Name.
     * @param string  $property_hash_id  Property Hash Id.
     * @param string  $request_hash_id   Request Hash Id.
     * @param string  $traveller_hash_id Traveller Hash Id.
     * @param string  $payable_amount    Payable Amount.
     * @param integer $units             Units.
     * @param string  $check_in          Check In.
     * @param string  $check_out         Check Out.
     *
     * @return boolean
     */
    public function sendBookingEmailToAdmin(
        array $to_email,
        string $traveller_name,
        string $property_hash_id,
        string $request_hash_id,
        string $traveller_hash_id,
        string $payable_amount,
        int $units,
        string $check_in,
        string $check_out
    ) {
        $mailer_name = 'booked_admin';
        // phpcs:ignore
        // TODO: Change how to create url.
        $view_data = [
            'request_hash_id'   => $request_hash_id,
            'property_hash_id'  => $property_hash_id,
            'traveller_hash_id' => $traveller_hash_id,
            'traveller_name'    => $traveller_name,
            'payable_amount'    => $payable_amount,
            'units'             => $units,
            'check_in'          => $check_in,
            'check_out'         => $check_out,
        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendBookingEmailToAdmin()


    /**
     * Email for adding money for First Booking Bonus.
     *
     * @param string $to_mail         To mail.
     * @param string $name            Name.
     * @param string $wallet_currency Wallet currency.
     * @param float  $amount_added    Amount added.
     * @param float  $wallet_balance  Wallet balance.
     * @param string $added_on_date   Added date.
     * @param string $subject         Subject of mail.
     * @param string $property_title  Property title.
     * @param string $property_link   Property Link.
     *
     * @return boolean
     */
    public function sendUpdateWalletMoneyForApplyWalletEmail(string $to_mail, string $name, string $wallet_currency, float $amount_added, float $wallet_balance, string $added_on_date, string $subject, string $property_title, string $property_link)
    {
        $mailer_name = 'apply_wallet_money';
        $mailer      = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        // Mail template params.
        $view_data = [
            'amount'          => $amount_added,
            'name'            => $name,
            'wallet_currency' => $wallet_currency,
            'date'            => $added_on_date,
            'wallet_balance'  => $wallet_balance,
            'property_title'  => $property_title,
            'property_link'   => $property_link,
        ];
        $mail      = [
            'to_email'  => $to_mail,
            'subject'   => $subject,
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendUpdateWalletMoneyForApplyWalletEmail()


     /**
      * Send sendProperlySupportEmail.
      *
      * @param string $subject Subject.
      * @param string $message Message.
      *
      * @return boolean
      */
    public function sendProperlySupportEmail(string $subject, string $message)
    {
        $mailer_name = 'support_job';
        $mailer      = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        // Mail template params.
        $view_data = ['message' => $message];
        $mail      = [
            'to_email'  => CONTACT_PROPERLY_EMAIL,
            'subject'   => $subject,
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];
        $this->send($mail);
        return true;

    }//end sendProperlySupportEmail()


    /**
     * Email for Property Listing Review to Host.
     *
     * @param string $to_mail          To mail.
     * @param string $property_title   Property Name.
     * @param string $property_type    Property Type.
     * @param string $property_hash_id Property Hash Id.
     *
     * @return boolean
     */
    public function sendPropertyListingUnderReviewEmailToHost(string $to_mail, string $property_title, string $property_type, string $property_hash_id)
    {
        $mailer_name = 'property_listing_review';

        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        // Mail template params.
        $view_data = [
            'title'            => $property_title,
            'property_type'    => $property_type,
            'property_hash_id' => $property_hash_id,
            'hide_sign'        => 1,
        ];

        $mail = [
            'to_email'  => $to_mail,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendPropertyListingUnderReviewEmailToHost()


    /**
     * Email for sending payment link.
     *
     * @param string $to_mail          To mail.
     * @param string $request_hash_id  Request hash id.
     * @param string $traveller_name   Traveller Name.
     * @param string $link_expire_time Link Expire time in hrs.
     * @param string $payment_link     Payment Link.
     *
     * @return boolean
     */
    public function sendPaymentLinkEmail(string $to_mail, string $request_hash_id, string $traveller_name, string $link_expire_time, string $payment_link)
    {
        $mailer_name = 'properly_payment_link';

        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        // Mail template params.
        $view_data = [
            'request_hash_id'  => $request_hash_id,
            'traveller_name'   => $traveller_name,
            'link_expire_time' => $link_expire_time,
            'payment_link'     => $payment_link,
        ];

        $mail = [
            'to_email'  => $to_mail,
            'subject'   => $mailer['subject'].$request_hash_id,
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendPaymentLinkEmail()


    /**
     * Email for reset password.
     *
     * @param string $to_mail    To mail.
     * @param string $reset_link Reset Password Link.
     * @param string $role       Role Of User.
     *
     * @return boolean
     */
    public function sendResetPasswordLinkEmail(string $to_mail, string $reset_link, string $role)
    {
        if ($role === 'prive') {
            $reset_link  = PROPERLY_RESET_PASSWORD_LINK.$reset_link;
            $mailer_name = 'properly_reset_password';
            $mailer      = Helper::getMailer($mailer_name);
        } else {
            $reset_link  = RESET_PASSWORD_LINK.$reset_link;
            $mailer_name = 'reset_password';
            $mailer      = Helper::getMailer($mailer_name);
        }

        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        // Mail template params.
        $view_data = ['reset_link' => $reset_link];

        $mail = [
            'to_email'  => $to_mail,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendResetPasswordLinkEmail()


    /**
     * Send OTP in Email, to reset password.
     *
     * @param string $to_mail Email id.
     * @param string $otp     OTP Code.
     * @param string $role    Role of User.
     *
     * @return boolean
     */
    public function sendResetPasswordOtpEmail(string $to_mail, string $otp, string $role)
    {
        if ($role === 'prive') {
            $mailer_name = 'properly_reset_password';
            $mailer      = Helper::getMailer($mailer_name);
        } else {
            $mailer_name = 'reset_password';
            $mailer      = Helper::getMailer($mailer_name);
        }

        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        // Mail template params.
        $view_data = ['otp' => $otp];

        $mail = [
            'to_email'  => $to_mail,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);

        return true;

    }//end sendResetPasswordOtpEmail()


    /**
     * Email for Property Listing Review to BD Team.
     *
     * @param array  $to_mails         To mails.
     * @param string $property_title   Property Name.
     * @param string $property_hash_id Property Hash Id.
     *
     * @return boolean
     */
    public function sendPropertyListingUnderReviewToBDTeamEmail(array $to_mails, string $property_title, string $property_hash_id)
    {
        $mailer_name = 'property_listing_submit_to_bd_team';
        $mailer      = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        // Mail template params.
        $view_data = [
            'title'            => $property_title,
            'property_hash_id' => $property_hash_id,
            'hide_sign'        => 1,
        ];

        $mail = [
            'to_email'  => $to_mails,
            'subject'   => $mailer['subject'].$property_hash_id,
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendPropertyListingUnderReviewToBDTeamEmail()


    /**
     * Email for Property Listing Modification to BD Team.
     *
     * @param array  $to_mails         To mails.
     * @param string $property_title   Property Name.
     * @param string $property_hash_id Property Hash Id.
     * @param array  $updates_data     Property Updates Details.
     *
     * @return boolean
     */
    public function sendPropertyListingModifyToBDTeamEmail(array $to_mails, string $property_title, string $property_hash_id, array $updates_data)
    {
        $mailer_name = 'property_listing_modification_submit_to_bd_team';
        $mailer      = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        // Mail template params.
        $view_data = [
            'title'            => $property_title,
            'property_hash_id' => $property_hash_id,
            'updates_data'     => $updates_data,
        ];

        $mail = [
            'to_email'  => $to_mails,
            'subject'   => $mailer['subject'].$property_hash_id,
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);
        return true;

    }//end sendPropertyListingModifyToBDTeamEmail()


    /**
     * Welcome email for new users registered via Apple Id.
     *
     * @param string $to_email  Email id of user.
     * @param string $user_name Name of user.
     * @param string $password  Password.
     *
     * @return boolean
     */
    public function sendWelcomeAppleEmail(string $to_email, string $user_name, string $password)
    {
        $mailer_name = 'registration_welcome_apple';
        $view_data   = [
            'to_email'        => $to_email,
            'user_name'       => $user_name,
            'password'        => $password,
            'tracking_params' => '',

        ];

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);

        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
            return false;
        }

        $mail = [
            'to_email'  => $to_email,
            'subject'   => $mailer['subject'],
            'view'      => $mailer['view'],
            'view_data' => array_merge($mailer['images'], $view_data),
        ];

        $this->send($mail);

    }//end sendWelcomeAppleEmail()


}//end class
