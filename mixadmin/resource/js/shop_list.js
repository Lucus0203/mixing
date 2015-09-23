$(function(){
	$('a.delBtn').click(function(){
		if(confirm('确定删除吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	$('a.pubBtn,a.depubBtn').click(function(){
		if(confirm('确定'+$(this).text()+'吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	
	$('.province_id').change(function(){
		var index=$('.province_id').index($(this));
		var provinceURL=$('#provinceApiURL').val();
		var pro_id=$(this).val();
		$.ajax({
			type:'get',
			url:provinceURL,
			data:{'province_id':pro_id},
			success:function(res){
				$('.city_id').eq(index).html('<option value="">选择</option>'+res);
				$('.town_id').eq(index).html('<option value="">选择</option>');
			}
		})
	});
	$('.city_id').change(function(){
		var index=$('.city_id').index($(this));
		var cityApiURL=$('#cityApiURL').val();
		var city_id=$(this).val();
		$.ajax({
			type:'get',
			url:cityApiURL,
			data:{'city_id':city_id},
			success:function(res){
				$('.addcircle').eq(index).html('<option value="">选择</option>'+res);
			}
		})
	});
        
	$('.addprovince_id').change(function(){
		var index=$('.addprovince_id').index($(this));
		var provinceURL=$('#addcityApiURL').val();
		var pro_id=$(this).val();
		$.ajax({
			type:'get',
			url:provinceURL,
			data:{'province_id':pro_id},
			success:function(res){
				$('.addcity_id').eq(index).html('<option value="">选择</option>'+res);
				$('.addarea_id').eq(index).html('<option value="">选择</option>');
				$('.addcircle').eq(index).html('<option value="">选择</option>');
			}
		})
	});
	$('.addcity_id').change(function(){
		var index=$('.addcity_id').index($(this));
		var cityApiURL=$('#addareaApiURL').val();
		var city_id=$(this).val();
		$.ajax({
			type:'get',
			url:cityApiURL,
			data:{'city_id':city_id},
			success:function(res){
				$('.addarea_id').eq(index).html('<option value="">选择</option>'+res);
				$('.addcircle').eq(index).html('<option value="">选择</option>');
			}
		})
	});
	$('.addarea_id').change(function(){
		var index=$('.addarea_id').index($(this));
		var cityApiURL=$('#addcircleApiURL').val();
		var area_id=$(this).val();
		$.ajax({
			type:'get',
			url:cityApiURL,
			data:{'area_id':area_id},
			success:function(res){
				$('.addcircle_id').eq(index).html('<option value="">选择</option>'+res);
			}
		})
	});
        
});