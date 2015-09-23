<script type="text/javascript" src="{$smarty.const.SITE}resource/js/public_list.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">官方活动管理</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="PublicEvent" />
         <input type="hidden" name="action" value="Index" />
         <div class="hd_t1">查找活动<input class="cz_input" type="text" name="title" value="{$title}"><input class="cz_btn" type="submit" value="查找"></div>
         </form>
         <input id="orderNumUrl" type="hidden" value="{url controller=PublicEvent action=order}" />
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="5%">
				<col width="15%">
				<col width="">
				<col width="7%">
				<col width="7%">
				<col width="20%">
				<col width="7%">
				<col width="7%">
			</colgroup>
             <tr>
                 <th>排序</th>
                 <th>缩略图</th>
                 <th>官方活动标题</th>
                 <th>开始时间</th>
                 <th>时间</th>
                 <th>地点</th>
                 <th>发布状态</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td><input class="num" type="text" value="{$list[sec].num}" style="width:40px;"><input type="hidden" value="{$list[sec].id}" /></td>
                 <td>{if $list[sec].img neq ''}<img src="{$list[sec].img}">{else}<img src="{$smarty.const.SITE}resource/images/no_img.gif">{/if}</td>
                 <td class="hd_td_l">{$list[sec].title}</td>
                 <td>{$list[sec].created|date_format:"%Y-%m-%d"}~{$list[sec].end_date|date_format:"%Y-%m-%d"}</td>
                 <td>{$list[sec].datetime}</td>
                 <td>{$list[sec].address}</td>
                 <td>{if $list[sec].ispublic eq '2'}未发布{else}发布中{/if}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=PublicEvent action=Edit id=$list[sec].id}">编辑</a><a class="delBtn" href="{url controller=PublicEvent action=Del id=$list[sec].id}">删除</a><br/>
                 	{if $list[sec].ispublic eq '2'}<a class="pubBtn" href="{url controller=PublicEvent action=Public id=$list[sec].id}">发布</a>{else}<a class="depubBtn" href="{url controller=PublicEvent action=DePublic id=$list[sec].id}">不发布{/if}</a>
                 </td>
             </tr>
             {/section}
         </table>
         <p class="btn"><input id="changeOrder" type="button" value="变更顺序" /></p>
         {$page}
     </div>
 </td>