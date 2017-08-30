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
$app->post('/tenant',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $database=localhost();
    $body=$app->request->getBody();
    $body=json_decode($body);
    $company=$body->company;
    $company_type=$body->company_type;
    $from_city_id=$body->from_city_id;
    $receive_city_id=$body->receive_city_id;
    $contact_id=$body->contact_id;
    $array=array();
    foreach($body as $key=>$value){
        $array[$key]=$value;
    }
    if($company!=null||$company!=''){
        if($company_type!=null||$company_type!=''){
            if($from_city_id!=null||$from_city_id!=''){
                if($receive_city_id!=null||$receive_city_id!=''){
                        if($contact_id!=null||$contact_id!=''){
                                    $selectStatement = $database->select()
                                        ->from('tenant')
                                        ->where('company','=',$company);
                                    $stmt = $selectStatement->execute();
                                    $data = $stmt->fetch();
                                    if($data!=null){
                                        echo json_encode(array("result"=>"1","desc"=>"公司已存在"));
                                    }else{
                                        $selectStatement = $database->select()
                                            ->from('city')
                                            ->where('id','=',$from_city_id);
                                        $stmt = $selectStatement->execute();
                                        $data1 = $stmt->fetch();
                                        if($data1==null){
                                            echo json_encode(array("result"=>"2","desc"=>"发货人城市不存在"));
                                        }else{
                                            $selectStatement = $database->select()
                                                ->from('city')
                                                ->where('id','=',$receive_city_id);
                                            $stmt = $selectStatement->execute();
                                            $data2 = $stmt->fetch();
                                            if($data2==null){
                                                echo json_encode(array("result"=>"3","desc"=>"收货人城市不存在"));
                                            }else{
                                                $selectStatement = $database->select()
                                                    ->from('customer')
                                                    ->where('exist','=','0')
                                                    ->where('customer_id','=',$contact_id);
                                                $stmt = $selectStatement->execute();
                                                $data3 = $stmt->fetch();
                                                if($data3==null){
                                                    echo json_encode(array("result"=>"4","desc"=>"公司联系人不存在"));
                                                }else{
                                                    $selectStatement = $database->select()
                                                        ->from('tenant');
                                                    $stmt = $selectStatement->execute();
                                                    $data4 = $stmt->fetchAll();
                                                    $tenant_id=10000001+count($data4);
                                                    $array['tenant_id']=$tenant_id;
                                                    $array['exist']=0;
                                                    $insertStatement = $database->insert(array_keys($array))
                                                        ->into('tenant')
                                                        ->values(array_values($array));
                                                    $insertId = $insertStatement->execute(false);
                                                    echo json_encode(array("result"=>"0","desc"=>"success"));
                                                }
                                            }
                                        }
                                    }
                        }else{
                            echo json_encode(array('result'=>'5','desc'=>'缺少联系人id'));
                        }
                }else{
                    echo json_encode(array('result'=>'6','desc'=>'缺少收货城市id'));
                }
            }else{
                echo json_encode(array('result'=>'7','desc'=>'缺少发货城市id'));
            }
        }else{
            echo json_encode(array('result'=>'8','desc'=>'缺少公司类型'));
        }
    }else{
        echo json_encode(array('result'=>'9','desc'=>'缺少公司名字'));
    }
});

