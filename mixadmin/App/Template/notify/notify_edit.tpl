<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">App通知数据</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="{$data.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t">编辑通知</td>
             </tr>
             <tr>
                 <td style="text-align:center;">小图</td>
                 <td><input name="img" type="text" value="{$data.img}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">消息</td>
                 <td><input name="msg" type="text" value="{$data.msg}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">消息类型</td>
                 <td><select name="type"><option value="mixing">官方通知</option></select></td>
             </tr>
             <tr>
                 <td style="text-align:center;">链接</td>
                 <td><input name="url" type="text" value="{$data.url}" style="width:240px;"></td>
             </tr>
             
         </table>
             <p class="btn"><input type="submit" value=" 确定 "><input type="button" value=" 返回 " onclick="window.location='{url controller=Notify action=Index}'" /></p>
         </form>
 	</div>       
 </td>