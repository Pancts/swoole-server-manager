<?php

namespace ServerManager\Queue;

use Resque;
use swoole_process;

abstract class Queue
{


    public static $config = [
        'host' => 'localhost:6379',
        'name' => 'default',
        'classBase' => ''
    ];


    abstract public function perform();



    public static function setConfig($config = [])
    {

        self::$config = array_merge(self::$config, $config);

        Resque::setBackend(self::$config['host']);
    }


    /**
     * 发送队列
     * @param string $qname
     * @param array $data
     */
    public static function sendQueue($qname, $data = [])
    {
        $classBase = self::$config['classBase'];

        Resque::enqueue(self::$config['name'], $classBase . '\\' . $qname, $data);

    }


    /**
     * 队列进程 入口方法
     * @param swoole_process $worker
     */
    public static function runProcess(swoole_process $worker)
    {

        $queuePath = __DIR__ . '/run.php';

        echo 'start queue';
        //传递必要参数 resque
        $worker->exec($queuePath, [
            'QUEUE=default',
            'VVERBOSE=1'
        ]);

    }


}