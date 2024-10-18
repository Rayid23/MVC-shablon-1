<?php

namespace App\Routes;

class Request
{
    public function url(){

        $path =  $_SERVER['REQUEST_URI'] ?? '/';

        $parsedUrl = parse_url($path);

        return $parsedUrl['path'] ?? '/';

    }
    public function method(){
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        return $method;
    }
}

?>