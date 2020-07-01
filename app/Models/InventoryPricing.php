<?php
/**
 * InventoryPricing contain all functions related to inventory princing.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use App\Libraries\Helper;

use App\Jobs\SyncAirbnbProperties;

/**
 * Class InventoryPricing
 */
class InventoryPricing extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'inventory_pricing';


    /**
     * Helper function to create scope with status equal one
     *
     * @param integer $pid        Property id.
     * @param string  $start_date Start date.
     * @param string  $end_date   End date.
     *
     * @return array Inventory Pricing data.
     */
    public static function getPropertyInventoryDetails(int $pid, string $start_date, string $end_date)
    {
        $inventory_prices = self::where('pid', $pid)->where('date', '>=', $start_date)->where('date', '<', $end_date)->orderBy('date', 'asc')->get()->keyBy('date');

        if (empty($inventory_prices) === true) {
            return [];
        }

        return $inventory_prices->toArray();

    }//end getPropertyInventoryDetails()


    /**
     * Inventory data query for start date, end date and location.
     *
     * @param string $start_date Start date.
     * @param string $end_date   End date.
     * @param string $country    Country.
     * @param string $state      State.
     *
     * @return string
     */
    public static function inventoryDataQuery(string $start_date, string $end_date, string $country, string $state)
    {
        //phpcs:disable Generic.Files.LineLength.TooLong
        $query = DB::Raw(
            "(select 
                p.custom_discount,
                ip.discount,
                ip.service_fee,
                ip.pid,
                ip.date,
                if(min(is_available) > 0 ,min(available_units * is_available),0 ) as min_units, 
                if(min(is_available) > 0 ,min(booked_units * is_available),0 ) as booked_units, 
                min(ip.instant_booking) as is_instant_booking,
                avg(((ip.price/(100-ip.service_fee))*100)*(100-ip.discount)/100) as avg_per_night_price, 
                avg(ip.extra_guest_cost) AS avg_extra_guest_cost,
                count(*) AS custom_price_days, 
                sum(ip.price) AS toal_custom_price, 
                sum(ip.extra_guest_cost) AS total_custom_avg_extra_guest_cost,
                if(sum(booked_units), sum(booked_units), 0) as total_booked_units,
                sum(((1 - ip.discount/100)*( (if(ip.smart_discount,( 1 -(ip.smart_discount/100)),1)) *((ceil(if(2/p.accomodation > 0/p.bedrooms, 2/p.accomodation, 0/p.bedrooms))) * ip.price + (if(pp.additional_guest_count, if(2 - pp.additional_guest_count * (ceil(if(2/p.accomodation > 0/p.bedrooms, 2/p.accomodation, 0/p.bedrooms))) > 0, 2 - pp.additional_guest_count * (ceil(if(2/p.accomodation > 0/p.bedrooms, 2/p.accomodation, 0/p.bedrooms))), 0),0)) * if(ip.extra_guest_cost,ip.extra_guest_cost,0)) ))/(1 - (if(ip.service_fee,ip.service_fee,p.service_fee))/100)) as custom_rate,
                max(discount) as host_discount,
                max(smart_discount) as smart_discount
            from inventory_pricing as ip
            left join properties as p on p.id = ip.pid
            left join property_pricing as pp on p.id = pp.pid
            where ip.date >= '".$start_date."'
            and ip.date < '".$end_date."'
            and p.country = '".$country."'
            and p.state = '".$state."'
            group by ip.pid) as ip"
        );
        //phpcs:enable
        return $query;

    }//end inventoryDataQuery()


    /**
     * Descrease inventory of booked dates
     *
     * @param integer $pid       Property Id.
     * @param string  $from_date From date.
     * @param string  $to_date   To date.
     * @param integer $units     Property Units to decrease.
     *
     * @return boolean true/false.
     */
    public static function decreaseInventory(int $pid, string $from_date, string $to_date, int $units)
    {
        $property   = Property::find($pid);
        $first_date = strtotime($from_date);
        $end_date   = strtotime($to_date);

        while ($first_date < $end_date) {
            $cur_date   = date('Y-m-d', $first_date);
            $first_date = ($first_date + 60 * 60 * 24);

            $pricing = self::where('pid', '=', $pid)->where('date', '=', $cur_date)->first();
            if (empty($pricing) === false) {
                $pricing->available_units = ($pricing->available_units >= $units) ? ($pricing->available_units - $units) : 0;
                $pricing->booked_units    = ($pricing->booked_units + $units);
            } else {
                $pricing                   = new InventoryPricing;
                $pricing->pid              = $pid;
                $pricing->date             = $cur_date;
                $pricing->price            = $property->property_price->per_night_price;
                $pricing->extra_guest_cost = $property->property_price->additional_guest_fee;
                $pricing->available_units  = ($property->units >= $units) ? ($property->units - $units) : 0;
                $pricing->booked_units     = $units;
                $pricing->is_available     = 1;
                $pricing->instant_booking  = $property->instant_book;
                $pricing->discount         = 0;
                $pricing->discount_type    = 0;
                $pricing->discount_days    = '';
            }

            $pricing->save();
        }//end while

        Property::updatePropertyLastUpdate($pid);

        $airbnb_channel_manager_info = ChannelManagerProperties::getAirbnbDataByProperty($pid);

        foreach ($airbnb_channel_manager_info as $values) {
            $job = new SyncAirbnbProperties($values);
            dispatch($job)->onQueue(API_BNB_QUEUE);
        }

        return true;

    }//end decreaseInventory()


    /**
     * Increase inventory while cancelling booked dates
     *
     * @param integer $pid       Property Id.
     * @param string  $from_date From date.
     * @param string  $to_date   To date.
     * @param integer $units     Property Units to increase.
     *
     * @return boolean true/false.
     */
    public static function increaseInventory(int $pid, string $from_date, string $to_date, int $units)
    {
        $property   = Property::find($pid);
        $first_date = strtotime($from_date);
        $end_date   = strtotime($to_date);

        while ($first_date < $end_date) {
            $cur_date   = date('Y-m-d', $first_date);
            $first_date = ($first_date + 60 * 60 * 24);
            $pricing    = self::where('pid', '=', $pid)->where('date', '=', $cur_date)->first();
            if (empty($pricing) === false) {
                $pricing->available_units = (($pricing->available_units + $units) <= $property->units) ? ($pricing->available_units + $units) : $property->units;
                $pricing->booked_units    = (($pricing->booked_units - $units) < 0) ? 0 : ($pricing->booked_units - $units);
                $pricing->save();
            }
        }

        Property::updatePropertyLastUpdate($pid);

        $airbnb_channel_manager_info = ChannelManagerProperties::getAirbnbDataByProperty($pid);

        foreach ($airbnb_channel_manager_info as $values) {
            $job = new SyncAirbnbProperties($values);
            dispatch($job)->onQueue(API_BNB_QUEUE);
        }

        return true;

    }//end increaseInventory()


    /**
     * Helper function to create/update Property Inventory
     *
     * @param array $param Create/Update Inventory Parameters.
     *
     * @return array Inventory Pricing data.
     */
    public static function changePropertyInventory(array $param)
    {
        $pid                = $param['property_id'];
        $available_units    = $param['available_units'];
        $service_fee        = $param['service_fee'];
        $per_night_price    = $param['per_night_price'];
        $extra_guest_cost   = $param['extra_guest_cost'];
        $markup_service_fee = $param['markup_service_fee'];
        $inventory_date     = $param['date'];
        $is_available       = $param['is_available'];
        $instant_booking    = $param['instant_book'];
        $gh_commission      = $param['gh_commission'];
        $x_plus_5           = $param['x_plus_5'];
        $discount_type      = $param['discount_type'];
        $discount_days      = $param['discount_days'];
        $discount           = $param['discount'];
        $admin_id           = $param['admin_id'];

        $inventory = self::where('pid', '=', $pid)->where('date', '=', $inventory_date)->first();

        if (empty($inventory) === true) {
            $inventory = new InventoryPricing;
        }

        $inventory->pid                   = $pid;
        $inventory->available_units       = $available_units;
        $inventory->service_fee           = $service_fee;
        $inventory->price                 = $per_night_price;
        $inventory->extra_guest_cost      = $extra_guest_cost;
        $inventory->markup_service_fee    = $markup_service_fee;
        $inventory->last_updated_by_admin = $admin_id;
        $inventory->last_manual_change    = Carbon::now('GMT');
        $inventory->date                  = $inventory_date;
        $inventory->is_available          = $is_available;
        $inventory->instant_booking       = $instant_booking;
        $inventory->gh_commission         = $gh_commission;
        $inventory->x_plus_5              = $x_plus_5;
        $inventory->discount_type         = $discount_type;
        $inventory->discount_days         = $discount_days;
        $inventory->discount              = $discount;
        $inventory->save();

        return $inventory;

    }//end changePropertyInventory()


}//end class
