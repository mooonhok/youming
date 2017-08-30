<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/28
 * Time: 16:27
 */
use Slim\PDO\Database;
function connect(){
//    $serverName = env("MYSQL_PORT_3306_TCP_ADDR", "	db2.daocloudinternal.io");
//    $databaseName = env("MYSQL_INSTANCE_NAME", "temp_db");
//    $username = env("MYSQL_USERNAME", "root");
//    $password = env("MYSQL_PASSWORD", "Gd6lhOSsY");
    $serverName = env("MYSQL_PORT_3306_TCP_ADDR", "127.0.0.1");
    $databaseName = env("MYSQL_INSTANCE_NAME", "cloud_ware");
    $username = env("MYSQL_USERNAME", "root");
    $password = env("MYSQL_PASSWORD", "");
    $database=new database("mysql:host=".$serverName.";port=60212;dbname=".$databaseName.";charset=utf8",$username,$password);
    return  $database;
}
function env($key, $default = null)
{
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    return $value;
}
?>