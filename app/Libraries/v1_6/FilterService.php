<?php
/**
 * Filter Service contain method defining which card to show after property tiles
 */

namespace App\Libraries\v1_6;

/**
 * Class FilterService
 */
class FilterService
{


    /**
     * Get filter card type to display after property tiles in serach.
     *
     * @param array $data Data to fetch filter cards.
     *
     * @return array filter type data.
     */
    public static function getFilterCardAndRepetition(array $data)
    {
        // Collect input params.
        $checkin                 = $data['checkin'];
        $checkout                = $data['checkout'];
        $guests                  = $data['guests'];
        $min_budget              = $data['min_budget'];
        $max_budget              = $data['max_budget'];
        $slider_min_value        = $data['slider_min_value'];
        $slider_max_value        = $data['slider_max_value'];
        $instant_book            = $data['instant_book'];
        $bedroom                 = $data['bedroom'];
        $property_type           = $data['property_type'];
        $roomtype                = $data['roomtype'];
        $amenities               = $data['amenities'];
        $search_keyword          = $data['search_keyword'];
        $cash_on_arrival         = $data['cash_on_arrival'];
        $current_page_number     = $data['current_page_number'];
        $per_page                = $data['per_page'];
        $total_properties_count  = $data['total_properties_count'];
        $nearby_properties_count = $data['nearby_properties_count'];

        $response = [
            'filter_card_type'       => '',
            'filter_card_repetition' => [0],
        ];

        if (empty($checkin) === true && empty($checkout) === true && $total_properties_count > 50) {
            // Check in - chcekout not given and results are greater than 50.
            // 0 - The FOR CHECK IN CHECK OUT FILTER.
            $response['filter_card_type']       = 'provide_dates';
            $response['filter_card_repetition'] = [
                6,
                12,
            ];
        } else if (empty($guests) === true && $total_properties_count > 50) {
            // Number of guests not given and results are greater than 50.
            // 1 - The FOR GUEST AND BEDROOM FILTER.
            $response['filter_card_type']       = 'provide_guests';
            $response['filter_card_repetition'] = [
                6,
                12,
            ];
        } else if ((($min_budget === 0 && $max_budget === 0)
            || ($min_budget === $slider_min_value && $max_budget === $slider_max_value))
            && $total_properties_count > 50
        ) {
            // Number of min and max budget not given or are at their min and max limits and results are greater than 50.
            // 2 - The FOR MIN AND MAX PRICE FILTER.
            $response['filter_card_type']       = 'provide_budget';
            $response['filter_card_repetition'] = [
                6,
                12,
            ];
        } else if ($instant_book !== 1 && $total_properties_count > 50) {
            // Instant book and results are greater than 50.
            // 3 - The FOR INSTANT AND PAYLATER FILTER.
            $response['filter_card_type']       = 'instant_book';
            $response['filter_card_repetition'] = [
                6,
                12,
            ];
        } else if ((($total_properties_count === 0)
            || ($current_page_number * $per_page > $total_properties_count))
            && (            (            ($min_budget !== 0 && $max_budget !== 0)
            && ($min_budget !== $slider_min_value && $max_budget !== $slider_max_value)            ) || $bedroom > 0 || count($property_type) > 0 || empty($roomtype) === false
            || count($amenities) > 0 || count($search_keyword) > 0 || $cash_on_arrival === 1
            || $instant_book === 1)
        ) {
            // (No properties found or page is last) and ((budget is not set or set to max limits) or any other parameter is set in params).
            // Reset filter as properties are not found because of filters applied.
            // 4 - The FOR RESET FILTER.
            $response['filter_card_type']       = 'reset_filter';
            $response['filter_card_repetition'] = ($current_page_number * $per_page >= $total_properties_count) ? [$total_properties_count] : [1];
        } else if ($total_properties_count === 0 && $nearby_properties_count > 0) {
            // Porperties not found in selected area but properties are available near by.
            // We need to fetch properties near area selected for this.
            // 5 - The FOR EXPLORE FILTER.
            $response['filter_card_type']       = 'explore_more';
            $response['filter_card_repetition'] = [0];
        } else if ($total_properties_count === 0 && (($min_budget === $slider_min_value && $max_budget === $slider_max_value)
            || $bedroom === 0 || count($property_type) === 0 || empty($roomtype) === true
            || count($amenities) === 0 || count($search_keyword) === 0 || $cash_on_arrival === 0
            || $instant_book === 0            )
        ) {
            // No properties found and no params are provided.
            // 6 - The FOR HOST WITH US FILTER.
            $response['filter_card_type']       = 'host_with_us';
            $response['filter_card_repetition'] = [0];
        }//end if

        return $response;

    }//end getFilterCardAndRepetition()


}//end class
