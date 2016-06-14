<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">订单列表</div>
         <form action="" method="get">
         <input type="hidden" name="controller" value="Order" />
         <input type="hidden" name="action" value="Index" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="30%">
                        <col width="30%">
                        <col width="40%">
                        <col width="10%">
                </colgroup>
                <td>
                        订单号<input name="order_no" type="text" value="{$pageparm.order_no}">
                </td>
                <td>
                        验证码<input name="verifycode" type="text" value="{$pageparm.verifycode}">
                </td>
                <td>
                        关键字<input name="keyword" type="text" value="{$pageparm.keyword}" style="width:250px">
                </td>
                <td><input class="cz_btn" type="submit" value="检索"></td></table>
         </form>
         
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
            <colgroup>
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="12%">
                    <col width="13%">
                    <col width="10%">
                    <col width="10%">
                    <col width="5%">
                    <col width="5%">
                    <col width="7%">
            </colgroup>
             <tr>
                 <th>店名</th>
                 <th>用户</th>
                 <th>手机</th>
                 <th>摘要</th>
                 <th>订单号</th>
                 <th>领取码</th>
                 <th>寄存码</th>
                 <th>金额</th>
                 <th>状态</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td class="hd_td_l"><a href="{url controller=Shop action=Edit id=$list[sec].shop_id}">{$list[sec].shop_name}</a></td>
                 <td class="hd_td_l">{$list[sec].nick_name}</td>
                 <td class="hd_td_l">{$list[sec].mobile}</td>
                 <td class="hd_td_l">{$list[sec].body}</td>
                 <td>{$list[sec].order_no}</td>
                 <td>{$list[sec].encouter_receive_code}</td>
                 <td>{$list[sec].encouter_code}</td>
                 <td class="amount">{$list[sec].amount}</td>
                 <td>{if $list[sec].status eq 1}{if $list[sec].paid eq 1 }已付{else}未付{/if}
                     {elseif $list[sec].status eq 2}失效{elseif $list[sec].status eq 3}退款中{elseif $list[sec].status eq 4}已退款{/if}</td>
                 <td style="word-break:keep-all;">
                        <a class="delBtn" href="#">详细</a>
                 	{if $list[sec].status eq 3}
                            <a class="delBtn" href="{url controller=Order action=Refunded order_id=$list[sec].order_id}">已退款</a>
                        {elseif $list[sec].status eq 4}
                            <a class="delBtn" href="{url controller=Order action=RefundedCancel order_id=$list[sec].order_id}">取消退款</a>
                        {/if}
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>
