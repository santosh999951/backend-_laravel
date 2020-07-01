<?php
/**
 * Model containing data regarding traffic data
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * Class TrafficData
 */
class TrafficData extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'traffic_data';


    /**
     * Create a new traffic data request.
     *
     * @param array $data Data required for traffic request.
     *
     * @return boolean True/false
     */
    public static function createNew(array $data)
    {
        // Get traffic data of device.
        $traffic_source_data = DB::table('traffic_source')->where('device_id', '=', $data['device_unique_id'])->first();

        // Create new request.
        $request           = new self;
        $request->event    = $data['event'];
        $request->actor_id = $data['actor_id'];
        $request->referer  = $data['referrer'];
        if (empty($traffic_source_data) === false) {
            $request->source   = $traffic_source_data->source;
            $request->campaign = $traffic_source_data->campaign;
            $request->medium   = $traffic_source_data->medium;
        }

        $request->save();

        return true;

    }//end createNew()


}//end class
