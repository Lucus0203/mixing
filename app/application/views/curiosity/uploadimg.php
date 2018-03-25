<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="description" content="">
<meta name="keywords" content="">
<title>试试你的好奇心指数</title>
<link rel="shortcut icon" href="http://www.coffee15.cn/favicon.ico" />
<link rel="stylesheet" href="<?php echo base_url();?>css/default.css" type="text/css" />
<link rel="stylesheet" href="<?php echo base_url();?>css/curiosity.css" type="text/css" />
</head>
<body>
    <div style="display: none;"><img src="<?php echo $user['headimgurl']; ?>" /></div>
<article id="container">
	<div class="main">
            <h1 style="color:#fff;">选一张图片</h1>
            <p><?php if(!empty($res['error'])){ echo '图片上传失败:'.$res['error'];} ?></p>
            <form id="pickerForm" action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="act" value="upload" />
                <div id="filePicker" class="webuploader-container">
                    <div id="show" class="webuploader-pick"><img src="<?php echo base_url();?>img/image_upload.png" alt="上传图片" /></div>
                    <div class="webinputbox">
                        <label>
                            <input type="file" name="file" id="upimg" accept="image/*" style="display: none;">
                        </label>
                    </div>
                </div>
                <p class="subBox"><input id="subBtn" type="submit" value="就这张图了" /></p>
            </form>
	</div>
</article>
<script src="<?php echo base_url();?>js/jquery-1.8.1.min.js"></script>
<script>
$(document).ready(function(){
        var upimg = document.querySelector('#upimg');
        upimg.addEventListener('change', function(e){
            var files = this.files;
            if(files.length){
                // 对文件进行处理，下面会讲解checkFile()会做什么
                checkFile(this.files);
            }
        });
	// 图片处理
        function checkFile(files){
            var file = files[0];
            var reader = new FileReader();
            // show表示<div id='show'></div>，用来展示图片预览的
            if(!/image\/\w+/.test(file.type)){
                show.innerHTML = "请选择图片";
                return false;
            }
            // onload是异步操作
            reader.onload = function(e){
                show.innerHTML = '<img src="'+e.target.result+'" width="100%" alt="img">';
                $('#show').css({'background':'#fff','padding':'0','margin':0});
                $('#subBtn').show();
                $('#pickerForm').css({'background':'#eec'});
                $('#filePicker').css('height','auto');
            }
            reader.readAsDataURL(file);
        }
});
</script>
</body>
</html>
