$(function(){
	$('a.delBtn').click(function(){
		if(confirm('确定删除这个活动吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	$('a.pubBtn').click(function(){
		if(confirm('确定发布这个活动吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	$('a.depubBtn').click(function(){
		if(confirm('确定不发布这个活动吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	$('#changeOrder').click(function(){
		var num='';
		var id='';
		var url=$('#orderNumUrl').val();
		$('.num').each(function(i){
			num+=$(this).val()+',';
			id+=$(this).next().val()+',';
		});
		$.ajax({
			type:"post",
			url:url,
			data:{'ids':id,'nums':num},
			success:function(res){
				window.location=res;
			}
		})
	});
});