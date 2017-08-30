<?php
require 'Slim/Slim.php';
require 'connect.php';

use Slim\PDO\Database;
use Slim\PDO\Statement;
use Slim\PDO\Statement\SelectStatement;

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
//微信添加订单
//$app->post('/wx_order_n', function () use ($app) {
//    $app->response->headers->set('Content-Type', 'application/json');
//    $tenant_id = $app->request->headers->get("tenant-id");
//    $orderid = $app->request->get("orderid");
//    $database = localhost();
//
//});



$app->delete('/order', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $orderid = $app->request->get("orderid");
    $database = localhost();
//云端客户的id和订单id都不为空
    if ($tenant_id != null || $tenant_id != "") {
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data4 = $stmt->fetch();
        if ($data4 != null) {
            if ($orderid != null || $orderid != "") {
                $selectStatement = $database->select()
                    ->from('orders')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('order_id', '=', $orderid);
                $stmt = $selectStatement->execute();
                $data = $stmt->fetch();
                if ($data["order_status"] == 3 || $data["order_status"] == 2 || $data["order_status"] == 4) {
                    $updateStatement = $database->update(array('exist' => '1'))
                        ->table('orders')
                        ->where('tenant_id', '=', $tenant_id)
                        ->where('id', '=', $orderid);
                    $affectedRows = $updateStatement->execute();
                    if ($affectedRows > 0) {
                        echo json_encode(array("result" => "0", "desc" => "success"));
                    } else {
                        echo json_encode(array("result" => "1", "desc" => "记录不存在"));
                    }
                } else {
                    echo json_encode(array("result" => "2", "desc" => "记录不可以删除"));
                }
            } else {
                echo json_encode(array("result" => "3", "desc" => "缺少订单id"));
            }
        } else {
            echo json_encode(array("result" => "4", "desc" => "租户不存在"));
        }
    } else {
        echo json_encode(array("result" => "5", "desc" => "缺少租户id"));
    }
});


$app->post('/order', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $database = localhost();
    $body = $app->request->getBody();
    $body = json_decode($body);
    $sender_id = $body->sender_id;
    $receiver_id = $body->receiver_id;
    $order_cost = $body->order_cost;
    $pay_method = $body->pay_method;
    $order_status = $body->order_status;
    $order_datetime = $body->order_datetime;
    $selectStatement = $database->select()
        ->from('orders')
        ->where('tenant_id', '=', $tenant_id);
    $stmt = $selectStatement->execute();
    $data = $stmt->fetchAll();

    if ($data == null) {
        $order_id = 100000001;
    } else {
        $order_id = count($data) + 100000001;
    }
    if ($sender_id != null || $sender_id != "") {
        if ($receiver_id != null || $receiver_id > 0) {
            if ($order_cost != null || $order_cost != "") {
                if ($pay_method != null || $pay_method != "") {
                    if ($order_status != null || $order_status != "") {
                        if ($order_datetime != null || $order_datetime != "") {
                            if ($tenant_id != null || $tenant_id != "") {
                                $selectStatement = $database->select()
                                    ->from('tenant')
                                    ->where('exist', "=", 0)
                                    ->where('tenant_id', '=', $tenant_id);
                                $stmt = $selectStatement->execute();
                                $data4= $stmt->fetch();
                                if ($data4 != null) {
                                    $selectStatement = $database->select()
                                        ->from('customer')
                                        ->where('customer_id', '=', $sender_id)
                                        ->where('tenant_id', '=', $tenant_id)
                                        ->where('exist', "=", 0);
                                    $stmt = $selectStatement->execute();
                                    $data1 = $stmt->fetch();
                                    $selectStatement = $database->select()
                                        ->from('customer')
                                        ->where('customer_id', '=', $receiver_id)
                                        ->where('tenant_id', '=', $tenant_id)
                                        ->where('exist', "=", 0);
                                    $stmt = $selectStatement->execute();
                                    $data2 = $stmt->fetch();
                                    if ($data1 != null && $data2 != null) {
                                        $selectStatement=$database->select()
                                            ->from('inventory_location')
                                            ->where('tenant_id','=',$tenant_id);
                                            $stmt=$selectStatement->execute();
                                            $data3=$stmt->fetch();
                                            $sum=count($data3)+1;
                                        $insertStatement = $database->insert(array('order_id', 'sender_id', 'receiver_id', 'pay_method', 'order_cost', 'order_status', 'order_datetime', 'tenant_id', 'exist',
                                            'inventory_loc_id','inventory_type'))
                                            ->into('orders')
                                            ->values(array($order_id, $sender_id, $receiver_id, $pay_method, $order_cost, $order_status, $order_datetime, $tenant_id, 0,$sum,2));

                                        $insertId = $insertStatement->execute(false);
                                        echo json_encode(array("result" => "0", "desc" => "success"));
                                    } else {
                                        echo json_encode(array("result" => "1", "desc" => "发货人或者收货人不存在"));
                                    }
                                } else {
                                    echo json_encode(array("result" => "2", "desc" => "租户不存在"));
                                }
                            } else {
                                echo json_encode(array("result" => "3", "desc" => "缺少租户id"));
                            }
                        } else {
                            echo json_encode(array("result" => "4", "desc" => "缺少运单生成时间"));
                        }
                    } else {
                        echo json_encode(array("result" => "5", "desc" => "缺少订单状态"));
                    }
                } else {
                    echo json_encode(array("result" => "6", "desc" => "缺少付款方式"));
                }
            } else {
                echo json_encode(array("result" => "7", "desc" => "缺少订单金额"));
            }
        } else {
            echo json_encode(array("result" => "8", "desc" => "缺少收货人"));
        }
    } else {
        echo json_encode(array("result" => "9", "desc" => "缺少发货人"));
    }
});


