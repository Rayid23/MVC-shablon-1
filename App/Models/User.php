<?php

namespace App\Models;

class User extends Model {
    public static $table = "user";
    public static $columns = ['id', 'name', 'login', 'password'];
    
}

?>