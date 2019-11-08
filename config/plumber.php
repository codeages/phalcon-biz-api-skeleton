<?php

return [
    'app_name' => 'ExampleWorker',
    'queues' => [
        'default_queue' => [
            'type' => 'beanstalk',
            'host' => env('BEANSTALKD_HOST', '127.0.0.1'),
            'port' => env('BEANSTALKD_PORT', 11300),
        ],
    ],
    'workers' => [
        [
            'class' =>  \App\Worker\ExampleWorker::class,
            'num' => 1,
            'queue' => 'default_queue',
            'topic' => 'example_worker_topic',
        ],
    ],
    'log_path' => dirname(__DIR__) . '/var/log/plumber.log',
    'pid_path' => dirname(__DIR__) . '/var/run/plumber.pid',
];

