<?php
/**
 * Email related constants.
 **/

// Email content base url.
define('STATIC_BASE_URL', SITE_URL.'/');

// Sendgrid.
define('SENDGRID_SMTP_URL', 'smtp.sendgrid.net');
define('SENDGRID_SMTP_PORT', '587');
define('SENDGRID_SMTP_USER', 'guesthouser_mail');
define('SENDGRID_SMTP_PASS', 'ghouser@321');

// Email campaign tracking code.
define('DC_TRANS_TRACK_ID', 86768);
define('DC_PROMO_TRACK_ID', 86769);

// Transactional email campaigns name.
define('CGN_WELCOME_MAILER', 'new_signup');
define('CGN_EMAIL_VERIFICATION_MAIILER', 'email_verification');
define('CGN_EMAIL_NEW_REQUEST', 'new_request');
define('CGN_EMAIL_NEW_REQUEST_REMINDER', 'new_request_reminder');
define('CGN_EMAIL_REQUEST_APPROVED', 'request_approved');
define('CGN_EMAIL_REQUEST_REJECTED', 'request_rejected');
define('CGN_EMAIL_REQUEST_CANCELLED', 'request_cancelled');
define('CGN_EMAIL_REQUEST_TIMEOUT', 'request_timeout');
define('CGN_EMAIL_REQUEST_EXPIRED', 'request_expired');
define('CGN_EMAIL_BOOKING_COMPLETE', 'booking_complete');
define('CGN_EMAIL_BOOKING_CANCELLED_HOST', 'booking_cancelled_host');
define('CGN_EMAIL_BOOKING_CANCELLED_GUEST', 'booking_cancelled_guest');
define('CGN_EMAIL_BOOKING_NOT_AVAILABLE', 'booking_not_available');
define('CGN_EMAIL_SHARED_INVOICE', 'shared_invoice');
define('CGN_EMAIL_CANCELLATION_REJECTED', 'cancellation_rejected');
define('CGN_EMAIL_TRAVELLER_REVIEW_REMINDER', 'traveller_review_reminder');
define('CGN_EMAIL_HOST_REVIEW_REMINDER', 'host_review_reminder');
define('CGN_EMAIL_HOST_REVIEWED_GUEST', 'host_reviewed_guest');
define('CGN_EMAIL_GUEST_REVIEWED_PROPERTY', 'guest_reviewed_property');
define('CGN_EMAIL_SETUP_PASSWORD', 'setup_password');
define('CGN_EMAIL_PAYOUT_INFO', 'payout_info');
define('CGN_EMAIL_LISTING_MODIFIED', 'listing_modified');
define('CGN_EMAIL_NEW_LISTING', 'new_listing');
define('CGN_EMAIL_RESET_PASSWORD', 'reset_password');

// Otp expire time in minutes.
define('OTP_EXPIRY_TIME', 30);

// Send sms via service (SmsCountry, TextLocal).
define('SEND_SMS_METHOD', 'SmsCountry');

define('SMS_LOG_DIR', storage_path().'/logs/sms_log/');

// Twilio sms api constants.
define('TWILIO_SID', 'AC4987bf5331598a2ad6f55e9e19101714');
define('TWILIO_TOKEN', 'a53a5b166f6fb1eeef81be4444c3206f');

// Twilio numbers to send sms and make call.
define('TWILIO_NUMBERS', ['+16468469446', '+13022020404']);


define('TWIMLET_URL', 'http://twimlets.com/message?Message=');


// Smscountry api credentials.
define('SMS_COUNTRY_URL', 'http://www.smscountry.com/SMSCwebservice_Bulk.aspx');
define('SMS_COUNTRY_API_USERNAME', 'sahilnainta');
define('SMS_COUNTRY_API_PASSWORD', 'gh@123');

