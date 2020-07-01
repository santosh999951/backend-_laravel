<?php
/**
 * Booking related constants.
 **/

// Booking hash params.
define('HASH_LENGTH_FOR_BOOKING_REQUEST_ID', 5);
define('HASH_CHAR_FOR_BOOKING_REQUEST_ID', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789');
define('HASH_SALT_FOR_BOOKING_REQUEST_ID', '7711fa12cacdde1450b8edee54de514a');

// Booking request status.
define('AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED', -13);
define('CANCELLED_BY_GH_AS_TRAVELLER', -12);
define('CANCELLED_BY_GH_AS_HOST', -11);
define('INVENTORY_FULL_CANCELLATION', -10);
define('AUTOMATION_CANCEL_REQUEST', -9);
define('LOADED_BOOKING_REQUEST', -8);
define('NO_RESPONSE_EXPIRY', -7);
define('TEST_BOOKING', -6);
define('REQUEST_CANCELLED', -5);
define('EXPIRED', -4);
define('NO_REPLY', -3);
define('REQUEST_REJECTED', -2);
define('NEW_REQUEST', -1);
define('REQUEST_APPROVED', 0);
define('BOOKED', 1);
define('REQUEST_TO_CANCEL_AFTER_PAYMENT', 2);
define('CANCELLED_AFTER_PAYMENT', 3);
define('NON_AVAILABILITY_REFUND', 4);
define('CANCEL_AFTER_RELEASED_PAYMENT', 5);
define('CANCELLED_BY_HOST_AFTER_PAYMENT', 6);
define('OVERBOOKED', 7);
define('CANCELLED_AFTER_OVERBOOKED', 8);
define('BOOKING_SWITCHED', 9);
define('CANCEL_OFFLINE_BOOKING', 10);
define('CANCELLED_BY_GH_AFTER_PAYMENT_AS_HOST', 11);
define('CANCELLED_BY_GH_AFTER_PAYMENT_AS_TRAVELLER', 12);
define('AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_AFTER_PAYMENT', 13);

// Booking request status text.
define('AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_TEXT', 'Cancelled');
define('CANCELLED_BY_GH_AS_TRAVELLER_TEXT', 'Cancelled');
define('CANCELLED_BY_GH_AS_HOST_TEXT', 'Cancelled');
define('INVENTORY_FULL_CANCELLATION_TEXT', 'Cancelled');
define('AUTOMATION_CANCEL_REQUEST_TEXT', 'Cancelled');
define('LOADED_BOOKING_REQUEST_TEXT', 'Pending');
define('NO_RESPONSE_EXPIRY_TEXT', 'No Response');
define('TEST_BOOKING_TEXT', 'Test Booking');
define('REQUEST_CANCELLED_TEXT', 'Cancelled');
define('EXPIRED_TEXT', 'Expired');
define('NO_REPLY_TEXT', 'No Reply');
define('REQUEST_REJECTED_TEXT', 'Rejected');
define('NEW_REQUEST_TEXT', 'Awaiting Response');
define('REQUEST_APPROVED_TEXT', 'Approved');
define('BOOKED_TEXT', 'Booked');
define('REQUEST_TO_CANCEL_AFTER_PAYMENT_TEXT', 'Cancellation Requested');
define('CANCELLED_AFTER_PAYMENT_TEXT', 'Cancelled');
define('NON_AVAILABILITY_REFUND_TEXT', 'Non availability refund');
define('CANCELLED_BY_HOST_AFTER_PAYMENT_TEXT', 'Cancelled');
define('OVERBOOKED_TEXT', 'Overbooked');
define('CANCELLED_AFTER_OVERBOOKED_TEXT', 'Cancelled');
define('BOOKING_SWITCHED_TEXT', 'Cancelled');
define('CANCEL_OFFLINE_BOOKING_TEXT', 'Cancelled');
define('CANCELLED_BY_GH_AFTER_PAYMENT_AS_HOST_TEXT', 'Cancelled');
define('CANCELLED_BY_GH_AFTER_PAYMENT_AS_TRAVELLER_TEXT', 'Cancelled');
define('AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_AFTER_PAYMENT_TEXT', 'Cancelled');
define('UPCOMING_TEXT', 'Upcoming');
define('ONGOING_TEXT', 'Ongoing');
define('COMPLETED_TEXT', 'Completed');
define('CANCELLED_TEXT', 'Cancelled');

// Traveller Booking Request Status Header Text.
define('REQUEST_CANCELLED_BY_HOST_HEADER_TEXT', "Sorry! This property is not available for your\nselected dates.");
define('REQUEST_CANCELLED_HEADER_TEXT', 'Oh! You have cancelled your request.');
define('EXPIRED_HEADER_TEXT', 'Sorry! Your request has been expired.');
define('NON_PAYMENT_EXPIRED_HEADER_TEXT', 'Sorry! Your payment time has been expired.');
define('REQUEST_REJECTED_HEADER_TEXT', "Sorry! This property is not available for your\nselected dates.");
define('NEW_REQUEST_HEADER_TEXT', "We’re checking availability with the host!\nOnce confirmed, you can proceed to pay.");
define('REQUEST_APPROVED_HEADER_TEXT', 'Pay the amount before your request expire');
define('BOOKING_CANCELLED_BY_TRAVELLER_HEADER_TEXT', '');
define('BOOKING_CANCELLED_HEADER_TEXT', '');
define('BOOKING_CONFIRMED_HEADER_TEXT', '');
define('BOOKING_COMPLETED_HEADER_TEXT', '');

// Booking request status text for host.
define('HOST_AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_TEXT', 'Cancelled');
define('HOST_CANCELLED_BY_GH_AS_TRAVELLER_TEXT', 'Cancelled');
define('HOST_CANCELLED_BY_GH_AS_HOST_TEXT', 'Cancelled');
define('HOST_INVENTORY_FULL_CANCELLATION_TEXT', 'Cancelled');
define('HOST_AUTOMATION_CANCEL_REQUEST_TEXT', 'Cancelled');
define('HOST_LOADED_BOOKING_REQUEST_TEXT', 'Pending');
define('HOST_NO_RESPONSE_EXPIRY_TEXT', 'Expired');
define('HOST_TEST_BOOKING_TEXT', 'New request');
define('HOST_REQUEST_CANCELLED_TEXT', 'Cancelled');
define('HOST_EXPIRED_TEXT', 'Expired');
define('HOST_NO_REPLY_TEXT', 'Expired');
define('HOST_REQUEST_REJECTED_TEXT', 'Rejected');
define('HOST_NEW_REQUEST_TEXT', 'New request');
define('HOST_REQUEST_APPROVED_TEXT', 'Approved');
define('HOST_BOOKED_TEXT', 'Confirmed');
define('HOST_REQUEST_TO_CANCEL_AFTER_PAYMENT_TEXT', 'Cancelled');
define('HOST_CANCELLED_AFTER_PAYMENT_TEXT', 'Cancelled');
define('HOST_NON_AVAILABILITY_REFUND_TEXT', 'Cancelled');
define('HOST_CANCEL_AFTER_RELEASED_PAYMENT_TEXT', 'Cancelled');
define('HOST_CANCELLED_BY_HOST_AFTER_PAYMENT_TEXT', 'Cancelled');
define('HOST_OVERBOOKED_TEXT', 'Confirmed');
define('HOST_CANCELLED_AFTER_OVERBOOKED_TEXT', 'Cancelled');
define('HOST_BOOKING_SWITCHED_TEXT', 'Cancelled');
define('HOST_CANCEL_OFFLINE_BOOKING_TEXT', 'Cancelled');
define('HOST_CANCELLED_BY_GH_AFTER_PAYMENT_AS_HOST_TEXT', 'Cancelled');
define('HOST_CANCELLED_BY_GH_AFTER_PAYMENT_AS_TRAVELLER_TEXT', 'Cancelled');
define('HOST_AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_AFTER_PAYMENT_TEXT', 'Cancelled');

// Host Booking Request Status Header Text.
define('HOST_REQUEST_CANCELLED_BY_TRAVELLER_HEADER_TEXT', "We are really sorry!\nThis request is cancelled by traveller");
define('HOST_REQUEST_CANCELLED_HEADER_TEXT', "We are really sorry!\nThis request is cancelled");
define('HOST_EXPIRED_HEADER_TEXT', 'Sorry! Your request has been expired.');
define('HOST_REQUEST_REJECTED_HEADER_TEXT', 'Oh! You have Rejected this request.');
define('HOST_NEW_REQUEST_HEADER_TEXT', "We have blocked this home for the guest!\nAccept request before the timer runs out.");
define('HOST_REQUEST_APPROVED_HEADER_TEXT', "Thanks for approving request!\nWe will inform as traveller complete payment.");
define('HOST_BOOKING_CANCELLED_BY_TRAVELLER_HEADER_TEXT', "We are really sorry!\nThis booking is cancelled by traveller");
define('HOST_BOOKING_CANCELLED_HEADER_TEXT', "We are really sorry!\nThis booking is cancelled");
define('HOST_BOOKING_CONFIRMED_HEADER_TEXT', '');
define('HOST_BOOKING_COMPLETED_HEADER_TEXT', '');


// Host booking status.
define('HOST_BOOKING_CONFIRMED', 1);
define('HOST_BOOKING_CANCELLED', 2);
define('HOST_BOOKING_COMPLETED', 3);
define('HOST_NEW_REQUEST', 4);
define('HOST_REQUEST_DECLINED', 5);
define('HOST_REQUEST_EXPIRED', 6);
define('HOST_REQUEST_CANCELLED', 7);
define('HOST_REQUEST_APPROVED', 8);

// Host Dashboard Notification Type.
define('HOST_FILTER_NEW_REQUEST', 1);
define('HOST_FILTER_CHECK_IN_TODAY', 2);
define('HOST_FILTER_UPCOMING_CHECKIN', 3);
define('HOST_FILTER_NEW_GUEST_REVIEW', 4);
define('HOST_FILTER_AWAITING_CONFIRMATION_BOOKING', 5);


// Maximum booking amount till cash on arrival is available.
define('COA_TILL_MAX_AMOUNT', 12000);
define('COA_CURRENCY', 'INR');
// Amounts are defined in which currencies.
define('COA_MIN_AMOUNT', 0);
// Minimum booking amount for cash on arrival.
define('COA_MAX_AMOUNT', 300000);
// Maximum booking amount to avail cash on arrival.
// COA CHARGE FOR PARTIAL PAYMENT.
define('APPLY_COA_CHARGE', 0);
// Set 1 if want to apply coa charges.
define('COA_MIN_CHARGE', 299);
// Guesthouser charges for coa to calculate partial payment.
define('COA_MIN_CHARGE_EXTRA', 2250);
// Guesthouser charges for coa to calculate partial payment.
define('COA_CHARGE_SLAB', 28000);
// Slab after which coa_charge will increase.
define('COA_CHARGE_PERCENTAGE', 5);
// Coa_charge percentage.
define('COA_CHARGE_PERCENTAGE_EXTRA', 8);
// Coa_charge percentage if amount more than slab price.
define('HOST_TRANSFER_PERCENTAGE', 5);

define('ROOM_TYPE', [1, 2, 3, 4]);

// Default dates.
define('BOOKING_DEFAULT_START_DAYS_FROM_TODAY_IN_SECONDS', (60 * 60 * 24 * 14));
define('BOOKING_DEFAULT_END_DAYS_FROM_TODAY_IN_SECONDS', (60 * 60 * 24 * 16));
define('DEFAULT_NUMBER_OF_DAYS', 2);
define('DEFAULT_NUMBER_OF_GUESTS', 1);
define('DEFAULT_NUMBER_OF_UNITS', 1);

// Early bird cahsback percantage.
define('EARLYBIRD_CB_45_DAYS', 5);
define('EARLYBIRD_CB_90_DAYS', 10);

// All payment method.
define(
    'ALL_PAYMENT_METHODS',
    [
        'coa_payment',
        'si_payment',
        'partial_payment',
        'full_payment',
        // 'pay_later_payment',
    ]
);

define('DEFAULT_PAYMENT_METHOD', 'full_payment');

define('APPROVAL_CLASS', 'approved');
define('EXPIRY_CLASS', 'expired');
define('CANCELLATION_CLASS', 'canceled');
define('REJECTION_CLASS', 'rejected');
define('AWAITING_CLASS', 'awaiting');
define('UPCOMING_CLASS', 'upcoming');
define('ONGOING_CLASS', 'ongoing');
define('COMPLETED_CLASS', 'completed');

// Booking request status color code.
define('APPROVAL_COLOR_CODE', '#00ba8c');
define('EXPIRY_COLOR_CODE', '#c4c4c4');
define('CANCELLATION_COLOR_CODE', '#f26565');
define('REJECTION_COLOR_CODE', '#f2a419');
define('AWAITING_COLOR_CODE', '#d8b215');
define('CONFIRMED_COLOR_CODE', '#7ac696');
define('COMPLETED_COLOR_CODE', '#989898');

// Booking request status host color code.
define('HOST_APPROVED_COLOR_CODE', '#7ac696');
define('HOST_NEW_REQUEST_COLOR_CODE', '#00ba8c');
define('HOST_REJECTION_COLOR_CODE', '#f2a419');
define('HOST_EXPIRY_COLOR_CODE', '#c4c4c4');
define('HOST_CONFIRMED_COLOR_CODE', '#7ac696');
define('HOST_CANCELLATION_COLOR_CODE', '#f26565');
define('HOST_COMPLETED_COLOR_CODE', '#989898');


// Coupon types.
define('MONEY_COUPON', '1');
define('PERCENTAGE_COUPON', '2');



// Booking request timer.
define('REQUEST_APPROVAL_TIME_MORNING', '03:30:00');
// For APPROVAL TIMER 9 AM IST.
define('REQUEST_APPROVAL_TIME_EVENING', '15:30:00');
// 8 PM.
define('REQUEST_APPROVAL_DAY_TIMER', 172800);
// 15 minutes like 8am - 8pm.
define('REQUEST_APPROVAL_NIGHT_TIMER', 172800);
// 12 hours like 8:01pm - 7:59 am.
define('PAYMENT_DAY_TIMER', 172800);
// 30 minutes like 8am - 8pm.
define('PAYMENT_NIGHT_TIMER', 172800);
// 12 hours like 8:01pm - 7:59 am.
// GST Percentage.
define('GST_PERCENTAGE_SLAB_1', 12);

define('GST_PERCENTAGE_SLAB_2', 18);

define('GST_PER_NIGHT_PRICE_SLAB_1', 1001);

define('GST_PER_NIGHT_PRICE_SLAB_2', 7501);

define('GH_GST_PERCENTAGE', 18);

define('SERVICE_FEE', 15);
define('GH_MARKUP_MAX_PERCENTAGE', 15);

// Payment tracking status.
define('PAYMENT_INITIATED', 0);
define('PAYMENT_FAILED', -1);
define('PAYMENT_SUCCESS', 1);
define('PAYMENT_VERIFICATION_PENDING', 2);

// Payment transfer status.
define('PAYMENT_TRANSFER_PENDING', 0);
define('PAYMENT_TRANSFER_DONE', 1);
define('PAYMENT_TRANSFER_REJECTED', 2);

// Trip status.
define('UPCOMING_TRIP', 1);
define('PAST_TRIP', 2);
define('ONGOING_TRIP', 3);




if (APP_IS_LIVE === true) {
    // Payu credentials.
    define('INR_MERCHANT_ID', 'Uvq1b9');
    define('INR_SALT', 'kGJL6xqw');
    // Old live: ID: Qrl3Uc  SALT: JfYsqadX.
    // New live: ID: Uvq1b9  SALT: kGJL6xqw.
} else {
    // Payu credentials.
    define('INR_MERCHANT_ID', 'izBZET');
    // Live: Qrl3Uc loacl:gtKFFx.
    define('INR_SALT', 'cSm9ih1D');
    // Live: JfYsqadX  test: eCwWELxi.
}

if (APP_IS_LIVE === true) {
    define('PAYU_BASE_URL', 'https://secure.payu.in');
} else {
    define('PAYU_BASE_URL', 'https://test.payu.in');
}

// Payu payment urls.
define('PAYU_SUCCESS_URL', SITE_URL.'/v1.6/payment/paysuccess');
define('PAYU_PARTIALPAY_SUCCESS_URL', SITE_URL.'/v1.6/payment/partialpaysuccess');
define('PAYU_REFUND_SUCCESS_URL', SITE_URL.'/v1.6/payment/payrefundsuccess');
define('PAYU_REFUND_FAILURE_URL', SITE_URL.'/v1.6/payment/payrefundfail');
define('PAYU_FAILURE_URL', SITE_URL.'/v1.6/payment/payfail');
define('PAYU_PARTIALPAY_FAILURE_URL', SITE_URL.'/v1.6/payment/partialpayfail');

// Razorpay Payment urls.
define('RAZORPAY_SUCCESS_URL', SITE_URL.'/v1.6/payment/paysuccess');
define('RAZORPAY_PARTIALPAY_SUCCESS_URL', SITE_URL.'/v1.6/payment/partialpaysuccess');
define('RAZORPAY_FAILURE_URL', SITE_URL.'/v1.6/payment/payfail');
define('RAZORPAY_PARTIALPAY_FAILURE_URL', SITE_URL.'/v1.6/payment/partialpayfail');

define('PAYU_COA_SUCCESS_URL', SITE_URL.'/v1.6/payment/paysuccess?payment_option=coa');


define('PAYU_CASHCARD_SUCCESS_URL', SITE_URL.'/v1.6/payment/cashcardpaysuccess');
define('PAYU_CASHCARD_FAILURE_URL', SITE_URL.'/v1.6/payment/cashcardpayfail');
define('PAYU_CASHCARD_CANCEL_URL', SITE_URL.'/v1.6/payment/cashcardpaycancel');


if (APP_IS_LIVE === true) {
    define('PAYU_API_ENDPOINT', 'https://info.payu.in/merchant/postservice');
} else {
    define('PAYU_API_ENDPOINT', 'https://test.payu.in/merchant/postservice');
}

define('PAYU_ID', '1');
define('PAYU_NAME', 'PAYU_INR');


// RAZORPAY.
if (APP_IS_LIVE === true) {
    define('RAZORPAY_MERCHANT_ID', 'rzp_live_aqFFdPUY2P7k5K');
    define('RAZORPAY_API_ENDPOINT', 'https://api.razorpay.com/v1');
    define('RAZORPAY_SECRET', '3mWfrkWcYHiBbRMgWpJW11w5');
} else {
    define('RAZORPAY_MERCHANT_ID', 'rzp_test_UfVVfXPtkcB8Vj');
    define('RAZORPAY_API_ENDPOINT', 'https://api.razorpay.com/v1');
    define('RAZORPAY_SECRET', 'vxbYunpyj237NsicUfO3Xt0g');
}




// Booking payment option.
define('FULL_PAYMENT', 1);
define('CASH_ON_ARRIVAL', 2);

define('ONE_RUPPE_PAYMENT', 3);
define('SI_PAYMENT', 4);
// Standing Instruction Payment.
define('SI_AUTO_RECCURING_PAYMENT', 5);

// Pay later zero payment for instant book.
define('PAY_LATER_ZERO_PAYMENT', 6);

define('DEFAULT_PAYMENT_CURRENCY', 'INR');

define('PAYMENT_NO', ['full_payment' => 1, 'coa_payment' => 2, 'si_payment' => 4, 'partial_payment' => 2, 'pay_later_payment' => 6]);
define(
    'PAYMENT_OPTION_TEXT',
    [
        1 => [
            'code' => 'full_payment',
            'text' => 'Full Payment',
        ],
        4 => [
            'code' => 'si_payment',
            'text' => 'Charge Later',
        ],
        2 => [
            'code' => 'partial_payment',
            'text' => 'Partial Payment',
        ],
        6 => [
            'code' => 'pay_later_payment',
            'text' => 'Pay Later',
        ],
    ]
);


define('PAYMENT_ICONS_URL', CDN_URL.'public/payment_icons_2x/');
define('COA_PAYMENT_ICON_2X', PAYMENT_ICONS_URL.'Full_cash.png');
define('PAY_LATER_PAYMENT_ICON_2X', PAYMENT_ICONS_URL.'Pay_later_old.png');
define('SI_PAYMENT_ICON_2X', PAYMENT_ICONS_URL.'Pay_later.png');
define('PARTIAL_PAYMENT_ICON_2X', PAYMENT_ICONS_URL.'Parital.png');
define('FULL_PAYMENT_ICON_2X', PAYMENT_ICONS_URL.'Full_payment.png');



define('APP_PAYMENT_METHOD', ['method' => env('APP_PAYMENT_METHOD', 'sdk'), 'gateway' => env('APP_PAYMENT_GATEWAY', 'razorpay')]);

// Payout show date.
define('PAYOUT_DATA_SHOW_DATE', '2016-09-01');
define('DEFAULT_SI_PAYMENT_METHOD_GATEWAY', ['GATEWAY_ID' => 1, 'GATEWAY_NAME' => 'payu']);

// Refund Status.
define('REFUND_REQUESTED', -1);
define('REFUND_INITIATED', 0);
define('REFUND_PROCESSED', 1);

// Refund text.
define('REFUND_REQUESTED_TEXT', 'Requested');
define('REFUND_INITIATED_TEXT', 'Initiated');
define('REFUND_PROCESSED_TEXT', 'Processed');

// Refund Class.
define('REFUND_REQUESTED_CLASS', 'refund_requested');
define('REFUND_INITIATED_CLASS', 'refund_initiated');
define('REFUND_PROCESSED_CLASS', 'refund_processed');

// Wallet.
define('MAX_REDEEMABALE_WALLET_MONEY_PERCENTAGE', 20);
define('DAILY_WALLET_USAGE_LIMIT', 5000);
define('MAXIMUM_WALLET_MONEY_EARNED_MONTH', 10000);
define('MAX_TOTAL_MONEY_FOR_REFERRAL', 1000);
define('MAX_MONEY_FOR_FRIEND_BONUS', 300);
define('MAX_MONEY_FOR_FIRST_BOOKING', 700);
define('REVIEW_CASHBACK_PERCENTAGE', 5);
define('MAX_CASHBACK_FOR_REVIEW', 1000);

// Wallet money for review.
define('WALLET_MONEY_FOR_REVIEW', 1000);

define('MAXIMUM_WALLET_MONEY_INR', 10000);
define('MAXIMUM_WALLET_MONEY_USD', 200);
define('MAX_MONEY_FOR_SIGN_UP', 1000);
define('MAX_MONEY_FOR_FRIEND_REFERRAL_BONUS', 300);
define('MAXIMUM_WALLET_MONEY_EARNED_IN_MONTH', 10000);
define('MAXIMUM_WALLET_MONEY_EARNED_FOR_REFERRAL', 5000);

define('WALLET_MONEY_REWARD_CURRENCY', 'INR');
define('MAXIMUM_WALLET_MONEY_EARNED_FOR_REFERRER', 5000);
define('MAXIMUM_WALLET_MONEY_EARNED_FOR_REFERRER_BOOKING', 5000);
// Review data.
define('TRIP_AND_REVIEW_WALLET_MONEY', 65);
define('TRIP_AND_REVIEW_WALLET_MONEY_WITH_IMAGE', 650);

// Wallet screen events for app.
define('WALLET_EVENT_SHARE_CONTACT', 1);
define('WALLET_EVENT_WRITE_REVIEW', 2);

// Wallet events.
define(
    'WALLET_EVENTS',
    [
        '1'  => [
            'code'        => 'INITIAL_WALLET_BALANCE',
            'description' => 'Accumulated wallet balance',
        ],
        '2'  => [
            'code'        => 'INVITE_USER_GMAIL_CONTACT',
            'description' => 'Shared Gmail contacts',
        ],
        '3'  => [
            'code'        => 'INVITE_PHONE_CONTACT',
            'description' => 'Shared phone contact',
        ],
        '4'  => [
            'code'        => 'TRIP_AND_REVIEW',
            'description' => 'Reviewed a trip',
        ],
        '5'  => [
            'code'        => 'APPLY_WALLET_MONEY',
            'description' => 'Wallet money used',
        ],
        '6'  => [
            'code'        => 'WALLET_MONEY_EXPIRED',
            'description' => 'Wallet money expired',
        ],
        '7'  => [
            'code'        => 'SIGNUP_SHARE_VIA_FACEBOOK',
            'description' => 'Shared post on Facebook wall',
        ],
        '8'  => [
            'code'        => 'PROMOTIONAL_CREDITS',
            'description' => 'Promotional credits',
        ],
        '9'  => [
            'code'        => 'BOOKING_CANCELLATION_WALLET_CASHBACK',
            'description' => 'Wallet cashback on booking cancellation',
        ],
        '10' => [
            'code'        => 'COUPON_CASHBACK',
            'description' => 'Coupon cashback',
        ],
        '11' => [
            'code'        => 'EARLYBIRD_BOOKING_CASHBACK',
            'description' => 'Earlybird booking cashback',
        ],
        '12' => [
            'code'        => 'FRIEND_REFERRAL_BONUS',
            'description' => 'Bonus on successful referral signup',
        ],
        '13' => [
            'code'        => 'REFERRAL_FIRST_BOOKING_BONUS',
            'description' => 'Bonus on successful friend\'s first booking',
        ],
        '14' => [
            'code'        => 'REFERRAL_BONUS',
            'description' => 'Referral bonus',
        ],
    ]
);


define('INITIAL_WALLET_BALANCE', 1);
define('INVITE_USER_GMAIL_CONTACT', 2);
define('INVITE_PHONE_CONTACT', 3);
define('TRIP_AND_REVIEW', 4);
define('APPLY_WALLET_MONEY', 5);
define('WALLET_MONEY_EXPIRED', 6);
define('SIGNUP_SHARE_VIA_FACEBOOK', 7);
define('PROMOTIONAL_CREDITS', 8);
define('BOOKING_CANCELLATION_WALLET_CASHBACK', 9);
define('COUPON_CASHBACK', 10);

// Wallet events.
define('EARLYBIRD_BOOKING_CASHBACK', 11);
define('FRIEND_REFERRAL_BONUS', 12);
define('REFERRAL_FIRST_BOOKING_BONUS', 13);
define('REFERRAL_BONUS', 14);




// Wallet referral image.
define('WALLET_REFERRAL_IMAGE_URL', CDN_URL.'public/earn_more/wallet_referral.png');
// Wallet review image.
define('WALLET_REVIEW_IMAGE_URL', CDN_URL.'public/earn_more/wallet_reviews.png');

// Channel manager.
define('CM_BCOM', 1);
define('CM_TRAVIATE', 2);
define('CM_AIRBNB', 4);

// Prive Booking Sort status.
define('PRIVE_BOOKING_SORT_BY_CHECKIN', 1);
define('PRIVE_BOOKING_SORT_BY_CHECKOUT', 2);
define('PRIVE_BOOKING_SORT_BY_AMOUNT', 3);


// Prive Manage Checkedin Status.
define('PRIVE_MANAGER_UPCOMING', 1);
define('PRIVE_MANAGER_CHECKEDIN', 2);
define('PRIVE_MANAGER_CHECKEDOUT', 3);
define('PRIVE_MANAGER_NO_SHOW', 4);
define('PRIVE_MANAGER_COMPLETED', 5);
define('PRIVE_MANAGER_CANCELLED', 6);

// Prive Booking Status.
define('PRIVE_BOOKED', 1);
define('PRIVE_CANCELLED', 2);

// Properly booking start date.
define('PROPERLY_BOOOKING_START_DATE', '2019-06-01');

// Properly Task status.
define('PRIVE_TASK_OPEN', 1);
define('PRIVE_TASK_TODO', 2);
define('PRIVE_TASK_PENDING', 3);
define('PRIVE_TASK_COMPLETED', 4);

// Properly task color_code.
define('TASK_OPEN_COLOR_CODE', '#f62525');
define('TASK_TODO_COLOR_CODE', '#0099fa');
define('TASK_PENDING_COLOR_CODE', '#fa9e00');
define('TASK_COMPLETED_COLOR_CODE', '#2fa549');

// Properly Task Types.
define('TASK_TYPE_CHECKIN', 1);
define('TASK_TYPE_CHECKOUT', 2);
define('TASK_TYPE_OCCUPIED_SERVICE', 3);
define('TASK_TYPE_TURN_DOWN_SERVICE', 4);
define('TASK_TYPE_DEPARTURE_SERVICE', 5);
define('TASK_TYPE_MAINTAINENCE_SERVICE', 6);

// Properly Task Reccuring type.
define('RECCURING', 1);
define('NOT_RECCURING', 2);


// Task Hash.
define('HASH_SALT_FOR_TASK', '5678fa12cacgade9070b8edee54dg514x');
define('HASH_LENGTH_FOR_TASK', 5);
define('HASH_CHAR_FOR_TASK', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789');

// Service Task Timing.
define('OCCUPIED_SERVICE_TIME', '11:00:00');
define('TURN_DOWN_SERVICE_TIME', '17:00:00');

// Properly entity Type.
define('ENTITY_TYPE_BOOKING', 1);
define('ENTITY_TYPE_PROPERTY', 2);

// Properly Expense Type.
define('EXPENSE_FIXED', 1);
define('EXPENSE_VARIABLE', 2);
