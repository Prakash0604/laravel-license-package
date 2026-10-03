<?php
return [
    'server'=>rtrim((string)env('LICENSE_SERVER'),'/'),
    'key'=>env('LICENSE_KEY'),
    'domain'=>env('LICENSE_DOMAIN'),
    'environment'=>env('LICENSE_ENVIRONMENT',env('APP_ENV','production')),
    'cache_store'=>env('LICENSE_CACHE_STORE','file'),
    'cache_key'=>env('LICENSE_CACHE_KEY','yourcompany.license.v2'),
    'grace_period_seconds'=>(int)env('LICENSE_GRACE_PERIOD_SECONDS',259200),
    'public_key'=>env('LICENSE_SIGNING_PUBLIC_KEY'),
    'fail_closed'=>(bool)env('LICENSE_FAIL_CLOSED',true),
    'max_clock_skew_seconds'=>(int)env('LICENSE_MAX_CLOCK_SKEW_SECONDS',300),
    'middleware'=>['enabled'=>(bool)env('LICENSE_MIDDLEWARE_ENABLED',true)],
    'paths'=>['excluded'=>['up','health','api/v1/license/*']],
];
