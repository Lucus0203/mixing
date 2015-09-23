<script type="text/javascript" src="{$smarty.const.SITE}resource/js/master_index.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <input type="hidden" id="provinceApiURL" value="{url controller=Api action=GetCityByProvince}" />
         <input type="hidden" id="cityApiURL" value="{url controller=Api action=GetTownByCity}" />
         <div class="hd_t">咖啡店铺</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="Shop" />
         <input type="hidden" name="action" value="Index" />
         <div class="hd_t1">
         	<select name="province_id" class="province_id">
				<option value="">不限</option>
				{section name=sec loop=$provinces}
				<option value="{$provinces[sec].id}" {if $province_id eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
				{/section}
			</select>
			<select name="city_id" class="city_id">
				<option value="">不限</option>
				{section name=sec loop=$city}
				<option value="{$city[sec].id}" {if $city_id eq $city[sec].id}selected{/if}>{$city[sec].name}</option>
				{/section}
			</select>
			<select name="town_id" class="town_id">
				<option value="">不限</option>
				{section name=sec loop=$towns}
				<option value="{$towns[sec].id}" {if $town_id eq $towns[sec].id}selected{/if}>{$towns[sec].name}</option>
				{/section}
			</select>
			&nbsp;
			关键字<input class="cz_input" type="text" name="title" value="{$title}"><input class="cz_btn" type="submit" value="查找"></div>
         </form>
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="10%">
				<col width="15%">
				<col width="9%">
				<col width="20%">
				<col width="7%">
				<col width="17%">
				<col width="7%">
				<col width="7%">
				<col width="7%">
			</colgroup>
             <tr>
                 <th>缩略图</th>
                 <th>店铺名</th>
                 <th>电话</th>
                 <th>地点</th>
                 <th>坐标</th>
                 <th>简介</th>
                 <th>状态</th>
                 <th>内容</th>
                 <th>注册时间</th>
                 <th>注册手机</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td><a href="{url controller=Master action=ShopInfo shopid=$list[sec].id}">
                 	{if $list[sec].img neq ''}<img src="{$list[sec].img}">{else}<img src="{$smarty.const.SITE}resource/images/no_img.gif">{/if}
                 	</a>
                 </td>
                 <td class="hd_td_l">{$list[sec].title}</td>
                 <td>{$list[sec].tel}</td>
                 <td>{$list[sec].address}</td>
                 <td>{$list[sec].lng},<br/>{$list[sec].lat}</td>
                 <td>{$list[sec].introduction|substr:0:40}</td>
                 <td>
                 	{if $list[sec].status eq '1'}<font color="red">待审核</font>{else}审核通过{/if}
                 </td>
                 <td><a href="{url controller=Master action=ShopInfo shopid=$list[sec].id}">查看</a></td>
                 <td>{$list[sec].created}</td>
                 <td>{$list[sec].mobile}</td>
                 <td style="word-break:keep-all;">
                 	{if $list[sec].status neq '2'}<a class="pubBtn" href="{url controller=Master action=Pass shopid=$list[sec].id}">通过</a>{else}<a class="depubBtn" href="{url controller=Master action=DePass shopid=$list[sec].id}">再审核{/if}</a>
                 	{if $list[sec].shop_id neq ''}<br/><br/><a href="{url controller=Shop action=Edit id=$list[sec].shop_id}">编辑</a>{/if}
                 	
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>