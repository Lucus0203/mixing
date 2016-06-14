/*if (navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)) {
    setTimeout(function () { window.location = 'https://itunes.apple.com/app/jiao-ban/id1036717871?l=zh&ls=1&mt=8'; },25);
	var s=getParam('s');
	if(s){
    	window.location = "mixing://shopDetail?s="+s;
	}
}*/
$(document).ready(function(){
	setTimeout(function(){
	  $(".foods ul").carouFredSel({
		  items : 3,
		  scroll:1,
		  width : '100%',
		  swipe		: {
			onTouch		: true,
			onMouse		: true
		}
	  });
	},1000);
	$('#gotomap').click(function(){
		window.open($(this).find('a').attr('rel'));
	});
	
	var s=getParam('s');
	var requestHost='http://v2.xn--8su10a.com/api.php?c=shop&act=shopInfo&shopid='+s;
	$.ajax({
		type:"get",
		url:requestHost,
		dataType:"JSONP",
		success:function(data){
			alert(data.result.title);
		}
	})
});