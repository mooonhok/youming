<?php
    function get_token(){
    $appid = "wx15ef051f9f0bba92";
    $secret = "57ea0ee4abf4f4c6d6e38c88a289e687";
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";

    $json = https_request($url);

    $arr = json_decode($json, true);

    return $arr['access_token'];
}
$access_token = get_token();

$jsonmenu = '{
    "button": [
        {
            "name": "服务项目", 
            "sub_button": [
                {
                    "type": "view", 
                    "name": "我要发货", 
                    "url": "http://mooonhok-cloudware.daoapp.io/wx_send.php"
                }, 
                {
                    "type": "view", 
                    "name": "运单查询", 
                    "url": "http://mooonhok-cloudware.daoapp.io/wx_query.php"
                }
            ]
        }, 
        {
            "name": "业务洽谈", 
            "sub_button": [
                {
                    "type": "view", 
                    "name": "电话询价", 
                    "url": "http://mooonhok-cloudware.daoapp.io/weixin/jiangsuyouming.html"
                }, 
                {
                    "type": "view", 
                    "name": "网上询价", 
                    "url": "http://mooonhok-cloudware.daoapp.io/wx_online_inquiry.php"
                }
            ]
        }, 
        {
            "name": "我", 
            "sub_button":[
 		 {
                    "type": "view", 
                    "name": "注册", 
                    "url": "http://mooonhok-cloudware.daoapp.io/wx_register.php"
                }, 
                 {
                    "type": "view", 
                    "name": "我的运单", 
                    "url": "http://mooonhok-cloudware.daoapp.io/wx_my_consignment_note.php"
                }, 
                 {
                    "type": "view", 
                    "name": "到货付款", 
                    "url": "http://mooonhok-cloudware.daoapp.io/wx_payment.php"
                },
                {
                    "type": "view", 
                    "name": "发票申请", 
                    "url": "http://mooonhok-cloudware.daoapp.io/weixin/jstest.php"
                }
        ]
     }        
    ]
}';


$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
$result = https_request($url, $jsonmenu);
var_dump($result);

function https_request($url,$data = null){
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
if (!empty($data)){
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
}
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($curl);
curl_close($curl);
return $output;
}
?>