$app->put("/order", function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $database = localhost();
    $body = $app->request->getBody();
    $body = json_decode($body);
    $order_id = $body->order_id;
    $array = array();
    foreach ($body as $key => $value) {
        if ($key != "order_id" && ($value != null || $value != "" || $value > 0)) {
            $array[$key] = $value;
        }
        if ($key == "sender_id") {
            $selectStatement = $database->select()
                ->from('customer')
                ->where('customer_id', '=', $value)
                ->where('tenant_id', '=', $tenant_id)
                ->where('exist', "=", 0);
            $stmt = $selectStatement->execute();
            $data1 = $stmt->fetch();
        }
        if ($key == "receiver_id") {
            $selectStatement = $database->select()
                ->from('customer')
                ->where('customer_id', '=', $value)
                ->where('tenant_id', '=', $tenant_id)
                ->where('exist', "=", 0);
            $stmt = $selectStatement->execute();
            $data2 = $stmt->fetch();
        }
    }
    if($tenant_id != null || $tenant_id != ""){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data4= $stmt->fetch();
        if ($data4 != null) {
            $selectStatement = $database->select()
                ->from('orders')
                ->where('order_id', '=', $order_id)
                ->where('tenant_id', '=', $tenant_id);
            $stmt = $selectStatement->execute();
            $data5 = $stmt->fetchAll();
            if($data5!=null){
                if( $data1 != null){
                    if($data2 != null){
                        if (count($array) > 0  ) {
                            $updateStatement = $database->update($array)
                                ->table('orders')
                                ->where('tenant_id', '=', $tenant_id)
                                ->where('order_id', '=', $order_id);
                            $affectedRows = $updateStatement->execute();
                            echo json_encode(array("result" => "0", "desc" => "success"));
                        }else{
                            echo json_encode(array("result" => "1", "desc" => "数据不全"));
                        }
                    } else {
                        echo json_encode(array("result" => "2", "desc" => "收货人不存在"));
                    }
                }else{
                    echo json_encode(array("result" => "3", "desc" => "发货人不存在"));
                }
            }else{
                echo json_encode(array("result" => "4", "desc" => "该租户下订单不存在"));
            }
        }else{
            echo json_encode(array("result" => "5", "desc" => "租户不存在"));
        }
    }else{
        echo json_encode(array("result" => "6", "desc" => "缺少租户id"));
    }

});


$app->get('/orders', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $page = $app->request->get('page');
    $per_page = $app->request->get("per_page");
    $database = localhost();
    if ($tenant_id != null || $tenant_id != "") {
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data4= $stmt->fetch();
        if ($data4 != null) {
        if ($page == null || $per_page == null) {
            $selectStatement = $database->select()
                ->from('orders')
                ->where('tenant_id', '=', $tenant_id)
                ->where('exist', "=", 0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            echo json_encode(array("result" => "0", "desc" => "success", "orders" => $data));
        } else {
            $selectStatement = $database->select()
                ->from('orders')
                ->where('tenant_id', '=', $tenant_id)
                ->where('exist', "=", 0)
                ->limit((int)$per_page, (int)$per_page * (int)$page);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            echo json_encode(array("result" => "0", "desc" => "success", "orders" => $data));
        }
        } else {
            echo json_encode(array("result" => "1", "desc" => "租户不存在", "orders" => ""));
        }
    } else {
        echo json_encode(array("result" => "2", "desc" => "缺少租户id", "orders" => ""));
    }

});


$app->get('/order', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $order_id = $app->request->get('orderid');
    $database = localhost();
    if ($tenant_id != null || $tenant_id != "") {
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data4= $stmt->fetch();
        if ($data4 != null) {
        if ($order_id != null || $order_id != "") {
            $selectStatement = $database->select()
                ->from('orders')
                ->where('exist', "=", 0)
                ->where('order_id', '=',$order_id);
            $stmt = $selectStatement->execute();
            $data5= $stmt->fetch();
            if ($data5 != null) {
            $selectStatement = $database->select()
                ->from('orders')
                ->where('tenant_id', '=', $tenant_id)
                ->where('order_id', '=', $order_id)
                ->where('exist', "=", 0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            echo json_encode(array("result" => "0", "desc" => "success", "order" => $data));
            } else {
                echo json_encode(array("result" => "1", "desc" => "订单不存在", "order" => ""));
            }
        } else {
            echo json_encode(array("result" => "2", "desc" => "缺少运单id", "order" => ""));
        }
        } else {
            echo json_encode(array("result" => "3", "desc" => "租户不存在", "order" => ""));
        }
    } else {
        echo json_encode(array("result" => "4", "desc" => "缺少租户id", "order" => ""));
    }
});

