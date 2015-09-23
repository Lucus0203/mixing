var remainsecondes=60;
$(function(){
	$('#getCode').click(function(){
		var url=$('#baseUrl').val();
		var username=$('#username').val();
		var mobile=$('#mobile').val();
		if(ismobile(mobile)&&$('#getCode').attr('rel')<=0){
			$.ajax({
				type:"post",
				url:url+'login/getcode',
				data:{'mobile':mobile,'username':username},
				success:function(res){
					if(res==1){
						alert('验证码已发送,请注意查收')
						$('#getCode').text('重新获取验证码60').attr('rel','60');
						remainsecondes=60;
						timing()
					}else{
						alert(res);
					}
				}
			})
		}
		
	});
});

function registe(){
	var username=$.trim($('#username').val());
	var pass=$.trim($('#pass').val());
	var pass_confirm=$('#pass_confirm').val();
	var mobile=$.trim($('#mobile').val());
	var captcha_code=$.trim($('#captcha_code').val());
	if(username.length<6){
		alert('账号不能小于6位');
		return false;
	}
	
	if(pass.length<6){
		alert('密码不能小于6位');
		return false;
	}
	
	if(pass!=pass_confirm){
		alert('两次密码不一样');
		return false;
	}
	if(!ismobile(mobile)){
		return false;
	}
	if(captcha_code==''){
		alert('请输入验证码');
		return false;
	}
	
	return true;
	
}

function timing(){
	if(remainsecondes>0){
		setTimeout(function(){
			remainsecondes--;
			$('#getCode').text('重新获取验证码'+remainsecondes).attr('rel',remainsecondes);
			timing();
		},1000);
	}else{
		$('#getCode').text('获取验证码').attr('rel',0);
	}
}