// Textlocal api credentials.
define('TLOCAL_URL', 'https://api.textlocal.in/send/?');
define('TLOCAL_SMS_API_USERNAME', 'tech@guesthouser.com');
define('TLOCAL_SMS_API_HASH', '605bef533421c875abc2335fe64f3449f052a8ab43dd7c5216c85b69e7bce890');

// Sender Id for transactional sms.
define('BOOKING_SMS_SENDER_ID', 'GHBOOK');
define('HELP_SMS_SENDER_ID', 'GHHELP');
define('VERIFY_SMS_SENDER_ID', 'GHVRFY');
define('DEFAULT_SMS_SENDER_ID', 'GSTHSR');
define('SMS_SENDER_IDS', implode(',', [BOOKING_SMS_SENDER_ID, HELP_SMS_SENDER_ID, VERIFY_SMS_SENDER_ID, DEFAULT_SMS_SENDER_ID]));

// Reset password otp limit.
define('MAX_RESET_PASSWORD_OTP_PER_DAY', 5000);
define('MAX_RESET_PASSWORD_OTP_PER_HOUR', 1000);

// Push notifications.
define('BOOKINGS_PUSH_NOTIFICATION', 1);
define('CUSTOM_NOTIFICATION', 2);
define('PROPERTY_DETAILS', 3);
define('WEB_VIEW', 4);
define('MANAGE_LISTING', 5);
define('SIGNUP_SCREEN', 6);
define('SEARCH_SCREEN', 7);
define('INVITE_SCREEN', 8);
define('APP_UPDATE', 9);
define('BANNER_NOTIFICATION', 10);
define('WALLET_NOTIFICATION', 12);
define('PAY_REMANING_AMOUNT', 13);
define('HOST_PROPERTY_VIEW', 14);
define('CTA_NOTIFICATION', 15);
define('UPDATE_CALENDAR', 16);

// Push Notification Credentials.
define(
    'ANDROID_API_ACCESS_KEYS',
    [
        'gcm'   => 'AIzaSyCLKw--EXPMVA3qtsEorf-9aqpM2pJBAa4',
        'fcm'   => 'AAAANTwfRlE:APA91bE_GR8_XvOfxL-Ykgnz9LjIYZZOXlv7-h_xtf4eNFXkYrjamyFFjWzQ660lW8gN-_mNmSJxBoJ11ogEGDOnvUpA10PIPzjrTzFTvW2I6veZTN1ZLUVHDIdmtmhb24TuMBbmY5SYEA1GMeWVImarSAYL6dA0dA',
        'prive' => 'AIzaSyCS7H1dswlX-wcTKyYY7nrZlTbzOsiwNYA',
        'other' => 'AIzaSyCfk05gUF9u18Pz-Suv9hB1-ptGzF_et_c',
    ]
);

define(
    'ANDROID_PUSH_NOTIFICATION_URL',
    [
        'gcm'   => 'https://android.googleapis.com/gcm/send',
        'fcm'   => 'https://fcm.googleapis.com/fcm/send',
        'prive' => 'https://android.googleapis.com/fcm/send',
        'other' => 'https://android.googleapis.com/fcm/send',
    ]
);

// IOS APNS DATA.
define('IOS_AUTH_KEY', 'AuthKey_2HY33H7RA2.p8');
define('IOS_TEAM_ID', 'R26BPY33GG');
define('IOS_AUTH_KEY_ID', '2HY33H7RA2');

// IOS APNS URL.
if (app()->environment('production') === true) {
    define('IOS_CERTIFICATE', base_path().'/public/push_production.pem');
    define('IOS_APNS_URL', 'ssl://gateway.push.apple.com:2195');
    define('IOS_FEEDBACK_URL', 'ssl://feedback.push.apple.com:2196');

    define('IOS_APP_BUNDLE_ID', 'com.guesthouser.ghTest');
    define('IOS_PUSH_NOTIFICATION_URL', 'http://api.push.apple.com');
} else {
    define('IOS_CERTIFICATE', base_path().'/public/push_development.pem');
    define('IOS_APNS_URL', 'ssl://gateway.sandbox.push.apple.com:2195');
    define('IOS_FEEDBACK_URL', 'ssl://feedback.sandbox.push.apple.com:2196');

    define('IOS_APP_BUNDLE_ID', 'com.guesthouser.ghTest-staging');
    define('IOS_PUSH_NOTIFICATION_URL', 'https://api.development.push.apple.com');
}

