<?php
require_once "jssdk.php";
$jssdk = new JSSDK("wx15ef051f9f0bba92", "57ea0ee4abf4f4c6d6e38c88a289e687");
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<meta content="width=device-width,initial-scale=1.0,maximum-scale=1,minimum-scale=0.1,user-scalable=0" name="viewport">
		<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
		<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
		<script type="text/javascript" src="js/jquery.cookie.js"></script>
		<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
		<link rel="stylesheet" href="css/wodeyundan.css">
		<title>我的运单</title>
	</head>

	<body>
		<div class="box">
			<div class="top2">

				<div class="yundanghao">
					<div class="rongqi">
						<div class="kuang">
							<input id="yundanhao" type="number" placeholder="请输入要查询的运单号" pattern="[0-9]*">
						</div>

						<div class="tu" id="saoman">
							<img src="images/saoma.png" alt="">
						</div>
					</div>
				</div>

			</div>
			<!-- center -->
			<div class="center">

				<div class="BT">
					<div class="center1">
						<h3>我寄的</h3></div>

					<div class="center2">
						<h3>我收的</h3></div>

				</div>
				<div class="BT2">
					<div class="center1_1"></div>
					<div class="center2_1"></div>
				</div>

				<div class="box1" id="bo1">
				</div>

				<div class="box2" id="bo2">

				</div>

			</div>

		</div>

	</body>
	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	
	<script type="text/javascript">
		$(".box2").hide();
		$(".center1_1").css("border", "1px solid #AAF0EB");
		$(".center2").on("click", function() {
			$(".box1").hide();
			$(".box2").show();
			$(".center1_1").css("border", "1px solid white");
			$(".center2_1").css("border", "1px solid #AAF0EB");

		})
		$(".center1").on("click", function() {
			$(".box2").hide();
			$(".box1").show();
			$(".center2_1").css("border", "1px solid white");
			$(".center1_1").css("border", "1px solid #AAF0EB");

		})
	</script>
	<script type="text/javascript">
		$(".tu").on("click", function() {
			//		alert('ok')
		})
	</script>
	<script type="text/javascript">
		$(".yundan_3_1").on("click", function() {
			//		alert('ok')
		})
	</script>
	<script>
		//判断openid是否已经被注册
		var openid = $.cookie('openid');
		if(openid != null) {
			$.ajax({
				url: "http://mooonhok-cloudware.daoapp.io/customer.php/wx_openid?wx_openid="+openid,
				beforeSend: function(request) {
					request.setRequestHeader("tenant-id", "1");
				},
				dataType: 'json',
				type: 'get',
				contentType: "application/json;charset=utf-8",
				data: JSON.stringify({
					
				}),
				success: function(msg) {
					//alert("用户注册成功" + msg.result + "/////" + msg.desc + "//////" + msg.customer);
				if(msg.result == 0) {
						window.location.href = "http://mooonhok-cloudware.daoapp.io/weixin/register.html?page=1";
					}
				},
				error: function(xhr) {
					alert("获取后台失败！" + xhr.responseText);
				}

			});
		}
	</script>
	<script>
		//查询运单
		$("#yundanhao").on('keyup', function() {
			$("#bo2").html("");
			$("#bo1").html("");
			var order_id = $("#yundanhao").val();
			//	alert(order_id);
			$.ajax({
				url: "http://mooonhok-cloudware.daoapp.io/order.php/wx_order",
				beforeSend: function(request) {
					request.setRequestHeader("tenant-id", "1");
				},
				dataType: 'json',
				type: 'post',
				contentType: "application/json;charset=utf-8",
				data: JSON.stringify({
					wx_openid: openid,
					order_id: order_id
				}),
				success: function(msg) {
					alert("我寄的" + msg.result + "////" + msg.desc + "////" + msg.orders[0].order_id);
					if(msg.orders.length == 0) {
						a == null;
						alert("没有订单");
					} else {
						for(var i = 0; i < msg.orders.length; i++) {
							if(msg){}else{}
							
							var a="<div class='xian'></div><div class='yundan'><div class='yundan_1'><p>运单号:<span>"
							 +msg.orders[i].order_id+"</span></p><p>订单价格:<span >"
							 +msg.orders[i].order_cost+"</span></p></div><div class='yundan_2'><div class='yundan_2_1'><h3>"
							 +msg.orders[i].sendcity+"</h3><p>"
							 +msg.orders[i].sendname+"</p></div><div class='yundan_2_2'><p class='sta'>"
							 +msg.orders[i].receive+"</p></div><div class='yundan_2_1'><h3>"
							 +msg.orders[i].acceptcity+"</h3><p>"
							 +msg.orders[i].acceptname+"</p></div></div></div><div class='yundan_3'><div class='yundan_3_1'>"
							 +msg.orders[i].status+"</div></div><div class='xian'></div><div class='kongbai'></div>";
							if(msg.orders[i].fashou == 1) {
								$("#bo2").html(a);
								$(".box1").hide();
			                    $(".box2").show();
							} else {
								$("#bo1").html(a);
								$(".box2").hide();
			                    $(".box1").show();
							}
						};
						if($(".yundan_3_1").text() == "已签收") {
						$(".sta").css("color", "#000000");
					} else {
						$(".sta").css("color", "#F75000");
					}
					//点击事件
					$(".yundan").click(function() {
						var sendid = $(this).children().eq(0).children().eq(0).children().eq(0).text();
						alert(sendid);
					     window.location.href = "http://mooonhok-cloudware.daoapp.io/weixin/waybill_details.html?order_id="+sendid;
					});
					}
				},
				error: function(xhr) {
					alert("获取数据失败");
				}
			});
		});
	</script>
	<script>
		//扫一扫
	</script>
	<script>
		//我寄的
		$.ajax({
			url: "http://mooonhok-cloudware.daoapp.io/order.php/wx_orders_s",
			beforeSend: function(request) {
				request.setRequestHeader("tenant-id", "1");
			},
			dataType: 'json',
			type: 'post',
			contentType: "application/json;charset=utf-8",
			data: JSON.stringify({
				wx_openid: openid
			}),
			success: function(msg) {
				alert("我寄的" + msg.result + "////" + msg.desc + "////" + msg.orders[1].order_id);
				for(var i = 0; i < msg.orders.length; i++) {

					var a="<div class='xian'></div><div class='yundan'><div class='yundan_1'><p>运单号:<span>"
							 +msg.orders[i].order_id+"</span></p><p>订单价格:<span >"
							 +msg.orders[i].order_cost+"</span></p></div><div class='yundan_2'><div class='yundan_2_1'><h3>"
							 +msg.orders[i].sendcity+"</h3><p>"
							 +msg.orders[i].sendname+"</p></div><div class='yundan_2_2'><p class='sta'>"
							 +msg.orders[i].receive+"</p></div><div class='yundan_2_1'><h3>"
							 +msg.orders[i].acceptcity+"</h3><p>"
							 +msg.orders[i].acceptname+"</p></div></div></div><div class='yundan_3'><div class='yundan_3_1'>"
							 +msg.orders[i].status+"</div></div><div class='xian'></div><div class='kongbai'></div>";
					$("#bo1").append(a);
				};
					if($(".yundan_3_1").text() == "已签收") {
						$(".sta").css("color", "#000000");
					} else {
						$(".sta").css("color", "#F75000");
					}
					//点击事件
					$(".yundan").click(function() {
						var sendid = $(this).children().eq(0).children().eq(0).children().eq(0).text();
						alert(sendid);
					   window.location.href = "http://mooonhok-cloudware.daoapp.io/weixin/waybill_details.html?order_id="+sendid;
					});
			},
			error: function(xhr) {
				alert("获取数据失败");
			}
		});
	</script>
	<script>
		//我收的
		$.ajax({
			url: "http://mooonhok-cloudware.daoapp.io/order.php/wx_orders_r",
			beforeSend: function(request) {
				request.setRequestHeader("tenant-id", "1");
			},
			dataType: 'json',
			type: 'post',
			contentType: "application/json;charset=utf-8",
			data: JSON.stringify({
				wx_openid: openid
			}),
			success: function(msg) {
				alert("我收的" + msg.result + "////"+ msg.orders[0].order_id);
				for(var i = 0; i < msg.orders.length; i++) {

					var a="<div class='xian'></div><div class='yundan'><div class='yundan_1'><p>运单号:<span>"
							 +msg.orders[i].order_id+"</span></p><p>订单价格:<span >"
							 +msg.orders[i].order_cost+"</span></p></div><div class='yundan_2'><div class='yundan_2_1'><h3>"
							 +msg.orders[i].sendcity+"</h3><p>"
							 +msg.orders[i].sendname+"</p></div><div class='yundan_2_2'><p class='sta'>"
							 +msg.orders[i].receive+"</p></div><div class='yundan_2_1'><h3>"
							 +msg.orders[i].acceptcity+"</h3><p>"
							 +msg.orders[i].acceptname+"</p></div></div></div><div class='yundan_3'><div class='yundan_3_1'>"
							 +msg.orders[i].status+"</div></div><div class='xian'></div><div class='kongbai'></div>";
					$("#bo2").append(a);
				};
					if($(".yundan_3_1").text() == "已签收") {
						$(".sta").css("color", "#000000");
					} else {
						$(".sta").css("color", "#F75000");
					}
					//点击事件
					$(".yundan").click(function() {
						var sendid = $(this).children().eq(0).children().eq(0).children().eq(0).text();
						alert(sendid);
					     window.location.href = "http://mooonhok-cloudware.daoapp.io/weixin/waybill_details.html?order_id="+sendid;
					});
			},
			error: function(xhr) {
				alert("获取数据失败");
			}
		});
	</script>
	<script type="text/javascript">
		 wx.config({
        debug: true,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            'checkJsApi', 'scanQRCode'
        ]
    });
    wx.ready(function () { 
  document.querySelector('#saoman').onclick = function () {  
    wx.scanQRCode({  
      needResult: 1,  
      desc: 'scanQRCode desc',  
      success: function (res) {    
        alert(res.resultStr);
        var a=new Array();
        a=res.resultStr.split(",");
        alert(a[1]);
       window.location.href="http://mooonhok-cloudware.daoapp.io/weixin/waybill_details.html?order_id="+a[1];
      }  
    });  
  };  
});  
  
wx.error(function (res) {  
  //alert(res.errMsg);  
}); 
	</script>
</html>