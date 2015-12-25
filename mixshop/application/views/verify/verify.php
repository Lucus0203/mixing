<script type="text/javascript" src="<?php echo base_url();?>js/verify.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">验券记录</div>
         <form action="" method="post" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="checkcode" />
         <div class="input_box">
             <input type="text" autocomplete="off" maxlength="60" placeholder="请输入领取验证码" value="<?php echo $verifycode ?>" name="verifycode" id="verifycode" class="f-input">
             <button type="submit" class="btn-yel consume-btn" id="coupon-serial-number-verify-button">消 费</button>
             <p class="error"><?php echo $msg; ?></p>
         </div>
         </form>
         <p class="today_amount">今日验券金额总计:<?php echo $today_amount ?>元<span class="total_amount">历史总计:<?php echo $total_amount ?>元</span></p>
         <table id="menuList" class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
                    <col width="25%">
                    <col width="25%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
             </colgroup>
             <tr>
                 <td class="hd_ta_t">领取验证码</td>
                 <td class="hd_ta_t">领取内容</td>
                 <td class="hd_ta_t">价格</td>
                 <td class="hd_ta_t">领取时间</td>
                 <td class="hd_ta_t">操作</td>
             </tr>
             
             <?php foreach ($orders as $o){ ?>
             <tr>
                 <td class="menu_title verifycode" style="font-size:20px;"><?php echo $o['verifycode'];//number_format($o['verifycode'],0,',',' ') ?></td>
                 <td class="menu_title"><?php echo $o['note']?></td>
                 <td class="menu_title price"><?php echo number_format($o['price'])?>元</td>
                 <td class="menu_title"><?php echo $o['created']?></td>
                 <td class="menu_title">取消</td>
             </tr>
	     <?php } ?>
         </table>
         <?php echo $links;?>
 	</div>       
 </td>
