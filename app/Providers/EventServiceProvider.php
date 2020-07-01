<?php
/**
 * Register events and their listeners in this file.
 */

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider.
 */
class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // User Event and Listeners.
        'App\Events\UserRegistered'              => ['App\Listeners\UserRegisteredListener'],
        // Wallet updatation event.
        'App\Events\WalletUpdation'              => ['App\Listeners\WalletUpdationListener'],

        // Booking Requests Event and Listeners.
        'App\Events\CreateBookingRequest'        => ['App\Listeners\CreateBookingRequestListener'],
        'App\Events\CancelBookingRequest'        => ['App\Listeners\CancelBookingRequestListener'],
        'App\Events\StatusChangedBookingRequest' => ['App\Listeners\StatusChangedBookingRequestListener'],
        'App\Events\CreateBooking'               => ['App\Listeners\CreateBookingListener'],
        'App\Events\UserEmailVerified'           => ['App\Listeners\UserEmailVerifiedListner'],
        // Properly Contact us event and listners.
        'App\Events\ContactUs'                   => ['App\Listeners\ContactUsListener'],
        'App\Events\PropertyListing'             => ['App\Listeners\PropertyListingListener'],
        'App\Events\SendBookingPaymentLink'      => ['App\Listeners\SendBookingPaymentLinkListener'],
        'App\Events\UserResetPassword'           => ['App\Listeners\UserResetPasswordListner'],
        'App\Events\UserLoginUrlSms'             => ['App\Listeners\UserLoginUrlSmsListner'],

    ];
}//end class
