<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="description" content="">
<meta name="keywords" content="">
<title>有话想对你说</title>
<link rel="shortcut icon" href="http://www.xn--8su10a.com/favicon.ico" />
<style type="text/css">
        @font-face {
                font-family: digit;
                src: url('/css/digital-7_mono.ttf') format("truetype");
        }
</style>
<link rel="stylesheet" href="<?php echo base_url();?>css/default.css" type="text/css" />
<link rel="stylesheet" href="<?php echo base_url();?>css/meetdate.css" type="text/css" />
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.8.1.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/garden_dev.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/meetdate_dev.js"></script>
</head>
<body style="max-width:400px;">
<div style="display: none;"><img src="<?php echo base_url();?>img/logo.png" /></div>
	<div id="mainDiv">
		<div id="content">
                    <div id="code"><?php echo nl2br($data['msg']); ?>
		  </div>
			<div id="loveHeart">
				<canvas id="garden"></canvas>
                                <div id="words">&nbsp;
					<div id="messages">
						<?php echo $data['to_user'] ?>,我们已经相识了
					  <div id="elapseClock"></div>
					</div>
					<div id="loveu">
                                                一直有话想对你说的<br/>
						<div class="signature">- <?php echo $data['from_user'] ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var offsetX = $("#loveHeart").width() / 2;
		var offsetY = $("#loveHeart").height() / 2 - 55;
		var displayMode = 1;
		var together = new Date();
		together.setFullYear(<?php echo $data['year'] ?>, <?php echo $data['month'] ?>, <?php echo $data['day'] ?>);
		together.setHours(<?php echo $data['hours'] ?>);
		together.setMinutes(<?php echo $data['minutes'] ?>);
		together.setSeconds(<?php echo $data['seconds'] ?>);
		together.setMilliseconds(0);
		
		$("#loveHeart").click(function(){
			displayMode *= -1;
			timeElapse(together, displayMode);
		});
		
		if (!document.createElement('canvas').getContext) {
			var msg = document.createElement("div");
			msg.id = "errorMsg";
			msg.innerHTML = "Your browser doesn't support HTML5!<br/>Recommend use Chrome 14+/IE 9+/Firefox 7+/Safari 4+"; 
			document.body.appendChild(msg);
			$("#code").css("display", "none");
		    document.execCommand("stop");
		} else {
			setTimeout(function () {
				adjustWordsPosition();
				startHeartAnimation();
			}, 1000);

			timeElapse(together, displayMode);
			setInterval(function () {
				timeElapse(together, displayMode);
			}, 500);

			adjustCodePosition();
			$("#code").typewriter();
		}
	</script>
        
    <footer>
        <div class="left"><a class="btn" href="<?php echo site_url('meetdate/create');?>">
                我有想说的话&nbsp>
                </a></div>
        <div class="right"><a id="download" href="https://itunes.apple.com/app/jiao-ban/id1036717871?l=zh&ls=1&mt=8" target="_blank"><img src="<?php echo base_url();?>img/share_logo.png" /><div><p>搅拌APP<br><span>在咖啡馆遇见你</span></p></div><span class="arr">></span></a></div>
    </footer>
<script>
//判断ios
if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {
    $('footer div.left').css('width','55%');
    $('footer div.right').show();
}
$('#download').click(function(){
        if(navigator.userAgent.match(/MicroMessenger/i)){
            alert('请点击右上角更多,在Safari中打开本页')
        }
});
</script>
</body></html>
