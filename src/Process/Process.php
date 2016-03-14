<?php

namespace ServerManager\Process;

use swoole_process;
use ServerManager\Queue\Queue;

abstract class Process
{


    /**
     * 进程名称
     * @var string
     */
    public $name = '';


    /**
     * 进程启动参数
     * @var array
     */
    public $params = [];


    /**
     * 进程类名称
     * @var string
     */
    public $className = '';


    /**
     * 进程实例
     * @var swoole_process
     */
    public $process = null;


    /**
     * 进程初始化
     * @param swoole_process $worker
     */
    public function init(swoole_process $worker)
    {

        //$this->process = $worker;

        //swoole_set_process_name($this->name);

        $this->run();

        $self = $this;
        swoole_event_add($worker->pipe, function($pipe) use ($self) {

            $worker = $self->process;
            $result = $worker->read();
            $command = $self->runCommand($result);

            //send data to master
            $worker->write($command);

        });

    }



    /**
     * @param string $result
     * @return void $data
     */
    public function runCommand($result = '')
    {

        $info = null;
        $data = null;
        $command = null;

        if (!empty($result)) {
            $command = unserialize($result);
            $commandAction = $command->getCommand();
            $data = $command->getData();
        }

        $action = 'command' . ucfirst($commandAction);
        if (method_exists($this, $action)) {
            $info = $this->{$action}($data);

            $command->setResult($info);
        } else {
            echo "command {$action} is error!";
        }

        return $command;

    }



    /**
     * 进程执行入口
     * @return mixed
     */
    abstract public function run();


    /**
     * 当前work状态
     */
    abstract function commandStatus();


    /**
     * 发送队列
     * @param string $qname
     * @param array $data
     */
    public function sendQueue($qname, $data = [])
    {

        Queue::sendQueue($qname, $data);

    }


    /**
     * 向主进程发送数据
     */
    public function sendMaster($data = '')
    {
        return $this->process->write($data);
    }

}