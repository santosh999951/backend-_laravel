<?php
// phpcs:ignoreFile
/**
 * SearchService containing all search releated functions
 */

namespace App\Libraries\v1_6;

use Illuminate\Support\Facades\DB;
use App\Models\PropertyTileStat;

use App\Libraries\Helper;

/**
 * Class SearchService
 */
class SearchService
{

    /**
     * Get Map Exceptional Locations to area.
     *
     * @param array $data Property data.
     *
     * @return array Exceptional cities.
     */
    public static function mapExceptionalLocationsToArea(array $data)
    {
        // Input params.
        $city     = $data['city'];
        $state    = $data['state'];
        $country  = $data['country'];
        $location = $data['location'];

        // Wayanad.
        if ($city === '' && strtolower($state) === 'kerala' && strrpos(strtolower($location), 'wayanad') !== false) {
            $country = 'IN';
            $city    = 'Wayanad';
        }

        // Location is leh.
        if (strtolower($location) === 'leh') {
            $country = 'IN';
            $state   = 'Jammu And Kashmir';
            $city    = 'Leh';
        }

        if (strtolower($city) === 'lonavla') {
            $city = 'Lonavala';
        }

        // For various cities.
        switch (strtolower($city)) {
            case 'srinagar':
            case 'leh':
            case 'jammu':
            case 'pahalgam':
            case 'katra':
            case 'anantnag':
            case 'banihal':
            case 'bishnah':
            case 'kargil':
            case 'sonamarg':
            case 'tangmarg':
                $country = 'IN';
                $state   = 'Jammu and Kashmir';
            break;

            case 'tawang':
                $country = 'IN';
                $state   = 'Arunachal Pradesh';
                $city    = 'Tawang';
            break;

            case 'bomdila':
                $country = 'IN';
                $state   = 'Arunachal Pradesh';
                $city    = 'Bomdila';
            break;

            case 'dirang':
                $country = 'IN';
                $state   = 'Arunachal Pradesh';
                $city    = 'Dirang';
            break;

            case 'itanagar':
                $country = 'IN';
                $state   = 'Arunachal Pradesh';
                $city    = 'Itanagar';
            break;

            case 'panchagani':
                $country = 'IN';
                $city    = 'Panchgani';
            break;

            case 'gurugram':
                $country = 'IN';
                $city    = 'Gurgaon';
            break;

            case 'gulmarg':
                $country = 'IN';
                $state   = 'Jammu and Kashmir';
            break;

            case 'chikkamangalore':
                $country = 'IN';
                $city    = 'Chikkamagaluru';
            break;

            case 'alleppey':
                $country = 'IN';
                $city    = 'Alappuzha';
            break;

            case 'bangkok':
                $country = 'TH';
                $state   = 'Bangkok';
                $city    = 'Bangkok';
            break;

            case 'kodagu':
                $country = 'IN';
                $state   = 'Karnataka';
                $city    = 'Kodagu';
            break;

            case 'central delhi':
                $city = 'New Delhi';
            break;

            case 'delhi':
                $country = 'IN';
                $city    = '';
            break;

            case 'dharamsala':
                $city = 'Dharamshala';
            break;

            default:
                // Default case.
            break;
        }//end switch

        return [
            'city'     => $city,
            'state'    => $state,
            'country'  => $country,
            'location' => $location,
        ];

    }//end mapExceptionalLocationsToArea()


    /**
     * Get Search Params Weightage.
     *
     * @param array $data Property data.
     *
     * @return array Search params weightage.
     */
    // phpcs:ignore
    private static function _getSearchParamsWeightage(array $data)
    {
        // Input params.
        $checkout = $data['checkout'];

        // Weightages.
        $weights = [
            'photography_score_weightage'   => 0.10,
            // Not using now.
            'image_score_weightage'         => 0.15,
            'admin_score_weightage'         => 0.07,
            'br_pv_score_weightage'         => 0.20,
            'bkg_bracc_score_weightage'     => 0.10,
            'rating_review_score_weightage' => 0.15,
            'instant_book_score_weightage'  => 0.05,
            'click_view_score_weightage'    => 0.10,
            'bkg_view_score_weightage'      => 0.10,
            'host_app_activity_weightage'   => 0.10,
            'calendar_updated_weightage'    => 0.20,
            'rejection_rate_weightage'      => 0.20,
            'acceptance_rate_weightage'     => 0.20,
            'instant_book_score'            => 0,
        ];

        if (empty($checkout) === false) {
            // Difference between todays date and checkin date.
            $days_diff = ((strtotime($checkout) - time()) / (24 * 60 * 60));

            // Checkin date is withi next 7 days.
            if ($days_diff > 0 && $days_diff <= 7) {
                $weights['instant_book_score_weightage'] = 0.10;
            }

            // Different instant book weightage based on days.
            switch ($days_diff) {
                // phpcs:ignore
                case $days_diff >= 0 && $days_diff <= 1:
                    $weights['instant_book_score'] = 5;
                break;

                // phpcs:ignore
                case $days_diff >= 2 && $days_diff <= 3:
                    $weights['instant_book_score'] = 4;
                break;

                // phpcs:ignore
                case $days_diff >= 4 && $days_diff <= 7:
                    $weights['instant_book_score'] = 3;
                break;

                // phpcs:ignore
                case $days_diff >= 8 && $days_diff <= 15:
                    $weights['instant_book_score'] = 3;
                break;

                // phpcs:ignore
                case $days_diff >= 16:
                    $weights['instant_book_score'] = 2;
                break;

                default:
                    // Default case.
                break;
            }//end switch
        }//end if

        return $weights;

    }//end _getSearchParamsWeightage()


