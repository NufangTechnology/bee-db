<?php
require dirname(__DIR__) . '/vendor/autoload.php';

go(function () {

    $config = [
        'host' => '',
        'port' => 0,
        'options' => [
            'connect_timeout' => 1,
            'timeout'         => 1,
            'reconnect'       => 3,
            'password'        => ''
        ]
    ];

    $redis = new \Swoole\Coroutine\Redis();
    $redis->connect('192.168.0.254', 6379);
    $redis->set('hello', 'word');

    $val = $redis->get('hello');

    echo $val;
});