<?php
/**
 * Listener for the Property Litsting Add/Update event.
 */

namespace App\Listeners;

use App\Events\PropertyListing;

use App\Libraries\v1_6\PropertyService;

/**
 * Class PropertyListingListener for handling Request Create event.
 */
class PropertyListingListener extends Listener
{

    /**
     * Property service.
     *
     * @var PropertyService $property_service Property Service.
     */
    protected $property_service;


    /**
     * Initialize the object.
     *
     * @param PropertyService $property_service Property Service.
     */
    public function __construct(PropertyService $property_service)
    {
        $this->property_service = $property_service;

    }//end __construct()


    /**
     * Handle the event.
     *
     * @param PropertyListing $event Event.
     *
     * @return void
     */
    public function handle(PropertyListing $event)
    {
        if ($event->is_added === true && $event->is_admin === false) {
            $this->property_service->sendPropertyLisingMailToHost($event->property, $event->host_email);
            $this->property_service->sendPropertyLisingMailToAdmin($event->property);
        } else if ($event->is_admin === false) {
            $this->property_service->sendPropertyModifyMailToAdmin($event->property, $event->updated_data);
        }

    }//end handle()


}//end class
