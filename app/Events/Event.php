<?php
// phpcs:ignoreFile
namespace App\Events;

use Illuminate\Queue\SerializesModels;

abstract class Event
{
    use SerializesModels;
}//end class
