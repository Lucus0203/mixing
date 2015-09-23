<script type="text/javascript" src="{$smarty.const.SITE}resource/js/business_circle.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">店铺标签</div>
         <form action="{url controller=Base action=AddShopTagTeam}" method="post">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="45%">
                        <col width="45%">
                </colgroup>
                <td>
                        <input name="name" value="" placeholder="分组">
                </td>
                <td><input class="cz_btn" type="submit" value="添加分组"></td></table>
         </form>
         <form action="{url controller=Base action=AddShopTag}" method="post">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="45%">
                        <col width="45%">
                </colgroup>
                <td>
                    <select name="team_id">
                        <option value="">选择</option>
                        {section name=sec loop=$team}
                        <option value="{$team[sec].id}">{$team[sec].name}</option>
                        {/section}
                    </select>
                        <input name="name" value="" placeholder="标签">
                </td>
                <td><input class="cz_btn" type="submit" value="添加标签"></td></table>
         </form>
         
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
            <colgroup>
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
            </colgroup>
             <tr>
                 <th>分组</th>
                 <th>标签</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td class="hd_td_l">{$list[sec].team}</td>
                 <td class="hd_td_l">{$list[sec].name}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=Base action=EditShopTag id=$list[sec].id}">编辑</a><a class="delBtn" href="{url controller=Base action=DelShopTag id=$list[sec].id}">删除</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>