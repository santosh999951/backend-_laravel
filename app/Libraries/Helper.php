<?php
/**
 * Helper containing methods used commonly amongst controllers and services
 */

namespace App\Libraries;


use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use \Torann\GeoIP\Facades\GeoIP as GeoIP;
use \Hashids\Hashids;
use \Carbon\Carbon;
use \Google_Client;
use Illuminate\Support\Facades\View;
use App\Models\CurrencyConversion;
use App\Libraries\v1_6\AwsService;

/**
 * Class Helper
 */
class Helper
{


    /**
     * Get user ip address
     *
     * @return string Return user ipaddress
     */
    public static function getUserIpAddress()
    {
        return GeoIP::getClientIP();

    }//end getUserIpAddress()


    /**
     * Get user ip address
     *
     * @param string $ip_address IP address for location.
     *
     * @return array Return user location details
     */
    public static function getLocationByIp(string $ip_address)
    {
        $location = GeoIP::getLocation($ip_address);
        return [
            'country_code' => (empty($location->getAttribute('iso_code')) === false && $location->getAttribute('iso_code') !== 'NA') ? $location->getAttribute('iso_code') : '',
            'country'      => (empty($location->getAttribute('country')) === false && $location->getAttribute('country') !== 'NA') ? $location->getAttribute('country') : '',
            'city'         => (empty($location->getAttribute('city')) === false && $location->getAttribute('city') !== 'NA') ? $location->getAttribute('city') : '',
            'state'        => (empty($location->getAttribute('state')) === false && $location->getAttribute('state') !== 'NA') ? $location->getAttribute('state') : '',
            'state_name'   => (empty($location->getAttribute('state_name')) === false && $location->getAttribute('state_name') !== 'NA') ? $location->getAttribute('state_name') : '',
            'postal_code'  => (empty($location->getAttribute('postal_code')) === false && $location->getAttribute('postal_code') !== 'NA') ? $location->getAttribute('postal_code') : '',
            'lat'          => (empty($location->getAttribute('lat')) === false && $location->getAttribute('lat') !== 'NA') ? $location->getAttribute('lat') : '',
            'lon'          => (empty($location->getAttribute('lon')) === false && $location->getAttribute('lon') !== 'NA') ? $location->getAttribute('lon') : '',
            'currency'     => (empty($location->getAttribute('currency')) === false && $location->getAttribute('currency') !== 'NA') ? $location->getAttribute('currency') : '',
        ];

    }//end getLocationByIp()


    /**
     * Generate referral code using user id
     *
     * @param integer $user_id User id.
     *
     * @return string Return generated referral code
     */
    public static function generateReferralCode(int $user_id)
    {
        return self::_hash(HASH_SALT_REFFERAL, HASH_LENGTH_FOR_ID_REFFERAL, HASH_CHAR_FOR_ID_REFFERAL, $user_id);

    }//end generateReferralCode()


    /**
     * Curl for hitting urls.
     *
     * @param string $url            Url to hit curl request on.
     * @param string $post_params    Params to be passed for curl request.
     * @param string $request_method Http request method.
     * @param string $access_token   Authorization token.
     * @param array  $extra_headers  Extra Headers.
     *
     * @return mixed Curl request response.
     */
    public static function sendCurlRequest(string $url, string $post_params='', string $request_method='GET', string $access_token=null, array $extra_headers=[])
    {
        $curl = curl_init();

        if (empty($curl) === true) {
            return false;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if ($request_method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_params);
        }

        // Set headers.
        $curlheader = [];
        if ($access_token !== null) {
            array_push($curlheader, 'Authorization: Bearer '.$access_token);
        }

        if (empty($extra_headers) === false) {
            foreach ($extra_headers as $type => $value) {
                array_push($curlheader, $type.': '.$value);
            }
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheader);

        $json_response = curl_exec($curl);

        $curl_errno = curl_errno($curl);
        $curl_error = curl_error($curl);

        curl_close($curl);

        return $json_response;

    }//end sendCurlRequest()


    /**
     * Generate hash id for a property from property id
     *
     * @param integer $property_id Property id.
     *
     * @return string Return generated hash id
     */
    public static function encodePropertyId(int $property_id)
    {
        return 'P'.self::_hash(HASH_SALT_FOR_PROPERTY, HASH_LENGTH_FOR_PROPERTY, HASH_CHAR_FOR_PROPERTY, $property_id);

    }//end encodePropertyId()


    /**
     * Get property id from a property hash id
     *
     * @param string $hash_id Property hash code.
     *
     * @return integer|string Return decoded property id
     */
    public static function decodePropertyHashId(string $hash_id)
    {
        // Use ctype_digit instead of is_numeric because is_numeric consider HEX, OCTAL etc. as numeric eg. 00X12 is hex number.
        // Due these issues. check seperate digit by ctype_digit method.
        if (ctype_digit($hash_id) === true && $hash_id > 0) {
            return $hash_id;
        } else {
            if ($hash_id[0] === 'P') {
                $hash_id = substr($hash_id, 1);
            }

            return self::_unhash(HASH_SALT_FOR_PROPERTY, HASH_LENGTH_FOR_PROPERTY, HASH_CHAR_FOR_PROPERTY, $hash_id);
        }

    }//end decodePropertyHashId()


    /**
     * Generate encripted address for a property from property address
     *
     * @param string $address Property address.
     *
     * @return string Return generated address
     */
    public static function encodePropertyAddress(string $address)
    {
        return openssl_encrypt($address, 'AES-128-ECB', PROPERTY_ADDRESS_KEY);

    }//end encodePropertyAddress()


    /**
     * Get property address from a property encripted address
     *
     * @param string $encripted_address Property encripted address.
     *
     * @return string Return decripted property address
     */
    public static function decodePropertyAddress(string $encripted_address)
    {
        return openssl_decrypt($encripted_address, 'AES-128-ECB', PROPERTY_ADDRESS_KEY);

    }//end decodePropertyAddress()


    /**
     * Generate hash id for a booking request from booking reqquest id
     *
     * @param integer $booking_request_id Request id.
     *
     * @return string Return generated hash id
     */
    public static function encodeBookingRequestId(int $booking_request_id)
    {
        return self::_hash(HASH_SALT_FOR_BOOKING_REQUEST_ID, HASH_LENGTH_FOR_BOOKING_REQUEST_ID, HASH_CHAR_FOR_BOOKING_REQUEST_ID, $booking_request_id);

    }//end encodeBookingRequestId()


    /**
     * Get booking reqquest id from a booking request hash id
     *
     * @param string $hash_id Booking hash code.
     *
     * @return integer|string Return booking request id
     */
    public static function decodeBookingRequestId(string $hash_id)
    {
        // Use ctype_digit instead of is_numeric because is_numeric consider HEX, OCTAL etc. as numeric eg. 00X12 is hex number.
        // Due these issues. check seperate digit by ctype_digit method.
        // Commented because unable to decode numeric hash id.
        // phpcs:disable
        // if (ctype_digit($hash_id) === true) {
        //     return $hash_id;
        // }
        // phpcs:enable

        return self::_unhash(HASH_SALT_FOR_BOOKING_REQUEST_ID, HASH_LENGTH_FOR_BOOKING_REQUEST_ID, HASH_CHAR_FOR_BOOKING_REQUEST_ID, $hash_id);

    }//end decodeBookingRequestId()


     /**
      * Generate hash id for a user from user id
      *
      * @param integer $user_id User id.
      *
      * @return string Return generated hash id
      */
    public static function encodeUserId(int $user_id)
    {
        return 'U'.self::_hash(HASH_SALT_FOR_USER, HASH_LENGTH_FOR_USER, HASH_CHAR_FOR_USER, $user_id);

    }//end encodeUserId()


     /**
      * Get booking reqquest id from a booking request hash id
      *
      * @param string $user_id Booking hash code.
      *
      * @return integer|string Return booking request id
      */
    public static function decodeUserId(string $user_id)
    {
        // Use ctype_digit instead of is_numeric because is_numeric consider HEX, OCTAL etc. as numeric eg. 00X12 is hex number.
        // Due these issues. check seperate digit by ctype_digit method.
        if (ctype_digit($user_id) === true) {
            return $user_id;
        } else {
            if ($user_id[0] === 'U') {
                $user_id = substr($user_id, 1);
            }

            return self::_unhash(HASH_SALT_FOR_USER, HASH_LENGTH_FOR_USER, HASH_CHAR_FOR_USER, $user_id);
        }

        return 0;

    }//end decodeUserId()


    /**
     * Generate hash value from id.
     *
     * @param string  $salt          Salt value.
     * @param integer $hash_length   Length for hash code.
     * @param string  $hash_charset  Character set for hash.
     * @param integer $value_to_hash Value to hash.
     *
     * @return string Return encoded hash.
     */
    // phpcs:ignore
    private static function _hash(string $salt, int $hash_length, string $hash_charset, int $value_to_hash)
    {
        $hashids = new Hashids($salt, $hash_length, $hash_charset);

        return $hashids->encode($value_to_hash);

    }//end _hash()


    /**
     * Generate numeric value from hash.
     *
     * @param string  $salt          Salt value.
     * @param integer $hash_length   Length for hash code.
     * @param string  $hash_charset  Character set for hash.
     * @param string  $value_to_hash Value to hash.
     *
     * @return integer Return int id.
     */
    // phpcs:ignore
    private static function _unhash(string $salt, int $hash_length, string $hash_charset, string $value_to_hash)
    {
        $hashids = new Hashids($salt, $hash_length, $hash_charset);

        try {
            $property_id = $hashids->decode($value_to_hash);
            if (count($property_id) > 0) {
                return $property_id[0];
            }

            return '';
        } catch (\Exception $e) {
            return '';
        }

    }//end _unhash()


    /**
     * Encode Array.
     *
     * @param array $data Array.
     *
     * @return string Return generated hash id
     */
    public static function encodeArray(array $data)
    {
        return self::_hashArray(HASH_SALT_FOR_ARRAY, HASH_LENGTH_FOR_ARRAY, HASH_CHAR_FOR_ARRAY, $data);

    }//end encodeArray()


     /**
      * Decode to Array
      *
      * @param string $hash Hash code.
      *
      * @return array Return Unhash array
      */
    public static function decodeArray(string $hash)
    {
        return self::_unhashArray(HASH_SALT_FOR_ARRAY, HASH_LENGTH_FOR_ARRAY, HASH_CHAR_FOR_ARRAY, $hash);

    }//end decodeArray()


     /**
      * Generate hash value from array.
      *
      * @param string  $salt          Salt value.
      * @param integer $hash_length   Length for hash code.
      * @param string  $hash_charset  Character set for hash.
      * @param array $value_to_hash Value to hash.
      *
      * @return string Return encoded hash.
      */
    // phpcs:ignore
    private static function _hashArray(string $salt, int $hash_length, string $hash_charset, array $value_to_hash)
    {
        $hashids = new Hashids($salt, $hash_length, $hash_charset);

        return $hashids->encode($value_to_hash);

    }//end _hashArray()


    /**
     * Generate array value from hash.
     *
     * @param string  $salt          Salt value.
     * @param integer $hash_length   Length for hash code.
     * @param string  $hash_charset  Character set for hash.
     * @param string  $value_to_hash Value to hash.
     *
     * @return array Return int id.
     */
    // phpcs:ignore
    private static function _unhashArray(string $salt, int $hash_length, string $hash_charset, string $value_to_hash)
    {
        $hashids = new Hashids($salt, $hash_length, $hash_charset);

        try {
            $property_id = $hashids->decode($value_to_hash);
            if (count($property_id) > 0) {
                return $property_id[0];
            }

            return '';
        } catch (\Exception $e) {
            return '';
        }

    }//end _unhashArray()


