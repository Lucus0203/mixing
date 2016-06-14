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
<link rel="stylesheet" href="<?php echo base_url();?>css/default.css" type="text/css" />
<link rel="stylesheet" href="<?php echo base_url();?>css/meetdate.css" type="text/css" />
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.8.1.min.js"></script>
</head>
<body>
<div style="display: none;"><img src="<?php echo base_url();?>img/logo.png" /></div>
<article id="container">
	<div class="main">
            <form action="" method="post" onsubmit="return validateFrom()" >
                <input type="hidden" name="act" value="act" />
            <div class="chooseBox">
                <p>您的称呼<input id="from_user" type="text" name="from_user" /></p>
                <p>对方的称呼<input id="to_user" type="text" name="to_user" /></p>
                <p>第一次相遇时间是在</p>
                <p class="time">
                    <select name="year">
                        <?php for($i=1950;$i<=date('Y');$i++){ ?>
                        <option value="<?php echo $i ?>" <?php if($i==date('Y')){echo 'selected';} ?> ><?php echo $i ?></option>
                        <?php } ?>
                    </select>年
                    <select name="month">
                        <?php for($i=0;$i<12;$i++){ ?>
                        <option value="<?php echo $i ?>" <?php if($i==0){echo 'selected';} ?> ><?php echo $i*1+1 ?></option>
                        <?php } ?>
                    </select>月
                    <select name="day">
                        <?php for($i=1;$i<32;$i++){ ?>
                        <option value="<?php echo $i ?>" <?php if($i==1){echo 'selected';} ?> ><?php echo $i ?></option>
                        <?php } ?>
                    </select>日<br/>
                    <select name="hours">
                        <?php for($i=0;$i<24;$i++){ ?>
                        <option value="<?php echo $i ?>" <?php if($i==0){echo 'selected';} ?> ><?php echo $i ?></option>
                        <?php } ?>
                    </select>时
                    <select name="minutes">
                        <?php for($i=0;$i<60;$i++){ ?>
                        <option value="<?php echo $i ?>" <?php if($i==0){echo 'selected';} ?> ><?php echo $i ?></option>
                        <?php } ?>
                    </select>分
                    <select name="seconds">
                        <?php for($i=0;$i<60;$i++){ ?>
                        <option value="<?php echo $i ?>" <?php if($i==0){echo 'selected';} ?> ><?php echo $i ?></option>
                        <?php } ?>
                    </select>秒
                </p>
                <p>我想说的话(注意换行,方便阅读哦)</p>
                <p><textarea name="msg" maxlength="200"></textarea></p>
            </div>
                <div class="btn"><input class="button orange" value="确定" type="submit" /></div>
            </form>
            <p class="follow">关注微信公众号"咖啡约我"随时获得最新活动哦</p>
            <div class="kfyw"><img src="<?php echo base_url();?>img/kfyw.jpg" /></div>
	</div>
</article>
<script>
function validateFrom(){
    if($.trim($('#from_user').val())==''){
        alert('您的称呼是?');
        return false;
    }
    if($.trim($('#to_user').val())==''){
        alert('对方的称呼是?');
        return false;
    }
}
</script>
</body>
</html>
