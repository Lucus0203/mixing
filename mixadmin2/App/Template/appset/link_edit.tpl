<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">App公告</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data" >
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="{$data.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">公告内容</td>
             </tr>
             <tr>
                 <td style="text-align:center;">标题</td>
                 <td><input name="title" type="text" value="{$data.title}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">链接(需要http://开头)</td>
                 <td><input name="link" type="text" value="{$data.link}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">图片(尺寸750*380)</td>
                 <td><img src="{$data.img}" /><input name="img" type="file"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">状态</td>
                 <td><label><input name="status" type="radio" checked value="2">待发布</label><label><input {if $data.status eq 1}checked{/if} name="status" type="radio" value="1">发布中</label></td>
             </tr>
             
         </table>
             <p class="btn"><input type="submit" value=" 确定 "><input type="button" value=" 返回 " onclick="window.location='{url controller=Appset action=Link}'" /></p>
         </form>
 	</div>       
 </td>