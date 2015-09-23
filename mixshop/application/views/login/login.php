<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>搅拌后台</title>
<link href="<?php echo base_url() ?>css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url() ?>js/jQuery.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/common.js"></script>
</head>
<body style="background:#977342;">
<div class="login_box">
	<div class="login_t">店家后台</div>
   <form action="" method="post">
    <div class="login_in">
	 <?php if (!empty($error_msg)){ ?>
    	<p style="color:red;margin-left:50px;"><?php echo $error_msg ?></p>
     <?php } ?>
    	<ul>
        	<li>账号：<input id="admname" name="username" type="text"></li>
            <li>密码：<input id="pass" name="password" type="password"></li>
        </ul>
    </div>
    <div class="login_btn"><input type="image" src="<?php echo base_url() ?>images/login_btn_03.jpg" /><input onclick="document.getElementById('admname').value='';document.getElementById('pass').value='';return false;" type="image" src="<?php echo base_url() ?>images/login_btn_05.jpg" /></div>
    <p class="register_link"><a href="<?php echo base_url() ?>login/register">点击这里注册</a></p>
    </form>
</div>

</body>
</html>
