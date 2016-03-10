<?php

require dirname(__DIR__) . '/autoload.php';


$server = new swoole_server('127.0.0.1', 9501);

$process = new swoole_process(function($process) use ($server) {
    while (true) {
        $msg = $process->read();
        foreach($server->connections as $conn) {
            $server->send($conn, $msg);
        }
    }
});

$server->addProcess($process);

$server->on('receive', function ($serv, $fd, $from_id, $data) use ($process) {


    //如何获取此进程 $process  的 work_id ?

    $serv->sendMessage($data, $work_id);

    $process->write($data);
});

$server->start();
