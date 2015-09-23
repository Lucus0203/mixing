<script type="text/javascript" src="<?php echo base_url();?>js/jquery.cropit.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/lightbox.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/menu_add.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">咖啡甜品</div>
         <input type="hidden" name="act" value="edit" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">咖啡甜品</td>
             </tr>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">上传菜品<br>(图片大小414x380)</td>
                 <td style="padding-left:30px;">
                 	<a id="menuimgtool" href="javascript:void(0);">显示上传工具</a>
                 	<div id="menuimgBox" style="display: none;">
	                 	<div class="image-menuer">
		                    <input name="file" type="file" style="width:240px;" class="cropit-image-input" />
		                    <div class="cropit-image-preview-container">
							    <div class="cropit-image-preview"></div>
							  </div>
							<div class="slider-wrapper"><span class="icon icon-image small-image"></span><input type="range" class="cropit-image-zoom-input" min="0" max="1" step="0.01"><span class="icon icon-image large-image"></span></div>
					    </div>
	                 	菜品名称：<input type="text" id="menuTitle" style="margin-right: 20px;"/><input type="button" value="添加" id="menuImg_add" />
                 	</div>
                 </td>
             </tr>
         </table>
         <table id="menuList" class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="20%">
				<col width="15%">
				<col width="25%">
				<col width="10%">
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t">图片</td>
                 <td class="hd_ta_t">名称</td>
                 <td class="hd_ta_t">价格</td>
                 <td class="hd_ta_t">状态</td>
                 <td class="hd_ta_t">操作</td>
             </tr>
             
             <?php foreach ($menu as $m){ ?>
             <tr>
                 <td class="menu_img">
                 	<a href="<?php echo $m['img']?>" data-lightbox="menu-group"><img src="<?php echo $m['img']?>"></a>
	             </td>
                 <td class="menu_title"><?php echo $m['title']?></td>
                 <td>
	                 <ul class="menu_price">
	                 	<?php foreach ($m['prices'] as $p){ $typeflag=1;?>
	                 	<li>
	                 		价格:<input class="price" type="text" value="<?php echo $p['price'] ?>" style="ime-mode:disabled;" /> 
	                 		规格:<select class="type">
	                 			<option value="常规" <?php if($p['type']=='常规'){echo 'selected';$typeflag=2;} ?> >常规</option>
	                 			<option value="小" <?php if($p['type']=='小'){echo 'selected';$typeflag=2;} ?> >小</option>
	                 			<option value="中" <?php if($p['type']=='中'){echo 'selected';$typeflag=2;} ?> >中</option>
	                 			<option value="大" <?php if($p['type']=='大'){echo 'selected';$typeflag=2;} ?> >大</option>
	                 			<option value="超大" <?php if($p['type']=='超大'){echo 'selected';$typeflag=2;} ?> >超大</option>
	                 			<option value="自定义" <?php if($typeflag==1){echo 'selected';} ?> >自定义</option>
	                 		</select> <?php if($typeflag==1){?><input type="text" value="<?php echo $p['type'] ; ?>" /><?php } ?>	<a class="del" href="#">删除</a>
	                 	</li>
	                 	<?php } ?>
	                 	<li class="add"><a href="#">添加</a></li>
	                 </ul>
                 </td>
                 <td style="text-align:center;">
                 	<?php if($m['stauts']==1){?>待售<?php }else{ ?>寄售中<?php } ?>
                 </td>
                 <td class="opera">
                 	<a class="updatePrice" href="javascript:void(0)">更新价格</a>
                 	<?php if($m['stauts']==1){?>
                 		<a class="public" href="javascript:void(0)">上架</a>
                 	<?php }else{ ?>
                 		<a class="depublic" href="javascript:void(0)">下架</a>
                 	<?php } ?>
                 	<a class="delMenuImg" rel="<?php echo $m['id']?>" href="javascript:void(0)">删 除</a>
                 </td>
             </tr>
	         <?php } ?>
         </table>
 	</div>       
 </td>
