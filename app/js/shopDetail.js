if (navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)) {
    if(!navigator.userAgent.match(/MicroMessenger/i)){
        setTimeout(function () { window.location = 'https://itunes.apple.com/app/jiao-ban/id1036717871?l=zh&ls=1&mt=8'; },25);
	var s=getParam('s');
	if(s){
    	window.location = "mixing://shopDetail?s="+s;
	}
    }
}
$(document).ready(function(){
        $('#header').css('visibility','hidden');
//
// $(document).load("http://v2.xn--8su10a.com/upload/shop/20150928/1443425353.jpg", function() {
// alert("加载完成");
// });
	setTimeout(function(){
        $("#header").carouFredSel({
                items : 1,
                scroll:1,
                width : '100%',
                swipe		: {
                      onTouch		: true,
                      onMouse		: true
              }
        });
	  $(".foods ul").carouFredSel({
		  items : 3,
		  scroll:1,
		  width : '100%',
		  swipe		: {
			onTouch		: true,
			onMouse		: true
		}
	  });
	},500);
	$('#gotomap').click(function(){
		window.open($(this).find('a').attr('rel'));
	});
        $('#download').click(function(){
                if(navigator.userAgent.match(/MicroMessenger/i)){
                    alert('请点击右上角更多,在Safari中打开本页')
                }else{
                    window.location = 'https://itunes.apple.com/app/jiao-ban/id1036717871?l=zh&ls=1&mt=8';
                }
        });
        
        // 判断图片加载状况，加载完成后回调
        isImgLoad(function(){
            // 加载完成
            setTimeout(function () { $('#header').css('visibility','visible'); },600);
            
        });
});