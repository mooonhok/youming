<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/10
 * Time: 9:10
 */
require 'Slim/Slim.php';
require 'connect.php';


\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->post('/agreement',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $database=localhost();
    $tenant_id=$app->request->headers->get("tenant-id");
    $body = $app->request->getBody();
    $body=json_decode($body);
    $firstparty_id=$body->firstparty_id;
    $secondparty_id=$body->secondparty_id;
    $schedule_id=$body->schedule_id;
    $freight=$body->freight;
    $array=array();
    foreach($body as $key=>$value){
        $array[$key]=$value;
    }
    if($tenant_id!=""||$tenant_id!=null){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data6 = $stmt->fetch();
        if($data6!=null){
        if($firstparty_id!=""||$firstparty_id!=null){
            if($secondparty_id>0||$secondparty_id!=null){
                    if ($schedule_id != "" || $schedule_id != null) {
                        if ($freight != "" || $freight != null) {
                            $selectStatement = $database->select()
                                ->from('agreement')
                                ->where('tenant_id', '=', $tenant_id);
                            $stmt = $selectStatement->execute();
                            $data = $stmt->fetchAll();
                            if ($data == null) {
                                $agreement_id = 10000001;
                            } else {
                                $agreement_id = count($data) + 10000001;
                            }
                            $array["agreement_id"] = $agreement_id;
                            $array["tenant_id"] = $tenant_id;
                            $array["exist"] = 0;
                            $selectStatement = $database->select()
                                ->from('tenant')
                                ->where('exist',"=",0)
                                ->where('tenant_id', '=', $firstparty_id);
                            $stmt = $selectStatement->execute();
                            $data1 = $stmt->fetch();
                            if($data1!=null){
                                $selectStatement = $database->select()
                                    ->from('lorry')
                                    ->where('exist','=','0')
                                    ->where('lorry_id', '=', $secondparty_id);
                                $stmt = $selectStatement->execute();
                                $data2 = $stmt->fetch();
                                if($data2!=null){
                                    $selectStatement = $database->select()
                                        ->from('scheduling')
                                        ->where('exist','=','0')
                                        ->where('scheduling_id', '=', $schedule_id);
                                    $stmt = $selectStatement->execute();
                                    $data3 = $stmt->fetch();
                                    if($data3!=null){
                                        $insertStatement = $database->insert(array_keys($array))
                                            ->into('agreement')
                                            ->values(array_values($array));
                                        $insertId = $insertStatement->execute(false);
                                        echo json_encode(array("result" => "0", "desc" => "success"));
                                    }else{
                                        echo json_encode(array("result" => "1", "desc" => "关联调度信息不存在"));
                                    }
                                }else{
                                    echo json_encode(array("result" => "2", "desc" => "关联车辆信息不存在"));
                                }
                            }else{
                                echo json_encode(array("result" => "3", "desc" => "关联客户信息不存在"));
                            }
                        } else {
                            echo json_encode(array("result" => "4", "desc" => "缺少运费信息"));
                        }
                    } else {
                        echo json_encode(array("result" => "5", "desc" => "缺少调度信息"));
                    }
            }else{
                echo json_encode(array("result"=>"6","desc"=>"缺少车辆信息"));
            }

        }else{
            echo json_encode(array("result"=>"7","desc"=>"缺少客户信息"));
        }
        }else{
            echo json_encode(array("result"=>"8","desc"=>"租户信息不存在"));
        }
    }else{
        echo json_encode(array("result"=>"9","desc"=>"缺少租户id"));
    }
});

