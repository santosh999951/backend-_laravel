<?php
/**
 * Property related constants
 */

// Hash.
define('HASH_LENGTH_FOR_PROPERTY', 5);
define('HASH_CHAR_FOR_PROPERTY', 'A123BCDEFGHIJKLMNOPQRSTUVWXYZ456789');
define('HASH_SALT_FOR_PROPERTY', '7711fa12cacdde1450b8edee54de514a');

define('PARTIAL_PAYMENT_COA_MAX_AMOUNT', 300000);

// Maximum booking amount to avail partial payment and rest for cash on arrival.
define('PRIVE_PROPERTY_COA_MAX_AMOUNT', 12000);
// Maximum booking amount to avail cash on arrival for prive property.
define('PROPERTY_LABEL_COLOR', '183,183,183');
// #00a598.
define('PROPERTY_LABEL_COLOR_LATER_HEX', '#00BA8C');
define('PROPERTY_LABEL_COLOR_LATER_RGB', '0, 186, 140');

// Review status.
define('NEW_REVIEW', 0);
define('APPROVED_REVIEW', 1);
define('EDITED_REVIEW', 2);
define('REJECTED_REVIEW', 3);
define('ONLINE', 4);
define('OFFLINE', 5);

define('PROPERTY_STATUS_UNDER_REVIEW', 0);
define('PROPERTY_STATUS_MODIFIED', 2);
define('PROPERTY_STATUS_DEACTIVATED', 3);
define('PROPERTY_STATUS_ACTIVATED', 1);
define('PROPERTY_STATUS_DELETED', -1);

// If host has full refund cancellation policy for 1, 3, 7 or 14 days.
define('FULL_REFUND_CANCELLATION_POLICY', [6, 7, 8, 9]);

// Total properties and cities.
define('TOTAL_PROPERTIES_COUNT', '1,75,000');
define('TOTAL_CITIES_COUNT', '2,200');

// Amenities id.
define('SHARED_POOL_AMENITY_ID', 41);
define('PRIVATE_POOL_AMENITY_ID', 42);

// Search pagination count.
define('NUMBER_OF_PROPERTIES_PER_PAGE', 12);
define('MAX_NUMBER_OF_PROPERTIES_PER_PAGE', 50);
define('NON_FEATURED_PROPERTIES_PER_PAGE_IN_PERCENTAGE', 25);
define('STRICT_FEATURED_PROPERTIES_POSITIONS', [2, 7, 10, 14, 17]);

// Tag colors for property.
define('YELLOW_TAG_ARRAY', ['scenic', 'romantic', 'value-for-money', 'adventure', 'family-friendly', 'business-corporate', 'offbeat', 'city-centre', 'peaceful', 'outskirts', 'easy-accessibility', 'luxurious']);
// phpcs:ignore
define('GREEN_TAG_ARRAY', ['lake-facing', 'beach-facing', 'sea-view', 'beachside', 'mountain-view', 'valley-view', 'pool-facing', 'solo-travellers', 'backpackers', 'mall-nearby', 'female-friendly', 'market-nearby', 'near-eataries', 'near-pubs-bars', 'casino-nearby', 'forest-retreat', 'spiritual-retreat', 'river-view']);
define('GREY_TAG_ARRAY', ['top-booked', 'booked-today', 'best-rated-host', 'steal-deal', 'best-reviewed']);

define('YELLOW_TAG_ARRAY_COLOR_CODE', ['colorRgb' => '255,239,140', 'colorHex' => '#ffef8c', 'textRgb' => '183,172,39', 'textHex' => '#b7ac27']);
define('GREEN_TAG_ARRAY_COLOR_CODE', ['colorRgb' => '188,233,209', 'colorHex' => '#bce9d1', 'textRgb' => '53,182,147', 'textHex' => '#35b693']);
define('GREY_TAG_ARRAY_COLOR_CODE', ['colorRgb' => '243,243,243', 'colorHex' => '#f3f3f3', 'textRgb' => '151,149,149', 'textHex' => '#979595']);

