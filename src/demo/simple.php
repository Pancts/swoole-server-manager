<?php

require dirname(__DIR__) . '/autoload.php';


//队列的基本设置
ServerManager\Queue\Queue::setConfig([
    'host' => 'localhost:6379',
    'name' => 'default',
    'classBase' => 'ServerManager\demo\simple\queue'
]);

//框架实例
$server = new ServerManager\Manager\Server();

//设置服务api类
$server->setApiClassName('ServerManager\demo\simple\TestController');

//设置 process 设置
$server->setProcessConfig([

    //服务子进程名称，唯一，如果进程类代码都一样，还是请设置不同的名字。
    'process_name' => [
        //子进程类
        'class' => 'ServerManager\demo\simple\process\DemoServer',
        //子进程启动入参
        'params' => ['data' => 1]

    ],

    'process_name_two' => [
        'class' => 'ServerManager\demo\simple\process\DemoServer',
        'params' => ['data' => 2]
    ]


]);

//启动唠
$server->run();