//微信通过货主的openid，货主为发货方，获得订单总数
$app->post('/wx_orders_s', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $body = $app->request->getBody();
    $body = json_decode($body);
    $order_id = $body->order_id;
    $wx_openid=$body->wx_openid;
    $database = localhost();
    if ($tenant_id != null || $tenant_id != "") {
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data1= $stmt->fetch();
        if ($data1 != null) {
            $selectStatement = $database->select()
                ->from('customer')
                ->where('exist', "=", 0)
                ->where('wx_openid','=',$wx_openid)
                ->where('tenant_id', '=', $tenant_id);
            $stmt = $selectStatement->execute();
            $data2= $stmt->fetch();
            if($data2==null){
                echo json_encode(array("result" => "0", "desc" => "用户不存在", "orders" => ""));
            }else{
                if($order_id==null){
                    $selectStatement = $database->select()
                        ->from('orders')
                        ->where('exist', "=", 0)
                        ->where('sender_id','=',$data2['customer_id'])
                        ->where('tenant_id', '=', $tenant_id);
                    $stmt = $selectStatement->execute();
                    $data3= $stmt->fetchAll();
                    $selectStatement = $database->select()
                        ->from('customer')
                        ->where('exist', "=", 0)
                        ->where('customer_id','=',$data2['customer_id'])
                        ->where('tenant_id', '=', $tenant_id);
                    $stmt = $selectStatement->execute();
                    $data6= $stmt->fetch();
                    $selectStatement = $database->select()
                        ->from('city')
                        ->where('id', '=', $data6['customer_city_id']);
                    $stmt = $selectStatement->execute();
                    $data7= $stmt->fetch();
                    $num1=count($data3);
                    $array=array();
                    for($i=0;$i<$num1;$i++){
                        $array1=array();
                        $array1['sendcity']=$data7['name'];
                        $array1['sendname']=$data6['customer_name'];
                        $array1['order_id']=$data3[$i]['order_id'];
                        $array1['status']=$data3[$i]['order_status'];
                        $array1['order_cost']=$data3[$i]['order_cost'];
                        if($array1['status']==0&&$array1['order_cost']==null){
                            $array1['order_cost']=='受理中';
                            $array1['receive']='未签收';
                            $array1['status']='受理中';
                        }else if($array1['status']==1){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==2){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==3){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==4){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==5){
                            $array1['receive']='签收时间'.$data3[$i]['order_datetime5'];
                            $array1['status']='已签收';
                        }else if($array1['status']==-1){
                            $array1['order_cost']=='拒受理';
                            $array1['receive']='拒受理';
                            $array1['status']='拒受理';
                        }else if($array1['status']==-2){
                            $array1['order_cost']=='未受理';
                            $array1['receive']='未受理';
                            $array1['status']='未受理';
                        }else if($array1['status']==0&&$array1['order_cost']!=null){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('exist', "=", 0)
                            ->where('customer_id','=',$data3[$i]['receiver_id'])
                            ->where('tenant_id', '=', $tenant_id);
                        $stmt = $selectStatement->execute();
                        $data4= $stmt->fetch();
                        $array1['acceptname']=$data4['customer_name'];

                        $selectStatement = $database->select()
                            ->from('city')
                            ->where('id', '=', $data4['customer_city_id']);
                        $stmt = $selectStatement->execute();
                        $data5= $stmt->fetch();
                        $array1['acceptcity']=$data5['name'];
                        array_push($array,$array1);
                    }
                    echo json_encode(array("result" => "1", "desc" => "success", "orders" => $array));
                }else{
                    $array=array();
                    $array1=array();
                    $selectStatement = $database->select()
                        ->from('orders')
                        ->where('exist', "=", 0)
                        ->where('order_id','=',$order_id)
                        ->where('tenant_id', '=', $tenant_id);
                    $stmt = $selectStatement->execute();
                    $data3= $stmt->fetch();
                    if($data3!=null){
                        $array1['status']=$data3['order_status'];
                        $array1['order_cost']=$data3['order_cost'];
                        if($array1['status']==0&&$array1['order_cost']==null){
                            $array1['order_cost']=='受理中';
                            $array1['receive']='未签收';
                            $array1['status']='受理中';
                        }else if($array1['status']==1){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==2){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==3){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==4){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==5){
                            $array1['receive']='签收时间'.$data3['order_datetime5'];
                            $array1['status']='已签收';
                        }else if($array1['status']==-1){
                            $array1['order_cost']=='拒受理';
                            $array1['receive']='拒受理';
                            $array1['status']='拒受理';
                        }else if($array1['status']==-2){
                            $array1['order_cost']=='未受理';
                            $array1['receive']='未受理';
                            $array1['status']='未受理';
                        }else if($array1['status']==0&&$array1['order_cost']!=null){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }

                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('exist', "=", 0)
                            ->where('customer_id','=',$data3['receiver_id'])
                            ->where('tenant_id', '=', $tenant_id);
                        $stmt = $selectStatement->execute();
                        $data4= $stmt->fetch();
                        $array1['acceptname']=$data4['customer_name'];
                        $selectStatement = $database->select()
                            ->from('city')
                            ->where('id', '=', $data4['customer_city_id']);
                        $stmt = $selectStatement->execute();
                        $data5= $stmt->fetch();
                        $array1['acceptcity']=$data5['name'];
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('exist', "=", 0)
                            ->where('customer_id','=',$data3['sender_id'])
                            ->where('tenant_id', '=', $tenant_id);
                        $stmt = $selectStatement->execute();
                        $data6= $stmt->fetch();
                        $array1['sendname']=$data6['customer_name'];
                        $selectStatement = $database->select()
                            ->from('city')
                            ->where('id', '=', $data4['customer_city_id']);
                        $stmt = $selectStatement->execute();
                        $data7= $stmt->fetch();
                        $array1['sendcity']=$data7['name'];
                        array_push($array,$array1);
                        echo json_encode(array("result" => "1", "desc" => "ji", "orders" => $array));
                    }else{
                        echo json_encode(array("result" => "1", "desc" => "ji", "orders" => $array));
                    }
                }
            }
        } else {
            echo json_encode(array("result" => "2", "desc" => "租户不存在", "orders" => ""));
        }
    } else {
        echo json_encode(array("result" => "3", "desc" => "缺少租户id", "orders" => ""));
    }
});


