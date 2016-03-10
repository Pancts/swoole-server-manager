<?php

require dirname(__DIR__) . '/autoload.php';


ServerManager\Queue\Queue::setConfig([
    'host' => 'localhost:6379',
    'name' => 'default',
    'classBase' => 'ServerManager\demo\simple\queue'
]);


$server = new ServerManager\Manager\Server();

$server->setApiClassName('ServerManager\demo\simple\TestController');

//设置 process, task 设置
$server->setProcessConfig([

    'process_name' => [
        'class' => 'ServerManager\demo\simple\process\DemoServer',
        'params' => ['data' => 1]

    ],

    'process_name_two' => [
        'class' => 'ServerManager\demo\simple\process\DemoServer',
        'params' => ['data' => 2]
    ]


]);




$server->run();