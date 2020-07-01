<?php
/**
 * TrafficSource Model containing all functions related to traffic source
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
/**
 * Class TrafficSource
 */
class TrafficSource extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'traffic_source';


    /**
     * Save Traffic Data
     *
     * @param array $data Array of params.
     *
     * @return boolean
     */
    public static function saveTrafficData(array $data)
    {
        $traffic_source                 = new self;
        $traffic_source->device_id      = $data['device_id'];
        $traffic_source->source         = $data['source'];
        $traffic_source->medium         = $data['medium'];
        $traffic_source->campaign       = $data['campaign'];
        $traffic_source->all_parameters = $data['all_parameters'];

        return $traffic_source->save();

    }//end saveTrafficData()


}//end class
