<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="description" content="">
<meta name="keywords" content="">
<title>试试你的好奇心指数</title>
<link rel="shortcut icon" href="http://www.xn--8su10a.com/favicon.ico" />
<link rel="stylesheet" href="<?php echo base_url();?>css/curiosity.css" type="text/css" />
</head>
<body style="background: #81e2a3;">
<input type="hidden" id="baseUrl" value="<?php echo base_url(); ?>" />
<div style="display: none;"><img src="<?php echo $user['headimgurl']; ?>" /></div>
<audio id="musicBox" preload="metadata" controls src="<?php echo base_url();?>js/5018.mp3" autostart="0" style="display:none;"></audio>
<article id="container">
	<div class="main">
            <p class="shack_img"><img src="<?php echo base_url();?>img/shake_img.png" width="15%" /><span id="visibility">摇一摇</span></p>
		<div class="photo">
			<img src="/uploads/<?php echo $obj['img'] ?>" />
			<div class="fragment">
				<div id="fragBox01">
					<div class="frag01"></div>
					<div class="frag02"></div>
					<div class="frag03"></div>
					<div class="frag04"></div>
					<div class="frag05"></div>
					<div class="frag06"></div>
					<div class="frag07"></div>
					<div class="frag08"></div>
					<div class="frag09"></div>
					<div class="frag10"></div>
					<div class="frag11"></div>
					<div class="frag12"></div>
					<div class="frag13"></div>
					<div class="frag14"></div>
					<div class="frag15"></div>
				</div>
			</div>
		</div>
            <div class="chooseBox">
                <p>听说后面有福利哟~</p>
                <div class="btn"><a href="<?php echo site_url('curiosity/showsecond/'.$obj['id']);?>">看福利</a></div>
                <div class="btn"><a class="seeResult" href="#">看结果</a></div>
            </div>
            <div class="btn resultBtn" style="display: none;"><a href="<?php echo site_url('curiosity/showsecond/'.$obj['id']);?>">看福利</a></div>
            <div class="btn resultBtn" style="display: none;"><a class="seeResult" href="#">看结果</a></div>
	</div>
</article>
<div id="loading-bg"><img id="share_img"src="<?php echo base_url();?>img/share_img.png" /></div>
<script src="<?php echo base_url();?>js/jquery-1.8.1.min.js"></script>
<script src="<?php echo base_url();?>js/jquery.easing.min.js"></script>
<script>
var media = document.getElementById("musicBox");
var shakeSpeed=0;
var shake=0;
var visibleNum=$('.fragment div:visible').length;//可见的块数
var timestart=0;
if (window.DeviceMotionEvent) { 
    window.addEventListener('devicemotion',deviceMotionHandler, false);  
}
var speed = 15;//speed
var x = y = z = lastX = lastY = lastZ = 0;
function deviceMotionHandler(eventData) {  
  var acceleration =eventData.accelerationIncludingGravity;
                x = acceleration.x;
                y = acceleration.y;
                z = acceleration.z;
                if(Math.abs(x-lastX) > speed || Math.abs(y-lastY) > speed || Math.abs(z-lastZ) > speed) {
                        var myDate=new Date()
                        timestart=timestart==0?myDate.getTime():timestart;
                        shake+=1;
                        if(shake>5){
                                shakeSpeed=shake/2/((myDate.getTime()-timestart)/1000);
                        }
                        //简单的摇一摇触发代码
                        //media.currentTime = 0;
                        if($('#visibility').html()!='100%'){
                            media.play();
                            var num=$('.fragment div:visible').length;
                            $('.fragment div:visible').eq(Math.floor(Math.random()*num)).animate({top:"+="+lastY*10+"px",left:"+="+lastX*10+"px",opacity:0},2000,'easeOutQuint',function(){
                                    $(this).hide();
                                    var visib=Math.round((1-$('.fragment div:visible').length/visibleNum)*100);
                                    $('#visibility').html(visib+'%');
                                    if(visib==100){
                                        $('#loading-bg,.chooseBox,.resultBtn').show();
                                        $('#loading-bg').height($(document).height());
                                    }
                                    media.stop();
                            });
                        }
                }
}

$('#loading-bg').height($(document).height());
$('#loading-bg').click(function(){$(this).hide();$('.chooseBox,#share_img').hide()});

var seeresultflag=true;
$('.seeResult').click(function(){
    seeresultflag=false;
    var baseUrl=$('#baseUrl').val();
    var percent=Math.round((1-$('.fragment div:visible').length/visibleNum)*100);
    window.location=baseUrl+'curiosity/seeresult/'+percent+'/'+Math.round(shakeSpeed)+'/'+shake/2+'/'+1;
    return false;
});
$(window).unload(function(){
    if(seeresultflag){
        var baseUrl=$('#baseUrl').val();
        var percent=Math.round((1-$('.fragment div:visible').length/visibleNum)*100);
        $.ajax({
                type:'post',
                url:baseUrl+'curiosity/upcuriositydata',
                async:false,
                data:{'percent':percent,'shakeSpeed':Math.round(shakeSpeed),'shakeCount':shake/2,'step':1},
                success:function(res){
                        if(res==1){
                            res='success';
                        }
                }
        })
    }
});
</script>
</body>
</html>