//微信通过货主的openid，货主为收货方，获得订单总数
$app->post('/wx_orders_r', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $body = $app->request->getBody();
    $body = json_decode($body);
    $order_id = $body->order_id;
    $wx_openid=$body->wx_openid;
    $database = localhost();
    if ($tenant_id != null || $tenant_id != "") {
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data1= $stmt->fetch();
        if ($data1 != null) {
            $selectStatement = $database->select()
                ->from('customer')
                ->where('exist', "=", 0)
                ->where('wx_openid','=',$wx_openid)
                ->where('tenant_id', '=', $tenant_id);
            $stmt = $selectStatement->execute();
            $data2= $stmt->fetch();
            if($data2==null){
                echo json_encode(array("result" => "0", "desc" => "用户不存在", "orders" => ""));
            }else{
                if($order_id==null){
                    $selectStatement = $database->select()
                        ->from('orders')
                        ->where('exist', "=", 0)
                        ->where('receiver_id','=',$data2['customer_id'])
                        ->where('tenant_id', '=', $tenant_id);
                    $stmt = $selectStatement->execute();
                    $data3= $stmt->fetchAll();
                    $selectStatement = $database->select()
                        ->from('customer')
                        ->where('exist', "=", 0)
                        ->where('customer_id','=',$data2['customer_id'])
                        ->where('tenant_id', '=', $tenant_id);
                    $stmt = $selectStatement->execute();
                    $data6= $stmt->fetch();
                    $selectStatement = $database->select()
                        ->from('city')
                        ->where('id', '=', $data6['customer_city_id']);
                    $stmt = $selectStatement->execute();
                    $data7= $stmt->fetch();
                    $num1=count($data3);
                    $array=array();
                    for($i=0;$i<$num1;$i++){
                        $array1=array();
                        $array1['acceptcity']=$data7['name'];
                        $array1['acceptname']=$data6['customer_name'];
                        $array1['order_id']=$data3[$i]['order_id'];
                        $array1['status']=$data3[$i]['order_status'];
                        $array1['order_cost']=$data3[$i]['order_cost'];
                        if($array1['status']==0&&$array1['order_cost']==null){
                            $array1['order_cost']=='受理中';
                            $array1['receive']='未签收';
                            $array1['status']='受理中';
                        }else if($array1['status']==1){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==2){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==3){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==4){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==5){
                            $array1['receive']='签收时间'.$data3[$i]['order_datetime5'];
                            $array1['status']='已签收';
                        }else if($array1['status']==-1){
                            $array1['order_cost']=='拒受理';
                            $array1['receive']='拒受理';
                            $array1['status']='拒受理';
                        }else if($array1['status']==-2){
                            $array1['order_cost']=='未受理';
                            $array1['receive']='未受理';
                            $array1['status']='未受理';
                        }else if($array1['status']==0&&$array1['order_cost']!=null){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('exist', "=", 0)
                            ->where('customer_id','=',$data3[$i]['sender_id'])
                            ->where('tenant_id', '=', $tenant_id);
                        $stmt = $selectStatement->execute();
                        $data4= $stmt->fetch();
                        $array1['sendname']=$data4['customer_name'];

                        $selectStatement = $database->select()
                            ->from('city')
                            ->where('id', '=', $data4['customer_city_id']);
                        $stmt = $selectStatement->execute();
                        $data5= $stmt->fetch();
                        $array1['sendcity']=$data5['name'];
                        array_push($array,$array1);
                    }
                    echo json_encode(array("result" => "1", "desc" => "success", "orders" => $array));
                }else{
                    $array=array();
                    $array1=array();
                    $selectStatement = $database->select()
                        ->from('orders')
                        ->where('exist', "=", 0)
                        ->where('order_id','=',$order_id)
                        ->where('tenant_id', '=', $tenant_id);
                    $stmt = $selectStatement->execute();
                    $data3= $stmt->fetch();
                    if($data3!=null){
                        $array1['status']=$data3['order_status'];
                        $array1['order_cost']=$data3['order_cost'];
                        if($array1['status']==0&&$array1['order_cost']==null){
                            $array1['order_cost']=='受理中';
                            $array1['receive']='未签收';
                            $array1['status']='受理中';
                        }else if($array1['status']==1){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==2){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==3){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==4){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==5){
                            $array1['receive']='签收时间'.$data3['order_datetime5'];
                            $array1['status']='已签收';
                        }else if($array1['status']==-1){
                            $array1['order_cost']=='拒受理';
                            $array1['receive']='拒受理';
                            $array1['status']='拒受理';
                        }else if($array1['status']==-2){
                            $array1['order_cost']=='未受理';
                            $array1['receive']='未受理';
                            $array1['status']='未受理';
                        }else if($array1['status']==0&&$array1['order_cost']!=null){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('exist', "=", 0)
                            ->where('customer_id','=',$data3['receiver_id'])
                            ->where('tenant_id', '=', $tenant_id);
                        $stmt = $selectStatement->execute();
                        $data4= $stmt->fetch();
                        $array1['acceptname']=$data4['customer_name'];
                        $selectStatement = $database->select()
                            ->from('city')
                            ->where('id', '=', $data4['customer_city_id']);
                        $stmt = $selectStatement->execute();
                        $data5= $stmt->fetch();
                        $array1['acceptcity']=$data5['name'];
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('exist', "=", 0)
                            ->where('customer_id','=',$data3['sender_id'])
                            ->where('tenant_id', '=', $tenant_id);
                        $stmt = $selectStatement->execute();
                        $data6= $stmt->fetch();
                        $array1['sendname']=$data6['customer_name'];
                        $selectStatement = $database->select()
                            ->from('city')
                            ->where('id', '=', $data4['customer_city_id']);
                        $stmt = $selectStatement->execute();
                        $data7= $stmt->fetch();
                        $array1['sendcity']=$data7['name'];
                        array_push($array,$array1);
                        echo json_encode(array("result" => "1", "desc" => "success", "orders" => $array));
                    }else{
                        echo json_encode(array("result" => "2", "desc" => "", "orders" => $array));
                    }

                }
            }
        } else {
            echo json_encode(array("result" => "3", "desc" => "租户不存在", "orders" => ""));
        }
    } else {
        echo json_encode(array("result" => "4", "desc" => "缺少租户id", "orders" => ""));
    }
});


//根据订单order_id和wx_openid查出对应的订单
$app->post('/wx_order', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $body = $app->request->getBody();
    $body = json_decode($body);
    $order_id = $body->order_id;
    $wx_openid=$body->wx_openid;
    $database = localhost();
    if ($tenant_id != null || $tenant_id != "") {
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data1= $stmt->fetch();
        if ($data1 != null) {
            $selectStatement = $database->select()
                ->from('customer')
                ->where('exist', "=", 0)
                ->where('wx_openid','=',$wx_openid)
                ->where('tenant_id', '=', $tenant_id);
            $stmt = $selectStatement->execute();
            $data2= $stmt->fetch();
            if($data2==null){
                echo json_encode(array("result" => "0", "desc" => "用户不存在", "orders" => ""));
            }else{
                if($order_id==null){
                    $selectStatement = $database->select()
                        ->from('orders')
                        ->where('exist', "=", 0)
                        ->where('receiver_id','=',$data2['customer_id'])
                        ->where('tenant_id', '=', $tenant_id);
                    $stmt = $selectStatement->execute();
                    $data3= $stmt->fetchAll();
                    $selectStatement = $database->select()
                        ->from('customer')
                        ->where('exist', "=", 0)
                        ->where('customer_id','=',$data2['customer_id'])
                        ->where('tenant_id', '=', $tenant_id);
                    $stmt = $selectStatement->execute();
                    $data6= $stmt->fetch();
                    $selectStatement = $database->select()
                        ->from('city')
                        ->where('id', '=', $data6['customer_city_id']);
                    $stmt = $selectStatement->execute();
                    $data7= $stmt->fetch();
                    $num1=count($data3);
                    $array=array();
                    for($i=0;$i<$num1;$i++){
                        $array1=array();
                        $array1['acceptcity']=$data7['name'];
                        $array1['acceptname']=$data6['customer_name'];
                        $array1['order_id']=$data3[$i]['order_id'];
                        $array1['status']=$data3[$i]['order_status'];
                        $array1['order_cost']=$data3[$i]['order_cost'];
                        if($array1['status']==0&&$array1['order_cost']==null){
                            $array1['order_cost']=='受理中';
                            $array1['receive']='未签收';
                            $array1['status']='受理中';
                        }else if($array1['status']==1){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==2){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==3){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==4){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==5){
                            $array1['receive']='签收时间'.$data3[$i]['order_datetime5'];
                            $array1['status']='已签收';
                        }else if($array1['status']==-1){
                            $array1['order_cost']=='拒受理';
                            $array1['receive']='拒受理';
                            $array1['status']='拒受理';
                        }else if($array1['status']==-2){
                            $array1['order_cost']=='未受理';
                            $array1['receive']='未受理';
                            $array1['status']='未受理';
                        }else if($array1['status']==0&&$array1['order_cost']!=null){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('exist', "=", 0)
                            ->where('customer_id','=',$data3[$i]['sender_id'])
                            ->where('tenant_id', '=', $tenant_id);
                        $stmt = $selectStatement->execute();
                        $data4= $stmt->fetch();
                        $array1['sendname']=$data4['customer_name'];

                        $selectStatement = $database->select()
                            ->from('city')
                            ->where('id', '=', $data4['customer_city_id']);
                        $stmt = $selectStatement->execute();
                        $data5= $stmt->fetch();
                        $array1['sendcity']=$data5['name'];
                        array_push($array,$array1);
                    }
                    echo json_encode(array("result" => "1", "desc" => "success", "orders" => $array));
                }else{
                    $array=array();
                    $array1=array();
                    $selectStatement = $database->select()
                        ->from('orders')
                        ->where('exist', "=", 0)
                        ->where('order_id','=',$order_id)
                        ->where('tenant_id', '=', $tenant_id);
                    $stmt = $selectStatement->execute();
                    $data3= $stmt->fetch();
                    if($data3!=null){
                        $array1['status']=$data3['order_status'];
                        $array1['order_cost']=$data3['order_cost'];
                        if($array1['status']==0&&$array1['order_cost']==null){
                            $array1['order_cost']=='受理中';
                            $array1['receive']='未签收';
                            $array1['status']='受理中';
                        }else if($array1['status']==1){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==2){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==3){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==4){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }else if($array1['status']==5){
                            $array1['receive']='签收时间'.$data3['order_datetime5'];
                            $array1['status']='已签收';
                        }else if($array1['status']==-1){
                            $array1['order_cost']=='拒受理';
                            $array1['receive']='拒受理';
                            $array1['status']='拒受理';
                        }else if($array1['status']==-2){
                            $array1['order_cost']=='未受理';
                            $array1['receive']='未受理';
                            $array1['status']='未受理';
                        }else if($array1['status']==0&&$array1['order_cost']!=null){
                            $array1['receive']='未签收';
                            $array1['status']='未签收';
                        }
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('exist', "=", 0)
                            ->where('customer_id','=',$data3['receiver_id'])
                            ->where('tenant_id', '=', $tenant_id);
                        $stmt = $selectStatement->execute();
                        $data4= $stmt->fetch();
                        if($data4['wx_openid']==$wx_openid){
                          $array1['fashou']=1;
                        }
                        $array1['acceptname']=$data4['customer_name'];
                        $selectStatement = $database->select()
                            ->from('city')
                            ->where('id', '=', $data4['customer_city_id']);
                        $stmt = $selectStatement->execute();
                        $data5= $stmt->fetch();
                        $array1['acceptcity']=$data5['name'];
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('exist', "=", 0)
                            ->where('customer_id','=',$data3['sender_id'])
                            ->where('tenant_id', '=', $tenant_id);
                        $stmt = $selectStatement->execute();
                        $data6= $stmt->fetch();
                        if($data6['wx_openid']==$wx_openid){
                            $array1['fashou']=2;
                        }
                        $array1['sendname']=$data6['customer_name'];
                        $selectStatement = $database->select()
                            ->from('city')
                            ->where('id', '=', $data4['customer_city_id']);
                        $stmt = $selectStatement->execute();
                        $data7= $stmt->fetch();
                        $array1['sendcity']=$data7['name'];
                        array_push($array,$array1);
                        echo json_encode(array("result" => "1", "desc" => "", "orders" => $array));
                    }else{
                        echo json_encode(array("result" => "1", "desc" => "", "orders" => $array));
                    }
                }
            }
        } else {
            echo json_encode(array("result" => "2", "desc" => "租户不存在", "orders" => ""));
        }
    } else {
        echo json_encode(array("result" => "3", "desc" => "缺少租户id", "orders" => ""));
    }
});


//根据订单order_id查出对应的订单详细信息
$app->post('/wx_order_z', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $body = $app->request->getBody();
    $body = json_decode($body);
    $order_id = $body->order_id;
//    $order_id = $app->request->get("order_id");
    $database = localhost();
    $array=array();
    if ($tenant_id != null || $tenant_id != "") {
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data1= $stmt->fetch();
        if ($data1 != null) {
            $selectStatement = $database->select()
                ->from('orders')
                ->where('exist', "=", 0)
                ->where('tenant_id','=',$tenant_id)
                ->where('order_id','=',$order_id);
            $stmt = $selectStatement->execute();
            $data2= $stmt->fetch();
            if($data2!=null){
                $array['order_status']=$data2['order_status'];
                $array['order_time0']=$data2['order_datetime0'];
                $array['order_time1']=$data2['order_datetime1'];
                $array['order_time2']=$data2['order_datetime2'];
                $array['order_time3']=$data2['order_datetime3'];
                $array['order_time4']=$data2['order_datetime4'];
                $array['order_time5']=$data2['order_datetime5'];
                $selectStatement = $database->select()
                    ->from('customer')
                    ->where('exist', "=", 0)
                    ->where('customer_id','=',$data2['sender_id'])
                    ->where('tenant_id', '=', $data2['tenant_id']);
                $stmt = $selectStatement->execute();
                $data7= $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id', '=', $data7['customer_city_id']);
                $stmt = $selectStatement->execute();
                $data8= $stmt->fetch();
                $array['sendcity']=$data8['name'];
                $selectStatement = $database->select()
                    ->from('customer')
                    ->where('exist', "=", 0)
                    ->where('customer_id','=',$data2['receiver_id'])
                    ->where('tenant_id', '=', $data2['tenant_id']);
                $stmt = $selectStatement->execute();
                $data9= $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id', '=', $data9['customer_city_id']);
                $stmt = $selectStatement->execute();
                $data10= $stmt->fetch();
                $array['receivercity']=$data10['name'];
                $selectStatement = $database->select()
                    ->from('schedule_order')
                    ->where('exist', "=", 0)
                    ->where('order_id','=',$order_id)
                    ->where('tenant_id', '=', $data2['tenant_id']);
                $stmt = $selectStatement->execute();
                $data4= $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('scheduling')
                    ->where('exist', "=", 0)
                    ->where('scheduling_id','=',$data4['schedule_id'])
                    ->where('tenant_id', '=', $data2['tenant_id']);
                $stmt = $selectStatement->execute();
                $data5= $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('lorry')
                    ->where('exist', "=", 0)
                    ->where('lorry_id','=',$data5['lorry_id'])
                    ->where('tenant_id', '=', $data2['tenant_id']);
                $stmt = $selectStatement->execute();
                $data6= $stmt->fetch();
                $array['plate_number']=$data6['plate_number'];
                echo json_encode(array("result" => "1", "desc" => "租户不存在", "orders" => $array));
            }else{
                echo json_encode(array("result" => "2", "desc" => "租户不存在", "orders" => ""));
            }
        } else {
            echo json_encode(array("result" => "3", "desc" => "租户不存在", "orders" => ""));
        }
    } else {
        echo json_encode(array("result" => "3", "desc" => "缺少租户id", "orders" => ""));
    }
});




