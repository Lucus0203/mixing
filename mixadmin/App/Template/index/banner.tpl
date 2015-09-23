<script type="text/javascript" src="{$smarty.const.SITE}resource/js/banner.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">首页滚动图</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">首页滚动图</td>
             </tr>
             {section name=ban loop=$banner}
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高640:589)滚动图片</td>
                 <td>
                 	<img src="{$banner[ban].img}"><a class="delImg" rel="{$banner[ban].id}" href="{url controller=Index action=DelBanner}">删 除</a>
                 	<input name="oldbanner[]" type="hidden" value="{$banner[ban].img}" />
                 </td>
             </tr>
             {/section}
             <tr>
                 <td style="text-align:center;">(宽高640:589)滚动图片</td>
                 <td><input name="banners[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="banner_add"><td colspan="2" ><a style="margin-left:30px;" href="javascript:void(0)">添加图片</a></td></tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定 "></p>
         </form>
 	</div>       
 </td>