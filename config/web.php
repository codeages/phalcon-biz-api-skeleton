<?php

return [
    'subscribers' => [
        // 'Codeages\\PhalconBiz\\Authentication\\ApiAuthenticateSubscriber',
        'Codeages\\PhalconBiz\\Event\\ExceptionSubscriber',
        'Codeages\\PhalconBiz\\Event\\ResponseSubscriber',
    ],

    'user_provider' => 'ApiUserProvider',
    'route_discovery' => [
        'Controller' => dirname(__DIR__).'/src/Controller'
    ]
];
