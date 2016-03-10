<?php

namespace ServerManager\demo\simple\queue;


class EmailWork extends \ServerManager\Queue\Queue
{


    public function perform()
    {
        // Work work work
        var_dump($this->args);
    }



}