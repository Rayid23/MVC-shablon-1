<?php

use App\Helpers\Views;
use App\Helpers\Auth;

if(!function_exists('dd')){
    function dd(... $data){
        echo "<body bgcolor='black'>";
        echo "<pre style='background-color-black; color:#0dbb2a; font-family: monospace'>";
        print_r($data);
        echo "</pre>";
        exit();
    }
}

if(!function_exists("view")){
    function view($view, $title, $data = [], $path){
        Views::make($view, $title, $data, $path);
    }
}

if(!function_exists("check")){
    function check(){
        return Auth::check();
    }
}

if(!function_exists("auth")){
    function auth(){
        return Auth::user();
    }
}

// if(!function_exists("layout")){
//     function layout($view){
//         Views::$main = $view;
//     }
// }

?>