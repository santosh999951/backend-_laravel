<?php
/**
 * File defining Api routes.
 **/

// Device.
// Payment.
$router->get('booking/payment/{request_hash_id}', 'BookingController@getPaymentData');
$router->get('booking/partialpayment/{request_hash_id}', 'BookingController@getPaymentData');


// After payment success or fail.
$router->post('payment/paysuccess', 'BookingController@anyPaymentSuccess');
$router->get('payment/paysuccess', 'BookingController@anyPaymentSuccess');
$router->post('payment/payfail', 'BookingController@postPaymentFail');
$router->get('payment/payfail', 'BookingController@postPaymentFail');


$router->post('device', 'DeviceController@postRegister');
$router->get('booking/test', 'BookingController@getTestPage');
$router->post('generate/response/mapping', 'StaticController@postGenerateApiResponseMappings');
$router->post('generate/response/model', 'StaticController@postGenerateApiResponseModels');
$router->post('generate/response/existing-mappings', 'StaticController@postGenerateApiResponseMappingsForExistingMappings');

// Status code.
$router->get('statuscode/{status_code}', 'StaticController@getStatuscode');

// Location suggestion code.
$router->get('location/suggest', 'StaticController@getLocationSuggestions');






// Allow cors request.
$router->options(
    '{p1}',
    function ($p1) {
    }
);
$router->options(
    '{p1}/{p2}',
    function ($p1) {
    }
);
$router->options(
    '{p1}/{p2}/{p3}',
    function ($p1) {
    }
);
$router->options(
    '{p1}/{p2}/{p3}/{p4}',
    function ($p1) {
    }
);


