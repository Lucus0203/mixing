//KindEditor.ready(function(K) {
//        window.editor = K.create('#editor');
//});
$(function(){
//        CKEDITOR.replace( 'content' , {
//                // Define the toolbar groups as it is a more accessible solution.
//                toolbarGroups: [
//                        {"name":"links","groups":["links"]},
//                        {"name":"paragraph","groups":["list","blocks"]},
//                        {"name":"document","groups":["mode"]},
//                        {"name":"insert","groups":["insert"]},
//                        {"name":"styles","groups":["styles"]}
//                ],
//                // Remove the redundant buttons from toolbar groups defined above.
//                removeButtons: 'Strike,Subscript,Superscript,Anchor,Styles,Specialchar',
//        });
//        CKEDITOR.config.width = '600';
//        CKEDITOR.config.height = '600';
//	CKEDITOR.config.filebrowserUploadUrl='index.php?controller=PublicEvent&action=UploadImage';
//	$('#photo_add').click(function(){
//		$(this).before('<tr><td style="text-align:center;">海报图片</td>'+
//                '<td><input name="photos[]" type="file" style="width:240px;"></td></tr>');
//	});
//	
//	$('a.delImg').click(function(){
//		var url=$(this).attr('href');
//		var thistr=$(this).parent().parent();
//		if(confirm('确定删除吗?')){
//			var pid=$(this).attr('rel');
//			$.ajax({
//				type:'get',
//				url:url,
//				data:{'pid':pid},
//				success:function(res){
//					if(res==1){
//						thistr.remove();
//					}
//				}
//			})
//		}
//		return false;
//	});

        //裁剪工具
	$('.image-eventer').cropit({ width:750,height:500,imageBackground: true ,imageBackgroundBorderWidth: 25 });// Width of background border
	$('.cropit-image-resize').change(function(){
		var h=$(this).val()*1+500;
		$('.image-eventer').cropit('previewSize', { width: 750, height: h });
	});
	$('#eventimgtool').click(function(){
		if (typeof FileReader =='undefined'){
            alert("您的浏览器不支持文件上传工具,建议换谷歌或者火狐浏览器.");
		}
		$(this).text($("#eventimgBox").is(":hidden") ? "收起上传工具" : "显示上传工具");
		$("#eventimgBox").slideToggle();
	});

	//Ajax上传店铺图片公用方法
	function eventImgUpload(imageData){
		var baseUrl=$('#baseUrl').val();
		var AddUrl=baseUrl+'index.php?controller=PublicEvent&action=AjaxUploadPublicImg';
	    var eventid=$('input[name=id]').val();
	    $('#eventimgs').append('<li class="loading"><img src="'+baseUrl+'resource/images/loading.gif" width="32" height="32"></li>');
		$.ajax({
			type:'post',
			url:AddUrl,
			data:{'eventid':eventid,'image-data':imageData},
			dataType:'json',
			success:function(res){
				if(res.src!=''){
					$('#eventimgs .loading').eq(0).remove();
					$('#eventimgs').append('<li><a href="'+res.src+'" data-lightbox="roadtrip"><img src="'+res.src+'"></a><a class="delEventImg" rel="'+res.id+'" href="javascript:void(0);">删 除</a>'+
             			'<label><input type="radio" name="img" value="'+res.src+'" />作为主图</label></li>');
				}else{
					alert('图片上传失败,请联系管理员');
				}
			}
		});
	}
	//上传活动裁剪
	$('#eventImg_add').click(function(){
		var baseUrl=$('#baseUrl').val();
	    var imageData = $('.image-eventer').cropit('export');
		if(imageData){
	        eventImgUpload(imageData);
		}
	});
	//上传活动原图
	$('#eventImg_add_nocut').click(function(){
		var file=$('#eventimgBox .cropit-image-input').get(0).files[0];
		if(typeof FileReader) {  
            var fr = new FileReader();
            fr.onloadend = function(e) {
               var imageData=e.target.result;
               eventImgUpload(imageData);
            };
            fr.readAsDataURL(file);
        }  
	});

	
	
	$('#eventimgs').on('click','a.delEventImg',function(){
		var baseUrl=$('#baseUrl').val();
		var thisimg=$(this).parent();
		if(confirm('确定删除吗?')){
			var pid=$(this).attr('rel');
			$.ajax({
				type:'get',
				url:baseUrl+'index.php?controller=PublicEvent&action=DelPhoto',
				data:{'pid':pid},
				success:function(res){
					if(res==1){
						thisimg.remove();
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