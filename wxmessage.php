<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/19
 * Time: 10:27
 */
require 'Slim/Slim.php';
require 'connect.php';


\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

//微信添加
$app->post('/wxmessage_insert',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $body=$app->request->getBody();
    $body=json_decode($body);
    $database=localhost();
//    $customer_name_s=$body->customer_name_s;
//    $customer_city_s=$body->customer_city_s;
//    $customer_adress_s=$body->customer_address_s;
//    $customer_phone_s=$body->customer_phone_s;
//    $customer_name_a=$body->customer_name_a;
//    $customer_city_a=$body->customer_city_a;
//    $customer_address_a=$body->customer_address_a;
//    $customer_phone_a=$body->customer_phone_a;
    $customer_send_id=$body->customer_send_id;
    $customer_accept_id=$body->customer_accept_id;
    $goods_name=$body->goods_name;
    $goods_weight=$body->goods_weight;
    $goods_capacity=$body->goods_capacity;
    $goods_package=$body->goods_package;
    $goods_count=$body->goods_count;
    $special_need=$body->special_need;
    $good_worth=$body->good_worth;
    $pay_method=$body->pay_method;
    $wx_openid=$body->openid;
    if($tenant_id!=''||$tenant_id!=null){
//        if($customer_name_s!=''||$customer_name_s!=null){
//            if($customer_city_s!=''||$customer_city_s!=null){
//                if($customer_adress_s!=''||$customer_adress_s!=null){
//                        if($customer_phone_s!=''||$customer_phone_s!=null){
//                            if($customer_name_a!=''||$customer_name_a!=null){
//                                if($customer_city_a!=''||$customer_city_a!=null){
//                                    if($customer_address_a!=''||$customer_address_a!=null){
//                                        if($customer_phone_a!=''||$customer_phone_a!=null){
                                      if($customer_send_id!=null||$customer_send_id!=''){
                                          if($customer_accept_id!=null||$customer_accept_id!=''){
                                            if($goods_name!=''||$goods_name!=null){
                                                if($goods_weight!=''||$goods_weight!=null){
                                                    if($goods_capacity!=''||$goods_capacity!=null){
                                                        if($goods_package!=''||$goods_package!=null){
                                                            if($goods_count!=''||$goods_count!=null){
                                                                if($special_need!=''||$special_need!=null){
                                                                    if($good_worth!=''||$good_worth!=null){
                                                                        if($pay_method!=''||$pay_method!=null){
                                                                            if($wx_openid!=''||$wx_openid!=null){
                                                                                $selectStatement = $database->select()
                                                                                    ->from('customer')
                                                                                    ->where('tenant_id','=',$tenant_id)
                                                                                    ->where('exist',"=",0)
//                                                                                    ->where('customer_address','=',$customer_adress_s)
//                                                                                    ->where('customer_name','=',$customer_name_s)
//                                                                                    ->where('customer_city_id','=',$customer_city_s)
//                                                                                    ->where('customer_phone','=',$customer_phone_s)
                                                                                    ->where('customer_id','=',$customer_send_id)
                                                                                    ->where('type','=',1)
                                                                                    ->where('wx_openid','=',$wx_openid);
                                                                                $stmt = $selectStatement->execute();
                                                                                $data = $stmt->fetch();
                                                                                    if($data!=null){
                                                                                        $selectStatement = $database->select()
                                                                                            ->from('customer')
                                                                                            ->where('tenant_id','=',$tenant_id)
                                                                                            ->where('exist',"=",0)
//                                                                                            ->where('customer_address','=',$customer_adress_a)
//                                                                                            ->where('customer_name','=',$customer_name_a)
//                                                                                            ->where('customer_city_id','=',$customer_city_a)
//                                                                                            ->where('customer_phone','=',$customer_phone_a)
                                                                                            ->where('customer_id','=',$customer_accept_id)
                                                                                            ->where('type','=',2)
                                                                                            ->where('wx_openid','=',$wx_openid);
                                                                                        $stmt = $selectStatement->execute();
                                                                                        $data1 = $stmt->fetch();
                                                                                            if($data1!=null) {
                                                                                                $str=null;
                                                                                                do{
                                                                                                    $time=time();
//                                                                                                    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
//                                                                                                    $str = substr($chars, mt_rand(0, strlen($chars) - 3), 2);
                                                                                                    $str.=$time;
                                                                                                    $selectStatement = $database->select()
                                                                                                        ->from('orders')
                                                                                                        ->where('tenant_id','=',$tenant_id)
                                                                                                        ->where('order_id','=',$str)
                                                                                                        ->where('exist',"=",0);
                                                                                                    $stmt = $selectStatement->execute();
                                                                                                    $data4= $stmt->fetchAll();
                                                                                                }while($data4!=null);
                                                                                    $insertStatement = $database->insert(array('order_id', 'tenant_id', 'pay_method','exist','order_status','sender_id','order_source','receiver_id'))
                                                                                        ->into('orders')
                                                                                        ->values(array($str,$tenant_id, $pay_method,0,-2,$data["customer_id"],1,$data1['customer_id']));
                                                                                    $insertId = $insertStatement->execute(false);
                                                                                    if($insertId!=null){
//                                                                                        $selectStatement = $database->select()
//                                                                                            ->from('wx_message')
//                                                                                            ->where('tenant_id','=',$tenant_id);
//                                                                                        $stmt = $selectStatement->execute();
//                                                                                        $data5= $stmt->fetchAll();
//                                                                                        $wx_message_id=count($data5);
                                                                                        $str1=null;
                                                                                        do{$time=time();
//                                                                                            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
//                                                                                            $str1 = substr($chars, mt_rand(0, strlen($chars) - 3), 2);
//                                                                                            $str=rand(10,99);
                                                                                            $str1.=$time;
                                                                                            $selectStatement = $database->select()
                                                                                                ->from('wx_message')
                                                                                                ->where('tenant_id','=',$tenant_id)
                                                                                                ->where('message_id','=',$str)
                                                                                                ->where('exist',"=",0);
                                                                                            $stmt = $selectStatement->execute();
                                                                                            $data4= $stmt->fetchAll();
                                                                                        }while($data4!=null);
                                                                                        $selectStatement = $database->select()
                                                                                            ->from('customer')
                                                                                            ->where('tenant_id','=',$tenant_id)
                                                                                            ->where('exist',"=",0)
                                                                                            ->where('customer_adress','=','-1')
                                                                                            ->where('customer_city_id','=','-1')
                                                                                            ->where('type','=',"")
                                                                                            ->where('wx_openid','=',$wx_openid);
                                                                                        $stmt = $selectStatement->execute();
                                                                                        $data6 = $stmt->fetch();
                                                                                        if($data6!=null){
                                                                                            $insertStatement = $database->insert(array('order_id', 'tenant_id', 'message_id','exist','from_user','mobilephone','is_read','ms_date'))
                                                                                                ->into('wx_message')
                                                                                                ->values(array($str,$tenant_id, $str1,0,$data6['customer_name'],$data6["customer_phone"],0,date("Y-m-d h:i:sa")));
                                                                                            $insertId = $insertStatement->execute(false);
                                                                                            if($insertId!=null){
                                                                                                $str2=null;
                                                                                                do{
                                                                                                    $time=time();
//                                                                                                    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
//                                                                                                    $str2 = substr($chars, mt_rand(0, strlen($chars) - 3), 2);
                                                                                                    $str2.=$time;
                                                                                                    $selectStatement = $database->select()
                                                                                                        ->from('goods')
                                                                                                        ->where('tenant_id','=',$tenant_id)
                                                                                                        ->where('goods_id','=',$str2)
                                                                                                        ->where('exist',"=",0);
                                                                                                    $stmt = $selectStatement->execute();
                                                                                                    $data4= $stmt->fetchAll();
                                                                                                }while($data4!=null);
                                                                                                $selectStatement = $database->select()
                                                                                                    ->from('goods_package')
                                                                                                    ->where('goods_package','=',$goods_package);
                                                                                                $stmt = $selectStatement->execute();
                                                                                                $data7= $stmt->fetch();
                                                                                                $insertStatement = $database->insert(array('order_id', 'tenant_id', 'goods_id','exist','goods_package_id','goods_name','goods_weight','goods_capacity','goods_count','special_need','goods_value'))
                                                                                                    ->into('goods')
                                                                                                    ->values(array($str,$tenant_id, $str2,0,$data7['goods_package_id'],$goods_name,$goods_weight,$goods_capacity,$goods_count,$special_need,$good_worth));
                                                                                                $insertId = $insertStatement->execute(false);
                                                                                                if($insertId!=null){
                                                                                                    echo json_encode(array("result"=>"1","desc"=>"success"));
                                                                                                }else{
                                                                                                    echo json_encode(array("result"=>"2","desc"=>"微信添加货物失败"));
                                                                                                }
                                                                                            }else{
                                                                                                echo json_encode(array("result"=>"3","desc"=>"微信添加微信消息失败"));
                                                                                            }
                                                                                        }else{
                                                                                            echo json_encode(array("result"=>"4","desc"=>"订单填写人信息不存在"));
                                                                                        }

                                                                                    }else{
                                                                                        echo json_encode(array("result"=>"5","desc"=>"微信添加订单执行失败"));
                                                                                    }
                                                                                            }else {
                                                                                                echo json_encode(array("result"=>"6","desc"=>"收件人信息不存在"));
                                                                                            }
                                                                                }else{
                                                                                    echo json_encode(array("result"=>"7","desc"=>"寄件人信息不存在"));
                                                                                }
                                                                            }else{
                                                                                echo json_encode(array("result"=>"8","desc"=>"缺少消息内容"));
                                                                            }
                                                                        }else{
                                                                            echo json_encode(array("result"=>"9","desc"=>"缺少消息标题"));
                                                                        }
                                                                    }else{
                                                                        echo json_encode(array("result"=>"10","desc"=>"缺少订单创建人电话"));
                                                                    }
                                                                }else{
                                                                    echo json_encode(array("result"=>"11","desc"=>"缺少订单创建人"));
                                                                }
                                                            }else{
                                                                echo json_encode(array("result"=>"12","desc"=>"缺少运单id"));
                                                            }
                                                        }else{
                                                            echo json_encode(array("result"=>"13","desc"=>"缺少租户id"));
                                                        }
                                                    }else{
                                                        echo json_encode(array("result"=>"14","desc"=>"缺少消息内容"));
                                                    }
                                                }else{
                                                    echo json_encode(array("result"=>"15","desc"=>"缺少消息标题"));
                                                }
                                            }else{
                                                echo json_encode(array("result"=>"16","desc"=>"缺少订单创建人电话"));
                                            }
                                          }else{
                                              echo json_encode(array("result"=>"17","desc"=>"缺少收件人id"));
                                          }
                                         }else{
                                                echo json_encode(array("result"=>"18","desc"=>"缺少寄件人id"));
                                         }
//                                        }else{
//                                            echo json_encode(array("result"=>"17","desc"=>"缺少订单创建人"));
//                                        }
//                                    }else{
//                                        echo json_encode(array("result"=>"18","desc"=>"缺少收货人的地址"));
//                                    }
//                                }else{
//                                    echo json_encode(array("result"=>"19","desc"=>"缺少收货人的城市"));
//                                }
//                            }else{
//                                echo json_encode(array("result"=>"20","desc"=>"缺少收货人的姓名"));
//                            }
//                        }else{
//                            echo json_encode(array("result"=>"21","desc"=>"缺少发货人的电话"));
//                        }
//                }else{
//                    echo json_encode(array("result"=>"22","desc"=>"缺少发货人的地址"));
//                }
//            }else{
//                echo json_encode(array("result"=>"23","desc"=>"缺少发货人的城市"));
//            }
//        }else{
//            echo json_encode(array("result"=>"24","desc"=>"缺少发货人的姓名"));
//        }
}else{
    echo json_encode(array("result"=>"25","desc"=>"缺少租户id"));
}



});



