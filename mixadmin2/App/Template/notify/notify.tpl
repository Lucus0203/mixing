<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">通知消息</div>
         <form action="{url controller=Notify action=AddNotify}" method="post">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="45%">
                        <col width="45%">
                </colgroup>
                <td>
                        小图(默认官方logo)<input name="img" value="http://www.xn--8su10a.com/img/office_mark_head.png">
                </td>
                <td>
                        消息<input name="msg" value="">
                </td>
                <td>
                    消息类型<select name="type"><option value="mixing">官方通知</option></select>
                </td>
                <td>
                        链接<input name="url" value="">
                </td>
                <td><input class="cz_btn" type="submit" value="添加通知"></td></table>
         </form>
         
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
            <colgroup>
                    <col width="15%">
                    <col width="30%">
                    <col width="15%">
                    <col width="30%">
                    <col width="15%">
            </colgroup>
             <tr>
                 <th>小图</th>
                 <th>消息内容</th>
                 <th>类型</th>
                 <th>链接</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td><img src="{$list[sec].img}" /></td>
                 <td class="hd_td_l">{$list[sec].msg}</td>
                 <td class="hd_td_l">官方</td>
                 <td class="hd_td_l"><a style="word-break: break-all;" href="{$list[sec].url}" target="_blank">链接详情</a></td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=Notify action=EditNotify id=$list[sec].id}">编辑</a><a class="delBtn" href="{url controller=Notify action=DelNotify id=$list[sec].id}">删除</a>
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>
