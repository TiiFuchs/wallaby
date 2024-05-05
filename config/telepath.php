<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Telegram Bot
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the bots below you wish to use as
    | your default bot. If you only need one bot in your application
    | you don't need to worry about it.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Telegram Bots
    |--------------------------------------------------------------------------
    |
    | If you need more than one bot in your application, you can define
    | them here. Else it is sufficient to use the environment vars
    | that are preconfigured for the default bot.
    |
    */

    'bots'    => [

        'main' => [
            'api_token' => env('TELEGRAM_API_TOKEN', ''),

            'directory' => app_path('Telepath'),
        ],

    ],

    'webhook' => [

        /*
        |--------------------------------------------------------------------------
        | Webhook Secret Token
        |--------------------------------------------------------------------------
        |
        | Here you may specify the secret token that is used to verify
        | the webhook url. This is used to prevent unauthorized
        | access to your webhook url.
        |
        */

        'secret' => env('TELEGRAM_WEBHOOK_SECRET'),

        /*
        |--------------------------------------------------------------------------
        | Allow Local Subnets
        |--------------------------------------------------------------------------
        |
        | Here you may specify if you want to allow local subnets to access
        | your webhook url in non-local environment. This is useful for
        | testing purposes or if a custom bot API server is used.
        |
        */

        'allow_local_subnets' => env('TELEPATH_ALLOW_LOCAL_SUBNETS', false),

        /*
        |--------------------------------------------------------------------------
        | Webhook Middleware
        |--------------------------------------------------------------------------
        |
        |
        |
        */

        'middleware' => [
            Telepath\Laravel\Http\Middleware\ResolveWebhook::class,
            Telepath\Laravel\Http\Middleware\ValidateRequestSource::class,
            Telepath\Laravel\Http\Middleware\ValidateSecretToken::class,
        ],

        /*
        |--------------------------------------------------------------------------
        | Webhook Resolver
        |--------------------------------------------------------------------------
        |
        | Here you may specify the class that is responsible for resolving
        | the webhook url secret. The default implementation uses Laravels
        | Hash::make function.
        |
        */

        'resolver'   => \Telepath\Laravel\WebhookResolver\HashWebhookResolver::class,

    ],

];