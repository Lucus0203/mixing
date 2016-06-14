<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">{$shop.title}{$user.nick_name}慢生活</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="Diary" />
         <input type="hidden" name="action" value="Index" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="80%">
                        <col width="20%">
                </colgroup>
                <td>
                        关键字(咖啡馆,内容,昵称)<input name="keyword" type="text" value="{$pageparm.keyword}" style="width:250px">
                </td>
                <td><input class="cz_btn" type="submit" value="检索"></td></table>
         </form>
         
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
            <colgroup>
                    <col width="10%">
                    <col width="10%">
                    <col width="23%">
                    <col width="5%">
                    <col width="5%">
                    <col width="5%">
                    <col width="7%">
            </colgroup>
             <tr>
                 <th>用户</th>
                 <th>店名</th>
                 <th>内容</th>
                 <th>阅读数</th>
                 <th>点赞数</th>
                 <th>状态</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td class="hd_td_l"><a href="{url controller=Diary action=User user_id=$list[sec].user_id}">{$list[sec].nick_name}</a></td>
                 <td class="hd_td_l"><a href="{url controller=Diary action=Shop shop_id=$list[sec].shop_id}">{$list[sec].shop_name}</a></td>
                 <td class="hd_td_l"><a href="{url controller=Diary action=Detail diary_id=$list[sec].diary_id}">{$list[sec].note}</a></td>
                 <td>{$list[sec].views}</td>
                 <td>{$list[sec].beans}</td>
                 <td>{if $list[sec].shop_view_status eq 1}公开{else}屏蔽{/if}</td>
                <td style="word-break:keep-all;">
                        <a class="delBtn" href="{url controller=Diary action=Detail diary_id=$list[sec].diary_id}">详细</a>
                 	{if $list[sec].shop_view_status eq 1}
                            <a class="delBtn" href="{url controller=Diary action=DeView diary_id=$list[sec].diary_id}">屏蔽</a>
                        {else}
                            <a class="delBtn" href="{url controller=Diary action=DeViewCancel diary_id=$list[sec].diary_id}">公开</a>
                        {/if}
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>
