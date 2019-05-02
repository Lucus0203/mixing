<script type="text/javascript" src="{$smarty.const.SITE}resource/js/lightbox.min.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/master_shopinfo.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=ho6LXkYw6eWBzWFlPvcMpLhR"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <input type="hidden" id="provinceApiURL" value="{url controller=Api action=GetCityByProvince}" />
         <input type="hidden" id="cityApiURL" value="{url controller=Api action=GetTownByCity}" />
         <div class="hd_t">店铺编辑</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="{$data.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">店铺编辑</td>
             </tr>
             <tr>
                 <td style="text-align:center;">店铺名称</td>
                 <td>{$data.title}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">别名</td>
                 <td>{$data.subtitle}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">店铺图片</td>
                 <td>
	                 <ul  id="shopimgs">
             			{section name=spi loop=$shopimg}
	                 		<li>
	                 			<a href="{$shopimg[spi].img}" data-lightbox="roadtrip"><img src="{$shopimg[spi].img}"></a>
	                 			<label>{if $data.img eq $shopimg[spi].img}<input type="radio" checked />为主图{/if}</label>
	                 		</li>
             			{/section}
	             	</ul>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">营业时间</td>
                 <td>
                 	{$data.hours}(旧数据需要)<br/>
                 	{$data.hours1}~{$data.hours2}<br/>
                 	{if $data.holidayflag eq 2 or $data.holidayflag eq 3}
                 		休息日:
                 		{if $data.holidays|strpos:"1" !== false}一{/if}
                 		{if $data.holidays|strpos:"2" !== false}二{/if}
                 		{if $data.holidays|strpos:"3" !== false}三{/if}
                 		{if $data.holidays|strpos:"4" !== false}四{/if}
                 		{if $data.holidays|strpos:"5" !== false}五{/if}
                 		{if $data.holidays|strpos:"6" !== false}六{/if}
                 		{if $data.holidays|strpos:"0" !== false}日{/if}
                 	{/if}
                 	{if $data.holidayflag eq 3}
                 		额外营业时间:{$data.holidayhours1}~{$data.holidayhours2}
                 	{/if}
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">电话</td>
                 <td>{$data.tel}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">城市区域</td>
                 <td>
                 	{$data.province}{$data.city}{$data.town}
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">地址</td>
                 <td>{$data.address}
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td>{$data.lng}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td>{$data.lat}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">特色</td>
                 <td>
                 	{$data.feature}
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">简介</td>
                 <td>{$data.introduction}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">菜品</td>
                 <td>
	                 <ul  id="menuimgs">
           				{section name=sec loop=$menu}
	                 		<li>
	                 			<a href="{$menu[sec].img}" data-lightbox="menu-group"><img src="{$menu[sec].img}"></a>
	                 			<label>{$menu[sec].title}</label>
	                 		</li>
             			{/section}
	             	</ul>
                 </td>
             </tr>
         </table>
         <div class="hd_t">店主信息</div>
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">店主信息</td>
             </tr>
             <tr>
                 <td style="text-align:center;">店主姓名</td>
                 <td>{$masterinfo.name}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">联系电话</td>
                 <td>{$masterinfo.tel}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">QQ</td>
                 <td>{$masterinfo.qq}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">微信号</td>
                 <td>{$masterinfo.weixin}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">身份证<br/>(照片或者扫描,文件格式gif|jpg|png|jpeg)<span class="red">*</span></td>
                 <td>
                 	{if $masterinfo.idfile neq '' }<a href="{$masterinfo.idfile}" data-lightbox="idfile"><img src="{$masterinfo.idfile}" height="200" /></a>{/if}
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">营业执照<br/>(必须和本人有关,文件格式gif|jpg|png|jpeg)<span class="red">*</span></td>
                 <td>
                 	{if $masterinfo.business_license neq '' }<a href="{$masterinfo.business_license}" data-lightbox="business_license"><img src="{$masterinfo.business_license}" height="200" /></a>{/if}
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">审核状态</td>
                 <td>
                 	<label><input name="status" type="radio" value="1" checked="checked">再审核</label>
                 	<label><input name="status" type="radio" value="2" {if $data.ispassed eq 1}checked="checked"{/if} >通过</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确  定 "></p>
         </form>
 	</div>       
 </td>