// Device unique id in headers.
$router->group(
    ['middleware' => 'device'],
    function () use ($router) {
        // Common-data.
        $router->get('common-data', 'StaticController@getCommonData');

        // Send otp for login.
        $router->post('user/login/mobile', 'UserController@postMobileLogin');
        // Verify otp for login.
        $router->put('user/login/mobile', 'UserController@putMobileLogin');
        // Forgot password.
        $router->post('user/password/reset', 'UserController@postResetPassword');
        $router->put('user/password/reset', 'UserController@putUpdatePasswordViaEmail');
        $router->put('user/password', 'UserController@putUpdatePasswordViaOtp');
        $router->get('user/password/{token_hash}', 'UserController@getCheckIfValidResetPasswordTokenExists');
        $router->put('user/verify/email', 'UserController@putVerifyEmail');

        // Prive Reset password.
        $router->post('prive/password/reset', 'PriveController@postResetPassword');

        // Device.
        $router->post('device/trafficsource', 'DeviceController@postTrafficSource');

        // User.
        $router->post('user', 'UserController@postRegister');
        $router->get('user/getrefererdetails', 'UserController@getRefererDetails');

        $router->get('search/spotlight', 'SearchController@getSpotlightDetails');

        $router->post('user/loginoldappuser', 'UserController@postLoginOldAppUser');

        // Prive manager login via.
        $router->post('prive/loginvia', 'PriveController@postLoginVia');
        // Prive Owner Login.
        $router->post('prive/login', 'PriveController@postLogin');
        // Verify otp for login.
        $router->put('prive/login/mobile', 'PriveController@putMobileLogin');

        $router->post('prive/register', 'PriveController@postRegister');
        $router->get('prive/listings', 'PriveController@getTravellerPriveProperties');

        // Login via token.
        $router->post('user/loginviatoken', 'UserController@postLoginViaToken');

        // Dial and country codes(country details).
        $router->get('countrydetails', 'StaticController@getCountryDetails');

        // Semi auth middleware.
        $router->group(
            ['middleware' => 'auth:api,semi'],
            function () use ($router) {
                $router->get('home', 'HomeController@getIndex');
                $router->get('home/collections/{collection_hash_id}', 'HomeController@getParticularCollection');
                $router->get('home/collections', 'HomeController@getCollections');
                $router->get('search', 'SearchController@getIndex');
                $router->get('search/popular', 'SearchController@getPopularSearch');
                // User profile.
                $router->get('user/properties', 'UserController@getUserProperties');
                $router->post('user/login', 'UserController@postLogin');
                // Property api.
                $router->get('property/{property_hash_id}', 'PropertyController@getProperty');
                $router->get('property/reviews/{property_hash_id}', 'PropertyController@getPropertyReviews');
                $router->get('property/price/calendar/{property_hash_id}', 'PropertyController@getPropertyPriceCalendar');
                // Dial codes.
                $router->get('dialcodes', 'StaticController@getDialCodes');
                // Country codes.
                $router->get('countrycodes', 'StaticController@getCountryCodes');
                // Currency codes.
                $router->get('currencycodes', 'UserController@getCurrencyCodes');

                // User.
                $router->get('user', 'UserController@getUser');

                // Prepayment api.
                $router->get('prepayment/{property_hash_id}', 'PrepaymentController@getPrepaymentDetails');
                // Similar Properties.
                $router->get('property/similar/{property_hash_id}', 'PropertyController@getSimilarProperties');

                 // App Feedback.
                $router->post('user/appfeedback', 'UserController@postAppFeedback');
            }
        );

        // Auth middleware.
        $router->group(
            ['middleware' => 'auth:api'],
            function () use ($router) {
                // Rm Host listing.
                $router->get('host/rmhostlisting', 'HostController@getRmHostListing');
                $router->get('host/rmashostlogin/{host_hash_id}', 'HostController@getRmAsHostLogin');

                // User logout.
                $router->post('user/logout', 'UserController@postLogout');
                // Recently Viewed.
                $router->get('search/recent', 'SearchController@getRecentlyViewedProperties');
                // Wishlist.
                $router->get('user/wishlist', 'UserController@getWishlist');
                $router->post('user/wishlist/{property_hash_id}', 'UserController@postAddToWishlist');
                $router->delete('user/wishlist/{property_hash_id}', 'UserController@deleteFromWishlist');
                // Mobile verify.
                $router->post('user/verify/mobile', 'UserController@postSendOtp');
                $router->put('user/verify/mobile', 'UserController@putVerifyOtp');
                // Email verify.
                $router->post('user/verify/email', 'UserController@postSendVerificationEmail');
                // User.
                $router->put('user', 'UserController@putUpdateUser');
                $router->put('user/delete', 'UserController@putDeleteUser');
                $router->post('user/picture', 'UserController@postUpdateUserPicture');
                $router->put('user/currency', 'UserController@putUpdateUserCurrency');
                $router->get('user/wallet', 'UserController@getWallet');
                $router->get('user/refertoearn', 'UserController@getReferToEarnDetails');
                $router->put('user/lastactive/update', 'UserController@putUpdateLastactive');

                // Trips.
                $router->get('booking/trip', 'BookingController@getTrips');
                $router->get('booking/trip/{request_hash_id}', 'BookingController@getTripDetails');

                // Property Review and Rating.
                $router->get('trip/review', 'PropertyController@getRatingReviewDetails');

                $router->post('property/rating', 'PropertyController@postAddRating');
                $router->post('property/review', 'PropertyController@postAddReview');
                $router->post('property/review/image', 'PropertyController@postReviewImage');

                // Requests.
                $router->get('/booking/request', 'BookingController@getBookingRequests');
                $router->post('/booking/request', 'BookingController@postBookingRequest');
                $router->get('/booking/request/{request_hash_id}', 'BookingController@getBookingRequest');

                // Confirm Traveller arrival.
                $router->put('/booking/confirm-arrival', 'BookingController@putTravellerConfirmationOnArrival');

                $router->get('/booking/share', 'BookingController@getFbShareProperties');

                // Update Password.
                $router->put('user/password/update', 'UserController@putUpdatePassword');

                // Prepayment for request.
                $router->get('prepayment/request/{request_hash_id}', 'PrepaymentController@getPrepaymentDetailsForRequest');
                $router->put('/booking/request/{request_hash_id}', 'BookingController@putBookingRequest');

                $router->post('/booking/request/cancel', 'BookingController@postCancelBookingRequest');
                $router->post('/booking/request/resend', 'BookingController@postResendBookingRequest');

                $router->post('/booking/request/emailinvoice', 'BookingController@postEmailInvoiceForBookingRequest');

                // Anonymous user oauth credentials.
                $router->get('user/anonymous/{user_id}', 'UserController@getUserOAuthToken');

                // Offers.
                $router->post('admin/uploadPromotionalimage', 'Admin\OfferBannerController@postPromotionalImage');
                $router->put('admin/homebanner', 'Admin\OfferBannerController@putHomeBanner');
                $router->get('admin/offers', 'Admin\OfferBannerController@getOffers');
                $router->post('admin/offers', 'Admin\OfferBannerController@postOffers');
                $router->put('admin/offers', 'Admin\OfferBannerController@putOffers');
                $router->delete('admin/offers', 'Admin\OfferBannerController@deleteOffer');

                // Home Banners.
                $router->delete('admin/homebanner', 'Admin\OfferBannerController@deleteHomeBanner');
                $router->get('admin/homebanner', 'Admin\OfferBannerController@getHomeBanner');
                $router->post('admin/homebanner', 'Admin\OfferBannerController@postHomeBanner');

                // Promo Banners.
                $router->put('admin/promobanner', 'Admin\OfferBannerController@putPromoBanner');
                $router->delete('admin/promobanner', 'Admin\OfferBannerController@deletePromoBanner');
                $router->get('admin/promobanner', 'Admin\OfferBannerController@getPromoBanner');
                $router->post('admin/promobanner', 'Admin\OfferBannerController@postPromoBanner');
                // Host Lead.
                $router->post('host/lead', 'HostController@postLead');

                // Property Listing Api.
                $router->post('property', 'PropertyController@postProperty');

                $router->get('notifications', 'StaticController@getNotificationData');

                // Payment Options. (Pay via Credit Card, Debit Card).
                $router->get('booking/payment/options/{request_hash_id}', 'BookingController@getSeamlessPaymentOptions');
                $router->get('booking/payment/payload/{request_hash_id}', 'BookingController@getSeamlessPayment');

                // Offline Discovery App api.
                $router->post('offlinediscovery/createlead', 'OfflineDiscoveryController@postCreateLead');
                $router->post('offlinediscovery/leaduploadimage', 'OfflineDiscoveryController@postLeadUploadImage');
                $router->get('offlinediscovery/search', 'OfflineDiscoveryController@getSearch');
                $router->get('offlinediscovery/leadformlist', 'OfflineDiscoveryController@getLeadFormList');

                $router->group(
                    ['middleware' => 'host'],
                    function () use ($router) {
                        // Host api.
                        $router->get('host/home', 'HostController@getIndex');
                        $router->get('host/booking', 'HostController@getBookingList');
                        $router->get('host/booking/trip/{request_hash_id}', 'HostController@getTripDetails');
                        $router->get('host/booking/request/{request_hash_id}', 'HostController@getRequestDetails');
                        $router->put('host/booking/status', 'HostController@putRequestStatus');
                        $router->post('host/booking/confirm', 'HostController@postBookingConfirmation');
                        $router->get('host/property', 'HostController@getProperties');
                        $router->get('host/property/review', 'HostController@getPropertyReviews');
                        $router->put('host/property/review', 'HostController@putReviewReply');
                        $router->get('host/property/{property_hash_id}', 'HostController@getProperty');
                        $router->get('host/property/calendar/{property_hash_id}', 'HostController@getPropertyPriceCalendar');
                        $router->put('host/property/calendar', 'HostController@putPropertyCalendar');
                        $router->put('host/property/status', 'HostController@putPropertyStatus');
                        $router->get('host/payouts', 'HostController@getPayoutHistory');
                        $router->get('host/payment/preferences', 'HostController@getPaymentPreferences');
                        $router->put('host/payment/preferences', 'HostController@putPaymentPreferences');
                        $router->post('host/payment/preferences', 'HostController@postPaymentPreferences');
                        $router->post('host/property/clone', 'HostController@postPropertyClone');
                        $router->post('host/smartdiscounts', 'HostController@postSmartDiscounts');
                        $router->delete('host/property/remove', 'HostController@deleteProperty');

                        // Property Listing Update.
                        $router->put('property', 'HostController@putProperty');
                        $router->get('host/listing/property/{property_hash_id}', 'HostController@getListingProperty');

                        // Confirm Travller arrival.
                        $router->put('host/booking/confirm-guest-arrival', 'HostController@putConfirmTravellerArrival');
                    }
                );

                $router->get('prive', 'PriveController@getUser');

                $router->group(
                    ['middleware' => 'prive'],
                    function () use ($router) {
                        // Prive Api.
                        $router->get('prive/bookings', 'PriveController@getBookings');
                        $router->get('prive/invoice', 'PriveController@getInvoice');
                        $router->get('prive/property', 'PriveController@getProperties');
                        $router->put('prive', 'PriveController@putUser');
                        $router->get('prive/home', 'PriveController@getIndex');
                        $router->get('prive/homegraph', 'PriveController@getHomeGraph');
                        $router->get('prive/booking/{request_hash_id}', 'PriveController@getBookingDetails');
                        $router->post('prive/booking', 'PriveController@postBooking');
                        $router->post('prive/contactus', 'PriveController@postSupportEmail');
                        $router->post('prive/expense', 'PriveController@postProperlyExpense');

                        $router->put('prive/expense/{properly_expense_hash_id}', 'PriveController@putProperlyExpense');
                        $router->delete('prive/expense/{properly_expense_hash_id}', 'PriveController@deleteProperlyExpense');

                        $router->get('prive/expense/index', 'PriveController@getProperlyExpenseIndex');
                        $router->get('prive/expense', 'PriveController@getProperlyExpense');
                        $router->get('prive/expense/accordance', 'PriveController@getProperlyExpenseAccordance');
                    }
                );

                // Prive Manager Api.
                $router->group(
                    ['middleware' => ['permission:booking-view#operations']],
                    function () use ($router) {
                        $router->get('prive/manager/bookings', 'PriveController@getManagerBookings');
                        $router->get('prive/manager/booking/{request_hash_id}', 'PriveController@getManagerBookingDetail');
                        $router->get('prive/manager/property', 'PriveController@getManagersProperties');
                    }
                );

                $router->group(
                    ['middleware' => ['permission:checkedin-time-edit#operations|notes-edit#operations']],
                    function () use ($router) {
                        $router->post('prive/manager/booking/operation', 'PriveController@postManagerOperation');
                    }
                );

                $router->group(
                    ['middleware' => ['permission:checked-status-edit#operations']],
                    function () use ($router) {
                        $router->post('prive/manager/booking/status', 'PriveController@postBookingCheckedInStatus');
                    }
                );

                $router->group(
                    ['middleware' => ['permission:collect-payment#operations']],
                    function () use ($router) {
                        $router->post('prive/manager/booking/cash-collect', 'PriveController@postManagerBookingCashCollect');
                        $router->post('prive/manager/booking/send-payment-link', 'PriveController@postManagerSendPaymentLink');
                    }
                );

                $router->group(
                    ['middleware' => ['permission:call-traveller#operations']],
                    function () use ($router) {
                        $router->post('prive/manager/booking/contact-traveller', 'PriveController@postManagerContactTraveller');
                    }
                );

                $router->group(
                    ['middleware' => ['permission:task-view#operations']],
                    function () use ($router) {
                        $router->get('properly/task', 'PriveController@getProperlytask');
                        $router->get('properly/booking/task/{request_hash_id}', 'PriveController@getProperlyScheduledTask');
                        $router->get('properly/task/{task_hash_id}', 'PriveController@getProperlyTaskDetail');
                    }
                );

                $router->group(
                    ['middleware' => ['permission:task-add#operations']],
                    function () use ($router) {
                        $router->post('properly/task', 'PriveController@postProperlytask');
                    }
                );

                $router->group(
                    ['middleware' => ['permission:task-edit#operations']],
                    function () use ($router) {
                        $router->put('properly/task', 'PriveController@putProperlyTask');
                    }
                );

                $router->group(
                    ['middleware' => ['permission:task-status-edit#operations']],
                    function () use ($router) {
                        $router->put('properly/task/status', 'PriveController@putProperlyTaskStatus');
                    }
                );

                $router->group(
                    ['middleware' => ['permission:member-view#operations']],
                    function () use ($router) {
                        $router->get('properly/member/filter', 'PriveController@getFilterMember');
                    }
                );

                $router->group(
                    ['middleware' => ['permission:member-add#operations']],
                    function () use ($router) {
                        $router->post('properly/member', 'PriveController@postCreateMember');
                    }
                );

                $router->group(
                    ['middleware' => ['permission:member-edit#operations']],
                    function () use ($router) {
                        $router->put('properly/member/suspend', 'PriveController@putSuspendMember');
                        $router->get('properly/loginaccess', 'PriveController@getResendLoginAccess');
                    }
                );

                $router->group(
                    ['middleware' => ['permission:member-delete#operations']],
                    function () use ($router) {
                        $router->delete('properly/member', 'PriveController@deleteMember');
                    }
                );
            }
        );

        // Offline discovery app api.
        $router->post('offlinediscovery/login', 'OfflineDiscoveryController@postLogin');
    }
);