$app->put('/agreement',function()use($app){
    $app->response->headers->set('Content-type','application/json');
    $tenant_id=$app->request->headers->get('tenant-id');
    $database=localhost();
    $body=$app->request->getBody();
    $body=json_decode($body);
    $firstparty_id=$body->firstparty_id;
    $secondparty_id=$body->secondparty_id;
    $schedule_id=$body->schedule_id;
    $freight=$body->freight;
    $agreement_id=$body->agreement_id;
    $array=array();
    foreach($body as $key=>$value){
        $array[$key]=$value;
    }
    if($tenant_id!=""||$tenant_id!=null){
        if($firstparty_id!=""||$firstparty_id!=null){
            if($secondparty_id>0||$secondparty_id!=null){
                if ($schedule_id != "" || $schedule_id != null) {
                    if ($freight != "" || $freight != null) {
                        if($agreement_id!=""||$agreement_id!=null){
                            $selectStatement = $database->select()
                                ->from('tenant')
                                ->where('exist','=','0')
                                ->where('tenant_id', '=', $tenant_id);
                            $stmt = $selectStatement->execute();
                            $data = $stmt->fetch();
                            if($data!=null) {
                                $selectStatement = $database->select()
                                    ->from('tenant')
                                    ->where('exist','=','0')
                                    ->where('tenant_id', '=', $firstparty_id);
                                $stmt = $selectStatement->execute();
                                $data1 = $stmt->fetch();
                                if($data1!=null){
                                    $selectStatement = $database->select()
                                        ->from('lorry')
                                        ->where('exist','=','0')
                                        ->where('tenant_id','=',$tenant_id)
                                        ->where('lorry_id', '=', $secondparty_id);
                                    $stmt = $selectStatement->execute();
                                    $data1 = $stmt->fetch();
                                    if($data1!=null){
                                        $selectStatement = $database->select()
                                            ->from('scheduling')
                                            ->where('exist','=','0')
                                            ->where('tenant_id','=',$tenant_id)
                                            ->where('scheduling_id', '=', $schedule_id);
                                        $stmt = $selectStatement->execute();
                                        $data2 = $stmt->fetch();
                                        if($data2!=null){
                                            $selectStatement = $database->select()
                                                ->from('schedule_order')
                                                ->where('exist','=','0')
                                                ->where('tenant_id','=',$tenant_id)
                                                ->where('schedule_id', '=', $schedule_id);
                                            $stmt = $selectStatement->execute();
                                            $data3= $stmt->fetch();
                                            if($data3!=null){
                                                $selectStatement = $database->select()
                                                    ->from('agreement')
                                                    ->where('exist','=','0')
                                                    ->where('tenant_id','=',$tenant_id)
                                                    ->where('agreement_id', '=', $agreement_id);
                                                $stmt = $selectStatement->execute();
                                                $data4= $stmt->fetch();
                                                if($data4!=null){
                                                    $updateStatement = $database->update($array)
                                                        ->table('agreement')
                                                        ->where('tenant_id','=',$tenant_id)
                                                        ->where('agreement_id','=',$agreement_id)
                                                        ->where('exist',"=","0");
                                                    $affectedRows = $updateStatement->execute();
                                                    echo json_encode(array("result" => "0", "desc" => "success"));
                                                }else{
                                                    echo json_encode(array("result" => "1", "desc" => "合同不存在"));
                                                }

                                            }else{
                                                echo json_encode(array("result" => "2", "desc" => "调度单上运单不存在"));
                                            }
                                        }else{
                                            echo json_encode(array("result" => "3", "desc" => "调度单不存在"));
                                        }
                                    }else{
                                        echo json_encode(array("result" => "4", "desc" => "车辆不存在"));
                                    }
                                }else{
                                    echo json_encode(array("result" => "5", "desc" => "托运公司不存在"));
                                }
                            }else{
                                echo json_encode(array("result" => "6", "desc" => "该租户不存在"));
                            }
                        }else{
                            echo json_encode(array("result" => "7", "desc" => "缺少合同id"));
                        }
                    } else {
                        echo json_encode(array("result" => "8", "desc" => "缺少运费信息"));
                    }
                } else {
                    echo json_encode(array("result" => "9", "desc" => "缺少调度id"));
                }
            }else{
                echo json_encode(array("result"=>"10","desc"=>"缺少车辆id"));
            }
        }else{
            echo json_encode(array("result"=>"11","desc"=>"缺少托运公司id"));
        }
    }else{
        echo json_encode(array("result"=>"12","desc"=>"缺少租户id"));
    }
});


