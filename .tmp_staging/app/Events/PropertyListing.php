<?php
/**
 * A simple Property Listing Create/Update event class.
 */

namespace App\Events;

use App\Models\{Property};


/**
 * Class PropertyListing. An event class which is fired when Property Listed/Updated.
 */
class PropertyListing extends Event
{

    /**
     * Property Object
     *
     * @var Property $property
     */
    public $property;

    /**
     * Host Email
     *
     * @var Strinf $host_email.
     */
    public $host_email;

    /**
     * Property Added / Update By Admin status
     *
     * @var boolean $is_admin.
     */
    public $is_admin;

    /**
     * Property Added / Update Status
     *
     * @var boolean $is_added.
     */
    public $is_added;

    /**
     * Property Updated Data with Old and new data
     *
     * @var array $updated_data.
     */
    public $updated_data;


    /**
     * Create/Update Property instance.
     *
     * @param Property $property     Property Object.
     * @param string   $host_email   Host Emails.
     * @param boolean  $is_added     Property Added or Modified Status.
     * @param array    $updated_data Property Updated Data with keys (key, old, new).
     * @param boolean  $is_admin     Is admin status.
     *
     * @return void
     */
    public function __construct(Property $property, string $host_email, bool $is_added=true, array $updated_data=[], bool $is_admin=false)
    {
        $this->property     = $property;
        $this->host_email   = $host_email;
        $this->is_added     = $is_added;
        $this->updated_data = $updated_data;
        $this->is_admin     = $is_admin;

    }//end __construct()


}//end class
