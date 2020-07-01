<?php
/**
 * BookingRequestServiceTest containing tests for BookingRequestService
 */

use App\Libraries\Helper;
use App\Libraries\v1_6\{EmailService, SmsService , PushNotificationService};
use App\Libraries\v1_6\{BookingRequestService, SimilarListingService};
use Carbon\Carbon;
use App\Models\{PropertyImage, BookingRequest};

/**
 * Class BookingRequestServiceTest
 *
 * @group Services
 */
class BookingRequestServiceTest extends TestCase
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

        $this->mocked_email_service        = $this->mock(EmailService::class);
        $this->mocked_sms_service          = $this->mock(SmsService::class);
        $this->mocked_notification_service = $this->mock(PushNotificationService::class);
        $this->mocked_booking_request      = $this->mock(BookingRequest::class);
        $this->booking_request_service     = new BookingRequestService($this->mocked_email_service, $this->mocked_sms_service, $this->mocked_notification_service, $this->mocked_booking_request);

    }//end setup()


    /**
     * Tear down.
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->clearmock();

    }//end tearDown()


    /**
     * Test for emails sent when user create request via website.
     *
     * @return void
     */
    public function test_send_request_create_email_to_host()
    {
        $create_booking_request = $this->createBookingRequests();

        $to_email         = $create_booking_request['host']->email;
        $host_name        = $create_booking_request['host']->getUserFullName();
        $traveller_name   = $create_booking_request['traveller']->getUserFullName();
        $property_hash_id = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_title   = $create_booking_request['properties']->title;

         // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds([$create_booking_request['booking_request']->pid], [], 1);

        $property_image = (array_key_exists($create_booking_request['booking_request']->pid, $property_images) === true) ? $property_images[$create_booking_request['booking_request']->pid][0]['image'] : MAILER_ASSETS_URL.'default_property.png';

        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);

        $price_details = json_decode($create_booking_request['booking_request']->price_details);

        // Calculate Amount.
        $formatted_amount = Helper::getFormattedMoney($this->booking_request_service->getHostAmount($create_booking_request['booking_request']), $price_details->currency_code);

        $guests              = $create_booking_request['booking_request']->guests;
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');

        // Accept String for Accept Url request_id,property_id,traveller id,action = 1 for accept request.
        $accept_hash = Helper::encodeArray([$create_booking_request['booking_request']->id, $create_booking_request['booking_request']->pid, $create_booking_request['booking_request']->traveller_id, 1]);

        // Accept String for Accept Url request_id,property_id,traveller id,action = 0 for reject request.
        $reject_hash = Helper::encodeArray([$create_booking_request['booking_request']->id, $create_booking_request['booking_request']->pid, $create_booking_request['booking_request']->traveller_id, 0]);

        $expiry_time = Helper::stringTimeFormattedString(strtotime($create_booking_request['booking_request']->approve_till) - strtotime($create_booking_request['booking_request']->created_at));

        $this->mocked_email_service->shouldReceive('sendNewRequestEmailToHost')->once()->with(
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

        $this->booking_request_service->sendNewRequestEmailToHost(
            $create_booking_request['booking_request'],
            $create_booking_request['properties']->title,
            $create_booking_request['host']->email,
            $create_booking_request['host']->getUserFullName(),
            $create_booking_request['traveller']->getUserFullName()
        );

    }//end test_send_request_create_email_to_host()


    /**
     * Test for emails sent when user cancel booking
     *
     * @return void
     */
    public function test_send_cancel_booking_request_emails()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $host_name        = $create_booking_request['host']->getUserFullName();
        $traveller_name   = $create_booking_request['traveller']->getUserFullName();
        $property_hash_id = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_title   = $create_booking_request['properties']->title;

         // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds([$create_booking_request['booking_request']->pid], [], 1);

        $property_image = (array_key_exists($create_booking_request['booking_request']->pid, $property_images) === true) ? $property_images[$create_booking_request['booking_request']->pid][0]['image'] : MAILER_ASSETS_URL.'default_property.png';

        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);

        $guests              = $create_booking_request['booking_request']->guests;
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');

        $refund_amount           = 16912.00;
        $formatted_refund_amount = Helper::getFormattedMoney(16912.00, 'INR');

        $this->mocked_email_service->shouldReceive('sendCancelBookingRequestEmailToHost')->once()->with(
            $create_booking_request['host']->email,
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

        $this->mocked_email_service->shouldReceive('sendCancelBookingRequestEmailToGuest')->once()->with(
            $create_booking_request['traveller']->email,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $guests,
            $formatted_check_in,
            $formatted_check_out
        );

        $this->mocked_email_service->shouldReceive('sendCancelBookingRequestEmailToCustomerSupport')->once()->with(ADMIN_EMAILS_FOR_NOTIFICATIONS, $request_hash_id, $formatted_refund_amount);

        $this->booking_request_service->sendCancelBookingRequestEmails(
            $create_booking_request['booking_request'],
            $create_booking_request['properties']->title,
            $create_booking_request['host']->email,
            $create_booking_request['host']->getUserFullName(),
            $create_booking_request['traveller']->email,
            $create_booking_request['traveller']->getUserFullName(),
            $refund_amount
        );

    }//end test_send_cancel_booking_request_emails()


    /**
     * Test for Sms On New Request Created.
     *
     * @return void
     */
    public function test_new_request_sms_to_host()
    {
        $create_booking_request = $this->createBookingRequests();
        $property_title         = Helper::shortPropertyTitle($create_booking_request['properties']->title);
        $host_dial_code         = $create_booking_request['host']->dial_code;
        $host_contact           = $create_booking_request['host']->contact;

        $request_hash_id     = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $guests              = $create_booking_request['booking_request']->guests;
        $units               = $create_booking_request['booking_request']->units;
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');
        // Calculate Amount.
        $formatted_amount = Helper::getFormattedMoney($this->booking_request_service->getHostAmount($create_booking_request['booking_request']), 'INR', true, false);
        // Expiry Time.
        $expiry_time = Helper::stringTimeFormattedString(strtotime($create_booking_request['booking_request']->approve_till) - strtotime($create_booking_request['booking_request']->created_at));

        $this->mocked_sms_service->shouldReceive('sendCreateNewRequestSmsToHost')->once()->with(
            $host_dial_code,
            $host_contact,
            $request_hash_id,
            $property_title,
            $formatted_check_in,
            $formatted_check_out,
            $guests,
            $units,
            $formatted_amount,
            $expiry_time
        );

        $this->booking_request_service->sendNewRequestSmsToHost($create_booking_request['booking_request'], $property_title, $host_dial_code, $host_contact);

    }//end test_new_request_sms_to_host()


    /**
     * Test for Sms On Booking Cancel.
     *
     * @return void
     */
    public function test_cancel_booking_request_sms()
    {
        $create_booking_request = $this->createBookingRequests();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $property_title         = Helper::shortPropertyTitle($create_booking_request['properties']->title);
        $host_dial_code         = $create_booking_request['host']->dial_code;
        $host_contact           = $create_booking_request['host']->contact;
        $traveller_dial_code    = $create_booking_request['traveller']->dial_code;
        $traveller_contact      = $create_booking_request['traveller']->contact;

        $this->mocked_sms_service->shouldReceive('sendCancelBookingSmsToHost')->with($host_dial_code, $host_contact, $request_hash_id, $property_title);

        $this->mocked_sms_service->shouldReceive('sendCancelBookingSmsToGuest')->with($traveller_dial_code, $traveller_contact, $request_hash_id, $property_title);

        $this->booking_request_service->sendCancelBookingRequestSms($create_booking_request['booking_request'], $property_title, $host_dial_code, $host_contact, $traveller_dial_code, $traveller_contact);

    }//end test_cancel_booking_request_sms()


    /**
     * Test for emails sent when user request approved by host.
     *
     * @return void
     */
    public function test_send_booking_request_approved_email_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();

        $to_email         = $create_booking_request['traveller']->email;
        $traveller_name   = $create_booking_request['traveller']->getUserFullName();
        $property_hash_id = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_title   = $create_booking_request['properties']->title;

         // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds([$create_booking_request['booking_request']->pid], [], 1);

        $property_image = (array_key_exists($create_booking_request['booking_request']->pid, $property_images) === true) ? $property_images[$create_booking_request['booking_request']->pid][0]['image'] : MAILER_ASSETS_URL.'default_property.png';

        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);

        $price_details = json_decode($create_booking_request['booking_request']->price_details);

        // Calculate Amount.
        $formatted_amount = Helper::getFormattedMoney($price_details->payable_amount, $price_details->currency_code);

        $guests              = $create_booking_request['booking_request']->guests;
        $units               = $create_booking_request['booking_request']->units;
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');

        // Cancel String for Cancel Url request_id,property_id,traveller id,action = 0 for cancel request.
        $cancel_hash = Helper::encodeArray([$create_booking_request['booking_request']->id, $create_booking_request['booking_request']->pid, $create_booking_request['booking_request']->traveller_id, 0]);

        $expiry_time = Helper::stringTimeFormattedString(REQUEST_APPROVAL_DAY_TIMER);

        $this->mocked_email_service->shouldReceive('sendBookingRequestApprovedEmailToGuest')->once()->with(
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

        $this->booking_request_service->sendBookingRequestApprovedEmailToGuest(
            $create_booking_request['booking_request'],
            $create_booking_request['properties']->title,
            $create_booking_request['traveller']->email,
            $create_booking_request['traveller']->getUserFullName()
        );

    }//end test_send_booking_request_approved_email_to_guest()


    /**
     * Test for Sms On Request Approved.
     *
     * @return void
     */
    public function test_send_booking_request_approved_sms_to_guest()
    {
        $this->mocked_service_helper = $this->mock('alias:App\Libraries\Helper');
        $this->mocked_service_helper->shouldReceive('generateReferralCode')->andReturn('');
        $this->mocked_service_helper->shouldReceive('encodeBookingRequestId')->andReturn('ASDFGH');
        $this->mocked_service_helper->shouldReceive('shortPropertyTitle')->andReturn('');
        $this->mocked_service_helper->shouldReceive('stringTimeFormattedString')->andReturn('');
        $create_booking_request = $this->createBookingRequests();
        $property_title         = Helper::shortPropertyTitle($create_booking_request['properties']->title);
        $traveller_dial_code    = $create_booking_request['traveller']->dial_code;
        $traveller_contact      = $create_booking_request['traveller']->contact;

        $request_hash_id = 'ASDFGH';

        // Expiry Time.
        $expiry_time = '';

        // Generate Payment Url.
        $payment_url = 'https://g-h.app/Qxh6uC5dQZvn1tXb7';
        $this->mocked_service_helper->shouldReceive('getFirebaseShortUrl')->andReturn('https://g-h.app/Qxh6uC5dQZvn1tXb7');

        $this->mocked_sms_service->shouldReceive('sendBookingRequestApprovedSmsToGuest')->once()->with(
            $traveller_dial_code,
            $traveller_contact,
            $request_hash_id,
            $property_title,
            $expiry_time,
            $payment_url
        );

        $this->booking_request_service->sendBookingRequestApprovedSmsToGuest($create_booking_request['booking_request'], $property_title, $traveller_dial_code, $traveller_contact);

    }//end test_send_booking_request_approved_sms_to_guest()


    /**
     * Test for emails sent when user request rejected by host.
     *
     * @return void
     */
    public function test_send_booking_request_rejected_email_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();

        $to_email         = $create_booking_request['traveller']->email;
        $traveller_name   = $create_booking_request['traveller']->getUserFullName();
        $property_hash_id = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_title   = $create_booking_request['properties']->title;

        $price_details = json_decode($create_booking_request['booking_request']->price_details);

        $similar_properties = SimilarListingService::getSimilarProperties(
            [
                'property_id'    => $create_booking_request['properties']->id,
                'start_date'     => $create_booking_request['booking_request']->from_date,
                'end_date'       => $create_booking_request['booking_request']->to_date,
                'days'           => 1,
                'guests'         => 1,
                'units'          => 1,
                'currency'       => DEFAULT_CURRENCY,
                'offset'         => 0,
                'limit'          => 3,
                'latitude'       => $create_booking_request['properties']->latitude,
                'longitude'      => $create_booking_request['properties']->longitude,
                'state'          => $create_booking_request['properties']->state,
                'country'        => $create_booking_request['properties']->country,
                'property_type'  => $create_booking_request['properties']->property_type,
                'payable_amount' => $price_details->payable_amount,
                'headers'        => [],
                'user_id'        => $create_booking_request['traveller']->id,
            ]
        );

        $min_price = round((85 / 100) * $price_details->payable_amount);
        $max_price = round((115 / 100) * $price_details->payable_amount);

        // phpcs:ignore
        $search_url = MAILER_SITE_URL.'/search/s?location='.$create_booking_request['properties']->state.', India&state='.$create_booking_request['properties']->state.'&country='.$create_booking_request['properties']->country.'&minvalue='.$min_price.'&maxvalue='.$max_price.'&checkin='.$create_booking_request['booking_request']->from_date.'&checkout='.$create_booking_request['booking_request']->to_date.'&guests='.(1).'&property_type='.$create_booking_request['properties']->property_type.'&utm_source=booking_mailer&utm_medium=email&utm_campaign=search_suggestions';

        $this->mocked_email_service->shouldReceive('sendBookingRequestRejectedEmailToGuest')->once()->with(
            $to_email,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $similar_properties,
            $search_url
        );

        $this->booking_request_service->sendBookingRequestRejectedEmailToGuest(
            $create_booking_request['booking_request'],
            $create_booking_request['properties']->title,
            $create_booking_request['traveller']->email,
            $create_booking_request['traveller']->getUserFullName()
        );

    }//end test_send_booking_request_rejected_email_to_guest()


    /**
     * Test for Sms On Request Rejected.
     *
     * @return void
     */
    public function test_send_booking_request_rejected_sms_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();
        $property_title         = Helper::shortPropertyTitle($create_booking_request['properties']->title);
        $traveller_dial_code    = $create_booking_request['traveller']->dial_code;
        $traveller_contact      = $create_booking_request['traveller']->contact;

        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);

        $this->mocked_sms_service->shouldReceive('sendBookingRequestRejectedSmsToGuest')->once()->with(
            $traveller_dial_code,
            $traveller_contact,
            $request_hash_id,
            $property_title
        );

        $this->booking_request_service->sendBookingRequestRejectedSmsToGuest($create_booking_request['booking_request'], $property_title, $traveller_dial_code, $traveller_contact);

    }//end test_send_booking_request_rejected_sms_to_guest()


    /**
     * Test for emails sent when user create partial payment booking.
     *
     * @return void
     */
    public function test_send_partial_booking_email_to_traveller()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $to_email         = $create_booking_request['traveller']->email;
        $traveller_name   = $create_booking_request['traveller']->getUserFullName();
        $property_hash_id = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_title   = $create_booking_request['properties']->title;

         // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds([$create_booking_request['booking_request']->pid], [], 1);

        $property_image = (array_key_exists($create_booking_request['booking_request']->pid, $property_images) === true) ? $property_images[$create_booking_request['booking_request']->pid][0]['image'] : MAILER_ASSETS_URL.'default_property.png';

        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);

        $guests              = $create_booking_request['booking_request']->guests;
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');

        $invoice_name = 'T_'.$request_hash_id.'.pdf';
        $invoice_url  = INVOICE_PDF_DIR.$invoice_name;

        $this->mocked_email_service->shouldReceive('sendPartialBookingEmailToTraveller')->once()->with(
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

        $this->booking_request_service->sendBookingEmailToTraveller(
            $create_booking_request['booking_request'],
            0.00,
            // Balance Fee.
            $create_booking_request['properties']->title,
            $create_booking_request['traveller']->email,
            $create_booking_request['traveller']->getUserFullName(),
            true
        );

    }//end test_send_partial_booking_email_to_traveller()


    /**
     * Test for emails sent when user create fully payment booking.
     *
     * @return void
     */
    public function test_send_fully_booking_email_to_traveller()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $to_email         = $create_booking_request['traveller']->email;
        $traveller_name   = $create_booking_request['traveller']->getUserFullName();
        $property_hash_id = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_title   = $create_booking_request['properties']->title;

         // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds([$create_booking_request['booking_request']->pid], [], 1);

        $property_image = (array_key_exists($create_booking_request['booking_request']->pid, $property_images) === true) ? $property_images[$create_booking_request['booking_request']->pid][0]['image'] : MAILER_ASSETS_URL.'default_property.png';

        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);

        $guests              = $create_booking_request['booking_request']->guests;
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');

        $invoice_name = 'T_'.$request_hash_id.'.pdf';
        $invoice_url  = INVOICE_PDF_DIR.$invoice_name;

        $this->mocked_email_service->shouldReceive('sendFullyBookingEmailToTraveller')->once()->with(
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

        $this->booking_request_service->sendBookingEmailToTraveller(
            $create_booking_request['booking_request'],
            0.00,
            // Balance Fee.
            $create_booking_request['properties']->title,
            $create_booking_request['traveller']->email,
            $create_booking_request['traveller']->getUserFullName()
        );

    }//end test_send_fully_booking_email_to_traveller()


        /**
         * Test for emails sent when user create partial payment booking.
         *
         * @return void
         */
    public function test_send_partial_booking_email_to_host()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $to_email         = $create_booking_request['host']->email;
        $host_name        = $create_booking_request['host']->getUserFullName();
        $traveller_name   = $create_booking_request['traveller']->getUserFullName();
        $property_hash_id = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_title   = $create_booking_request['properties']->title;

         // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds([$create_booking_request['booking_request']->pid], [], 1);

        $property_image = (array_key_exists($create_booking_request['booking_request']->pid, $property_images) === true) ? $property_images[$create_booking_request['booking_request']->pid][0]['image'] : MAILER_ASSETS_URL.'default_property.png';

        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);

        $guests              = $create_booking_request['booking_request']->guests;
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');

        $invoice_name = 'H_'.$request_hash_id.'.pdf';
        $invoice_url  = INVOICE_PDF_DIR.$invoice_name;

        $this->mocked_email_service->shouldReceive('sendPartialBookingEmailToHost')->once()->with(
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

        $this->booking_request_service->sendBookingEmailToHost(
            $create_booking_request['booking_request'],
            0.00,
            // Balance Fee.
            $property_title,
            $create_booking_request['host']->email,
            $host_name,
            $traveller_name,
            true
        );

    }//end test_send_partial_booking_email_to_host()


    /**
     * Test for emails sent when user create fully payment booking.
     *
     * @return void
     */
    public function test_send_fully_booking_email_to_host()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $to_email         = $create_booking_request['host']->email;
        $host_name        = $create_booking_request['host']->getUserFullName();
        $traveller_name   = $create_booking_request['traveller']->getUserFullName();
        $property_hash_id = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $property_title   = $create_booking_request['properties']->title;

         // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds([$create_booking_request['booking_request']->pid], [], 1);

        $property_image = (array_key_exists($create_booking_request['booking_request']->pid, $property_images) === true) ? $property_images[$create_booking_request['booking_request']->pid][0]['image'] : MAILER_ASSETS_URL.'default_property.png';

        $request_hash_id = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);

        $guests              = $create_booking_request['booking_request']->guests;
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');

        $invoice_name = 'H_'.$request_hash_id.'.pdf';
        $invoice_url  = INVOICE_PDF_DIR.$invoice_name;

        $this->mocked_email_service->shouldReceive('sendFullyBookingEmailToHost')->once()->with(
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

        $this->booking_request_service->sendBookingEmailToHost(
            $create_booking_request['booking_request'],
            0.00,
            // Balance Fee.
            $property_title,
            $create_booking_request['host']->email,
            $host_name,
            $traveller_name
        );

    }//end test_send_fully_booking_email_to_host()


    /**
     * Test for emails sent to add payout detail.
     *
     * @return void
     */
    public function test_send_add_payout_detail_email_to_host()
    {
        $to_email         = 'testing.new.api'.str_random(4).time().'@guesthouser.com';
        $host_name        = 'Unit Testing';
        $property_hash_id = Helper::encodePropertyId(12345);
        $property_title   = 'Villa';

        $this->mocked_email_service->shouldReceive('sendBookedPayoutDetailToHost')->once()->with(
            $to_email,
            $host_name,
            $property_hash_id,
            $property_title
        );

        $this->booking_request_service->sendAddPayoutDetailEmailToHost(
            12345,
            $property_title,
            12345,
            $to_email,
            $host_name
        );

    }//end test_send_add_payout_detail_email_to_host()


    /**
     * Test for emails sent to admin on booking.
     *
     * @return void
     */
    public function test_send_booking_email_to_admin()
    {
        $create_booking_request = $this->createBookingRequests(BOOKED);

        $to_email          = ADMIN_EMAILS_FOR_NOTIFICATIONS;
        $traveller_name    = $create_booking_request['traveller']->getUserFullName();
        $property_hash_id  = Helper::encodePropertyId($create_booking_request['booking_request']->pid);
        $request_hash_id   = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $traveller_hash_id = Helper::encodeUserId($create_booking_request['traveller']->id);

        $units               = $create_booking_request['booking_request']->units;
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');

        $price_details    = json_decode($create_booking_request['booking_request']->price_details);
        $formatted_amount = Helper::getFormattedMoney($price_details->payable_amount, $price_details->currency_code);

        $this->mocked_email_service->shouldReceive('sendBookingEmailToAdmin')->once()->with(
            $to_email,
            $traveller_name,
            $property_hash_id,
            $request_hash_id,
            $traveller_hash_id,
            $formatted_amount,
            $units,
            $formatted_check_in,
            $formatted_check_out
        );

        $this->booking_request_service->sendBookingEmailToAdmin(
            $create_booking_request['booking_request'],
            $traveller_name
        );

    }//end test_send_booking_email_to_admin()


    /**
     * Test for Sms On Booking to Traveller.
     *
     * @return void
     */
    public function test_send_partial_booking_sms_to_traveller()
    {
        $create_booking_request = $this->createBookingRequests();
        $property_title         = Helper::shortPropertyTitle($create_booking_request['properties']->title);
        $traveller_dial_code    = $create_booking_request['traveller']->dial_code;
        $traveller_contact      = $create_booking_request['traveller']->contact;
        $traveller_name         = $create_booking_request['traveller']->getUserFullName();
        $host_name              = $create_booking_request['host']->getUserFullName();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);

        $price_details    = json_decode($create_booking_request['booking_request']->price_details);
        $formatted_amount = Helper::getFormattedMoney($price_details->payable_amount, $price_details->currency_code, true, false);

        $this->mocked_sms_service->shouldReceive('sendPartialBookingSmsToGuest')->once()->with(
            $traveller_dial_code,
            $traveller_contact,
            $request_hash_id,
            $formatted_amount
        );

        $this->booking_request_service->sendBookingSmsToTraveller($create_booking_request['booking_request'], 0.00, $property_title, $traveller_dial_code, $traveller_contact, $traveller_name, $host_name, true);

    }//end test_send_partial_booking_sms_to_traveller()


    /**
     * Test for Sms On Booking to Traveller.
     *
     * @return void
     */
    public function test_send_booking_sms_to_traveller()
    {
        $create_booking_request = $this->createBookingRequests();
        $property_title         = Helper::shortPropertyTitle($create_booking_request['properties']->title);
        $traveller_dial_code    = $create_booking_request['traveller']->dial_code;
        $traveller_contact      = $create_booking_request['traveller']->contact;
        $traveller_name         = $create_booking_request['traveller']->getUserFullName();
        $host_name              = $create_booking_request['host']->getUserFullName();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);

        $price_details       = json_decode($create_booking_request['booking_request']->price_details);
        $formatted_amount    = Helper::getFormattedMoney($price_details->payable_amount, $price_details->currency_code, true, false);
        $guests              = $create_booking_request['booking_request']->guests;
        $units               = $create_booking_request['booking_request']->units;
        $formatted_check_in  = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');

        $this->mocked_sms_service->shouldReceive('sendBookingSmsToGuest')->once()->with(
            $traveller_dial_code,
            $traveller_contact,
            $request_hash_id,
            $formatted_amount,
            $traveller_name,
            $host_name,
            $property_title,
            $formatted_check_in,
            $formatted_check_out,
            $guests,
            $units
        );

        $this->booking_request_service->sendBookingSmsToTraveller($create_booking_request['booking_request'], 0.00, $property_title, $traveller_dial_code, $traveller_contact, $traveller_name, $host_name);

    }//end test_send_booking_sms_to_traveller()


    /**
     * Test for Sms On Booking to Host.
     *
     * @return void
     */
    public function test_send_booking_sms_to_host()
    {
        $create_booking_request = $this->createBookingRequests();
        $host_dial_code         = $create_booking_request['host']->dial_code;
        $host_contact           = $create_booking_request['host']->contact;
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $guests                 = $create_booking_request['booking_request']->guests;
        $units                  = $create_booking_request['booking_request']->units;
        $formatted_check_in     = Carbon::parse($create_booking_request['booking_request']->from_date)->format('d-M-Y');
        $formatted_check_out    = Carbon::parse($create_booking_request['booking_request']->to_date)->format('d-M-Y');

        $this->mocked_sms_service->shouldReceive('sendBookingSmsToHost')->once()->with(
            $host_dial_code,
            $host_contact,
            $request_hash_id,
            $formatted_check_in,
            $formatted_check_out,
            $guests,
            $units
        );

        $this->booking_request_service->sendBookingSmsToHost($create_booking_request['booking_request'], $host_dial_code, $host_contact);

    }//end test_send_booking_sms_to_host()


    /**
     * Test for Sms for Property Direction to Guest.
     *
     * @return void
     */
    public function test_send_property_direction_sms_to_guest()
    {
        $this->mocked_service_helper = $this->mock('alias:App\Libraries\Helper');
        $this->mocked_service_helper->shouldReceive('getFirebaseShortUrl')->andReturn('https://g-h.app/azqBBLzqjFVUcGC57');
        $this->mocked_service_helper->shouldReceive('shortPropertyTitle')->andReturn('Villa');
        $this->mocked_service_helper->shouldReceive('getSmsContent')->andReturn('');

        $traveller_dial_code = '91';
        $traveller_contact   = '8989898989';

        $property_title = 'Villa';
        $latitude       = 76.08909854;
        $longitude      = 23.08894888;
        $direction_url  = 'https://g-h.app/azqBBLzqjFVUcGC57';

        $this->mocked_sms_service->shouldReceive('sendPropertyDirectionSmsToGuest')->once()->with(
            $traveller_dial_code,
            $traveller_contact,
            $direction_url,
            $property_title
        );

        $this->booking_request_service->sendPropertyDirectionSmsToGuest($property_title, $latitude, $longitude, $traveller_dial_code, $traveller_contact);

    }//end test_send_property_direction_sms_to_guest()


    /**
     * Test for Notification On New Request Created.
     *
     * @return void
     */
    public function test_new_request_notification_to_host()
    {
        $create_booking_request = $this->createBookingRequests();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $host_id                = $create_booking_request['host']->id;

        $this->mocked_notification_service->shouldReceive('sendNewRequestPushNotificationsToHost')->once()->with(
            $request_hash_id,
            $host_id
        );

        $this->booking_request_service->sendNewRequestPushNotifications($create_booking_request['booking_request']);

    }//end test_new_request_notification_to_host()


     /**
      * Test for Notification On Approved Request to guest.
      *
      * @return void
      */
    public function test_approved_request_notification_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $host_id                = $create_booking_request['traveller']->id;

        $this->mocked_notification_service->shouldReceive('sendApprovedRequestPushNotificationsToGuest')->once()->with(
            $request_hash_id,
            $host_id
        );

        $this->booking_request_service->sendBookingRequestApprovedNotificationToGuest($create_booking_request['booking_request']);

    }//end test_approved_request_notification_to_guest()


    /**
     * Test for Notification On reject Request to guest.
     *
     * @return void
     */
    public function test_reject_request_notification_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $host_id                = $create_booking_request['traveller']->id;

        $this->mocked_notification_service->shouldReceive('sendRejectRequestPushNotificationsToGuest')->once()->with(
            $request_hash_id,
            $host_id
        );

        $this->booking_request_service->sendBookingRequestRejectNotificationToGuest($create_booking_request['booking_request']);

    }//end test_reject_request_notification_to_guest()


     /**
      * Test for Notification On Booking to guest.
      *
      * @return void
      */
    public function test_booking_notification_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $host_id                = $create_booking_request['traveller']->id;

        $this->mocked_notification_service->shouldReceive('sendBookingNotificationToGuest')->once()->with(
            $request_hash_id,
            $host_id
        );

        $this->booking_request_service->sendBookingPushNotificationsToGuest($create_booking_request['booking_request']);

    }//end test_booking_notification_to_guest()


     /**
      * Test for Notification On Booking to host.
      *
      * @return void
      */
    public function test_booking_notification_to_host()
    {
        $create_booking_request = $this->createBookingRequests();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $host_id                = $create_booking_request['host']->id;

        $this->mocked_notification_service->shouldReceive('sendBookingNotificationToHost')->once()->with(
            $request_hash_id,
            $host_id
        );

        $this->booking_request_service->sendBookingPushNotificationsToHost($create_booking_request['booking_request']);

    }//end test_booking_notification_to_host()


      /**
       * Test for Notification On Booking to host.
       *
       * @return void
       */
    public function test_get_graph_data()
    {
        $traveller     = $this->createUsers(1, ['prive_owner' => 1]);
        $start_of_year = PROPERLY_BOOOKING_START_DATE;
        $end_of_year   = carbon::parse(PROPERLY_BOOOKING_START_DATE)->endOfYear()->format('Y-m-d');
        $this->mocked_booking_request->shouldReceive('getGraphData')->with($traveller[0]->id, $start_of_year, $end_of_year)->andReturn([]);
        $this->booking_request_service->getGraphData($traveller[0]->id, $start_of_year, $end_of_year);

    }//end test_get_graph_data()


     /**
      * Test for Notification On Booking to host.
      *
      * @return void
      */
    public function test_get_prive_bookings()
    {
        $property   = $this->createProperties(1, 1);
        $start_date = Carbon::now()->toDateString();
        $end_date   = Carbon::now()->addMonth(1)->toDateString();
        $sort       = 1;
        $sort_order = 'ASC';
        // Show only booked property.
        $booking_status = 1;

        // Show only 5 property ad booking for home page.
        $total_count_show = 5;
        $this->mocked_booking_request->shouldReceive('getPriveBookings')->with($property['host']->id, $property['properties'][0]->id, $start_date, $end_date, 0, $total_count_show, $sort, $sort_order, $booking_status)->andReturn([]);
        $this->booking_request_service->getPriveBookings($property['host']->id, $property['properties'][0]->id, $start_date, $end_date, 0, $total_count_show, $sort, $sort_order, $booking_status);

    }//end test_get_prive_bookings()


}//end class