    /**
     * Short Property Title.
     *
     * @param string  $title  Property Title.
     * @param integer $length New Length Of Title.
     *
     * @return string
     */
    public static function shortPropertyTitle(string $title, int $length=10)
    {
        if (strlen($title) <= $length) {
            return $title;
        } else {
            return substr($title, 0, $length).'...';
        }

    }//end shortPropertyTitle()


    /**
     * Get formatted location.
     *
     * @param string $area           Area provided.
     * @param string $city           City provided.
     * @param string $state          State provided.
     * @param string $search_keyword Search Keyword.
     *
     * @return string "Area, city" or "area, state" or "city, state", whichever given.
     */
    public static function formatLocation(string $area='', string $city='', string $state='', string $search_keyword='')
    {
        $location = [];

        // Make all upper case.
        $area           = ucfirst($area);
        $city           = ucfirst($city);
        $state          = ucfirst($state);
        $search_keyword = ucfirst($search_keyword);

        if (empty($search_keyword) === false) {
            // State is goa then add search keyword in middle else first.
            if (strtolower($state) === 'goa') {
                if (empty($city) === false) {
                    $location[] = $city;
                }

                $location[] = preg_replace('/^[^,]*-\s*/', '', ucwords($search_keyword));
                if (empty($state) === false) {
                    $location[] = $state;
                }
            } else {
                $location[] = ucwords($search_keyword);
                if (empty($city) === false) {
                    $location[] = $city;
                }

                if (empty($state) === false) {
                    $location[] = $state;
                }
            }
        } else if (empty($area) === false) {
            $location[] = $area;
            if (strtolower($area) === strtolower($city)) {
                // If area and city are same then skip city.
                if (empty($state) === false) {
                    $location[] = $state;
                }
            } else {
                // If area and city are different then add city and skip state.
                if (empty($city) === false) {
                    $location[] = $city;
                }

                if (empty($state) === false) {
                    $location[] = $state;
                }
            }
        } else {
            if (empty($city) === false) {
                $location[] = $city;
            }

            if (empty($state) === false) {
                $location[] = $state;
            }
        }//end if

        return implode(', ', $location);

    }//end formatLocation()


    /**
     * Get price converted from one currency to other.
     *
     * @param string $from_currency Currency to convert.
     * @param float  $amount        Amount to convert.
     * @param string $to_currency   Currency to convert into.
     *
     * @return float $converted_amount In new currency
     */
    public static function convertPriceToCurrentCurrency(string $from_currency, float $amount, string $to_currency)
    {
        $countries_currency_exchange_rates = CurrencyConversion::getAllCurrencyDetails();

        if ($from_currency === $to_currency) {
            return $amount;
        }

        // Currency exchange rates.
        $from_currency_rate = $countries_currency_exchange_rates[$from_currency]['exchange_rate'];
        $to_currency_rate   = $countries_currency_exchange_rates[$to_currency]['exchange_rate'];

        // Amount in usd.
        $in_usd = ($amount / $from_currency_rate);

        // Converted amount.
        $converted_amount = ($in_usd * $to_currency_rate);
        return $converted_amount;

    }//end convertPriceToCurrentCurrency()


    /**
     * Get price without Service Fee.
     *
     * @param float $amount                        Amount.
     * @param float $service_fee_percentage        Service Fee Percentage.
     * @param float $markup_service_fee_percentage Markup Fee Percentage.
     *
     * @return float $amount Amount
     */
    public static function getPriceWithoutServiceFee(float $amount, float $service_fee_percentage, float $markup_service_fee_percentage)
    {
        $base_price = (($amount) / ((100 / (100 - $service_fee_percentage)) + ($markup_service_fee_percentage / 100)));

        $service_fee = (($base_price * $service_fee_percentage) / (100 - $service_fee_percentage));

        return ($amount - $service_fee);

    }//end getPriceWithoutServiceFee()


    /**
     * Joins parts of query to build complete query.
     *
     * @param array $data Contains params to build a query(select, from, where, having etc.).
     *
     * @return string $query Query built from passed params.
     */
    public static function buildQuery(array $data)
    {
        $select = $data['select'];
        $from   = $data['from'];
        $where  = (array_key_exists('where', $data) === true) ? $data['where'] : false;
        $having = (array_key_exists('having', $data) === true) ? $data['having'] : false;
        $group  = (array_key_exists('group', $data) === true) ? $data['group'] : false;
        $order  = (array_key_exists('order', $data) === true) ? $data['order'] : false;
        $limit  = (array_key_exists('limit', $data) === true) ? $data['limit'] : false;

        $query  = ' SELECT ';
        $query .= implode($select, ', ');
        $query .= ' FROM ';
        $query .= implode($from, ' ');
        $query .= ' WHERE ';
        $query .= implode($where, ' AND ');

        if (empty($group) === false) {
            $query .= ' GROUP BY ';
            $query .= implode($group, ', ');
        }

        if (empty($having) === false) {
            $query .= ' HAVING ';
            $query .= implode($having, ', ');
        }

        if (empty($order) === false) {
            $query .= ' ORDER BY ';
            $query .= implode($order, ', ');
        }

        if (empty($limit) === false) {
            $query .= ' LIMIT ';
            $query .= implode($limit, ', ');
        }

        return $query;

    }//end buildQuery()


    /**
     * Returns the pagination limits for all pages in search results for featured and non featured properties.
     *
     * @param array $pagination_inputs Array containing per page featured/non featured properties count.
     *
     * @return array
     */
    public static function getPaginationLimits(array $pagination_inputs)
    {
        // Input params.
        $per_page                   = $pagination_inputs['per_page'];
        $total_featured_results     = $pagination_inputs['total_featured_results'];
        $total_non_featured_results = $pagination_inputs['total_non_featured_results'];

        // Total numbers of records available to display.
        $total_available_records = ($total_featured_results + $total_non_featured_results);

        // Non featured properties count will be 25% of all property shown.
        $non_featured_properties_per_page = ((int) floor((NON_FEATURED_PROPERTIES_PER_PAGE_IN_PERCENTAGE / 100) * $per_page) > 1) ? (int) floor((NON_FEATURED_PROPERTIES_PER_PAGE_IN_PERCENTAGE / 100) * $per_page) : 1;

        // Total number of different records to display per page.
        $records_per_page = [
            'non_featured' => $non_featured_properties_per_page,
        // By default 3.
            'featured'     => ($per_page - $non_featured_properties_per_page),
        // By default 9.
        ];

        // Total number of pages based on per page items.
        $total_number_of_pages = (int) ceil($total_available_records / $per_page);

        // Pagination response array.
        $pagination = [
            'total_number_of_pages'   => $total_number_of_pages,
            'featured_pagination'     => [],
            'non_featured_pagination' => [],
        ];

        // Offsets.
        $featured_offset     = 0;
        $non_featured_offset = 0;

        // Calculate number of featured and non featured propertes per page.
        for ($i = 0; $i < $total_number_of_pages; $i++) {
            // Per page featured properties.
            // Per page records page.
            $per_page_total_records_left = $per_page;

            $per_page_featured_properties = ($total_featured_results >= $records_per_page['featured']) ? $records_per_page['featured'] : $total_featured_results;

            $per_page_total_records_left -= $per_page_featured_properties;

            // Per page non featured properties.
            $per_page_non_featured_properties = ($total_non_featured_results >= $per_page_total_records_left) ? $per_page_total_records_left : $total_non_featured_results;

            // Remaining featured results.
            $total_featured_results -= $per_page_featured_properties;
            // Remaining non featured results.
            $total_non_featured_results -= $per_page_non_featured_properties;

            $per_page_total_records_left -= $per_page_non_featured_properties;

            // Check if per page records are added or not.
            // If not then check again.
            if ($per_page_total_records_left > 0) {
                if ($total_featured_results >= $per_page_total_records_left) {
                    // Featured results left is greater than or equal per page records left.
                    $per_page_featured_properties += $per_page_total_records_left;
                    $total_featured_results       -= $per_page_total_records_left;
                    $per_page_total_records_left  -= $per_page_total_records_left;
                } else {
                    // Featured results left is less than per page records left.
                    $per_page_featured_properties += $total_featured_results;
                    $total_featured_results       -= $total_featured_results;
                    $per_page_total_records_left  -= $total_featured_results;
                }
            }

            // Per page offset and total properties.
            $pagination['featured'][]     = [
                'offset' => $featured_offset,
                'total'  => $per_page_featured_properties,
            ];
            $pagination['non_featured'][] = [
                'offset' => $non_featured_offset,
                'total'  => $per_page_non_featured_properties,
            ];

            // Featured and non featured properties ffset for each page.
            $featured_offset     += $per_page_featured_properties;
            $non_featured_offset += $per_page_non_featured_properties;
        }//end for

        return $pagination;

    }//end getPaginationLimits()


    /**
     * Returns the rounded min value for slider.
     *
     * @param float $slider_min_value Minimum value for budget filter in search.
     *
     * @return float
     */
    public static function roundOfSliderMinValue(float $slider_min_value)
    {
        $len = strlen((string) $slider_min_value);
        if ($slider_min_value < 10) {
            return $slider_min_value;
        }

        $round_to = pow(10, ($len - 1));
        return (floor($slider_min_value / $round_to) * $round_to);

    }//end roundOfSliderMinValue()


    /**
     * Returns the rounded max value for slider.
     *
     * @param float $slider_max_value Maximum value for budget filter in search.
     *
     * @return integer
     */
    public static function roundOfSliderMaxValue(float $slider_max_value)
    {
        $len      = strlen((string) $slider_max_value);
        $round_to = pow(10, ($len - 1));
        return (ceil($slider_max_value / $round_to) * $round_to);

    }//end roundOfSliderMaxValue()


    /**
     * Generate hash id for a collection from collection id.
     *
     * @param integer $collection_id Collection id(integer).
     *
     * @return string Return generated hash id.
     */
    public static function encodeCollectionId(int $collection_id)
    {
        return self::_hash(HASH_SALT_FOR_COLLECTION, HASH_LENGTH_FOR_COLLECTION, HASH_CHAR_FOR_COLLECTION, $collection_id);

    }//end encodeCollectionId()


    /**
     * Get collection id from a collection hash id.
     *
     * @param string $collection_hash_id Collection hash code.
     *
     * @return integer|string Return decoded collection id.
     */
    public static function decodeCollectionHashId(string $collection_hash_id)
    {
        // Use ctype_digit instead of is_numeric because is_numeric consider HEX, OCTAL etc. as numeric eg. 00X12 is hex number.
        // Due these issues. check seperate digit by ctype_digit method.
        if (ctype_digit($collection_hash_id) === true) {
            return $collection_hash_id;
        }

        return self::_unhash(HASH_SALT_FOR_COLLECTION, HASH_LENGTH_FOR_COLLECTION, HASH_CHAR_FOR_COLLECTION, $collection_hash_id);

    }//end decodeCollectionHashId()


    /**
     * Check if password string is of required minimum length
     *
     * @param string $password Password string.
     *
     * @return boolean
     */
    public static function validatePasswordLength(string $password)
    {
        if (strlen($password) < 6) {
            return false;
        }

        return true;

    }//end validatePasswordLength()


    /**
     * Generate an avatar for user based on gender or return profile pic.
     *
     * @param string  $gender      User gender.
     * @param string  $profile_img User profile image name.
     * @param integer $user_id     User id.
     *
     * @return string Avatar url.
     */
    // phpcs:ignore
    public static function generateProfileImageUrl($gender='Male', string $profile_img='', int $user_id=null)
    {
        if (empty($user_id) === true) {
            return S3_AVATAR_MEN.'1.png';
            // FOR MEN.
        }

        $user_id_last_digit = (int) substr((string) $user_id, -1);

        if (empty($profile_img) === true) {
            if (empty($gender) === true) {
                return S3_AVATAR_MEN.(($user_id_last_digit === 0) ? 10 : $user_id_last_digit ).'.png';
                // FOR MEN | Men Avatar range are from 1-10.
            } else if (strtolower($gender) === 'male') {
                return S3_AVATAR_MEN.(($user_id_last_digit === 0) ? 10 : $user_id_last_digit ).'.png';
                // FOR MEN | Men Avatar range are from 1-10.
            } else {
                return S3_AVATAR_WOMEN.((empty($user_id_last_digit) === true) ? 20 : '1'.$user_id_last_digit).'.png';
                // FOR Women | Women Avatar range are from 11-20.
            }
        } else {
            return S3_PROFILE_PIC_FOLDER_URL.$profile_img;
        }

    }//end generateProfileImageUrl()


