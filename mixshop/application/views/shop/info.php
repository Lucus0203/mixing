<script type="text/javascript" src="<?php echo base_url();?>js/jquery.cropit.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/lightbox.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/shop_add.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=ho6LXkYw6eWBzWFlPvcMpLhR"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">店铺内容</div>
         <?php if($msg!=''){?>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;"><?php echo $msg; ?></p>
         <?php } ?>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="<?php echo $data['id'] ?>" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">店铺内容</td>
             </tr>
             <tr>
                 <td style="text-align:center;">店铺名称</td>
                 <td><input name="title" type="text" value="<?php echo $data['title'] ?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">店铺别名(选填)</td>
                 <td><input name="subtitle" type="text" value="<?php echo $data['subtitle'] ?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">上传店铺图片<br/>(最小尺寸750x500)</td>
                 <td style="padding-left:30px;">
                 	<a id="shopimgtool" href="javascript:void(0);">显示上传工具</a>
                 	<div id="shopimgBox" style="display: none;">
                 		<div class="image-shoper">
		                    <input name="file" type="file" style="width:240px;" class="cropit-image-input" />
		                    <div class="cropit-image-preview-container">
							    <div class="cropit-image-preview"></div>
							  </div>
							<div class="slider-wrapper"><span class="icon icon-image small-image"></span><input type="range" class="cropit-image-zoom-input" min="0" max="1" step="0.01"><span class="icon icon-image large-image"></span></div>
					    	<div class="shopimgBoxResize"><span>图片高度</span><input type="range" step="1" max="500" min="0" class="cropit-image-resize" value="0"></div>
	                 	</div>
	                 	<input type="button" value="上传图片" id="shopImg_add" />
                 	</div>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">店铺图片</td>
                 <td>
	                 <ul  id="shopimgs">
	             		<?php foreach ($shopimg as $img){ ?>
	                 		<li>
	                 			<a href="<?php echo $img['img']?>" data-lightbox="roadtrip"><img src="<?php echo $img['img']?>"></a><a class="delShopImg" rel="<?php echo $img['id']?>" href="javascript:void(0)">删 除</a>
	                 			<label><input type="radio" name="img" value="<?php echo $img['img']?>" <?php if($data['img']==$img['img']){ echo 'checked';} ?> />作为主图</label>
	                 		</li>
	             		<?php } ?>
	             	</ul>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">营业时间</td>
                 <td>
                 	<select name="hours1" >
                 		<?php for ($i=0;$i<24;$i++){
                 			$h=$i<10?'0'.$i:$i;
                 		?>
                 		<option value="<?php echo $h; ?>" <?php if($h==$data['hours1']){?>selected <?php }?> ><?php echo $h; ?></option>
                 		<?php } ?>
                 	</select>
                 	:
                 	<select name="minutes1">
                 		<option value="00" <?php if('00'==$data['minutes1']){?>selected <?php }?>>00</option>
                 		<option value="30" <?php if('30'==$data['minutes1']){?>selected <?php }?>>30</option>
                 	</select>
                 	~
                 	<select name="hours2">
                 		<?php for ($i=0;$i<24;$i++){
                 			$h=$i<10?'0'.$i:$i;
                 		?>
                 		<option value="<?php echo $h; ?>" <?php if($h==$data['hours2']){?>selected <?php }?> ><?php echo $h; ?></option>
                 		<?php } ?>
                 	</select>
                 	:
                 	<select name="minutes2">
                 		<option value="00" <?php if('00'==$data['minutes2']){?>selected <?php }?> >00</option>
                 		<option value="30" <?php if('30'==$data['minutes2']){?>selected <?php }?> >30</option>
                 	</select>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">休息日</td>
                 <td>
                 	<label><input type="radio" name="holidayflag" value="1" checked />无休</label><label><input type="radio" name="holidayflag" <?php if($data['holidayflag']==2){ ?>checked<?php } ?> value="2" />休息日</label><label><input type="radio" name="holidayflag" <?php if($data['holidayflag']==3){ ?>checked<?php } ?> value="3" />休息日营业时间</label>
                 	<ul class="holidays" <?php if($data['holidayflag']==2||$data['holidayflag']==3){ ?>style="display: block;"<?php } ?> >
                 		<li><label><input name="holidays[]" type="checkbox" <?php if(strpos($data['holidays'],'1') !== false){?>checked<?php } ?> value="1">一</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" <?php if(strpos($data['holidays'],'2') !== false){?>checked<?php } ?> value="2">二</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" <?php if(strpos($data['holidays'],'3') !== false){?>checked<?php } ?> value="3">三</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" <?php if(strpos($data['holidays'],'4') !== false){?>checked<?php } ?> value="4">四</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" <?php if(strpos($data['holidays'],'5') !== false){?>checked<?php } ?> value="5">五</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" <?php if(strpos($data['holidays'],'6') !== false){?>checked<?php } ?> value="6">六</label></li>
                 		<li><label><input name="holidays[]" type="checkbox" <?php if(strpos($data['holidays'],'0') !== false){?>checked<?php } ?> value="0">日</label></li>
                 	</ul>
                 	<div class="holidaytime" <?php if($data['holidayflag']==3){ ?>style="display: block;"<?php } ?> >
	                 	<select name="holidayhours1">
	                 		<?php for ($i=0;$i<24;$i++){
	                 			$h=$i<10?'0'.$i:$i;
	                 		?>
	                 		<option value="<?php echo $h; ?>" <?php if($h==$data['holidayhours1']){?>selected <?php }?> ><?php echo $h; ?></option>
	                 		<?php } ?>
	                 	</select>
	                 	:
	                 	<select name="holidayminutes1">
	                 		<option value="00" <?php if('00'==$data['holidayminutes1']){?>selected <?php }?> >00</option>
	                 		<option value="30" <?php if('30'==$data['holidayminutes1']){?>selected <?php }?> >30</option>
	                 	</select>
	                 	~
	                 	<select name="holidayhours2">
	                 		<?php for ($i=0;$i<24;$i++){
	                 			$h=$i<10?'0'.$i:$i;
	                 		?>
	                 		<option value="<?php echo $h; ?>" <?php if($h==$data['holidayhours2']){?>selected <?php }?> ><?php echo $h; ?></option>
	                 		<?php } ?>
	                 	</select>
	                 	:
	                 	<select name="holidayminutes2">
	                 		<option value="00" <?php if('00'==$data['holidayminutes2']){?>selected <?php }?> >00</option>
	                 		<option value="30" <?php if('30'==$data['holidayminutes2']){?>selected <?php }?> >30</option>
	                 	</select>
	                 </div>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">电话</td>
                 <td><input name="tel" type="text" value="<?php echo $data['tel']?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">城市区域</td>
                 <td>
	                <select name="province_id" class="province_id">
	                	<?php foreach ($provinces as $p){ ?>
							<option value="<?php echo $p['id'] ?>" <?php if($data['province_id']==$p['id']){?>selected<?php } ?> > <?php echo $p['name'] ?></option>
						<?php } ?>
					</select>
	                <select name="city_id" class="city_id">
	                	<?php foreach ($cities as $c){ ?>
							<option value="<?php echo $c['id'] ?>" <?php if ($data['city_id']==$c['id']){ ?>selected<?php } ?> > <?php echo $c['name'] ?></option>
						<?php } ?>
					</select>
					<select name="town_id" class="town_id">
	                	<?php foreach ($towns as $t){ ?>
						{section name=sec loop=$towns}
							<option value="<?php echo $t['id'] ?>" <?php if ($data['town_id']==$t['id']){ ?>selected<?php } ?> > <?php echo $t['name'] ?></option>
						{/section}
	                	<?php } ?>
					</select>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">地址</td>
                 <td><input id="address" name="address" type="text" value="<?php echo $data['address']?>" style="width:600px;">
                 	<div id="allmap"></div>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td><input id="lng" name="lng" type="text" value="<?php echo $data['lng']?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td><input id="lat" name="lat" type="text" value="<?php echo $data['lat']?>" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">特色</td>
                 <td>
                 <?php foreach ($tags as $tag){ ?>
                 	<label><input name="features[]" type="checkbox" <?php echo $tag['checked'] ?> value="<?php echo $tag['tag'] ?>"><?php echo $tag['tag'] ?></label>&nbsp;
                 <?php } ?>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">简介</td>
                 <td><textarea name="introduction" style="width:640px;height:250px;"><?php echo $data['introduction']?></textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">是否发布</td>
                 <td>
                 	<label><input name="status" type="radio" value="1" checked="checked">准备中</label>
                 	<label><input name="status" type="radio" value="2" <?php if ($data['status']==2){ ?>checked="checked"<?php } ?> >发布中</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定操作 "></p>
         </form>
 	</div>       
 </td>
