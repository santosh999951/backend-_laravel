<?php
/**
 * Containn logging functionality
 */

return [

  /*
      |--------------------------------------------------------------------------
      | Default Log Channel
      |--------------------------------------------------------------------------
      |
      | This option defines the default log channel that gets used when writing
      | messages to the logs. The name specified in this option should match
      | one of the channels defined in the "channels" configuration array.
      |
  */

    'default'  => env('LOG_CHANNEL', 'daily'),

    /*
        |--------------------------------------------------------------------------
        | Log Channels
        |--------------------------------------------------------------------------
        |
        | Here you may configure the log channels for your application. Out of
        | the box, Laravel uses the Monolog PHP logging library. This gives
        | you a variety of powerful log handlers / formatters to utilize.
        |
        | Available Drivers: "single", "daily", "slack", "syslog",
        |                    "errorlog", "monolog",
        |                    "custom", "stack"
        |
    */

    'channels' => [
        'info'  => [
            'driver' => 'single',
            'path'   => storage_path('logs/infolog.log'),
            'level'  => 'debug',
        ],
        'error' => [
            'driver' => 'single',
            'path'   => storage_path('logs/errorlog.log'),
            'level'  => 'debug',
        ],
        'daily' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/lumen.log'),
            'level'  => 'debug',
            'days'   => 7,
        ],
        'query' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/query.log'),
            'level'  => 'debug',
            'days'   => 7,
        ],

    ],

];
