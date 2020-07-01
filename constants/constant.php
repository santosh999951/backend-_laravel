<?php
/**
 * Common Constants.
 **/

define('API_BASE_URL', env('API_BASE_URL'));
define('SITE_URL', env('SITE_URL'));

define('WEBSITE_URL', env('WEBSITE_URL', 'https://www.guesthouser.com'));
define('MSITE_URL', env('MSITE_URL', 'https://m.guesthouser.com'));
define('PROPERLY_URL', env('PROPERLY_URL', 'https://app.manageproperly.com'));

define('QUEUE_DRIVER', env('QUEUE_DRIVER', 'sqs'));
define('QUEUE_DATABASE', 'database');

if (app()->environment('production') === true) {
    define('MAILER_SITE_URL', 'https://www.guesthouser.com');
} else {
    define('MAILER_SITE_URL', env('WEBSITE_URL', 'https://www.guesthouser.com'));
}

define('GH_CONTACT_NUMBER', '01244616100');
define('CONTACT_EMAIL', 'support@guesthouser.com');
define('APP_IS_LIVE', env('APP_IS_LIVE', false));

// Properly Support Email.
if (app()->environment('production') === true) {
    define('CONTACT_PROPERLY_EMAIL', ['contact@manageproperly.com']);
} else {
    define('CONTACT_PROPERLY_EMAIL', ['santosh@guesthouser.com', 'kanit@guesthouser.com', 'chiranjeet@guesthouser.com']);
}

// Admin Emails.
if (app()->environment('production') === true) {
    define(
        'ADMIN_EMAILS_FOR_NOTIFICATIONS',
        [
            'itush@guesthouser.com',
            'vishwas@guesthouser.com',
            'shruti@guesthouser.com',
        ]
    );

    // Property Listing Admin Mails.
    define(
        'LISTING_ADMIN_EMAIL_FOR_NOTIFICATIONS',
        [
            'yashaswi@guesthouser.com',
            'sumit@guesthouser.com',
            'srishti@guesthouser.com',
            'aeina@guesthouser.com',
            'aditi@guesthouser.com',
            'nitika@guesthouser.com',
            'prateek@guesthouser.com',
            'siddharth@guesthouser.com',
            'sayan@guesthouser.com',
        ]
    );
} else {
    define(
        'ADMIN_EMAILS_FOR_NOTIFICATIONS',
        [
            'gaurav@guesthouser.com',
            'akashdeep@guesthouser.com',
            'sahil.sethi@guesthouser.com',
        ]
    );

    define(
        'LISTING_ADMIN_EMAIL_FOR_NOTIFICATIONS',
        [
            'gaurav@guesthouser.com',
            'akashdeep@guesthouser.com',
            'sahil.sethi@guesthouser.com',
        ]
    );
}//end if

// Seo.
define('HOME_PAGE_TITLE', 'Book Holiday Homes, Vacation Rentals & More - GuestHouser');
define('HOME_PAGE_KEYWORD', 'holiday homes, Villas, cottages, service apartments, bungalows, houseboats , Guesthouses, Cruises, Guesthouser');
define('HOME_PAGE_DESC', 'Explore over 1,75,000 unique holiday homes in more than 2,200 destinations across India. Choose from homestays, villas, cottages, and more to experience the local way to stay!');


define('COMMON_META_TITLE', 'Guesthouser.com - The Local Way To Stay');
define('COMMON_META_DESC', 'GuestHouser is a platform that promotes a deeper and more genuine aspect of travel by connecting travellers with locals and delivering extra-ordinary travel experiences');

define('ANDROID_MIN_VERSION', '1.1');
define('ANDROID_LATEST_VERSION', '4.0.2');
define('ANDROID_LATEST_VERSION_CODE', 35);
define('ANDROID_MIN_VERSION_CODE', 4);

define('IOS_MIN_VERSION', '3.0.3');
define('IOS_LATEST_VERSION', '4.0.4');
define('MIN_VERSION_TEXT', 'This version is outdated. Please update to latest version.');
define('LATEST_VERSION_TEXT', 'Please update to latest version.');

