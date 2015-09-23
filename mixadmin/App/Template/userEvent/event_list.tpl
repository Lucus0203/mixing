<script type="text/javascript" src="{$smarty.const.SITE}resource/js/event_list.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">个人活动</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="UserEvent" />
         <input type="hidden" name="action" value="Index" />
         <div class="hd_t1">查找活动<input class="cz_input" type="text" name="title" value="{$title}"><input class="cz_btn" type="submit" value="查找"></div>
         </form>
         <input id="orderNumUrl" type="hidden" value="{url controller=PublicEvent action=order}" />
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="5%">
				<col width="15%">
				<col width="">
				<col width="15%">
				<col width="20%">
				<col width="7%">
				<col width="7%">
				<col width="7%">
			</colgroup>
             <tr>
                 <th>序号</th>
                 <th>缩略图</th>
                 <th>活动标题</th>
                 <th>活动时间</th>
                 <th>地点</th>
                 <th>留言</th>
                 <th>允许发布</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td>{$list[sec].id}</td>
                 <td>{if $list[sec].img neq ''}<img src="{$list[sec].img}">{else}<img src="{$smarty.const.SITE}resource/images/no_img.gif">{/if}</td>
                 <td class="hd_td_l">{$list[sec].title}</td>
                 <td>{$list[sec].datetime}</td>
                 <td>{$list[sec].address}</td>
                 <td><a href="{url controller=Bbs action=UserEvent eventid=$list[sec].id}">查看</a></td>
                 <td>{if $list[sec].allow eq '2'}不允许{else}允许{/if}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=UserEvent action=Edit id=$list[sec].id}">编辑</a><a class="delBtn" href="{url controller=UserEvent action=Del id=$list[sec].id}">删除</a><br/>
                 	{if $list[sec].allow eq '2'}<a class="pubBtn" href="{url controller=UserEvent action=Allow id=$list[sec].id}">允许</a>{else}<a class="depubBtn" href="{url controller=UserEvent action=DeAllow id=$list[sec].id}">不允许{/if}</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>