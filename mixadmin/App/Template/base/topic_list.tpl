<script type="text/javascript" src="{$smarty.const.SITE}resource/js/business_circle.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">话题数据</div>
         <form action="{url controller=Base action=AddTopic}" method="post">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="45%">
                        <col width="45%">
                </colgroup>
                <td>
                        <input name="title" value="">
                        <label><input name="recommend" type="radio" value="1" checked="checked">正常</label><label><input name="recommend" type="radio" value="2">推荐</label>
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
                 <th>标签</th>
                 <th>状态</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td class="hd_td_l">{$list[sec].title}</td>
                 <td class="hd_td_l">{if $list[sec].recommend eq 1}正常{else}推荐{/if}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=Base action=EditTopic id=$list[sec].id}">编辑</a><a class="delBtn" href="{url controller=Base action=DelTopic id=$list[sec].id}">删除</a>
                        {if $list[sec].recommend eq 1}<a href="{url controller=Base action=RecommendTopic id=$list[sec].id}">推荐</a>{else}<a href="{url controller=Base action=UnRecommendTopic id=$list[sec].id}">不推荐</a>{/if}
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>