     /**
      * Get Extra properties sent.
      *
      * @param array $data Property data.
      *
      * @return array Properties.
      */
    public static function getPropertiesFromParams(array $data)
    {
        // Get all vars as individuals.
        extract($data);

        // Search weights.
        $get_search_params_weightage = self::_getSearchParamsWeightage(
            ['checkout' => $checkout]
        );
        // Get all weights as individuals.
        extract($get_search_params_weightage);

        // Variables.
        $excluded_cities = [
            'North Goa',
            'South Goa',
            'Central Goa',
        ];

        // ---- select clause ----
        $select_params   = [];
        $select_params[] = ' distinct p.id as id ';

        $select_params[] = ' ss.score as search_score';

        // COMMENTED FOR SEARCH SCORE
        // Scores.
        // phpcs:disable Squiz.NamingConventions.ValidVariableName.StringNotCamelCaps, Generic.Files.LineLength.TooLong
        // $select_params[] = ' '.$photography_score_weightage.'* (if(pph.status = 3,1,(if(pph.status = 4,'.(1 - (NON_FEATURED_PROPERTIES_PER_PAGE_IN_PERCENTAGE / 100)).','.(NON_FEATURED_PROPERTIES_PER_PAGE_IN_PERCENTAGE / 100).')))) as photography_score ';
        // $select_params[] = ' '.$image_score_weightage."* (CASE
        // WHEN CAST((length(p.property_images) - LENGTH(REPLACE(p.property_images,'\"', '')))/2 AS UNSIGNED) BETWEEN 0 AND 3 THEN -2
        // WHEN CAST((length(p.property_images) - LENGTH(REPLACE(p.property_images,'\"', '')))/2 AS UNSIGNED) BETWEEN 3 AND 5 THEN 0.50
        // WHEN CAST((length(p.property_images) - LENGTH(REPLACE(p.property_images,'\"', '')))/2 AS UNSIGNED) BETWEEN 6 AND 10 THEN 0.85
        // ELSE 1
        // end) AS image_score ";
        // $select_params[] = ' '.$admin_score_weightage.'* (p.admin_score) as calculated_admin_score ';
        // $select_params[] = ' '.$br_pv_score_weightage.'* (if(ps.booking_request_count > 1 or ps.views_count > 50, if(ps.booking_request_count/ps.views_count,ps.booking_request_count/ps.views_count,0), 0)) as br_pv_score ';
        // $select_params[] = ' '.$bkg_bracc_score_weightage.'* (if(ps.bookings_count/ps.approved_request_count,ps.bookings_count/ps.approved_request_count,0)) as bkg_bracc_score ';
        // $select_params[] = ' '.$rating_review_score_weightage.'*
        // if(ps.total_reviews > 10,10,if(ps.total_reviews,ps.total_reviews,0))
        // *
        // (CASE
        // WHEN !ps.property_score THEN 0
        // WHEN ps.property_score <= 2.5 THEN 0
        // WHEN ps.property_score > 2.5 AND ps.property_score <= 3 THEN 5
        // WHEN ps.property_score > 3 AND ps.property_score <= 4 THEN 8
        // ELSE 10
        // end) AS rating_review_score ';
        $select_params[] = ' '.$instant_book_score_weightage.'* '.$instant_book_score.'* (if('.$days.' - if(ip.custom_price_days,ip.custom_price_days,0) > 0, if(if(ip.is_instant_booking is not null,ip.is_instant_booking,1) < p.instant_book,if(ip.is_instant_booking is not null,ip.is_instant_booking,1),p.instant_book) , if(ip.is_instant_booking is not null,ip.is_instant_booking,1))) as instant_book_score ';

        // COMMENTED FOR SEARCH SCORE
        // $select_params[] = ' '.$click_view_score_weightage.'* (if(if(pts.views,pts.views,0) > 100, if(pts.clicks,pts.clicks,0)/if(pts.views,pts.views,0), 0)) as calculated_click_view_score ';
        // $select_params[] = ' '.$bkg_view_score_weightage.'* (if(if(ps.bookings_count,ps.bookings_count,0) > 0, if(ps.bookings_count,ps.bookings_count,0)/if(pts.views,pts.views,0), 0)) as calculated_bkg_view_score ';
        // $select_params[] = ' '.$host_app_activity_weightage.'* (CASE
        // WHEN ps.app_last_active >= 0  AND ps.app_last_active <= 1 THEN 100
        // WHEN ps.app_last_active >= 1  AND ps.app_last_active <= 2 THEN 70
        // WHEN ps.app_last_active >= 2  AND ps.app_last_active <= 3 THEN 50
        // WHEN ps.app_last_active >= 4  AND ps.app_last_active <= 7 THEN 20
        // WHEN ps.app_last_active >= 8  AND ps.app_last_active <= 15 THEN 10
        // WHEN ps.app_last_active >= 16  AND ps.app_last_active <= 30 THEN 7
        // WHEN ps.app_last_active = -1 OR ps.app_last_active > 45 THEN 0
        // ELSE 0
        // end) AS host_app_activity_score ';
        // $select_params[] = " if(ps.booking_request_count >= 10, 1, 0) * $rejection_rate_weightage * (CASE
        // WHEN ps.rejected_request_count <= 0 THEN 0
        // WHEN (ps.rejected_request_count/booking_request_count) * 100 >= 15  AND (ps.rejected_request_count/booking_request_count) * 100 <= 30 THEN -2
        // WHEN (ps.rejected_request_count/booking_request_count) * 100 >= 31  AND (ps.rejected_request_count/booking_request_count) * 100 <= 40 THEN -4
        // WHEN (ps.rejected_request_count/booking_request_count) * 100 >= 41  AND (ps.rejected_request_count/booking_request_count) * 100 <= 50 THEN -6
        // WHEN (ps.rejected_request_count/booking_request_count) * 100 >= 51  AND (ps.rejected_request_count/booking_request_count) * 100 <= 60 THEN -8
        // WHEN (ps.rejected_request_count/booking_request_count) * 100 >= 61 THEN -10
        // ELSE 0
        // end) AS rejection_rate_score ";
        // $select_params[] = " if(ps.booking_request_count >= 10, 1, 0) * $acceptance_rate_weightage * (CASE
        // WHEN ps.approved_request_count <= 0 THEN 0
        // WHEN (ps.approved_request_count/booking_request_count) * 100 >= 90 THEN 20
        // ELSE 0
        // end) AS acceptance_rate_score ";
        // $select_params[] = " if(ps.booking_request_count >= 10, 1, 0) * $calendar_updated_weightage * (CASE
        // WHEN ps.rejected_request_count <= 0 OR (ps.rejected_request_count/booking_request_count) * 100 <= 15 THEN 10
        // WHEN ps.calendar_last_updated = -1 THEN 0
        // WHEN ps.calendar_last_updated >= 0  AND ps.calendar_last_updated <= 3 THEN 10
        // WHEN ps.calendar_last_updated >= 4  AND ps.calendar_last_updated <= 7 THEN 8
        // WHEN ps.calendar_last_updated >= 8  AND ps.calendar_last_updated <= 15 THEN 5
        // WHEN ps.calendar_last_updated >= 16  AND ps.calendar_last_updated <= 30 THEN 4
        // WHEN ps.calendar_last_updated >= 31  AND ps.calendar_last_updated <= 45 THEN 2
        // ELSE 0
        // end) AS calendar_updated_score ";
        $select_params[] = ' ps.performance_score as performance_score ';
        $select_params[] = ' if(u.email,1,0) + if(u.contact,1,0) + if(u.secondry_contact,1,0) as contacts ';
        $select_params[] = ' u.email_verify + u.mobile_verify as verifications ';
        $select_params[] = ' RTRIM(u.name) as host_name ';
        $select_params[] = ' u.id as host_id ';
        $select_params[] = ' u.gender as host_gender ';
        $select_params[] = ' u.profile_img as host_image ';
        $select_params[] = ' round(ps.property_score,1) as property_score ';
        $select_params[] = " if($days - if(ip.custom_price_days,ip.custom_price_days,0) > 0, if(if(ip.is_instant_booking is not null,ip.is_instant_booking,1) < p.instant_book,if(ip.is_instant_booking is not null,ip.is_instant_booking,1),p.instant_book) , if(ip.is_instant_booking is not null,ip.is_instant_booking,1)) as instant_book ";
        $select_params[] = ' p.search_keyword ';
        $select_params[] = ' p.room_type ';
        $select_params[] = ' p.property_type ';
        $select_params[] = ' p.title ';
        $select_params[] = ' p.area ';
        $select_params[] = ' p.city ';
        $select_params[] = ' p.state ';
        $select_params[] = ' p.country ';
        $select_params[] = ' p.bedrooms ';
        $select_params[] = ' p.check_in ';
        $select_params[] = ' p.checkout ';

        $final_rate = " ((ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) * pp.per_night_price + (if(pp.additional_guest_count, if($guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) > 0, $guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))), 0),0)) * pp.additional_guest_fee + if(pp.cleaning_mode = 1,if(pp.cleaning_fee/$days,pp.cleaning_fee/$days,0),if($days = 0,0,pp.cleaning_fee))) ";

        $select_params[] = " ceil((((($days-if(ip.custom_price_days,ip.custom_price_days,0)) * $final_rate/(100 - p.service_fee))*100 + ( ($days-if(ip.custom_price_days, ip.custom_price_days, 0)) * ( ($final_rate) * (pp.markup_service_fee/100) )) + if(ip.custom_rate,ip.custom_rate,0))/cc.exchange_rate)*$user_currency_factor/$days) as final_rate";

        $select_params[] = " ceil
        (
          ((
            ($days-if(ip.custom_price_days,ip.custom_price_days,0)
          )
           * $final_rate
        + (
            ($days
              -
                if(ip.custom_price_days, ip.custom_price_days, 0)
            )
            *
            (
              ($final_rate) * (pp.markup_service_fee/100)
            )
          )
       + if(ip.custom_rate_without_service_fee,ip.custom_rate_without_service_fee,0)
       )
       /cc.exchange_rate
      )*$user_currency_factor/$days) as final_rate_without_service_fee";

        $actual_rate_param = " ((ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) * pp.per_night_price + (if(pp.additional_guest_count, if($guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) > 0, $guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))), 0),0)) * pp.additional_guest_fee + if(pp.cleaning_mode = 1,if(pp.cleaning_fee/$days,pp.cleaning_fee/$days,0),if($days = 0,0,pp.cleaning_fee))) ";

        $select_params[] = "ceil((((($days-if(ip.custom_price_days,ip.custom_price_days,0)) * $actual_rate_param/(100 - p.service_fee))*100 + ( ($days-if(ip.custom_price_days, ip.custom_price_days, 0)) * ( ($actual_rate_param) * (pp.markup_service_fee/100)) ) + if(ip.orig_custom_rate,ip.orig_custom_rate,0))/cc.exchange_rate)*$user_currency_factor/$days) as actual_rate";

        $select_params[] = "ceil(((($days-if(ip.custom_price_days,ip.custom_price_days,0)) * $actual_rate_param + ( ($days-if(ip.custom_price_days, ip.custom_price_days, 0)) * ( ($actual_rate_param) * (pp.markup_service_fee/100)) ) + if(ip.orig_custom_rate_without_service_fee,ip.orig_custom_rate_without_service_fee,0))/cc.exchange_rate)*$user_currency_factor/$days) as actual_rate_without_service_fee";

        $select_params[] = " if(p.cash_on_arrival =1 AND $days * ceil((((($days-if(ip.custom_price_days,ip.custom_price_days,0)) *
        (( 1 + pp.markup_service_fee/100 ) * ((ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) * pp.per_night_price + (if(pp.additional_guest_count, if($guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) > 0, $guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))), 0),0)) * pp.additional_guest_fee + if(pp.cleaning_mode = 1,if(pp.cleaning_fee/$days,pp.cleaning_fee/$days,0),if($days = 0,0,pp.cleaning_fee))))/(100 - p.service_fee))*100 + if(ip.custom_rate,ip.custom_rate,0))/cc.exchange_rate)*$user_currency_factor/$days) <=  $max_coa_amount , 1 ,0) as cash_on_arrival ";

        $select_params[] = "if
         (  p.cash_on_arrival =1 AND $days *

          ceil
          (
            (
              (
                (
                  (
                    $days-if(ip.custom_price_days,ip.custom_price_days,0
                  )
                ) *
                (
                  (
                    1 + pp.markup_service_fee/100
                  )
                  *
                  (
                    (
                      ceil
                        (
                          if
                            (
                              $guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms
                            )
                        )
                    ) * pp.per_night_price

                    +

                    (
                      if(pp.additional_guest_count, if($guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) > 0, $guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))), 0),0)
                    ) * pp.additional_guest_fee

                    +

                    if
                    (
                        pp.cleaning_mode = 1,
                        if
                          (
                            pp.cleaning_fee/$days,pp.cleaning_fee/$days,0
                          )
                          ,if
                            (
                              $days = 0,0,pp.cleaning_fee
                            )
                    )
                  )
                )
              )*100

          + if(ip.custom_rate,ip.custom_rate,0))/cc.exchange_rate)*$user_currency_factor/$days) <=  $max_coa_amount , 1 ,0) as cash_on_arrival_without_service_fee  ";

        $select_params[] = " '$currency' as final_currency ";
        $select_params[] = ' p.v_lat ';
        $select_params[] = ' p.v_lng ';
        $select_params[] = ' p.latitude ';
        $select_params[] = ' p.longitude ';
        $select_params[] = ' pp.per_night_price ';
        $select_params[] = " if($bedroom/p.bedrooms >= 1,$bedroom/p.bedrooms,1+(1 - $bedroom/p.bedrooms)) as bedroom_score ";
        $select_params[] = " ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms)) as units_consumed ";

        $select_params[] = ' pt.name as property_type_name ';
        $select_params[] = ' rt.name as room_type_name ';
        $select_params[] = ' p.accomodation ';
        $select_params[] = ' p.fake_discount ';
        $select_params[] = ' p.prive ';
        $select_params[] = ' p.cancelation_policy ';
        $select_params[] = ' ip.instant_booking_concat ';
        $select_params[] = ' ip.smart_discount ';

        if (empty($lat) === false && empty($lng) === false) {
            $select_params[] = '
                                (3959 * acos (
                                    cos ( radians('.$lat.') )
                                    * cos( radians(latitude) )
                                    * cos( radians(longitude) - radians( '.$lng.') )
                                    + sin ( radians( '.$lat.') )
                                    * sin( radians(latitude) ))) as distance ';
        }

        if (empty($checkin) === false && empty($checkout) === false) {
            $select_params[] = ' ip.min_units ';
        }

        // ---- from clause ----
        $property_tile_stats_table = PropertyTileStat::getWorkingTableName();

        $from_params   = [];
        $from_params[] = ' properties p ';
        $from_params[] = ' INNER JOIN property_pricing pp on p.id = pp.pid ';
        $from_params[] = ' INNER JOIN currency_conversion cc on p.currency = cc.currency_code ';
        // Used for column promoted.
        $from_params[] = " LEFT JOIN $property_tile_stats_table pts on pts.pid = p.id ";
        $from_params[] = ' LEFT JOIN property_stats_new ps on ps.id=p.id ';
        $from_params[] = ' LEFT JOIN users u on u.id = p.user_id ';

        if (isset($checkin) === true && isset($checkout) === true) {
            $ip_join = '';

            $custom_rate = "(
                (
                  1 - (
                    if(
                      ip.smart_discount,
                      if(
                        ip.smart_discount > ip.discount,
                        ip.smart_discount,
                        ip.discount
                      ),
                      ip.discount
                    )
                  )/100
                )
                *(((ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) * ip.price + (if(pp.additional_guest_count, if($guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) > 0, $guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))), 0),0)) * if(ip.extra_guest_cost,ip.extra_guest_cost,0)) ))";

            $orig_custom_rate = "((ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) * ip.price + (if(pp.additional_guest_count, if($guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) > 0, $guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))), 0),0)) * if(ip.extra_guest_cost,ip.extra_guest_cost,0) )";

            $ip_join .= " left join (select
                                        ip.pid,
                                        ip.date,
                                        if(min(is_available) > 0 ,min(available_units * is_available),0 ) as min_units,
                                        if(min(is_available) > 0 ,min(booked_units * is_available),0 ) as booked_units,
                                        group_concat(ip.instant_booking) as instant_booking_concat,
                                        min(ip.instant_booking) as is_instant_booking,
                                        ip.discount,
                                        ip.service_fee,
                                        avg(ip.extra_guest_cost) AS avg_extra_guest_cost,
                                        count(*) AS custom_price_days,
                                        sum(ip.price) AS toal_custom_price,
                                        sum(ip.extra_guest_cost) AS total_custom_avg_extra_guest_cost,
                                        if(sum(booked_units), sum(booked_units), 0) as total_booked_units,


                                        sum($custom_rate /  (1 - (if(ip.service_fee,ip.service_fee,p.service_fee))/100)+  (($custom_rate)* (  ip.markup_service_fee/100 ))) as custom_rate ,

                                        sum($custom_rate +  (($custom_rate)* (  ip.markup_service_fee/100 ))) as custom_rate_without_service_fee ,

                                      sum($orig_custom_rate / (1 - (if(ip.service_fee,ip.service_fee,p.service_fee))/100) + (($orig_custom_rate ) * (  ip.markup_service_fee/100 ))) as orig_custom_rate,

                                      sum($orig_custom_rate  + (($orig_custom_rate ) * (  ip.markup_service_fee/100 ))) as orig_custom_rate_without_service_fee,

                                        max(smart_discount) as smart_discount
                                    from inventory_pricing ip
                                    left join properties p on p.id = ip.pid
                                    left join property_pricing pp on p.id = pp.pid
                                    where
                                        ip.date >='$checkin'
                                        and
                                        ip.date < '$checkout' ";

            if (empty($country) === false) {
                $ip_join .= " and p.country = '{$country}' ";
            }

            if (empty($state) === false) {
                $ip_join .= " and p.state = '{$state}' ";
                if (empty($city) === false && in_array($city, $excluded_cities) === false) {
                    // Fixed for Mapbox Search (In Old code only city comdition applied).
                    if (DEFAULT_AUTOCOMPLETE_API === 'MAPBOX_AUTOCOMPLETE') {
                        $ip_join .= " and (p.city='{$city}'".((empty($area) === false) ? " or p.city = '{$area}') " : ') ');
                    } else {
                        $ip_join .= " and (p.city='{$city}' or p.area like'%{$city}%') ";
                    }
                }
            }

            if (empty($exclude_city) === true && $view !== 'map') {
                $ip_join .= " and p.city != '".$exclude_city."' ";
            }

            $ip_join .= ' group by ip.pid
                                    ) ip on p.id = ip.pid ';

            $from_params[] = $ip_join;
        } else {
            $from_params[] = ' left join (select null as pid, null as avg_extra_guest_cost, null as min_units, 0 as booked_units, 1 as is_instant_booking, null AS instant_booking_concat, null AS custom_price_days, null AS toal_custom_price, null AS total_custom_avg_extra_guest_cost, 0 as total_booked_units, 0 as custom_rate, 0 as custom_rate_without_service_fee, 0 as orig_custom_rate_without_service_fee, 0 as discount, null as service_fee ) ip on p.id = ip.pid ';
        }//end if

        $from_params[] = ' left join property_type pt on p.property_type = pt.id ';
        $from_params[] = ' left join room_type rt on p.room_type = rt.id ';
        // COMMENTED OUT FOR SEARCH SCORE
        // $from_params[] = ' left join property_photography pph on p.id = pph.pid ';
        $from_params[] = ' left join search_score ss on p.id = ss.pid ';

        // ---- where clause ----
        $where_params   = [];
        $where_params[] = ' p.status = 1 ';
        $where_params[] = ' p.enabled = 1 ';
        $where_params[] = ' p.deleted_at IS NULL ';
        $where_params[] = ' p.latitude != 0 ';
        $where_params[] = ' p.longitude != 0 ';
        $where_params[] = ' p.admin_score != 0 ';

        // Country and state in where clause.
        if (empty($country) === false) {
            $where_params[] = " p.country='$country' ";
        }

        if (empty($state) === false && $view !== 'map') {
            $where_params[] = " p.state='$state' ";
        }

        // Add city.
        $city_condition = '';
        if (empty($city) === false && empty($state) === false && in_array($city, $excluded_cities) === false && $view !== 'map') {
            // Fixed for Mapbox Search (In Old code only city comdition applied).
            if (DEFAULT_AUTOCOMPLETE_API === 'MAPBOX_AUTOCOMPLETE') {
                $city_condition = " p.city='$city'".((empty($area) === false) ? " or p.city = '$area' " : ' ');
            } else {
                $city_condition = " p.city='$city' ";
            }
        }

        if (empty($city_condition) === false) {
            $where_params[] = '('.$city_condition.')';
        }

        // Search keywords clause and area clause.
        if (empty($search_keyword) === false && count($search_keyword) > 0) {
            $new_keyword_arr = [];
            foreach ($search_keyword as $row) {
                $new_keyword_arr[] = "p.search_keyword like '%".$row."%'";
            }

            $all_keyword    = implode(' OR ', $new_keyword_arr);
            $where_params[] = "($all_keyword)";
        } else {
            if (empty($area) === false) {
                $where_params[] = " p.area like '%$area%' ";
            }
        }

        // Exclude areas.
        if (empty($exclude_area) === false && $view !== 'map') {
            $where_params[] = " p.area not like '%".$exclude_area."%' ";
        }

        // Look into this condition.
        // Checkin and checkout dates.
        if (empty($checkin) === false && empty($checkout) === false) {
            $where_params[] = " p.min_nights <= $days ";
            $where_params[] = " p.max_nights >= $days ";
        }

        // Cancellation policies.
        if (empty($cancellation_policy) === false && count($cancellation_policy) > 0) {
            $cancellation_policy = implode($cancellation_policy, ',');
            $where_params[]      = " p.cancelation_policy in ($cancellation_policy) ";
        }

        // Bedrooms.
        if ($bedroom > 0) {
            $where_params[] = " p.bedrooms*p.units >= $bedroom ";
        }

        // Room types.
        if (empty($roomtype) === false) {
            $roomtype       = implode($roomtype, ',');
            $where_params[] = " p.room_type in ($roomtype)";
        }

        // Property type.
        if (empty($property_type) === false && count($property_type) > 0) {
            $property_type  = implode($property_type, ',');
            $where_params[] = " p.property_type in ($property_type)";
        }

        // Amenities.
        if (empty($amenities) === false) {
            // Check if both private and public pool are selected.
            $pools_selected = 0;
            if (in_array(SHARED_POOL_AMENITY_ID, $amenities) === true && in_array(PRIVATE_POOL_AMENITY_ID, $amenities) === true) {
                $pools_selected = 1;
            }

            // Iterate over amenities.
            foreach ($amenities as $one_amenity) {
                if ($pools_selected === 1 && ((int) $one_amenity === SHARED_POOL_AMENITY_ID || (int) $one_amenity === PRIVATE_POOL_AMENITY_ID)) {
                    continue;
                }

                $where_params[] = ' find_in_set('.$one_amenity.',p.amenities) > 0 ';
            }

            // Private and shared pools are selected.
            if ($pools_selected === 1) {
                $where_params[] = ' (find_in_set('.SHARED_POOL_AMENITY_ID.',p.amenities) > 0 or find_in_set('.PRIVATE_POOL_AMENITY_ID.',p.amenities) > 0) ';
            }
        }//end if

        // Instant book filter.
        if ((int) $instant_book === 1) {
            $where_params[] = "  if($days - if(ip.custom_price_days,ip.custom_price_days,0) > 0, if(if(ip.is_instant_booking is not null,ip.is_instant_booking,1) < p.instant_book,if(ip.is_instant_booking is not null,ip.is_instant_booking,1),p.instant_book) , if(ip.is_instant_booking is not null,ip.is_instant_booking,1)) = 1 ";
        }

        // Cash on arrival filter.
        // Service is wrongly applied on this
        // When you will start using this.
        // Fix it, take refrecnce from.
        // Select param of same name.
        if ((int) $cash_on_arrival === 1) {
            $where_params[] = " if(p.cash_on_arrival =1 AND $days * ceil((((($days-if(ip.custom_price_days,ip.custom_price_days,0)) *
        (( 1 + pp.markup_service_fee/100 ) * ((ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) * pp.per_night_price + (if(pp.additional_guest_count, if($guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))) > 0, $guests - pp.additional_guest_count * (ceil(if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms))), 0),0)) * pp.additional_guest_fee + if(pp.cleaning_mode = 1,if(pp.cleaning_fee/$days,pp.cleaning_fee/$days,0),if($days = 0,0,pp.cleaning_fee))))/(100 - p.service_fee))*100 + if(ip.custom_rate,ip.custom_rate,0))/cc.exchange_rate)*$user_currency_factor/$days) <=  $max_coa_amount , 1 ,0) = 1  ";
        }//end if

        // Guests.
        if ($guests > 0) {
            $where_params[] = ' p.accomodation * p.units >= '.$guests.' ';
        }

        // Checkin and checkout dates.
        if (empty($checkin) === false && empty($checkout) === false) {
            if ($guests > 0) {
                $where_params[] = " (ip.min_units is null or ip.min_units >= if($guests/p.accomodation > $bedroom/p.bedrooms, $guests/p.accomodation, $bedroom/p.bedrooms) ) ";
            } else {
                $where_params[] = ' (ip.min_units is null or ip.min_units> 0) ';
            }
        }

        // Commented because show wrong data when min_budget or max_budget was missing.
        // phpcs:disable
        //$min_budget     = ($min_budget == 0) ? Helper::convertPriceToCurrentCurrency('INR', MIN_FILTER_BUDGET_VALUE_IN_INR, $currency) : $min_budget;

        //$max_budget     = ($max_budget == 0) ? Helper::convertPriceToCurrentCurrency('INR', MAX_FILTER_BUDGET_VALUE_IN_INR, $currency) : $max_budget;
        // $min_budget_inr = Helper::convertPriceToCurrentCurrency($currency, $min_budget, 'INR');

        // // Min budget.
        // if ((int) $min_budget_inr >= 100) {
        //     // Max amount in usd.
        //     $max_amount_in_usd = Helper::convertPriceToCurrentCurrency($currency, $max_budget, 'USD');

        //     // Where clause.
        //     $budget_where_clause = " ceil((((($days-if(ip.custom_price_days,ip.custom_price_days,0)) * $actual_rate_param/(100 - p.service_fee))*100 + ( ($days-if(ip.custom_price_days, ip.custom_price_days, 0)) * ( ($actual_rate_param) * (pp.markup_service_fee/100)) ) + if(ip.custom_rate,ip.custom_rate,0))/cc.exchange_rate)*$user_currency_factor/$days) ";

        //     // Where clause.
        //     $budget_without_service_fee_where_clause = " ceil(((($days-if(ip.custom_price_days,ip.custom_price_days,0)) * $actual_rate_param + ( ($days-if(ip.custom_price_days, ip.custom_price_days, 0)) * ( ($actual_rate_param) * (pp.markup_service_fee/100)) ) + if(ip.custom_rate,ip.custom_rate,0))/cc.exchange_rate)*$user_currency_factor/$days) ";

        //     // If max amout is huge then remove max budget.
        //     if ($max_amount_in_usd >= 1000) {
        //         $where_params[] = $budget_without_service_fee_where_clause." >= $min_budget ";
        //     } else {
        //         $where_params[] = $budget_without_service_fee_where_clause." BETWEEN $min_budget and $max_budget ";
        //     }
        // }
        // phpcs:enable

        // Apply Min Max Budget.
        $budget_without_service_fee_where_clause = " ceil(((($days-if(ip.custom_price_days,ip.custom_price_days,0)) * $actual_rate_param + ( ($days-if(ip.custom_price_days, ip.custom_price_days, 0)) * ( ($actual_rate_param) * (pp.markup_service_fee/100)) ) + if(ip.custom_rate,ip.custom_rate,0))/cc.exchange_rate)*$user_currency_factor/$days) ";

        if (empty($min_budget) === false && empty($max_budget) === false && $max_budget > $min_budget) {
            // When Min and max budget passed in query string.
            $where_params[] = $budget_without_service_fee_where_clause." BETWEEN $min_budget and $max_budget ";
        } else if (empty($min_budget) === false && empty($max_budget) === true) {
            // When only min budget passed in query string.
            $where_params[] = $budget_without_service_fee_where_clause." >= $min_budget ";
        } else if (empty($max_budget) === false && empty($min_budget) === true) {
            // When only max budget passed in query string.
            $where_params[] = $budget_without_service_fee_where_clause." <= $max_budget ";
        }

        // Within distance clause.
        if ($within_distance > 0 && empty($lat) === false && empty($lng) === false) {
            $where_params[] = '
                    (3959 * acos (
                    cos ( radians('.$lat.') )
                    * cos( radians(p.latitude) )
                    * cos( radians(p.longitude) - radians( '.$lng.') )
                    + sin ( radians( '.$lat.') )
                    * sin( radians(p.latitude) ))) < '.$within_distance.' ';
        }

        // Bounds for map search.
        if (empty($bounds_nelat) === false && empty($bounds_nelng) === false && empty($bounds_swlat) === false && empty($bounds_swlng) === false) {
            $where_params[] = ' v_lat > '.$bounds_swlat.' ';
            $where_params[] = ' v_lat < '.$bounds_nelat.' ';
            $where_params[] = ' v_lng > '.$bounds_swlng.' ';
            $where_params[] = ' v_lng < '.$bounds_nelng.' ';
        }

        // Extra params.
        $is_prive_clause_added = 0;
        if (empty($extra_params) === false) {
            foreach ($extra_params as $extra_param_key => $extra_param_value) {
                switch ($extra_param_key) {
                    case 'prive':
                        // Prive property.
                        if ((int) $extra_param_value === 1) {
                            $is_prive_clause_added = 1;
                        }

                            $where_params[] = ' p.prive = 1 ';
                    break;

                    case 'is_promotional':
                        // Is promotional.
                        $is_promotional = $extra_param_value;
                    break;

                    default:
                        // Default Case.
                    break;
                }
            }//end foreach
        }//end if

        // Prive is true and clause not already added.
        if ((int) $prive === 1 && (int) $is_prive_clause_added === 0) {
            $where_params[] = ' p.prive = 1 ';
        }

        // Show only properties with gh_commision > 15.
        if ((int) $is_promotional === 1) {
            $where_params[] = ' pp.gh_commission >= 15 ';
        }

        // ---- order params ----
        $order_params = [];

        if (empty($share) === false) {
            $order_params[] = ' (case when p.id in ('.$share.') then -1 else 0 end) ';
        }

        switch ($sort) {
            /*
                Case 'popularity':
                Popularity.
                Break;
            */

            case 'low_to_high':
                // Price low to high.
                $order_params[] = ' final_rate asc ';
            break;

            case 'high_to_low':
                // Price high to low.
                $order_params[] = ' final_rate desc ';
            break;

            case 'ratings':
                // Ratings sort.
                $order_params[] = ' property_score desc ';
            break;

            default:
                // Empty case.
            break;
        }//end switch

        // Bedroom in order clause.
        if ($bedroom > 0) {
            $order_params[] = ' bedroom_score asc ';
        }

        // Other order params.
        // Added priority order for getting non priority property in last.
        $order_params[] = ' prive desc, (case priority when 0 then 99999 else 1 end) asc, exclusive desc, featured desc ';

        // Order based on scores.
        $order_params[] = ' (search_score + instant_book_score)  desc';

        if (empty($exclude_area) === false && $view !== 'map') {
            unset($order_params);
            $order_params[] = ' distance asc ';
        }

        // ---- search results ----
        $select_params_total = [];

        // Count featured properties.
        $having_clause          = [];
        $total_featured_results = 0;

        $promoted_featured_condition_for_popularity = [];

        // In guesthouser 5, its no base ,1 and 4 and others.
        if (in_array($sort, ['popularity', 'ratings']) === false) {
            // Featured properties - not promoted or featured.
            // 75% of properties are these.
            $total_featured_query = Helper::buildQuery(
                [
                    'select' => [' count(distinct p.id) as total '],
                    'from'   => $from_params,
                    'where'  => array_merge($where_params, [' (promoted = 1 && featured != 1) ']),
                    'having' => $having_clause,
                ]
            );

            // Get total non featured properties.
            $featured_properties_data = DB::select($total_featured_query);
            $total_featured_results   = (isset($featured_properties_data[0]) === true ) ? $featured_properties_data[0]->total : 0;

            $promoted_featured_condition_for_popularity = [' (promoted != 1 or featured = 1)  '];
        }

        // Non featured properties - promoted but not featured.
        // 25% of properties are these.
        $total_non_featured_properties_query = Helper::buildQuery(
            [
                'select' => [' count(distinct p.id) as total '],
                'from'   => $from_params,
                'where'  => array_merge($where_params, $promoted_featured_condition_for_popularity),
                'having' => $having_clause,
            ]
        );

        // Get total non featured properties.
        $non_featured_properties_data = DB::select($total_non_featured_properties_query);
        $total_non_featured_results   = (isset($non_featured_properties_data[0]) === true) ? $non_featured_properties_data[0]->total : 0;

        // Total properties found count.
        $total_properties_count = ($total_featured_results + $total_non_featured_results);

        // Get query limit params array.
        $featured_non_featured_pagination = Helper::getPaginationLimits(
            [
                'per_page'                   => $per_page,
                'total_featured_results'     => $total_featured_results,
                'total_non_featured_results' => $total_non_featured_results,
            ]
        );

        // Featured and non featured properties.
        $featured_properties     = [];
        $non_featured_properties = [];

        // Get non featured properties.
        if ($total_non_featured_results > 0) {
            $limit_clause = [
                0,
                0,
            ];
            if (isset($featured_non_featured_pagination['non_featured'][$current_page_number]) === true) {
                $limit_clause = [
                    $featured_non_featured_pagination['non_featured'][$current_page_number]['offset'],
                // Offset.
                    $featured_non_featured_pagination['non_featured'][$current_page_number]['total'],
                // Count.
                ];
            }

            // Promoted !=1 and featured =1 is taken from website
            // Build and run query to fetch properties.
            $non_featured_results_query = Helper::buildQuery(
                [
                    'select' => array_merge($select_params, [' 1 as is_promoted ']),
                    'from'   => $from_params,
                    'where'  => array_merge($where_params, $promoted_featured_condition_for_popularity),
                    'having' => $having_clause,
                    'order'  => $order_params,
                    'limit'  => $limit_clause,
                ]
            );

            $non_featured_properties = DB::select($non_featured_results_query);
        }//end if

        // Get featured properties.
        if ($total_featured_results > 0) {
            $limit_clause = [
                0,
                0,
            ];
            if (isset($featured_non_featured_pagination['featured'][$current_page_number]) === true) {
                $limit_clause = [
                    $featured_non_featured_pagination['featured'][$current_page_number]['offset'],
                // Offset.
                    $featured_non_featured_pagination['featured'][$current_page_number]['total'],
                // Count.
                ];
            }

             // Promoted =1 and featured !=1 is taken from website
            // Build and run query to fetch properties.
            $featured_results_query = Helper::buildQuery(
                [
                    'select' => array_merge($select_params, [' 0 as is_promoted ']),
                    'from'   => $from_params,
                    'where'  => array_merge($where_params, [' (promoted = 1 && featured != 1)  ']),
                    'having' => $having_clause,
                    'order'  => $order_params,
                    'limit'  => $limit_clause,
                ]
            );
            $featured_properties    = DB::select($featured_results_query);
        }//end if

        // Combined featured and non featured properties list.
        $properties_sorted_list = [];

        // Featured = 75%, non_featured = 25% then put them in specific order.
        if (count($featured_properties) === floor(NUMBER_OF_PROPERTIES_PER_PAGE * (1 - (NON_FEATURED_PROPERTIES_PER_PAGE_IN_PERCENTAGE / 100)))
            && count($non_featured_properties) === floor(NUMBER_OF_PROPERTIES_PER_PAGE * (NON_FEATURED_PROPERTIES_PER_PAGE_IN_PERCENTAGE / 100))
        ) {
            // Total properties found.
            $total_properties_found = (count($featured_properties) + count($non_featured_properties));

            // Iterate over all properties and combine them in order.
            for ($i = 0; $i < $total_properties_found; $i++) {
                if (in_array($i, STRICT_FEATURED_PROPERTIES_POSITIONS) === true) {
                    // Strict property.
                    $properties_sorted_list[] = array_shift($non_featured_properties);
                } else {
                    $properties_sorted_list[] = array_shift($featured_properties);
                }
            }
        } else {
            // If 75% and 25% ratio is not maintained then add featured and then non featured.
            $properties_sorted_list = array_merge($featured_properties, $non_featured_properties);
        }//end if

        // Properties sorted list .
        foreach ($properties_sorted_list as $one_sorted_property) {
            // New score of property.
            $one_sorted_property->new_score = ($one_sorted_property->search_score + $one_sorted_property->instant_book_score);

            // Calculate fake price of a property after deducting fake discount.
            $one_sorted_property->fake_price = $one_sorted_property->final_rate;

            // If fake discount is there.
            if ($one_sorted_property->fake_discount > 0) {
                // Set fake price.
                $one_sorted_property->fake_price = ceil(($one_sorted_property->final_rate * 100) / (100 - $one_sorted_property->fake_discount));
            }

            $one_sorted_property->full_coa = 0;

            // Show full cash on arrival.
            if (in_array($one_sorted_property->cancelation_policy, FULL_REFUND_CANCELLATION_POLICY) === true) {
                $one_sorted_property->full_coa = 1;
            }
        }//end foreach

        // For Near By properties.
        if (empty($lat) === false && empty($lng) === false && empty($search_keyword) === true && $view !== 'map' && empty($area) === false && $total_properties_count < $per_page) {
            end($where_params);
            $keyend = key($where_params);

            for ($i = 0; $i < $keyend; $i++) {
                if (preg_match('#\b(p.area|p.search_keyword)\b#', $where_params[$i])) {
                    unset($where_params[$i]);
                }
            }

            $where_params[] = " p.area not like '%".strtolower($area)."%' ";
            unset($order_params);

            $order_params[] = ' distance asc ';
            $limit_clause   = [
                0,
                $per_page,
            ];

            // Promoted !=1 and featured =1 is taken from website
            // Build and run query to fetch properties.
            $near_by_property_results_query = Helper::buildQuery(
                [
                    'select' => $select_params,
                    'from'   => $from_params,
                    'where'  => $where_params,
                    'having' => $having_clause,
                    'order'  => $order_params,
                    'limit'  => $limit_clause,
                ]
            );

            $near_by_properties = DB::select($near_by_property_results_query);

            usort(
                $near_by_properties,
                function ($a, $b) {
                    return $a->performance_score == $b->performance_score ? 0 : ( $a->performance_score > $b->performance_score ) ? -1 : 1;
                }
            );

            reset($near_by_properties);
            $near_by_properties_first_key = key($near_by_properties);

            if (empty($near_by_properties) === false) {
                $near_by_properties[$near_by_properties_first_key]->show_nearby_text = 1;
            }

            $properties_sorted_list = array_merge($properties_sorted_list, $near_by_properties);
        } else if (empty($lat) === false && empty($lng) === false && empty($search_keyword) === true && $view !== 'map' && empty($area) === true && empty($city) === false && empty($exclude_city) === false && $total_properties_count < $per_page) {
            $having_clause  = [];
            $distance       = 2;
            $where_params[] = '
              (3959 * acos (
                cos ( radians('.$lat.') )
                * cos( radians(latitude) )
                * cos( radians(longitude) - radians( '.$lng.') )
                  + sin ( radians( '.$lat.'))
                  * sin( radians(latitude) ))) < '.$distance.' ';

            // Remove city, area or search_keyword.
            for ($i = 0; $i < sizeof($where_params); $i++) {
                if (preg_match('#\b(p.city|p.area|p.search_keyword)\b#', $where_params[$i])) {
                    unset($where_params[$i]);
                }
            }

            $where_params[] = " p.city != '".$city."' ";

            unset($order_params);
            $order_params[] = ' distance asc ';

            $limit_clause = [
                (($current_page_number - 1) * $per_page),
                $per_page,
            ];

            $near_by_property_results_query = Helper::buildQuery(
                [
                    'select' => $select_params,
                    'from'   => $from_params,
                    'where'  => $where_params,
                    'having' => $having_clause,
                    'order'  => $order_params,
                    'limit'  => $limit_clause,
                ]
            );

            $near_by_properties = DB::select($near_by_property_results_query);

            usort(
                $near_by_properties,
                function ($a, $b) {
                    return $a->performance_score == $b->performance_score ? 0 : ( $a->performance_score > $b->performance_score ) ? -1 : 1;
                }
            );

            reset($near_by_properties);
            $near_by_properties_first_key = key($near_by_properties);

            $properties_sorted_list = array_merge($properties_sorted_list, $near_by_properties);
        }//end if

        return [
            'properties_sorted_list' => $properties_sorted_list,
            'total_properties_count' => $total_properties_count,
        ];

    }//end getPropertiesFromParams()


    public function getPropertySearchScore($data)
    {
        $weights = $this->_getSearchParamsWeightage(['checkout' => false]);

        $getPhotographyScore = $this->getPhotographyScore($weights['photography_score_weightage'], $data['photography_status']);
        $getImageScore       = $this->getImageScore($weights['image_score_weightage'], $data['property_images']);
        $getAdminScore       = $this->getAdminScore($weights['admin_score_weightage'], $data['property_admin_score']);
        $getBookingRequestToPropertyViewsScore      = $this->getBookingRequestToPropertyViewsScore($weights['br_pv_score_weightage'], $data['booking_request_count'], $data['views_count']);
        $getBookingCountToApprovedRequestCountScore = $this->getBookingCountToApprovedRequestCountScore($weights['bkg_bracc_score_weightage'], $data['bookings_count'], $data['approved_request_count']);
        $getRatingReviewScore              = $this->getRatingReviewScore($weights['rating_review_score_weightage'], $data['total_reviews'], $data['property_score']);
        $getClickToViewScore               = $this->getClickToViewScore($weights['click_view_score_weightage'], $data['clicks'], $data['views']);
        $getBookingToViewScore             = $this->getBookingToViewScore($weights['bkg_view_score_weightage'], $data['bookings_count'], $data['views']);
        $getHostActivityScore              = $this->getHostActivityScore($weights['host_app_activity_weightage'], $data['app_last_active']);
        $getRejectionToBookingRequestScore = $this->getRejectionToBookingRequestScore($weights['rejection_rate_weightage'], $data['rejected_request_count'], $data['booking_request_count']);
         $getApprovedToRequestScore        = $this->getApprovedToRequestScore($weights['acceptance_rate_weightage'], $data['approved_request_count'], $data['booking_request_count']);
        $getCalendarScore                  = $this->getCalendarScore($weights['acceptance_rate_weightage'], $data['booking_request_count'], $data['rejected_request_count'], $data['calendar_last_updated']);

        return [
            'photography_score'           => round($getPhotographyScore, 4),
            'image_score'                 => round($getImageScore, 4),
            'admin_score'                 => round($getAdminScore, 4),
            'br_pv_score'                 => round($getBookingRequestToPropertyViewsScore, 4),
            'bkg_bracc_score'             => round($getBookingCountToApprovedRequestCountScore, 4),
            'rating_review_score'         => round($getRatingReviewScore, 4),
            'calculated_click_view_score' => round($getClickToViewScore, 4),
            'calculated_bkg_view_score'   => round($getBookingToViewScore, 4),
            'host_app_activity_score'     => round($getHostActivityScore, 4),
            'rejection_rate_score'        => round($getRejectionToBookingRequestScore, 4),
            'acceptance_rate_score'       => round($getApprovedToRequestScore, 4),
            'calendar_updated_score'      => round($getCalendarScore, 4),
            'score'                       => round(
                ($getPhotographyScore + $getImageScore + $getAdminScore + $getBookingRequestToPropertyViewsScore + $getBookingCountToApprovedRequestCountScore + $getRatingReviewScore + $getClickToViewScore + $getBookingToViewScore + $getHostActivityScore + $getRejectionToBookingRequestScore + $getApprovedToRequestScore + $getCalendarScore),
                4
            ),

        ];

            // return $score;

    }//end getPropertySearchScore()


    private function getPhotographyScore($photography_score_weightage, $photography_status)
    {
        $non_featured_pct = config('gh.search.non_featured_properties_per_page_in_percentage');

        if ($photography_status == 3) {
            $photography_value = 1;
        } else if ($photography_status == 4) {
            $photography_value = (1 - ($non_featured_pct / 100));
        } else {
            $photography_value = ($non_featured_pct / 100);
        }

        $photography_score = ($photography_score_weightage * $photography_value);

        return $photography_score;

    }//end getPhotographyScore()


    private function getImageScore($image_score_weightage, $property_images)
    {
        $images_len          = strlen($property_images);
        $images_len_stripped = strlen(str_replace('"', '', $property_images));
        $len_diff            = abs(($images_len - $images_len_stripped) / 2);

        if ($len_diff >= 0 and $len_diff <= 3) {
            $image_value = -2;
        } else if ($len_diff >= 3 and $len_diff <= 5) {
            $image_value = 0.50;
        } else if ($len_diff >= 6 and $len_diff <= 10) {
            $image_value = 0.85;
        } else {
             $image_value = 1;
        }

        $image_score = ($image_score_weightage * $image_value);

        return $image_score;

    }//end getImageScore()


    private static function getAdminScore($admin_score_weightage, $property_admin_score)
    {
        $admin_score = ($admin_score_weightage * $property_admin_score);

        return $admin_score;

    }//end getAdminScore()


    private static function getBookingRequestToPropertyViewsScore($br_pv_score_weightage, $booking_request_count, $views_count)
    {
        if (($booking_request_count > 1 or $views_count > 50) && $views_count > 0) {
            $br_pv_value = ($booking_request_count / $views_count);
        } else {
            $br_pv_value = 0;
        }

        $br_pv_score = ($br_pv_score_weightage * $br_pv_value);

        return $br_pv_score;

    }//end getBookingRequestToPropertyViewsScore()


    private static function getBookingCountToApprovedRequestCountScore($bkg_bracc_score_weightage, $bookings_count, $approved_request_count)
    {
        if ($approved_request_count > 0) {
            $bkg_brap_value = ($bookings_count / $approved_request_count);
        } else {
            $bkg_brap_value = 0;
        }

        $bkg_bracc_score = ($bkg_bracc_score_weightage * $bkg_brap_value);

        return $bkg_bracc_score;

    }//end getBookingCountToApprovedRequestCountScore()


    private static function getRatingReviewScore($rating_review_score_weightage, $total_reviews, $property_score)
    {
        if ($total_reviews > 10) {
            $review_value = 10;
        } else {
            $review_value = $total_reviews;
        }

        if ($property_score <= 2.5) {
            $property_score_value = 0;
        } else if ($property_score > 2.5 && $property_score <= 3) {
              $property_score_value = 5;
        } else if ($property_score > 3 && $property_score <= 4) {
              $property_score_value = 8;
        } else {
             $property_score_value = 10;
        }

        $rating_review_score = ($rating_review_score_weightage * $review_value * $property_score_value);

        return $rating_review_score;

    }//end getRatingReviewScore()


    private static function getClickToViewScore($click_view_score_weightage, $clicks, $views)
    {
        if ($views > 100) {
            $click_to_view = ($clicks / $views);
        } else {
            $click_to_view = 0;
        }

        $calculated_click_view_score = ($click_view_score_weightage * $click_to_view);

        return $calculated_click_view_score;

    }//end getClickToViewScore()


    private static function getBookingToViewScore($bkg_view_score_weightage, $bookings_count, $views)
    {
        if ($views > 0) {
            $booking_to_view = ($bookings_count / $views);
        } else {
            $booking_to_view = 0;
        }

        $calculated_bkg_view_score = ($bkg_view_score_weightage * $booking_to_view);

        return $calculated_bkg_view_score;

    }//end getBookingToViewScore()


    private static function getHostActivityScore($host_app_activity_weightage, $app_last_active)
    {
        if ($app_last_active >= 0 && $app_last_active <= 1) {
            $host_activity_value = 100;
        } else if ($app_last_active >= 1 && $app_last_active <= 2) {
            $host_activity_value = 70;
        } else if ($app_last_active >= 2 && $app_last_active <= 3) {
             $host_activity_value = 50;
        } else if ($app_last_active >= 4 && $app_last_active <= 7) {
            $host_activity_value = 20;
        } else if ($app_last_active >= 8 && $app_last_active <= 15) {
             $host_activity_value = 10;
        } else if ($app_last_active >= 16 && $app_last_active <= 30) {
             $host_activity_value = 7;
        } else {
            $host_activity_value = 0;
        }

        $host_app_activity_score = ($host_app_activity_weightage * $host_activity_value);

        return $host_app_activity_score;

    }//end getHostActivityScore()


    private static function getRejectionToBookingRequestScore($rejection_rate_weightage, $rejected_request_count, $booking_request_count)
    {
        $booking_request_value = $booking_request_count >= 10 ? 1 : 0;

        $reject_to_request_count_pct = 0;
        if ($booking_request_count > 0) {
             $reject_to_request_count_pct = (($rejected_request_count / $booking_request_count) * 100);
        }

        if ($reject_to_request_count_pct >= 15 && $reject_to_request_count_pct <= 30) {
            $rejection_value = -2;
        } else if ($reject_to_request_count_pct >= 31 && $reject_to_request_count_pct <= 40) {
            $rejection_value = -4;
        } else if ($reject_to_request_count_pct >= 41 && $reject_to_request_count_pct <= 50) {
             $rejection_value = -6;
        } else if ($reject_to_request_count_pct >= 51 && $reject_to_request_count_pct <= 60) {
            $rejection_value = -8;
        } else if ($reject_to_request_count_pct >= 61) {
             $rejection_value = -10;
        } else {
            $rejection_value = 0;
        }

        $rejection_rate_score = ($rejection_rate_weightage * $booking_request_value * $rejection_value);

        return $rejection_rate_score;

    }//end getRejectionToBookingRequestScore()


    private static function getApprovedToRequestScore($acceptance_rate_weightage, $approved_request_count, $booking_request_count)
    {
        $booking_request_value = $booking_request_count >= 10 ? 1 : 0;
        if ($booking_request_count > 0 && (($approved_request_count / $booking_request_count) * 100) > 90) {
            $approved_to_request_value = 20;
        } else {
            $approved_to_request_value = 0;
        }

        $acceptance_rate_score = ($acceptance_rate_weightage * $booking_request_value * $approved_to_request_value);

        return $acceptance_rate_score;

    }//end getApprovedToRequestScore()


    private static function getCalendarScore($calendar_updated_weightage, $booking_request_count, $rejected_request_count, $calendar_last_updated)
    {
        $booking_request_value = $booking_request_count >= 10 ? 1 : 0;

        $reject_to_request_count_pct = 0;
        if ($booking_request_count > 0) {
             $reject_to_request_count_pct = (($rejected_request_count / $booking_request_count) * 100);
        }

        if ($rejected_request_count <= 0 or $reject_to_request_count_pct <= 15) {
            $calender_value = 10;
        } else if ($calendar_last_updated >= 0 && $calendar_last_updated <= 3) {
            $calender_value = 10;
        } else if ($calendar_last_updated >= 4 && $calendar_last_updated <= 7) {
            $calender_value = 8;
        } else if ($calendar_last_updated >= 8 && $calendar_last_updated <= 15) {
             $calender_value = 5;
        } else if ($calendar_last_updated >= 16 && $calendar_last_updated <= 30) {
            $calender_value = 4;
        } else if ($calendar_last_updated >= 31 && $calendar_last_updated <= 45) {
             $calender_value = 2;
        } else {
            $calender_value = 0;
        }

        $calendar_updated_score = ($calendar_updated_weightage * $booking_request_value * $calender_value);

        return $calendar_updated_score;

    }//end getCalendarScore()


}//end class
