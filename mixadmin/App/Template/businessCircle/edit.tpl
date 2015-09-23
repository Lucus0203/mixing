<script type="text/javascript" src="{$smarty.const.SITE}resource/js/business_circle.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <input type="hidden" id="provinceApiURL" value="{url controller=Api action=GetCityByProvince}" />
         <input type="hidden" id="cityApiURL" value="{url controller=Api action=GetTownByCity}" />
         <div class="hd_t">商圈</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="{$data.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">编辑商圈</td>
             </tr>
             <tr>
                 <td style="text-align:center;">城市区域</td>
                 <td>
	                <select name="province_id" class="province_id">
                            {section name=sec loop=$provinces}
                                    <option value="{$provinces[sec].id}" {if $city_data.province_id eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
                            {/section}
                        </select>
	                <select name="city_id" class="city_id">
                            <option value="">不限</option>
                            {section name=sec loop=$city}
                                    <option value="{$city[sec].id}" {if $data.city_id eq $city[sec].id}selected{/if}>{$city[sec].name}</option>
                            {/section}
                        </select>
	                <select name="area_id" class="area_id">
                            <option value="">不限</option>
                            {section name=sec loop=$area}
                                    <option value="{$area[sec].id}" {if $data.area_id eq $area[sec].id}selected{/if}>{$area[sec].name}</option>
                            {/section}
                        </select>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">商圈名称</td>
                 <td><input name="name" type="text" value="{$data.name}" style="width:240px;"></td>
             </tr>
             <!--<tr>
                 <td style="text-align:center;">经度</td>
                 <td><input name="lng" type="text" value="{$data.lng}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td><input name="lat" type="text" value="{$data.lat}" style="width:240px;"></td>
             </tr>-->
             
         </table>
         <p class="btn"><input type="submit" value=" 确定 "></p>
         </form>
 	</div>       
 </td>