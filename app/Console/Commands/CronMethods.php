<?php
/**
 * This file contains the all the function that would be run as crons.
 */

namespace App\Console\Commands;


use App\Models\{Property,PropertyPhotography,PropertyTileStat,BookingRequest,ProperlyTask , PropertyMonthlyPriceBreakup};
use Carbon\Carbon;
use App\Libraries\v1_6\{BookingRequestService,SearchService};
use Elasticsearch\ClientBuilder as EsClientBuilder;

/**
 * Class CronMethods.
 */
class CronMethods
{


    /**
     * Test Function Recieves space seprated input as array
     *
     * @param array $input_array Space Seprated Input.
     *
     * @return string Message to print at console.
     */
    public function test(array $input_array)
    {
        return 'return message if need to print at console';

    }//end test()


    /**
     * Add Properly Checkin Task for booking which have checkin tomorrow
     *
     * @param array $input_array Space Seprated Input.
     *
     * @return string Message to print at console.
     */
    public function addProperlyCheckinTask(array $input_array)
    {
        $booking_details = BookingRequest::getProperlyCheckin();

        $booking_request_service = new BookingRequestService;
        foreach ($booking_details as $key => $booking_detail) {
            $property_checkin_time = @Carbon::createFromFormat('Y-m-d H:i:s', $booking_detail['from_date'].' '.$booking_detail['property_checkin_time'])->format('Y-m-d H:i:s');

            $checkin_run_at      = (empty($booking_detail['expected_checkin_datetime']) === false) ? Carbon::parse($booking_detail['expected_checkin_datetime'])->format('Y-m-d H:i:s') : $property_checkin_time;
            $create_checkin_task = $booking_request_service->createReccuringTask($booking_detail['id'], 1, $checkin_run_at);
        }

        $booking_cancelled = BookingRequest::getProperlyCanceledTask();
        foreach ($booking_cancelled as $key => $booking_detail) {
            ProperlyTask::deleteTask($booking_detail['id']);
        }

        return 'Checkin Task For Next 3 days booking Created Successfully.';

    }//end addProperlyCheckinTask()


    /**
     * Calculate aggregat search score
     *
     */
    public function aggregateSearchScore(){
        ini_set('memory_limit', -1);

        $property_tile_stats_table = PropertyTileStat::getWorkingTableName();
        $search_service = new SearchService();
        $total = 0;

        \DB::table('search_score_tmp')->truncate();

        $properties_count= \DB::table('properties as p')->count();
        \DB::table('properties as p')
            ->leftJoin('property_photography as pph','pph.pid','p.id')
            ->leftJoin('property_stats_new  as ps','ps.id','p.id')
            ->leftJoin($property_tile_stats_table.' as pts','pts.pid','p.id')
            ->select(\DB::raw("p.id, p.property_images, p.admin_score, pph.status, ps.booking_request_count, ps.views_count, ps.bookings_count,ps.approved_request_count, ps.total_reviews, ps.property_score, ps.app_last_active, ps.rejected_request_count, ps.calendar_last_updated, pts.views, pts.clicks"))
            //->where('p.id', 3)
            ->orderBy('p.id')->chunk(1000, function ($properties) use ($search_service, &$total) {
                $score = [];
                foreach ($properties as $property) {
                    $data=[
                        'photography_status' => ($property->status ?? 0) ,
                        'property_images' => ($property->property_images ?? ''),
                        'views_count' => ($property->views_count ?? 0),
                        'property_admin_score' =>($property->admin_score ?? 0),
                        'booking_request_count' =>($property->booking_request_count ?? 0),
                        'approved_request_count'=>($property->approved_request_count ?? 0),
                        'rejected_request_count'=>($property->rejected_request_count ?? 0),
                        'bookings_count' =>($property->bookings_count ?? 0),
                        'total_reviews' =>($property->total_reviews ?? 0),
                        'property_score' =>($property->property_score ?? 0),
                        'clicks' =>($property->clicks ?? 0),
                        'views' =>($property->views ?? 0),
                        'app_last_active'=>($property->app_last_active ?? -1),
                        'calendar_last_updated'=>($property->calendar_last_updated ?? -1),
                    ];

                    $score = $search_service->getPropertySearchScore($data);   
                    $score['pid']=$property->id;
                    $score['created_at']=Carbon::now()->toDateTimeString();
                    $score['updated_at']=Carbon::now()->toDateTimeString();

                    $scores[]= $score;

                    $total= $total+1;
                }
                $table=\DB::table('search_score_tmp')->insert($scores);

                echo "Inserted $total scores"."\n";
        });

        $score_properties_count = \DB::table('search_score_tmp')->count();
        if($score_properties_count == $properties_count){
            \DB::select("RENAME TABLE search_score TO search_score_bckup, search_score_tmp TO search_score, search_score_bckup to search_score_tmp");
            echo "Renaming table and finishing process";
        }
    }



