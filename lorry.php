<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/19
 * Time: 10:08
 */
require 'Slim/Slim.php';
require 'connect.php';
use Slim\PDO\Database;


\Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
$app->post('/lorry',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $body=$app->request->getBody();
    $body=json_decode($body);
    $database=localhost();
    $plate_number=$body->plate_number;
    $driver_name=$body->driver_name;
    $driver_phone=$body->driver_phone;
    $driver_identycard=$body->driver_identycard;
    $array=array();
    foreach($body as $key=>$value){
        $array[$key]=$value;
    }
    $array['tenant_id']=$tenant_id;
    if($tenant_id!=''||$tenant_id!=null){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data2 = $stmt->fetch();
        if($data2!=null){
        if($plate_number!=null||$plate_number!=''){
            $selectStatement = $database->select()
                ->from('lorry')
                ->where('exist',"=",0)
                ->where('plate_number','=',$plate_number);
            $stmt = $selectStatement->execute();
            $data3 = $stmt->fetch();
            if($data3!=null){
            if($driver_name!=null||$driver_name!=''){
                if($driver_phone!=''||$driver_phone!=null){
                    $selectStatement = $database->select()
                        ->from('tenant')
                        ->where('exist',"=",0)
                        ->where('','=',$driver_phone)
                        ->where('tenant_id','=',$tenant_id);
                    $stmt = $selectStatement->execute();
                    $data4 = $stmt->fetch();
                    if($data4!=null){
                    if(preg_match("/^1[34578]\d{9}$/", $driver_phone)) {
                        if ($driver_identycard != '' || $driver_identycard != null) {
                            $selectStatement = $database->select()
                                ->from('lorry')
                                ->where('tenant_id', '=', $tenant_id);
                            $stmt = $selectStatement->execute();
                            $data = $stmt->fetchAll();
                            if ($data != null) {
                                $lorry_id = count($data) + 100000001;
                            } else {
                                $lorry_id = 100000001;
                            }
                            $array['lorry_id'] = $lorry_id;
                            $array['exist'] = 0;
                            $insertStatement = $database->insert(array_keys($array))
                                ->into('lorry')
                                ->values(array_values($array));
                            $insertId = $insertStatement->execute(false);
                            echo json_encode(array("result" => "0", "desc" => "success"));
                        } else {
                            echo json_encode(array("result" => "1", "desc" => "缺少司机身份证"));
                        }
                    }else{
                        echo json_encode(array("result"=>"2","desc"=>"电话号码不符合要求"));
                    }
                    }else{
                        echo json_encode(array("result"=>"3","desc"=>"该公司下该电话已经存在"));
                    }
                }else{
                    echo json_encode(array("result"=>"4","desc"=>"缺少司机电话"));
                }
            }else{
                echo json_encode(array("result"=>"5","desc"=>"缺少司机姓名"));
            }
            }else{
                echo json_encode(array("result"=>"6","desc"=>"车牌号已经存在"));
            }
        }else{
            echo json_encode(array("result"=>"7","desc"=>"缺少车牌号"));
        }
        }else{
            echo json_encode(array("result"=>"8","desc"=>"该租户不存在"));
        }
    }else{
        echo json_encode(array("result"=>"9","desc"=>"缺少租户id"));
    }
});

$app->put('/lorry',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $body=$app->request->getBody();
    $body=json_decode($body);
    $database=localhost();
    $lorry_id=$body->lorry_id;
    $array=array();
    foreach($body as $key=>$value){
        $array[$key]=$value;
    }
    if($tenant_id!=''||$tenant_id!=null){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data2 = $stmt->fetch();
        if($data2!=null){
        if($lorry_id!=''||$lorry_id!=null){
            $selectStatement = $database->select()
                ->from('lorry')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist','=',0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            if($data!=null){
                $updateStatement = $database->update($array)
                    ->table('lorry')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('exist','=',0)
                    ->where('lorry_id','=',$lorry_id);
                $affectedRows = $updateStatement->execute();
                echo json_encode(array("result"=>"0","desc"=>"success"));
            }else{
                echo json_encode(array("result"=>"1","desc"=>"车辆不存在"));
            }
        }else{
            echo json_encode(array("result"=>"2","desc"=>"缺少车辆id"));
        }
        }else{
            echo json_encode(array("result"=>"3","desc"=>"该租户不存在"));
        }
    }else{
        echo json_encode(array("result"=>"4","desc"=>"缺少租户id"));
    }
});

$app->get('/lorry',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant-id');
    $database=localhost();
    $page=$app->request->get("page");
    $per_page=$app->request->get("per_page");
    if(($tenant_id!=''||$tenant_id!=null)){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data2 = $stmt->fetch();
        if($data2!=null){
        if($page==null||$per_page==null){
            $selectStatement = $database->select()
                ->from('lorry')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            echo  json_encode(array("result"=>"0","desc"=>"success","lorries"=>$data));
        }else{
            $selectStatement = $database->select()
                ->from('lorry')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist',"=",0)
                ->limit((int)$per_page,(int)$per_page*(int)$page);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            echo json_encode(array("result"=>"0","desc"=>"success","lorries"=>$data));
        }
        }else{
            echo json_encode(array("result"=>"1","desc"=>"该租户不存在"));
        }
    }else{
        echo json_encode(array("result"=>"2","desc"=>"信息不全","lorries"=>""));
    }
});

$app->delete('/lorry',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant-id');
    $database=localhost();
    $lorry_id=$app->request->get('lorryid');
    if(($tenant_id!=''||$tenant_id!=null)){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data2 = $stmt->fetch();
        if($data2!=null){
        if($lorry_id!=""||$lorry_id!=null){
            $selectStatement = $database->select()
                ->from('lorry')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist',"=",0)
                ->where('lorry_id','=',$lorry_id);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if($data!=null){
                $selectStatement = $database->select()
                    ->from('scheduling')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('exist',"=",0)
                    ->where('lorry_id','=',$lorry_id);
                $stmt = $selectStatement->execute();
                $data1 = $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('schedule_order')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('exist',"=",0)
                    ->where('schedule_id','=',$data1['schedule_id']);
                $stmt = $selectStatement->execute();
                $data2 = $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('orders')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('exist',"=",0)
                    ->where('order_id','=',$data2['order_id']);
                $stmt = $selectStatement->execute();
                $data3 = $stmt->fetch();
                if($data3['order_status']==0||$data3['order_status']==5){
                    $updateStatement = $database->update(array('exist' => 1))
                        ->table('lorry')
                        ->where('tenant_id','=',$tenant_id)
                        ->where('exist',"=",0)
                        ->where('lorry_id','=',$lorry_id);
                    $affectedRows = $updateStatement->execute();
                    echo json_encode(array("result"=>"0","desc"=>"success"));
                }else{
                    echo json_encode(array("result"=>"1","desc"=>"车辆在送货途中"));
                }

            }else{
                echo json_encode(array("result"=>"2","desc"=>'车辆不存在'));
            }
        }else{
            echo json_encode(array("result"=>"3","desc"=>"缺少车辆id"));
        }
        }else{
            echo json_encode(array("result"=>"4","desc"=>"该租户不存在"));
        }
    }else{
        echo json_encode(array("result"=>"5","desc"=>"缺少租户id"));
    }
});

$app->run();


function localhost(){
    return connect();
}
?>