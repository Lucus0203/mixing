$(function(){
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
                if(index>0){
                    var cityApiURL=$('#cityApiURL').val();
                    var city_id=$(this).val();
                    $.ajax({
                            type:'get',
                            url:cityApiURL,
                            data:{'city_id':city_id},
                            success:function(res){
                                    $('.area_id').eq(index-1).html('<option value="">选择</option>'+res);
                            }
                    })
                }
	});
        //添加一条
        $('.addone').click(function(){
            $input=$(this).prev().clone().val('');
            $(this).before('<br/>');
            $(this).before($input);
        })
        //删除城市
        $('#delCity').click(function(){
            var city=$(this).prev().find("option:selected");
            if(city.val()==''){
                alert('请选择城市');
                return false;
            }
            if(confirm('确认删除该城市么?')){
                var url=$('#delCityURL').val();
                $.ajax({
                        type:'get',
                        url:url,
                        data:{'id':city.val()},
                        success:function(res){
                            if(res==1){
                                city.remove();
                            }else{
                                alert('有数据关联,不可删除')
                            }
                        }
                })
            }
        });
        //删除区域
        $('#delArea').click(function(){
            var area=$(this).prev().find("option:selected");
            if(area.val()==''){
                alert('请选择区域');
                return false;
            }
            if(confirm('确认删除该区域么?')){
                var url=$('#delAreaURL').val();
                $.ajax({
                        type:'get',
                        url:url,
                        data:{'id':area.val()},
                        success:function(res){
                            if(res==1){
                                area.remove();
                            }else{
                                alert('有数据关联,不可删除')
                            }
                        }
                })
            }
        });
        
        //编辑城市
        $('#editCity').click(function(){
            var city=$(this).prev().prev().find("option:selected");
            var url=$('#editCityURL').val();
            if(city.val()==''){
                alert('请选择城市');
                return false;
            }else{
                window.location=url+'&id='+city.val();
            }
        });
        
        //编辑区域
        $('#editArea').click(function(){
            var area=$(this).prev().prev().find("option:selected");
            var url=$('#editAreaURL').val();
            if(area.val()==''){
                alert('请选择区域');
                return false;
            }else{
                window.location=url+'&id='+area.val();
            }
        });
        
});