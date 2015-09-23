<script type="text/javascript" src="{$smarty.const.SITE}resource/js/business_circle.js"></script>
<td valign="top" align="center">
         <input type="hidden" id="provinceApiURL" value="{url controller=Api action=GetShopCityByProvince}" />
         <input type="hidden" id="cityApiURL" value="{url controller=Api action=GetShopAreaByCity}" />
         <input type="hidden" id="delCityURL" value="{url controller=BusinessCircle action=DelCity}" />
         <input type="hidden" id="delAreaURL" value="{url controller=BusinessCircle action=DelArea}" />
         <input type="hidden" id="editCityURL" value="{url controller=BusinessCircle action=EditCity}" />
         <input type="hidden" id="editAreaURL" value="{url controller=BusinessCircle action=EditArea}" />
 	<div class="main_ta_box">
         <div class="hd_t">城市商圈</div>
         <form action="{url controller=BusinessCircle action=AddCity}" method="post">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="45%">
                        <col width="45%">
                        <col width="15%">
                </colgroup>
         	<tr>
                    <td>
                    <select name="province_id">
                        <option value="">选择</option>
                        {section name=sec loop=$provinces}
                        <option value="{$provinces[sec].id}" {if $province_id eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
                        {/section}
                    </select>
                    </td>
                <td>
                        <input name="name[]" value="" placeholder="城市">
                        <input type="button" class="addone" value="再添加一条" />
                </td>
                <td><input class="cz_btn" type="submit" value="提交城市数据"></td></table>
         </form>
         <form action="{url controller=BusinessCircle action=AddArea}" method="post">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="45%">
                        <col width="45%">
                        <col width="15%">
                </colgroup>
         	<tr>
                    <td>
                        <select class="province_id">
                                <option value="">选择</option>
                                {section name=sec loop=$provinces}
                                <option value="{$provinces[sec].id}" {if $province_id eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
                                {/section}
                        </select>
                        <select name="city_id" class="city_id">
                                <option value="">选择</option>
                            {section name=sec loop=$city}
                            <option value="{$city[sec].id}" {if $city_id eq $city[sec].id}selected{/if}>{$city[sec].name}({$city[sec].code})</option>
                            {/section}
                        </select>
                        <input type="button" value="删除城市" id="delCity" />
                        <input type="button" value="编辑城市" id="editCity" />
                    </td>
                    <td>
                            <input name="name[]" value="" placeholder="区域">
                            <input type="button" class="addone" value="再添加一条" />
                    </td>
                    <td>
                    <input class="cz_btn" type="submit" value="提交区域数据"></td></table>
         </form>
         <form action="{url controller=BusinessCircle action=AddCircle}" method="post">
         <table class="hd_del_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center" style="margin-bottom:30px;">
                <colgroup>
                        <col width="45%">
                        <col width="45%">
                        <col width="15%">
                </colgroup>
         	<tr>
                    <td>
                        <select class="province_id">
                                <option value="">选择</option>
                                {section name=sec loop=$provinces}
                                <option value="{$provinces[sec].id}" {if $province_id eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
                                {/section}
                        </select>
                        <select name="city_id" class="city_id">
                                <option value="">选择</option>
                            {section name=sec loop=$city}
                            <option value="{$city[sec].id}" {if $city_id eq $city[sec].id}selected{/if}>{$city[sec].name}({$city[sec].code})</option>
                            {/section}
                        </select>
                        <select name="area_id" class="area_id">
                                <option value="">选择</option>
                            {section name=sec loop=$area}
                            <option value="{$area[sec].id}" {if $area_id eq $area[sec].id}selected{/if}>{$area[sec].name}</option>
                            {/section}
                        </select>
                        <input type="button" value="删除区域" id="delArea" />
                        <input type="button" value="编辑区域" id="editArea" />
                    </td>
                    <td>
                            <input name="name[]" value="" placeholder="商圈">
                            <input type="button" class="addone" value="再添加一条" />
                    </td>
                    <td>
                    <input class="cz_btn" type="submit" value="提交商圈数据"></td></table>
         </form>
         <p style="text-align: left;padding: 0 0 0 20px;font-size: 15px;"><a href="{url controller=BusinessCircle action=DownCsv}">导出csv</a></p>
         <form action="" method="get">
         <input type="hidden" name="controller" value="BusinessCircle" />
         <input type="hidden" name="action" value="Index" />
         <div class="hd_t1">
         		省份
				<select name="province_id" class="province_id">
					<option value="">选择</option>
					{section name=sec loop=$provinces}
					<option value="{$provinces[sec].id}" {if $province_id eq $provinces[sec].id}selected{/if}>{$provinces[sec].name}</option>
					{/section}
				</select>
				城市
				<select name="city_id" class="city_id">
					<option value="">选择</option>
                                        {section name=sec loop=$city}
                                        <option value="{$city[sec].id}" {if $city_id eq $city[sec].id}selected{/if}>{$city[sec].name}({$city[sec].code})</option>
                                        {/section}
				</select>
                                区域
				<select name="area_id" class="area_id">
					<option value="">选择</option>
                                        {section name=sec loop=$area}
                                        <option value="{$area[sec].id}" {if $area_id eq $area[sec].id}selected{/if}>{$area[sec].name}</option>
                                        {/section}
				</select>
                                城市热门
				<select name="type" class="type_id">
					<option value="">选择</option>
					<option value="1" {if $type eq 1}selected{/if} >普通</option>
					<option value="2" {if $type eq 2}selected{/if} >热门</option>
				</select>
                                <input name="keyword" type="text" value="{$keyword}" />
				<input class="cz_btn" type="submit" value="查找"></div>
         </form>
         
         <table class="hd_ta" border="0" cellpadding="0" cellspacing="1" width="97%" align="center">
			<colgroup>
				<col width="15%">
				<col width="15%">
				<col width="15%">
				<col width="15%">
				<col width="15%">
			</colgroup>
             <tr>
                 <th>省份</th>
                 <th>城市</th>
                 <th>行政区</th>
                 <th>商圈</th>
                 <th>城市热门</th>
                 <th>操作</th>
             </tr>
             {section name=sec loop=$list}
             <tr>
                 <td>{$list[sec].province}</td>
                 <td>{$list[sec].city}</td>
                 <td>{$list[sec].area}</td>
                 <td>{$list[sec].name}</td>
                 <td>{if $list[sec].type eq 1}普通{else}热门{/if}</td>
                 <td style="word-break:keep-all;">
                 	<a href="{url controller=BusinessCircle action=Edit id=$list[sec].id}">编辑</a><a class="delBtn" href="{url controller=BusinessCircle action=Del id=$list[sec].id}">删除</a>
                        <br/>
                        {if $list[sec].type eq 1}<a href="{url controller=BusinessCircle action=HotCircle id=$list[sec].id type=2}">推荐城市热门</a>{else}<a href="{url controller=BusinessCircle action=HotCircle id=$list[sec].id type=1}">改为普通</a>{/if}
                 </td>
             </tr>
             {/section}
         </table>
         {$page}
     </div>
 </td>