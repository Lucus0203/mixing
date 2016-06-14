// JavaScript Document
if(navigator.userAgent.match(/MicroMessenger/i)){
	if (navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)) {
        alert('请点击右上角更多,在Safari中打开本页')
	}else{
        alert('请点击右上角更多,在浏览器中打开本页')
	}
  }
 if (navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)) {
    setTimeout(function () { window.location = 'https://itunes.apple.com/app/jiao-ban/id1036717871?l=zh&ls=1&mt=8'; },25);
	var u=getParam('u');
	if(u){
    	window.location = "mixing://userInfo?u="+u;
	}
  /*} else if (navigator.userAgent.match(/android/i)) {
    var state = null;
    try {
      state = window.open("http://www.xn--8su10a.com", '_blank');//scheme
    } catch(e) {}
    if (state) {
      window.close();
    } else {
      window.location = "http://www.xn--8su10a.com";
    }*/
  }else{
      //window.location = "http://www.xn--8su10a.com";
  }