    /**
     * Get COA charges guest has to bear in case of partial payment.
     *
     * @param float  $price    Total amount to calculate coa charges for.
     * @param string $currency User currency.
     *
     * @return array Coa charges.
     */
    public static function coaChargesForGuesthouser(float $price, string $currency)
    {
        if (APPLY_COA_CHARGE === 0) {
            return [
                'coa_fee'                 => 0,
                'coa_fee_percentage_slab' => 0,
            ];
        }

        $coa_amount_limit = self::convertPriceToCurrentCurrency(DEFAULT_CURRENCY, COA_CHARGE_SLAB, $currency);

        if ($price <= $coa_amount_limit) {
            $min_coa_charge          = self::convertPriceToCurrentCurrency(DEFAULT_CURRENCY, COA_MIN_CHARGE, $currency);
            $coa_charge              = round((COA_CHARGE_PERCENTAGE * $price / 100), 2);
            $coa_fee_percentage_slab = COA_CHARGE_PERCENTAGE;
        } else {
            $min_coa_charge          = self::convertPriceToCurrentCurrency(DEFAULT_CURRENCY, COA_MIN_CHARGE_EXTRA, $currency);
            $coa_charge              = round((COA_CHARGE_PERCENTAGE_EXTRA * $price / 100), 2);
            $coa_fee_percentage_slab = COA_CHARGE_PERCENTAGE_EXTRA;
        }

        $coa_fee = ($min_coa_charge > $coa_charge) ? $min_coa_charge : $coa_charge;

        return [
            'coa_fee'                 => $coa_fee,
            'coa_fee_percentage_slab' => $coa_fee_percentage_slab,
        ];

    }//end coaChargesForGuesthouser()


    /**
     * Check if amount paid by user is refundable or not.
     *
     * @param integer $policy_days  Policy days applcable.
     * @param string  $checkin_date Checkin date for booking.
     *
     * @return boolean true/false if amount is refundable/not refundable
     */
    public static function isAmountRefundable(int $policy_days, string $checkin_date)
    {
        $now          = Carbon::now('GMT');
        $checkin_date = Carbon::parse($checkin_date);
        $diff         = $checkin_date->diffInDays($now);

        if ($now < $checkin_date && $diff >= $policy_days && $policy_days > 0) {
            return true;
        }

        return false;

    }//end isAmountRefundable()


    /**
     * Return amount of service charge to be paid by user (in amount).
     *
     * @param float $after_price Amount after adding service fee.
     * @param float $service_fee Percentage of service fee.
     *
     * @return float Service fee amount.
     */
    public static function calculateServiceCharge(float $after_price, float $service_fee)
    {
        return ($after_price - (($after_price * 100) / (100 + $service_fee)));

    }//end calculateServiceCharge()


    /**
     * Calculate upfront amount to be paid by user if he/she opts for partial payment.
     *
     * @param float   $total_price          Total amount to be paid by user.
     * @param float   $service_fee          Total service fee to be paid by user.
     * @param float   $gst                  Total gst amount.
     * @param integer $prive                Is property prive or not.
     * @param float   $commission_from_host Commission to be paid to guesthouser.
     * @param float   $coa_fee              To be paid for opting for partial payment.
     * @param float   $markup_service_fee   Markup service fee.
     * @param float   $host_fee             Host fee.
     *
     * @return array upfront and remaining amount to be paid by user
     */
    public static function calculateCoaUpfrontAmount(float $total_price, float $service_fee, float $gst, int $prive, float $commission_from_host, float $coa_fee, float $markup_service_fee, float $host_fee)
    {
        // Need to remove markup price and replace host_fee with total_price when all changes done on website.
        if ($commission_from_host > 0 && $host_fee > 0) {
            $gh_commission_from_host = round((($host_fee * $commission_from_host) / 100), 2);
        } else {
            $gh_commission_from_host = 0;
        }

        // Non prive properties.
        if ($prive === 0) {
            $net_commission       = ((($service_fee > 0) ? $service_fee : 0 ) + $gh_commission_from_host + $coa_fee);
            $host_transfer_amount = ($host_fee > 0) ? round((($host_fee - $gh_commission_from_host) * (HOST_TRANSFER_PERCENTAGE / 100)), 2) : 0;

            $coa_upfront_amount = ($net_commission + $host_transfer_amount);
        } else {
            $net_commission       = ((($service_fee > 0) ? $service_fee : 0) + $coa_fee);
            $host_transfer_amount = ($host_fee > 0) ? round((($host_fee - $gh_commission_from_host) * (HOST_TRANSFER_PERCENTAGE / 100)), 2) : 0;

            $coa_upfront_amount = ($net_commission + $host_transfer_amount);
        }

        $coa_upfront_amount += $gst;
        $coa_upfront_amount += $markup_service_fee;
        $remaining_amount    = ($total_price - $coa_upfront_amount);

        return [
            'coa_upfront_amount' => $coa_upfront_amount,
            'remaining_amount'   => $remaining_amount,
        ];

    }//end calculateCoaUpfrontAmount()


    /**
     * Return string containing text to be displayed for corresponding booking status.
     *
     * @param integer $status Booking status.
     *
     * @return array Booking text, status and class.
     */
    public static function getBookingStatusTextAndClass(int $status)
    {
        $text       = '';
        $class      = EXPIRY_CLASS;
        $color_code = EXPIRY_COLOR_CODE;
        switch ($status) {
            case AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED:
                $text        = AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = REQUEST_CANCELLED_BY_HOST_HEADER_TEXT;
            break;

            case CANCELLED_BY_GH_AS_TRAVELLER:
                $text        = CANCELLED_BY_GH_AS_TRAVELLER_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = REQUEST_CANCELLED_BY_HOST_HEADER_TEXT;
            break;

            case CANCELLED_BY_GH_AS_HOST:
                $text        = CANCELLED_BY_GH_AS_HOST_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = REQUEST_CANCELLED_BY_HOST_HEADER_TEXT;
            break;

            case INVENTORY_FULL_CANCELLATION:
                $text        = INVENTORY_FULL_CANCELLATION_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = REQUEST_CANCELLED_BY_HOST_HEADER_TEXT;
            break;

            case AUTOMATION_CANCEL_REQUEST:
                $text        = AUTOMATION_CANCEL_REQUEST_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = REQUEST_CANCELLED_BY_HOST_HEADER_TEXT;
            break;

            case LOADED_BOOKING_REQUEST:
                $text        = LOADED_BOOKING_REQUEST_TEXT;
                $class       = AWAITING_CLASS;
                $color_code  = AWAITING_COLOR_CODE;
                $header_text = NEW_REQUEST_HEADER_TEXT;
            break;

            case NO_RESPONSE_EXPIRY:
                $text        = NO_RESPONSE_EXPIRY_TEXT;
                $class       = EXPIRY_CLASS;
                $color_code  = EXPIRY_COLOR_CODE;
                $header_text = NON_PAYMENT_EXPIRED_HEADER_TEXT;
            break;

            case REQUEST_CANCELLED:
                $text        = REQUEST_CANCELLED_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = REQUEST_CANCELLED_HEADER_TEXT;
            break;

            case EXPIRED:
                $text        = EXPIRED_TEXT;
                $class       = EXPIRY_CLASS;
                $color_code  = EXPIRY_COLOR_CODE;
                $header_text = EXPIRED_HEADER_TEXT;
            break;

            case NO_REPLY:
                $text        = NO_REPLY_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = NON_PAYMENT_EXPIRED_HEADER_TEXT;
            break;

            case REQUEST_REJECTED:
                $text        = REQUEST_REJECTED_TEXT;
                $class       = REJECTION_CLASS;
                $color_code  = REJECTION_COLOR_CODE;
                $header_text = REQUEST_REJECTED_HEADER_TEXT;
            break;

            case NEW_REQUEST:
                $text        = NEW_REQUEST_TEXT;
                $class       = AWAITING_CLASS;
                $color_code  = AWAITING_COLOR_CODE;
                $header_text = NEW_REQUEST_HEADER_TEXT;
            break;

            case REQUEST_APPROVED:
                $text        = REQUEST_APPROVED_TEXT;
                $class       = APPROVAL_CLASS;
                $color_code  = APPROVAL_COLOR_CODE;
                $header_text = REQUEST_APPROVED_HEADER_TEXT;
            break;

            case BOOKED:
                $text        = BOOKED_TEXT;
                $class       = APPROVAL_CLASS;
                $color_code  = APPROVAL_COLOR_CODE;
                $header_text = BOOKING_CONFIRMED_HEADER_TEXT;
            break;

            case REQUEST_TO_CANCEL_AFTER_PAYMENT:
                $text        = REQUEST_TO_CANCEL_AFTER_PAYMENT_TEXT;
                $class       = APPROVAL_CLASS;
                $color_code  = APPROVAL_COLOR_CODE;
                $header_text = BOOKING_CANCELLED_BY_TRAVELLER_HEADER_TEXT;
            break;

            case CANCELLED_AFTER_PAYMENT:
                $text        = CANCELLED_AFTER_PAYMENT_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = BOOKING_CANCELLED_BY_TRAVELLER_HEADER_TEXT;
            break;

            case CANCELLED_BY_HOST_AFTER_PAYMENT:
                $text        = CANCELLED_AFTER_PAYMENT_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case NON_AVAILABILITY_REFUND:
                $text        = NON_AVAILABILITY_REFUND_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case CANCELLED_BY_HOST_AFTER_PAYMENT:
                $text        = CANCELLED_BY_HOST_AFTER_PAYMENT_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case OVERBOOKED:
                $text        = OVERBOOKED_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case CANCELLED_AFTER_OVERBOOKED:
                $text        = CANCELLED_AFTER_OVERBOOKED_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case BOOKING_SWITCHED:
                $text        = BOOKING_SWITCHED_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case CANCEL_OFFLINE_BOOKING:
                $text        = CANCEL_OFFLINE_BOOKING_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case CANCELLED_BY_GH_AFTER_PAYMENT_AS_HOST:
                $text        = CANCELLED_BY_GH_AFTER_PAYMENT_AS_HOST_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case CANCELLED_BY_GH_AFTER_PAYMENT_AS_TRAVELLER:
                $text        = CANCELLED_BY_GH_AFTER_PAYMENT_AS_TRAVELLER_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_AFTER_PAYMENT:
                $text        = AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_AFTER_PAYMENT_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = CANCELLATION_COLOR_CODE;
                $header_text = BOOKING_CANCELLED_HEADER_TEXT;
            break;

            default:
                $text        = '';
                $class       = EXPIRY_CLASS;
                $color_code  = EXPIRY_COLOR_CODE;
                $header_text = '';
            break;
        }//end switch

        return [
            'text'        => $text,
            'class'       => $class,
            'color_code'  => $color_code,
            'status'      => $status,
            'header_text' => $header_text,
        ];

    }//end getBookingStatusTextAndClass()


