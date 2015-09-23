<script type="text/javascript" src="{$smarty.const.SITE}resource/js/business_circle.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">问题数据</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="{$data.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="3">编辑标签</td>
             </tr>
             <tr>
                 <td style="text-align:center;">名称</td>
                 <td><input name="title" type="text" value="{$data.title}" style="width:240px;"></td>
                 <td><label><input name="recommend" type="radio" value="1" checked="checked">正常</label><label><input name="recommend" type="radio" value="2" {if $data.recommend eq '2'}checked{/if}>推荐</label></td>
             </tr>
             
         </table>
             <p class="btn"><input type="submit" value=" 确定 "><input type="button" value=" 返回 " onclick="window.location='{url controller=Base action=Question}'" /></p>
         </form>
 	</div>       
 </td>