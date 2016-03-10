<?php

namespace ServerManager\demo\simple\process;

use swoole_timer_tick;

class DemoServer extends \ServerManager\Process\Process
{



    public function run()
    {

        //定时

        $self = $this;

        swoole_timer_tick(1000, function() use ($self) {

            $self->log($self->name . date('Y-m-d H:i:s') . "\n");


            //echo $self->name;
        });

    }

    public function log($data = '')
    {

        $file = dirname(__DIR__) . '/log/' . $this->name . '.log';

        file_put_contents($file, $data, FILE_APPEND);


    }



    public function commandStatus()
    {


        return [
            'name' => $this->name,
            'count' => $this->params['data'] * 2,
            'error' => 20
        ];

    }


    public function commandQueue()
    {

        $data = [
            'email' => 'test@qq.com',
            'text' => 'hello ! ' . $this->name,
            'date' => date('Y-m-d H:i:s')
        ];

        $this->sendQueue('EmailWork', $data);


    }



}