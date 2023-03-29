//标记层显示和隐藏
$(".emotion").live('mouseover',function(event) {
	$('.menu_list').each(function(){
		$(this).hide();
	});
	$(this).parent().find('.menu_list').css('display','block');
	
	window.menu_list_position = mousePosition(event);
	
});
$(".menu_list").live('mouseleave',function() {
	$(this).css('display','none');
});

//自动隐藏
$(document).mousemove(function(event){
	if(window.menu_list_position == undefined) return;
	
	var xPos = window.menu_list_position[0];
	var yPos = window.menu_list_position[1];
	
	var nowPos = mousePosition(event)
	if(Math.abs(nowPos[0] - xPos) >150 || Math.abs(nowPos[1] - yPos) >150) {
		$('.menu_list').each(function(){
			$(this).hide();
		});
	}
});