$app->get('/agreements',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $page=$app->request->get('page');
    $per_page=$app->request->get("per_page");
    $database=localhost();
    if($tenant_id!=null||$tenant_id!="") {
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data6 = $stmt->fetch();
        if($data6!=null){
        if ($page == null || $per_page == null) {
            $selectStatement = $database->select()
                ->from('agreement')
                ->where('tenant_id', '=', $tenant_id)
                ->where('exist', "=", 0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            $agreements = array();
            $num = count($data);
            for ($i = 0; $i < $num; ++$i) {
                $agreement = array();
                $agreement['agreement_id'] = $data[$i]['agreement_id'];
                $selectStatement = $database->select()
                    ->from('scheduling')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('scheduling_id', '=', $data[$i]['schedule_id'])
                    ->where('exist', "=", 0);
                $stmt = $selectStatement->execute();
                $data2 = $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('lorry')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('lorry_id', '=', $data2['lorry_id'])
                    ->where('exist', "=", 0);
                $stmt = $selectStatement->execute();
                $data1 = $stmt->fetch();
                $agreement["plate_number"] = $data1["plate_number"];
                $agreement["driver_name"] = $data1["driver_name"];
                $agreement["driver_phone"] = $data1["driver_phone"];
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id', '=', $data2["receive_city_id"]);
                $stmt = $selectStatement->execute();
                $data3 = $stmt->fetch();
                $agreement["receive_city"] = $data3['name'];
                array_push($agreements, $agreement);
            }
            echo json_encode(array("result" => "0", "desc" => "success", "agreements" => $agreements));
        } else {
            $selectStatement = $database->select()
                ->from('agreement')
                ->where('tenant_id', '=', $tenant_id)
                ->where('exist', "=", 0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            $agreements = array();
            $num = count($data);
            for ($i = 0; $i < $num; ++$i) {
                $agreement = array();
                $agreement['agreement_id'] = $data[$i]['agreement_id'];
                $selectStatement = $database->select()
                    ->from('scheduling')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('scheduling_id', '=', $data[$i]['schedule_id'])
                    ->where('exist', "=", 0);
                $stmt = $selectStatement->execute();
                $data2 = $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('lorry')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('lorry_id', '=', $data2['lorry_id'])
                    ->where('exist', "=", 0);
                $stmt = $selectStatement->execute();
                $data1 = $stmt->fetch();
                $agreement["plate_number"] = $data1["plate_number"];
                $agreement["driver_name"] = $data1["driver_name"];
                $agreement["driver_phone"] = $data1["driver_phone"];
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id', '=', $data2["receive_city_id"]);
                $stmt = $selectStatement->execute();
                $data3 = $stmt->fetch();
                $agreement["receive_city"] = $data3['name'];
                array_push($agreements, $agreement);
            }
            echo json_encode(array("result" => "0", "desc" => "success", "agreements" => $agreements));
        }
        }else{
            echo json_encode(array("result"=>"1","desc"=>"租户不存在","agreements"=>""));
        }
    }else{
        echo json_encode(array("result"=>"2","desc"=>"缺少租户id","agreements"=>""));
    }
});

$app->get("/agreement",function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant-id');
    $agreement_id=$app->request->get("agreementid");
    $database=localhost();
    if($tenant_id!=null||$tenant_id!=""){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data12 = $stmt->fetch();
        if($data12!=null){
        if($agreement_id!=null||$agreement_id!=""){
            $selectStatement = $database->select()
                ->from('agreement')
                ->where('tenant_id','=',$tenant_id)
                ->where('agreement_id','=',$agreement_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if ($data!=null) {
                $agreement = array();
                $agreement['agreement_id'] = $data['agreement_id'];
                $selectStatement = $database->select()
                    ->from('lorry')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('lorry_id', '=', $data['secondparty_id'])
                    ->where('exist', "=", 0);
                $stmt = $selectStatement->execute();
                $data1 = $stmt->fetch();
                $lorry = array();
                $lorry['lorry_id'] = $data1['lorry_id'];
                $lorry['plate_number'] = $data1['plate_number'];
                $lorry['driver_name'] = $data1['driver_name'];
                $lorry['driver_phone'] = $data1['driver_phone'];
                $lorry['driver_identycard'] = $data1['driver_identycard'];
                $lorry['driving_license'] = $data1['driving_license'];
                $lorry['vehicle_travel_license'] = $data1['vehicle_travel_license'];
                $agreement['lorry'] = $lorry;
                $selectStatement = $database->select()
                    ->from('schedule_order')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('schedule_id', '=', $data['schedule_id'])
                    ->where('exist', "=", 0);
                $stmt = $selectStatement->execute();
                $data2 = $stmt->fetchAll();
                $num1 = count($data2);
                $goods = array();
                for ($k = 0; $k < $num1; $k++) {
                    $selectStatement = $database->select()
                        ->from('goods')
                        ->where('tenant_id', '=', $tenant_id)
                        ->where('order_id', '=', $data2[$k]['order_id'])
                        ->where('exist', "=", 0);
                    $stmt = $selectStatement->execute();
                    $data4 = $stmt->fetch();
                    array_push($goods, $data4);
                }
                $agreement['goods_list'] = $goods;
                $selectStatement = $database->select()
                    ->from('scheduling')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('scheduling_id', '=', $data['schedule_id'])
                    ->where('exist', "=", 0);
                $stmt = $selectStatement->execute();
                $data5 = $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id', "=", $data5['send_city_id']);
                $stmt = $selectStatement->execute();
                $data8 = $stmt->fetch();
                $agreement['send_city'] = $data8['name'];
                $selectStatement = $database->select()
                    ->from('tenant')
                    ->where('tenant_id', '=', $data5['receiver_id'])
                    ->where('exist', "=", 0);
                $stmt = $selectStatement->execute();
                $data10 = $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('customer')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('customer_id', '=', $data10['contact_id'])
                    ->where('exist', "=", 0);
                $stmt = $selectStatement->execute();
                $data6 = $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id', "=", $data6['customer_city_id']);
                $stmt = $selectStatement->execute();
                $data9 = $stmt->fetch();
                $agreement['receive_city'] = $data9['name'];
                $agreement['receiver_name'] = $data6['customer_name'];
                $agreement['receiver_phone'] = $data6['customer_phone'];
                $agreement['receiver_address'] = $data6['customer_address'];
                $selectStatement = $database->select()
                    ->from('tenant')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('exist', "=", 0);
                $stmt = $selectStatement->execute();
                $data7 = $stmt->fetch();
                $agreement['receive_company'] = $data7['company'];
                echo json_encode(array("result" => "0", "desc" => "success", "agreement" => $agreement));
            }else{
                echo json_encode(array("result"=>"1","desc"=>"该租户下该合同不存在","agreement"=>""));
            }
        }else{
            echo json_encode(array("result"=>"1","desc"=>"缺少合同id","agreement"=>""));
        }
        }else{
            echo json_encode(array("result"=>"1","desc"=>"租户不存在","agreement"=>""));
        }
    }else{
        echo json_encode(array("result"=>"2","desc"=>"缺少租户id","agreement"=>""));
    }
});



$app->delete('/agreement',function()use($app){
    $app->response->headers->set('Content-type','application/json');
    $tenant_id=$app->request->headers->get('tenant-id');
    $database=localhost();
    $agreement_id=$app->request->get('agreementid');
    if($tenant_id!=null||$tenant_id!=""){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist',"=",0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data6 = $stmt->fetch();
        if($data6!=null){
        if($agreement_id!=null||$agreement_id!=""){
            $selectStatement = $database->select()
                ->from('agreement')
                ->where('tenant_id','=',$tenant_id)
                ->where('agreement_id','=',$agreement_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if($data!=null){
                $selectStatement = $database->select()
                    ->from('schedule_order')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('schedule_id','=',$data['schedule_id'])
                    ->where('exist',"=",0);
                $stmt = $selectStatement->execute();
                $data1 = $stmt->fetch();
                if($data1==null){
                    $updateStatement = $database->update(array('exist'=>1))
                        ->table('agreement')
                        ->where('tenant_id','=',$tenant_id)
                        ->where('agreement_id','=',$agreement_id)
                        ->where('exist',"=",0);
                    $affectedRows = $updateStatement->execute();
                    echo json_encode(array("result"=>"0","desc"=>"success"));
                }else{
                    $num = count($data1);
                    $num1=0;
                    for($i=0;$i<$num;++$i){
                        $selectStatement = $database->select()
                            ->from('orders')
                            ->where('tenant_id','=',$tenant_id)
                            ->where('order_id','=',$data1['order_id'])
                            ->where('exist',"=",0);
                        $stmt = $selectStatement->execute();
                        $data2= $stmt->fetch();
                        if(!($data2['order_status']==5)){
                             $num1=1;
                             break;
                        }
                    }
                    if($num1==0){
                        $updateStatement = $database->update(array('exist'=>1))
                            ->table('agreement')
                            ->where('tenant_id','=',$tenant_id)
                            ->where('agreement_id','=',$agreement_id)
                            ->where('exist',"=",0);
                        $affectedRows = $updateStatement->execute();
                        echo json_encode(array("result"=>"0","desc"=>"success"));
                    }else{
                        echo json_encode(array("result"=>"1","desc"=>"有订单未完结"));
                    }
                }
            }else{
                echo json_encode(array("result"=>"2","desc"=>"合同不存在"));
            }
        }else{
            echo json_encode(array("result"=>"3",'desc'=>'缺少合同id'));
        }
        }else{
            echo json_encode(array("result"=>"4","desc"=>"租户信息不存在"));
        }
    }else{
        echo json_encode(array("result"=>"5",'desc'=>'缺少租户id'));
    }
});


$app->run();

function localhost(){
    return connect();
}
?>