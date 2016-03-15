<?php

namespace ServerManager\demo\simple;
use ServerManager\Api\Controller;

class TestController extends Controller
{


    public function actionIndex()
    {

        return 'index test';
    }


    public function actionStatus()
    {

        //向所有子进程发送status命令
        $this->_manager->sendCommandAll('status');

        //堵塞接收所有子进程返回status命令的执行结果
        $result = $this->_manager->getCommandAll();


        var_dump($result);

        return $result;

    }

    /**
     * 测试 process 发送 queue
     */
    public function actionQueue()
    {

        //指定明确的子进程执行 queue 命令，第三个参数是入参
        $this->_manager->sendCommand('process_name_two', 'queue', 'queue_1');

    }



}