$app->post('/wxmessage',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $body=$app->request->getBody();
    $body=json_decode($body);
    $database=localhost();
    $order_id=$body->order_id;
    $from_user=$body->from_user;
    $mobilephone=$body->mobilephone;
    $title=$body->title;
    $content=$body->content;
    $array=array();
    foreach($body as $key=>$value){
         $array[$key]=$value;
    }
    if($tenant_id!=''||$tenant_id!=null){
        if($order_id!=''||$order_id!=null){
            if($from_user!=''||$from_user!=null){
                if($mobilephone!=''||$mobilephone!=null){
                    if(preg_match("/^1[34578]\d{9}$/", $mobilephone)){
                   if($title!=''||$title!=null){
                       if($content!=''||$content!=null){
                           $array['exist']=0;
                           $array['tenant_id']=$tenant_id;
                           $selectStatement = $database->select()
                               ->from('wx_message')
                               ->where('tenant_id', '=',$tenant_id);
                           $stmt = $selectStatement->execute();
                           $data = $stmt->fetchAll();
                           if($data==null){
                               $messageid=100000001;
                           }else{
                               $messageid=count($data)+100000001;
                           }
                           $array['message_id']=$messageid;
                           $insertStatement = $database->insert(array_keys($array))
                               ->into('wx_message')
                               ->values(array_values($array));
                           $insertId = $insertStatement->execute(false);
                           echo json_encode(array('result'=>'0','desc'=>'success'));
                       }else{
                           echo json_encode(array("result"=>"1","desc"=>"缺少消息内容"));
                       }
                   }else{
                       echo json_encode(array("result"=>"2","desc"=>"缺少消息标题"));
                   }
                    }else{
                        echo json_encode(array("result"=>"3","desc"=>"创建人电话不符合要求"));
                    }
                }else{
                    echo json_encode(array("result"=>"4","desc"=>"缺少订单创建人电话"));
                }
            }else{
                echo json_encode(array("result"=>"5","desc"=>"缺少订单创建人"));
            }
        }else{
            echo json_encode(array("result"=>"6","desc"=>"缺少运单id"));
        }
    }else{
        echo json_encode(array("result"=>"7","desc"=>"缺少租户id"));
    }
});


