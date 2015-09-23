<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>搅拌后台</title>
<link href="<?php echo base_url();?>css/lightbox.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url();?>css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url();?>js/jQuery.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/common.js"></script>
</head>
<body>
   <input type="hidden" id="baseUrl" value="<?php echo base_url() ?>" />
<div class="top">
	<div class="fl"><img src="<?php echo base_url();?>images/weblogo.png" height="66" style="margin-top:5px;"></div>
    <div class="fr top_fr">欢迎您 <?php echo $loginInfo['user_name'] ?><a href="<?php echo base_url();?>login/loginout.html">[ 退出系统 ]</a></div>
</div>
<div class="main_box">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
    	<tr>