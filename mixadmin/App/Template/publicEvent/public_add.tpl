<script type="text/javascript" src="{$smarty.const.SITE}resource/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/public_add.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=ho6LXkYw6eWBzWFlPvcMpLhR"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">发起官方活动</div>
         <form action="" method="post" enctype="multipart/form-data" onsubmit="return checkFrom();">
         <input type="hidden" name="act" value="add" />
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
				<col width="10%">
			 </colgroup>
             <tr>
                 <td class="hd_ta_t" colspan="2">添加官方活动</td>
             </tr>
             <tr>
                 <td style="text-align:center;">排序</td>
                 <td><input name="num" type="text" value="1" style="width:40px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动标题</td>
                 <td><input name="title" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动地址</td>
                 <td><input id="address" name="address" type="text" value="" style="width:600px;">
                 	<div id="allmap"></div>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">经度</td>
                 <td><input id="lng" name="lng" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">纬度</td>
                 <td><input id="lat" name="lat" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">价格费用</td>
                 <td><input name="price" type="text" value="" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">开始时间</td>
                 <td><input name="created" type="text" value="" style="width:140px;">(格式：2015-04-02)</td>
             </tr>
             <tr>
                 <td style="text-align:center;">截止时间</td>
                 <td><input name="end_date" type="text" value="" style="width:140px;">(格式：2015-04-30)</td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动时间</td>
                 <td><input name="datetime" type="text" value="" style="width:140px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">活动内容</td>
                 <td><textarea id="content" name="content" style="width:540px;height:800px;"></textarea></td>
             </tr>
             <tr>
                 <td style="text-align:center;">(宽高640:310)<br/>首页图片</td>
                 <td><input name="img" type="file" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;word-break:keep-all;">(宽高640:310)<br/>海报图片</td>
                 <td><input name="photos[]" type="file" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">海报图片</td>
                 <td><input name="photos[]" type="file" style="width:240px;"></td>
             </tr>
             <tr>
                 <td style="text-align:center;">海报图片</td>
                 <td><input name="photos[]" type="file" style="width:240px;"></td>
             </tr>
             <tr id="photo_add"><td colspan="2" ><a style="margin-left:30px;" href="javascript:void(0)">添加海报</a></td></tr>
             <tr>
                 <td style="text-align:center;">是否发布</td>
                 <td>
                 	<label><input name="ispublic" type="radio" value="1">发布中</label>
                 	<label><input name="ispublic" type="radio" value="2" checked="checked">不发布</label>
                 </td>
             </tr>
         </table>
         <p class="btn"><input type="submit" value=" 确定发起 "></p>
         </form>
 	</div>       
 </td>