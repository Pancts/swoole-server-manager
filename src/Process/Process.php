<?php

namespace ServerManager\Process;

use swoole_process;
use ServerManager\Queue\Queue;

abstract class Process
{


    public $name = '';

    public $params = [];

    public $className = '';


    /**
     * @var swoole_process
     */
    public $process = null;




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