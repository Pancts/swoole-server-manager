<?php

namespace ServerManager\Api;

use ServerManager\Tool\ConsoleTable;

class Controller
{


    private $_request = null;

    /**
     * @var \ServerManager\Manager\Server
     */
    protected $_manager = null;


    /**
     *
     * @param $request
     * @param $manager \ServerManager\Manager\Server
     */
    public function __construct($request, $manager)
    {

        $this->_request = $request;
        $this->_manager = $manager;

    }


    public function get($key = null)
    {

        return $this->_request->get[$key];

    }


    public function post()
    {

    }


    public function actionIndex()
    {

        return 'index';
    }

    public function actionStatus()
    {

        $this->_manager->sendCommandAll('status');

        $result = $this->_manager->getCommandAll();



        $table = new ConsoleTable();


        $table = $table->setHeaders(['name', 'count', 'wait'])->addRow(['xxx', '2', '34'])->getTable();


        echo $table;

        //var_dump($result);

        return $table;

    }


    /**
     * 返回work 列表
     */
    public function actionWork()
    {


    }


    /**
     * 动态增加个work
     */
    public function actionAddWork()
    {


    }


    /**
     * 返回task 进程
     */
    public function actionTask()
    {


    }


    /**
     * 动态增加 task进程
     */
    public function actionAddTask()
    {



    }





}