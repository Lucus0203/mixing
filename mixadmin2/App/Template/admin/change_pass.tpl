<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">修改管理员密码</div>
         <p style="text-align:left;margin-left:20px;color:red;">{$msg}</p>
         <form action="" method="post">
         <input type="hidden" name="act" value="update" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td style="text-align:center;">原始密码</td>
                 <td><input name="old_pass" type="password" value="" style="width:500px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">新密码</td>
                 <td><input name="new_pass" type="password" value="" style="width:500px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">确认新密码</td>
                 <td><input name="confirm_pass" type="password" value="" style="width:500px;"></td>
             </tr>
             <tr>
                 <td colspan="2" style="text-align:center;"><input type="submit" value=" 确认修改 "></td>
             </tr>
         </table>
         </form>
 	</div>       
 </td>