//获得所有微信下的单
$app->post('/wxmessages',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant-id');
    $database=localhost();
    $body=$app->request->getBody();
    $body=json_decode($body);
    $is_read=$body->is_read;
    $page=$app->request->get("page");
    $per_page=$app->request->get("per_page");
    if(($tenant_id!=''||$tenant_id!=null)){
        if($page==null||$per_page==null){
            $selectStatement = $database->select()
                             ->from('wx_message')
                             ->where('tenant_id','=',$tenant_id)
                             ->where('exist',"=",0)
                             ->where('is_read','=',$is_read)
                             ->orderBy('ms_date');
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            $num1=count($data);
            $array1=array();
            for($i=0;$i<$num1;$i++){
                $array=array();
                $array['wxmessage']=$data[$i];
                $selectStatement = $database->select()
                    ->from('orders')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('order_source','=','1')
                    ->where('exist',"=",0)
                    ->where('order_id','=',$data[$i]['order_id']);
                $stmt = $selectStatement->execute();
                $data1 = $stmt->fetch();
                $array['orders']=$data1;
                $selectStatement = $database->select()
                    ->from('customer')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('exist',"=",0)
                    ->where('customer_id','=',$data1['sender_id']);
                $stmt = $selectStatement->execute();
                $data2 = $stmt->fetch();
                $array['sender']=$data2;
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id','=',$data2['customer_city_id']);
                $stmt = $selectStatement->execute();
                $data5 = $stmt->fetch();
                $array['sender_city']=$data5['name'];
                $selectStatement = $database->select()
                    ->from('customer')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('exist',"=",0)
                    ->where('customer_id','=',$data1['receiver_id']);
                $stmt = $selectStatement->execute();
                $data3 = $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id','=',$data3['customer_city_id']);
                $stmt = $selectStatement->execute();
                $data6 = $stmt->fetch();
                $array['receiver_city']=$data6['name'];
                $array['receiver']=$data3;
                $selectStatement = $database->select()
                    ->from('goods')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('exist',"=",0)
                    ->where('order_id','=',$data[$i]['order_id']);
                $stmt = $selectStatement->execute();
                $data4 = $stmt->fetch();
                $array['goods']=$data4;
                array_push($array1,$array);
            }
            echo  json_encode(array("result"=>"0","desc"=>"success","wxmessage"=>$array1));
        }else {
            $selectStatement = $database->select()
                ->from('wx_message')
                ->where('tenant_id', '=', $tenant_id)
                ->where('exist', "=", 0)
                ->orderBy('ms_date')
                ->limit((int)$per_page, (int)$per_page * (int)$page);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            $num1 = count($data);
            $array1 = array();
            for ($i = 0; $i < $num1; $i++) {
                $array = array();
                $array['wxmessage']=$data[$i];
                $selectStatement = $database->select()
                    ->from('orders')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('exist', "=", 0)
                    ->where('order_id', '=', $data[$i]['order_id']);
                $stmt = $selectStatement->execute();
                $data1 = $stmt->fetch();
                $array['orders'] = $data1;
                $selectStatement = $database->select()
                    ->from('customer')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('exist', "=", 0)
                    ->where('customer_id', '=', $data1['sender_id']);
                $stmt = $selectStatement->execute();
                $data2 = $stmt->fetch();
                $array['sender']=$data2;
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id','=',$data2['customer_city_id']);
                $stmt = $selectStatement->execute();
                $data5 = $stmt->fetch();
                $array['sender_city']=$data5['name'];
                $array['sender'] = $data2;
                $selectStatement = $database->select()
                    ->from('customer')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('exist', "=", 0)
                    ->where('customer_id', '=', $data1['receiver_id']);
                $stmt = $selectStatement->execute();
                $data3 = $stmt->fetch();
                $selectStatement = $database->select()
                    ->from('city')
                    ->where('id','=',$data3['customer_city_id']);
                $stmt = $selectStatement->execute();
                $data6 = $stmt->fetch();
                $array['receiver_city']=$data6['name'];
                $array['receiver'] = $data3;
                $selectStatement = $database->select()
                    ->from('goods')
                    ->where('tenant_id', '=', $tenant_id)
                    ->where('exist', "=", 0)
                    ->where('order_id', '=', $data1['order_id']);
                $stmt = $selectStatement->execute();
                $data7 = $stmt->fetch();
                $array['goods'] = $data7;
                array_push($array1, $array);
            }
            echo json_encode(array("result" => "0", "desc" => "success", "orders" => $array1));
        }
    }else{
        echo json_encode(array("result"=>"1","desc"=>"信息不全","orders"=>""));
    }
});





