<?php
/**
 * config file.
 */

return [
    'dsn'                      => 'https://759a0c4945904a92959c209aae39ac73@sentry.io/1251081',

    // capture release as git sha
    // 'release' => trim(exec('git log --pretty="%h" -n1 HEAD')),
    // Capture bindings on SQL queries
    'breadcrumbs.sql_bindings' => true,
    'release'                  => 'api',
];
