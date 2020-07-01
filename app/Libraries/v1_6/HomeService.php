<?php
/**
 * Home Service containing methods to return banners, widgets and video
 */

namespace App\Libraries\v1_6;

use App\Libraries\Helper;
use App\Models\HomeBanner;
use App\Models\HomeWidget;

/**
 * Class HomeService
 */
class HomeService
{


    /**
     * Get home banners.
     *
     * @param array $data Array Containing country code to fetch banner.
     *
     * @return array Home banners.
     */
    public static function getHomeBanner(array $data)
    {
        // Input params.
        $country_codes = $data['country_codes'];
        $currency      = $data['currency'];
        $device_type   = $data['device_type'];

        // Combined home banners.
        $organized_home_banners = [];

        // Home banners.
        $home_banners = HomeBanner::getMobileHomeBanners();

        $all_notification_type = [
            PROPERTY_DETAILS,
            WEB_VIEW,
            MANAGE_LISTING,
            SIGNUP_SCREEN,
            INVITE_SCREEN,
            APP_UPDATE,
            WALLET_NOTIFICATION,
            SEARCH_SCREEN,
        ];

        // Iterate over home banners.
        foreach ($home_banners as $home_banner) {
            $payload_params = [];

            $all_params = json_decode($home_banner->destination, true);

            $notification_type = (empty($payload_params['notification_type']) === false) ? (int) $payload_params['notification_type'] : SEARCH_SCREEN;

            $notification_type = (in_array($notification_type, $all_notification_type) === true) ? $notification_type : SEARCH_SCREEN;

            $image = CDN_URL.(($device_type === 'website') ? S3_HOMEBANNER_DIR_WEB : S3_HOMEBANNER_DIR_MOBILE).(trim($home_banner->image, ' '));

            if (empty($all_params['country']) === false) {
                $all_params['country'] = (isset($country_codes[$all_params['country']]) === true) ? $country_codes[$all_params['country']] : '';
            }

            // Set city or state or country as heading.
            if (empty($all_params['city']) === false) {
                $all_params['heading'] = $all_params['city'];
            } else if (empty($all_params['state']) === false) {
                $all_params['heading'] = $all_params['state'];
            } else if (isset($all_params['country']['name']) === true) {
                $all_params['heading'] = $all_params['country']['name'];
            } else {
                $all_params['heading'] = '';
            }

            $one_home_banner = [
                'id'                => $home_banner->id,
                'notification_type' => $notification_type,
                'location'          => $all_params['location'],
                'country'           => (empty($all_params['country']) === false) ? $all_params['country']['ccode'] : '',
                'country_name'      => (empty($all_params['country']) === false) ? $all_params['country']['name'] : '',
                'state'             => $all_params['state'],
                'city'              => $all_params['city'],
                'latitude'          => $all_params['lat'],
                'longitude'         => $all_params['lng'],
                'search_keyword'    => $all_params['search_keyword'],
                'check_in'          => $all_params['checkin'],
                'check_out'         => $all_params['checkout'],
                'guests'            => ((int) $all_params['guests'] > 0) ? $all_params['guests'] : '1',
                'min_budget'        => $all_params['minvalue'],
                'max_budget'        => $all_params['maxvalue'],
                'currency'          => CURRENCY_SYMBOLS[$currency],
                'property_type'     => $all_params['property_type'],
                'room_type'         => (empty($all_params['room_type']) === false) ? array_filter(explode(',', $all_params['room_type'])) : [],
                'amenities'         => $all_params['amenities'],
                'bedroom'           => $all_params['bedrooms'],
                'tag'               => $all_params['tag'],
                'property_hash_id'  => (empty($all_params['pid']) === false) ? ((is_numeric($all_params['pid']) === false) ? $all_params['pid'] : Helper::encodePropertyId($all_params['pid'])) : '',
                'keyword'           => $all_params['keyword'],
                'title'             => $all_params['title'],
                'promo'             => $all_params['promo'],
                'utm_source'        => $all_params['utm_source'],
                'utm_campaign'      => $all_params['utm_campaign'],
                'utm_medium'        => $all_params['utm_medium'],
                'heading'           => $all_params['heading'],
                'property_images'   => [$image],
                'content'           => $all_params['html_content'],
                'url'               => $all_params['url'],
            ];

            // Home banners.
            array_push($organized_home_banners, $one_home_banner);
        }//end foreach

        // Are properties liked by user.
        return $organized_home_banners;

    }//end getHomeBanner()


