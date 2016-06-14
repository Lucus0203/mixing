<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="description" content="">
<meta name="keywords" content="">
<title>胶片里的咖啡馆—搅拌APP</title>
<link rel="stylesheet" href="<?php echo base_url();?>css/default.css" type="text/css" />
</head>
<body>
    <div id="container">
    <link rel="stylesheet" href="<?php echo base_url();?>css/vote.css" type="text/css" />
    <div id="main">
        <p class="voted">恭喜您成功参加活动</p>
        <p class="join" style="font-size:1em;text-align: center;margin-top:20px; ">微信搜索"咖啡约我"查看实时投票结果<br>还可以分享给好友投票哦~</p><br/>
        <ul class="box1">
            <li class="first"><img src="<?php echo $d['img']; ?>" width="100%" /><span>参赛者:<font class="nickname"><?php echo $d['nick_name'] ?></font>&nbsp;编号:<?php echo $d['diary_id'] ?>&nbsp;票数:<font class="votenum"><?php echo number_format($d['num']) ?></font></span><p class="note"><br/><?php echo nl2br(preg_replace('/(＃胶片里的咖啡馆＃\s*)|(#胶片里的咖啡馆#\s*)|(#胶片里的咖啡厅#\s*)|(#胶片里的咖啡厅#\s*)/','店名:', $d['note'])) ?></li>
        </ul>
    </div>
</body>
</html>