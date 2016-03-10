<?php

namespace ServerManager\Api;

use swoole_http_request;
use swoole_http_response;

class HttpEvent
{


    /**
     * swoole server
     * @var null
     */
    public $manager = null;

    public $controller = '';


    /**
     * @param $manager \ServerManager\Manager\Server
     * @param $controller
     */
    public function __construct($manager, $controller)
    {

        $this->manager = $manager;
        $this->controller = $controller;

    }


    public function onHandler()
    {

        $serv = $this->manager->getSwooleServer();


        $serv->on('request', [$this, 'onConnect']);
        //$serv->on('receive', [$this, 'onReceive']);
        $serv->on('request', [$this, 'onRequest']);
        $serv->on('close', [$this, 'onClose']);

    }


    public function onRequest(swoole_http_request $request, swoole_http_response $response)
    {


        $request_uri = $request->server['request_uri'];
        $uris = explode('/', $request_uri);
        $result = '404';

        try {

            $api = new $this->controller($request, $this->manager);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        if (!empty($uris[1]) && $uris[1] == 'api') {
            $action = 'action' . ucfirst($uris[2] ? $uris[2] : 'index');


        } else {
            $action = 'actionIndex';
        }

        if (method_exists($api, $action)) {
            $result = $api->{$action}();
        }

        $response->end((string) $result);

    }


    public function onConnect($serv, $fd)
    {

        echo "Client:Connect.\n";
    }


    public function onReceive($serv, $fd, $from_id, $data)
    {
        $serv->send($fd, 'Swoole: '.$data);
        $serv->close($fd);

    }


    public function onClose($serv, $fd)
    {

        echo "Client: Close.\n";
    }


}