$(function(){
	$('a.pubBtn').click(function(){
		if(confirm('确定通过审核吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
});