//客户端对微信的订单受理
$app->post('/wx_orders_accept', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $body = $app->request->getBody();
    $body = json_decode($body);
    $message_id = $body->message_id;
    $database = localhost();
    if($tenant_id!=null||$tenant_id!=''){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id','=',$tenant_id);
        $stmt = $selectStatement->execute();
        $data1= $stmt->fetch();
        if($data1!=null){
            $selectStatement = $database->select()
                ->from('wx_message')
                ->where('exist', "=", 0)
                ->where('tenant_id','=',$tenant_id)
                ->where('message_id','=',$message_id);
            $stmt = $selectStatement->execute();
            $data2= $stmt->fetch();
            if($data2!=null){
                $array=array();
                $selectStatement = $database->select()
                    ->from('orders')
                    ->where('exist', "=", 0)
                    ->where('tenant_id','=',$tenant_id)
                    ->where('order_id','=',$data2['order_id']);
                $stmt = $selectStatement->execute();
                $data3= $stmt->fetch();
                $array['order']=$data3;
                if($data3!=null){
                    $selectStatement = $database->select()
                        ->from('customer')
                        ->where('tenant_id', '=', $tenant_id)
                        ->where('exist', "=", 0)
                        ->where('customer_id', '=', $data3['sender_id']);
                    $stmt = $selectStatement->execute();
                    $data4 = $stmt->fetch();
                    $array['sender']=$data4;
                    if($data4!=null){
                        $selectStatement = $database->select()
                            ->from('city')
                            ->where('id','=',$data4['customer_city_id']);
                        $stmt = $selectStatement->execute();
                        $data5 = $stmt->fetch();
                        $array['sender_city']=$data5['name'];
                        $selectStatement = $database->select()
                            ->from('customer')
                            ->where('tenant_id', '=', $tenant_id)
                            ->where('exist', "=", 0)
                            ->where('customer_id', '=', $data3['receiver_id']);
                        $stmt = $selectStatement->execute();
                        $data6 = $stmt->fetch();
                        if($data6!=null){
                            $selectStatement = $database->select()
                                ->from('city')
                                ->where('id','=',$data6['customer_city_id']);
                            $stmt = $selectStatement->execute();
                            $data7 = $stmt->fetch();
                            $array['receiver_city']=$data7['name'];
                            $array['receiver'] = $data6;
                            $selectStatement = $database->select()
                                ->from('goods')
                                ->where('tenant_id', '=', $tenant_id)
                                ->where('exist', "=", 0)
                                ->where('order_id', '=', $data2['order_id']);
                            $stmt = $selectStatement->execute();
                            $data8 = $stmt->fetch();
                            if($data8!=null){
                                $array['goods']=$data8;
                                $selectStatement = $database->select()
                                    ->from('goods_package')
                                    ->where('goods_package_id', '=', $data8['goods_package_id']);
                                $stmt = $selectStatement->execute();
                                $data9 = $stmt->fetch();
                                $array['goods_package'] = $data9;
                                echo json_encode(array("result" => "1", "desc" => "success", "wx_message" => $array));
                            }else{
                                echo json_encode(array("result" => "2", "desc" => "货物不存在", "wx_message" =>""));
                            }
                        }else{
                            echo json_encode(array("result" => "3", "desc" => "收货人不存在", "wx_message" =>""));
                        }
                    }else{
                        echo json_encode(array("result" => "4", "desc" => "发货人不存在", "wx_message" =>""));
                    }

                }else{
                    echo json_encode(array("result" => "5", "desc" => "订单不存在", "wx_message" =>""));
                }
            }else{
                echo json_encode(array("result" => "6", "desc" => "", "wx_message" =>""));
            }
        }else{
            echo json_encode(array("result" => "7", "desc" => "", "wx_message" => ""));
        }
    }else{
        echo json_encode(array("result" => "8", "desc" => "", "wx_message" => ""));
    }
});