$app->get('/wxmessage/isread',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant-id');
    $database=localhost();
    $message_id=$app->request->get('messageid');
    if($tenant_id!=''||$tenant_id!=null){
        if ($message_id!=''||$message_id!=null){
            $selectStatement = $database->select()
                ->from('wx_message')
                ->where('tenant_id','=',$tenant_id)
                ->where('message_id','=',$message_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if($data!=null){
                echo json_encode(array('code'=>0,'is_read'=>$data['is_read']));
            }else{
                echo json_encode(array('code'=>1,'is_read'=>''));
            }
        }else{
            echo json_encode(array('code'=>3,'is_read'=>'缺少消息id'));
        }
    }else{
        echo json_encode(array('code'=>4,'is_read'=>'缺少用户id'));
    }
});

$app->get('/wxmessage/set-read',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant-id');
    $database=localhost();
    $message_id=$app->request->get('messageid');
    if($tenant_id!=''||$tenant_id!=null){
        if ($message_id!=''||$message_id!=null){
            $selectStatement = $database->select()
                ->from('wx_message')
                ->where('tenant_id','=',$tenant_id)
                ->where('message_id','=',$message_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if($data!=null){
                $updateStatement = $database->update(array('is_read' => 1))
                    ->table('wx_message')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('message_id','=',$message_id)
                    ->where('exist',"=",0);
                $affectedRows = $updateStatement->execute();
                echo json_encode(array("result"=>"0","desc"=>"success"));
            }else{
                echo json_encode(array("result"=>"1","desc"=>"不存在"));
            }
        }else{
            echo json_encode(array("result"=>"2","desc"=>"缺少消息id"));
        }
    }else{
        echo json_encode(array("result"=>"3","desc"=>"缺少租户id"));
    }
});

