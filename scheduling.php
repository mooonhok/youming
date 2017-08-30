<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/21
 * Time: 16:03
 */
require 'Slim/Slim.php';
require 'connect.php';


\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->post('/scheduling',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $database=localhost();
    $tenant_id=$app->request->headers->get("tenant-id");
    $body=$app->request->getBody();
    $body=json_decode($body);
    $send_city_id=$body->send_city_id;
    $receiver_id=$body->receiver_id;
    $receive_city_id=$body->receive_city_id;
    $lorry_id=$body->lorry_id;
    $is_transfer=$body->is_transfer;
    $transfer_id=$body->transfer_id;
    $transfer_charges=$body->transfer_charges;
    $order_ids=$body->order_ids;
    $array=array();
    foreach($body as $key=>$value){
        if($key!="order_ids"){
            $array[$key]=$value;
        }
    }
    if($tenant_id!=null||$tenant_id!=''){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data6 = $stmt->fetch();
        if($data6!=null){
            if($send_city_id!=null||$send_city_id!=''){
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id','=',$send_city_id);
                $stmt = $selectStatement->execute();
                $data5 = $stmt->fetch();
                if($data5!=null){
                    if($receiver_id!=null||$receiver_id!=''){
                        if($receive_city_id!=null||$receive_city_id!=''){
                            $selectStatement = $database->select()
                                ->from('city')
                                ->where('id','=',$receive_city_id);
                            $stmt = $selectStatement->execute();
                            $data4 = $stmt->fetch();
                            if($data4!=null){
                                if($lorry_id!=null||$lorry_id!=''){
                                    $selectStatement = $database->select()
                                        ->from('lorry')
                                        ->where('lorry_id','=',$lorry_id);
                                    $stmt = $selectStatement->execute();
                                    $data6 = $stmt->fetch();
                                    if($data6!=null) {
                                        if ($is_transfer != null || $is_transfer != '') {
                                            if ($transfer_id != null || $transfer_id != ''){
                                                if ($transfer_charges != null || $transfer_charges != '') {
                                                    if (count($order_ids) != 0) {
                                                        $selectStatement = $database->select()
                                                            ->from('tenant')
                                                            ->where('tenant_id', '=', $receiver_id)
                                                            ->where('exist', '=', "0");
                                                        $stmt = $selectStatement->execute();
                                                        $data = $stmt->fetch();
                                                        if ($data != null) {
                                                            $selectStatement = $database->select()
                                                                ->from('lorry')
                                                                ->where('lorry_id', '=', $lorry_id)
                                                                ->where('exist', '=', "0")
                                                                ->where('tenant_id', '=', $tenant_id);
                                                            $stmt = $selectStatement->execute();
                                                            $data1 = $stmt->fetch();
                                                            if ($data1 != null) {
                                                                $selectStatement = $database->select()
                                                                    ->from('tenant')
                                                                    ->where('exist', '=', "0")
                                                                    ->where('tenant_id', '=', $tenant_id);
                                                                $stmt = $selectStatement->execute();
                                                                $data2 = $stmt->fetch();
                                                                if ($data2 != null) {
                                                                    $selectStatement = $database->select()
                                                                        ->from('scheduling')
                                                                        ->where('tenant_id', '=', $tenant_id);
                                                                    $stmt = $selectStatement->execute();
                                                                    $data3 = $stmt->fetchAll();
                                                                    if ($data3 != null) {
                                                                        $scheduling_id = count($data3) + 100000001;
                                                                    } else {
                                                                        $scheduling_id = 100000001;
                                                                    }
                                                                    $num1=count($order_ids);
                                                                    $num2=1;
                                                                    for($j=0;$j<$num1;$j++){
                                                                        $selectStatement=$database->select()
                                                                            ->from('schedule_order')
                                                                            ->where('order_id','=',$order_ids[$j])
                                                                            ->where('exist','=',0)
                                                                            ->where('tenant_id','=',$tenant_id);

                                                            $stmt = $selectStatement->execute();
                                                            $data4 = $stmt->fetch();
                                                            if($data4!=null){
                                                                $num2=2;
                                                                break;
                                                            }
                                                            }
                                                                    if($num2==2){
                                                                        echo json_encode(array("result" => "0", "desc" => "订单已在其他调度"));
                                                                    }else if($num2==1){
                                                                        date_default_timezone_set('PRC');
                                                                        $now = date("Y-m-d H:i:s");
                                                                        $array['scheduleing_datetime'] = $now;
                                                                        $array['tenant_id'] = $tenant_id;
                                                                        $array['scheduling_id'] = $scheduling_id;
                                                                        $array['exist'] = 0;
                                                                        $insertStatement = $database->insert(array_keys($array))
                                                                            ->into('scheduling')
                                                                            ->values(array_values($array));
                                                                        $insertId = $insertStatement->execute(false);
                                                                        $num = count($order_ids);
                                                                        for ($i = 0; $i < $num; $i++) {
                                                                            $insertStatement = $database->insert(array('schedule_id', 'order_id', 'tenant_id', 'exist'))
                                                                                ->into('schedule_order')
                                                                                ->values(array($scheduling_id, $order_ids[$i], $tenant_id, '0'));
                                                                            $insertId = $insertStatement->execute(false);
                                                                        }
                                                                        echo json_encode(array("result" => "0", "desc" => "success"));
                                                                    }

                                                                } else {
                                                                    echo json_encode(array("result" => "1", "desc" => "中转接收人信息不存在"));
                                                                }
                                                            } else {
                                                                echo json_encode(array("result" => "2", "desc" => "车辆信息不存在"));
                                                            }
                                                        } else {
                                                            echo json_encode(array("result" => "3", "desc" => "收货人信息不存在"));
                                                        }

                                                    } else {
                                                        echo json_encode(array("result" => "4", "desc" => "缺少订单信息"));
                                                    }
                                                } else {
                                                    echo json_encode(array("result" => "5", "desc" => "缺少运费信息"));
                                                }
                                            } else {
                                                echo json_encode(array('result' => '6', 'desc' => '缺少中转接收人信息'));
                                            }
                                        } else {
                                            echo json_encode(array('result' => '7', 'desc' => '缺少中转信息'));
                                        }
                                    }else{
                                        echo json_encode(array('result'=>'8','desc'=>'车辆信息不存在'));
                                    }
                                }else{
                                    echo json_encode(array('result'=>'9','desc'=>'缺少车辆信息'));
                                }
                            }else{
                                echo json_encode(array('result'=>'10','desc'=>'城市不存在'));
                            }
                        }else{
                            echo json_encode(array('result'=>'11','desc'=>'缺少收货人城市'));
                        }
                    }else{
                        echo json_encode(array('result'=>'12','desc'=>'缺少收货人信息'));
                    }
                }else{
                    echo json_encode(array('result'=>'13','desc'=>'城市不存在'));
                }
            }else{
                echo json_encode(array('result'=>'14','desc'=>'缺少发货人城市'));
            }
        }else{
            echo json_encode(array('result'=>'15','desc'=>'该租户不存在'));
        }
    }else{
        echo json_encode(array('result'=>'16','desc'=>'缺少租户id'));
    }
});

