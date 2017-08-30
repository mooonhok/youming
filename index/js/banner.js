/*
* @Author: Administrator
* @Date:   2017-08-29 12:00:01
* @Last Modified by:   Administrator
* @Last Modified time: 2017-08-29 15:12:00
*/

$(function(){

var num = 1;
var width = $(".banner_box .imgs img").width();
var count = $(".banner_box .imgs img").length
var timer = null;

points(0);
	
function points(index){
		$(".banner_box .points span").eq(index).addClass("current").siblings('span').removeClass('current');
		}
	
	$(".right_btn").click(function(){
		
		if(!$(".banner_box .imgs").is(":animated")){
		if(num == count){
			$(".banner_box .imgs").animate({"margin-left":0});
			num=1;
		}else{
			$(".banner_box .imgs").animate({"margin-left":"-="+width});
		num++;
		}
		points(num-1);
	}

	})
	
	// 3.点击左按钮
	$(".left_btn").click(function(){

		if(!$(".banner_box .imgs").is(":animated")){
		if(num == 1){
			$(".banner_box .imgs").animate({"margin-left":"-"+width*(count-1)});
			num=count;
		}else{
			$(".banner_box .imgs").animate({"margin-left":"+="+width});
		num--;
		}
		points(num-1);
	}

	})
	
	// 4.自动轮播	
	timer=setInterval("$('.right_btn').click()",2000);
	$(".banner_box").mouseover(function(){
		clearInterval(timer);
	}).mouseout(function(){
		timer=setInterval("$('.right_btn').click()",2000);
	})
	
	$(".banner_box .points span").mouseover(function(){
		if(!$(".banner_box .imgs").is(":animated")){
		var index = $(this).index();
		console.log(index);
		
		$(".banner_box .imgs").animate({"margin-left":"-"+width*index});
		points(index);
		num=index+1;
		}
	});

})