//获得微信下单受理总数
$app->get('/wx_orders_num', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $database = localhost();
    if ($tenant_id != null || $tenant_id != "") {
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data1= $stmt->fetch();
        if($data1!=null){
            $array1=array();
            $selectStatement = $database->select()
                ->from('orders')
                ->where('exist', "=", 0)
                ->where('order_source','=','1')
                ->where('order_status','=',0)
                ->where('tenant_id', '=', $tenant_id);
            $stmt = $selectStatement->execute();
            $data2= $stmt->fetchAll();
            $num1=count($data2);
            echo json_encode(array("result" => "1", "desc" => "success", "num" => $num1));
        }else{
            echo json_encode(array("result" => "2", "desc" => "没有该租户", "orders" => ""));
        }
    } else {
        echo json_encode(array("result" => "3", "desc" => "缺少租户id", "orders" => ""));
    }
});

//分页显示微信的单子
$app->post('/wx_orders_order_source', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $database = localhost();
    $body = $app->request->getBody();
    $body = json_decode($body);
    $offset = $body->offset;
    $size=$body->size;
    if ($tenant_id != null || $tenant_id != "") {
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data1= $stmt->fetch();
        if($data1!=null){
            $array=array();
            $array1=array();
            $selectStatement = $database->select()
                ->from('wx_message')
                ->join('orders', 'wx_message.order_id', '=', 'orders.order_id','right')
                ->where('wx_message.exist', "=", 0)
                ->where('wx_message.tenant_id', '=', $tenant_id)
                ->where('orders.order_status','=',0)
                ->orderBy("wx_message.ms_date")
                ->limit((int)$size,(int)$offset);
            $stmt = $selectStatement->execute();
            $data2= $stmt->fetchAll();
            if($data2!=null){
             $num1=count($data2);
                for($i=0;$i<$num1;$i++){
                    $selectStatement = $database->select()
                        ->from('orders')
                        ->where('order_id', "=", $data2[$i]['order_id'])
                        ->where('order_status','=',0)
                        ->where('tenant_id', '=', $tenant_id)
                        ->where('exist','=',0);
                    $stmt = $selectStatement->execute();
                    $data3= $stmt->fetch();
                    $selectStatement = $database->select()
                        ->from('wx_message')
                        ->where('exist', "=", 0)
                        ->where('order_id','=',$data2[$i]['order_id'])
                        ->where('tenant_id', '=', $tenant_id);
                    $stmt = $selectStatement->execute();
                    $data4= $stmt->fetch();
                        $array1["orders"]=$data3;
                        $array1['message']=$data4;
                        array_push($array,$array1);
                 }
                echo json_encode(array("result" => "1", "desc" => "success", "orders" => $array));
            }else{
                echo json_encode(array("result" => "2", "desc" => "success", "orders" => ""));
            }
        }else{
            echo json_encode(array("result" => "3", "desc" => "没有该租户", "orders" => ""));
        }
    } else {
        echo json_encode(array("result" => "4", "desc" => "缺少租户id", "orders" => ""));
    }
});

