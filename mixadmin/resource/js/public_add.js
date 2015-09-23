//KindEditor.ready(function(K) {
//        window.editor = K.create('#editor');
//});
$(function(){
        CKEDITOR.replace( 'content' , {
                // Define the toolbar groups as it is a more accessible solution.
                toolbarGroups: [
                        {"name":"links","groups":["links"]},
                        {"name":"paragraph","groups":["list","blocks"]},
                        {"name":"document","groups":["mode"]},
                        {"name":"insert","groups":["insert"]},
                        {"name":"styles","groups":["styles"]}
                ],
                // Remove the redundant buttons from toolbar groups defined above.
                removeButtons: 'Strike,Subscript,Superscript,Anchor,Styles,Specialchar',
        });
        CKEDITOR.config.width = '600';
        CKEDITOR.config.height = '600';
	CKEDITOR.config.filebrowserUploadUrl='index.php?controller=PublicEvent&action=UploadImage';
	$('#photo_add').click(function(){
		$(this).before('<tr><td style="text-align:center;">海报图片</td>'+
                '<td><input name="photos[]" type="file" style="width:240px;"></td></tr>');
	});
	
	$('a.delImg').click(function(){
		var url=$(this).attr('href');
		var thistr=$(this).parent().parent();
		if(confirm('确定删除吗?')){
			var pid=$(this).attr('rel');
			$.ajax({
				type:'get',
				url:url,
				data:{'pid':pid},
				success:function(res){
					if(res==1){
						thistr.remove();
					}
				}
			})
		}
		return false;
	});
	
	//北京
	var lng=$('#lng').val()!=''?$('#lng').val():116.400244;
	var lat=$('#lat').val()!=''?$('#lat').val():39.92556;
	// 百度地图API功能
	var map = new BMap.Map("allmap");
	map.enableScrollWheelZoom();                         //启用滚轮放大缩小
	map.addControl(new BMap.ScaleControl());             // 添加比例尺控件
	var point = new BMap.Point(lng,lat); //121.487899,31.249162 上海
	map.centerAndZoom(point, 13);
	var marker = new BMap.Marker(point);// 创建标注
	map.addOverlay(marker);             // 将标注添加到地图中
	marker.enableDragging();           // 可拖拽
	//单击获取点击的经纬度
	map.addEventListener("mousemove",function(e){
		var p = marker.getPosition();  //获取marker的位置
		$('#lng').val(p.lng);
		$('#lat').val(p.lat);
	});
	
	$('#address').blur(function(){
		changeMap(map,point,marker,18);
	});
	$('.town_id,.city_id').change(function(){
		changeMap(map,point,marker,11);
	});
});

//根据地址变化变更地图
function changeMap(map,point,marker,zoom){
	var address=$('.province_id option:selected').text()+$('.city_id option:selected').text()+$('.town_id option:selected').text()+$('#address').val();
	var myGeo = new BMap.Geocoder();// 创建地址解析器实例
	// 将地址解析结果显示在地图上,并调整地图视野
	myGeo.getPoint(address, function(point){
		if (point) {
			map.centerAndZoom(point, zoom);
			marker.setPosition(point);
			$('#lng').val(point.lng);
			$('#lat').val(point.lat);
		}else{
			alert("您选择地址没有解析到结果!");
		}
	}, $('.city_id option:selected').text());
}

function checkFrom(){
	var flag=true;
	var title=$('input[name=title]').val();
	var created=$('input[name=created]').val();
	var enddate=$('input[name=end_date]').val();
	var content=$('#editor').val();
	var msg='';
	if($.trim(title)==''){
		msg+='请填写活动标题\n';
		flag=false;
	}

	if($.trim(created)==''){
		msg+='请填写开始时间\n';
		flag=false;
	}
	if($.trim(enddate)==''){
		msg+='请填写截止时间\n';
		flag=false;
	}
//	if($.trim(content)==''){
//		msg+='请填写活动内容\n';
//		flag=false;
//	}
	if(!flag){
		alert(msg);
	}
	return flag;
}