if (app()->environment('production') === true) {
    define('FAILED_JOB_NOTIFICATION_EMAILS', ['techsupport@guesthouser.com']);
} else {
    define('FAILED_JOB_NOTIFICATION_EMAILS', ['santosh@guesthouser.com', 'akashdeep@guesthouser.com', 'sahil.sethi@guesthouser.com']);
}

// Mailer Templates.
// phpcs:disable
define(
    'MAILERS',
    [
        'failed_job' => [
            'view' => 'emails.failed_job_notification',
            'subject' => 'Failed Job Notification'
        ],
        'support_job' => [
            'view' => 'emails.prive.support',
            'images' => [
                'support'           => MAILER_ASSETS_URL.'support.png',
             ],
        ],
        'wallet_default' => [
            'view' => 'emails.wallet.default_mailer',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png',
                'wallet_image'            => MAILER_ASSETS_URL.'wallet.png'
            ],
        ],
        'wallet_trip_and_review' => [
            'view' => 'emails.wallet.trip_and_review_mailer',
                'images' => [
                    'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                    'social_image'      => MAILER_ASSETS_URL.'socials.png',
                    'wallet_image'            => MAILER_ASSETS_URL.'wallet.png'
            ],
        ],
        'wallet_referal_bonus' => [
                'view' => 'emails.wallet.referal_bonus_mailer',
                'images' => [
                    'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                    'social_image'      => MAILER_ASSETS_URL.'socials.png',
                    'wallet_image'            => MAILER_ASSETS_URL.'wallet.png'
            ],
        ],
        'wallet_friend_referal_bonus' => [
                'view' => 'emails.wallet.friend_referal_bonus_mailer',
                'images' => [
                    'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                    'social_image'      => MAILER_ASSETS_URL.'socials.png',
                    'wallet_image'            => MAILER_ASSETS_URL.'wallet.png'
            ],
        ],
        'wallet_referal_first_booking_bonus' => [
                'view' => 'emails.wallet.referal_first_booking_mailer',
                'images' => [
                    'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                    'social_image'      => MAILER_ASSETS_URL.'socials.png',
                    'wallet_image'            => MAILER_ASSETS_URL.'wallet.png'
            ],
        ],
        'apply_wallet_money' => [
                'view' => 'emails.wallet.apply_wallet_money_mailer',
                'images' => [
                    'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                    'social_image'      => MAILER_ASSETS_URL.'socials.png',
                    'wallet_image'            => MAILER_ASSETS_URL.'wallet.png'
            ],
        ],
        'registration_welcome' => [
            'view' => 'emails.registration.welcome',
            'subject' => 'Registration Success!!!',
            'images' => [
                'welcome_bg'      => MAILER_ASSETS_URL.'welcome_email_bg.jpg',
                'logo_bordered'   => MAILER_ASSETS_URL.'logo_bordered.png'
            ]
        ],
        'registration_welcome_google' => [
            'view' => 'emails.registration.register_google',
            'subject' => 'Registration Success!!!',
            'images' => [
                'welcome_bg'      => MAILER_ASSETS_URL.'welcome_email_bg.jpg',
                'logo_bordered'   => MAILER_ASSETS_URL.'logo_bordered.png',
            ]
        ],
        'registration_welcome_fb' => [
            'view' => 'emails.registration.register_fb',
            'subject' => 'Registration Success!!!',
            'images' => [
                'welcome_bg'      => MAILER_ASSETS_URL.'welcome_email_bg.jpg',
                'logo_bordered'   => MAILER_ASSETS_URL.'logo_bordered.png',
            ]
        ],
        'registration_verify_email' => [
            'view' => 'emails.registration.verifymail',
            'subject' => 'GuestHouser Email Verification',
            'images' => [
                'password_reset_bg'  => MAILER_ASSETS_URL.'password_reset_background.png',
                'mail_verify_footer' => MAILER_ASSETS_URL.'mail_verify_footer.png',
                'logo'               => MAILER_ASSETS_URL.'logo.png',
            ]
        ],
        'new_request_email' => [
            'view' => 'emails.booking.new_request',
            'subject' => 'New Booking Request',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                'new_request_image' => MAILER_ASSETS_URL.'new_request_graphic.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png'
            ]
        ],
        'cancel_booking_request_to_host' => [
            'view' => 'emails.booking.booking_cancelled_host',
            'subject' => 'Booking Cancelled',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                'cancel_request_image' => MAILER_ASSETS_URL.'cancelled_graphic.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png'
            ]
        ],
        'cancel_booking_request_to_guest' => [
            'view' => 'emails.booking.booking_cancelled_guest',
            'subject' => 'Booking Cancellation Confirmation',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                'cancel_request_image' => MAILER_ASSETS_URL.'cancelled_graphic.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png'
            ]
        ],
        'cancel_booking_request_to_customer_support' => [
            'view' => 'emails.booking.booking_cancelation_notify_cust_sup',
            'subject' => 'Booking Cancellation After Payment',
            'images' => []
        ],
        'request_approved_email' => [
            'view' => 'emails.booking.request_approved',
            'subject' => 'Booking Request Approved',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                'approved_request_image' => MAILER_ASSETS_URL.'booking_approved_graphic.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png'
            ]
        ],
        'request_rejected_email' => [
            'view' => 'emails.booking.request_rejected',
            'subject' => 'Sorry, your requested property is unavailable.',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                'cancel_request_image' => MAILER_ASSETS_URL.'cancelled_graphic.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png'
            ]
        ],
        'partial_booked_guest' => [
            'view' => 'emails.booking.partial_booked_guest',
            'subject' => 'Booking Completed',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                'booking_confirmed_image' => MAILER_ASSETS_URL.'booking_confirmed_traveller_graphic.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png'
            ]
        ],
        'booked_guest' => [
            'view' => 'emails.booking.booked_guest',
            'subject' => 'Booking Completed',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                'booking_confirmed_image' => MAILER_ASSETS_URL.'booking_confirmed_traveller_graphic.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png'
            ]
        ],
        'partial_booked_host' => [
            'view' => 'emails.booking.partial_booked_host',
            'subject' => 'New Booking',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                'booking_confirmed_image' => MAILER_ASSETS_URL.'booking_confirmed_traveller_graphic.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png'
            ]
        ],
        'booked_host' => [
            'view' => 'emails.booking.booked_host',
            'subject' => 'New Booking',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                'booking_confirmed_image' => MAILER_ASSETS_URL.'booking_confirmed_traveller_graphic.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png'
            ]
        ],
        'booked_payout_information' => [
            'view' => 'emails.booking.booked_payout_information',
            'subject' => 'Payout Details',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'site_logo_2x.png',
                'booking_confirmed_image' => MAILER_ASSETS_URL.'booking_confirmed_traveller_graphic.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png'
            ]
        ],
        'booked_admin' => [
            'view' => 'emails.booking.booked_admin',
            'subject' => 'New Booking',
            'images' => []
        ],
        'property_listing_review' => [
            'view' => 'emails.mailers.listing_review',
            'subject' => 'Property UnderReview',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'logo.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png',
            ]
        ],
        'property_listing_submit_to_bd_team' => [
            'view' => 'emails.mailers.listing_submitted_bd',
            'subject' => 'New Listing Request - ',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'logo.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png',
            ]
        ],
        'property_listing_modification_submit_to_bd_team' => [
            'view' => 'emails.mailers.modification_submitted_bd',
            'subject' => 'Property Modified - ',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'logo.png',
                'social_image'      => MAILER_ASSETS_URL.'socials.png',
            ]
        ],
        'reset_password' => [
            'view' => 'emails.auth.reset_password',
            'subject' => 'Password Reset',
            'images' => [
                'logo'              => MAILER_ASSETS_URL.'logo.png',
                'background_image'  => MAILER_ASSETS_URL.'background.png',
                'footer'            => MAILER_ASSETS_URL.'reset-footer.png'
            ]
        ],
        'properly_payment_link' => [
            'view'      => 'emails.booking.properly_payment_link',
            'subject'   => 'Payment for booking request id ',
            'images'    => [
                'payment_note'              => MAILER_ASSETS_URL.'payment_link_note.png',
            ]
        ],
        'properly_reset_password' => [
            'view' => 'emails.auth.properly_reset_password',
            'subject' => 'Password Reset',
            'images' => [
                'lock'              => MAILER_ASSETS_URL.'reset_password_lock.png',
            ]
        ],
        'registration_welcome_apple' => [
            'view' => 'emails.registration.register_apple',
            'subject' => 'Registration Success!!!',
            'images' => [
                'welcome_bg'      => MAILER_ASSETS_URL.'welcome_email_bg.jpg',
                'logo_bordered'   => MAILER_ASSETS_URL.'logo_bordered.png',
            ]
        ],
    ]
);



