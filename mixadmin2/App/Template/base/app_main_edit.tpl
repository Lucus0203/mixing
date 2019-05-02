<script type="text/javascript" src="{$smarty.const.SITE}resource/js/lightbox.min.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/app_main.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">App首页内容</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkaddform()">
         <input type="hidden" name="act" value="edit" />
         <input type="hidden" name="id" value="{$data.id}" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
			<col width="10%">
                        <col width="50%">
                                
            </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">编辑内容</td>
             </tr>
             <tr>
                 <td style="text-align:center;">图片</td>
                 <td><a href="{$data.img}" data-lightbox="roadtrip"><img src="{$data.img}" height="150" /></a><br/><input type="file" name="img" /></td>
             </tr>
             <tr>
                 <td style="text-align:center;">文字内容</td>
                 <td><textarea id="note" name="note" style="width: 400px;">{$data.note}</textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">发布时间</td>
                 <td><input id="datetime" name="datetime" value="{$data.datetime|date_format:"%Y-%m-%d"}" readonly onfocus="c.showMoreDay = false;c.show(this,'');" size="10"></td>
             </tr>
            <tr>
                <td>类型</td>
                <td><select id="type" name="type"><option value="">无</option>
                            <option value="shop" {if $data.type eq 'shop'}selected{/if}>店铺</option>
                            <option value="event" {if $data.type eq 'event'}selected{/if}>活动</option>
                    </select></td>
            </tr>
            <tr>
                <td>对象id</td>
                <td><input id="dataid" name="dataid" value="{$data.dataid}" /></td>
            </tr>
                <tr>
                    <td>对象标题</td>
                    <td><input id="title" name="title" value="{$data.title}" /></td>
                </tr>
             <tr>
                 <td style="text-align:center;">发布状态</td>
                 <td><label><input name="status" type="radio" value="1" checked="checked">发布</label><label><input name="status" type="radio" value="2" {if $data.status eq '2'}checked{/if}>未发布</label></td>
             </tr>
             
         </table>
             <p class="btn"><input type="submit" value=" 确定 "><input type="button" value=" 返回 " onclick="window.location='{url controller=Base action=AppMain}'" /></p>
         </form>
 	</div>       
 </td>