define('NEW_RATING_DAYS', 10);
define('OLD_RATING_DAYS', 5);
define('PRICE_WITHOUT_SERVICE_FEE', 1);

// User hash param.
define('HASH_LENGTH_FOR_USER', 4);
define('HASH_CHAR_FOR_USER', '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
define('HASH_SALT_FOR_USER', '7711fa12cacdde1450b8edee54de514a');

// Admin user hash param.
define('HASH_LENGTH_FOR_ADMIN_USER', 4);
define('HASH_CHAR_FOR_ADMIN_USER', '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
define('HASH_SALT_FOR_ADMIN_USER', '6fRNifQ7gMmVfKvXwNtuEoNFC68GlNmr');

// Array hash param.
define('HASH_LENGTH_FOR_ARRAY', 4);
define('HASH_CHAR_FOR_ARRAY', 'abcdefghijklmnopqrstuvwxyz1234567890');
define('HASH_SALT_FOR_ARRAY', '7711fa12cacdde1450b8edee54de514a');

// Google place api key.
define('GOOGLE_PLACE_API_KEY', 'AIzaSyCLKw--EXPMVA3qtsEorf-9aqpM2pJBAa4');


// Google Auth.
if (app()->environment('production') === true) {
    define(
        'GOOGLE_CLIENT_ID',
        [
            'android' => '228641949265-frov88kjstvqa1hffhms1jhu7go1vrpe.apps.googleusercontent.com',
            'ios'     => '228641949265-1il5t4hnsol6se7bkpt7tf44hr5juhmo.apps.googleusercontent.com',
            'web'     => '228641949265-1vaf76rm0jrf0egn80up9jmh9ou75vq0.apps.googleusercontent.com',
        ]
    );
} else {
    define(
        'GOOGLE_CLIENT_ID',
        [
            'android' => '228641949265-6lbjhbcs9838m04fl7t9e71r5ge5ii8k.apps.googleusercontent.com',
            'ios'     => '228641949265-eoqin2s8mm9lguejds6hs2h68kef2pqe.apps.googleusercontent.com',
            'web'     => '228641949265-79n9sh989dh33mmodbv0urav1fevli14.apps.googleusercontent.com',
        ]
    );
}//end if

// OTP Verification Unique code.
if (app()->environment('production') === true) {
    define('OTP_VERIFICATION_HASH', '1tEtTBj+ooY');
} else if (app()->environment('testing') === true) {
    define('OTP_VERIFICATION_HASH', 'wI5nAnC70ja');
} else {
    define('OTP_VERIFICATION_HASH', 'Uwr+TrRXH9j');
}

// Google directions url.
define('GOOGLE_DIRECTION_URL', 'http://maps.google.com/?q=');

// Britly Access Token.
define('BITLY_ACCESS_TOKEN', 'b691c80e13d3fb5677e100338d2da35634c7d8f4');

// Signup source ids.
define('WEBSITE_SOURCE_ID', 0);
define('GOOGLE_SOURCE_ID', 1);
define('FACEBOOK_SOURCE_ID', 2);
define('EMAIL_SOURCE_ID', 3);
define('PHONE_SOURCE_ID', 4);
define('APPLE_SOURCE_ID', 5);

// Default device type.
define('DEFAULT_DEVICE_TYPE', 'web');

// All Device Type.
define(
    'ALL_DEVICE_TYPE',
    [
        'android' => 'app',
        'ios'     => 'app',
        'web'     => 'web',
        'website' => 'website',
    ]
);

// Hash.
define('HASH_LENGTH_FOR_ID_REFFERAL', 5);
define('HASH_CHAR_FOR_ID_REFFERAL', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789');
define('HASH_LENGTH_REFFERAL', 5);
define('HASH_SALT_REFFERAL', '31cbdf7fsa8dde1450b8edee54de514a');

// Wallet money.
// Character set for token generation.
define('TOKEN_FOR_RESET_PASSWORD', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
define('RESET_PASSWORD_LINK', WEBSITE_URL.'/password/reset/');
define('EMAIL_VERIFICATION_LINK', WEBSITE_URL.'/user/mailverify?ucode=');

// Properly Reset Password link.
define('PROPERLY_RESET_PASSWORD_LINK', PROPERLY_URL.'/reset/');



// Chat time.
define('CHAT_TIME_MORN', '10.00');
define('CHAT_TIME_EVE', '23.00');
define('CHAT_CALL_TEXT', 'Connect with us on phone. Available all days, 09:00am - 09:00pm');

// Passport Related.
define('PASSPORT_DRIVER', env('PASSPORT_DRIVER', 'db'));
define('PASSPORT_MEMORY', 'memory');
define('PASSPORT_USER_DATA_TO_STORE', ['id', 'name', 'email']);

define('PASSPORT_ACCESS_TOKEN_TTL', env('PASSPORT_ACCESS_TOKEN_TTL', (24 * 60 * 60)));
define('PASSPORT_REFRESH_TOKEN_TTL', env('PASSPORT_REFRESH_TOKEN_TTL', (30 * 24 * 60 * 60)));

 // AWS related constants.
define('S3_URL', 'https://s3-ap-southeast-1.amazonaws.com/');
define('S3_BUCKET', 'guesthouser');



if (app()->environment('production') === true) {
    define('USING_S3', true);
    define('PROPERTY_VIDEOS_S3_FOLDER', 'property_videos/');
} else {
    define('USING_S3', false);
    define('PROPERTY_VIDEOS_S3_FOLDER', 'property_videos_test/');
}

// Default s3 region.
define('DEFAULT_S3_REGION', 'ap-southeast-1');

// Cdn base url.
define('CDN_URL', 'https://d39vbwyctxz5qa.cloudfront.net/');

// Email static images.
define('MAILER_ASSETS_URL', CDN_URL.'mailer_images/static_assets/');

// Property base dir.
define('S3_PROPERTY_DIR', 'properties_images/properties_images/');

// Default property image.
define('S3_PROPERTY_DEFAULT_IMAGE', CDN_URL.'assets/images/no_property.png');

// Attraction url.
define('S3_ATTRACTION_DIR', 'attraction_images/');

// Property directories.
define('S3_PROPERTY_1X_DIR', CDN_URL.S3_PROPERTY_DIR.'1x/');
define('S3_PROPERTY_1_5X_DIR', CDN_URL.S3_PROPERTY_DIR.'1-5x/');
define('S3_PROPERTY_2X_DIR', CDN_URL.S3_PROPERTY_DIR.'2x/');
define('S3_PROPERTY_3X_DIR', CDN_URL.S3_PROPERTY_DIR.'3x/');
define('S3_PROPERTY_4_5X_DIR', CDN_URL.S3_PROPERTY_DIR.'4-5x/');


// Attraction dir.
define('S3_ATTRACTION_ORIGINAL_DIR', CDN_URL.S3_ATTRACTION_DIR.'original/');
define('S3_ATTRACTION_NEW_ORIGINAL_DIR', CDN_URL.S3_ATTRACTION_DIR.'new_original/');
define('S3_ATTRACTION_HD_DIR', CDN_URL.S3_ATTRACTION_DIR.'hd/');
define('S3_ATTRACTION_MEDIUM_DIR', CDN_URL.S3_ATTRACTION_DIR.'medium/');
define('S3_ATTRACTION_SMALL_DIR', CDN_URL.S3_ATTRACTION_DIR.'small/');
define('S3_ATTRACTION_THUMB_DIR', CDN_URL.S3_ATTRACTION_DIR.'thumb/');
define('S3_ATTRACTION_1X_DIR', CDN_URL.S3_ATTRACTION_DIR.'1x/');
define('S3_ATTRACTION_1_5X_DIR', CDN_URL.S3_ATTRACTION_DIR.'1-5x/');
define('S3_ATTRACTION_2X_DIR', CDN_URL.S3_ATTRACTION_DIR.'2x/');
define('S3_ATTRACTION_3X_DIR', CDN_URL.S3_ATTRACTION_DIR.'3x/');
define('S3_ATTRACTION_4_5X_DIR', CDN_URL.S3_ATTRACTION_DIR.'4-5x/');


// Property videos.
define(
    'PROPERTY_VIDEO_RESOLUTION',
    [
        '1080' => CDN_URL.PROPERTY_VIDEOS_S3_FOLDER.'videos/1080/',
        '720'  => CDN_URL.PROPERTY_VIDEOS_S3_FOLDER.'videos/720/',
        '480'  => CDN_URL.PROPERTY_VIDEOS_S3_FOLDER.'videos/480/',
        '360'  => CDN_URL.PROPERTY_VIDEOS_S3_FOLDER.'videos/360/',
    ]
);

define(
    'PROPERTY_VIDEO_THUMBNAIL_RESOLUTION',
    [
        'thumbs' => CDN_URL.PROPERTY_VIDEOS_S3_FOLDER.'thumbnails/thumbs/',
        '3x'     => CDN_URL.PROPERTY_VIDEOS_S3_FOLDER.'thumbnails/3x/',
    ]
);

// Default property image.
define('S3_PROPERTY_DEFAULT_THUMBNAIL_IMAGE', PROPERTY_VIDEO_THUMBNAIL_RESOLUTION['thumbs'].'default.png');


// Invoice images.
define('S3_INVOICE_IMAGES_URL', CDN_URL.'invoice_images/');

// Invoice Pdf temp dir.
define('PDF_TMP_DIR', '/tmp/');

if (app()->environment('production') === true) {
    define('S3_INVOICE_PDF_DIR', 'invoicepdf/');
} else {
    define('S3_INVOICE_PDF_DIR', 'invoicepdf_test/');
}

define('INVOICE_PDF_DIR', CDN_URL.S3_INVOICE_PDF_DIR);

// User profile pic folder.
define('PROFILE_PIC_FOLDER_URL', SITE_URL.'/user_images/');
define('S3_PROFILE_PIC_FOLDER_URL', CDN_URL.'user_images/user_images/');
define('PROFILE_PIC_TMP_DIR', base_path('public').'/tmp/');
define('PROFILE_PIC_DIR', base_path('public').'/user_images/');
define('S3_PROFILE_PIC_DIR', 'user_images/user_images/');
define('S3_RAW_PROFILE_PIC_DIR', 'user_images/raw_user_images/');

// Otp audio files generated from polly.
define('POLLY_OTP_AUDIO_DIR', storage_path().'/app/gh-lex/');
define('S3_POLLY_OTP_AUDIO_DIR', 'gh-lex-mum');
define('DEFAULT_POLLY_REGION', 'us-east-1');
define('S3_OTP_AUDIO_REGION', 'ap-south-1');

// Transcoding Region.
define('TRANSCODER_AWS_REGION', 'ap-south-1');

define('SQS_MUMBAI_REGION_URL', 'https://sqs.ap-south-1.amazonaws.com/003367487097/');

if (empty(env('QUEUE_DRIVER')) === false && env('QUEUE_DRIVER') === 'sqs') {
    define('SMS_HIGH_PRIORITY_QUEUE', SQS_MUMBAI_REGION_URL.'high_priority_queue');
    define('SMS_LOW_PRIORITY_QUEUE', SQS_MUMBAI_REGION_URL.'low_priority_queue');
    define('EMAIL_HIGH_PRIORITY_QUEUE', SQS_MUMBAI_REGION_URL.'high_priority_queue');
    define('EMAIL_LOW_PRIORITY_QUEUE', SQS_MUMBAI_REGION_URL.'low_priority_queue');
} else {
    define('SMS_HIGH_PRIORITY_QUEUE', 'high_priority_queue');
    define('SMS_LOW_PRIORITY_QUEUE', 'low_priority_queue');
    define('EMAIL_HIGH_PRIORITY_QUEUE', 'high_priority_queue');
    define('EMAIL_LOW_PRIORITY_QUEUE', 'low_priority_queue');
}

if (app()->environment('production') === true) {
    define('API_BNB_QUEUE', SQS_MUMBAI_REGION_URL.'airbnb_queue_live');
} else {
    define('API_BNB_QUEUE', SQS_MUMBAI_REGION_URL.'airbnb_queue_test');
}

define('AMENITY_ICON_URL', S3_URL.S3_BUCKET.'/public/');

// Review images.
define('S3_REVIEW_DIR', 'review_images/');
define('S3_REVIEW_DIR_TMP', 'review_images_tmp/');

// Lead images.
define('S3_LEAD_DIR', 'lead_images/');

define('S3_REVIEW_ORIGINAL_DIR', S3_REVIEW_DIR.'original_images/');
define('S3_REVIEW_THUMB_DIR', S3_REVIEW_DIR_TMP.'thumb/');
define('PROPERTY_REVIEW_IMAGE_TEMP_URL', base_path('public').'/review_images/temp_images/');
define('PROPERTY_REVIEW_IMAGE_BASE_URL', base_path('public').'/review_images/original_images/');
define('S3_REVIEW_ORIGINAL_DIR_URL', CDN_URL.S3_REVIEW_ORIGINAL_DIR);
define('S3_REVIEW_THUMB_DIR_URL', CDN_URL.S3_REVIEW_THUMB_DIR);
define('S3_IMAGES_CACHE_TIME', (60 * 60 * 24 * 365 * 5));


define('S3_PROMOTIONAL_DIR_TMP', 'promotional_images/offer_landing_page_image_orignal/');

// Lead Images.
define('S3_LEAD_PIC_FOLDER_URL', S3_URL.S3_BUCKET.'/public/leads/');


// Offer images.
define('S3_OFFER_DIR_MOBILE', 'promotional_images/offer_landing_page_images/');
define('S3_OFFER_DIR_WEB', 'promotional_images/offer_landing_page_web/');

// Home Banners.
define('S3_HOMEBANNER_DIR_WEB', 'home_banners/app_banners_new/');
define('S3_HOMEBANNER_DIR_MOBILE', 'home_banners/app_banners_new/mobile/');

// Promo Serach Banners.
define('S3_PROMOBANNER_DIR_MOBILE', 'promotional_images/search_banner/mobile/');
define('S3_PROMOBANNER_DIR_WEB', 'promotional_images/search_banner/web/');

// Avatar Url.
define('S3_AVATAR_MEN', CDN_URL.'profile_pic_avatar/male/');
define('S3_AVATAR_WOMEN', CDN_URL.'profile_pic_avatar/female/');

if (app()->environment('production') === true) {
    define('PROCESS_INVOICE', 'https://sqs.ap-southeast-1.amazonaws.com/003367487097/process_invoice');
} else {
    define('PROCESS_INVOICE', 'https://sqs.ap-southeast-1.amazonaws.com/003367487097/process_invoice_test');
}

if (app()->environment('production') === true) {
    define('NEW_API_MAIL_QUEUE', SQS_MUMBAI_REGION_URL.'send_email_api');
} else {
    define('NEW_API_MAIL_QUEUE', SQS_MUMBAI_REGION_URL.'testing_api_email_queue');
}

if (app()->environment('production') === true) {
    define('NEW_API_SMS_QUEUE', SQS_MUMBAI_REGION_URL.'send_sms_api');
} else {
    define('NEW_API_SMS_QUEUE', SQS_MUMBAI_REGION_URL.'testing_api_sms_queue');
}

if (app()->environment('production') === true) {
    define('NEW_API_NOTIFICATION_QUEUE', SQS_MUMBAI_REGION_URL.'send_notification_api');
} else {
    define('NEW_API_NOTIFICATION_QUEUE', SQS_MUMBAI_REGION_URL.'testing_send_notification_api');
}

if (app()->environment('production') === true) {
    define('EVENT_QUEUE', SQS_MUMBAI_REGION_URL.'events');
    define('COMMUNICATION_QUEUE', SQS_MUMBAI_REGION_URL.'communication');
} else {
    define('EVENT_QUEUE', SQS_MUMBAI_REGION_URL.'events_testing');
    define('COMMUNICATION_QUEUE', SQS_MUMBAI_REGION_URL.'communication_testing');
}

define('SPOT_LIGHT_IMAGES_PATH', CDN_URL.'public/app_place_images/');

// Profile picture dimensions.
define('DEFAULT_PROFILE_PIC_WIDTH', 375);
define('DEFAULT_PROFILE_PIC_HEIGHT', 288);
define('DEFAULT_PROFILE_PIC_2X_WIDTH', (DEFAULT_PROFILE_PIC_WIDTH * 2));
define('DEFAULT_PROFILE_PIC_2X_HEIGHT', (DEFAULT_PROFILE_PIC_HEIGHT * 2));
define('WATERMARK_ALLOWDED_IMAGES_EXTENSION', ['jpg', 'jpeg', 'JPG', 'JPEG', 'png', 'PNG']);
define('WATERMARK_ALLOWDED_IMAGES_SIZE', '10240');

if (app()->environment('production') === true) {
    // Queue to read to what pid to download from s3 to watermark.
    define('WATERMARK_PROPERTY_IMAGES_QUEUE', 'https://sqs.ap-southeast-1.amazonaws.com/003367487097/watermark-property-images-using-imagick');

    // Video Transcode Queue.
    define('VIDEOS_PENDING_FOR_TRANSCODING_QUEUE', 'https://sqs.ap-south-1.amazonaws.com/003367487097/videos_pending_for_elastic_transcoding');
    define('VIDEOS_TO_DELETE', 'https://sqs.ap-south-1.amazonaws.com/003367487097/videos_to_delete');
} else {
    // Queue to read to what pid to download from s3 to watermark.
    define('WATERMARK_PROPERTY_IMAGES_QUEUE', 'https://sqs.ap-southeast-1.amazonaws.com/003367487097/watermark-property-images-using-imagick-test');
    define('VIDEOS_TO_DELETE', 'https://sqs.ap-south-1.amazonaws.com/003367487097/videos_to_delete_test');

    // Video Transcode Queue.
    define('VIDEOS_PENDING_FOR_TRANSCODING_QUEUE', 'https://sqs.ap-south-1.amazonaws.com/003367487097/videos_pending_for_elastic_transcoding_test');
}


// Size in kb
// Search Page related.
define('MIN_FILTER_BUDGET_VALUE_IN_INR', 100);
define('MAX_FILTER_BUDGET_VALUE_IN_INR', 70000);
define('TRENDING_PLACES_LIMIT', 5);

// Default currency.
define('DEFAULT_CURRENCY', 'INR');

// Currency symbols.
define(
    'CURRENCY_SYMBOLS',
    [
        'AUD' => [
            'webicon'     => 'A$',
            'non-webicon' => 'A$',
            'iso_code'    => 'AUD',
        ],
        'INR' => [
            'webicon'     => '₹',
            'non-webicon' => 'Rs',
            'iso_code'    => 'INR',
        ],
        'MYR' => [
            'webicon'     => 'RM',
            'non-webicon' => 'RM',
            'iso_code'    => 'MYR',
        ],
        'MVR' => [
            'webicon'     => 'Rf',
            'non-webicon' => 'Rf',
            'iso_code'    => 'MVR',
        ],
        'SGD' => [
            'webicon'     => 'S$',
            'non-webicon' => 'S$',
            'iso_code'    => 'SGD',
        ],
        'THB' => [
            'webicon'     => '฿',
            'non-webicon' => '฿',
            'iso_code'    => 'THB',
        ],
        'GBP' => [
            'webicon'     => '£',
            'non-webicon' => '£',
            'iso_code'    => 'GBP',
        ],
        'USD' => [
            'webicon'     => '$',
            'non-webicon' => '$',
            'iso_code'    => 'USD',
        ],
        'EUR' => [
            'webicon'     => '€',
            'non-webicon' => '€',
            'iso_code'    => 'EUR',
        ],
        'NZD' => [
            'webicon'     => 'NZ$',
            'non-webicon' => 'NZ$',
            'iso_code'    => 'NZD',
        ],
        'IDR' => [
            'webicon'     => 'Rp',
            'non-webicon' => 'Rp',
            'iso_code'    => 'IDR',
        ],
        'AED' => [
            'webicon'     => 'AED',
            'non-webicon' => 'AED',
            'iso_code'    => 'AED',
        ],
        'NPR' => [
            'webicon'     => 'NPR',
            'non-webicon' => 'NPR',
            'iso_code'    => 'NPR',
        ],
        'LKR' => [
            'webicon'     => 'LKR',
            'non-webicon' => 'LKR',
            'iso_code'    => 'LKR',
        ],
        'BDT' => [
            'webicon'     => '৳',
            'non-webicon' => 'BDT',
            'iso_code'    => 'BDT',
        ],
        'PHP' => [
            'webicon'     => '₱',
            'non-webicon' => 'PHP',
            'iso_code'    => 'PHP',
        ],
        'HKD' => [
            'webicon'     => 'HK$',
            'non-webicon' => 'HK$',
            'iso_code'    => 'HKD',
        ],
        'VND' => [
            'webicon'     => '₫',
            'non-webicon' => '₫',
            'iso_code'    => 'VND',
        ],
    ]
);

define(
    'IVR_DID_NUMBER_LIST_FOR_TRAVELLER_CONTACT',
    [
        4614829,
        4614828,
        4614827,
        4614826,
        4614825,
        4614824,
        4614823,
        4614822,
        4614821,
        4614820,
    ]
);

define(
    'AUTOCOMPLETE_APIS',
    [
        'GOOGLE_AUTOCOMPLETE'         => 1,
        'MAPBOX_AUTOCOMPLETE'         => 2,
        'ELASTIC_SEARCH_AUTOCOMPLETE' => 3,
    ]
);

define('DEFAULT_AUTOCOMPLETE_API', 'GOOGLE_AUTOCOMPLETE');


// Log path used by crons.
define('CRON_LOGS_BASE_PATH', '/var/log/cron_log/');


define('CONTROLLER_PATH', base_path('app/Http/Controllers'));
define('RESPONSE_PATH', base_path('app/Http/Response'));

// Constant for writing db query logs to files.
if (app()->environment('production') === true) {
    define('DB_QUERY_LOG_QUEQE', SQS_MUMBAI_REGION_URL.'log_db_queries');
    define('DB_QUERY_LOG_METHOD', 'queue');
} else {
    define('DB_QUERY_LOG_QUEQE', SQS_MUMBAI_REGION_URL.'log_db_queries_test');
    define('DB_QUERY_LOG_METHOD', 'file');
}

// Properly Team Member status.
define('PROPERLY_TEAM_MEMBER_INVITE', '0');
define('PROPERLY_TEAM_MEMBER_ACTIVE', '1');
define('PROPERLY_TEAM_MEMBER_SUSPEND', '2');
define('PROPERLY_TEAM_MEMBER_DELETE', '3');

define('GH_DEFAULT_EMAIL_DOMAIN', 'g-h.app');
define('PROPERTY_LIVE_DATE', '2019-08-01');

// Lead Images Status.
define('SUBMITTED_STATUS', 2);

// For Apple Login.
define('APPLE_APP_CLIENT_ID', 'com.guesthouser.ghTest');
define('APPLE_LOGIN_CLAIM_ISSUER', 'https://appleid.apple.com');