$app->delete("/wxmessage",function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant_id');
    $database=localhost();
    $message_id=$app->request->get('messageid');
    if($tenant_id!=''||$tenant_id!=null){
        if ($message_id!=''||$message_id!=null){
            $selectStatement = $database->select()
                ->from('wx_message')
                ->where('tenant_id','=',$tenant_id)
                ->where('message_id','=',$message_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if($data!=null){
                $selectStatement = $database->select()
                    ->from('orders')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('order_id','=',$data['order_id'])
                    ->where('exist',"=",0);
                $stmt = $selectStatement->execute();
                $data1 = $stmt->fetch();
                if($data1['order_status']==0||$data1['order_status']==5){
                    $updateStatement = $database->update(array('exist' => 1))
                        ->table('wx_message')
                        ->where('tenant_id','=',$tenant_id)
                        ->where('message_id','=',$message_id)
                        ->where('exist',"=",0);
                    $affectedRows = $updateStatement->execute();
                    echo json_encode(array("result"=>"0","desc"=>"success"));
                }else{
                    echo json_encode(array("result"=>"1","desc"=>"订单已发出"));
                }
            }else{
                echo json_encode(array("result"=>"2","desc"=>"不存在"));
            }
        }else{
            echo json_encode(array("result"=>"3","desc"=>"缺少消息id"));
        }
    }else{
        echo json_encode(array("result"=>"4","desc"=>"缺少租户id"));
    }
});