$app->put('/scheduling',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $database=localhost();
    $tenant_id=$app->request->headers->get("tenant-id");
    $body=$app->request->getBody();
    $body=json_decode($body);
    $scheduling_id=$body->scheduling_id;
    $order_ids=$body->order_ids;
    $array=array();
    foreach($body as $key=>$value){
        if($key!="order_ids"){
            $array[$key]=$value;
        }
    }
    if($tenant_id!=null||$tenant_id!=''){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data6 = $stmt->fetch();
        if($data6!=null){
        if($scheduling_id!=null||$scheduling_id!=""){
            $selectStatement = $database->select()
                ->from('scheduling')
                ->where('scheduling_id','=',$scheduling_id)
                ->where('exist','=',"0")
                ->where('tenant_id','=',$tenant_id);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if($data!=null){
                $selectStatement = $database->select()
                    ->from('tenant')
                    ->where('exist','=',"0")
                    ->where('tenant_id','=',$tenant_id);
                $stmt = $selectStatement->execute();
                $data1 = $stmt->fetch();
                if($data1!=null){
                    $num=count($order_ids);
                    $num1=0;
                    for($i=0;$i<$num;$i++){
                        $selectStatement = $database->select()
                            ->from('orders')
                            ->where('order_id','=',$order_ids[$i])
                            ->where('exist','=',"0")
                            ->where('tenant_id','=',$tenant_id);
                        $stmt = $selectStatement->execute();
                        $data3 = $stmt->fetch();

                        $selectStatement = $database->select()
                            ->from('schedule_order')
                            ->where('order_id','=',$order_ids[$i])
                            ->where('schedule_id','!=',$scheduling_id)
                            ->where('exist','=',"0")
                            ->where('tenant_id','=',$tenant_id);
                        $stmt = $selectStatement->execute();
                        $data4 = $stmt->fetch();
                        if($data3==null){
                            $num1=1;
                            break;
                        }
                        if($data3['order_status']==5){
                            $num1=2;
                            break;
                        }
                        if($data4!=null){
                            $num1=3;
                            break;
                        }
                    }
                    if($num1==0){
                        $updateStatement = $database->update($array)
                            ->table('scheduling')
                            ->where('tenant_id','=',$tenant_id)
                            ->where('exist',"=",0)
                            ->where('scheduling_id','=',$scheduling_id);
                        $affectedRows = $updateStatement->execute();
                        $updateStatement=$database->update(array("exist"=>"1"))
                            ->table('schedule_order')
                            ->where('tenant_id','=',$tenant_id)
                            ->where('exist',"=",0)
                            ->where('schedule_id','=',$scheduling_id);
                        $affectedRows = $updateStatement->execute();
                        for($a=0;$a<$num;$a++){
                            $insertStatement = $database->insert(array('schedule_id','order_id','tenant_id','exist'))
                                ->into('schedule_order')
                                ->values(array($scheduling_id,$order_ids[$a],$tenant_id,'0'));
                            $insertId = $insertStatement->execute(false);
                        }
                        echo json_encode(array("result"=>"0","desc"=>"修改调度信息成功"));
                    }else if($num1==1){
                        echo json_encode(array("result"=>"1","desc"=>"订单不存在"));
                    }else if($num1==2){
                        echo json_encode(array("result"=>"2","desc"=>"订单已经到达"));
                    }else if($num1==3){
                        echo json_encode(array("result"=>"3","desc"=>"订单已经在其他调度信息中"));
                    }
                }else{
                    echo json_encode(array("result"=>"4","desc"=>"车辆4信息不存在"));
                }
            }else{
                echo json_encode(array("result"=>"5","desc"=>"调度表人信息不存在"));
            }
        }else{
            echo json_encode(array("result"=>"6","desc"=>"收货人信息不存在"));
        }
        }else{
            echo json_encode(array("result"=>"7","desc"=>"租户不存在"));
        }
    }else{
        echo json_encode(array('result'=>'8','desc'=>'缺少租户id'));
    }
});

