<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">APP公告</div>
         <p style="text-align: left;padding: 0 0 0 20px;font-size: 15px;"><a href="{url controller=Appset action=EditLink}">添加公告</a>
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
            <colgroup>
                    <col width="35%">
                    <col width="15%">
                    <col width="30%">
                    <col width="15%">
                    <col width="15%">
            </colgroup>
             <tr>
                 <th>标题</th>
                 <th>图片</th>
                 <th>链接</th>
                 <th>状态</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td>{$list[sec].title}</td>
                 <td><img src="{$list[sec].img}" /></td>
                 <td class="hd_td_l"><a style="word-break: break-all;" href="{$list[sec].link}" target="_blank">链接详情</a></td>
                 <td class="hd_td_l">{if $list[sec].status eq 1}发布{else}未发布{/if}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=Appset action=EditLink linkid=$list[sec].id}">编辑</a><a class="delBtn" href="{url controller=Appset action=DelLink id=$list[sec].id}">删除</a>
                 	<a href="{url controller=Appset action=PublicLink linkid=$list[sec].id status=1}">发布</a><a class="delBtn" href="{url controller=Appset action=PublicLink linkid=$list[sec].id status=2}}">不发布</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>