//受理的单子（更改order_status）
$app->put('/order_status', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $database = localhost();
    $body = $app->request->getBody();
    $body = json_decode($body);
    $order_id=$body->order_id;
    $order_status = $body->order_status;
    if ($tenant_id != null || $tenant_id != "") {
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data1= $stmt->fetch();
        if($data1!=null){
            $selectStatement = $database->select()
                ->from('orders')
                ->where('exist', "=", 0)
                ->where('order_id','=',$order_id)
                ->where('tenant_id', '=', $tenant_id);
            $stmt = $selectStatement->execute();
            $data2= $stmt->fetch();
            if($data2!=null){
                $updateStatement = $database->update(array('order_status' => $order_status))
                    ->table('orders')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('exist','=',0)
                    ->where('order_id', '=', $order_id);
                $affectedRows = $updateStatement->execute();
                if($affectedRows!=null){
                    echo json_encode(array("result" => "1", "desc" => "success"));
                }else{
                    echo json_encode(array("result" => "2", "desc" => "未执行"));
                }
            }else{
                echo json_encode(array("result" => "3", "desc" => "没有该订单"));
            }
        }else{
            echo json_encode(array("result" => "4", "desc" => "没有该租户"));
        }
    } else {
        echo json_encode(array("result" => "5", "desc" => "缺少租户id"));
    }
});



