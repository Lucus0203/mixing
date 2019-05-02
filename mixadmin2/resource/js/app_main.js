$(function(){
    $('.delBtn').click(function(){
        return confirm('是否删除'+$(this).parent().parent().find('td:eq(1)').html());
    });
});

function checkaddform(){
    var flag=true;
    var msg='';
    if($('#note').val()==''){
        msg+='内容不能为空';
        flag = false;
    }
    if($('#datetime').val()==''){
        msg+='\n发布时间不能为空';
        flag = false;
    }
    if($('#type').val()!=''&&$('#dataid').val()==''){
        msg+='\n对象id不能为空';
        flag = false;
    }
    if(!flag){
        alert(msg);
    }
    return flag;
}