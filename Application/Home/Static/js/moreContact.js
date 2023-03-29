function checkEmails(priv){
	priv = priv.replace(/\n/g,'');
	
	if($.trim(priv) == '') {
		return false;
	}
	
	var is_true = true;
	var priv_emails = priv;
	priv_emails = priv_emails.replace(/，/g, ",");

	if(priv_emails.indexOf(",")>=0){
		var arr = priv_emails.split(",");
	}else{
		var arr = [priv_emails];
	}

	//前端判断邮箱队列正确性
	arr.forEach(function(v){
		v = $.trim(v);
		var regExpGroup = /^\-[\s\S]+\-$/;
		if(regExpGroup.test(v) || v == '') {
			return;
		}
			
		var regExpEmail = /\<([\s\S]+?)\>/;
		var vArr = v.match(regExpEmail);
		
		if(vArr != null) {
			v = vArr[1];
		}
		
		if(!wx.checkemail(v)){
			is_true = false;
		}
	});

	return is_true;
}

//多个邮箱确定
$(".group_saveyes").live('click',function(){
	var priv =  $(this).parent().find(".edit_email").val();

	priv = priv.replace(/\n/g,'');
	priv = priv.replace(/\s/g,'');
	var privArr = priv.replace('，',',').split(',');
	var new_arr = [];

	for(var i = 0;i<privArr.length;i++){

		var items = privArr[i];
		if($.inArray(items,new_arr)==-1 && items != ''){
			new_arr.push(items);
		}
	}
	
	var obj_error_tip = $(this).parent().parent().next();
	
	if(obj_error_tip.attr('id') !== 'transmit_error_tip') {
		obj_error_tip = $('#transmit_error_tip');
	}
	
	if(checkEmails(priv)){
		$(this).parent().parent().find('input[type=text]').val(new_arr.join(','));
		$(".add_email_box").css("display","none");

		obj_error_tip.hide();
	}else{

		$("#priv_emails").focus().val(priv);
		obj_error_tip.show();
	}
});


//添加多个邮箱
function addEmail(obj_this){
	var email_email = $(obj_this).parent().find('input[type=text]').val();
	$(obj_this).next().css("display","block");
	$(".edit_email").val(email_email);
}

//多个邮箱取消
$(".group_chanle").live('click',function(){
	$(".add_email_box").css("display","none");
	$("#transmit_error_tip").css("display","none");
	$('#newTrans').attr('onclick',$('#newTrans').attr('data-onclick'));
	$(this).parent().parent().next().hide();
})

$('.edit_email').live("keyup", function() {
	var emailStr = $(this).val();
	var emailArr = emailStr.replace('，',',').split(',');
	var q = emailArr[(emailArr.length-1)];
	
	if(q == '') {
		$(this).parent().find('.choose_emain_or_group_box').hide();
		return;
	}
	
	if(window.emailGroupOrEmail == undefined) {
		$(this).parent().find('.choose_emain_or_group_box').show();
		
		var param = 'random=' + Math.random();
		var reqUrl = U('Corp/service/loadEmailOrEmailGroup');
		wx.sendData(reqUrl,param,
			function(data) {
				window.emailGroupOrEmail = data;
				$('.edit_email').keyup();
			});
		
		return;
	}
	
	
	var result = [];
	window.emailGroupOrEmail.forEach(function(v) {
		if(v.indexOf(q) !== -1) {
			result.push(v);
		}
	});
	
	var li_html = '';
	result.forEach(function(v) {
		li_html += '<li class="email_option">'+HTMLEnCode(v)+'</li>'
	});
	
	if(li_html == '') {
		li_html = '<li onclick="$(this).parent().parent().hide();">'+window.Think.LANG.UN_MATCH_CONTACT+'</li>';
	}
	$(this).parent().find('.choose_emain_or_group_box').show().html('<ul>'+li_html+'</ul>');
});

$('.email_option').live('click',function() {
	var obj_textarea = $(this).parent().parent().parent().find('.edit_email');
	var emailStr = obj_textarea.val();
	var emailArr = emailStr.replace('，',',').split(',');
	emailArr[(emailArr.length-1)] = $(this).html();
	
	obj_textarea.val(HTMLDeCode(emailArr.join(','))+',');
	obj_textarea.parent().find('.choose_emain_or_group_box').hide();
	obj_textarea.focus();
});
