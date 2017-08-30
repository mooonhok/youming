<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/16
 * Time: 13:49
 */
require 'Slim/Slim.php';
require 'connect.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->post('/stafftest',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant-id');
    $database=localhost();
    $body=$app->request->getBody();
    $body=json_decode($body);
    $staff_id=$body->staff_id;
    $username=$body->username;
    $name=$body->name;
    $telephone=$body->telephone;
    $position=$body->position;
    $staff_status=$body->status;
    $permission=$body->permission;
    $array=array();
    foreach($body as $key=>$value){
        $array[$key]=$value;
    }
    if($tenant_id!=null||$tenant_id!=''){
        if($username!=null||$username!=''){
            if($name!=null||$name!=''){
                if($telephone!=null||$telephone!=''){
                        if($position!=null||$position!=''){
                            if($staff_status!=null||$staff_status!=''){
                                if($permission!=null||$permission!=''){
                                    if($staff_id!=null||$staff_id!=''){
                                            $array['password']="123456";
                                            $array['tenant_id']=$tenant_id;
                                            $array['exist']=0;
                                            $insertStatement = $database->insert(array_keys($array))
                                                ->into('staff')
                                                ->values(array_values($array));
                                            $insertId = $insertStatement->execute(false);
                                            echo json_encode(array("result"=>"0","desc"=>"success"));
                                    }else{
                                        echo json_encode(array('result'=>'1','desc'=>'缺少员工id'));
                                    }


                                }else{
                                    echo json_encode(array('result'=>'1','desc'=>'缺少权限信息'));
                                }
                            }else{
                                echo json_encode(array('result'=>'2','desc'=>'缺少状态（在职，离职，实习）'));
                            }
                        }else{
                            echo json_encode(array('result'=>'3','desc'=>'缺少职位'));
                        }
                }else{
                    echo json_encode(array('result'=>'5','desc'=>'缺少电话'));
                }
            }else{
                echo json_encode(array('result'=>'6','desc'=>'缺少姓名'));
            }
        }else{
            echo json_encode(array('result'=>'7','desc'=>'缺少用户名'));
        }
    }else{
        echo json_encode(array('result'=>'8','desc'=>'缺少租户id'));
    }
});

$app->put('/stafftest',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant-id');
    $database=localhost();
    $body=$app->request->getBody();
    $body=json_decode($body);
    $staff_id=$body->staff_id;
    $array=array();
    foreach($body as $key=>$value){
        $array[$key]=$value;
    }
    if($tenant_id!=null||$tenant_id!=''){
        if($staff_id!=null||$staff_id!=''){
            $selectStatement = $database->select()
                ->from('staff')
                ->where('staff_id','=',$staff_id)
                ->where('exist','=',0)
                ->where('tenant_id','=',$tenant_id);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if($data!=null){
                $updateStatement = $database->update($array)
                    ->table('staff')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('exist',"=",0)
                    ->where('staff_id','=',$staff_id);
                $affectedRows = $updateStatement->execute();
                echo json_encode(array("result"=>"0","desc"=>"success"));
            }else{
                echo json_encode(array('result'=>'1','desc'=>'该员工不存在'));
            }
        }else{
            echo json_encode(array('result'=>'2','desc'=>'缺少员工id'));
        }
    }else{
        echo json_encode(array('result'=>'3','desc'=>'缺少租户信息'));
    }
});

$app->put('/stafftest',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id=$app->request->headers->get('tenant-id');
    $database=localhost();
    $body=$app->request->getBody();
    $body=json_decode($body);
    $staff_id=$body->staff_id;
    $password=$body->password;
    $array=array();
    foreach($body as $key=>$value){
        $array[$key]=$value;
    }
    if($tenant_id!=null||$tenant_id!=''){
        if($staff_id!=null||$staff_id!=''){
            if($password!=null||$password!=''){
                $selectStatement = $database->select()
                    ->from('staff')
                    ->where('staff_id','=',$staff_id)
                    ->where('exist','=',0)
                    ->where('tenant_id','=',$tenant_id);
                $stmt = $selectStatement->execute();
                $data = $stmt->fetch();
                if($data!=null){
                    $updateStatement = $database->update($array)
                        ->table('staff')
                        ->where('tenant_id','=',$tenant_id)
                        ->where('exist',"=",0)
                        ->where('staff_id','=',$staff_id);
                    $affectedRows = $updateStatement->execute();
                    echo json_encode(array("result"=>"0","desc"=>"success"));
                }else{
                    echo json_encode(array('result'=>'1','desc'=>'该员工不存在'));
                }
            }else{
                echo json_encode(array('result'=>'2','desc'=>'缺少密码'));
            }
        }else{
            echo json_encode(array('result'=>'3','desc'=>'缺少员工id'));
        }
    }else{
        echo json_encode(array('result'=>'4','desc'=>'缺少租户id'));
    }
});

$app->get('/stafftest',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $page=$app->request->get('page');
    $per_page=$app->request->get("per_page");
    $database=localhost();
    if($tenant_id!=null||$tenant_id!=""){
        if($page==null||$per_page==null){
            $selectStatement = $database->select()
                ->from('staff')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            echo  json_encode(array("result"=>"0","desc"=>"success","staff"=>$data));
        }else{
            $selectStatement = $database->select()
                ->from('staff')
                ->where('tenant_id','=',$tenant_id)
                ->where('exist',"=",0)
                ->limit((int)$per_page,(int)$per_page*(int)$page);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetchAll();
            echo json_encode(array("result"=>"0","desc"=>"success","staff"=>$data));
        }
    }else{
        echo json_encode(array("result"=>"1","desc"=>"信息不全","staff"=>""));
    }
});

$app->delete('/stafftest',function()use($app){
    $app->response->headers->set('Content-Type', 'application/json');
    $tenant_id=$app->request->headers->get("tenant-id");
    $database=localhost();
    $staff_id=$app->request->get('staffid');
    if ($tenant_id!=null||$tenant_id!=''){
        if($staff_id!=null||$staff_id!=''){
            $selectStatement = $database->select()
                ->from('staff')
                ->where('tenant_id','=',$tenant_id)
                ->where('staff_id','=',$staff_id)
                ->where('exist',"=",0);
            $stmt = $selectStatement->execute();
            $data = $stmt->fetch();
            if($data!=null){
                $updateStatement = $database->update(array('exist'=>1))
                    ->table('staff')
                    ->where('tenant_id','=',$tenant_id)
                    ->where('exist',"=",0)
                    ->where('staff_id','=',$staff_id);
                $affectedRows = $updateStatement->execute();
                echo json_encode(array("result"=>"0","desc"=>"success"));
            }else{
                echo json_encode(array("result"=>"1","desc"=>"员工不存在"));
            }
        }else{
            echo json_encode(array("result"=>"2","desc"=>"缺少员工id"));
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