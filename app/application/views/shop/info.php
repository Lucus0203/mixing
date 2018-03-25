<link rel="stylesheet" href="<?php echo base_url();?>css/shopDetail.css" type="text/css" />
<script src="<?php echo base_url();?>js/shopDetail.js?180325"></script>
<div id="header">
        <?php foreach ($shopimg as $img){ ?>
    <img src="<?php echo $img['img']?>" width="420">
        <?php } ?>
</div>
<div id="main">
    	<div class="page01 bottomLine">
        	<h2><?php echo $shop['title'] ?></h2>
            <div class="zhuangtai">
                <?php if($shop['isopen']==1){ ?>
                    <img src="<?php echo base_url();?>img/open.png" alt=" " />
                <?php }else{ ?>
                    <img src="<?php echo base_url();?>img/close.png" alt=" " />
                <?php } ?>
            </div>
                <p class="ourb"><?php echo nl2br($shop['introduction']); ?></p>
        </div>
    	<div class="biao bottomLine">
        <table>
                <tr>
                        <td style="width:auto;">
                        <img src="<?php echo base_url();?>img/time.png"  alt=" "/>
                        </td>
                        <td>
                        <p><?php echo $shop['hours'] ?></p>
                        </td>
                        <td></td>
                </tr>
		<tr>
			<td>
                        <img src="<?php echo base_url();?>img/phone.png"  alt=" "/>
                        </td>
			<td>
                        <p><?php echo $shop['tel'] ?></p>
                        </td>
                        <td></td>
		</tr>
     
        <tr id="gotomap">
			<td>
        	<img src="<?php echo base_url();?>img/gps.png"  alt=" "/>
        	</td>
			<td>
                            <p><a rel="http://api.map.baidu.com/marker?location=<?php echo $shop['lat'] ?>,<?php echo $shop['lng'] ?>&title=<?php echo urlencode($shop['title']) ?>&content=<?php echo urlencode($shop['title']) ?>&output=html&src=搅拌" target="_blank"><?php echo $shop['address'] ?></a>
           	  </p>
        	</td>
            <td>
            	<img src="<?php echo base_url();?>img/jiantou.png"  alt=" "/>
            </td>
		</tr>
		</table>
  </div>
  <div class="ourc bottomLine clearfix">
        	<div class="page02">
            <img src="<?php echo base_url();?>img/biaoqian.png" alt="" />
       	   
        	<ul>
                <?php foreach ($shop['features'] as $value) { ?>
                <li><?php echo $value ?></li>
                <?php } ?>
        	</ul>
            </div>
  </div>
    <?php if(!empty($shop['menus'])){ ?>
  <div class="foods">
    	<ul>
            <?php foreach ($shop['menus'] as $m) { ?>
            <li><img src="<?php echo $m['img'] ?>" alt=" " height="380"/>
                <p><?php echo $m['title'] ?></p>
                </li>
            <?php } ?>
            
            <?php 
            if(count($shop['menus']>1)){
            foreach ($shop['menus'] as $m) { ?>
                <li><img src="<?php echo $m['img'] ?>" alt=" " height="380"/>
                <p><?php echo $m['title'] ?></p>
                </li>
            <?php }
            }?>
         </ul>
    		
    </div>
    <?php } ?>