$app->get('/scheduling',function()use($app){
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
        $data6 = $stmt->fetch();
        if($data6!=null){
        if($page==null||$per_page==null){
            $selectStatement = $database->select()
                ->from('scheduling')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            $schedulings=array();
            if($data!=null){
                $num=count($data);
                for($i=0;$i<$num;$i++){
                    $scheduling=array();
                    $scheduling['scheduling_id']=$data[$i]['scheduling_id'];
                    $selectStatement = $database->select()
                        ->from('city')
                        ->where('id','=',$data[$i]['send_city_id']);
                    $stmt = $selectStatement->execute();
                    $data1 = $stmt->fetch();
                    $scheduling['send_city']=$data1['name'];
                    $selectStatement = $database->select()
                        ->from('city')
                        ->where('id','=',$data[$i]['receive_city_id']);
                    $stmt = $selectStatement->execute();
                    $data2 = $stmt->fetch();
                    $scheduling['receive_city']=$data2['name'];
                    $selectStatement = $database->select()
                        ->from('lorry')
                        ->where('tenant_id','=',$tenant_id)
                        ->where('lorry_id','=',$data[$i]['lorry_id'])
                        ->where('exist',"=",0);
                    $stmt = $selectStatement->execute();
                    $data3 = $stmt->fetch();
                    $scheduling['plate_number']=$data3['plate_number'];
                    $scheduling['driver_name']=$data3['driver_name'];
                    $scheduling['driver_phone']=$data3['driver_phone'];
                    array_push($schedulings,$scheduling);
                }
                echo json_encode(array("result"=>"0","desc"=>"success","staff"=>$schedulings));
            }else{
                echo json_encode(array("result"=>"1","desc"=>"该租户下不存在调度记录","staff"=>""));
            }
        }else{
            $selectStatement = $database->select()
                ->from('scheduling')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist',"=",0)
                ->limit((int)$per_page,(int)$per_page*(int)$page);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            $schedulings=array();
            if($data!=null){
                $num=count($data);
                for($i=0;$i<$num;$i++){
                    $scheduling=array();
                    $scheduling['scheduling_id']=$data[$i]['scheduling_id'];
                    $selectStatement = $database->select()
                        ->from('city')
                        ->where('id','=',$data[$i]['send_city_id']);
                    $stmt = $selectStatement->execute();
                    $data1 = $stmt->fetch();
                    $scheduling['send_city']=$data1['name'];
                    $selectStatement = $database->select()
                        ->from('city')
                        ->where('id','=',$data[$i]['receive_city_id']);
                    $stmt = $selectStatement->execute();
                    $data2 = $stmt->fetch();
                    $scheduling['receive_city']=$data2['name'];
                    $selectStatement = $database->select()
                        ->from('lorry')
                        ->where('tenant_id','=',$tenant_id)
                        ->where('lorry_id','=',$data[$i]['lorry_id'])
                        ->where('exist',"=",0);
                    $stmt = $selectStatement->execute();
                    $data3 = $stmt->fetch();
                    $scheduling['plate_number']=$data3['plate_number'];
                    $scheduling['driver_name']=$data3['driver_name'];
                    $scheduling['driver_phone']=$data3['driver_phone'];
                    array_push($schedulings,$scheduling);
                }
                echo json_encode(array("result"=>"0","desc"=>"success","staff"=>$schedulings));
            }else{
                echo json_encode(array("result"=>"1","desc"=>"该租户下不存在调度记录","staff"=>""));
            }
        }
        }else{
            echo json_encode(array("result"=>"2","desc"=>"租户不存在","staff"=>""));
        }
    }else{
        echo json_encode(array("result"=>"3","desc"=>"缺少租户id","staff"=>""));
    }
});


