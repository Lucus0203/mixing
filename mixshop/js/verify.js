$(function(){
    $('#verifycode').keyup(function(){
        $('#verifycode').val(formatNumber($(this).val()));
    });
    $('#verifycode').val(formatNumber($('#verifycode').val()));
});

function checkFrom(){
    var msg='';
    var flag=true;
    var code=$('#verifycode').val();
    code=code.replace(/\s/g,'');
    if(!isnumber(code)){
        msg+='请输入正确的验证码';
        flag=false;
    }
    if(!flag){
        $('.input_box .error').text(msg);
    }
    return flag;
}


//3位分隔数字
function formatNumber(str) {
    return str.replace(/\s/g,'').replace(/(\d{3})(?=\d)/g,"$1 ");
}