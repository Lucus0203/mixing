var startnum = 0;
$(function(){
	var timer;
	setScrollHeight();
	$(window).resize(setScrollHeight);
//	setTimeout(function(){
//		showAnima(startnum+1);
//	},200);
//	$("#container").bind("mousewheel",
//	function(event, delta, deltaX, deltaY) {
//		if (timer) {
//			window.clearTimeout(timer)
//		}
//		timer = window.setTimeout(function() {
//			if(delta > 0){
//				if(startnum <= 0){
//					startnum = 0;
//				}else{
//					startnum--;
//				}
//			}else{
//				if(startnum >= 5){
//					startnum = 5;
//				}else{
//					startnum++;
//				}
//			}
//			mainScroll(startnum,getWinHeight());
//			setTimeout(function(){
//				showAnima(startnum+1);
//			},500);
//		},600);
//	});
//	stage5();
});

function mainScroll(num,winHeight){
	if (!ltie10()) {
		$("#scrollContent").css({
			"-webkit-transform": "translate(0px, -" + num*winHeight + "px)",
			"-moz-transform": "translate(0px, -" + num*winHeight + "px)",
			"-o-transform": "translate(0px, -" + num*winHeight + "px)",
			"-ms-transform": "translate(0px, -" + num*winHeight + "px)",
			transform: "translate(0px, -" + num*winHeight + "px)"
		})
	} else {
		$("#scrollContent").animate({
			top: (0 - num*winHeight) + "px"
		},
		1000)
	}
	$('#sideNavi > li').eq(num).addClass('on').siblings().removeClass('on');
	setTimeout(dieAnima,500);
}

function getWinHeight(){
	return $(window).height();
}

function setScrollHeight(){
	$('#page01,#page03').height(getWinHeight());
}

function ltie10() {
	var lt = false;
	var verInfo = window.navigator.userAgent.match(/MSIE\s\d+/);
	if ( !! verInfo) {
		var verNum = verInfo[0].split(" ")[1];
		if (verNum < 10) {
			lt = true
		}
	}
	return lt
};

function showAnima(stagenum){
	var t = 0;
	$('#qiao-icon-wrap').removeClass('show');
	if(stagenum == 1){
		var s = setInterval(function(){
			if(t>9){
				clearInterval(s);
				//console.log(t);
			}else{
				$('#firstAnima > li').eq(t).addClass('bounceIn');
			}
			t++;
		},100);
	}else if(stagenum == 2){
		var s = setInterval(function(){
			if(t>4){
				clearInterval(s);
			}else{
				$('#secondAnima .imgIcon').eq(t).addClass('flipInX');
			}
			t++;
		},300);
	}else if(stagenum == 3){
		var s = setInterval(function(){
			if(t>6){
				clearInterval(s);
			}else{
				$('#thirdAnima > li').eq(t).addClass('fadeInDown');
			}
			t++;
		},200);
	}else if(stagenum == 4){
		var s = setInterval(function(){
			if(t>6){
				clearInterval(s);
			}else{
				$('#fourthAnima > li').eq(t).addClass('fadeInDown');
			}
			t++;
		},200);
	}else if(stagenum == 6){
		$('#qiao-icon-wrap').addClass('show');
		//stage5();
	}/*else if(stagenum == 6){
		var s = setInterval(function(){
			if(t>4){
				clearTimeout(t);
			}else{
				$('#sixthAnima .anielm').eq(t).addClass('tada');
			}
			t++;
		},1000);
	}else{

	}*/
}

function stage5(){
	$('#fifthAnima > .fifth').eq(0).find('img').addClass('bounceIn');
	setTimeout(function(){
		$('#fifthAnima > .fifth').eq(0).find('img').addClass('bounceOut');
		setTimeout(function(){
			$('#fifthAnima > .fifth').eq(1).find('img').addClass('bounceIn');
			setTimeout(function(){
				$('#fifthAnima > .fifth').eq(0).find('img').removeClass('bounceIn').removeClass('bounceOut');
			},500);
		},500);
	},2000);
	setTimeout(function(){
		$('#fifthAnima > .fifth').eq(1).find('img').addClass('bounceOut');
		setTimeout(function(){
			$('#fifthAnima > .fifth').eq(2).find('img').addClass('bounceIn');
			setTimeout(function(){
				$('#fifthAnima > .fifth').eq(1).find('img').removeClass('bounceIn').removeClass('bounceOut');
			},500);
		},500);
	},6000);
	setTimeout(function(){
		$('#fifthAnima > .fifth').eq(2).find('img').addClass('bounceOut');
		setTimeout(function(){
			$('#fifthAnima > .fifth').eq(0).find('img').addClass('bounceIn');
			setTimeout(function(){
				$('#fifthAnima > .fifth').eq(2).find('img').removeClass('bounceIn').removeClass('bounceOut');
				stage5();
			},500);
		},500);
	},10000);
}

function dieAnima(){
	$('#firstAnima > li').removeClass('bounceIn');
	$('#secondAnima .imgIcon').removeClass('flipInX');
	$('#thirdAnima > li').removeClass('fadeInDown');
	$('#fourthAnima > li').removeClass('fadeInDown');
	//$('#fifthAnima > .fifth > img').removeClass('bounceIn');
	//$('.fifth > img').removeAttr('style');
	//$('#sixthAnima .anielm').removeClass('tada');
}

function scrollTo(n){
	startnum = n;
	mainScroll(startnum,getWinHeight());
	setTimeout(function(){
		showAnima(startnum+1);
	},500);
}