    /**
     * Return string containing text to be displayed for corresponding booking status.
     *
     * @param integer $status    Booking status.
     * @param string  $from_date Check in date.
     * @param string  $to_date   Check out date.
     *
     * @return array Booking text, status and class.
     */
    public static function getTripStatusTextAndClassForMsite(int $status, string $from_date, string $to_date)
    {
        $status_text  = COMPLETED_TEXT;
        $status_class = COMPLETED_CLASS;
        if ($from_date > Carbon::now('Asia/Kolkata')->format('Y-m-d')) {
            $status_text  = UPCOMING_TEXT;
            $status_class = UPCOMING_CLASS;
        } else if ($from_date <= Carbon::now('Asia/Kolkata')->format('Y-m-d') && $to_date >= Carbon::now('Asia/Kolkata')->format('Y-m-d')) {
            $status_text  = ONGOING_TEXT;
            $status_class = ONGOING_CLASS;
        }

        $cancelled_status_array = [
            REQUEST_TO_CANCEL_AFTER_PAYMENT,
            CANCELLED_AFTER_PAYMENT,
            CANCELLED_BY_HOST_AFTER_PAYMENT,
            NON_AVAILABILITY_REFUND,
            OVERBOOKED,
            CANCELLED_AFTER_OVERBOOKED,
            BOOKING_SWITCHED,
            CANCEL_OFFLINE_BOOKING,
            CANCELLED_BY_GH_AFTER_PAYMENT_AS_HOST,
            CANCELLED_BY_GH_AFTER_PAYMENT_AS_TRAVELLER,
            AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_AFTER_PAYMENT,
        ];

        if (in_array($status, $cancelled_status_array) === true) {
            $status_text  = CANCELLED_TEXT;
            $status_class = CANCELLATION_CLASS;
        }

        return [
            'text'        => $status_text,
            'class'       => $status_class,
            'status'      => $status,
            'header_text' => '',
        ];

    }//end getTripStatusTextAndClassForMsite()


    /**
     * Return string containing status.
     *
     * @param string $status Booking status.
     *
     * @return array Booking status.
     */
    public static function getTripStatusViaStatusClass(string $status)
    {
        switch (strtolower($status)) {
            case strtolower(COMPLETED_CLASS):
            return [
                BOOKED,
                BOOKING_SWITCHED,
            ];

            default:
            return [];
        }

        return [];

    }//end getTripStatusViaStatusClass()


    /**
     * Return array containing Refund status data.
     *
     * @param integer $status Refund status.
     *
     * @return array Refund text, status and class.
     */
    public static function getRefundStatus(int $status)
    {
        $refund_all_status = [
            'refund_status'    => [
                [
                    'text'   => REFUND_REQUESTED_TEXT,
                    'class'  => REFUND_REQUESTED_CLASS,
                    'status' => REFUND_REQUESTED,
                ],
                [
                    'text'   => REFUND_INITIATED_TEXT,
                    'class'  => REFUND_INITIATED_CLASS,
                    'status' => REFUND_INITIATED,
                ],
                [
                    'text'   => REFUND_PROCESSED_TEXT,
                    'class'  => REFUND_PROCESSED_CLASS,
                    'status' => REFUND_PROCESSED,
                ],
            ],
            // Default not defined that request accepted or not.
            'request_accepted' => -1,

            // Default set that request is initiated.
            'current_status'   => -1,

            // Extra Message.
            'message'          => '',
        ];

        // Code is rough just because of Refund request table process changed by Solution team.
        switch ($status) {
            // When refund requested.
            case 0:
                $refund_all_status['current_status'] = REFUND_REQUESTED;
            break;

            // When refund request confiremed.
            case 1:
                $refund_all_status['request_accepted'] = 1;
                $refund_all_status['current_status']   = REFUND_INITIATED;
            break;

            // When refund request rejected.
            case 2:
                $refund_all_status['request_accepted'] = 0;
                $refund_all_status['current_status']   = REFUND_INITIATED;
                $refund_all_status['message']          = 'Request has been cancelled. For more info please contact customer care.';
                // When refund request processed.
            break;

            case 3:
                $refund_all_status['request_accepted'] = 1;
                $refund_all_status['current_status']   = REFUND_PROCESSED;
                $refund_all_status['message']          = 'Your refund request has been processed.';
            break;

            // phpcs:ignore
            default:
            break;
        }//end switch

        return $refund_all_status;

    }//end getRefundStatus()


    /**
     * Return currency code exchange rate corresponding to us dollar.
     *
     * @param string $image_name Image name.
     *
     * @return string image extension
     */
    public static function getImageExtension(string $image_name)
    {
        $extension_position = strrpos($image_name, '.');

        if ($extension_position === false) {
            return '';
        }

        $image_name_length = (strlen($image_name) - $extension_position);
        $extension         = substr($image_name, ($extension_position + 1));

        return $extension;

    }//end getImageExtension()


    /**
     * Return currency code exchange rate corresponding to us dollar.
     *
     * @param float   $pernightprice Per night price for booking.
     * @param integer $room_type     Room type of selected room.
     * @param integer $bedrooms      No of bedrooms.
     * @param string  $currency      Country's currency code.
     *
     * @return integer gst percentage applicable
     */
    public static function calculateGstPercentage(float $pernightprice, int $room_type, int $bedrooms, string $currency)
    {
        $amount = 0;
        if ($room_type === 1) {
            $amount = round($pernightprice / $bedrooms);
        } else {
            $amount = $pernightprice;
        }

        if ($currency !== 'INR') {
             $amount = self::convertPriceToCurrentCurrency($currency, $amount, 'INR');
        }

        $gst = 0;

        if ($amount >= GST_PER_NIGHT_PRICE_SLAB_1 && $amount < GST_PER_NIGHT_PRICE_SLAB_2) {
            $gst = GST_PERCENTAGE_SLAB_1;
        } else if ($amount >= GST_PER_NIGHT_PRICE_SLAB_2) {
            $gst = GST_PERCENTAGE_SLAB_2;
        }

        return $gst;

    }//end calculateGstPercentage()


    /**
     * Return currency code exchange rate corresponding to us dollar.
     *
     * @param float   $host_amount             Per night price for booking.
     * @param integer $room_type               Room type of selected room.
     * @param integer $bedrooms                No of bedrooms.
     * @param string  $currency                Country's currency code.
     * @param integer $days                    Days.
     * @param integer $units                   Units.
     * @param float   $service_fee             Service fee.
     * @param float   $markup_service_fee      Markup service fee.
     * @param float   $gh_commission_from_host Gh commission from host.
     *
     * @return array gst percentage applicable
     */
    public static function calculateGstAmount(float $host_amount, int $room_type, int $bedrooms, string $currency, int $days, int $units, float $service_fee, float $markup_service_fee, float $gh_commission_from_host)
    {
        $per_night_host_amount = 0;
        if ($room_type === 1) {
            $per_night_host_amount = round((($host_amount / $bedrooms) / $days) / $units);
        } else {
            $per_night_host_amount = round(($host_amount / $days) / $units);
        }

        if ($currency !== 'INR') {
             $per_night_host_amount = self::convertPriceToCurrentCurrency($currency, $per_night_host_amount, 'INR');
        }

        $host_gst_percentage = 0;

        if ($per_night_host_amount >= GST_PER_NIGHT_PRICE_SLAB_1 && $per_night_host_amount < GST_PER_NIGHT_PRICE_SLAB_2) {
            $host_gst_percentage = GST_PERCENTAGE_SLAB_1;
        } else if ($per_night_host_amount >= GST_PER_NIGHT_PRICE_SLAB_2) {
            $host_gst_percentage = GST_PERCENTAGE_SLAB_2;
        }

        $host_gst = (($host_amount * $host_gst_percentage) / 100);

        $gh_amount         = ($service_fee + $markup_service_fee + $gh_commission_from_host);
        $gh_gst            = 0;
        $gh_gst_percentage = 0;
        if ($gh_amount > 0) {
            $gh_gst_percentage = GH_GST_PERCENTAGE;
            $gh_gst            = (($gh_amount * $gh_gst_percentage) / 100);
        }

        return [
            'gh_gst'              => $gh_gst,
            'gh_gst_percentage'   => $gh_gst_percentage,
            'host_gst_percentage' => $host_gst_percentage,
            'host_gst'            => $host_gst,
            'total_gst'           => ($gh_gst + $host_gst),
        ];

    }//end calculateGstAmount()


    /**
     * Return currency code exchange rate corresponding to us dollar.
     *
     * @param string $currency_code Country's currency code.
     *
     * @return float currency exchange rate w.r.t usd
     */
    public static function getCurrencyExchanegRate(string $currency_code)
    {
        $countries_currency_exchange_rates = CurrencyConversion::getAllCurrencyDetails();

        return $countries_currency_exchange_rates[$currency_code]['exchange_rate'];

    }//end getCurrencyExchanegRate()


    /**
     * Return amount in a number format.
     *
     * @param float   $amount        Amount.
     * @param string  $currency_code Currency for amount format.
     * @param boolean $round         To round off the amount or not.
     * @param boolean $with_webicon  With Webcoin Icon Flag.
     *
     * @return string
     */
    public static function getFormattedMoney(float $amount, string $currency_code, bool $round=true, bool $with_webicon=true)
    {
        setlocale(LC_MONETARY, 'en_IN');

        $currency = self::getCurrencySymbol($currency_code, $with_webicon);

        $symbol_type = ($with_webicon === true) ? 'webicon' : 'non-webicon';

        if ($currency === CURRENCY_SYMBOLS['INR'][$symbol_type]) {
            return ($round === true) ? $currency.money_format('%!.0i', $amount) : $currency.money_format('%!i', $amount);
        } else {
            return ($round === true) ? $currency.number_format(round($amount)) : $currency.number_format($amount, 2);
        }

    }//end getFormattedMoney()


    /**
     * Return amount in a number format without currency symbol.
     *
     * @param float   $amount   Amount.
     * @param string  $currency Currency for amount format.
     * @param boolean $round    To round off the amount or not.
     *
     * @return string
     */
    public static function getFormattedMoneyWithoutCurrency(float $amount, string $currency, bool $round=true)
    {
        setlocale(LC_MONETARY, 'en_IN');

        $currency = self::getCurrencySymbol($currency);

        if ($currency === CURRENCY_SYMBOLS['INR']['webicon']) {
            return ($round === true) ? money_format('%!.0i', $amount) : money_format('%!i', $amount);
        } else {
            return ($round === true) ? number_format(round($amount)) : number_format($amount, 2);
        }

    }//end getFormattedMoneyWithoutCurrency()


    /**
     * Return currency symbol.
     *
     * @param string  $currency   Currency code/symbol.
     * @param boolean $in_webicon Data in Webcoin Icon Flag.
     *
     * @return string
     */
    public static function getCurrencySymbol(string $currency, bool $in_webicon=true)
    {
        $currency_code = array_search($currency, array_column(CURRENCY_SYMBOLS, 'webicon', 'iso_code'));

        if ($currency_code === false) {
            $currency_code = array_search($currency, array_column(CURRENCY_SYMBOLS, 'non-webicon', 'iso_code'));
        }

        if ($currency_code === false) {
            $currency_code = $currency;
        }

        return ($in_webicon === true) ? CURRENCY_SYMBOLS[$currency_code]['webicon'] : CURRENCY_SYMBOLS[$currency_code]['non-webicon'];

    }//end getCurrencySymbol()


