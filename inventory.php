<?php
require 'Slim/Slim.php';
require 'connect.php';
use Slim\PDO\Database;


\Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
$app->post('/inventory',function()use($app){
	$app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $body=$app->request->getBody();
    $body=json_decode($body);
    $database=localhost();
    $order_id=$body->order_id;
    if($tenant_id!=null||$tenant_id!=""){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data2 = $stmt->fetch();
        if($data2!=null){
        if($order_id!=''||$order_id!=null){
            $selectStatement = $database->select()
                ->from('inventory')
                ->where('tenant_id','=',$tenant_id);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            if($data!=null){
                $inventory_id=count($data)+100000001;
            }else{
                $inventory_id=100000001;
            }
            $selectStatement = $database->select()
                ->from('inventory')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist','=',0)
                ->where('order_id','=',$order_id);
            $stmt = $selectStatement->execute();
            $data1 = $stmt->fetch();
            $selectStatement = $database->select()
                ->from('orders')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist','=',0)
                ->where('order_id','=',$order_id);
            $stmt = $selectStatement->execute();
            $data2 = $stmt->fetch();
            if($data2!=null){
                $updateStatement = $database->update(array('order_status'=>'1'))
                    ->table('orders')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('order_id','=',$order_id);
                $affectedRows = $updateStatement->execute();
                if($data1==null){
                    $insertStatement = $database->insert(array('inventory_id', 'inventory_type', 'order_id','tenant_id','exist'))
                        ->into('inventory')
                        ->values(array($inventory_id, 0,$order_id,$tenant_id,0));
                    $insertId = $insertStatement->execute(false);
                    echo json_encode(array("result"=>"0","desc"=>"success"));
                }else{
                    echo json_encode(array("result"=>"1","desc"=>"仓库已建立"));
                }
            }else{
                echo json_encode(array("result"=>"2","desc"=>"订单不存在"));
            }
        }else{
            echo json_encode(array('result'=>'3','desc'=>'缺少运单id'));
        }
        }else{
            echo json_encode(array('result'=>'4','desc'=>'租户不存在'));
        }
    }else{
        echo json_encode(array('result'=>'5','desc'=>'缺少租户id'));
    }
});

$app->get('/inventory',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $page=$app->request->get('page');
    $per_page=$app->request->get("per_page");
    $database=localhost();
    if($tenant_id!=null||$tenant_id!=""){
        if($page==null||$per_page==null){
            $selectStatement = $database->select()
                ->from('inventory')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            $inventories=array();
            $num = count($data);
            for($i=0;$i<$num;++$i){
                $inventory=array();
                foreach($data[$i] as $k=>$v){
                    if($k=="inventory_id"){
                        $inventory['inventory_id']=$v;
                    }
                    if($k=="order_id"){
                        $selectStatement = $database->select()
                            ->from('goods')
                            ->where('order_id','=',$v)
                            ->where('exist',"=",0);
                        $stmt = $selectStatement->execute();
                        $data1 = $stmt->fetch();
                        $inventory["goods_name"]= $data1["goods_name"];
                        $inventory["goods_capacity"]= $data1["goods_capacity"];
                        $inventory["goods_weight"]= $data1["goods_weight"];
                        $inventory["goods_count"]= $data1["goods_count"];
                        $inventory["goods_package"]= $data1["goods_package"];
                        $selectStatement = $database->select()
                            ->from('orders')
                            ->where('order_id','=',$v)
                            ->where('exist',"=",0);
                        $stmt = $selectStatement->execute();
                        $data2 = $stmt->fetch();
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('customer_id','=',$data2['sender_id'])
                            ->where('exist',"=",0);
                        $stmt = $selectStatement->execute();
                        $data3 = $stmt->fetch();
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('customer_id','=',$data2['receiver_id'])
                            ->where('exist',"=",0);
                        $stmt = $selectStatement->execute();
                        $data4 = $stmt->fetch();
                        $inventory["send_city"]= $data3["customer_city"];
                        $inventory["receive_city"]= $data4["customer_city"];
                    }
                }
                array_push($inventories,$inventory);
            }
            echo  json_encode(array("result"=>"0","desc"=>"success","inventories"=>$inventories));
        }else{
            $selectStatement = $database->select()
                ->from('inventory')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist',"=",0)
                ->limit((int)$per_page,(int)$per_page*(int)$page);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            $inventories=array();
            $num = count($data);
            for($i=0;$i<$num;++$i){
                $inventory=array();
                foreach($data[$i] as $k=>$v){
                    if($k=="inventory_id"){
                        $inventory['inventory_id']=$v;
                    }
                    if($k=="order_id"){
                        $selectStatement = $database->select()
                            ->from('goods')
                            ->where('order_id','=',$v)
                            ->where('exist',"=",0);
                        $stmt = $selectStatement->execute();
                        $data1 = $stmt->fetch();
                        $inventory["goods_name"]= $data1["goods_name"];
                        $inventory["goods_capacity"]= $data1["goods_capacity"];
                        $inventory["goods_weight"]= $data1["goods_weight"];
                        $inventory["goods_count"]= $data1["goods_count"];
                        $inventory["goods_package"]= $data1["goods_package"];
                        $selectStatement = $database->select()
                            ->from('orders')
                            ->where('order_id','=',$v)
                            ->where('exist',"=",0);
                        $stmt = $selectStatement->execute();
                        $data2 = $stmt->fetch();
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('customer_id','=',$data2['sender_id'])
                            ->where('exist',"=",0);
                        $stmt = $selectStatement->execute();
                        $data3 = $stmt->fetch();
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('customer_id','=',$data2['receiver_id'])
                            ->where('exist',"=",0);
                        $stmt = $selectStatement->execute();
                        $data4 = $stmt->fetch();
                        $inventory["send_city"]= $data3["customer_city"];
                        $inventory["receive_city"]= $data4["customer_city"];
                    }
                }
                array_push($inventories,$inventory);
            }
            echo  json_encode(array("result"=>"0","desc"=>"success","inventories"=>$inventories));
        }
    }else{
        echo json_encode(array("result"=>"1","desc"=>"信息不全","inventories"=>""));
    }
});

$app->delete('/inventory',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $database=localhost();
    $inventory_id=$app->request->get('inventory_id');
    if($tenant_id!=null||$tenant_id!=""){
            $selectStatement = $database->select()
                ->from('inventory')
                ->where('tenant_id','=',$tenant_id)
                ->where('inventory_id','=',$inventory_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if($data!=null) {
                $selectStatement = $database->select()
                    ->from('orders')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('order_id','=',$data['order_id'])
                    ->where('exist',"=",0);
                $stmt = $selectStatement->execute();
                $data1 = $stmt->fetch();
                if (($data['order_status'] == 0 || $data['order_status'] == 5) && $data != null) {
                    $updateStatement = $database->update(array('exist' => '1'))
                        ->table('inventory')
                        ->where('tenant_id', '=', $tenant_id)
                        ->where('inventory_id', '=', $inventory_id)
                        ->where('exist', '=', 0);
                    $affectedRows = $updateStatement->execute();
                    echo json_encode(array("result" => "0", "desc" => "success"));
                } else {
                    echo json_encode(array("result" => "2", "desc" => "订单还在途中"));
                }
            }else{
                echo json_encode(array("result" => "3", "desc" => "仓库不存在"));
            }
    }else{
        echo json_encode(array("result"=>"4","desc"=>"信息不全"));
    }
});



$app->run();


function localhost(){
    return connect();
}
?>