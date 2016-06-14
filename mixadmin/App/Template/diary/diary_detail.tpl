<script type="text/javascript" src="{$smarty.const.SITE}resource/js/lightbox.min.js"></script>
<td valign="top" align="center">
 	<div class="main_ta_box">
         <div class="hd_t">慢生活详情</div>
         <p style="color:red;font-size:14px;text-align:left;padding-left:20px;">{$msg}</p>
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
             <colgroup>
                    <col width="10%">
             </colgroup>
             <tr>
             	<td style="text-align:center;">用户</td>
             	<td>{$diary.nick_name}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">店铺</td>
                 <td>{$diary.shop_title}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">阅读量</td>
                 <td>{$diary.views}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">点赞量</td>
                 <td>{$diary.beans}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">内容</td>
                 <td>{$diary.note}</td>
             </tr>
             <tr>
                 <td style="text-align:center;">图片</td>
                 <td >
	                 <ul id="shopimgs">
             			{section name=spi loop=$diary.imgs}
	                 		<li>
	                 			<a href="{$diary.imgs[spi].img}" data-lightbox="roadtrip"><img src="{$diary.imgs[spi].img}"></a>
	                 		</li>
             			{/section}
	             	</ul>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">留言</td>
                 <td >
	                 <ul id="msg">
             			{section name=ms loop=$diary.msgs}
	                 		<li>
                                            来自<a>{$diary.msgs[ms].from_nick_name}</a>{if $diary.msgs[ms].to_nick_name neq ''} to <a>{$diary.msgs[ms].to_nick_name}</a> {/if}的留言:{$diary.msgs[ms].msg}
	                 		</li>
             			{/section}
	             	</ul>
                 </td>
             </tr>
             <tr>
                 <td style="text-align:center;">发布时间</td>
                 <td>{$diary.created}</td>
             </tr>
         </table>
         <p class="btn">
            {if $diary.shop_view_status eq 1}
                <a class="delBtn" href="{url controller=Diary action=DeView diary_id=$diary.diary_id}">屏蔽</a>
            {else}
                <a class="delBtn" href="{url controller=Diary action=DeViewCancel diary_id=$diary.diary_id}">公开</a>
            {/if}
        </p>
 	</div>       
 </td>