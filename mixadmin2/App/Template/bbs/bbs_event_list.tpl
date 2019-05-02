<script type="text/javascript" src="{$smarty.const.SITE}resource/js/bbs_list.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">{$event.title}活动留言</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="Bbs" />
         <input type="hidden" name="action" value="UserEvent" />
         <input type="hidden" name="eventid" value="{$event.id}" />
         <div class="hd_t1">查找留言<input class="cz_input" type="text" name="keyword" value="{$keyword}"><input class="cz_btn" type="submit" value="查找"></div>
         </form>
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="15%">
				<col width="10%">
				<col width="8%">
				<col width="">
				<col width="7%">
				<col width="7%">
			</colgroup>
             <tr>
                 <th>头像</th>
                 <th>咖啡号</th>
                 <th>留言时间</th>
                 <th>内容</th>
                 <th>审核状态</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td>{if $list[sec].path neq ''}<img src="{$list[sec].path}">{else}<img src="{$smarty.const.SITE}resource/images/no_img.gif">{/if}</td>
                 <td class="hd_td_l">{$list[sec].user_name}</td>
                 <td>{$list[sec].created}</td>
                 <td>{$list[sec].content}</td>
                 <td>{if $list[sec].allow eq 1}通过{else}不通过{/if}</td>
                 <td style="word-break:keep-all;">
                 	{if $list[sec].allow neq 1}
                 		<a class="pubBtn" href="{url controller=Bbs action=Allow type=userEvent id=$list[sec].id}">通过</a>
                 	{else}
                 		<a class="depubBtn" href="{url controller=Bbs action=DeAllow type=userEvent id=$list[sec].id}">不通过</a>
                 	{/if}
                 		<a class="delBtn" href="{url controller=Bbs action=Del type=userEvent id=$list[sec].id}">删除</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>