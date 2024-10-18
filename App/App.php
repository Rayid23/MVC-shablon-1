<?php

namespace App;

use App\Routes\Request;
use App\Routes\Route;

class App
{
    public function run()
    {
        $request = new Request();
        $route = new Route($request);
        $route->action();
    }

    public function logout()
    {
        session_destroy();
    }
}