    /**
     * Return array containing db booking status corresponding to status displayed to host.
     *
     * @param integer $status Status displayed to host.
     *
     * @return array
     */
    public static function getDBStatusForHostStatus(int $status)
    {
        switch ($status) {
            case HOST_BOOKING_CONFIRMED:
                 $db_status = [
                     BOOKED,
                     OVERBOOKED,
                 ];
            break;

            case HOST_BOOKING_CANCELLED:
                 $db_status = [
                     REQUEST_TO_CANCEL_AFTER_PAYMENT,
                     CANCELLED_AFTER_PAYMENT,
                     NON_AVAILABILITY_REFUND,
                     CANCEL_AFTER_RELEASED_PAYMENT,
                     CANCELLED_BY_HOST_AFTER_PAYMENT,
                     CANCELLED_AFTER_OVERBOOKED,
                     BOOKING_SWITCHED,
                     CANCEL_OFFLINE_BOOKING,
                     CANCELLED_BY_GH_AFTER_PAYMENT_AS_HOST,
                     CANCELLED_BY_GH_AFTER_PAYMENT_AS_TRAVELLER,
                     AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_AFTER_PAYMENT,
                 ];
            break;

            case HOST_BOOKING_COMPLETED:
                 $db_status = [BOOKED];
            break;

            case HOST_NEW_REQUEST:
                 $db_status = [
                     LOADED_BOOKING_REQUEST,
                     TEST_BOOKING,
                     NEW_REQUEST,
                 ];
            break;

            case HOST_REQUEST_DECLINED:
                 $db_status = [REQUEST_REJECTED];
            break;

            case HOST_REQUEST_EXPIRED:
                 $db_status = [
                     NO_RESPONSE_EXPIRY,
                     EXPIRED,
                     NO_REPLY,
                 ];
            break;

            case HOST_REQUEST_CANCELLED:
                 $db_status = [
                     AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED,
                     CANCELLED_BY_GH_AS_TRAVELLER,
                     CANCELLED_BY_GH_AS_HOST,
                     INVENTORY_FULL_CANCELLATION,
                     AUTOMATION_CANCEL_REQUEST,
                     REQUEST_CANCELLED,
                 ];
            break;

            case HOST_REQUEST_APPROVED:
                 $db_status = [REQUEST_APPROVED];
            break;

            default:
                $db_status = [];
            break;
        }//end switch

        return $db_status;

    }//end getDBStatusForHostStatus()


    /**
     * Return array containing booking status of host.
     *
     * @param array $selected_status Selected Status.
     *
     * @return array
     */
    public static function getBookingStatusOfHost(array $selected_status=[])
    {
        $all_status = [
            [
                'status'   => HOST_BOOKING_CONFIRMED,
                'text'     => 'Confirmed Bookings',
                'selected' => (in_array(HOST_BOOKING_CONFIRMED, $selected_status) === true) ? 1 : 0,
            ],
            [
                'status'   => HOST_BOOKING_CANCELLED,
                'text'     => 'Cancelled Bookings',
                'selected' => (in_array(HOST_BOOKING_CANCELLED, $selected_status) === true) ? 1 : 0,
            ],
            [
                'status'   => HOST_BOOKING_COMPLETED,
                'text'     => 'Completed Bookings',
                'selected' => (in_array(HOST_BOOKING_COMPLETED, $selected_status) === true) ? 1 : 0,
            ],
        ];
        return $all_status;

    }//end getBookingStatusOfHost()


    /**
     * Return string containing text to be displayed for corresponding booking status to host.
     *
     * @param integer $status  Booking status.
     * @param string  $to_date To Date.
     *
     * @return array Booking text, status and class.
     */
    public static function getHostBookingStatusTextAndClass(int $status, string $to_date)
    {
        $text       = '';
        $class      = EXPIRY_CLASS;
        $color_code = EXPIRY_COLOR_CODE;
        switch ($status) {
            case AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED:
                $text        = HOST_AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_REQUEST_CANCELLED;
                $header_text = HOST_REQUEST_CANCELLED_HEADER_TEXT;
            break;

            case CANCELLED_BY_GH_AS_TRAVELLER:
                $text        = HOST_CANCELLED_BY_GH_AS_TRAVELLER_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_REQUEST_CANCELLED;
                $header_text = HOST_REQUEST_CANCELLED_HEADER_TEXT;
            break;

            case CANCELLED_BY_GH_AS_HOST:
                $text        = HOST_CANCELLED_BY_GH_AS_HOST_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_REQUEST_CANCELLED;
                $header_text = HOST_REQUEST_CANCELLED_HEADER_TEXT;
            break;

            case INVENTORY_FULL_CANCELLATION:
                $text        = HOST_INVENTORY_FULL_CANCELLATION_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_REQUEST_CANCELLED;
                $header_text = HOST_REQUEST_CANCELLED_HEADER_TEXT;
            break;

            case AUTOMATION_CANCEL_REQUEST:
                $text        = HOST_AUTOMATION_CANCEL_REQUEST_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_REQUEST_CANCELLED;
                $header_text = HOST_REQUEST_CANCELLED_HEADER_TEXT;
            break;

            case LOADED_BOOKING_REQUEST:
                $text        = HOST_LOADED_BOOKING_REQUEST_TEXT;
                $class       = AWAITING_CLASS;
                $color_code  = HOST_NEW_REQUEST_COLOR_CODE;
                $host_status = HOST_NEW_REQUEST;
                $header_text = HOST_NEW_REQUEST_HEADER_TEXT;
            break;

            case NO_RESPONSE_EXPIRY:
                $text        = HOST_NO_RESPONSE_EXPIRY_TEXT;
                $class       = EXPIRY_CLASS;
                $color_code  = HOST_EXPIRY_COLOR_CODE;
                $host_status = HOST_REQUEST_EXPIRED;
                $header_text = HOST_EXPIRED_HEADER_TEXT;
            break;

            case REQUEST_CANCELLED:
                $text        = HOST_REQUEST_CANCELLED_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_REQUEST_CANCELLED;
                $header_text = HOST_REQUEST_CANCELLED_BY_TRAVELLER_HEADER_TEXT;
            break;

            case EXPIRED:
                $text        = HOST_EXPIRED_TEXT;
                $class       = EXPIRY_CLASS;
                $color_code  = HOST_EXPIRY_COLOR_CODE;
                $host_status = HOST_REQUEST_EXPIRED;
                $header_text = HOST_EXPIRED_HEADER_TEXT;
            break;

            case NO_REPLY:
                $text        = HOST_NO_REPLY_TEXT;
                $class       = EXPIRY_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_REQUEST_EXPIRED;
                $header_text = HOST_EXPIRED_HEADER_TEXT;
            break;

            case REQUEST_REJECTED:
                $text        = HOST_REQUEST_REJECTED_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_REJECTION_COLOR_CODE;
                $host_status = HOST_REQUEST_DECLINED;
                $header_text = HOST_REQUEST_REJECTED_HEADER_TEXT;
            break;

            case NEW_REQUEST:
                $text        = HOST_NEW_REQUEST_TEXT;
                $class       = AWAITING_CLASS;
                $color_code  = HOST_NEW_REQUEST_COLOR_CODE;
                $host_status = HOST_NEW_REQUEST;
                $header_text = HOST_NEW_REQUEST_HEADER_TEXT;
            break;

            case REQUEST_APPROVED:
                $text        = HOST_REQUEST_APPROVED_TEXT;
                $class       = APPROVAL_CLASS;
                $color_code  = HOST_NEW_REQUEST_COLOR_CODE;
                $host_status = HOST_REQUEST_APPROVED;
                $header_text = HOST_REQUEST_APPROVED_HEADER_TEXT;
            break;

            case BOOKED:
                $text        = HOST_BOOKED_TEXT;
                $class       = APPROVAL_CLASS;
                $color_code  = HOST_CONFIRMED_COLOR_CODE;
                $host_status = HOST_BOOKING_CONFIRMED;
                $header_text = HOST_BOOKING_CONFIRMED_HEADER_TEXT;
            break;

            case REQUEST_TO_CANCEL_AFTER_PAYMENT:
                $text        = HOST_REQUEST_TO_CANCEL_AFTER_PAYMENT_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_BOOKING_CANCELLED;
                $header_text = HOST_BOOKING_CANCELLED_BY_TRAVELLER_HEADER_TEXT;
            break;

            case CANCELLED_AFTER_PAYMENT:
                $text        = HOST_CANCELLED_AFTER_PAYMENT_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_BOOKING_CANCELLED;
                $header_text = HOST_BOOKING_CANCELLED_BY_TRAVELLER_HEADER_TEXT;
            break;

            case NON_AVAILABILITY_REFUND:
                $text        = HOST_NON_AVAILABILITY_REFUND_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_BOOKING_CANCELLED;
                $header_text = HOST_BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case CANCELLED_BY_HOST_AFTER_PAYMENT:
                $text        = HOST_CANCELLED_BY_HOST_AFTER_PAYMENT_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_BOOKING_CANCELLED;
                $header_text = HOST_BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case OVERBOOKED:
                $text        = HOST_OVERBOOKED_TEXT;
                $class       = APPROVAL_CLASS;
                $color_code  = HOST_CONFIRMED_COLOR_CODE;
                $host_status = HOST_BOOKING_CONFIRMED;
                $header_text = HOST_BOOKING_CONFIRMED_HEADER_TEXT;
            break;

            case CANCELLED_AFTER_OVERBOOKED:
                $text        = HOST_CANCELLED_AFTER_OVERBOOKED_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_BOOKING_CANCELLED;
                $header_text = HOST_BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case BOOKING_SWITCHED:
                $text        = HOST_BOOKING_SWITCHED_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_BOOKING_CANCELLED;
                $header_text = HOST_BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case CANCEL_OFFLINE_BOOKING:
                $text        = HOST_CANCEL_OFFLINE_BOOKING_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_BOOKING_CANCELLED;
                $header_text = HOST_BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case CANCELLED_BY_GH_AFTER_PAYMENT_AS_HOST:
                $text        = HOST_CANCELLED_BY_GH_AFTER_PAYMENT_AS_HOST_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_BOOKING_CANCELLED;
                $header_text = HOST_BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case CANCELLED_BY_GH_AFTER_PAYMENT_AS_TRAVELLER:
                $text        = HOST_CANCELLED_BY_GH_AFTER_PAYMENT_AS_TRAVELLER_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_BOOKING_CANCELLED;
                $header_text = HOST_BOOKING_CANCELLED_HEADER_TEXT;
            break;

            case AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_AFTER_PAYMENT:
                $text        = HOST_AUTOMATION_CANCEL_AVAILABILITY_NOT_MARKED_AFTER_PAYMENT_TEXT;
                $class       = CANCELLATION_CLASS;
                $color_code  = HOST_CANCELLATION_COLOR_CODE;
                $host_status = HOST_BOOKING_CANCELLED;
                $header_text = HOST_BOOKING_CANCELLED_HEADER_TEXT;
            break;

            default:
                $text        = '';
                $class       = EXPIRY_CLASS;
                $color_code  = HOST_EXPIRY_COLOR_CODE;
                $host_status = '';
                $header_text = '';
            break;
        }//end switch

        // Today's date.
        $today = Carbon::now('Asia/Kolkata');

        $end_date_obj             = Carbon::parse($to_date);
        $no_of_days_from_checkout = $today->diffInDays($end_date_obj, false);

        $all_status_of_confirmed_booking = self::getDBStatusForHostStatus(HOST_BOOKING_CONFIRMED);
        if (in_array($status, $all_status_of_confirmed_booking) === true && $no_of_days_from_checkout < 0) {
            $text       = COMPLETED_TEXT;
            $class      = COMPLETED_CLASS;
            $color_code = HOST_COMPLETED_COLOR_CODE;
        }

        return [
            'text'        => $text,
            'class'       => $class,
            'color_code'  => $color_code,
            'status'      => $status,
            'header_text' => $header_text,
        ];

    }//end getHostBookingStatusTextAndClass()


     /**
      * Return website payment method name as per api new name.
      *
      * @param string $payment_method Payment method.
      *
      * @return string
      */
    public static function getOldPaymentMethodName(string $payment_method)
    {
        if ($payment_method === 'full_payment' || $payment_method === 'partial_payment') {
            return $payment_method;
        } else if ($payment_method === 'coa_payment') {
            return 'full_coa';
        } else if ($payment_method === 'si_payment') {
            return 'pay_later';
        } else {
            return 'full_payment';
        }

    }//end getOldPaymentMethodName()


