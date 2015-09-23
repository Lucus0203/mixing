$(function(){
	$('#banner_add').click(function(){
		$(this).before('<tr><td style="text-align:center;">(宽高640:589)滚动图片</td>'+
                '<td><input name="banners[]" type="file" style="width:240px;"></td></tr>');
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
	
	
});

function checkFrom(){
	var flag=true;
	var msg='';
//	var title=$('input[name=title]').val();
//	if($.trim(title)==''){
//		msg+='请填写店铺名称\n';
//		flag=false;
//	}
	if(!flag){
		alert(msg);
	}
	return flag;
}