function checkFrom(){
	var flag=true;
	var name=$('input[name=name]').val();
	var tel=$('input[name=tel]').val();
	
	var msg='';
	if($.trim(name)==''){
		msg+='请填写姓名\n';
		flag=false;
	}
	if($.trim(tel)==''){
		msg+='请填写联系电话\n';
		flag=false;
	}
	
	if(!flag){
		alert(msg);
	}
	
	if(flag){
		return confirm('确认保存信息吗?')
	}
	return flag;
}