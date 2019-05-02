<script type="text/javascript" src="{$smarty.const.SITE}resource/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/jquery.cropit.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/lightbox.min.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/public_add.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=ho6LXkYw6eWBzWFlPvcMpLhR"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">活动编辑</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="{$data.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">活动编辑</td>
             </tr>
             <tr>
                 <td style="text-align:center;">排序</td>
                 <td><input name="num" type="text" value="{$data.num}" style="width:40px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动标题</td>
                 <td><input name="title" type="text" value="{$data.title}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">价格费用</td>
                 <td><input name="price" type="text" value="{$data.price}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">开始时间</td>
                 <td><input name="created" type="text" value="{$data.created|date_format:'%Y-%m-%d'}" style="width:140px;">(格式：2015-04-02)</td>
             </tr>
             <tr>
                 <td style="text-align:center;">截止时间</td>
                 <td><input name="end_date" type="text" value="{$data.end_date|date_format:'%Y-%m-%d'}" style="width:140px;">(格式：2015-04-30)</td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动时间</td>
                 <td><input name="datetime" type="text" value="{$data.datetime}" style="width:140px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动内容</td>
                 <td><textarea id="editor" name="content" style="width:540px;height:800px;">{$data.content}</textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动地址</td>
                 <td><input id="address" name="address" type="text" value="{$data.address}" style="width:600px;">
                 	<div id="allmap"></div>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td><input id="lng" name="lng" type="text" value="{$data.lng}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td><input id="lat" name="lat" type="text" value="{$data.lat}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">上传活动图片<br/>(最小尺寸750x500)</td>
                 <td style="padding-left:30px;">
                 	<a id="eventimgtool" href="javascript:void(0);">显示上传工具</a>
                 	<div id="eventimgBox" style="display: none;">
	                 	<div class="image-eventer">
		                    <input name="file" type="file" style="width:240px;" class="cropit-image-input" />
		                    <div class="cropit-image-preview-container">
							    <div class="cropit-image-preview"></div>
							  </div>
							<div class="slider-wrapper"><span class="icon icon-image small-image"></span><input type="range" class="cropit-image-zoom-input" min="0" max="1" step="0.01"><span class="icon icon-image large-image"></span></div>
					    	<div class="eventimgBoxResize"><span>图片高度</span><input type="range" step="1" max="500" min="0" class="cropit-image-resize" value="0"></div>
					    </div>
	                 	<input type="button" value="上传裁剪图片" id="eventImg_add" />
	                 	<input type="button" value="上传原始图片" id="eventImg_add_nocut" />
                 	</div>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动图片</td>
                 <td >
	                 <ul id="eventimgs">
                                {section name=sec loop=$photo}
	                 		<li>
	                 			<a href="{$photo[sec].img}" data-lightbox="roadtrip"><img src="{$photo[sec].img}"></a><a class="delEventImg" rel="{$photo[sec].id}" href="javascript:void(0)">删 除</a>
	                 			<label><input type="radio" name="img" value="{$photo[sec].img}" {if $data.img eq $photo[sec].img} checked {/if} />作为主图</label>
	                 		</li>
             			{/section}
	             	</ul>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">是否发布</td>
                 <td>
                 	<label><input name="ispublic" type="radio" value="1" checked="checked">发布中</label>
                 	<label><input name="ispublic" type="radio" value="2" {if $data.ispublic eq 2}checked="checked"{/if} >不发布</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定修改 "></p>
         </form>
 	</div>       
 </td>