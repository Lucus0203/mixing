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
        <p class="voted">活动已结束,感谢您的参与</p>
        <h1>"胶片里的咖啡馆"投票</h1>
        <p class="join">参赛拿大奖:请戳-><a href="http://mp.weixin.qq.com/s?__biz=MjM5MzU4MDUzMA==&mid=401971946&idx=1&sn=b5e4cc209d6af23a74e1b045e0589d40&scene=23&srcid=0121T1JBoOCHXlrOj2KmEJjG#rd">《胶片里的咖啡馆—搅拌APP》</a></p><br/>
        <form class="search" method="GET" action="">查找编号:<input type="tel" value="<?php if($votenum) echo $votenum; ?>" name="votenum" /><input class="subbtn" type="submit" value="确定" /></form>
        <?php echo $links; ?>
        <?php if(count($diarys)<=0){ echo '<p class="nocontent">很抱歉,未查找到您要的内容</p>'; }?>
        <ul class="box1">
            <?php foreach ($diarys as $k=>$d){ ?>
            <li class="first"><img src="<?php echo $d['img']; ?>" width="100%" /><span>参赛者:<font class="nickname"><?php echo $d['nick_name'] ?></font>&nbsp;编号:<?php echo $d['diary_id'] ?>&nbsp;票数:<font class="votenum"><?php echo number_format($d['num']) ?></font></span><p class="note"><br/><?php echo nl2br(preg_replace('/(＃胶片里的咖啡馆＃\s*)|(#胶片里的咖啡馆#\s*)|(#胶片里的咖啡厅#\s*)|(#胶片里的咖啡厅#\s*)/','店名:', $d['note'])) ?></p></li>
            <?php } ?>
        </ul>
        <?php echo $links; ?>
        <div id="footer" class="clearfix">
            <img width="75%" src="<?php echo base_url();?>img/download_vote.png">
        </div>
    </div>
</body>
</html>