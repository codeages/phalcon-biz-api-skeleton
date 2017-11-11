<?php

return [
    'subscribers' => [
        'Codeages\\PhalconBiz\\Authentication\\ApiAuthenticateSubscriber',
        'Codeages\\PhalconBiz\\Event\\ExceptionSubscriber',
        'Codeages\\PhalconBiz\\Event\\ResponseSubscriber',
    ],

    'user_provider' => 'ApiUserProvider',
];