$app->put('/tenant',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $database=localhost();
    $body=$app->request->getBody();
    $body=json_decode($body);
    $tenant_id=$body->tenant_id;
    $company=$body->company;
    $company_type=$body->company_type;
    $from_city_id=$body->from_city_id;
    $receive_city_id=$body->receive_city_id;
    $contact_id=$body->contact_id;
    $array=array();
    foreach($body as $key=>$value){
        $array[$key]=$value;
    }
    if($tenant_id!=null||$tenant_id!=''){
        if($company!=null||$company!=''){
            if($company_type!=null||$company_type!=''){
                if($from_city_id!=null||$from_city_id!=''){
                    if($receive_city_id!=null||$receive_city_id!=''){
                        if($contact_id!=null||$contact_id!=''){
                            $selectStatement = $database->select()
                                ->from('tenant')
                                ->where('tenant_id','=',$tenant_id)
                                ->where('exist','=','0');
                            $stmt = $selectStatement->execute();
                            $data = $stmt->fetch();
                            if($data!=null){
                                $selectStatement = $database->select()
                                    ->from('tenant')
                                    ->where('exist','=','0')
                                    ->where('tenant_id','=',$tenant_id)
                                    ->where('company','!=',$company);
                                $stmt = $selectStatement->execute();
                                $data1= $stmt->fetch();
                                if($data1==null){
                                    echo json_encode(array("result"=>"1","desc"=>"公司名字已存在"));
                                }else{
                                    $selectStatement = $database->select()
                                        ->from('city')
                                        ->where('id','=',$from_city_id);
                                    $stmt = $selectStatement->execute();
                                    $data2 = $stmt->fetch();
                                    if($data2==null){
                                        echo json_encode(array("result"=>"2","desc"=>"发货人城市不存在"));
                                    }else{
                                        $selectStatement = $database->select()
                                            ->from('city')
                                            ->where('id','=',$receive_city_id);
                                        $stmt = $selectStatement->execute();
                                        $data3 = $stmt->fetch();
                                        if($data3==null){
                                            echo json_encode(array("result"=>"3","desc"=>"收货人城市不存在"));
                                        }else{
                                            $selectStatement = $database->select()
                                                ->from('customer')
                                                ->where('exist','=','0')
                                                ->where('customer_id','=',$contact_id);
                                            $stmt = $selectStatement->execute();
                                            $data4 = $stmt->fetch();
                                            if($data4==null){
                                                echo json_encode(array("result"=>"4","desc"=>"公司联系人不存在"));
                                            }else{
                                                $array['exist']="0";
                                                $updateStatement = $database->update($array)
                                                    ->table('tenant')
                                                    ->where('tenant_id','=',$tenant_id);
                                                $affectedRows = $updateStatement->execute();
                                                echo json_encode(array("result"=>"0","desc"=>"success"));
                                            }
                                        }
                                    }
                                }

                            }else{
                                echo json_encode(array("result"=>"6","desc"=>"该公司不存在"));
                            }
                        }else{
                            echo json_encode(array('result'=>'7','desc'=>'缺少公司联系人id'));
                        }
                    }else{
                        echo json_encode(array('result'=>'8','desc'=>'缺少收货城市id'));
                    }
                }else{
                    echo json_encode(array('result'=>'9','desc'=>'缺少发货城市id'));
                }
            }else{
                echo json_encode(array('result'=>'10','desc'=>'缺少公司类型'));
            }
        }else{
            echo json_encode(array('result'=>'11','desc'=>'缺少公司名字'));
        }
    }else{
        echo json_encode(array('result'=>'12','desc'=>'缺少租户公司的id'));
    }

});


$app->get('/tenant',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $page=$app->request->get('page');
    $per_page=$app->request->get("per_page");
    $database=localhost();
        if($page==null||$per_page==null){
            $selectStatement = $database->select()
                ->from('tenant')
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            echo  json_encode(array("result"=>"0","desc"=>"success","tenants"=>$data));
        }else{
            $selectStatement = $database->select()
                ->from('tenant')
                ->where('exist',"=",0)
                ->limit((int)$per_page,(int)$per_page*(int)$page);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            echo  json_encode(array("result"=>"0","desc"=>"success","tenants"=>$data));
        }
});

$app->delete('/tenant',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $database=localhost();
    $tenant_id=$app->request->get('tenantid');
    if ($tenant_id!=null||$tenant_id!=''){
            $selectStatement = $database->select()
                ->from('tenant')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if($data!=null){
                $updateStatement = $database->update(array('exist'=>1))
                    ->table('tenant')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('exist',"=",0);
                $affectedRows = $updateStatement->execute();
                echo json_encode(array("result"=>"0","desc"=>"success"));
            }else{
                echo json_encode(array("result"=>"1","desc"=>"该公司不存在"));
            }
    }else{
        echo json_encode(array("result"=>"2","desc"=>"缺少租户id"));
    }
});

$app->run();

function localhost(){
    return connect();
}
?>