$app->delete('/scheduling',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $database=localhost();
    $scheduling_id=$app->request->get('schedulingid');
    if ($tenant_id!=null||$tenant_id!=''){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data6 = $stmt->fetch();
        if($data6!=null){
        if($scheduling_id!=null||$scheduling_id!=''){
            $selectStatement = $database->select()
                ->from('scheduling')
                ->where('tenant_id','=',$tenant_id)
                ->where('scheduling_id','=',$scheduling_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if($data!=null){
                $selectStatement = $database->select()
                    ->from('schedule_order')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('schedule_id','=',$scheduling_id)
                    ->where('exist',"=",0);
                $stmt = $selectStatement->execute();
                $data1 = $stmt->fetchAll();
                if($data1!=null){
                    $num=count($data1);
                    $num1=0;
                    for($i=0;$i<$num;$i++){
                        $selectStatement = $database->select()
                            ->from('orders')
                            ->where('tenant_id','=',$tenant_id)
                            ->where('order_id','=',$data1[$i]['order_id'])
                            ->where('exist',"=",0);
                        $stmt = $selectStatement->execute();
                        $data2 = $stmt->fetch();
                        if($data2['order_status']!=5&&$data2!=null){
                            $num1=1;
                            break;
                        }
                    }
                    if($num1==0){
                        $updateStatement = $database->update(array('exist'=>1))
                            ->table('scheduling')
                            ->where('tenant_id','=',$tenant_id)
                            ->where('exist',"=",0)
                            ->where('scheduling_id','=',$scheduling_id);
                        $affectedRows = $updateStatement->execute();
                        echo json_encode(array("result"=>"0","desc"=>"success"));
                    }else if($num1==1){
                        echo json_encode(array("result"=>"1","desc"=>"还有运单在调度中"));
                    }
                }else{
                    $updateStatement = $database->update(array('exist'=>1))
                        ->table('scheduling')
                        ->where('tenant_id','=',$tenant_id)
                        ->where('exist',"=",0)
                        ->where('scheduling_id','=',$scheduling_id);
                    $affectedRows = $updateStatement->execute();
                    echo json_encode(array("result"=>"0","desc"=>"success"));
                }
            }else{
                echo json_encode(array("result"=>"1","desc"=>"该租户下调度单不存在"));
            }
        }else{
            echo json_encode(array("result"=>"2","desc"=>"缺少运单id"));
        }
        }else{
            echo json_encode(array("result"=>"3","desc"=>"租户不存在"));
        }
    }else{
        echo json_encode(array("result"=>"3","desc"=>"缺少租户id"));
    }
});


$app->run();
function localhost(){
    return connect();
}
?>