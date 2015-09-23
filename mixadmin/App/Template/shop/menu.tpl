<script type="text/javascript" src="{$smarty.const.SITE}resource/js/jquery.cropit.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/lightbox.min.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/menu_add.js"></script>
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
                 <td style="text-align:center;">店铺信息</td>
                 <td style="padding-left:30px;"><a href="{url controller=Shop action=Edit id=$shopid}">返回店铺编辑</a><input id="shopid" type="hidden" value="{$shopid}" /></td>
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
             
             {section name=sec loop=$menu}
             <tr>
                 <td class="menu_img">
                 	<a href="{$menu[sec].img}" data-lightbox="menu-group"><img src="{$menu[sec].img}"></a>
	             </td>
                 <td class="menu_title">{$menu[sec].title}</td>
                 <td>
	                 <ul class="menu_price">
	                 	{section name=psec loop=$menu[sec].prices}
	                 	{assign var="typeflag" value="1"}
	                 	<li>
	                 		价格:<input class="price" type="text" value="{$menu[sec].prices[psec].price}" style="ime-mode:disabled;" /> 
	                 		规格:<select class="type">
	                 			<option value="常规" {if $menu[sec].prices[psec].type eq '常规'}{assign var="typeflag" value="2"}selected{/if} >常规</option>
	                 			<option value="小" {if $menu[sec].prices[psec].type eq '小'}{assign var="typeflag" value="2"}selected{/if} >小</option>
	                 			<option value="中" {if $menu[sec].prices[psec].type eq '中'}{assign var="typeflag" value="2"}selected{/if} >中</option>
	                 			<option value="大" {if $menu[sec].prices[psec].type eq '大'}{assign var="typeflag" value="2"}selected{/if} >大</option>
	                 			<option value="超大" {if $menu[sec].prices[psec].type eq '超大'}{assign var="typeflag" value="2"}selected{/if} >超大</option>
	                 			<option value="自定义" {if $typeflag eq '1'}selected{/if} >自定义</option>
	                 		</select> {if $typeflag eq '1'} <input type="text" value="{$menu[sec].prices[psec].type}" />{/if}	<a class="del" href="#">删除</a>
	                 	</li>
	                 	{/section}
	                 	<li class="add"><a href="#">添加</a></li>
	                 </ul>
                 </td>
                 <td style="text-align:center;">
                 	{if $menu[sec].status eq 1}待售{else}寄售中{/if}
                 </td>
                 <td class="opera">
                 	<a class="updatePrice" href="javascript:void(0)">更新价格</a>
                 	{if $menu[sec].status eq 1}
                 		<a class="public" href="javascript:void(0)">上架</a>
                 	{else}
                 		<a class="depublic" href="javascript:void(0)">下架</a>
                 	{/if}
                 	<a class="delMenuImg" rel="{$menu[sec].id}" href="javascript:void(0)">删 除</a>
                 </td>
             </tr>
	         {/section}
         </table>
 	</div>       
 </td>
