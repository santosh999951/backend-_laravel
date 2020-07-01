<?php
/**
 * Success code related constants.
 **/

// Success 2xx.
define('OK_HTTP_STATUS_CODE', 200);
define('CREATED_HTTP_STATUS_CODE', 201);

// Error 4xx.
define('BAD_REQUEST_HTTP_STATUS_CODE', 400);
define('UNAUTHORIZED_HTTP_STATUS_CODE', 401);
define('PAYMENT_REQUIRED_HTTP_STATUS_CODE', 402);
define('FORBIDDEN_HTTP_STATUS_CODE', 403);
define('NOT_FOUND_HTTP_STATUS_CODE', 404);

// Error 5xx.
define('INTERNAL_SERVER_ERROR_HTTP_STATUS_CODE', 500);
define('BAD_GATEWAY_HTTP_STATUS_CODE', 502);
define('SERVICE_UNAVAILABLE_HTTP_STATUS_CODE', 503);

// Error code.
define('ERROR_CODE_EMAIL_ALREADY_REGISTERED', 'We already have an account associated with this email.');
define('ERROR_CODE_USER_NOT_CREATED', 'There was some error while signing you up. Please try again after some time.');
define('ERROR_CODE_INVALID_EMAIL', 'Invalid email.');
define('ERROR_CODE_INVALID_MOBILE', 'Invalid contact number.');
define('ERROR_CODE_OTP_LIMIT', 'You have reached maximum otp limit. Please reset through link send in email!');
define('ERROR_CODE_MOBILE_NOT_VERIFIED', 'Mobile number not verified. Please login using email!');
define('ERROR_CODE_INVALID_MOBILE_LENGTH', 'Invalid contact number length.');
define('ERROR_CODE_INVALID_PASSWORD_LENGTH', 'New password should be of minimum 6 characters.');
define('ERROR_CODE_INVALID_OTP', 'Invalid verification code.');
define('ERROR_CODE_EMAIL_NOT_REGISTERED', 'Email id not registered.');
define('ERROR_CODE_INVALID_RESET_PASSWORD_TOKEN', 'Invalid reset token!');
define('ERROR_CODE_FILE_UPLOAD_FAILED', 'There was some error while file upload. Please try again after some time.');
define('ERROR_CODE_EMAIL_CONTACT_ALREADY_REGISTERED', 'We already have an account associated with this email or contact number.');
define('ERROR_CODE_RESET_PASSWORD_OTP_LIMIT_REACHED', 'You have reached maximum otp limit! Please try sometime later.');
define('ERROR_CODE_OTP_GENERATION_FAILED', 'Internal error while generating the otp.');
define('ERROR_CODE_CONTACT_NOT_FOUND', 'Contact not found.');

// Error Codes (EC).
define('EC_VALIDATION_FAILED', 'validation_failed');
define('EC_CONTACT_NOT_VERIFIED', 'contact_not_verified');
define('EC_PAYMENT_METHOD_NOT_AVAILABLE', 'payment_method_not_available');
define('EC_RATING_ALREADY_SUBMITTED', 'rating_already_submitted');
define('EC_REVIEW_ALREADY_SUBMITTED', 'review_already_submitted');
define('EC_SERVER_ERROR', 'server_error');
define('EC_DUPLICATE_USER', 'duplicate_user');
define('EC_INVALID_ACCESS_TOKEN', 'invalid_access_token');
define('EC_BAD_GATEWAY', 'bad_gateway');
define('EC_SERVICE_UNAVIALABLE', 'service_unavailable');
define('EC_CONTACT_UNCHANGED', 'contact_unchanged');
define('EC_DUPLICATE_CONTACT', 'duplicate_contact');
define('EC_INVALID_OTP_METHOD', 'invalid_otp_method');
define('EC_INVALID_OTP', 'invalid_otp');
define('EC_NOT_FOUND', 'not_found');
define('EC_PROPERTY_NOT_IN_WISHLIST', 'property_not_in_wishlist');
define('EC_AUTH_TOKEN_MISSING', 'auth_token_missing');
define('EC_FILE_NOT_UPLOADED', 'file_not_uploaded');
define('EC_UNAUTHORIZED', 'unauthorized');
define('EC_FORBIDDEN', 'not_allowed');
define('EC_PAYMENT_GATEWAY_NOT_AVAILABLE', 'payment_gateway_not_available');
define('EC_PAYMENT_GATEWAY_NOT_SUPPORTED', 'gateway_not_supported');
define('EC_INVALID_ID_TOKEN', 'invalid_id_token');
define('EC_RESET_PASSWORD_OTP_LIMIT_REACHED', 'reset_password_otp_limit_reached');
