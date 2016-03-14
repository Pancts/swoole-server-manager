<?php

namespace ServerManager\Process;

/**
 * 进程间 数据传递对象
 * @package ServerManager\Process
 */
class Command
{

    /**
     * 命令入参值
     * @var null
     */
    public $data = null;

    /**
     * 命令名
     * @var null
     */
    public $command = '';

    /**
     * 命令返回值
     * @var null
     */
    public $result = null;


    public function __construct($command, $data)
    {

        $this->data = $data;
        $this->command = $command;

    }


    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getData()
    {
        return $this->data;
    }


    public function __tostring()
    {
        return serialize($this);
    }


}