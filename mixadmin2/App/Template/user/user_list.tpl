<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">用户管理</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="User" />
         <input type="hidden" name="action" value="Index" />
         <div class="hd_t1">查找用户<input class="cz_input" type="text" name="keyword"><input class="cz_btn" type="submit" value="查找"></div>
         </form>
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="5%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				<col width="">
			</colgroup>
             <tr>
                 <th>序号</th>
                 <th>账号</th>
                 <th>昵称</th>
                 <th>性别</th>
                 <th>电话</th>
                 <th>地点</th>
                 <th>注册时间</th>
                 <th>获取地址</th>
                 <th>找到我</th>
                 <th>关注我</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td>{$list[sec].id}</td>
                 <td>{$list[sec].user_name}</td>
                 <td>{$list[sec].nick_name}</td>
                 <td>{if $list[sec].sex eq 1}男{elseif $list[sec].sex eq 2}女{/if}</td>
                 <td>{$list[sec].mobile}</td>
                 <td>{$list[sec].address}</td>
                 <td>{$list[sec].created}</td>
                 <td>{if $list[sec].allow_add eq 1}允许{else}不允许{/if}</td>
                 <td>{if $list[sec].allow_find eq 1}允许{else}不允许{/if}</td>
                 <td>{if $list[sec].allow_flow eq 1}允许{else}不允许{/if}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=User action=Edit id=$list[sec].id}">编辑</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>