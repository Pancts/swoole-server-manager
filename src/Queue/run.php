#!/usr/bin/env php
<?php

include_once dirname(__DIR__) . '/autoload.php';

$resquePath = VENDOR_DIR . '/chrisboulton/php-resque/resque.php';



foreach ($argv as $i => $v) {

    if ($i > 0) {
        putenv($v);
    }


};

include $resquePath;
