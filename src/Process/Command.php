<?php

namespace ServerManager\Process;

class Command
{

    public $data = null;

    public $command = '';

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