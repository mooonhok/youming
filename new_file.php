<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'test.php';
require 'Slim/Slim.php';

use Slim\PDO\Database;
use Slim\PDO\Statement;
use Slim\PDO\Statement\SelectStatement;

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim();

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */




// GET route

$app->get('/testPage', function() use ($app) {
    echo $app->request->get('name');
});


$app->get("/",function() use ($app) {
    $tablename=$app->request->get('name');
    getAllBook($tablename);
});

$app->get("/gettable",function(){
	$database=new database("mysql:host=127.0.0.1;dbname=its_youming;charset=utf8","root","");
    $selectStatement = $database->select()
                       ->from('goods')
                       ->where('goods_id', '=', "1");
    $stmt = $selectStatement->execute();
    $data = $stmt->fetchAll();
    echo json_encode($data);
});

$app->delete('/order', function() use ($app) {
   $tenant_id=$app->request->headers->get("tenant-id");
   $orderid=$app->request->get("orderid");
   $database=new database("mysql:host=127.0.0.1;dbname=its_youming;charset=utf8","root","");
   $selectStatement = $database->select()
                      ->from('tenant')
                      ->where('tenant_id', '=',$tenant_id);
    $stmt = $selectStatement->execute();
    $data = $stmt->fetch();
    echo $data["tenant_id"];
   
});

$app->put('/order',"getAllBook");

$app->get('/weixin',function(){
$url='http://mooonhok-cloudware.daoapp.io/test7.php';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    echo $output;
});

$app->run();

function getConnection()
{
    $dbhost = "127.0.0.1";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "its_youming";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}
function getAllBook()
{
    $tablename="goods";
    $sql = "SELECT * FROM ".$tablename."";
    try {
        $db =getConnection();
        $stmt = $db->query($sql);
        $book = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"book": ' . json_encode($book) . '}';
    } catch (PDOException $e) {
     
        echo "fff";
    }
}


function getopenid(){

getBaseInfo();
}