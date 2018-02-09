<?php

return [
    'subscribers' => [
        'Codeages\\PhalconBiz\\Authentication\\ApiAuthenticateSubscriber',
        'Codeages\\PhalconBiz\\Event\\ExceptionSubscriber',
        'Codeages\\PhalconBiz\\Event\\ResponseSubscriber',
    ],

    'user_provider' => 'App\\ApiUserProvider',
    'route_discovery' => [
        'App\\Controller' => dirname(__DIR__).'/src/Controller',
    ],
];