//根据order_id获得orders、goods、customer
$app->get('/orders_goods_customer', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id = $app->request->headers->get("tenant-id");
    $database = localhost();
    $order_id = $app->request->get('order_id');
    if ($tenant_id != null || $tenant_id != "") {
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist', "=", 0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data1= $stmt->fetch();
        if($order_id!=null||$order_id!=''){
            if($data1!=null){
                $array=array();
                $selectStatement = $database->select()
                    ->from('orders')
                    ->where('exist', "=", 0)
                    ->where('order_id','=',$order_id)
                    ->where('tenant_id', '=', $tenant_id);
                $stmt = $selectStatement->execute();
                $data2= $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('goods')
                    ->where('exist', "=", 0)
                    ->where('order_id','=',$order_id)
                    ->where('tenant_id', '=', $tenant_id);
                $stmt = $selectStatement->execute();
                $data3= $stmt->fetch();
                $array['order']=$data2;
                $array['goods']=$data3;
                $selectStatement = $database->select()
                    ->from('customer')
                    ->where('exist', "=", 0)
                    ->where('customer_id','=',$data2['sender_id'])
                    ->where('tenant_id', '=', $tenant_id);
                $stmt = $selectStatement->execute();
                $data4= $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id', "=", $data4['customer_city_id']);
                $stmt = $selectStatement->execute();
                $data6= $stmt->fetch();
                $array['sender_city']=$data6;
                $selectStatement = $database->select()
                    ->from('province')
                    ->where('id', "=", $data6['pid']);
                $stmt = $selectStatement->execute();
                $data7= $stmt->fetch();
                $array['sender_province']=$data7;
                $selectStatement = $database->select()
                    ->from('customer')
                    ->where('exist', "=", 0)
                    ->where('customer_id','=',$data2['receiver_id'])
                    ->where('tenant_id', '=', $tenant_id);
                $stmt = $selectStatement->execute();
                $data5= $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id', "=", $data5['customer_city_id']);
                $stmt = $selectStatement->execute();
                $data8= $stmt->fetch();
                $array['receiver_city']=$data8;
                $selectStatement = $database->select()
                    ->from('province')
                    ->where('id', "=", $data8['pid']);
                $stmt = $selectStatement->execute();
                $data9= $stmt->fetch();
                $array['receiver_province']=$data9;
                $array['sender']=$data4;
                $array['receiver']=$data5;
                echo json_encode(array("result" => "0", "desc" => "success",'orders'=>$array));
            }else{
                echo json_encode(array("result" => "1", "desc" => "没有该租户"));
            }
        }else{
           echo json_encode(array('result'=>'2','desc'=>'订单id缺失'));
        }
    } else {
        echo json_encode(array("result" => "3", "desc" => "缺少租户id"));
    }
});
$app->run();

function localhost()
{
    return connect();
}

?>