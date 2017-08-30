<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/12
 * Time: 11:38
 */
require 'Slim/Slim.php';
require 'connect.php';
use Slim\PDO\Database;


\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->get('/province',function ()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $database=localhost();
    $selectStatement = $database->select()
        ->from('province');
    $stmt = $selectStatement->execute();
    $data = $stmt->fetchAll();
    echo  json_encode(array("result"=>"0","desc"=>"success","province"=>$data));
});

$app->get('/city',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $database=localhost();
    $pid=$app->request->get('pid');
    if($pid!=null||$pid!=""){
            $selectStatement = $database->select()
                ->from('city')
                ->where('pid','=',$pid);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            echo  json_encode(array("result"=>"0","desc"=>"success","city"=>$data));
    }
});



$app->run();

function localhost(){
    return connect();
}
?>
