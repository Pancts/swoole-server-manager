<?php

namespace ServerManager\demo\simple;
use ServerManager\Api\Controller;

class TestController extends Controller
{


    /**
     * 测试 process 发送 queue
     */
    public function actionQueue()
    {


        $this->_manager->sendCommand('process_name_two', 'queue', 'queue_1');

    }



}