<?php
/**
 * EmailServiceTest containing tests for EmailService
 */

use App\Libraries\Helper;
use Carbon\Carbon;

use App\Libraries\v1_6\EmailService;
use App\Jobs\SendEmail;
use App\Mail\GenericMailable;
use Illuminate\Support\Facades\Queue;

/**
 * Class EmailServiceTest
 *
 * @group Services
 */
class EmailServiceTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Setup.
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();
        // Email service mock, Use this to call functions.
        $this->mocked_email_service = $this->mock(EmailService::class);
        Queue::fake();

    }//end setup()


    /**
     * Test send function that actually pushes job to queue
     *
     * @return void
     */
    public function test_send_function()
    {
        $user      = $this->createUser();
        $user_name = $user->name.' '.$user->last_name;

        $this->mocked_email_service->sendWelcomeEmail($user->email, $user_name);

        Queue::assertPushedOn(
            COMMUNICATION_QUEUE,
            SendEmail::class,
            function ($job) use ($user) {
                    $message_val = $job->mail;
                    $user_name   = $user->name.' '.$user->last_name;

                    return ($message_val['view_data']['user_name'] === $user_name
                    && $message_val['view_data']['welcome_bg'] === MAILER_ASSETS_URL.'welcome_email_bg.jpg'
                    && $message_val['view_data']['logo_bordered'] === MAILER_ASSETS_URL.'logo_bordered.png'
                    && $message_val['view_data']['tracking_params'] === ''
                    && $message_val['subject'] === 'Registration Success!!!'
                    && $message_val['to_email'] === $user->email
                    && $message_val['view'] === 'emails.registration.welcome'
                    );
            }
        );

    }//end test_send_function()


    /**
     * Test for welcome email sent when registration source is website.
     *
     * @return void
     */
    public function test_send_welcome_email()
    {
        $user      = $this->createUser();
        $user_name = $user->name.' '.$user->last_name;

        $mail = [
            'to_email'  => $user->email,
            'subject'   => 'Registration Success!!!',
            'view'      => 'emails.registration.welcome',
            'view_data' => [
                'welcome_bg'      => MAILER_ASSETS_URL.'welcome_email_bg.jpg',
                'logo_bordered'   => MAILER_ASSETS_URL.'logo_bordered.png',
                'user_name'       => $user_name,
                'tracking_params' => '',
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');
        $this->mocked_email_service->sendWelcomeEmail($user->email, $user_name);

    }//end test_send_welcome_email()


    /**
     * Test for verification email sent when registration source is website.
     *
     * @return void
     */
    public function test_send_verification_email()
    {
        $user      = $this->createUser();
        $user_name = $user->name.' '.$user->last_name;

        $mail              = [
            'to_email'  => $user->email,
            'subject'   => 'GuestHouser Email Verification',
            'view'      => 'emails.registration.verifymail',
            'view_data' => [
                'password_reset_bg'  => MAILER_ASSETS_URL.'password_reset_background.png',
                'mail_verify_footer' => MAILER_ASSETS_URL.'mail_verify_footer.png',
                'logo'               => MAILER_ASSETS_URL.'logo.png',
                'user_name'          => $user_name,
                'ucode'              => $user->confirmation_code,
                'verification_link'  => MAILER_SITE_URL.'/user/mailverify/?ucode='.$user->confirmation_code,
                'tracking_params'    => '',
            ],
        ];
        $verification_link = MAILER_SITE_URL.'/user/mailverify/?ucode='.$user->confirmation_code;

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');
        $this->mocked_email_service->sendVerificationEmail($user->email, $user_name, $user->confirmation_code, $verification_link);

    }//end test_send_verification_email()


    /**
     * Test for welcome email sent when registration source is google.
     *
     * @return void
     */
    public function test_send_welcome_email_google()
    {
        $user      = $this->createUser();
        $user_name = $user->name.' '.$user->last_name;

        $mail = [
            'to_email'  => $user->email,
            'subject'   => 'Registration Success!!!',
            'view'      => 'emails.registration.register_google',
            'view_data' => [
                'welcome_bg'      => MAILER_ASSETS_URL.'welcome_email_bg.jpg',
                'logo_bordered'   => MAILER_ASSETS_URL.'logo_bordered.png',
                'to_email'        => $user->email,
                'user_name'       => $user_name,
                'password'        => '111111',
                'tracking_params' => '',
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');
        $this->mocked_email_service->sendWelcomeGoogleEmail($user->email, $user_name, '111111');

    }//end test_send_welcome_email_google()


    /**
     * Test for welcome email sent when registration source is facebook.
     *
     * @return void
     */
    public function test_send_welcome_email_facebook()
    {
        $user      = $this->createUser();
        $user_name = $user->name.' '.$user->last_name;

        $mail = [
            'to_email'  => $user->email,
            'subject'   => 'Registration Success!!!',
            'view'      => 'emails.registration.register_fb',
            'view_data' => [
                'welcome_bg'      => MAILER_ASSETS_URL.'welcome_email_bg.jpg',
                'logo_bordered'   => MAILER_ASSETS_URL.'logo_bordered.png',
                'to_email'        => $user->email,
                'user_name'       => $user_name,
                'password'        => '111111',
                'tracking_params' => '',
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');
        $this->mocked_email_service->sendWelcomeFacebookEmail($user->email, $user_name, '111111');

    }//end test_send_welcome_email_facebook()


    /**
     * Test for create request email sent to host.
     *
     * @return void
     */
    public function test_send_new_request_email_to_host()
    {
        $create_booking_request = $this->createBookingRequests();

        $to_email            = $create_booking_request['host']->email;
        $host_name           = $create_booking_request['host']->getUserFullName();
        $traveller_name      = $create_booking_request['traveller']->getUserFullName();
        $property_title      = $create_booking_request['properties']->title;
        $property_hash_id    = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_image      = MAILER_SITE_URL.'images/property-placeholder.png';
        $request_hash_id     = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');
        $guests              = $create_booking_request['booking_request']->guests;
        $formatted_amount    = Helper::getFormattedMoney(13424.49, 'INR');
        $accept_hash         = str_random(20);
        $reject_hash         = str_random(20);
        $expiry_time         = Helper::stringTimeFormattedString(strtotime($create_booking_request['booking_request']->approve_till) - strtotime($create_booking_request['booking_request']->created_at));

        $mail = [
            'to_email'  => $to_email,
            'subject'   => 'New Booking Request',
            'view'      => 'emails.booking.new_request',
            'view_data' => [
                'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                'new_request_image' => MAILER_ASSETS_URL.'new_request_graphic.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png',
                'host_name'         => $host_name,
                'traveller_name'    => $traveller_name,
                'property_title'    => $property_title,
                'property_url'      => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
                'property_image'    => $property_image,
                'check_in'          => $formatted_check_in,
                'check_out'         => $formatted_check_out,
                'guests'            => $guests,
                'formatted_amount'  => $formatted_amount,
                'request_hash_id'   => $request_hash_id,
                'request_url'       => MAILER_SITE_URL.'/host/booking/'.$request_hash_id,
                'accept_url'        => MAILER_SITE_URL.'/host/booking/'.$request_hash_id,
                'reject_url'        => MAILER_SITE_URL.'/host/booking/'.$request_hash_id,
                'expiry_time'       => $expiry_time,
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');

        $this->mocked_email_service->sendNewRequestEmailToHost(
            $to_email,
            $host_name,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $formatted_amount,
            $guests,
            $formatted_check_in,
            $formatted_check_out,
            $accept_hash,
            $reject_hash,
            $expiry_time
        );

    }//end test_send_new_request_email_to_host()


    /**
     * Test for cancel booking email sent to host.
     *
     * @return void
     */
    public function test_send_cancel_booking_request_email_to_host()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $to_email            = $create_booking_request['host']->email;
        $host_name           = $create_booking_request['host']->getUserFullName();
        $traveller_name      = $create_booking_request['traveller']->getUserFullName();
        $property_title      = $create_booking_request['properties']->title;
        $property_hash_id    = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_image      = MAILER_SITE_URL.'images/property-placeholder.png';
        $request_hash_id     = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');
        $guests              = $create_booking_request['booking_request']->guests;

        $mail = [
            'to_email'  => $to_email,
            'subject'   => 'Booking Cancelled',
            'view'      => 'emails.booking.booking_cancelled_host',
            'view_data' => [
                'logo'                 => MAILER_ASSETS_URL.'site_logo_2x.png',
                'cancel_request_image' => MAILER_ASSETS_URL.'cancelled_graphic.png',
                'social_image'         => MAILER_ASSETS_URL.'socials.png',
                'host_name'            => $host_name,
                'traveller_name'       => $traveller_name,
                'property_title'       => $property_title,
                'property_url'         => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
                'property_image'       => $property_image,
                'check_in'             => $formatted_check_in,
                'check_out'            => $formatted_check_out,
                'guests'               => $guests,
                'request_hash_id'      => $request_hash_id,
                'request_url'          => MAILER_SITE_URL.'/host/booking/'.$request_hash_id,
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');

        $this->mocked_email_service->sendCancelBookingRequestEmailToHost(
            $to_email,
            $host_name,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $guests,
            $formatted_check_in,
            $formatted_check_out
        );

    }//end test_send_cancel_booking_request_email_to_host()


    /**
     * Test for cancel booking email sent to guest.
     *
     * @return void
     */
    public function test_send_cancel_booking_request_email_to_guest()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $to_email            = $create_booking_request['host']->email;
        $traveller_name      = $create_booking_request['traveller']->getUserFullName();
        $property_title      = $create_booking_request['properties']->title;
        $property_hash_id    = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_image      = MAILER_SITE_URL.'images/property-placeholder.png';
        $request_hash_id     = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');
        $guests              = $create_booking_request['booking_request']->guests;

        $mail = [
            'to_email'  => $to_email,
            'subject'   => 'Booking Cancellation Confirmation',
            'view'      => 'emails.booking.booking_cancelled_guest',
            'view_data' => [
                'logo'                 => MAILER_ASSETS_URL.'site_logo_2x.png',
                'cancel_request_image' => MAILER_ASSETS_URL.'cancelled_graphic.png',
                'social_image'         => MAILER_ASSETS_URL.'socials.png',
                'traveller_name'       => $traveller_name,
                'property_title'       => $property_title,
                'property_url'         => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
                'property_image'       => $property_image,
                'check_in'             => $formatted_check_in,
                'check_out'            => $formatted_check_out,
                'guests'               => $guests,
                'request_hash_id'      => $request_hash_id,
                'request_url'          => MAILER_SITE_URL.'/user/trip/'.$request_hash_id,
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');

        $this->mocked_email_service->sendCancelBookingRequestEmailToGuest(
            $to_email,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $guests,
            $formatted_check_in,
            $formatted_check_out
        );

    }//end test_send_cancel_booking_request_email_to_guest()


    /**
     * Test for cancel booking email sent to Customer Support.
     *
     * @return void
     */
    public function test_send_cancel_booking_request_email_to_customer_support()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $request_hash_id         = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $formatted_refund_amount = Helper::getFormattedMoney(16912.00, 'INR');
        $mail                    = [
            'to_email'  => ADMIN_EMAILS_FOR_NOTIFICATIONS,
            'subject'   => 'Booking Cancellation After Payment',
            'view'      => 'emails.booking.booking_cancelation_notify_cust_sup',
            'view_data' => [
                'request_hash_id'         => $request_hash_id,
                'formatted_refund_amount' => $formatted_refund_amount,
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');

        $this->mocked_email_service->sendCancelBookingRequestEmailToCustomerSupport(ADMIN_EMAILS_FOR_NOTIFICATIONS, $request_hash_id, $formatted_refund_amount);

    }//end test_send_cancel_booking_request_email_to_customer_support()


    /**
     * Test for approved request email sent to guest.
     *
     * @return void
     */
    public function test_send_booking_request_approved_email_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();

        $to_email            = $create_booking_request['traveller']->email;
        $traveller_name      = $create_booking_request['traveller']->getUserFullName();
        $property_title      = $create_booking_request['properties']->title;
        $property_hash_id    = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_image      = MAILER_SITE_URL.'images/property-placeholder.png';
        $request_hash_id     = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');
        $guests              = $create_booking_request['booking_request']->guests;
        $units               = $create_booking_request['booking_request']->units;
        $formatted_amount    = Helper::getFormattedMoney(13424.49, 'INR');
        $cancel_hash         = str_random(20);
        $expiry_time         = Helper::stringTimeFormattedString(REQUEST_APPROVAL_DAY_TIMER);

        $mail = [
            'to_email'  => $to_email,
            'subject'   => 'Booking Request Approved',
            'view'      => 'emails.booking.request_approved',
            'view_data' => [
                'logo'                   => MAILER_ASSETS_URL.'site_logo_2x.png',
                'approved_request_image' => MAILER_ASSETS_URL.'booking_approved_graphic.png',
                'social_image'           => MAILER_ASSETS_URL.'socials.png',
                'traveller_name'         => $traveller_name,
                'property_title'         => $property_title,
                // phpcs:ignore
                'property_url'           => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id.'?start_date='.Carbon::parse($formatted_check_in)->format('d-m-Y').'&end_date='.Carbon::parse($formatted_check_out)->format('d-m-Y').'&guests='.$guests.'&units='.$units.'&type=request',
                'property_image'         => $property_image,
                'check_in'               => $formatted_check_in,
                'check_out'              => $formatted_check_out,
                'guests'                 => $guests,
                'formatted_amount'       => $formatted_amount,
                'request_hash_id'        => $request_hash_id,
                'request_url'            => MAILER_SITE_URL.'/user/request/'.$request_hash_id,
                'payment_url'            => MAILER_SITE_URL.'/user/request/'.$request_hash_id,
                'cancel_url'             => MAILER_SITE_URL.'/user/request/'.$request_hash_id,
                'expiry_time'            => $expiry_time,
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');

        $this->mocked_email_service->sendBookingRequestApprovedEmailToGuest(
            $to_email,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $formatted_amount,
            $guests,
            $units,
            $formatted_check_in,
            $formatted_check_out,
            $cancel_hash,
            $expiry_time
        );

    }//end test_send_booking_request_approved_email_to_guest()


    /**
     * Test for rejected request email sent to guest.
     *
     * @return void
     */
    public function test_send_booking_request_rejected_email_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();

        $to_email         = $create_booking_request['traveller']->email;
        $traveller_name   = $create_booking_request['traveller']->getUserFullName();
        $property_title   = $create_booking_request['properties']->title;
        $property_hash_id = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $similar_property = [];
        $search_url       = '';

        $mail = [
            'to_email'  => $to_email,
            'subject'   => 'Sorry, your requested property is unavailable.',
            'view'      => 'emails.booking.request_rejected',
            'view_data' => [
                'logo'                 => MAILER_ASSETS_URL.'site_logo_2x.png',
                'cancel_request_image' => MAILER_ASSETS_URL.'cancelled_graphic.png',
                'social_image'         => MAILER_ASSETS_URL.'socials.png',
                'traveller_name'       => $traveller_name,
                'property_title'       => $property_title,
                'property_url'         => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id.'?utm_source=booking_mailer&utm_medium=email&utm_campaign=property_suggestions&type=request',
                'similar_property'     => $similar_property,
                'search_url'           => $search_url,
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');

        $this->mocked_email_service->sendBookingRequestRejectedEmailToGuest(
            $to_email,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $similar_property,
            $search_url
        );

    }//end test_send_booking_request_rejected_email_to_guest()


    /**
     * Test for create partial Booking email sent to Traveller.
     *
     * @return void
     */
    public function test_send_partial_booking_email_to_traveller()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $to_email            = $create_booking_request['traveller']->email;
        $traveller_name      = $create_booking_request['traveller']->getUserFullName();
        $property_title      = $create_booking_request['properties']->title;
        $property_hash_id    = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_image      = MAILER_SITE_URL.'images/property-placeholder.png';
        $request_hash_id     = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');
        $guests              = $create_booking_request['booking_request']->guests;
        $invoice_name        = 'T_'.$request_hash_id.'.pdf';
        $invoice_url         = INVOICE_PDF_DIR.$invoice_name;

        $mail = [
            'to_email'   => $to_email,
            'subject'    => 'Booking Completed',
            'view'       => 'emails.booking.partial_booked_guest',
            'view_data'  => [
                'logo'                    => MAILER_ASSETS_URL.'site_logo_2x.png',
                'booking_confirmed_image' => MAILER_ASSETS_URL.'booking_confirmed_traveller_graphic.png',
                'social_image'            => MAILER_ASSETS_URL.'socials.png',
                'traveller_name'          => $traveller_name,
                'property_title'          => $property_title,
                'property_url'            => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
                'property_image'          => $property_image,
                'check_in'                => $formatted_check_in,
                'check_out'               => $formatted_check_out,
                'guests'                  => $guests,
                'request_hash_id'         => $request_hash_id,
                'invoice_url'             => MAILER_ASSETS_URL.'/payment/invoice/'.$request_hash_id.'?type=trip',
            ],
            'attachment' => [
                'url'  => $invoice_url,
                'name' => $invoice_name,
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');

        $this->mocked_email_service->sendPartialBookingEmailToTraveller(
            $to_email,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $guests,
            $formatted_check_in,
            $formatted_check_out,
            $invoice_url,
            $invoice_name
        );

    }//end test_send_partial_booking_email_to_traveller()


    /**
     * Test for create fully Booking email sent to Traveller.
     *
     * @return void
     */
    public function test_send_fully_booking_email_to_traveller()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $to_email            = $create_booking_request['traveller']->email;
        $traveller_name      = $create_booking_request['traveller']->getUserFullName();
        $property_title      = $create_booking_request['properties']->title;
        $property_hash_id    = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_image      = MAILER_SITE_URL.'images/property-placeholder.png';
        $request_hash_id     = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');
        $guests              = $create_booking_request['booking_request']->guests;

        $invoice_name = 'T_'.$request_hash_id.'.pdf';
        $invoice_url  = INVOICE_PDF_DIR.$invoice_name;

        $mail = [
            'to_email'   => $to_email,
            'subject'    => 'Booking Completed',
            'view'       => 'emails.booking.booked_guest',
            'view_data'  => [
                'logo'                    => MAILER_ASSETS_URL.'site_logo_2x.png',
                'booking_confirmed_image' => MAILER_ASSETS_URL.'booking_confirmed_traveller_graphic.png',
                'social_image'            => MAILER_ASSETS_URL.'socials.png',
                'traveller_name'          => $traveller_name,
                'property_title'          => $property_title,
                'property_url'            => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
                'property_image'          => $property_image,
                'check_in'                => $formatted_check_in,
                'check_out'               => $formatted_check_out,
                'guests'                  => $guests,
                'request_hash_id'         => $request_hash_id,
                'invoice_url'             => MAILER_ASSETS_URL.'/payment/invoice/'.$request_hash_id.'?type=trip',
            ],
            'attachment' => [
                'url'  => $invoice_url,
                'name' => $invoice_name,
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');

        $this->mocked_email_service->sendFullyBookingEmailToTraveller(
            $to_email,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $guests,
            $formatted_check_in,
            $formatted_check_out,
            $invoice_url,
            $invoice_name
        );

    }//end test_send_fully_booking_email_to_traveller()


    /**
     * Test for create partial Booking email sent to Host.
     *
     * @return void
     */
    public function test_send_partial_booking_email_to_host()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $to_email            = $create_booking_request['host']->email;
        $host_name           = $create_booking_request['host']->getUserFullName();
        $traveller_name      = $create_booking_request['traveller']->getUserFullName();
        $property_title      = $create_booking_request['properties']->title;
        $property_hash_id    = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_image      = MAILER_SITE_URL.'images/property-placeholder.png';
        $request_hash_id     = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');
        $guests              = $create_booking_request['booking_request']->guests;
        $invoice_name        = 'H_'.$request_hash_id.'.pdf';
        $invoice_url         = INVOICE_PDF_DIR.$invoice_name;

        $mail = [
            'to_email'   => $to_email,
            'subject'    => 'New Booking',
            'view'       => 'emails.booking.partial_booked_host',
            'view_data'  => [
                'logo'                    => MAILER_ASSETS_URL.'site_logo_2x.png',
                'booking_confirmed_image' => MAILER_ASSETS_URL.'booking_confirmed_traveller_graphic.png',
                'social_image'            => MAILER_ASSETS_URL.'socials.png',
                'host_name'               => $host_name,
                'traveller_name'          => $traveller_name,
                'property_title'          => $property_title,
                'property_url'            => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
                'property_image'          => $property_image,
                'check_in'                => $formatted_check_in,
                'check_out'               => $formatted_check_out,
                'guests'                  => $guests,
                'request_hash_id'         => $request_hash_id,
            ],
            'attachment' => [
                'url'  => $invoice_url,
                'name' => $invoice_name,
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');

        $this->mocked_email_service->sendPartialBookingEmailToHost(
            $to_email,
            $traveller_name,
            $host_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $guests,
            $formatted_check_in,
            $formatted_check_out,
            $invoice_url,
            $invoice_name
        );

    }//end test_send_partial_booking_email_to_host()


    /**
     * Test for create fully Booking email sent to Host.
     *
     * @return void
     */
    public function test_send_fully_booking_email_to_host()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $to_email            = $create_booking_request['host']->email;
        $host_name           = $create_booking_request['host']->getUserFullName();
        $traveller_name      = $create_booking_request['traveller']->getUserFullName();
        $property_title      = $create_booking_request['properties']->title;
        $property_hash_id    = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_image      = MAILER_SITE_URL.'images/property-placeholder.png';
        $request_hash_id     = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');
        $guests              = $create_booking_request['booking_request']->guests;

        $invoice_name = 'H_'.$request_hash_id.'.pdf';
        $invoice_url  = INVOICE_PDF_DIR.$invoice_name;

        $mail = [
            'to_email'   => $to_email,
            'subject'    => 'New Booking',
            'view'       => 'emails.booking.booked_host',
            'view_data'  => [
                'logo'                    => MAILER_ASSETS_URL.'site_logo_2x.png',
                'booking_confirmed_image' => MAILER_ASSETS_URL.'booking_confirmed_traveller_graphic.png',
                'social_image'            => MAILER_ASSETS_URL.'socials.png',
                'host_name'               => $host_name,
                'traveller_name'          => $traveller_name,
                'property_title'          => $property_title,
                'property_url'            => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id,
                'property_image'          => $property_image,
                'check_in'                => $formatted_check_in,
                'check_out'               => $formatted_check_out,
                'guests'                  => $guests,
                'request_hash_id'         => $request_hash_id,
            ],
            'attachment' => [
                'url'  => $invoice_url,
                'name' => $invoice_name,
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');

        $this->mocked_email_service->sendFullyBookingEmailToHost(
            $to_email,
            $host_name,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $guests,
            $formatted_check_in,
            $formatted_check_out,
            $invoice_url,
            $invoice_name
        );

    }//end test_send_fully_booking_email_to_host()


    /**
     * Test for Booked Payout Deatil email to Host.
     *
     * @return void
     */
    public function test_send_booked_payout_detail_to_host()
    {
        $to_email         = 'testing.new.api'.strtolower(str_random(4)).time().'@guesthouser.com';
        $host_name        = 'Unit Testing';
        $property_title   = 'Vila';
        $property_hash_id = Helper::encodePropertyId(12345);

        $mail = [
            'to_email'  => $to_email,
            'subject'   => 'Payout Details',
            'view'      => 'emails.booking.booked_payout_information',
            'view_data' => [
                'logo'                    => MAILER_ASSETS_URL.'site_logo_2x.png',
                'booking_confirmed_image' => MAILER_ASSETS_URL.'booking_confirmed_traveller_graphic.png',
                'social_image'            => MAILER_ASSETS_URL.'socials.png',
                'host_name'               => $host_name,
                'property_title'          => $property_title,
                'property_url'            => MAILER_SITE_URL.'/properties/rooms/'.$property_hash_id.'?type=trip',
                'payout_url'              => MAILER_SITE_URL.'/payout/payouthistory#add?type=trip',
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');

        $this->mocked_email_service->sendBookedPayoutDetailToHost(
            $to_email,
            $host_name,
            $property_hash_id,
            $property_title
        );

    }//end test_send_booked_payout_detail_to_host()


    /**
     * Test for Booking email to Admin.
     *
     * @return void
     */
    public function test_send_booking_email_to_admin()
    {
        $to_email          = ['testing.new.api'.str_random(4).time().'@guesthouser.com'];
        $traveller_name    = 'Unit Testing';
        $property_hash_id  = 'PABCDE';
        $request_hash_id   = 'ABCDEF';
        $traveller_hash_id = 'UABCDE';
        $payable_amount    = 'Rs12345';
        $units             = 1;
        $check_in          = '3 Mar 2019';
        $check_out         = '4 Mar 2019';

        $mail = [
            'to_email'  => $to_email,
            'subject'   => 'New Booking',
            'view'      => 'emails.booking.booked_admin',
            'view_data' => [
                'request_hash_id'   => $request_hash_id,
                'property_hash_id'  => $property_hash_id,
                'traveller_hash_id' => $traveller_hash_id,
                'traveller_name'    => $traveller_name,
                'payable_amount'    => $payable_amount,
                'units'             => $units,
                'check_in'          => $check_in,
                'check_out'         => $check_out,
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');

        $this->mocked_email_service->sendBookingEmailToAdmin(
            $to_email,
            $traveller_name,
            $property_hash_id,
            $request_hash_id,
            $traveller_hash_id,
            $payable_amount,
            $units,
            $check_in,
            $check_out
        );

    }//end test_send_booking_email_to_admin()


    /**
     * Test send mail.
     *
     * @return void
     */
    public function test_function_send_added_wallet_money_for_trip_review_email_should_call_sendemail()
    {
        $booking  = $this->createBookingRequests(BOOKED);
        $user     = $booking['traveller'];
        $property = $booking['properties'];

        $to_mail = $user->email;
        $name    = $user->name;

        $currency       = DEFAULT_CURRENCY;
        $amount         = 1000;
        $wallet_balance = 1000;
        $added_on_date  = Carbon::now('Asia/Kolkata')->format('d-m-Y');

         // Mail_subject.
        $subject = "{$currency} {$amount} added in your wallet";

        // Mail_message.
        $property_link  = MAILER_SITE_URL.'/properties/rooms/'.$property->id;
        $property_title = $property->title;

        // View Data.
        $view_data = [
            'amount'          => $amount,
            'name'            => $name,
            'wallet_currency' => $currency,
            'date'            => $added_on_date,
            'wallet_balance'  => $wallet_balance,
            'property_link'   => $property_link,
            'property_title'  => $property_title,
            'logo'            => MAILER_ASSETS_URL.'site_logo_2x.png',
            'social_image'    => MAILER_ASSETS_URL.'socials.png',
            'wallet_image'    => MAILER_ASSETS_URL.'wallet.png',
        ];
         $mail     = [
             'to_email'  => $to_mail,
             'subject'   => $subject,
             'view'      => 'emails.wallet.trip_and_review_mailer',
             'view_data' => $view_data,
         ];

         $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');
         $this->mocked_email_service->sendAddedWalletMoneyForTripReviewEmail($to_mail, $name, $currency, $amount, $wallet_balance, $added_on_date, $subject, $property_link, $property_title);

    }//end test_function_send_added_wallet_money_for_trip_review_email_should_call_sendemail()


    /**
     * Test send mail.
     *
     * @return void
     */
    public function test_function_send_added_wallet_money_for_referal_email_should_call_sendemail()
    {
        $user = $this->createUser();

        $to_mail = $user->email;
        $name    = $user->name;

        $currency       = DEFAULT_CURRENCY;
        $amount         = 1000;
        $wallet_balance = 1000;
        $added_on_date  = Carbon::now('Asia/Kolkata')->format('d-m-Y');
        $expire_on      = Carbon::now('GMT')->addMonths(6)->toDateTimeString();

         // Mail_subject.
        $subject = "{$currency} {$amount} added in your GuestHouser wallet";

        // View Data.
        $view_data = [
            'amount'          => $amount,
            'name'            => $name,
            'wallet_currency' => $currency,
            'date'            => $added_on_date,
            'wallet_balance'  => $wallet_balance,
            'expire_on'       => $expire_on,
            'logo'            => MAILER_ASSETS_URL.'site_logo_2x.png',
            'social_image'    => MAILER_ASSETS_URL.'socials.png',
            'wallet_image'    => MAILER_ASSETS_URL.'wallet.png',
        ];
         $mail     = [
             'to_email'  => $to_mail,
             'subject'   => $subject,
             'view'      => 'emails.wallet.referal_bonus_mailer',
             'view_data' => $view_data,
         ];

         $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');
         $this->mocked_email_service->sendAddedWalletMoneyForReferalBonusEmail($to_mail, $name, $currency, $amount, $wallet_balance, $added_on_date, $subject, $expire_on);

    }//end test_function_send_added_wallet_money_for_referal_email_should_call_sendemail()


     /**
      * Test send mail for friend referal user.
      *
      * @return void
      */
    public function test_function_send_added_wallet_money_for_friend_referal_email_should_call_sendemail()
    {
        $user = $this->createUser();

        $to_mail = $user->email;
        $name    = $user->name;

        $currency          = DEFAULT_CURRENCY;
        $amount            = 1000;
        $wallet_balance    = 1000;
        $added_on_date     = Carbon::now('Asia/Kolkata')->format('d-m-Y');
        $expire_on         = Carbon::now('GMT')->addMonths(6)->toDateTimeString();
        $referal_user_name = str_random(8);

        $subject = "{$currency} {$amount} added in your GuestHouser wallet";

        // View Data.
        $view_data = [
            'amount'          => $amount,
            'name'            => $name,
            'wallet_currency' => $currency,
            'date'            => $added_on_date,
            'wallet_balance'  => $wallet_balance,
            'expire_on'       => $expire_on,
            'referal_name'    => $referal_user_name,
            'logo'            => MAILER_ASSETS_URL.'site_logo_2x.png',
            'social_image'    => MAILER_ASSETS_URL.'socials.png',
            'wallet_image'    => MAILER_ASSETS_URL.'wallet.png',
        ];
         $mail     = [
             'to_email'  => $to_mail,
             'subject'   => $subject,
             'view'      => 'emails.wallet.friend_referal_bonus_mailer',
             'view_data' => $view_data,
         ];

         $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');
         $this->mocked_email_service->sendAddedWalletMoneyForFriendReferalBonusEmail($to_mail, $name, $currency, $amount, $wallet_balance, $added_on_date, $subject, $expire_on, $referal_user_name);

    }//end test_function_send_added_wallet_money_for_friend_referal_email_should_call_sendemail()


     /**
      * Test send mail for friend referal user.
      *
      * @return void
      */
    public function test_function_send_added_wallet_money_for_first_booking_email_should_call_sendemail()
    {
        $user = $this->createUser();

        $to_mail = $user->email;
        $name    = $user->name;

        $currency          = DEFAULT_CURRENCY;
        $amount            = 1000;
        $wallet_balance    = 1000;
        $added_on_date     = Carbon::now('Asia/Kolkata')->format('d-m-Y');
        $expire_on         = Carbon::now('GMT')->addMonths(6)->toDateTimeString();
        $referal_user_name = str_random(8);

        $subject = "{$currency} {$amount} added in your GuestHouser wallet";

        // View Data.
        $view_data = [
            'amount'          => $amount,
            'name'            => $name,
            'wallet_currency' => $currency,
            'date'            => $added_on_date,
            'wallet_balance'  => $wallet_balance,
            'expire_on'       => $expire_on,
            'referal_name'    => $referal_user_name,
            'logo'            => MAILER_ASSETS_URL.'site_logo_2x.png',
            'social_image'    => MAILER_ASSETS_URL.'socials.png',
            'wallet_image'    => MAILER_ASSETS_URL.'wallet.png',
        ];
         $mail     = [
             'to_email'  => $to_mail,
             'subject'   => $subject,
             'view'      => 'emails.wallet.referal_first_booking_mailer',
             'view_data' => $view_data,
         ];

         $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');
         $this->mocked_email_service->sendAddedWalletMoneyForFirstBookingBonusEmail($to_mail, $name, $currency, $amount, $wallet_balance, $added_on_date, $subject, $expire_on, $referal_user_name);

    }//end test_function_send_added_wallet_money_for_first_booking_email_should_call_sendemail()


    /**
     * Test send mail for Apply Wallet.
     *
     * @return void
     */
    public function test_function_send_update_wallet_money_for_apply_wallet_email_should_call_sendemail()
    {
        $booking        = $this->createBookingRequests(BOOKED);
        $user           = $booking['traveller'];
        $property       = $booking['properties'];
        $property_link  = MAILER_SITE_URL.'/properties/rooms/'.$property->id;
        $property_title = $property->title;

        $to_mail = $user->email;
        $name    = $user->name;

        $currency       = DEFAULT_CURRENCY;
        $amount         = 1000;
        $wallet_balance = 1000;
        $added_on_date  = Carbon::now('Asia/Kolkata')->format('d-m-Y');
         // Mail_subject.
        $subject = "{$currency} {$amount} deducted from your wallet";

        // View Data.
        $view_data = [
            'amount'          => $amount,
            'name'            => $name,
            'wallet_currency' => $currency,
            'date'            => $added_on_date,
            'wallet_balance'  => $wallet_balance,
            'property_title'  => $property_title,
            'property_link'   => $property_link,
            'logo'            => MAILER_ASSETS_URL.'site_logo_2x.png',
            'social_image'    => MAILER_ASSETS_URL.'socials.png',
            'wallet_image'    => MAILER_ASSETS_URL.'wallet.png',
        ];
         $mail     = [
             'to_email'  => $to_mail,
             'subject'   => $subject,
             'view'      => 'emails.wallet.apply_wallet_money_mailer',
             'view_data' => $view_data,
         ];

         $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');
         $this->mocked_email_service->sendUpdateWalletMoneyForApplyWalletEmail($to_mail, $name, $currency, $amount, $wallet_balance, $added_on_date, $subject, $property_title, $property_link);

    }//end test_function_send_update_wallet_money_for_apply_wallet_email_should_call_sendemail()


     /**
      * Test reset password email.
      *
      * @return void
      */
    public function test_function_send_reset_password_link_email()
    {
        $user       = $this->createUser();
        $reset_link = strtolower(str_random(40));
        $role       = '';

        $mail = [
            'to_email'  => $user->email,
            'subject'   => 'Password Reset',
            'view'      => 'emails.auth.reset_password',
            'view_data' => [
                'reset_link'       => RESET_PASSWORD_LINK.$reset_link,
                'logo'             => MAILER_ASSETS_URL.'logo.png',
                'background_image' => MAILER_ASSETS_URL.'background.png',
                'footer'           => MAILER_ASSETS_URL.'reset-footer.png',
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');
        $this->mocked_email_service->sendResetPasswordLinkEmail($user->email, $reset_link, $role);

    }//end test_function_send_reset_password_link_email()


     /**
      * Test Properly Support email.
      *
      * @return void
      */
    public function test_function_properly_support_email()
    {
         $subject = 'Unit Testing Apis';
         $message = 'Unit Testing Apis';

        $mail = [
            'to_email'  => CONTACT_PROPERLY_EMAIL,
            'subject'   => $subject,
            'view'      => 'emails.prive.support',
            'view_data' => [
                'message' => $message,
                'support' => MAILER_ASSETS_URL.'support.png',
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');
        $this->mocked_email_service->sendProperlySupportEmail($subject, $message);

    }//end test_function_properly_support_email()


    /**
     * Test for welcome email sent when registration source is apple.
     *
     * @return void
     */
    public function test_send_welcome_email_apple()
    {
        $user      = $this->createUser();
        $user_name = $user->name.' '.$user->last_name;

        $mail = [
            'to_email'  => $user->email,
            'subject'   => 'Registration Success!!!',
            'view'      => 'emails.registration.register_apple',
            'view_data' => [
                'welcome_bg'      => MAILER_ASSETS_URL.'welcome_email_bg.jpg',
                'logo_bordered'   => MAILER_ASSETS_URL.'logo_bordered.png',
                'to_email'        => $user->email,
                'user_name'       => $user_name,
                'password'        => '111111',
                'tracking_params' => '',
            ],
        ];

        $this->mocked_email_service->shouldReceive('send')->with($mail)->once()->andReturn('');
        $this->mocked_email_service->sendWelcomeAppleEmail($user->email, $user_name, '111111');

    }//end test_send_welcome_email_apple()


}//end class