    /**
     * Add data in property_monthly_price_breakup table.
     *
     * @return string Message to print at console.
     */
    public function addPropertyDataMonthWise()
    {
        // Fetch all prive properties.
         $properties = Property::getAllPriveProperties();
        // $properties = Property::getAllPrivePropertiesByBookingingData();

        
        // $extra_pids = array(array('id'=>71086,'created_at' => '2019-07-01 00:00:00'),array('id'=>73596,'created_at' => '2019-07-01 00:00:00'));

        // $properties = array_merge($properties,$extra_pids);

        foreach ($properties as $key => $value) {
            //$start_date_obj = Carbon::parse($value['created_at']);
            //phpcs:ignore
            $start_date_obj = Carbon::now()->startOfMonth()->subMonth(3);
            $end_date_obj = Carbon::now();
            $start_month  = $start_date_obj;

            while (($start_month->lte($end_date_obj)) === true) {
                $start_date = $start_month->startOfMonth()->format('Y-m-d');
                $year       = Carbon::parse($start_date)->format('Y');
                $month      = Carbon::parse($start_date)->format('m');
                $pid        = $value['id'];

                $property_booking_data = (array) BookingRequest::getPrivePropertiesMonthlyBookingAmount($pid, $start_date)[0];
                if (empty($property_booking_data) === false) {
                    // Get monthly price breakup.
                    $prperty_price_breakup = PropertyMonthlyPriceBreakup::getPriceBreakup($pid, $month, $year);

                    if (empty($prperty_price_breakup) === true) {
                        $save_monthwise_expense = PropertyMonthlyPriceBreakup::savePropertyPriceBreakup($property_booking_data, $pid, $year, $month);
                    } else {
                        $breakup_id               = $prperty_price_breakup['id'];
                        $update_monthwise_expense = PropertyMonthlyPriceBreakup::updatePropertyPriceBreakup($breakup_id, $property_booking_data, $pid, $year, $month);
                    }
                }

                $start_month->addMonth();
            }//end while
        }//end foreach

        echo 'Data added successfully.';

    }//end addPropertyDataMonthWise()


    public function setupElasticSearch()
    {
        $index              = 'locations';
        $autocomplete_field = 'location_suggest';

        $hosts = [
            [
                'host' => config('gh.es_location_autocomplete.server.host'),
                'port'   => config('gh.es_location_autocomplete.server.port'),
                'scheme' => config('gh.es_location_autocomplete.server.scheme'),
            ],
        ];

        try {
            $client = EsClientBuilder::create()->setHosts($hosts)->setRetries(1)->build();

            // Delete existing index if exists
            $params = ['index' => $index];
            $index_exists=$client->indices()->exists($params);
            if($index_exists){
                $client->indices()->delete($params);
            }

            // Create the index
            $params   = [
                'index' => $index,
                'body'  => [
                    'mappings' => [
                        'properties' => [
                            'area'              => ['type' => 'text'],
                            'city'              => ['type' => 'text'],
                            'state'             => ['type' => 'text'],
                            'country'           => ['type' => 'text'],
                            'lat'               => ['type' => 'text'],
                            'lng'               => ['type' => 'text'],
                            $autocomplete_field => ['type' => 'completion'],
                        ],
                    ],
                ],
            ];
            $response = $client->indices()->create($params);

            \DB::select('SET SESSION max_heap_table_size=536870912');
            \DB::select('SET SESSION tmp_table_size=536870912');

            $country = \DB::select("select '' as area, '' as city, ''as state, if(country='IN','India',country) as country, '' as lat, '' as lng from properties where country='IN' group by country");

            $states = \DB::select("select '' as area, '' as city, state, if(country='IN','India',country)as country, '' as lat, '' as lng from properties where country='IN' and state!='Uttaranchal' and state!='' group by state");

            $cities = \DB::select("select  '' as area, city, state, if(country='IN','India',country) as country, '' as lat, '' as lng from properties where country='IN' and city!='' and state!=''  group by city,state");

            $areas = \DB::select("select area, city, state, if(country='IN','India',country) as country, '' as lat, '' as lng  from properties where country='IN' and area!='' and city!='' and state!='' group by area, city, state");

            echo 'Total countries found for indexing is '.count($country)."\n";
            echo 'Total states found for indexing are '.count($states)."\n";
            echo 'Total cities found for indexing are '.count($cities)."\n";
            echo 'Total areas found for indexing are '.count($areas)."\n";

            $locations = array_merge($country, $states, $cities, $areas);
            echo 'Total locations for indexing are '.count($locations)."\n";
            echo 'Starting Indexing in Elasticsearch '."\n";

            $params = [];

            foreach ($locations as $location) {
                $area    = $location->area;
                $city    = $location->city;
                $state   = $location->state;
                $country = $location->country;
                $lat     = $location->lat;
                $lng     = $location->lng;

                $input = '';
                if ($area != '') {
                    $input .= $area.', ';
                }

                if ($city != '') {
                    $input .= $city.', ';
                }

                if ($state != '') {
                    $input .= $state.', ';
                }

                if ($country != '') {
                    $input .= $country;
                }

                $params['body'][] = [
                    'index' => ['_index' => $index],
                ];

                $params['body'][] = [
                    'area'              => $area,
                    'city'              => $city,
                    'state'             => $state,
                    'country'           => $country,
                    'lat'               => $lat,
                    'lng'               => $lng,
                    $autocomplete_field => [
                        'input' => [$input],
                    ],

                ];
            }//end foreach

            $responses = $client->bulk($params);
            echo 'Indexing Complete in Elasticsearch ';
        } catch (\Exception $e) {
            echo 'There is an exception and it is :'.$e->getMessage();
            \Log::Error('Error in fetching from elasticsearch and error is '.$e->getMessage().' and query was query');
        }//end try

    }//end setupElasticSearch()

}//end class
