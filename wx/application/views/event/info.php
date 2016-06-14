<link rel="stylesheet" href="<?php echo base_url();?>css/eventDetail.css" type="text/css" />
<script src="<?php echo base_url();?>js/eventDetail.js?100801"></script>
<div id="header">
    <img src="<?php echo $event['img']?>" width="420">
        <?php foreach ($event['imgs'] as $img){ ?>
    <img src="<?php echo $img['img']?>" width="420">
        <?php } ?>
    </div>
<div id="main">
    <h2 class="title"><?php echo $event['title'] ?></h2>
<p><h2><?php echo nl2br($event['content']) ?></h2></p>
<table>
    <tr>
            <td><h2>费用：<?php echo $event['price'] ?></h2></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><h2>时间：<?php echo $event['datetime'] ?></h2></td>
        <td></td>
        <td></td>
    </tr>
    <tr id="gotomap">
        <td><h2><a rel="http://api.map.baidu.com/marker?location=<?php echo $event['lat'] ?>,<?php echo $event['lng'] ?>&title=<?php echo urlencode($event['title']) ?>&content=<?php echo urlencode($event['title']) ?>&output=html&src=搅拌" target="_blank"><?php echo $event['address'] ?></a></h2></td>
        <td></td>
        <td><img src="<?php echo base_url();?>img/jiantou.png" alt="" /></td>
    </tr>
   </table> 

</div>