    /**
     * Return website payment method name as per api new name.
     *
     * @param string $old_payment_method Payment method.
     *
     * @return string
     */
    public static function getNewPaymentMethodName(string $old_payment_method)
    {
        if ($old_payment_method === 'full_payment' || $old_payment_method === 'partial_payment') {
            return $old_payment_method;
        } else if ($old_payment_method === 'full_coa') {
            return 'coa_payment';
        } else if ($old_payment_method === 'pay_later') {
            return 'si_payment';
        } else {
            return 'full_payment';
        }

    }//end getNewPaymentMethodName()


    /**
     * Return location from from Googleapi.
     *
     * @param string  $location Location.
     * @param integer $map_type Map Type.
     *
     * @return array
     */
    public static function getLocationFromGoogleApi(string $location, int $map_type=null)
    {
        $adrs = [];
        if (empty($map_type) === true) {
            $map_type = AUTOCOMPLETE_APIS[DEFAULT_AUTOCOMPLETE_API];
        }

        if ($map_type === 1) {
            $get_place_detail = file_get_contents('https://maps.googleapis.com/maps/api/place/textsearch/json?query='.urlencode($location).'&key='.GOOGLE_PLACE_API_KEY);

            if (empty($get_place_detail) === false) {
                $places = json_decode($get_place_detail);
                try {
                    $place_id = (isset($places->results[0]->place_id) === true) ? $places->results[0]->place_id : '';
                } catch (\Exception $e) {
                    $place_id = '';
                }
            } else {
                $place_id = '';
            }

            if (empty($place_id) === false) {
                $adrs = self::getLocationFromPlaceId($place_id);
            }
        }

        if (empty($adrs) === true) {
            $adrs = self::getLocationFromDB($location);
        }//end if

        return $adrs;

    }//end getLocationFromGoogleApi()


    /**
     * Return location from from Googleapi.
     *
     * @param string $location Location.
     *
     * @return array
     */
    public static function getLocationFromDB(string $location)
    {
        $adrs = [];

        $adrs['country'] = '';
        $adrs['state']   = '';
        $adrs['city']    = '';
        $adrs['area']    = '';
        $adrs['lat']     = '';
        $adrs['long']    = '';
        $adrs['street']  = '';
        $adrs['postal']  = '';

        $location_input = trim($location);
        $location_arr   = explode(',', $location_input);
        $location_size  = count($location_arr);

        // Try to predict location from DB.
        if ($location_size > 0) {
            foreach ($location_arr as $key => $value) {
                $location_arr[$key] = trim(addslashes($value));
            }

            $location_query = 'select city, state, country, latitude, longitude from properties where enabled = 1 and status = 1 and admin_score > 0 and latitude != 0 and longitude != 0 and ';
            switch ($location_size) {
                case 1:
                    $location_query .= "(state = '".$location_arr[0]."' or city = '".$location_arr[0]."') ";
                    $location_query .= " group by country,state,city order by case when (state = '".$location_arr[0]."' and city = '".$location_arr[0]."') then 1 else 2 end asc limit 1";
                break;

                case 2:
                    $location_query .= "(state ='".$location_arr[0]."' or city ='".$location_arr[0]."') ";
                    $location_query .= " group by country,state,city order by case when (state = '".$location_arr[0]."' and city = '".$location_arr[0]."') then 1 else 2 end asc limit 1";
                break;

                case 3:
                    $location_query .= "state ='".$location_arr[1]."' and city='".$location_arr[0]."' ";
                    $location_query .= ' group by country,state,city limit 1';
                break;

                case 4:
                    $location_query .= "state ='".$location_arr[2]."' and city='".$location_arr[1]."' ";
                    $location_query .= ' group by country,state,city limit 1';
                break;

                default:
                    $location_query .= "state ='".$location_arr[1]."' and city='".$location_arr[0]."' ";
                    $location_query .= ' group by country,state,city limit 1';
                break;
            }//end switch

            $location_res = \DB::select($location_query);
            if (count($location_res) > 0) {
                $adrs['country'] = $location_res[0]->country;
                $adrs['state']   = $location_res[0]->state;
                $adrs['city']    = ($location_size === 1 || $location_size === 2) ? '' : $location_res[0]->city;
                $adrs['lat']     = $location_res[0]->latitude;
                $adrs['long']    = $location_res[0]->longitude;
            }
        }//end if

        return $adrs;

    }//end getLocationFromDB()


    /**
     * Return place_id from Googleapi using latitude,longitude.
     *
     * @param string $latitude  Latitude.
     * @param string $longitude Longitude.
     *
     * @return string
     */
    public static function getPlaceIdFromGoogleApi(string $latitude, string $longitude)
    {
        $adrs     = [];
        $place_id = '';

        $get_place_detail = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.urlencode($latitude.','.$longitude).'&key='.GOOGLE_PLACE_API_KEY);

        if (empty($get_place_detail) === false) {
            $places = json_decode($get_place_detail);
            if ($places->status === 'OK' && count($places->results) > 0) {
                $place_id = (isset($places->results[0]->place_id) === true) ? $places->results[0]->place_id : '';
            }
        }

