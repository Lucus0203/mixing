<script type="text/javascript" src="{$smarty.const.SITE}resource/js/business_circle.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <input type="hidden" id="provinceApiURL" value="{url controller=Api action=GetCityByProvince}" />
         <input type="hidden" id="cityApiURL" value="{url controller=Api action=GetTownByCity}" />
         <div class="hd_t">区域</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="{$data.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">编辑区域</td>
             </tr>
             <tr>
                 <td style="text-align:center;">区域名称</td>
                 <td><input name="name" type="text" value="{$data.name}" style="width:240px;"></td>
             </tr>
             
         </table>
         <p class="btn"><input type="submit" value=" 确定 "><input type="button" value=" 返回 " onclick="window.location='{url controller=BusinessCircle action=Index}'" /></p>
         </form>
 	</div>       
 </td>