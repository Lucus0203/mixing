<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">用户编辑</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="{$data.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">用户编辑</td>
             </tr>
             <tr>
                 <td style="text-align:center;">UUID</td>
                 <td>{$data.uuid}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">咖啡号</td>
                 <td><input name="user_name" type="text" value="{$data.user_name}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">新密码</td>
                 <td>
                 	<input name="user_password" type="text" value="" style="width:240px;">(不填则不变更原来的密码)
                 	<input name="old_password" type="hidden" value="{$data.user_password}">
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">性别</td>
                 <td>
                 	<label><input name="sex" type="radio" value="1" {if $data.sex eq 1}checked{/if} />男</label>
                 	<label><input name="sex" type="radio" value="2" {if $data.sex eq 2}checked{/if} />女</label>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">职业</td>
                 <td><input name="career" type="text" value="{$data.career}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">家乡</td>
                 <td><input name="home" type="text" value="{$data.home}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">常住地址</td>
                 <td><input name="address" type="text" value="{$data.address}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">星座</td>
                 <td><input name="constellation" type="text" value="{$data.constellation}" style="width:140px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">兴趣</td>
                 <td><input name="interest" type="text" value="{$data.interest}" style="width:140px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">说说</td>
                 <td><input name="talk" type="text" value="{$data.talk}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">个性签名</td>
                 <td><input name="signature" type="text" value="{$data.signature}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">手机</td>
                 <td><input name="mobile" type="text" value="{$data.mobile}" style="width:140px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">邮箱</td>
                 <td><input name="email" type="text" value="{$data.email}" style="width:240px;"></td>
             </tr>
              <tr>
                 <td style="text-align:center;">获取地址</td>
                 <td>
                 	<label><input name="allow_add" type="radio" value="1" checked="checked">允许</label>
                 	<label><input name="allow_add" type="radio" value="2" {if $data.allow_add eq 2}checked="checked"{/if} >不允许</label>
                 </td>
             </tr>
              <tr>
                 <td style="text-align:center;">找到我</td>
                 <td>
                 	<label><input name="allow_find" type="radio" value="1" checked="checked">允许</label>
                 	<label><input name="allow_find" type="radio" value="2" {if $data.allow_find eq 2}checked="checked"{/if} >不允许</label>
                 </td>
             </tr>
              <tr>
                 <td style="text-align:center;">关注我</td>
                 <td>
                 	<label><input name="allow_flow" type="radio" value="1" checked="checked">允许</label>
                 	<label><input name="allow_flow" type="radio" value="2" {if $data.allow_flow eq 2}checked="checked"{/if} >不允许</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定修改 "></p>
         </form>
 	</div>       
 </td>