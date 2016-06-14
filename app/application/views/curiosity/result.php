<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="description" content="">
<meta name="keywords" content="">
<title>内含福利！<?php echo $user['nickname'] ?>的好奇心指数居然是<?php echo $data['percent']>100?100:round($data['percent']) ?>%</title>
<link rel="shortcut icon" href="http://www.xn--8su10a.com/favicon.ico" />
<link rel="stylesheet" href="<?php echo base_url();?>css/curiosity.css" type="text/css" />
</head>
<body style="background: #fff;">
<audio id="musicBox" preload="metadata" controls src="<?php echo base_url();?>js/5018.mp3" autostart="0" style="display:none;opacity: 0"></audio>
<article id="container">
    <div class="main">
        <div class="resultBox">
            <canvas id='loadCanvas'></canvas>
            <img src="<?php echo $user['headimgurl']?>" />
            <span>0%</span>
            <p>
                <?php 
                    if($data['percent']<20){
                        echo '刚来就要走，客官好心急呦~';
                    }elseif ($data['percent']<30) {
                        if($user['sex']==2){//女
                            echo '后面可是让小编都弯了的帅哥啊！(¯﹃¯)';
                        }else{
                            echo '帅哥你是不是已经不喜欢女人了_( ﾟДﾟ)ﾉ';
                        }
                    }elseif ($data['percent']<55) {
                        echo '是不是看福利看的腾不出手来了(*^__^*) 。';
                    }elseif ($data['percent']<85) {
                        if($user['sex']==2){//女
                            echo '妹子你是不是完全不需要男朋友？(///∇//)';
                        }else{
                            echo '手酸啦？少年你要多锻炼啊(๑✦ˑ̫✦)';
                        }
                    }elseif ($data['percent']<100) {
                        echo '居然差一点不看完，你是要逼死强迫症的小编ᕙ(⇀‸↼‵‵)ᕗ';
                    }else{
                        echo '没啦没啦，擦擦口水吧(¯﹃¯) ';
                    }
                ?>
            </p>
        </div>
        <?php if(count($list)<=0){ ?>
        <p class="empty">暂时还没其他好友来玩,快去分享此页到朋友圈吧</p>
        <div class="btn resultBtn" style="width: 80%;margin: 5% 10%;"><a id="sharebtn" href="javascript:void(0);">分享到朋友圈</a></div>
        <?php }else{ ?>
        <div class="btn resultBtn" style="width: 80%;margin: 5% 10%;"><a id="sharebtn" href="javascript:void(0);">分享到朋友圈</a></div>
        <ul class="list">
            <li class="hli"><div class="li01">参与者</div><div class="li03">摇摇手速</div><div class="li02">指数</div></li>
           <?php foreach ($list as $k=>$o){ ?>
            <li><div class="li01"><span class="num"><?php echo $k*1+1; ?></span><img src="<?php echo $o['headimgurl'] ?>" width="40" /><?php echo $o['nickname'] ?></div><div class="li03"><?php echo round($o['shakeSpeed']) ?>下/秒</div><div class="li02"><?php echo $o['percent']>100?100:$o['percent'] ?>%</div></li>     
           <?php } ?>
        </ul>
        <?php } ?>
    </div>
    <footer>
        <div class="left"><img class="headimg" src="<?php echo $user['headimgurl']?>" width="50" /><div><a class="btn" href="<?php echo site_url('curiosity/uploadimg');?>">
                  生成我的主页&nbsp>
                </a><p>不满意?再试试</p></div></div>
        <div class="right"><a id="download" href="https://itunes.apple.com/app/jiao-ban/id1036717871?l=zh&ls=1&mt=8" target="_blank"><img src="<?php echo base_url();?>img/share_logo.png" /><div><p>搅拌APP<br><span>在咖啡馆遇见你</span></p></div><span class="arr">></span></a></div>
    </footer>
</article>
<div id="loading-bg"><img id="share_img"src="<?php echo base_url();?>img/share_img.png" /></div>
<script src="<?php echo base_url();?>js/jquery-1.8.1.min.js"></script>
<script src="<?php echo base_url();?>js/jquery.easing.min.js"></script>
<script>
//判断ios
if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {
    $('footer div.left').css('width','55%');
    $('footer div.right').show();
}
var percent=<?php echo $data['percent']; ?>;
var start=0;
function percentPlus(){
    start=start*1+0.1;
    start=start>=100?100:start.toFixed(1);
    if(start<=percent){
        $('div.resultBox span').html(start+'%');
    }
}
setInterval(percentPlus, 5);

//旋转效果
var W,H,
canvas = document.getElementById('loadCanvas'),
ctx = canvas.getContext('2d'),
hsl = 0,
angle = 0.01;
W = 300,
H = 300;
canvas.width = W;
canvas.height = H;

function paint(){
  angle += 0.02;
  hsl <= 360 ? hsl+=0.25 : hsl = 0;
  var s = -Math.sin(angle);
  var c = Math.cos(angle);

  ctx.save();
  ctx.globalAlpha = 0.5;
  ctx.beginPath();
  //ctx.fillStyle = 'hsla('+hsl+', 100%, 50%, 1)';
  ctx.fillStyle = '#81e2a3';
  ctx.arc(W/2+(s*145),H/2+(c*145),5,0,2*Math.PI);
  ctx.fill();
  ctx.restore();
}
setInterval(paint, 1);

$('#sharebtn').click(function(){
    $('#loading-bg').height($(document).height()).show();
    $('#share_img').show();
});
$('#loading-bg').height($(document).height());
$('#loading-bg').click(function(){$(this).hide();$('.chooseBox,#share_img').hide()});

$('#download').click(function(){
        if(navigator.userAgent.match(/MicroMessenger/i)){
            alert('请点击右上角更多,在Safari中打开本页')
        }
});
</script>
</body>
</html>
