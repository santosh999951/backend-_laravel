<?php
    /**
        |--------------------------------------------------------------------------
        | Default Filesystem Disk
        |--------------------------------------------------------------------------
        |
        | Here you may specify the default filesystem disk that should be used
        | by the framework. A "local" driver, as well as a variety of cloud
        | based drivers are available for your choosing. Just store away!
        |
        | Supported: "local", "s3", "rackspace"
        |
     */

return [

    'default' => 'local',

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ],
        'sms_log' => [
            'driver' => 'local',
            'root'   => storage_path().'/app/sms_logs',
        ],
        'sms_call' => [
            'driver' => 'local',
            'root'   => storage_path().'/app/gh-lex',
        ],
    ]
];
