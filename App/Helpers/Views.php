<?php

namespace App\Helpers;

class Views
{
    //public static $main = "main";
    public static function make($view, $title, $models = [], $path)
    {

        ob_start();
        include dirname(__DIR__) . "/Views/" . $view . '.php';
        $content = ob_get_clean();
        include dirname(__DIR__) . '/Views/' . $path;
    }
}
