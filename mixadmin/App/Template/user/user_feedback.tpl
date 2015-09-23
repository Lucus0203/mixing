<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">用户反馈</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="User" />
         <input type="hidden" name="action" value="Feedback" />
         <div class="hd_t1">查找<input class="cz_input" type="text" name="keyword" value="{$keyword}"><input class="cz_btn" type="submit" value="查找"></div>
         </form>
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="10%">
				<col width="10%">
				<col width="5%">
				<col width="10%">
				<col width="10%">
				<col width="">
				<col width="20%">
			</colgroup>
             <tr>
                 <th>账号</th>
                 <th>昵称</th>
                 <th>性别</th>
                 <th>电话</th>
                 <th>邮箱</th>
                 <th>反馈内容</th>
                 <th>反馈时间</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td>{$list[sec].user_name}</td>
                 <td>{$list[sec].nick_name}</td>
                 <td>{if $list[sec].sex eq 1}男{elseif $list[sec].sex eq 2}女{/if}</td>
                 <td>{$list[sec].mobile}</td>
                 <td>{$list[sec].email}</td>
                 <td>{$list[sec].content}</td>
                 <td>{$list[sec].created}</td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>