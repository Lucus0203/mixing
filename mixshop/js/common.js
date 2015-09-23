var uri = window.location + '';
if (uri.indexOf('/guanli/') != -1) {
	window.location = 'http://www.coffee15.com';
}
$(function() {
	$(window).resize(function() {
		$('.main_l').height($(window).height() - 160);
	});
	$('.main_l').height($(window).height() - 160);
	$('.selectPage').change(function() {
		var uri = window.location + '';
		var page = $(this).val();
		if (uri.indexOf('page_no') != -1) {
			uri = uri.replace(/&page_no=\d+/, '');
		}
		window.location = uri + '&page_no=' + page;
	});
});

function ismobile(mobile) {
	if (mobile.length == 0) {
		alert('请输入手机号码！');
		$('input [name=mobile]').focus();
		return false;
	}
	if (mobile.length != 11) {
		alert('请输入有效的手机号码！');
		$('input [name=mobile]').focus();
		return false;
	}

	var myreg = /^0?1[3|4|5|8][0-9]\d{8}$/;
	if (!myreg.test(mobile)) {
		alert('请输入有效的手机号码！');
		$('input [name=mobile]').focus();
		return false;
	}
	return true;
}

function isnumber(number){
	var reg=/^\d*$/
	return reg.test(number);
}