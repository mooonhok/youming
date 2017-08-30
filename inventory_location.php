<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/21
 * Time: 14:35
 */
require 'Slim/Slim.php';
require 'connect.php';
use Slim\PDO\Database;


\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->post('/inventory_location',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $body=$app->request->getBody();
    $body=json_decode($body);
    $database=localhost();
    $inventory_loc_name=$body->inventory_loc_name;
    if($tenant_id!=null||$tenant_id!=''){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data2 = $stmt->fetch();
        if($data2!=null){
        if($inventory_loc_name!=null||$inventory_loc_name!=''){
            $selectStatement = $database->select()
                ->from('inventory_location')
                ->where('inventory_loc_name','=',$inventory_loc_name)
                ->where('exist','=',0)
                ->where('tenant_id', '=',$tenant_id);
            $stmt = $selectStatement->execute();
            $data1 = $stmt->fetch();
            if($data1==null){
                $selectStatement = $database->select()
                    ->from('inventory_location')
                    ->where('tenant_id', '=',$tenant_id);
                $stmt = $selectStatement->execute();
                $data = $stmt->fetchAll();
                if($data!=null){
                    $inventory_loc_id=count($data)+100000001;
                }else{
                    $inventory_loc_id=100000001;
                }
                $insertStatement = $database->insert(array('inventory_loc_name','inventory_loc_id','exist','tenant_id'))
                    ->into('inventory_location')
                    ->values(array($inventory_loc_name,$inventory_loc_id,0,$tenant_id));
                $insertId = $insertStatement->execute(false);
                echo json_encode(array("result"=>"0","desc"=>"success"));
            }else{
                echo json_encode(array("result"=>"1","desc"=>"库名已存在"));
            }
        }else{
            echo json_encode(array("result"=>"2","desc"=>"缺少库位名称"));
        }
        }else{
            echo json_encode(array('result'=>'3','desc'=>'该租户不存在'));
        }
    }else{
        echo json_encode(array("result"=>"4","desc"=>"缺少租户id"));
    }
});

$app->get('/inventory_location',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $page=$app->request->get('page');
    $per_page=$app->request->get("per_page");
    $database=localhost();
    if($tenant_id!=null||$tenant_id!=""){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data2 = $stmt->fetch();
        if($data2!=null){
        if($page==null||$per_page==null){
            $selectStatement = $database->select()
                ->from('inventory_location')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            echo  json_encode(array("result"=>"0","desc"=>"success","locations"=>$data));
        }else{
            $selectStatement = $database->select()
                ->from('inventory_location')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist',"=",0)
                ->limit((int)$per_page,(int)$per_page*(int)$page);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            echo json_encode(array("result"=>"0","desc"=>"success","locations"=>$data));
        }
        }else{
            echo json_encode(array('result'=>'1','desc'=>'该租户不存在'));
        }
    }else{
        echo json_encode(array("result"=>"2","desc"=>"缺少租户信息","locations"=>""));
    }
});

$app->delete('/inventory_location',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $database=localhost();
    $inventory_loc_id =$app->request->get('inventory_loc_id');
    if($tenant_id!=null||$tenant_id!=''){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data2 = $stmt->fetch();
        if($data2!=null){
        if($inventory_loc_id!=null||$inventory_loc_id!=''){
            $selectStatement = $database->select()
                ->from('inventory_location')
                ->where('tenant_id','=',$tenant_id)
                ->where('inventory_loc_id','=',$inventory_loc_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if($data!=null){
                $updateStatement = $database->update(array('exist'=>1))
                    ->table('inventory_location')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('exist',"=",0)
                    ->where('inventory_loc_id','=',$inventory_loc_id);
                $affectedRows = $updateStatement->execute();
                echo json_encode(array("result"=>"0","desc"=>"success"));
            }else{
                echo json_encode(array("result"=>"1","desc"=>"库位不存在"));
            }
        }else{
            echo json_encode(array("result"=>"2","desc"=>"缺少库位id"));
        }
        }else{
            echo json_encode(array('result'=>'3','desc'=>'该租户不存在'));
        }
    }else{
        echo json_encode(array("result"=>"4","desc"=>"缺少租户id"));
    }
});

$app->run();

function localhost(){
    return connect();
}
?>