define('HOW_TO_REACH_PROPERTY_URL', 'public/how_to_reach/');

// Space icon ids.
define('BATHROOM_ICON_ID', 26);
define('BEDROOM_ICON_ID', 27);
define('BED_ICON_ID', 28);
define('ENTIRE_HOME', 29);
define('HOMESTAY', 30);
define('GUEST_ICON_ID', 31);
define('PRIVATE_ROOM', 32);
define('SHARED_HOME', 33);
define('SHARED_ROOM', 34);
define('YAUT', 35);

// Amenity rank order mapping.
// phpcs:ignore
define('AMENITY_RANK', [1 => 18, 2 => 14, 3 => 1, 4 => 20, 5 => 10, 7 => 2, 8 => 8, 9 => 22, 12 => 3, 13 => 25, 14 => 12, 15 => 24, 16 => 28, 23 => 36, 33 => 31, 34 => 29, 35 => 34, 36 => 30, 37 => 16, 38 => 17, 39 => 4, 41 => 9, 42 => 7, 44 => 23, 45 => 35, 46 => 5, 49 => 26, 50 => 32, 51 => 6, 52 => 33, 53 => 19, 54 => 21, 55 => 15, 56 => 11, 57 => 13, 58 => 27]);


// Local entities.
define('ADMIN_USER_ID', 0);
define('ENTITY_ADMIN', 1);
define('ENTITY_HOST', 2);
define('ENTITY_TRAVELLER', 3);
define('ENTITY_PROPERTY', 4);

define('DEFAULT_LIMIT_FOR_PROPERTY_REVIEWS', 10);
define('DEFAULT_NUMBER_OF_PROPERTY_LISTED_BY_HOST', 5);
define('MAX_UNITS_FOR_A_PROPERTY', 200000);

// Payment texts.
define('PAY_AT_CHECKIN_TITLE', 'Pay at Check-In');
define('PAY_AT_CHECKIN_TEXT', 'Lock in this great price now. No card needed.');
define('SI_PAYMENT_TITLE', 'Book now, pay later');
define('SI_PAYMENT_TEXT', 'Block this great price before we are sold out! You need to pay only {{policy_days}} days before check in.');
define('PARTIAL_PAYMENT_TITLE', 'Partial payment');
define('PARTIAL_PAYMENT_TEXT', 'Block this great rate by paying partial amount.');
define('FULL_PAYMENT_TITLE', 'Full Payment');
define('FULL_PAYMENT_TEXT', 'Block this great rate by paying the amount.');

// Refundable title and text.
define('TOTAL_AMOUNT_REFUNDABLE_TITLE', '100% Refundable');
define('TOTAL_AMOUNT_REFUNDABLE_TEXT', 'Cancel up to {{cancellation_days}} days before your trip and get a full refund.');


define('FREE_CANCELLATION_TITLE', 'Free cancellation');
define('FREE_CANCELLATION_TEXT', 'Pay at Check-In');

// Property address key.
define('PROPERTY_ADDRESS_KEY', ')H@McQfTjWnZr4u7x!A%D*F-JaNdRgUk');

// Admin ID for Property Review.
define('PROPERTY_REVIEW_ADMIN_ID', 542);

// Default number of collections to show.
define('DEFAULT_NUMBER_OF_COLLECTIONS', 5);

// Default number of collection properties.
define('DEFAULT_NUMBER_OF_COLLECTIONS_PROPERTIES', 6);

// Hash ids.
define('HASH_SALT_FOR_COLLECTION', '7711fa12cacdde1450b8edee54de514a');
define('HASH_LENGTH_FOR_COLLECTION', 5);
define('HASH_CHAR_FOR_COLLECTION', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789');

// Property Video status.
define('VIDEO_HIDDEN', 0);
define('VIDEO_READY_TO_SHOW', 1);
define('VIDEO_PENDING_FOR_ENCODING', 2);
define('VIDEO_IN_PROGRESS_FOR_ENCODING', 3);
define('VIDEO_IN_ERROR', 4);
