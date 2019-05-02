<script type="text/javascript" src="{$smarty.const.SITE}resource/js/lightbox.min.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/app_main.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">APP首页内容</div>
         <form action="{url controller=Base action=AddAppMain}" method="post" enctype="multipart/form-data" onsubmit="return checkaddform()">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="10%">
                        <col width="45%">
                </colgroup>
                <tr>
                    <td>图片(600x400)</td>
                    <td><input type="file" name="img" value="" class="calendarinput" /></td>
                <tr>
                    <td>内容</td>
                    <td><textarea id="note" name="note" style="width:400px;" ></textarea></td>
                </tr>
                <tr>
                    <td class="calendartd">发布时间</td>
                    <td><input id="datetime" name="datetime" value="" readonly onfocus="c.showMoreDay = false;c.show(this,'');" size="10">
                            <label><input name="status" type="radio" value="1" checked="checked">发布</label><label><input name="status" type="radio" value="2">不发布</label>
                    </td>
                </tr>
                <tr>
                    <td>类型</td>
                    <td><select id="type" name="type"><option value="">无</option>
                                <option value="shop">店铺</option>
                                <option value="event">活动</option>
                        </select></td>
                </tr>
                <tr>
                    <td>对象id</td>
                    <td><input id="dataid" name="dataid" value="" /></td>
                </tr>
                <tr>
                    <td>对象标题</td>
                    <td><input id="title" name="title" value="" /></td>
                </tr>
                <tr>
                    <td colspan="2"><input class="cz_btn" type="submit" value="添加"></td>
                </tr>
         </table>
         </form>
         
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
            <colgroup>
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="10%">
            </colgroup>
             <tr>
                 <th>图片</th>
                 <th>内容</th>
                 <th>发布时间</th>
                 <th>类型</th>
                 <th>对象id</th>
                 <th>状态</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td><a href="{$list[sec].img}" data-lightbox="roadtrip"><img src="{$list[sec].img}" height="150"></a></td>
                 <td>{$list[sec].note|nl2br}</td>
                 <td class="hd_td_l">{$list[sec].datetime|date_format:"%Y-%m-%d"}</td>
                 <td>{if $list[sec].type eq 'shop'}店铺{elseif $list[sec].type eq 'event'}活动{else}无{/if}</td>
                 <td>{$list[sec].dataid}</td>
                 <td class="hd_td_l">{if $list[sec].status eq 1}发布中{else}未发布{/if}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=Base action=EditAppMain id=$list[sec].id}">编辑</a><a class="delBtn" href="{url controller=Base action=DelAppMain id=$list[sec].id}">删除</a><br/>
                        {if $list[sec].status neq 1}<a href="{url controller=Base action=PublicAppMain id=$list[sec].id}">发布</a>{else}<a href="{url controller=Base action=DePublicAppMain id=$list[sec].id}">不发布</a>{/if}
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>