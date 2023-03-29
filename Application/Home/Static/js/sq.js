/**
* sq
*
* 智云桥-基础类库
*
* @author colin
*
*/

(function(window, document, $, undefined){
  "use strict";
  window.sq = new Object();

  sq._winWidth   = $(window).width(),
  sq._winHeight  = $(window).height(),
  sq._globalData = {};

  sq.alert = function (content,delayTime,holdTime,top,left){
	  
	  //ie8及以下兼容处理
		if(navigator.appName == "Microsoft Internet Explorer" && parseInt(navigator.appVersion.match(/IE (\d).0/i)[1])<=8) 
		{ 
			alert(content);
			return ;
		}
		
	  
	  if(delayTime == undefined) {
		  delayTime = 500;
	  }
	  if(holdTime == undefined) {
		  holdTime = 1500;
	  }
	  if(top == undefined) {
		  top = 98;
	  }
	  if(left == undefined) {
		  left = sq._winWidth/2;
	  }
	  
	  var _tpl = '<div id="sq_alert" class="sq_alert_class" style="top: '+top+'px;left:'+left+'px;" >'+content+'</div>';
	  $('body').append(_tpl);
	  setTimeout("sq.alert_action("+holdTime+")",delayTime);
  };
  sq.alert_action = function (holdTime) {
	  $("#sq_alert").fadeIn(200);
	  setTimeout('$("#sq_alert").fadeOut(500)',holdTime);
  };
  $("#sq_alert").live('click',function () {
	  $(this).fadeOut(500);
  });
  
})(window, document, jQuery);
