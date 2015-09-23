<script type="text/javascript" src="{$smarty.const.SITE}resource/js/public_add.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">活动编辑</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="{$data.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">活动编辑</td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动标题</td>
                 <td><input name="title" type="text" value="{$data.title}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">约会对象</td>
                 <td><input name="title" type="text" value="{$data.dating}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动时间</td>
                 <td><input name="datetime" type="text" value="{$data.datetime}" style="width:140px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动地址</td>
                 <td><input name="address" type="text" value="{$data.address}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td><input name="lng" type="text" value="{$data.lng}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td><input name="lat" type="text" value="{$data.lat}" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动内容</td>
                 <td><textarea name="content" style="width:540px;height:80px;">{$data.content}</textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">(宽高640:310)<br/>图片</td>
                 <td>
                 	<input name="imgIndex" type="file" style="width:240px;">{if $data.img neq ''}<br><img src="{$data.img}" />{/if}
                 	<input name="img" type="hidden" value="{$data.img}" />
                 </td>
             </tr>
         	<tr>
                 <td style="text-align:center;">是否允许发布</td>
                 <td>
                 	<label><input name="ispublic" type="radio" value="1" checked="checked">允许</label>
                 	<label><input name="ispublic" type="radio" value="2" {if $data.allow eq 2}checked="checked"{/if} >不允许</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定修改 "></p>
         </form>
 	</div>       
 </td>