//is_read的修改0至1
$app->put("/wxmessage_isread",function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant_id');
    $body=$app->request->getBody();
    $body=json_decode($body);
    $database=localhost();
    $message_id=$body->message_id;
    if($tenant_id!=''||$tenant_id!=null){
        if ($message_id!=''||$message_id!=null){
            $selectStatement = $database->select()
                ->from('tenant')
                ->where('exist', "=", 0)
                ->where('tenant_id', '=', $tenant_id);
            $stmt = $selectStatement->execute();
            $data1 = $stmt->fetch();
            if($data1!=null){
                $selectStatement = $database->select()
                    ->from('wx_message')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('message_id','=',$message_id)
                    ->where('exist',"=",0);
                $stmt = $selectStatement->execute();
                $data2 = $stmt->fetch();
                if($data2!=null){
                    $updateStatement = $database->update(array('is_read' => 1))
                        ->table('wx_message')
                        ->where('tenant_id','=',$tenant_id)
                        ->where('message_id','=',$message_id)
                        ->where('exist',"=",0);
                    $affectedRows = $updateStatement->execute();
                    if($affectedRows!=null){
                        echo json_encode(array("result"=>"1","desc"=>"successs"));
                    }else{
                        echo json_encode(array("result"=>"2","desc"=>"未执行"));
                    }
                }else{
                    echo json_encode(array("result"=>"3","desc"=>"信息不存在"));
                }
            }else{
                echo json_encode(array("result"=>"4","desc"=>"租户不存在"));
            }
        }else{
            echo json_encode(array("result"=>"3","desc"=>"缺少消息id"));
        }
    }else{
        echo json_encode(array("result"=>"4","desc"=>"缺少租户id"));
    }
});


//根据message_id查出已读is_read
$app->post("/wxmessage_isread",function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant_id');
    $body=$app->request->getBody();
    $body=json_decode($body);
    $database=localhost();
    $message_id=$body->message_id;
    $order_id=$body->order_id;
    if($tenant_id!=''||$tenant_id!=null){
        if ($message_id!=''||$message_id!=null){
            $selectStatement = $database->select()
                ->from('tenant')
                ->where('exist', "=", 0)
                ->where('tenant_id', '=', $tenant_id);
            $stmt = $selectStatement->execute();
            $data1 = $stmt->fetch();
            if($data1!=null){
                $selectStatement = $database->select()
                    ->from('wx_message')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('message_id','=',$message_id)
                    ->where('exist',"=",0);
                $stmt = $selectStatement->execute();
                $data2 = $stmt->fetch();
                echo json_encode(array("result"=>"1","desc"=>"","is_read"=>$data2['is_read']));
            }else{
                echo json_encode(array("result"=>"2","desc"=>"租户不存在"));
            }
        }else if($order_id!=''||$order_id!=null){
            $selectStatement = $database->select()
                ->from('tenant')
                ->where('exist', "=", 0)
                ->where('tenant_id', '=', $tenant_id);
            $stmt = $selectStatement->execute();
            $data1 = $stmt->fetch();
            if($data1!=null){
                $selectStatement = $database->select()
                    ->from('wx_message')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('order_id','=',$order_id)
                    ->where('exist',"=",0);
                $stmt = $selectStatement->execute();
                $data2 = $stmt->fetch();
                echo json_encode(array("result"=>"1","desc"=>"","is_read"=>$data2['is_read']));
            }else{
                echo json_encode(array("result"=>"2","desc"=>"租户不存在"));
            }
        }else{
            echo json_encode(array("result"=>"3","desc"=>"message_id和order_id必须有一个"));
        }
    }else{
        echo json_encode(array("result"=>"4","desc"=>"缺少租户id"));
    }
});


//order_source为1的所有订单数
$app->get("/wx_message_source",function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant_id');
    $database=localhost();
    if($tenant_id!=null){
        $selectStatement = $database->select()
            ->from('orders')
            ->where('tenant_id','=',$tenant_id)
            ->where('order_source','=',1)
            ->where('exist',"=",0);
        $stmt = $selectStatement->execute();
        $data1 = $stmt->fetchAll();
        echo json_encode(array("result"=>"1","desc"=>"success",'count'=>count($data1)));
    }else{
        echo json_encode(array("result"=>"2","desc"=>"缺少租户id"));
    }
});





$app->run();

function localhost(){
    return connect();
}
?>