        return $place_id;

    }//end getPlaceIdFromGoogleApi()


    /**
     * Return location from Googleapi using latitude,longitude.
     *
     * @param string $latitude  Latitude.
     * @param string $longitude Longitude.
     *
     * @return array
     */
    public static function getLocationFromLatLong(string $latitude, string $longitude)
    {
        $adrs     = [];
        $place_id = self::getPlaceIdFromGoogleApi($latitude, $longitude);
        if ($place_id !== '') {
            $adrs = self::getLocationFromPlaceId($place_id);
        } else {
            $adrs['country'] = '';
            $adrs['state']   = '';
            $adrs['city']    = '';
            $adrs['area']    = '';
            $adrs['lat']     = '';
            $adrs['long']    = '';
            $adrs['street']  = '';
            $adrs['postal']  = '';
        }

        return $adrs;

    }//end getLocationFromLatLong()


    /**
     * Return location data from Googleapi using place_id.
     *
     * @param string $place_id Place id.
     *
     * @return array
     */
    public static function getLocationFromPlaceId(string $place_id)
    {
        $adrs = [];
        $get_place_detail_place_id = file_get_contents('https://maps.googleapis.com/maps/api/place/details/json?placeid='.$place_id.'&key='.GOOGLE_PLACE_API_KEY);

        if (empty($get_place_detail_place_id) === false) {
            $place_detail = json_decode($get_place_detail_place_id);
        }

        if (empty($get_place_detail_place_id) === false && empty($place_detail->status) === false && $place_detail->status === 'OK') {
            $address_arr = $place_detail->result->address_components;
            $address     = [];

            foreach ($address_arr as $addr) {
                if (count($addr->types) === 0) {
                    continue;
                }

                if ($addr->types[0] === 'country') {
                    $address[$addr->types[0]] = $addr->short_name;
                } else {
                    $address[$addr->types[0]] = $addr->long_name;
                }
            }

            $geometry = $place_detail->result->geometry->location;
            foreach ($geometry as $key => $value) {
                $address[$key] = $value;
            }

            $country_short  = (isset($address['country']) === true) ? $address['country'] : '';
            $state_address  = (isset($address['administrative_area_level_1']) === true) ? $address['administrative_area_level_1'] : '';
            $city_address   = (isset($address['locality']) === true) ? $address['locality'] : '';
            $area_adrress   = (isset($address['sublocality_level_1']) === true) ? $address['sublocality_level_1'] : '';
            $latitude       = (isset($address['lat']) === true) ? $address['lat'] : '';
            $longitude      = (isset($address['lng']) === true) ? $address['lng'] : '';
            $street_address = (isset($address['sublocality_level_2']) === true) ? $address['sublocality_level_2'] : '';
            $postal_code    = (isset($address['postal_code']) === true) ? $address['postal_code'] : '';

            $adrs['country'] = addslashes($country_short);
            $adrs['state']   = addslashes($state_address);
            $adrs['city']    = addslashes($city_address);
            $adrs['area']    = addslashes($area_adrress);
            $adrs['lat']     = $latitude;
            $adrs['long']    = $longitude;
            $adrs['street']  = addslashes($street_address);
            $adrs['postal']  = $postal_code;

            // Exceptional conditions (Conflicted Areas).
            $conflicted_area = [
                'Srinagar',
                'Leh',
                'Jammu',
                'Katra',
                'Pahalgam',
                'Anantnag',
                'Banihal',
                'Bishnah',
                'Kargil',
                'Sonamarg',
                'Tangmarg',
            ];
            if (in_array($adrs['city'], $conflicted_area) === true) {
                $adrs['country'] = 'IN';
                $adrs['state']   = 'Jammu and Kashmir';
            }
        } else {
            $adrs['country'] = '';
            $adrs['state']   = '';
            $adrs['city']    = '';
            $adrs['area']    = '';
            $adrs['lat']     = '';
            $adrs['long']    = '';
            $adrs['street']  = '';
            $adrs['postal']  = '';
        }//end if

        return $adrs;

    }//end getLocationFromPlaceId()


    /**
     * Convert Seconds to Formatted string.
     *
     * @param integer $seconds Seconds.
     *
     * @return string
     */
    public static function stringTimeFormattedString(int $seconds)
    {
        if (floor($seconds / 3600) > 0) {
            return round(($seconds / 3600), 1).' '.((floor($seconds / 3600) > 1) ? 'hrs' : 'hr');
        } else if (floor(($seconds / 60) % 60) > 0) {
            return round((($seconds / 60) % 60), 1).' '.((floor(($seconds / 60) % 60) > 1) ? 'mins' : 'min');
        } else {
            return ($seconds % 60).' '.((($seconds % 60) > 1) ? 'secs' : 'sec');
        }

    }//end stringTimeFormattedString()


     /**
      * Get mailer array from constant.
      *
      * @param string $mailer_name Mailer name to fetch.
      *
      * @return array
      */
    public static function getMailer(string $mailer_name)
    {
        // Check http://php.net/manual/en/migration70.new-features.php#migration70.new-features.null-coalesce-op .
        return (MAILERS[$mailer_name] ?? []);

    }//end getMailer()


    /**
     * Get Sms view name from template name.
     *
     * @param string $sms_name Template name.
     *
     * @return array
     */
    public static function getSmsTemplate(string $sms_name)
    {
        // Check http://php.net/manual/en/migration70.new-features.php#migration70.new-features.null-coalesce-op .
        return (SMS_TEMPLATES[$sms_name] ?? []);

    }//end getSmsTemplate()


     /**
      * Get sms content from the sms template name.
      *
      * @param string $sms_name Sms Template name.
      * @param array  $params   Sms Template params.
      *
      * @return array
      */
    public static function getSmsContent(string $sms_name, array $params)
    {
        $sms = self::getSmsTemplate($sms_name);

        if (count($sms) === 0) {
            \Log::Error('sms template not found');
            return [];
        }

        $view = View::make($sms['view'], ['view_data' => $params]);
        return [
            'msg'       => $view->render(),
            'sender_id' => $sms['sender_id'],
        ];

    }//end getSmsContent()


    /**
     * Log Info
     *
     * @param string $message Message.
     * @param array  $context Context.
     *
     * @return void
     */
    public static function logInfo(string $message, array $context=[])
    {
         \Log::channel('info')->info($message, $context);

    }//end logInfo()


    /**
     * Log Error
     *
     * @param string $message Message.
     * @param array  $context Context.
     *
     * @return void
     */
    public static function logError(string $message, array $context=[])
    {
         \Log::channel('error')->error($message, $context);

    }//end logError()


    /**
     * Log Query
     *
     * @param string $message Message.
     * @param array  $context Context.
     *
     * @return void
     */
    public static function logQuery(string $message, array $context=[])
    {
         \Log::channel('query')->info($message, $context);

    }//end logQuery()


    /**
     * Get Google Login data via Google login access token.
     *
     * @param string $access_token Access Token.
     * @param string $device_type  Device Type.
     *
     * @return array
     */
    public static function getGoogleSignUpProfile(string $access_token, string $device_type='web')
    {
        // Get Google Client Id.
        $google_client_id = (isset(GOOGLE_CLIENT_ID[$device_type]) === true) ? GOOGLE_CLIENT_ID[$device_type] : GOOGLE_CLIENT_ID['web'];

        // Connect with Google client library.
        $google_client = new Google_Client(['client_id' => $google_client_id]);
        try {
            // Specify the CLIENT_ID of the app that accesses the backend.
            $payload = $google_client->verifyIdToken($access_token);
        } catch (\Exception $e) {
            return [];
        }

        // Payload data.
        $payload_filter_data = [];

         // Google data.
        $payload_filter_data = [
            'id'          => (isset($payload['sub']) === true) ? $payload['sub'] : '',
            'email'       => (isset($payload['email']) === true) ? $payload['email'] : '',
            'name'        => (isset($payload['given_name']) === true) ? $payload['given_name'] : '',
            'last_name'   => (isset($payload['family_name']) === true) ? $payload['family_name'] : '',
            'profile_img' => (isset($payload['picture']) === true) ? $payload['picture'].'?sz='.DEFAULT_PROFILE_PIC_WIDTH : '',
        ];

        if (empty($payload_filter_data['id']) === true || empty($payload_filter_data['email']) === true) {
            // Invaid access token.
            return [];
        }

        return $payload_filter_data;

    }//end getGoogleSignUpProfile()


    /**
     * Get Facebook Login data via Facebook login access token.
     *
     * @param string $access_token Access Token.
     *
     * @return array
     */
    public static function getFacebookSignUpProfile(string $access_token)
    {
        // Fetch user data using access token.
        $fb_data = self::sendCurlRequest('https://graph.facebook.com/me?access_token='.$access_token.'&fields=id,first_name,last_name,birthday,currency,email,picture.type(large),gender');

        if (empty($fb_data) === true) {
            return [];
        }

        $payload = json_decode($fb_data, true);

        if (empty($payload) === true) {
            return [];
        }

        // Payload data.
        $payload_filter_data = [];

         // Google data.
        $payload_filter_data = [
            'id'          => (isset($payload['id']) === true) ? $payload['id'] : '',
            'email'       => (isset($payload['email']) === true) ? $payload['email'] : '',
            'name'        => (isset($payload['first_name']) === true) ? $payload['first_name'] : '',
            'last_name'   => (isset($payload['last_name']) === true) ? $payload['last_name'] : '',
            'birthday'    => (empty($payload['birthday']) === true) ? '' : Carbon::parse($payload['birthday'])->format('Y-m-d'),
            'currency'    => (isset($payload['currency']['user_currency']) === true) ? $payload['currency']['user_currency'] : '',
            'profile_img' => (isset($payload['picture']['data']['url']) === true) ? $payload['picture']['data']['url'].'?sz='.DEFAULT_PROFILE_PIC_WIDTH : '',
            'gender'      => (isset($payload['gender']) === true) ? $payload['gender'] : '',

        ];

        if (empty($payload_filter_data['id']) === true) {
            // Invaid access token.
            return [];
        }

        return $payload_filter_data;

    }//end getFacebookSignUpProfile()


    /**
     * Generate Short Url Using Bitly.
     *
     * @param string $url Url.
     *
     * @return string
     */
    public static function getShortUrl(string $url)
    {
        // Fetch short url from the url.
        try {
            $response      = self::sendCurlRequest('https://api-ssl.bitly.com/v3/shorten?access_token='.BITLY_ACCESS_TOKEN.'&longUrl='.$url);
            $response_data = json_decode($response, true);
            if ($response_data['status_code'] !== 200) {
                return '';
            } else {
                return $response_data['data']['url'];
            }
        } catch (\Exception $e) {
            self::logError($e->getMessage());
            return '';
        }

    }//end getShortUrl()


    /**
     * Check File Exist or not.
     *
     * @param string $url Url.
     *
     * @return boolean
     */
    public static function remoteFileExists(string $url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        $result = curl_exec($curl);
        $ret    = false;
        if ($result !== false) {
            // If request was ok, check response code.
            $status_code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($status_code === 200) {
                $ret = true;
            }
        }

        curl_close($curl);
        return $ret;

    }//end remoteFileExists()


    /**
     * Get Virtual Location.
     *
     * @param string $coordinate Location Coordinate Lat/Long.
     *
     * @return string
     */
    public static function getVirtualLocationValue(string $coordinate)
    {
        return substr($coordinate, 0, strpos($coordinate, '.')).'.'.((substr($coordinate, (strpos($coordinate, '.') + 1), 3)) + ('0.000'.rand(10, 99)));

    }//end getVirtualLocationValue()


    /**
     * Get set data or Default.
     *
     * @param array  $input   Input Data.
     * @param string $key     Input Key.
     * @param mixed  $default Default Value.
     *
     * @return mixed
     */
    public static function issetOrDefault(array $input, string $key, $default=null)
    {
        return (isset($input[$key]) === true) ? $input[$key] : $default;

    }//end issetOrDefault()


    /**
     * Get non empty data or Default.
     *
     * @param array  $input   Input Data.
     * @param string $key     Input Key.
     * @param mixed  $default Default Value.
     *
     * @return mixed
     */
    public static function emptyOrDefault(array $input, string $key, $default=null)
    {
        return (empty($input[$key]) === false) ? $input[$key] : $default;

    }//end emptyOrDefault()


    /**
     * Get Given keys Data.
     *
     * @param array $input Input Data.
     * @param array $keys  Keys Data.
     *
     * @return array
     */
    public static function getArrayKeysData(array $input, array $keys)
    {
        $response_data = [];

        foreach ($keys as $value) {
            if (isset($input[$value]) === true) {
                $response_data[$value] = $input[$value];
            }
        }

        return $response_data;

    }//end getArrayKeysData()


    /**
     * Get Property Url
     *
     * @param string  $property_hash_id Property Hash Id.
     * @param string  $version          App Version.
     * @param boolean $with_prefix_url  Add Site Url flag.
     *
     * @return string
     */
    public static function getPropertyUrl(string $property_hash_id, string $version=VERSION_PREFIX, bool $with_prefix_url=false)
    {
        return (($with_prefix_url === true) ? SITE_URL.'/' : '').$version.'/property/'.$property_hash_id;

    }//end getPropertyUrl()


    /**
     * Get All Currency Symbols
     *
     * @param string $currency Currency.
     *
     * @return array
     */
    public static function getCurrencyObject(string $currency='INR')
    {
        if (empty(CURRENCY_SYMBOLS[$currency]) === false) {
            return CURRENCY_SYMBOLS[$currency];
        }

        return CURRENCY_SYMBOLS['INR'];

    }//end getCurrencyObject()


    /**
     * Get formatted contact number
     *
     * @param string $contact Contact.
     *
     * @return array
     */
    public static function getFormattedContact(string $contact)
    {
        if (empty($contact) === false && ctype_digit($contact) === true) {
            // Allow only Digits, remove all other characters.
            $contact = preg_replace('/[^\d]/', '', $contact);

            if (strlen($contact) === 10) {
                $contact = preg_replace('/^1?(\d{3})(\d{3})(\d{4})$/', 'xxx xxx $3', $contact);
                return '+91 '.$contact;
            }
        }

        return '';

    }//end getFormattedContact()


    /**
     * Get modified email
     *
     * @param string $email Contact.
     *
     * @return array
     */
    public static function getModifiedEmail(string $email)
    {
        if (empty($email) === false) {
            return preg_replace_callback(
                '/(\w)(.*?)(\w\w)(@.*?)$/s',
                function ($matches) {
                    return $matches[1].preg_replace('/\w/', '*', $matches[2]).$matches[3].$matches[4];
                },
                $email
            );
        }

        return '';

    }//end getModifiedEmail()


     /**
      * Generate hash id for a user from user id
      *
      * @param integer $admin_id Admin id.
      *
      * @return string Return generated hash id
      */
    public static function encodeAdminId(int $admin_id)
    {
        return 'A'.self::_hash(HASH_SALT_FOR_ADMIN_USER, HASH_LENGTH_FOR_ADMIN_USER, HASH_CHAR_FOR_ADMIN_USER, $admin_id);

    }//end encodeAdminId()


     /**
      * Get booking reqquest id from a booking request hash id
      *
      * @param string $admin_id Booking hash code.
      *
      * @return integer|string Return booking request id
      */
    public static function decodeAdminId(string $admin_id)
    {
        // Use ctype_digit instead of is_numeric because is_numeric consider HEX, OCTAL etc. as numeric eg. 00X12 is hex number.
        // Due these issues. check seperate digit by ctype_digit method.
        if (ctype_digit($admin_id) === true) {
            return $admin_id;
        } else {
            if ($admin_id[0] === 'A') {
                $admin_id = substr($admin_id, 1);
            }

            return self::_unhash(HASH_SALT_FOR_ADMIN_USER, HASH_LENGTH_FOR_ADMIN_USER, HASH_CHAR_FOR_ADMIN_USER, $admin_id);
        }

        return 0;

    }//end decodeAdminId()


    /**
     * Return array containing property status of host.
     *
     * @return array
     */
    public static function getPropertyStatusOfHost()
    {
        $all_status = [
            [
                'status' => NEW_REVIEW,
                'text'   => 'Under Review',
            ],
            [
                'status' => EDITED_REVIEW,
                'text'   => 'Modified',
            ],
            [
                'status' => REJECTED_REVIEW,
                'text'   => 'Deactivated',
            ],
            [
                'status' => ONLINE,
                'text'   => 'Online',
            ],
            [
                'status' => OFFLINE,
                'text'   => 'Offline',
            ],
        ];
        return $all_status;

    }//end getPropertyStatusOfHost()


    /**
     * Return array containing task status of prive.
     *
     * @param array $selected_status Selected Status Of Task.
     *
     * @return array
     */
    public static function getPriveTaskStatus(array $selected_status=[])
    {
        $task_status = [
            [
                'status'   => PRIVE_TASK_OPEN,
                'text'     => 'Open',
                'selected' => (in_array(PRIVE_TASK_OPEN, $selected_status) === true) ? 1 : 0,
            ],
            [
                'status'   => PRIVE_TASK_TODO,
                'text'     => 'Todo',
                'selected' => (in_array(PRIVE_TASK_TODO, $selected_status) === true) ? 1 : 0,
            ],
            [
                'status'   => PRIVE_TASK_PENDING,
                'text'     => 'Pending',
                'selected' => (in_array(PRIVE_TASK_PENDING, $selected_status) === true) ? 1 : 0,
            ],
            [
                'status'   => PRIVE_TASK_COMPLETED,
                'text'     => 'Completed',
                'selected' => (in_array(PRIVE_TASK_COMPLETED, $selected_status) === true) ? 1 : 0,
            ],
        ];
        return $task_status;

    }//end getPriveTaskStatus()


     /**
      * Return array containing task status with text and color code of prive.
      *
      * @param integer $task_status Status Of Task.
      *
      * @return array
      */
    public static function getPriveTaskShowStatus(int $task_status)
    {
        switch ($task_status) {
            case PRIVE_TASK_OPEN:
                $text       = 'Open';
                $color_code = TASK_OPEN_COLOR_CODE;
            break;

            case PRIVE_TASK_TODO:
                $text       = 'Todo';
                $color_code = TASK_TODO_COLOR_CODE;
            break;

            case PRIVE_TASK_PENDING:
                $text       = 'Pending';
                $color_code = TASK_PENDING_COLOR_CODE;
            break;

            case PRIVE_TASK_COMPLETED:
                $text       = 'Completed';
                $color_code = TASK_COMPLETED_COLOR_CODE;
            break;

            default:
                $text       = '';
                $color_code = '';
            break;
        }//end switch

        $show_status = [
            'text'       => $text,
            'color_code' => $color_code,
        ];

        return $show_status;

    }//end getPriveTaskShowStatus()


     /**
      * Return array containing task type of prive.
      *
      * @param array $selected_type Selected Task Type.
      *
      * @return array
      */
    public static function getPriveTaskType(array $selected_type=[])
    {
        $task_status = [
            [
                'status'        => TASK_TYPE_CHECKIN,
                'text'          => 'Checkin Service',
                'selected'      => (in_array(TASK_TYPE_CHECKIN, $selected_type) === true) ? 1 : 0,
                'autogenerated' => 1,
            ],
            [
                'status'        => TASK_TYPE_CHECKOUT,
                'text'          => 'Checkout Service',
                'selected'      => (in_array(TASK_TYPE_CHECKOUT, $selected_type) === true) ? 1 : 0,
                'autogenerated' => 1,
            ],
            [
                'status'        => TASK_TYPE_OCCUPIED_SERVICE,
                'text'          => 'Occupied Service',
                'selected'      => (in_array(TASK_TYPE_OCCUPIED_SERVICE, $selected_type) === true) ? 1 : 0,
                'autogenerated' => 0,
            ],
            [
                'status'        => TASK_TYPE_TURN_DOWN_SERVICE,
                'text'          => 'Turn down Service',
                'selected'      => (in_array(TASK_TYPE_TURN_DOWN_SERVICE, $selected_type) === true) ? 1 : 0,
                'autogenerated' => 0,
            ],
            [
                'status'        => TASK_TYPE_DEPARTURE_SERVICE,
                'text'          => 'Departure Service',
                'selected'      => (in_array(TASK_TYPE_DEPARTURE_SERVICE, $selected_type) === true) ? 1 : 0,
                'autogenerated' => 0,
            ],
            [
                'status'        => TASK_TYPE_MAINTAINENCE_SERVICE,
                'text'          => 'Maintenance Service',
                'selected'      => (in_array(TASK_TYPE_MAINTAINENCE_SERVICE, $selected_type) === true) ? 1 : 0,
                'autogenerated' => 0,
            ],
        ];
        return $task_status;

    }//end getPriveTaskType()


     /**
      * Return  task Type of prive.
      *
      * @param integer $task_type Task Type.
      *
      * @return string
      */
    public static function getPriveTaskShowType(int $task_type)
    {
        switch ($task_type) {
            case TASK_TYPE_CHECKIN:
                $text = 'Checkin Service';
            break;

            case TASK_TYPE_CHECKOUT:
                $text = 'Checkout Service';
            break;

            case TASK_TYPE_OCCUPIED_SERVICE:
                $text = 'Occupied Service';
            break;

            case TASK_TYPE_TURN_DOWN_SERVICE:
                $text = 'Turn down Service';
            break;

            case TASK_TYPE_DEPARTURE_SERVICE:
                $text = 'Departure Service';
            break;

            case TASK_TYPE_MAINTAINENCE_SERVICE:
                $text = 'Maintenance Service';
            break;

            default:
                $text = '';
            break;
        }//end switch

        return $text;

    }//end getPriveTaskShowType()


    /**
     * Generate hash id for a task from task id.
     *
     * @param integer $task_id Task id(integer).
     *
     * @return string Return generated hash id.
     */
    public static function encodeTaskId(int $task_id)
    {
        return 'T'.self::_hash(HASH_SALT_FOR_TASK, HASH_LENGTH_FOR_TASK, HASH_CHAR_FOR_TASK, $task_id);

    }//end encodeTaskId()


    /**
     * Get Task id from a Task hash id.
     *
     * @param string $task_hash_id Task hash code.
     *
     * @return integer|string Return decoded collection id.
     */
    public static function decodeTaskHashId(string $task_hash_id)
    {
        // Use ctype_digit instead of is_numeric because is_numeric consider HEX, OCTAL etc. as numeric eg. 00X12 is hex number.
        // Due these issues. check seperate digit by ctype_digit method.
        if (ctype_digit($task_hash_id) === true && $task_hash_id > 0) {
            return $task_hash_id;
        } else {
            if ($task_hash_id[0] === 'T') {
                $task_hash_id = substr($task_hash_id, 1);
            }

            return self::_unhash(HASH_SALT_FOR_TASK, HASH_LENGTH_FOR_TASK, HASH_CHAR_FOR_TASK, $task_hash_id);
        }

    }//end decodeTaskHashId()


    /**
     * Get Payment Status.
     *
     * @param object  $price_details  Price Details.
     * @param integer $booking_status Booking Status.
     *
     * @return integer
     */
    // phpcs:ignore
    public static function getPaymentStatus($price_details, int $booking_status)
    {
        if ($booking_status === BOOKED && isset($price_details->balance_amount) === true && $price_details->balance_amount > 0) {
            return 1;
        }

        return 0;

    }//end getPaymentStatus()


    /**
     * Generate Firebase Short Url.
     *
     * @param string $url Url.
     *
     * @return string
     */
    public static function getFirebaseShortUrl(string $url)
    {
        $firebase_api_key    = config('gh.firebase.api_key');
        $firebase_domain_url = config('gh.firebase.domain_url');

        $post_params = [
            'longDynamicLink' => $firebase_domain_url.'link='.$url,
        ];

        $headers = ['Content-Type' => 'application/json'];
        // Fetch Firebase short url from the url.
        try {
            $response = self::sendCurlRequest('https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key='.$firebase_api_key, json_encode($post_params), 'POST', null, $headers);

            $response_data = json_decode($response, true);
            if (array_key_exists('error', $response_data) === true) {
                return '';
            } else {
                return $response_data['shortLink'];
            }
        } catch (\Exception $e) {
            self::logError($e->getMessage());
            return '';
        }

    }//end getFirebaseShortUrl()


    /**
     * Get expense id from a properly expense hash id
     *
     * @param string $hash_id Properly expense hash code.
     *
     * @return integer|string Return decoded properly id
     */
    public static function decodeProperlyExpenseHashId(string $hash_id)
    {
        // Use ctype_digit instead of is_numeric because is_numeric consider HEX, OCTAL etc. as numeric eg. 00X12 is hex number.
        // Due these issues. check seperate digit by ctype_digit method.
        if (ctype_digit($hash_id) === true && $hash_id > 0) {
            return $hash_id;
        } else {
            if ($hash_id[0] === 'E') {
                $hash_id = substr($hash_id, 1);
            }

            return self::_unhash(HASH_SALT_FOR_PROPERLY_EXPENSE, HASH_LENGTH_FOR_PROPERLY_EXPENSE, HASH_CHAR_FOR_PROPERLY_EXPENSE, $hash_id);
        }

    }//end decodeProperlyExpenseHashId()


    /**
     * Get hash id from a properly expense id
     *
     * @param integer $properly_expense_id Properly expense id.
     *
     * @return integer|string Return encoded properly expense id
     */
    public static function encodeProperlyExpenseId(int $properly_expense_id)
    {
        return 'E'.self::_hash(HASH_SALT_FOR_PROPERLY_EXPENSE, HASH_LENGTH_FOR_PROPERLY_EXPENSE, HASH_CHAR_FOR_PROPERLY_EXPENSE, $properly_expense_id);

    }//end encodeProperlyExpenseId()


    /**
     * Write db queries to file.
     *
     * @param array   $queries  Queries.
     * @param integer $admin_id Admin Id.
     * @param integer $user_id  User Id.
     *
     * @return boolean
     */
    public static function writeDBQueriesToFile(array $queries, int $admin_id, int $user_id)
    {
        $query_data = '';
        foreach ($queries as $key => $value) {
            $query_type = gettype($value['query']);
            if ($query_type !== 'string') {
                self::logError('Invalid query found $query_type. should be string');
                continue;
            }

            $query = $value['query'];

            if (strrpos(strtolower($query), 'select') !== false) {
                continue;
            }

            foreach ($value['bindings'] as $key => $bindings) {
                $value['bindings'][$key] = (empty($value['bindings'][$key]) === true) ? "''" : $value['bindings'][$key];

                $value['bindings'][$key] = ($value['bindings'][$key] instanceof \Carbon\Carbon === true || $value['bindings'][$key] instanceof \DateTime === true) ? $value['bindings'][$key]->format('Y-m-d H:i:s') : $value['bindings'][$key];

                $query = str_replace_first('?', $value['bindings'][$key], $query);
            }

            $query = preg_replace("/\r\n|\n[ ]{2,}|[\t]/", ' ', $query);

            $query_array = [
                $user_id,
                $admin_id,
                Carbon::now('Asia/Kolkata'),
                trim($query),
            ];
            $query_data .= implode("\t", $query_array)."\n";
        }//end foreach

        if (empty($query_data) === false) {
             self::logQuery($query_data);
        }

        return true;

    }//end writeDBQueriesToFile()


    /**
     * Push Messages to queue.
     *
     * @param string $queue  Queue.
     * @param string $msg    Message.
     * @param string $region Region.
     *
     * @return boolean
     */
    public static function pushMessageToQueue(string $queue, string $msg, string $region)
    {
        try {
             $sqs = AwsService::getSqsClient($region);
            $sqs->sendMessage(
                [
                    'QueueUrl'    => $queue,
                    'MessageBody' => $msg,
                ]
            );
            return true;
        } catch (Exception $e) {
            self::logError('Error in pushing to queue '.$e->getMessage());
        }

        return false;

    }//end pushMessageToQueue()


    /**
     * Get Apple Login data via Apple id token.
     *
     * @param string $id_token Apple Id Token.
     *
     * @return array
     */
    public static function getAppleSignUpProfile(string $id_token)
    {
        try {
            // Decode Id token to read the headers and user claims.
            $token_parser = new Parser();
            $token        = $token_parser->parse((string) $id_token);

            /*
                |----------------------------------------------------------------------------------------------------------
                | Id Token Verification
                |----------------------------------------------------------------------------------------------------------
                |
                | Verifying the identity token make sure the key ID (kid) field matches what is at Apple's public
                | key URL ("AIDOPK1" at the moment). That is telling you which of their public keys to use. While there is
                | only one right now, that could change. Apple could also easily revoke a public key for security reasons.
            */

            $kid = $token->getHeader('kid');

            // Fetch Apple JWKSet.Keys to generate the public key for verification.
            $apple_jwk = self::sendCurlRequest('https://appleid.apple.com/auth/keys');

            // Generate public pem key from JWK Set (JSON Web Key Set) of specific key mentioned in token headers.
            $public_key = Jwk::parseKeySet($apple_jwk)[$kid];

            // Verify Id token.
            $signer      = new Sha256();
            $is_verified = $token->verify($signer, $public_key);

            $iss   = $token->getClaim('iss');
            $aud   = $token->getClaim('aud');
            $exp   = $token->getClaim('exp');
            $email = $token->getClaim('email');
            $sub   = $token->getClaim('sub');

            $email_verified = $token->getClaim('email_verified');

            if (true === $is_verified) {
                // Check claims and validate.
                if (APPLE_LOGIN_CLAIM_ISSUER !== $iss
                    || APPLE_APP_CLIENT_ID !== $aud
                    || time() >= $exp
                    || true === empty($sub)
                    || true === empty($email)
                ) {
                    return [];
                }

                return [
                    'id'             => $sub,
                    'email'          => $email,
                    'email_verified' => ($email_verified === 'true') ? 1 : 0,
                ];
            }
        } catch (\Exception $e) {
            \Log::Error($e->getMessage());
        }//end try

        return [];

    }//end getAppleSignUpProfile()


}//end class
