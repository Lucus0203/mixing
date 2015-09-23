<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$smarty.const.DEFAUT_TITLE}</title>
<link href="{$smarty.const.SITE}resource/css/lightbox.css" rel="stylesheet" type="text/css">
<link href="{$smarty.const.SITE}resource/css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/jQuery.js"></script>
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/common.js"></script>
</head>
<body>
<input type="hidden" value="{$smarty.const.SITE}" id="baseUrl" />
<div class="top">
	<div class="fl"><img src="resource/images/weblogo.png" height="66" style="margin-top:5px;"></div>
    <div class="fr top_fr">欢迎您 admin 管理员<a href="{url controller=Default action=LoginOut}">[ 退出系统 ]</a></div>
</div>
