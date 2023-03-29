$('.js_time_choose').live('click',function(e) {
	var checkTime = new Date().getTime();
	var cha_time = checkTime - window.loadStartTime;
	var obj_time = $(this).parent().find('.custom_date_box');
	var time_arr = $(this).siblings('.js_time_choose_destination').html().split('&nbsp;&nbsp;－&nbsp;&nbsp;');
	var flag = parseInt(Math.random()*1000000);
	var piker = $(this).attr('data-picker');
	if(obj_time.length == 0) {
		$(this).after('<div class="custom_date_box" data-flag="'+flag+'">\
							<div class="Time_box"><span class="starTime_text">'+window.Think.LANG.START_TIME+'：</span><input id="startTime_'+flag+'" type="text" value="'+time_arr[0]+'"/><label for="startTime_'+flag+'" class="custom_btn ui-datepicker-trigger" style="margin:0px;"></label></div>\
							<div class="Time_box"><span class="endTime_text">'+window.Think.LANG.END_TIME+'：</span><input id="endTime_'+flag+'" type="text" value="'+time_arr[1]+'"/><label for="endTime_'+flag+'" class="custom_btn ui-datepicker-trigger" style="margin:0px;"></label></div>\
							<div class="custom_button_box">\
								<button onclick="time_choose_sure(this);">'+window.Think.LANG.ALERT_YES+'</button>\
								<button class="cancel" onclick="$(this).parent().parent().hide();">'+window.Think.LANG.ALERT_NO+'</button>\
							</div>\
						</div>');
		
		if(piker == 1){  // 无时分秒控件
			var max =  new Date(new Date($('#endTime_'+flag).val()).getTime() - 86400000);
			$('#startTime_'+flag).datetimepicker({
				timeFormat:'',
				timeOnlyShowDate: true,
				timeText: '',
				maxDateTime: max,
				onClose: function( selectedDate ) {
					var min = new Date(new Date(selectedDate).getTime() + 86400000);
					$('#endTime_'+flag).datetimepicker( "option", "minDate", min);
				}
			});
			$('#endTime_'+flag).datetimepicker({
				timeFormat:'',
				timeOnlyShowDate: true,
				timeText: '',
				maxDateTime: new Date(window.Think.NOW_TIME*1000+cha_time),
				onClose: function( selectedDate ) {
					var maxed = new Date(new Date(selectedDate).getTime() - 86400000);
					$('#startTime_'+flag).datetimepicker( "option", "maxDate", maxed );
				}
			});
		}else if(piker == 2){
			var year= new Date(window.Think.NOW_TIME*1000).getFullYear() - 2;
			var month = new Date(window.Think.NOW_TIME*1000).getMonth()+1;
			var date = new Date(window.Think.NOW_TIME*1000).getDate();
			var current = year+"-"+month+"-"+seven(date);
			$('#startTime_'+flag).datetimepicker({
				maxDateTime: new Date(window.Think.NOW_TIME*1000),
				minDate: current,
				onClose: function( selectedDate ) {
					$('#endTime_'+flag).datetimepicker( "option", current,selectedDate);
				}
			});
			$('#endTime_'+flag).datetimepicker({
				maxDateTime: new Date(window.Think.NOW_TIME*1000+cha_time),
				onClose: function( selectedDate ) {
					maxDateTime = new Date(selectedDate).getTime() + cha_time;
					$('#startTime_'+flag).datetimepicker( "option", "maxDate", maxDateTime);
				}
			});
		}else{
			$('#startTime_'+flag).datetimepicker({
				maxDateTime: new Date(window.Think.NOW_TIME*1000+cha_time),
				onClose: function( selectedDate ) {
					$('#endTime_'+flag).datetimepicker( "option", "minDate", new Date(selectedDate));
				}
			});
			$('#endTime_'+flag).datetimepicker({
				maxDateTime: new Date(window.Think.NOW_TIME*1000+cha_time),
				onClose: function( selectedDate ) {
					maxDateTime = new Date(selectedDate).getTime() + cha_time;
					$('#startTime_'+flag).datetimepicker( "option", "maxDate", maxDateTime);
				}
			});
		}

		
	}else{
		if(obj_time.is(":hidden")) {
			var obj_box = $(this).parent().find('.custom_date_box');
			flag = obj_box.attr('data-flag');
			obj_box.find('#startTime_'+flag).val(time_arr[0]);
			obj_box.find('#endTime_'+flag).val(time_arr[1])
			obj_time.show();
		}else{
			obj_time.hide();
		}
	}
});

function time_choose_sure(obj_this) {
	var obj_time = $(obj_this).parent().parent();
	var obj_destination = obj_time.siblings('.js_time_choose_destination');
	var obj_touch = obj_time.siblings('.js_time_choose');
	var flag = obj_time.data('flag');
	obj_time.hide();

	var picker = $(obj_this).parents('.custom_choose_this').find('.js_time_choose').attr('data-picker');
	var year= new Date(window.Think.NOW_TIME*1000).getFullYear() - 2;
	var month = new Date(window.Think.NOW_TIME*1000).getMonth()+1;
	var date = new Date(window.Think.NOW_TIME*1000).getDate();
	var hours = new Date(window.Think.NOW_TIME*1000).getHours(); //分
	var mint = new Date(window.Think.NOW_TIME*1000).getMinutes();
	var current = year+"-"+month+"-"+seven(date)+' '+seven(hours)+':'+seven(mint);

	console.log($('#startTime_'+flag).val() > current,555)
	if($('#startTime_'+flag).val() > $('#endTime_'+flag).val()){
			var top_txt = ThinkPHP.LANG.ALERT_GT_ENDTIME;
			var tip_yes = ThinkPHP.LANG.ALERT_YES;
			wx.alert(top_txt,null, {
				tip_title: ThinkPHP.LANG.ALERT_TIP_SYSTEM_PROMPT,
				tip_width:"312px",
				tip_yes:tip_yes,
				attachBg : 1,
				height:650
			});
			return false;
	}else{
		if(picker == 2){
			if($('#startTime_'+flag).val() < current){
				$('#startTime_'+flag).val(current);
			}
		}
		obj_destination.html($('#startTime_'+flag).val()+'&nbsp;&nbsp;－&nbsp;&nbsp;'+$('#endTime_'+flag).val());
		eval(obj_touch.data('callback'));
	}
}

$(function() {
	$('.js_time_choose_destination').attr('readonly','readonly');
});