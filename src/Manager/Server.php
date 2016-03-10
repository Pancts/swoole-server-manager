<?php

namespace ServerManager\Manager;

use swoole_server;
use swoole_process;
use swoole_http_server;
use ServerManager\Api\HttpEvent;
use ServerManager\Process\Command;

class Server
{

    /**
     * @var swoole_server
     */
    private $_server = null;

    private $_processConfig = [];

    private $_process = [];

    private $tasks = [];

    private $_apiClassName = '';


    public function run()
    {


        $this->prepare();


        $list = swoole_process::wait();
        var_dump($list);

        $this->getSwooleServer()->start();




    }


    public function setProcessConfig($process = [])
    {
        $this->_processConfig = $process;

    }


    public function setApiClassName($className)
    {

        $this->_apiClassName = $className;
    }


    /**
     * @return swoole_server
     */
    public function getSwooleServer()
    {
        return $this->_server;
    }


    /**
     * 准备工作
     */
    public function prepare()
    {

        $this->_server = new swoole_http_server("127.0.0.1", 9501);

        $this->getSwooleServer()->set(array('worker_num' => 1, 'daemonize' => false, 'task_worker_num' => 0));

        $this->getSwooleServer()->on('pipeMessage', [$this, 'pipeMessageAll']);

        $this->getSwooleServer()->on('ManagerStart', function ($serv) {
            //global $argv;
            //swoole_set_process_name("php {$argv[0]}: manager");
        });


        $this->getSwooleServer()->on('start', [$this, 'onStart']);

        $this->getSwooleServer()->on('WorkerStart', [$this, 'my_onWorkerStart']);

        $this->prepareApi();

        $this->prepareProcess();

        $this->prepareQueue();
    }


    public function onStart(swoole_server $server)
    {


        //$this->prepareProcess();

    }

    function my_onWorkerStart(swoole_server $serv, $worker_id)
    {

        //echo "#{$serv->worker_id} message from #$worker_id \n";

    }

    public function prepareProcess()
    {

        $server = $this->getSwooleServer();

        foreach ($this->_processConfig as $name => $data) {

            $processHandler = new $data['class'];
            $processHandler->name = $name;

            $process = new swoole_process([$processHandler, 'init'], false, 2);
            //$process->useQueue();

            $server->addProcess($process);

            //$process->name($name);

            $processHandler->params = $data['params'];

            $processHandler->process = $process;
            $this->_process[$name] = $processHandler;

        }

    }


    public function prepareQueue()
    {

        //启动 resque

        $server = $this->getSwooleServer();
        $process = new swoole_process('\ServerManager\Queue\Queue::runProcess', false);
        $server->addProcess($process);

    }


    public function prepareApi()
    {


        $api = new HttpEvent($this, $this->_apiClassName);

        $api->onHandler();

    }



    /**
     * process 接受master数据
     */
    public function pipeMessageAll($serv, $src_worker_id, $data)
    {

        echo "#{$serv->worker_id} message from #$src_worker_id: $data\n";

    }


    /**
     * 向进程发送消息
     */
    public function sendCommand($name, $command ='', $data = [])
    {

        $data = new Command($command, $data);

        $processAll = $this->getProcess();
        foreach ($processAll as $pname => $process) {
            if ($pname == $name) {
                $process->process->write($data);
                break;
            }
        }

    }

    /**
     *
     * @param string $command
     * @param array $data
     */
    public function sendCommandAll($command = '', $data = [])
    {

        $processAll = $this->getProcess();
        $data = new Command($command, $data);

        foreach ($processAll as $process) {
            $process->process->write($data);
        }

    }

    public function getCommandAll()
    {

        $processAll = $this->getProcess();

        $result = [];
        foreach ($processAll as $name => $process) {
            $command = $process->process->read();
            if (!empty($command)) {
                $command = unserialize($command);
                $result[$name] = $command->getResult();

            }

        }

        return $result;

    }

    /**
     *
     * @return \ServerManager\Process\Process
     */
    public function getProcess()
    {

        return $this->_process;

    }

}