    /**
     * Get home widgets and popular cities.
     *
     * @param array $data Array Containing country code to fetch widgets and cities.
     *
     * @return array Home widgets and cities.
     */
    public static function getHomeWidgetsAndCities(array $data)
    {
        // Input params.
        $country_codes = $data['country_codes'];

        // Set Source.
        $device_type = $data['device_type'];

        // Get home widgets.
        $home_widgets = HomeWidget::getHomeWidgets();

        // Combined home widgets.
        $organized_home_widgets = [];

        // Combined city.
        $organized_city_data = [];

        // Home widget number.
        $home_widget_number = 0;

        // Iterate over home widgets.
        foreach ($home_widgets as $one_widget) {
            // Remove un-necessary space from right side of &.
            $payload_url      = rtrim($one_widget->payload_url, '&');
            $payload_url      = $payload_url.'&';
            $payload_splitted = explode('?', $payload_url);

            // Get param are there.
            $get_params_set = (isset($payload_splitted[1]) === true) ? true : false;

            $payload_params = [];

            if ($get_params_set === true) {
                // Get parameters of payload url in key value pair.
                parse_str($payload_splitted[1], $payload_params);
            }

            // Params from payload url.
            $country = (array_key_exists('country', $payload_params) === true) ? $payload_params['country'] : '';
            $country = (array_key_exists($country, $country_codes) === true) ? $country_codes[$country] : [];
            $state   = (array_key_exists('state', $payload_params) === true) ? $payload_params['state'] : '';
            $city    = (array_key_exists('city', $payload_params) === true) ? $payload_params['city'] : '';
            $lat     = (array_key_exists('lat', $payload_params) === true) ? $payload_params['lat'] : '';
            $lng     = (array_key_exists('lng', $payload_params) === true) ? $payload_params['lng'] : '';

            // Property type id.
            if ($one_widget->ptype_id > 0) {
                // Append data in return structure.
                $one_home_widget = [
                    'country'            => $country,
                    'state'              => $state,
                    'city'               => $city,
                    'latitude'           => $lat,
                    'longitude'          => $lng,
                    'heading1'           => (empty($country['name']) === false ) ? $country['name'] : '',
                    'property_images'    => [trim($one_widget->image, ' ')],
                    'property_type'      => $one_widget->ptype_id,
                    'property_type_name' => $one_widget->caption,
                    'tag'                => 'Property',
                    'title'              => ($one_widget->ptype_id === 3) ? $one_widget->caption : $one_widget->caption.'s',
                    'url'                => rtrim($one_widget->url, '?'),
                ];

                // Add to home widgets.
                array_push($organized_home_widgets, $one_home_widget);
            } else {
                $image = self::getMobileImageForTrendingAndBanner(trim($one_widget->image, ' '), $device_type);
                // If widgets are less than 12 then add city data.
                if ($home_widget_number < 12) {
                    // Append data in return structure.
                    $one_home_city_data = [
                        'country'         => $country,
                        'state'           => $state,
                        'city'            => $city,
                        'latitude'        => $lat,
                        'longitude'       => $lng,
                        'property_images' => [$image],
                        'tag'             => 'Place',
                        'title'           => $one_widget->caption,
                        'home_count'      => ($one_widget->home_count > 0) ? $one_widget->home_count.'+ Homes' : '0 Home',
                        'url'             => rtrim($one_widget->url, '?'),
                    ];

                    // Add to cities.
                    array_push($organized_city_data, $one_home_city_data);
                }

                $home_widget_number++;
            }//end if
        }//end foreach

        return [
            'organized_city_data'    => $organized_city_data,
            'organized_home_widgets' => $organized_home_widgets,
        ];

    }//end getHomeWidgetsAndCities()


    /**
     * Get home video data.
     *
     * @param string $web_image_url Parse Image Url.
     * @param string $device_type   Device Type.
     *
     * @return string Home videos.
     */
    private static function getMobileImageForTrendingAndBanner(string $web_image_url, string $device_type)
    {
        $pos = strrpos($web_image_url, '/');
        if ($pos === false) {
            return $web_image_url;
        } else {
            $path_till_image_name = substr($web_image_url, 0, $pos);
            $directory            = ($device_type === 'website') ? '/' : '/mobile/';
            $image_name           = substr($web_image_url, ($pos + 1));

            return $path_till_image_name.$directory.$image_name;
        }

    }//end getMobileImageForTrendingAndBanner()


    /**
     * Get home video data.
     *
     * @return array Home videos.
     */
    public static function getHomeVideos()
    {
        return [
            [
                'url'       => 'https://d39vbwyctxz5qa.cloudfront.net/properties_images/branding-video--640-426.mp4',
                'type'      => 'video',
                'thumbnail' => 'https://d39vbwyctxz5qa.cloudfront.net/properties_images/branding-thumbnail.jpg',
            ],
        ];

    }//end getHomeVideos()


}//end class