define(
    'SMS_TEMPLATES',
    [
        'add_wallet_money_for_trip_review' =>[
            'view' => 'sms.wallet_transactions.add_for_trip_review',
            'sender_id' => DEFAULT_SMS_SENDER_ID
        ],
        'create_booking_request' =>[
            'view' => 'sms.booking_request_sent',
            'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'cancel_booking_request_sms_to_host' =>[
            'view' => 'sms.boooking_cancellation_confirmation_host',
            'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'cancel_booking_request_sms_to_guest' =>[
            'view' => 'sms.boooking_cancellation_confirmation_guest',
            'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'approved_booking_request_sms_to_guest' =>[
            'view' => 'sms.booking_request_approved',
            'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'rejected_booking_request_sms_to_guest' =>[
            'view' => 'sms.booking_request_rejected',
            'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'partial_booking_complete_guest' =>[
            'view' => 'sms.partial_booking_complete_guest',
            'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'booking_complete_guest' =>[
            'view' => 'sms.booking_complete_guest',
            'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'property_directions' =>[
            'view' => 'sms.property_directions',
            'sender_id' => DEFAULT_SMS_SENDER_ID
        ],
        'booking_complete_host' =>[
            'view' => 'sms.booking_complete_host',
            'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'add_wallet_money_for_referal_bonus' =>[
            'view' => 'sms.wallet_transactions.referal_bonus',
             'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'add_wallet_money_for_friend_referal_bonus' =>[
            'view' => 'sms.wallet_transactions.friend_referal_bonus',
             'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'add_wallet_money_for_first_booking_bonus' =>[
            'view' => 'sms.wallet_transactions.first_booking_bonus',
             'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'update_wallet_money_for_apply_wallet_money_booking_bonus' =>[
            'view' => 'sms.wallet_transactions.apply_wallet_money',
            'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'payment_link_sms' =>[
            'view' => 'sms.payment_link_sms',
            'sender_id' => BOOKING_SMS_SENDER_ID
        ],
        'password_reset_otp' =>[
            'view' => 'sms.password_reset_code',
            'sender_id' => DEFAULT_SMS_SENDER_ID
        ],
        'user_loginurl_sms' =>[
            'view' => 'sms.user_loginurl_sms',
            'sender_id' => DEFAULT_SMS_SENDER_ID
        ]

    ]

);

// OTP types.
define(
    'OTP_TYPES',
    [
        'login'          => 0,
        'reset_password' => 1,
    ]
);
// phpcs:enable
