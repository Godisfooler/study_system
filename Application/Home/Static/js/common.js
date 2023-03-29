//ie8兼容
if (!Array.prototype.forEach)  
{  
    Array.prototype.forEach = function(fun /*, thisp*/)  
    {  
        var len = this.length;  
        if (typeof fun != "function")  
            throw new TypeError();  
  
        var thisp = arguments[1];  
        for (var i = 0; i < len; i++)  
        {  
            if (i in this)  
                fun.call(thisp, this[i], i, this);  
        }  
    };  
}  
/**
 * 移除某个元素
 */
if (!Array.prototype.remove)  
{  
	Array.prototype.remove = function(val /*, thisp*/)  
	{  
		var index = this.indexOf(val);
		if (index > -1) {
			this.splice(index, 1);
		} 
	};  
}  

/*js toFixed 精度修改*/
Number.prototype.myToFixed = function(s) {  
    var changenum = (parseInt(this * Math.pow(10, s) + 0.5) / Math.pow(10, s)).toString();  
    index = changenum.indexOf(".");  
    if (index < 0 && s > 0) {  
        changenum = changenum + ".";  
        for (i = 0; i < s; i++) {  
            changenum = changenum + "0";  
        }  

    } else {  
        index = changenum.length - index;  
        for (i = 0; i < (s - index) + 1; i++) {  
            changenum = changenum + "0";  
        }  

    }  

    return changenum;  
}

/*
 * 获取鼠标位置
 */
function mousePosition(evt){
	var xPos, yPos;
	evt = evt ? evt : (window.event ? window.event :null);
	if (evt.pageX) {         
		xPos=evt.pageX;         
		yPos=evt.pageY;     
	} else {         
		xPos=evt.clientX + document.body.scrollLeft - document.body.clientLeft;
		yPos=evt.clientY + document.body.scrollTop - document.body.clientTop;     
	}
	return [xPos, yPos];
}

/*
 * base64加密解密
 */
function base64encode(str) {
	var out, i, len, base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
	var c1, c2, c3;
	len = str.length;
	i = 0;
	out = "";
	while (i < len) {
		c1 = str.charCodeAt(i++) & 0xff;
		if (i == len) {
			out += base64EncodeChars.charAt(c1 >> 2);
			out += base64EncodeChars.charAt((c1 & 0x3) << 4);
			out += "==";
			break
		}
		c2 = str.charCodeAt(i++);
		if (i == len) {
			out += base64EncodeChars.charAt(c1 >> 2);
			out += base64EncodeChars.charAt(((c1 & 0x3) << 4)
					| ((c2 & 0xF0) >> 4));
			out += base64EncodeChars.charAt((c2 & 0xF) << 2);
			out += "=";
			break
		}
		c3 = str.charCodeAt(i++);
		out += base64EncodeChars.charAt(c1 >> 2);
		out += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
		out += base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6));
		out += base64EncodeChars.charAt(c3 & 0x3F)
	}
	return out
}
function base64decode(str) {
	var c1, c2, c3, c4, base64DecodeChars = new Array(-1, -1, -1, -1, -1, -1,
			-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
			-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
			-1, -1, -1, 62, -1, -1, -1, 63, 52, 53, 54, 55, 56, 57, 58, 59, 60,
			61, -1, -1, -1, -1, -1, -1, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
			11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1,
			-1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38,
			39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1,
			-1);
	var i, len, out;
	len = str.length;
	i = 0;
	out = "";
	while (i < len) {
		do {
			c1 = base64DecodeChars[str.charCodeAt(i++) & 0xff]
		} while (i < len && c1 == -1);
		if (c1 == -1)
			break;
		do {
			c2 = base64DecodeChars[str.charCodeAt(i++) & 0xff]
		} while (i < len && c2 == -1);
		if (c2 == -1)
			break;
		out += String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4));
		do {
			c3 = str.charCodeAt(i++) & 0xff;
			if (c3 == 61)
				return out;
			c3 = base64DecodeChars[c3]
		} while (i < len && c3 == -1);
		if (c3 == -1)
			break;
		out += String.fromCharCode(((c2 & 0XF) << 4) | ((c3 & 0x3C) >> 2));
		do {
			c4 = str.charCodeAt(i++) & 0xff;
			if (c4 == 61)
				return out;
			c4 = base64DecodeChars[c4]
		} while (i < len && c4 == -1);
		if (c4 == -1)
			break;
		out += String.fromCharCode(((c3 & 0x03) << 6) | c4)
	}
	return out
}
function utf16to8(str) {
	var out, i, len, c;
	out = "";
	len = str.length;
	for (i = 0; i < len; i++) {
		c = str.charCodeAt(i);
		if ((c >= 0x0001) && (c <= 0x007F)) {
			out += str.charAt(i)
		} else if (c > 0x07FF) {
			out += String.fromCharCode(0xE0 | ((c >> 12) & 0x0F));
			out += String.fromCharCode(0x80 | ((c >> 6) & 0x3F));
			out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F))
		} else {
			out += String.fromCharCode(0xC0 | ((c >> 6) & 0x1F));
			out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F))
		}
	}
	return out
}
function utf8to16(str) {
	var out, i, len, c;
	var char2, char3;
	out = "";
	len = str.length;
	i = 0;
	while (i < len) {
		c = str.charCodeAt(i++);
		switch (c >> 4) {
		case 0:
		case 1:
		case 2:
		case 3:
		case 4:
		case 5:
		case 6:
		case 7:
			out += str.charAt(i - 1);
			break;
		case 12:
		case 13:
			char2 = str.charCodeAt(i++);
			out += String.fromCharCode(((c & 0x1F) << 6) | (char2 & 0x3F));
			break;
		case 14:
			char2 = str.charCodeAt(i++);
			char3 = str.charCodeAt(i++);
			out += String.fromCharCode(((c & 0x0F) << 12)
					| ((char2 & 0x3F) << 6) | ((char3 & 0x3F) << 0));
			break
		}
	}
	return out
}

/**
 * 生成翻页
 * @param totalCount
 * @param eachPageCount
 * @param nowPage
 * @param flag
 * @returns {String}
 * @author colin
 */
function getPager(totalCount,eachPageCount,nowPage,flag) {
	var pageCount = Math.ceil(totalCount/eachPageCount);

	if(pageCount<=1) {
		return '';
	}else if(pageCount<=7) {
		var pageArr = [];
		pageArr.push({'page':nowPage-1,'name':ThinkPHP.LANG.UP_ROW,'classes':'previous'});
		for(var i=1;i<=pageCount;i++) {
			if(i == nowPage) {
				pageArr.push({'page':i,'name':i.toString(),'classes':'active'});
			}else{
				pageArr.push({'page':i,'name':i.toString(),'classes':''});
			}
		}
		if(nowPage == pageCount) {
			pageArr.push({'page':0,'name':ThinkPHP.LANG.DOWN_ROW,'classes':'next'});
		}else{
			pageArr.push({'page':nowPage+1,'name':ThinkPHP.LANG.DOWN_ROW,'classes':'next'});
		}
	}else if(nowPage-3 <= 1) {
		var pageArr = [];
		pageArr.push({'page':nowPage-1,'name':ThinkPHP.LANG.UP_ROW,'classes':'previous'});
		for(var i=1;i<=nowPage+3;i++) {
			if(i == nowPage) {
				pageArr.push({'page':i,'name':i.toString(),'classes':'active'});
			}else{
				pageArr.push({'page':i,'name':i.toString(),'classes':''});
			}
		}
		if( (pageCount-nowPage==4)){

		}else{
			pageArr.push({'page':0,'name':'...','classes':''});
		}

		pageArr.push({'page':pageCount,'name':pageCount.toString(),'classes':''});
		if(nowPage == pageCount) {
			pageArr.push({'page':0,'name':ThinkPHP.LANG.DOWN_ROW,'classes':'next'});
		}else{
			pageArr.push({'page':nowPage+1,'name':ThinkPHP.LANG.DOWN_ROW,'classes':'next'});
		}
	}else if(nowPage+3 >= pageCount) {
		var pageArr = [];
		pageArr.push({'page':nowPage-1,'name':ThinkPHP.LANG.UP_ROW,'classes':'previous'});
		pageArr.push({'page':1,'name':'1','classes':''});
		if(nowPage== 5 || pageCount-nowPage==4){

		}else{
			pageArr.push({'page':0,'name':'...','classes':''});
		}
		for(var i=nowPage-3;i<=pageCount;i++) {
			if(i == nowPage) {
				pageArr.push({'page':i,'name':i.toString(),'classes':'active'});
			}else{
				pageArr.push({'page':i,'name':i.toString(),'classes':''});
			}
		}
		if(nowPage == pageCount) {
			pageArr.push({'page':0,'name':ThinkPHP.LANG.DOWN_ROW,'classes':'next'});
		}else{
			pageArr.push({'page':nowPage+1,'name':ThinkPHP.LANG.DOWN_ROW,'classes':'next'});
		}
	}else{
		var pageArr = [];
		pageArr.push({'page':nowPage-1,'name':ThinkPHP.LANG.UP_ROW,'classes':'previous'});
		pageArr.push({'page':1,'name':'1','classes':''});
		if(nowPage== 5 ){

		}else{
			pageArr.push({'page':0,'name':'...','classes':''});
		}
		for(var i=nowPage-3;i<=nowPage+3;i++) {
			if(i == nowPage) {
				pageArr.push({'page':i,'name':i.toString(),'classes':'active'});
			}else{
				pageArr.push({'page':i,'name':i.toString(),'classes':''});
			}
		}
		if( pageCount-nowPage==4 ){

		}else{
			pageArr.push({'page':0,'name':'...','classes':''});
		}
		pageArr.push({'page':pageCount,'name':pageCount.toString(),'classes':''});
		if(nowPage == pageCount) {
			pageArr.push({'page':0,'name':ThinkPHP.LANG.DOWN_ROW,'classes':'next'});
		}else{
			pageArr.push({'page':nowPage+1,'name':ThinkPHP.LANG.DOWN_ROW,'classes':'next'});
		}
	}
		//console.log(pageArr);
		var pager_html = '<ul class="pagination">';
		
		pageArr.forEach(function(v){
			if(v.page == 0) v.classes = 'disabled';
			pager_html += '<li class="paginate_button'+flag+' '+v.classes+' " pagenum="'+v.page+'" >\
						        <a href="javascript:void(0);">'+v.name+'</a>\
						    </li>';
		});
		pager_html += '</ul>';
		
		return pager_html;
	}
/**
 * 数组转化为json 共php json_decode函数调用
 * @param o
 * @returns
 * @author colin
 */
function arrayToJson(o) { 
	var r = [];
	if (typeof o == "string")
		return "\""
				+ o.replace(/([\'\"\\])/g, "\\$1").replace(/(\n)/g, "\\n")
						.replace(/(\r)/g, "\\r").replace(/(\t)/g, "\\t")
				+ "\"";
	if (typeof o == "object") {
		if (!o.sort) {
			for ( var i in o)
				r.push("\"" + i + "\":" + arrayToJson(o[i]));
			if (!!document.all
					&& !/^\n?function\s*toString\(\)\s*\{\n?\s*\[native code\]\n?\s*\}\n?\s*$/
							.test(o.toString)) {
				r.push("toString:" + o.toString.toString());
			}
			r = "{" + r.join() + "}";
		} else {
			for (var i = 0; i < o.length; i++) {
				r.push(arrayToJson(o[i]));
			}
			r = "[" + r.join() + "]";
		}
		return r;
	}
	return o.toString();
}
/**
 * 导航条高亮
 * @param url
 */
function highlight_subnav(url) {
	$('.nav').find('a[href="' + url + '"]').addClass('only');
}
/**
 * 三位添加一个逗号
 * @param n
 * @returns
 */
function format_number(n,m) {
	if(m == undefined || m < 0) {
		var m = 0;
	}
	
	if(n < 0) {
		var flag = '-';
	}else{
		var flag = '';
	}
	
	if(isNaN(n)) {
		if(n.myToFixed != undefined) {
			n = n.myToFixed(m);
		}else{
			return n;
		}
	}else{
		n = parseFloat(n).myToFixed(m);
	}
	
	
	var n_arr = n.toString().split('.');
	
	if(n_arr[1] == undefined) {
		n_arr[1] = '';
	}else{
		n_arr[1] = '.'+n_arr[1];
	}
	
	var b = Math.abs(n_arr[0]).toString();
	var len = b.length;
	if (len <= 3) {
		return flag+b+n_arr[1];
	}
	var r = len % 3;
	return r > 0 ? flag+b.slice(0, r) + "," + b.slice(r, len).match(/\d{3}/g).join(",")+n_arr[1] : flag+b.slice(r, len).match(/\d{3}/g).join(",")+n_arr[1];
}
/**
 * 获取改id下面的子元素所有input值
 * arguments[1]默认为ture，返回选择了的字符串
 * 获取非checkbox类型input值一定要与checkbox一直
 * @param dom_id
 * @returns {String}
 * @author colin
 */
function getAllInputValue (dom_id) {
	var isChecked = typeof(arguments[1]) != "undefined" ? arguments[1] : true; 
	var Split = typeof(arguments[2]) != "undefined"?arguments[2]:''; 
	var comma = typeof(arguments[3]) != "undefined"?arguments[3]:','; 
	var text="";  
    $(dom_id).each(function() { 
    	if($(this).attr("type") == 'checkbox') {
    		if(isChecked) {
    			if ($(this).attr("checked")) {
    				text += comma+Split+$(this).val()+Split;  
    			}
    		}else{
    			if (!$(this).attr("checked")) {
    				text += comma+Split+$(this).val()+Split;  
    			}
    		}
    	}else{
    		if(isChecked) {
    			if ($(this).parent().find('input[type=checkbox]').attr("checked")) {
    				text += comma+Split+$(this).val()+Split;  
    			}
    		}else{
    			if (!$(this).parent().find('input[type=checkbox]').attr("checked")) {
    				text += comma+Split+$(this).val()+Split;  
    			}
    		}
    	}
    });
    text = text.substr(1);
    return text;
}
/**
 * 获取改id下面的子元素所有input值和标签值
 * arguments[1]默认为ture，返回选择了的字符串
 * @param dom_id
 * @returns {String}
 * @author colin
 */
function getAllInputValueAndName (dom_id) {
	var isChecked = typeof(arguments[1]) != "undefined" ? arguments[1] : true; 
	var text=[];  
	$(dom_id+" input").each(function() {  
		if(isChecked) {
			if ($(this).attr("checked")) {
				text.push({value:$(this).val(),name:$(this).next('label').html()});  
			}
		}else{
			if (!$(this).attr("checked")) {
				text.push({value:$(this).val(),name:$(this).next('label').html()});
			}
		}
	});
	return text;
}
/**
 * 获取input值，name属性相同合并成一个数组
 * @param dom_id
 * @returns {Object}
 */
function getAllInputNameValue(dom_id) {
	var isChecked = arguments[1]===false ? arguments[1] : true; 
	var text = new Object();  
	var name = '';
    $(dom_id+" input").each(function() {
    	name = $(this).attr("name");
    	if($(this).attr('type') == 'text') {
    		if('undefined' == typeof text[name])
				text[name] = $(this).val();
			else
				text[name] += ","+$(this).val(); 
    	}else{
    		if(isChecked) {
    			if ($(this).attr("checked")) {
    				if('undefined' == typeof text[name])
    					text[name] = $(this).val();  
    				else
    					text[name] += ","+$(this).val();  
    			}
    		}else{
    			if (!$(this).attr("checked")) {
    				if('undefined' == typeof text[name])
    					text[name] = $(this).val();  
    				else
    					text[name] += ","+$(this).val();  
    			}
    		}
    	}
    });
    return text;
}
/**
 * 获取input值，name属性相同合并成一个数组并指导attr作为值
 * @param dom_id
 * @returns {Object}
 */
function getAllInputNameValueAssignNameAndValue(dom_id,attr_name,attr_value,checked,split_str) {
	if(split_str == undefined) split_str = ',';
	
	var text = new Object();  
	var name = '';
	var value = '';
	$(dom_id+" input").each(function() {
		name = $(this).attr(attr_name);
		value = $(this).attr(attr_value);
		if(value == '' || (checked !== '' && !$(this).attr(checked))) return;
		
		if('undefined' == typeof text[name])
			text[name] = value;
		else
			text[name] += split_str+value; 
	});
	return text;
}
/**
 * 判断对象是否为空
 * @param obj
 * @returns {Boolean}
 */
function isNotNullObj(obj) {
	for ( var i in obj) {
		if (obj.hasOwnProperty(i)) {
			return true;
		}
	}
	return false;
}
/**
 * post形式往新窗口中提交数据
 * @param url
 * @param args [[key,value]]
 * @param name 窗口名
 * @author colin
 */
function openPostWindow(url, args, name){
	  var tempForm = document.createElement("form");
	  tempForm.id="tempForm";
	  tempForm.method="post";
	  tempForm.action=url;
	  tempForm.target=name;
	  tempForm.style.display="none";
	  
	//可传入多个参数
	  for(var i=0; i<args.length; i++){
	    var hideInput = document.createElement("input");
	    hideInput.type="hidden";
	    hideInput.name=args[i][0];
	    hideInput.value=args[i][1];
	    tempForm.appendChild(hideInput);  
	  }
	
	  tempForm.addEventListener("onsubmit",function(){ window.open("about:blank",name,"directories=no,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no"); });
	  document.body.appendChild(tempForm);
	  tempForm.submit();
	  document.body.removeChild(tempForm);
}
/**
 * 时间戳转日期
 * @param now
 * @returns {String}
 * @author colin
 */
function formatDate(style,now) {   
	var d = new Date(parseFloat(now)*1000);
    var   year=d.getFullYear();  
    var   month=d.getMonth()+1;     
    var   date=d.getDate();     
    var   hour=d.getHours();     
    var   minute=d.getMinutes();     
    var   second=d.getSeconds();
    
    style = style.replace(/Y/,year);
    style = style.replace(/m/,changeTwo(month));
    style = style.replace(/d/,changeTwo(date));
    
    if(hour == 0 && minute == 0 && second == 0) {
    	style = style.replace(/H.*?$/,'');
    }else{
    	style = style.replace(/H/,changeTwo(hour));
    	style = style.replace(/i/,changeTwo(minute));
    	style = style.replace(/s/,changeTwo(second));
    }
    
    style = $.trim(style);
    
    return style;     
} 
/**
 * 不足两位，自动补零
 * @param str
 * @returns
 */
function changeTwo(str)  
{  
	var temp_str = str.toString();
	if(temp_str.length == 1) {
		return '0'+temp_str;
	}else{
		return temp_str;
	}
}
/**
 * 截取字符串
 * @param str 字符串
 * @param len 截取长度
 * @returns str
 */
function cut_str(str, len ,addPoint){
	if(typeof str !== 'string') return str;
	
    var char_length = 0;
    for (var i = 0; i < str.length; i++){
        var son_str = str.charAt(i);
        (/[\u4e00-\u9fa5]+/).test(son_str) ? char_length += 1 : char_length += 0.5;
        if (char_length > len){
            var sub_len = char_length == len ? i+1 : i;
            var Point = addPoint?'...':'';
            return str.substr(0, sub_len)+Point;
            break;
        }
    }
    return str.substr(0, sub_len);
}
/**
 * 模拟U函数
 * @param url
 * @param params
 * @returns {string}
 * @constructor
 */
function U(url, params, rewrite) {
    if (window.Think.MODEL[0] == 2) {

        var website = window.Think.ROOT + '/';
        url = url.split('/');

        if (url[0] == '' || url[0] == '@')
            url[0] = APPNAME;
        if (!url[1])
            url[1] = 'Index';
        if (!url[2])
            url[2] = 'index';
        website = website + '' + url[0] + '/' + url[1] + '/' + url[2];

        if (params) {
            params = params.join('/');
            website = website + '/' + params;
        }
        if (!rewrite) {
            website = website + '.html';
        }

    } else {
        var website = window.Think.ROOT + '/index.php';
        url = url.split('/');
        if (url[0] == '' || url[0] == '@')
            url[0] = APPNAME;
        if (!url[1])
            url[1] = 'Index';
        if (!url[2])
            url[2] = 'index';
        website = website + '?s=/' + url[0] + '/' + url[1] + '/' + url[2];
        if (params) {
            params = params.join('/');
            website = website + '/' + params;
        }
        if (!rewrite) {
            website = website + '.html';
        }
    }

    if(typeof (window.Think.MODEL[1])!='undefined'){
        website=website.toLowerCase();
    }
    return website;
}


function R(url, params) {
	var website = '';
    if (params) {
        params = params.join('&');
    }
    
    website = url + '?' + params;
    
    return website;
}

/**
*跨域访问资源（添加资源host）
*/
function IpStatic(url) {
	if(typeof url !== 'string') return window.Think.LOADING_IP+url;
	
	var beginCode = url.substr(0,1);
	if(beginCode == '.') {
		url = url.substr(1);
	}else if(beginCode != '/') {
		url = '/'+url;
	}
	
	return window.Think.LOADING_IP+url;
}
/**
 * 正在加载效果显示
 * @param id
 */
function loadingShow(id) {
	$("#"+id).html('<div class="loading"><img  src="'+window.Think['PUBLIC']+'/images/ajax-loader.gif"></div>');
}
function loadingShowSmall(id,style) {
	$("#"+id).html('<div class="loading" style="'+style+'"><img  src="'+window.Think['PUBLIC']+'/images/ajax-loader-small.gif"></div>');
}
function loadingBtnShowSmall(id,style) {
	$("#"+id).html('<div class="loading" style="'+style+'"><img class="Rotation" src="'+window.Think['PUBLIC']+'/images/load_img.png"></div>');
}
function loadingError(id,content,style) {
	$("#"+id).html('<div class="loading" style="'+style+'">'+content+'</div>');
}

/**
 * 去出html标签
 * @param str
 * @returns
 */
function strip_tags(str) {
	if(typeof str !== 'string') return '';
	
	return str.replace(/<[^>].*?>/g,"");
}
/**
 * 加载进度条
 */
function loading_span_width_control(persent) {
	$('.loading_inner').css('width',parseInt(persent*16)*11+'px');
}

//---------------------------------------------------  
//日期格式化  
//格式 YYYY/yyyy/YY/yy 表示年份  
//MM/M 月份  
//W/w 星期  
//dd/DD/d/D 日期  
//hh/HH/h/H 时间  
//mm/m 分钟  
//ss/SS/s/S 秒  
//---------------------------------------------------  
Date.prototype.Format = function(formatStr)   
{   
var str = formatStr;   
var month = this.getMonth()+1;
var Week = ['日','一','二','三','四','五','六'];  
	
	str=str.replace(/yyyy|YYYY/,this.getFullYear());   
	str=str.replace(/yy|YY/,(this.getYear() % 100)>9?(this.getYear() % 100).toString():'0' + (this.getYear() % 100));   
	
	str=str.replace(/MM/,month>9?month.toString():'0' + month);   
	str=str.replace(/M/g,month);   
	
	str=str.replace(/w|W/g,Week[this.getDay()]);   
	
	str=str.replace(/dd|DD/,this.getDate()>9?this.getDate().toString():'0' + this.getDate());   
	str=str.replace(/d|D/g,this.getDate());   
	
	str=str.replace(/hh|HH/,this.getHours()>9?this.getHours().toString():'0' + this.getHours());   
	str=str.replace(/h|H/g,this.getHours());   
	str=str.replace(/mm/,this.getMinutes()>9?this.getMinutes().toString():'0' + this.getMinutes());   
	str=str.replace(/m/g,this.getMinutes());   
	
	str=str.replace(/ss|SS/,this.getSeconds()>9?this.getSeconds().toString():'0' + this.getSeconds());   
	str=str.replace(/s|S/g,this.getSeconds());   

return str;   
}   

//+---------------------------------------------------  
//| 求两个时间的天数差 日期格式为 YYYY-MM-dd   
//+---------------------------------------------------  
function daysBetween(DateOne,DateTwo)  
{   
	var OneMonth = DateOne.substring(5,DateOne.lastIndexOf ('-'));  
	var OneDay = DateOne.substring(DateOne.length,DateOne.lastIndexOf ('-')+1);  
	var OneYear = DateOne.substring(0,DateOne.indexOf ('-'));  
	
	var TwoMonth = DateTwo.substring(5,DateTwo.lastIndexOf ('-'));  
	var TwoDay = DateTwo.substring(DateTwo.length,DateTwo.lastIndexOf ('-')+1);  
	var TwoYear = DateTwo.substring(0,DateTwo.indexOf ('-'));  
	
	var cha=((Date.parse(OneMonth+'/'+OneDay+'/'+OneYear)- Date.parse(TwoMonth+'/'+TwoDay+'/'+TwoYear))/86400000);   
	return Math.abs(cha);  
}  

//+---------------------------------------------------  
//| 日期计算  
//+---------------------------------------------------  
Date.prototype.DateAdd = function(strInterval, Number) {   
  var dtTmp = this;  
  switch (strInterval) {   
      case 's' :return new Date(Date.parse(dtTmp) + (1000 * Number));  
      case 'n' :return new Date(Date.parse(dtTmp) + (60000 * Number));  
      case 'h' :return new Date(Date.parse(dtTmp) + (3600000 * Number));  
      case 'd' :return new Date(Date.parse(dtTmp) + (86400000 * Number));  
      case 'w' :return new Date(Date.parse(dtTmp) + ((86400000 * 7) * Number));  
      case 'q' :return new Date(dtTmp.getFullYear(), (dtTmp.getMonth()) + Number*3, dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds());  
      case 'm' :return new Date(dtTmp.getFullYear(), (dtTmp.getMonth()) + Number, dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds());  
      case 'y' :return new Date((dtTmp.getFullYear() + Number), dtTmp.getMonth(), dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds());  
  }  
}
//日期转时间戳
function DateToUnix(string) {
	var f = string.split(' ', 2);
	var d = (f[0] ? f[0] : '').split('-', 3);
	var t = (f[1] ? f[1] : '').split(':', 3);
	return (new Date(
			parseInt(d[0], 10) || null,
			(parseInt(d[1], 10) || 1) - 1,
			parseInt(d[2], 10) || null,
			parseInt(t[0], 10) || null,
			parseInt(t[1], 10) || null,
			parseInt(t[2], 10) || null
		)).getTime() / 1000;
}
/**
 * 选择指定class input
 */
function selectInputByClass(input_class) {
	$(input_class).each(function(){
		$(this).attr("checked","checked");
		this.indeterminate = false;
	});
}

/**
 * 取消选择指定class input
 */
function cansleSelectInputByClass(input_class) {
	$(input_class).each(function(){
		$(this).removeAttr("checked");
		this.indeterminate = false;
	});
}

/**
 * 点击input切换对应input选择状态
 * @param obj_this
 * @param input_class
 */
function clickInputToggleselectInputByClass(obj_this,input_class) {
	if($(obj_this).attr("checked")) {
		selectInputByClass(input_class);
	}else{
		cansleSelectInputByClass(input_class);
	}
}

/**
 * input_class全选时选择input_destination，input_class全未选时取消选择input_destination
 * @param input_class
 * @param input_destination
 */
function clickInputCheckSelectAllAndSelectByClass(input_class,input_destination) {
	var isSelectAll = true;
	$(input_class).each(function(){
		if(!$(this).attr("checked")) {
			isSelectAll = false;
		}
	});
	var length = $(input_class+':checked').length;
	var obj = $(input_destination)[0];
	if(isSelectAll) {//全选
		$(input_destination).attr("checked","checked");
		obj.indeterminate = false;
	}else{
		$(input_destination).removeAttr("checked");
		
		if(length > 0){
			obj.indeterminate = true;
		}else{
			obj.indeterminate = false;
		}
	}
}
function getInputValueByClass(input_class) {
	var inputValue = '';
	$(input_class).each(function(){
		if($(this).attr("checked")) {
			inputValue += ','+$(this).val();
 		}
	});
	inputValue=inputValue.substr(1);
	return inputValue;
}
/**
 * 字符串转秒数
 * @param sString
 * @returns
 */
function strtotime(sString) {
	var Time = parseInt((new Date(sString.replace(new RegExp("-", "g"), "/")).getTime())/1000);
	return Time;
}

function changeClass(obj,willChangedClass,nowClass) {
	$(obj).removeClass(willChangedClass);
	$(obj).addClass(nowClass);
}

function quickSortObject(obj,key,sort,eval_key_str) {
	if(!isNotNullObj(obj)) return new Object();
	
	var arr = [];
	for(var id in obj) {
		arr.push(obj[id]);
	}
	if(sort == 'desc') {
		var arr_result = quickSort(arr,key);
	}else{
		var arr_result = quickSortDesc(arr,key);
	}
	
	var obj_result = new Object();
	arr_result.forEach(function(v) {
		obj_result[eval(eval_key_str)] = v;
	});
	
	return obj_result;
}

/**
 * 快速排序-desc
 * @param arr
 * @returns
 */
function quickSortDesc(arr,key) {
	if (arr.length <= 1) { return arr; }
	var pivotIndex = Math.floor(arr.length / 2);
	var pivot = arr.splice(pivotIndex, 1)[0];
	var left = [];
	var right = [];
	for (var i = 0; i < arr.length; i++){
		if (arr[i][key] >= pivot[key]) {
			left.push(arr[i]);
		} else {
			right.push(arr[i]);
		}
	}
	return quickSortDesc(left,key).concat([pivot], quickSortDesc(right,key));
}
/**
 * 快速排序
 * @param arr
 * @returns
 */
function quickSort(arr,key) {
　　if (arr.length <= 1) { return arr; }
　　var pivotIndex = Math.floor(arr.length / 2);
　　var pivot = arr.splice(pivotIndex, 1)[0];
　　var left = [];
　　var right = [];
　　for (var i = 0; i < arr.length; i++){
　　　　if (arr[i][key] < pivot[key]) {
　　　　　　left.push(arr[i]);
　　　　} else {
　　　　　　right.push(arr[i]);
　　　　}
　　}
　　return quickSort(left,key).concat([pivot], quickSort(right,key));
}
/**
 * 二次排序
 * @param arr
 * @returns
 */
function quickSortObjectDouble(obj,key,sort,eval_key_str) {
	var arrSort = [];
	if(!isNotNullObj(obj)) return new Object();
	var arr = [];
	for(var id in obj) {
		if(id != 'remove'){
			arr.push(obj[id]);
		}
	}
	
	return uniqueJsonArray(arr,key,sort,eval_key_str); //排序;
}
/**
 * 快速排序-desc
 * @param arr
 * @returns
 */
function quickSortDescDouble(key) {
	return function(a,b){
		if(a[key] > b[key]){
			return -1;
		}else if(a[key] < b[key]){
			return 1;
		}
	}
}
/**
 * 快速排序
 * @param arr
 * @returns
 */
function quickSortDouble(key) {
	return function(a,b){
		if(a[key] > b[key]){
			return 1;
		}else if(a[key] < b[key]){
			return -1;
		}
	}
}
/**
 * JSON数组去重
 * @param array jsonArray
 * @param key 根据此key名进行去重
 */
function uniqueJsonArray(arr,field,sort,eval_key_str){
	
	// 构造新数组开始
	var map = {};
	var dest = [];
	
	for(var i = 0; i < arr.length; i++){
		var item = arr[i];
		if(!map[item[field]]){
			map[item[field]] = [];
			dest.push({
				list: item[field]
			});
		}
		
		map[item[field]].push(item);
	}

	var news = [];  //新数组排序
	if(sort == 'desc'){
		news = dest.sort(quickSortDouble('list'));
	}else{
		news = dest.sort(quickSortDescDouble('list'));
	}
	
	// 还原数组
	var obj_result = new Object();//类型还原
	for(var i in news){
		var news_key = news[i]['list'];
		if(!map[news_key]) {
			continue;
		}
		map[news_key].forEach(function(v) {
			obj_result[eval(eval_key_str)] = v;
		});
	}
	
	return obj_result;
}

// 对json数据分页
function page_array(limit,page,data) {
    var flag = 0;
    var start = (page-1)*limit;
    var send = page*limit;
    var object = new Array;

    data.forEach(function(v){
        if (flag >= start && flag < send) {
            object[flag] = v;
        };
        flag++;
    });
    return object;
}

/**
 * url encode 防止+号等字符丢失
 * @param sStr
 * @returns
 */
function URLencode(sStr)   
{  
    return sStr.replace(/%/g, "%25").replace(/\+/g, '%2B').replace(/\"/g,'%22').replace(/\'/g, '%27').replace(/\//g,'%2F').replace(/\&/g, "%26");  
} 

/**
 * 格式化显示内容 ---- html
 * @param sContent
 * @returns
 */
function formatContentForHtml(sContent) {
	var RexStr = /\{09\}|\{092\}r\{092\}n|\{060\}([\s\S]+?)\{062\}/g;
	var index = 0;//fixed 图片在首段bug fixed
	
	if(sContent === undefined) {
		return '';
	}

	sContent = sContent.replace(RexStr,  
        function(MatchStr){
			index++;
            switch(MatchStr){  
                case "{09}":  
                    return "　　";  
                    break;  
                case "{092}r{092}n":  
                    return "<br/>";  
                    break;  
                default :  
					var url = MatchStr.replace('{060}','').replace('{062}','');
					if(url.indexOf("|") > -1 ) { 
						url = url.split('|');
						return MatchStr = (index==1?'':'<br/>')+'<img class="small-img hover-img" data-url="'+url[1]+'" '+(get_cookie('isSmallImg') == 1?'style="max-width:100%;min-width:50px;display:inline-block;max-height:80px;"':'style="max-width:100%;min-width:50px;display:inline-block;"')+' src="'+url[0]+'" />';
					}else{
						return MatchStr = (index==1?'':'<br/>')+'<img class="small-img hover-img" data-url="'+url+'" '+(get_cookie('isSmallImg') == 1?'style="max-width:100%;min-width:50px;display:inline-block;max-height:80px;"':'style="max-width:100%;min-width:50px;display:inline-block;"')+' src="'+url+'" />';
					}
                    break;  
            }  
        }  
    ).replace(/　　/g,'<span class="content_space">　　</span>');
    
    return sContent;  
}
/**
 弹窗图片链接
 */
$('.hover-img').live('mouseover',function(){
	var length = $('.alert_link').length;
	var url = $(this).attr('data-url');
	var clipImgs = $(this).hasClass('small-img');
	if(length < 1){
		if(clipImgs == true){
			$(this).after('<a class="alert_link" data-href="'+url+'"><i class="pic-tio"></i></a><i class="pic-change pig-small"></i>');
			if(get_cookie('isSmallImg') == 1){
				$(this).nextAll('.pic-change').removeClass('pig-small').addClass('pig-big');
			}else{
				$(this).nextAll('.pic-change').removeClass('pig-big').addClass('pig-small');
			}
		}else{
			$(this).after('<a class="alert_link" data-href="'+url+'"><i class="pic-tio"></i></a>');
		}
	}
});
$('.alert_link').live('click',function(){
	var url = $(this).attr('data-href');
	window.open(url);
})
$('.hover-img').live("mouseleave",function(e){
	document.body.onmouseover = function(e){
	    e = e || window.event;
	    var node = e.target || e.srcElement;
		var dom = $('.pic-tio')[0];
		var img = $('.pic-change')[0];
	    if(node != dom && node != img){
			$('.alert_link').remove();
	    }
		if(node != img && node != dom){
			$('.pic-change').remove();
		}
	}
})
//点击图片缩小
$('.pig-small').live('click',function(){
	$.cookie('isSmallImg', '1', { expires: 365,path:'/'});
	$('.small-img').each(function(){
		$(this).css("max-height","80px");
	})
})
$('.pig-big').live('click',function(){
	$.cookie('isSmallImg', '2', { expires: 365,path:'/'});
	$('.small-img').each(function(){
		$(this).css("max-height","");
	})
})
/**
 * 格式化显示内容 ---- TextArea
 * @param sContent
 * @returns
 */
function formatContentForTextArea(sContent) {
	var RexStr = /\{09\}|\{092\}r\{092\}n/g  
		sContent = sContent.replace(RexStr,  
				function(MatchStr){  
			switch(MatchStr){  
			case "{09}":  
				return "　　";  
				break;  
			case "{092}r{092}n":  
				return "\r\n";  
				break;  
			default :  
				break;  
			}  
		}  
		)  
		return sContent;  
}
/**
 * 格式化显示内容复原 ---- TextArea
 * @param sContent
 * @returns
 */
function formatContentRecover(sContent) {
//	console.log(sContent);
	var RexStr = new RegExp(String.fromCharCode(10),"g");  
		sContent = sContent.replace(RexStr,  
				function(MatchStr){  
//			console.log(MatchStr);
			switch(MatchStr){  
			case String.fromCharCode(10):  
				return "{092}r{092}n";  
				break;  
			default :  
				break;  
			}  
		}  
		) 
		
		sContent = sContent.replace(/(\{092\}r\{092\}n)+$/ig,'')
		return sContent; 
}

/**
 * 格式化显示内容复原 ---- 富文本编辑器
 * @param sContent
 * @returns
 */
function formatContentRecoverForHtml(sContent) {
	var RexStr = new RegExp(/\<div\>|\<br\>|\<img[\s\S]+?src\=\"([\s\S]+?)\"[\s\S]*?\>/g);
	sContent = sContent.replace(/\<br\>\<img/ig,'<img');
	sContent = sContent.replace(RexStr,  
		function(MatchStr,$1){  
			switch(MatchStr){  
				case '<div>':  
				case '<br>':  
					return "{092}r{092}n";  
					break;  
				default :
					if($1 != undefined) {
						return '{060}'+$1+'{062}';
					}
					break;  
			}  
		}  
	);
	
	sContent = strip_tags(sContent);
	
	sContent = sContent.replace(/(\{092\}r\{092\}n)+$/ig,'')
	
	return sContent; 
}

/**
 * 格式化正文
 * @param obj_this
 */
function formatterContent(obj_this) {
	var sContent = $(obj_this).parents(2).find('textarea[name=sContent]').val();
	var rn = String.fromCharCode(10);
	var sContentArr = sContent.split(rn);
	sContent = '';

	sContentArr.forEach(function(v) {
		v = $.trim(v);
		if(v == '') {
			return ;
		}
		sContent += '　　'+v+rn;
	});
	
	$(obj_this).parents(2).find('textarea[name=sContent]').val(sContent)
}
function formatterContentMedia(obj_this,sContent) {
	//console.log(sContent);
	if(sContent == undefined) {
		sContent = $(obj_this).parents(2).find('div[name=sContent]').find('.w-e-text').html();
	}
	
	sContent = sContent.replace(/\<[\s\S]+?\>/g, function(match) {
    	if(/\<\/p\>|\<\/div\>|\<div\>|\<\/tr\>|\<br\>/i.test(match)) {
    		return '<br>';
    	}else if(/\<img[\s\S]+?\>/i.test(match)){//保留图片标签
    		return match;
    	}else{
    		return '';
    	}
    });
	sContent = sContent.replace(/(\<br\>){2,}|(\<br\>)+$/ig,'<br>');
	
	var rn = '<br>';
	var sContentArr = sContent.split(rn);

	sContent = '';
	sContentArr.forEach(function(v) {
		v = $.trim(HTMLDeCode(v));
		if(v == '') {
			return ;
		}
		if(/^\<img[\s\S]+?\>/i.test(v)) {
			sContent += v+rn;
		}else{
			sContent += '　　'+v+rn;
		}
	});

	$(obj_this).parents(2).find('div[name=sContent]').find('.w-e-text').html(sContent)
}
/**
 * 关键词高亮
 * @param sContent
 * @param keyName
 * @returns
 */
function highlightKeyName(sContent,keyName,flag_pre,separator) {
	var realKeyName = keyName;
	keyName = $.trim(keyName);
	if(sContent == '' || keyName == '') {
		return sContent;
	}
	
	if(flag_pre == undefined) {
		flag_pre = '';
	}
	
	if(separator == undefined) {
		separator = ',';
	}
	
	//严格处理高亮
	var keyNameArr_temp = keyName.split(separator);
	var i = 0;
	keyNameArr_temp.forEach(function(v) {
		if(isItStrict(v)) {
			v = strictedKeyName(v,true,flag_pre);
		}else{
			v = strictedKeyName(v,false,flag_pre);
		}
		
		
		keyNameArr_temp[i++] = v;
	});
	keyName = keyNameArr_temp.join(separator);
	
//	console.log(keyName);
//	return ;
	
	var keyNameArr = new Array();
	keyNameArr.push(keyName);
	
	var simplizedKeyName = simplized(keyName);
	if(keyNameArr.indexOf(simplizedKeyName) == -1) {
		keyNameArr.push(simplizedKeyName);
	}
	
	var traditionalizedKeyName = traditionalized(keyName);
	if(keyNameArr.indexOf(traditionalizedKeyName) == -1) {
		keyNameArr.push(traditionalizedKeyName);
	}
	
	var regRul_temp = new RegExp(''+separator+'',"ig");
	var regString = (keyNameArr.join('|')).replace(regRul_temp,'|').replace(/\*/ig,'\\*').replace(/\+/ig,'\\+').replace(/\./ig,'\\.').replace(/\|\|/ig,'|').replace(/\|$|^\|/ig,'');
	
	if(regString.indexOf('%2B') !== -1) {
		regString = regString.replace(/\%2B/ig,'\\+')+'|'+regString.replace(/\%2B/ig,'＋');
	}
	
//	console.log(regString);
	
//	console.log(sContent);
//	console.log(keyName);
//	console.log(regString,separator);
	
	var RexStr = new RegExp('(?:'+regString+')',"ig");  
	sContent = sContent.replace(RexStr, 	function(MatchStr){  
//			console.log(arguments);
//			console.log(regString);
			var arg_length = arguments.length-2;
			for(var i = 1;i < arg_length;i++) {
				if(arguments[i] != undefined && $.trim(arguments[i]) != '') {
					if(window.notKeyNameArr === undefined) {
						return MatchStr.replace(arguments[i],"<span class=\"key_color\">"+arguments[i]+"</span>")+'<!---->';
					}
					
					var pos = arguments[arg_length];
					var keyName = arguments[i];
					if(isPassKeyNotDeal(pos,keyName,sContent)) {
						return MatchStr.replace(arguments[i],"<span class=\"key_color\">"+arguments[i]+"</span>")+'<!---->';
					}else{
						return MatchStr;
					}
				}
			}
			
			return MatchStr;
		});
	
	if(flag_pre == '') {
		sContent = highlightKeyName(sContent,realKeyName,'\<\!\-\-\-\-\>',separator);
	}
	sContent = sContent.replace(/\<\!\-\-\-\-\>/ig,'');
	
	return sContent;
}

function strictedKeyName(keyName,isItStrict,flag_pre) {
	if(isItStrict) {
		var preg_pre = '(?:\&[a-z]{3,5};|[^a-z0-9\@\_\&\-]|^)';
		var preg_end = '(?:\&[a-z]{3,5};|[^a-z0-9\@\_\&\-]|$)';
	}else{
		var preg_pre = '';
		var preg_end = '';
	}
     
     if(isArray(keyName)) {
    	 var i = 0;
    	 keyName.forEach(function (v) {
    		 keyName[i++] = preg_pre+'('+flag_pre+v.replace(/\-/ig,'\\-').replace(/\(/ig,'\\(').replace(/\)/ig,'\\)')+')'+preg_end;
    	 });
     }else{
         keyName = preg_pre+'('+flag_pre+keyName.replace(/\-/ig,'\\-').replace(/\(/ig,'\\(').replace(/\)/ig,'\\)')+')'+preg_end;
     }
     
//     console.log(keyName);
     return keyName;
}

//console.log(isItStrict('okssdfs+f'));
function isItStrict(keyName) {
    //只处理英文关键词
    if(!/^[a-z0-9\@\_\&\s\+\-]+$/i.test(keyName)) {
        return false;
    }
    
    var keyNameArr = keyName.split('+');
    
    var keyNameArrCount = keyNameArr.length;
    //没有+号,5个字符长度代表严格匹配
    if(keyNameArrCount == 1 && keyName.length <= 5) {
        return true;
    //有+号,8个字符长度代表严格匹配
    }else if(keyNameArrCount >= 2 && keyName.length <= 8){
        return true;
    }
    
    return false;
}

function isPassKeyNotDeal(pos,realKeyName,sContent) {
//	console.log(realKeyName);
	realKeyName = $.trim(realKeyName).toUpperCase();
	keyName = simplized(realKeyName);
	if(window.notKeyName_tempArr == undefined) window.notKeyName_tempArr = new Object();
	
	if(window.notKeyNameArr[keyName] !== undefined) {
		
		 var keyNameLength = realKeyName.length;
		 if(window.notKeyName_tempArr[realKeyName] == undefined) {
             window.notKeyName_tempArr[realKeyName] = new Object();
             var pre_suf_temp_arr = new Array();
             
             window.notKeyNameArr[keyName].forEach(function(v) {
            	 var tempArr = v.split(keyName, 2);
            	 if(tempArr.length !== 2) return ;
//            	 console.log(tempArr[0]);
                 var preLen = (tempArr[0] == '')?0:tempArr[0].length;
                 var sufLen = (tempArr[1] == '')?0:tempArr[1].length;
                 if(pre_suf_temp_arr.indexOf(preLen+'|'+sufLen,pre_suf_temp_arr) === -1) {
                	 if(window.notKeyName_tempArr[realKeyName]['preLen'] == undefined) {
                		 window.notKeyName_tempArr[realKeyName]['preLen'] = new Array();
                		 window.notKeyName_tempArr[realKeyName]['sufLen'] = new Array();
                	 }
                     window.notKeyName_tempArr[realKeyName]['preLen'].push(preLen);
                     window.notKeyName_tempArr[realKeyName]['sufLen'].push(sufLen);
                     pre_suf_temp_arr.push(preLen+'|'+sufLen);
                 }
             });
         }
		 
//		 console.log(window.notKeyName_tempArr);
//		 console.log(window.notKeyNameArr[keyName]);
		 
		  var haveNotKeyName = false;
		  var i = 0;
		  window.notKeyName_tempArr[realKeyName]['preLen'].forEach(function(v) {
			  var keyName_temp = sContent.substr(pos-v,v+keyNameLength+window.notKeyName_tempArr[realKeyName]['sufLen'][i]).toUpperCase();
//			  console.log(keyName_temp);
			  if(window.notKeyNameArr[keyName].indexOf(keyName_temp) !== -1) {
//				  console.log(keyName_temp);
				  haveNotKeyName = true;
			  }
			  i++;
		  });
      
          if(haveNotKeyName === true) {
              return false;
          }else{
              return true;
          }
	}
	
	return true;
	
//	console.log(window.notKeyName_tempArr);
//	console.log(keyName);
//	console.log(pos);
//	console.log(realKeyName);
//	console.log(sContent);
//	console.log(window.notKeyNameArr);
}

function isArray(obj) {  
	return Object.prototype.toString.call(obj) === '[object Array]';   
}


function getFirstHlKey(sContent) {
	re = /(<span class="key_color">[\s\S]+?<\/span>([\s~\!@#$%\^\&*\(\)\-\\|\_\/\+,\.\'\"]*<span class="key_color">[\s\S]+?<\/span>)*)[^<]/i; 
	r = re.exec(sContent); 
	if(r == null || r == undefined || r[1] == undefined) {
		return '';
	}else{
		return r[1];
	}
}

/**
 * 获取内容中出现的第一个关键词
 */
function getFirstKeyNameForSolr(sContent,keyName) {
	var keyNameStr = formatSolrKey(keyName);
	return getFirstKeyName(sContent,keyNameStr);
}

function formatSolrKey(para) {
	var para_de_pre = '(';
	var para_de_end = ')';
	var para_de_space = ' ';
	var para_de_quot = '"';
	var para_de_quot_count = 0;
	var strLen = para.length;
	
	var str_cache = '';
	var para_de_quoted = false;
	var paraArr = new Array();
	for(i = 0;i<strLen;i++) {
		var code = para.substr(i,1);
		if(code == '\\') {
			code = mb_substr(para, i++,2);
		}
	
		if(code == para_de_quot) {
			para_de_quot_count++;
			if(1 == para_de_quot_count%2) {
				if(str_cache !== '') {
					paraArr.push(str_cache);
				}
				str_cache = code;
			}else if(0 == para_de_quot_count%2) {
				str_cache += code;
				if(str_cache !== '') {
					paraArr.push(str_cache);
				}
				str_cache = '';
			}
		}else{
			str_cache += code;
		}
	}
	if(str_cache !== '') {
		paraArr.push(str_cache);
	}
	
	var paraStr = '';
	paraArr.forEach(function (val) {
		if(/"[\s\S]+"/gi.test(val)) {
			paraStr += val.substr(1,val.length-2);
		}else{
			val = val.replace(/ AND | OR | NOT /gi,function(matchStr) {
				return '|'+matchStr+'|';
			});
			paraStr += val;
		}
	});
	
	var paraArr = paraStr.split(/\| AND \||\| OR \||\| NOT \|/gi);
	
	return paraArr.join(',');
}
/**
 * 获取内容中出现的第一个关键词
 */
function getFirstKeyNameForSolrG(sContent,keyName) {
	var keyNameStr = formatSolrKeyG(keyName,';');
	return getFirstKeyName(sContent,keyNameStr,';');
}

function formatSolrKeyG(para,separator) {
	if(separator == undefined) {
		separator = ',';
	}
	var para_de_pre = '(';
	var para_de_end = ')';
	var para_de_space = ' ';
	var para_de_quot = '"';
	var para_de_quot_count = 0;
	var strLen = para.length;
	
	var str_cache = '';
	var para_de_quoted = false;
	var paraArr = new Array();
	for(i = 0;i<strLen;i++) {
		var code = para.substr(i,1);
		if(code == '\\') {
			code = mb_substr(para, i++,2);
		}
		
		if(code == para_de_quot) {
			para_de_quot_count++;
			if(1 == para_de_quot_count%2) {
				if(str_cache !== '') {
					paraArr.push(str_cache);
				}
				str_cache = code;
			}else if(0 == para_de_quot_count%2) {
				str_cache += code;
				if(str_cache !== '') {
					paraArr.push(str_cache);
				}
				str_cache = '';
			}
		}else{
			str_cache += code;
		}
	}
	if(str_cache !== '') {
		paraArr.push(str_cache);
	}
	
	var paraStr = '';
	paraArr.forEach(function (val) {
		if(/"[\s\S]+"/gi.test(val)) {
			paraStr += val.substr(1,val.length-2);
		}else{
			val = val.replace(/ AND | NOT /gi,function(matchStr) {
				return '|'+matchStr+'|';
			});
			paraStr += val;
		}
	});
	
	var paraArr = paraStr.split(/\| AND \||\| OR \||\| NOT \|/gi);
	return paraArr.join(separator);
}

/**
 * 获取内容中出现的第一个关键词
 */
function getFirstKeyName(sContent,keyName,separator) {
	keyName = $.trim(keyName);
	if(sContent == '') {
		return keyName;
	}
	
	if(separator == undefined) {
		separator = ',';
	}
	
	//严格处理高亮
	var keyNameArr_temp = keyName.split(separator);
	var i = 0;
	keyNameArr_temp.forEach(function(v) {
//		var arr= v.split('+');
		if(isItStrict(v)) {
			v = strictedKeyName(v,true,'');
		}else{
			v = strictedKeyName(v,false,'');
		}
		
		keyNameArr_temp[i++] = v;
	});
	keyName = keyNameArr_temp.join(separator);
	
	var keyNameArr = new Array();
	keyNameArr.push(keyName);
	
	var simplizedKeyName = simplized(keyName);
	if(keyNameArr.indexOf(simplizedKeyName) == -1) {
		keyNameArr.push(simplizedKeyName);
	}
	
	var traditionalizedKeyName = traditionalized(keyName);
	if(keyNameArr.indexOf(traditionalizedKeyName) == -1) {
		keyNameArr.push(traditionalizedKeyName);
	}
	
//	var regRul_temp = new RegExp("\+|"+separator,'ig');
	var regRul_temp = new RegExp(''+separator+'',"ig");
	var regString = (keyNameArr.join('|')).replace(regRul_temp,'|').replace(/\*/ig,'\\*').replace(/\+/ig,'\\+').replace(/\|\|/ig,'|').replace(/\./ig,'\\.').replace(/\|$|^\|/ig,'');
	
	if(regString.indexOf('%2B') !== -1) {
		regString = regString.replace(/\%2B/ig,'\\+')+'|'+regString.replace(/\%2B/ig,'＋');
	}
	
	var RexStr = new RegExp(''+regString+'',"ig");  
	var mixPos = 999999;
	var keyName_temp = keyName;
	sContent.replace(RexStr, 	function(MatchStr){  
			if($.trim(MatchStr) != '') {
				var index_count = sContent.indexOf(MatchStr);
				if(index_count < mixPos) {
					mixPos = index_count;
					keyName_temp = MatchStr;
				}
			}
		});
	
	return keyName_temp;
}
/**
 * 选中文字
 * @param o
 * @param a
 * @param b
 */
function textSelect(o, a, b){
	o = o.get(0);
	//o是当前对象，例如文本域对象
	//a是起始位置，b是终点位置
	var a = parseInt(a, 10), b = parseInt(b, 10);
	var l = o.value.length;
	if(l){
		//如果非数值，则表示从起始位置选择到结束位置
		if(!a){
			a = 0;	
		}
		if(!b){
			b = l;	
		}
		//如果值超过长度，则就是当前对象值的长度
		if(a > l){
			a = l;	
		}
		if(b > l){
			b = l;	
		}
		//如果为负值，则与长度值相加
		if(a < 0){
			a = l + a;
		}
		if(b < 0){
			b = l + b;	
		}
		if(o.createTextRange){//IE浏览器
			var range = o.createTextRange();         
			range.moveStart("character",-l);              
			range.moveEnd("character",-l);
			range.moveStart("character", a);
			range.moveEnd("character",b);
			range.select();
		}else{
			o.setSelectionRange(a, b);
			o.focus();
		}
	}
}
/**
 * 获取光标位置
 * @param textarea
 * @returns {{text: string, start: number, end: number}}
 */
function getCursorPosition(textarea) {
	textarea = textarea.get(0);
	var rangeData = {text: "", start: 0, end: 0 };
	textarea.focus();
	if (textarea.setSelectionRange) { // W3C
		rangeData.start= textarea.selectionStart;
		rangeData.end = textarea.selectionEnd;
		rangeData.text = (rangeData.start != rangeData.end) ? textarea.value.substring(rangeData.start, rangeData.end): "";
	} else if (document.selection) { // IE
		var i,
			oS = document.selection.createRange(),
		// Don't: oR = textarea.createTextRange()
			oR = document.body.createTextRange();
		oR.moveToElementText(textarea);
		rangeData.text = oS.text;
		rangeData.bookmark = oS.getBookmark();
		// object.moveStart(sUnit [, iCount])
		// Return Value: Integer that returns the number of units moved.
		for (i = 0; oR.compareEndPoints('StartToStart', oS) < 0 && oS.moveStart("character", -1) !== 0; i ++) {
			// Why? You can alert(textarea.value.length)
			if (textarea.value.charAt(i) == '/n') {
				i ++;
			}
		}
		rangeData.start = i;
		rangeData.end = rangeData.text.length + rangeData.start;
	}
	return rangeData;
}
/**
 * 该字符串只能使用单引号括起来
 * @param str
 * @returns
 */
function singleQuotesHtmlEncode(str) {
	if(typeof str == "string") {
		return str.replace(/(')/g, "&apos;");
	}else{
		return str;
	}
}

function keyNameFormat(keyName) {
	if(typeof keyName == "string") {
		return HTMLEnCode(keyName.replace(/%2B/ig,'\'+\''));
	}else{
		return HTMLEnCode(keyName);
	}
}

/**
 * diy提交表单
 * @param param
 * @param url
 * @param method
 * @returns
 */
function submitfrom (param,url,method) {
	 // 取得要提交的参数  
   var my_val = $.trim($('#ipt').val());  
   // 创建Form  
   var form = $('<form></form>');  
   // 设置属性  
   form.attr('action', url);  
   form.attr('method', method);  
   // form的target属性决定form在哪个页面提交  
   // _self -> 当前页面 _blank -> 新页面  
   form.attr('target', '_self');  
   // 创建Input  
   
   param.forEach(function(v){
	    var my_input = $('<input type="text" name="'+v.name+'" value="'+v.value+'"/>');  
	    // 附加到Form  
	    form.append(my_input);  
	});
   // 提交表单  
   form.submit();  
   // 注意return false取消链接的默认动作  
   return false;  
}
/**
 * 根据关键词截取字符串
 * @param str 被截取的字符串
 * @param key 关键词
 * @param abstractCount 截取字数
 * @param addPoint 是否添加点点点
 * @returns {string}
 */
function getAbstract(str,key,abstractCount,addPoint) {
	var length = str.length;
	var keyLength = key.length;
	//这里修改为简繁体支持，和大小写支持
	var keyPos = str.indexOf(key);
	var point = addPoint?'...':'';
	//沒有关键词直接返回
	if(keyPos == -1) return str.substring(0,abstractCount)+(abstractCount>=length?'':point);

	var preCount = Math.floor((abstractCount - keyLength)/2);
	var start = keyPos - preCount;

	//起始位置小于等于0，则直接返回
	if(start <= 0 ) return str.substring(0,abstractCount)+(abstractCount>=length?'':point);

	var end = start + abstractCount;

	if(end >=  length-1) return (length-abstractCount-1 <= 0?'':point) + str.substring(length-abstractCount,length);

	return point + str.substring(start,end) + point;
}

function    HTMLEnCode(str)  
{  
	  if(str === undefined) {
		return "";
	  }
      var    s    =    "";  
      if    (str.length    ==    0)    return    "";  
      s    =    str.replace(/&/g,    "&amp;");  
      s    =    s.replace(/</g,        "&lt;");  
      s    =    s.replace(/>/g,        "&gt;");  
      s    =    s.replace(/    /g,        "&nbsp;");  
      s    =    s.replace(/\'/g,      "'");  
      s    =    s.replace(/\"/g,      "&quot;");  
      s    =    s.replace(/\n/g,      " <br>");  
      return    s;  
}  
function    HTMLDeCode(str)  
{  
      var    s    =    "";  
      if    (str.length    ==    0)    return    "";  
      s    =    str.replace(/&amp;/g,    "&");  
      s    =    s.replace(/&lt;/g,        "<");  
      s    =    s.replace(/&gt;/g,        ">");  
      s    =    s.replace(/&nbsp;/g,        " ");  
      s    =    s.replace(/'/g,      "\'");  
      s    =    s.replace(/&quot;/g,      "\"");  
      s    =    s.replace(/ <br>/g,      "\n");  
      return    s;  
}

function sprintf() {
	var i = 0, a, f = arguments[i++], o = [], m, p, c, x, s = '';
	while (f) {
		if (m = /^[^\x25]+/.exec(f)) {
			o.push(m[0]);
		}
		else if (m = /^\x25{2}/.exec(f)) {
			o.push('%');
		}
		else if (m = /^\x25(?:(\d+)\$)?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/.exec(f)) {
			if (((a = arguments[m[1] || i++]) == null) || (a == undefined)) {
				throw('Too few arguments.');
			}
			if (/[^s]/.test(m[7]) && (typeof(a) != 'number')) {
				throw('Expecting number but found ' + typeof(a));
			}
			switch (m[7]) {
				case 'b': a = a.toString(2); break;
				case 'c': a = String.fromCharCode(a); break;
				case 'd': a = parseInt(a); break;
				case 'e': a = m[6] ? a.toExponential(m[6]) : a.toExponential(); break;
				case 'f': a = m[6] ? parseFloat(a).myToFixed(m[6]) : parseFloat(a); break;
				case 'o': a = a.toString(8); break;
				case 's': a = ((a = String(a)) && m[6] ? a.substring(0, m[6]) : a); break;
				case 'u': a = Math.abs(a); break;
				case 'x': a = a.toString(16); break;
				case 'X': a = a.toString(16).toUpperCase(); break;
			}
			a = (/[def]/.test(m[7]) && m[2] && a >= 0 ? '+'+ a : a);
			c = m[3] ? m[3] == '0' ? '0' : m[3].charAt(1) : ' ';
			x = m[5] - String(a).length - s.length;
			p = m[5] ? str_repeat(c, x) : '';
			o.push(s + (m[4] ? a + p : p + a));
		}
		else {
			throw('Huh ?!');
		}
		f = f.substring(m[0].length);
	}
	return o.join('');
}
function str_repeat(i, m) {
	for (var o = []; m > 0; o[--m] = i);
	return o.join('');
}

/**
 * 获取焦点
 * @param obj
 * @returns
 */
function getFocus(obj,isFocus) {
	if(isFocus) {
		obj.focus();
	}
	var result = obj.val();
	obj.val('');
	obj.val(result);
	obj[0].scrollLeft=result.length;
}

//科学计数法转换金额
function formaUnit(number){
    if(number > 1000000000){
        return formatMoney(number/1000000000,2,"")+"B";
    }else if(number > 1000000){
        return formatMoney(number/1000000,2,"")+"M";
    }else if(number > 1000){
        return formatMoney(number/1000,2,"")+"K";
    }else{
        return formatMoney(number,2,"");
    }
}
function formatMoney(number, places, symbol, thousand, decimal) {
    number = number || 0;
    places = !isNaN(places = Math.abs(places)) ? places : 2;
    symbol = symbol !== undefined ? symbol : "$";
    thousand = thousand || ",";
    decimal = decimal || ".";
    var negative = number < 0 ? "-" : "",
        i = parseInt(number = Math.abs(+number || 0).myToFixed(places), 10) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).myToFixed(places).slice(2) : "");
}

//模糊匹配检测
window.pregVagueKeyArr = new Object();
function pregVagueKey(key,str) {
	if(str == '' || key == '') {
		return -1;
	}
	if(window.pregVagueKeyArr[str] === undefined) {
		str = window.pregVagueKeyArr[str] = simplized(str.toUpperCase());
	}else{
		str = window.pregVagueKeyArr[str];
	}
	
	if(window.pregVagueKeyArr[key] === undefined) {
		key = window.pregVagueKeyArr[key] = simplized(key.toUpperCase());
	}else{
		key = window.pregVagueKeyArr[key];
	}
	
	return str.indexOf(key);
}

//模糊高亮
function pregVagueKeyHeight(key,str,pre,end) {
	if(str == '' || key == '') {
		return sContent;
	}

	var addSlash = arguments[4] ? arguments[4] : false;
	
	var keyNameArr = new Array();
	keyNameArr.push(key);
	
	var simplizedKeyName = simplized(key);
	if(keyNameArr.indexOf(simplizedKeyName) == -1) {
		keyNameArr.push(simplizedKeyName);
	}
	
	var traditionalizedKeyName = traditionalized(key);
	if(keyNameArr.indexOf(traditionalizedKeyName) == -1) {
		keyNameArr.push(traditionalizedKeyName);
	}

	if (addSlash) {
        var regString = (keyNameArr.join('|')).replace(/\\/ig,'\\\\').replace(/\^/ig, '\^').replace(/\./ig, '\\.').replace(/\*/ig, '\\*').replace(/\+/ig, '\\+').replace(/\?/ig, '\\?').replace(/\=/ig, '\\=').replace(/\!/ig, '\\!').replace(/\:/ig, '\\:').replace(/\|/ig, '\\|').replace(/\//ig, '\\/').replace(/\(/ig, '\\(').replace(/\)/ig, '\\)').replace(/\[/ig, '\\[').replace(/\]/ig, '\\]').replace(/\{/ig, '\\{').replace(/\}/ig, '\\}').replace(/\$/ig, '\\$');
	} else {
		var regString = (keyNameArr.join('|')).replace(/\)/ig,'\\)').replace(/\(/ig,'\\(').replace(/\*/ig,'\\*').replace(/\+/ig,'\\+').replace(/\./ig,'\\.').replace(/\|\|/ig,'|').replace(/\|$|^\|/ig,'');
	}
	var RexStr = new RegExp('/(?:'+regString+')/',"ig");  
	str = str.replace(RexStr, 	function(MatchStr){  
			var arg_length = arguments.length-2;
			for(var i = 0;i < arg_length;i++) {
				if(arguments[i] != undefined && $.trim(arguments[i]) != '') {
					return MatchStr.replace(arguments[i],pre+arguments[i]+end);
				}
			}
			
			return MatchStr;
		});
	return str;
}

//字符串正则转义
function strAddSlashReg(str) {
	str = str.replace('^', '\^')
	.replace('.', '\\.')
	.replace('*', '\\*')
	.replace('+', '\\+')
	.replace('?', '\\?')
	.replace('=', '\\=')
	.replace('!', '\\!')
	.replace('|', '\\|')
	.replace('/', '\\/')
	.replace('\\', '\\\\')
	.replace('(', '\\(')
	.replace(')', '\\)')
	.replace('[', '\\[')
	.replace(']', '\\]')
	.replace('{', '\\{')
	.replace('}', '\\}')
	.replace('$', '\\$');
	
	return str;
};

/**
 * 获取cookie值
 */
function get_cookie(Name) {
	var search = Name + "="
	var returnvalue = "";
	if (document.cookie.length > 0) {
		offset = document.cookie.indexOf(search)
		if (offset != -1) { // if cookie exists
			offset += search.length
			end = document.cookie.indexOf(";", offset);
			if (end == -1)
				end = document.cookie.length;
			returnvalue=unescape(document.cookie.substring(offset, end))
		}
	}
	return returnvalue;
}
/**
 * 新闻弹窗简繁转换
 */
$('.click-lang').live('click',function(){
	$(this).parent().find('.select-lang').toggle();
	if(get_cookie('doubleLang') == 2){
		$(this).parent().find('#doubleLang').removeAttr('checked');
	}else{
		$(this).parent().find('#doubleLang').attr('checked','checked');
	}
});
$('#doubleLang').live('click',function(){
	if($(this).is(':checked')){
		$.cookie('doubleLang', '1', { expires: 365,path:'/'});
	}else{
		$.cookie('doubleLang', '2', { expires: 365,path:'/'});
	}
	var lang = $(this).parents('.language_box').attr("data-value");
	var contentlanguage = $('.select-lang div[data-value=original]').attr('date-orglang');
	if(lang == "original"){
		lang = contentlanguage;
		var content = $(".news_content_text").html();
	}
	var langArrs = [lang,contentlanguage];
	if(window.nowLanguage == ''){
		return;
	}else{
		if(window.noLang == true){
			if(lang != "original"){
				$(".news_content_text").html(' '+ThinkPHP.LANG.TRANSLATE_QUOTA_NOT_ENOUGH);
			}else{
				$(".news_content_text").html(content);
			}
			$('.save_news_img').fadeIn(200);
			return;
		}
		var checked = $(this).parents('.language_box').find('#doubleLang').attr('checked');
		if(checked != undefined && contentlanguage != lang && !(langArrs.indexOf('zh-cn') > -1 && langArrs.indexOf('zh-tw') > -1 )){
			$(".news_content_text").html(window.langContent['double'][lang].replace('undefined',''));
			$('.save_news_img').fadeIn(200);
		}else{
			$(".news_content_text").html(window.langContent['single'][lang].replace('undefined',''));
			$('.save_news_img').fadeIn(200);
		}
	}
});
function LangSelectCommon(originalLang,english,kolish){
	window.nowLanguage = '';
	window.langs = [];
	window.noLang = false;
	window.langContent = {"single":{},"double":{}};
	if(originalLang === undefined) {
		originalLang = '';
	}

	var selectHtml = '<span  class="language_box">\
		<span class="click-lang"><span class="language_name">'+ThinkPHP.LANG.LANG_1+'</span><img class="lang-img" src="'+window.Think['PUBLIC']+'/images/select-lang.png" /></span>\
			<div class="select-lang">\
				<p data-value="0"><input type="checkbox" id="doubleLang" checked="checked"><label for="doubleLang">'+ThinkPHP.LANG.LANG_0+'</label></p>\
				<div data-value="original" date-orglang="'+originalLang+'">'+ThinkPHP.LANG.LANG_1+'</div>\
				<div data-value="zh-cn">'+ThinkPHP.LANG.LANG_2+'</div>\
				<div data-value="zh-tw">'+ThinkPHP.LANG.LANG_3+'</div>\
				'+(english == 1?'<div data-value="en-us">'+ThinkPHP.LANG.LANG_4+'</div>':'')+'\
				'+(kolish == 1?'<div data-value="ko">'+ThinkPHP.LANG.LANG_5+'</div>':'')+'\
			</div>\
		</span>';
	return 	selectHtml;
}
$('.select-lang div').live('click',function(){
	$('.save_news_img').fadeOut(1);
	$(this).parents('.language_box').find('.select-lang').hide();
	$(this).parents('.language_box').find('.language_name').text($(this).text());
	var clicked = parseInt($(".news_content_text").attr('data-clicked'));
	var checked = $(this).parents('.language_box').find('#doubleLang').attr('checked');
	$(".news_content_text").attr('data-clicked',1);
	
	var split_str = '<title>';
	var contentlanguage = $('.select-lang div[data-value=original]').attr('date-orglang');
	var dataLanguage = $(this).attr('data-value');
	$(this).parents('.language_box').attr("data-value",dataLanguage);
	if(clicked != 1) {
		window.newsContent = $('.only-title').html()+split_str+$('.news_content_text').html().replace(/<[\/]*?span[^>]*?>/g,'');
	}
	if(dataLanguage == 'original') {
		var contentArr = window.newsContent.split(split_str,2);
		//标题更改
		$(".only-title").html($.trim(contentArr[0]));
		//内容更改
		$(".news_content_text").html(('　　'+$.trim(contentArr[1])).replace(/　　/g,'<span class="content_space">　　</span>'));
		window.nowLanguage = contentlanguage;
		return;
	}
	if(window.nowLanguage == dataLanguage){
		$('.save_news_img').fadeIn(200);
		return;
	}
	if(window.nowLanguage != ''){
		var langArrs = [contentlanguage,dataLanguage];
	}
	if(window.noLang == true){
		$(".news_content_text").html(('　　'+ThinkPHP.LANG.TRANSLATE_QUOTA_NOT_ENOUGH).replace(/　　/g,'<span class="content_space">　　</span>'));
		return;
	}else{
		if(window.langs.indexOf(dataLanguage) < 0){
			window.langs.push(dataLanguage);
		}else{
			if(contentlanguage != dataLanguage && checked != undefined && !(langArrs.indexOf('zh-cn') > -1 && langArrs.indexOf('zh-tw') > -1 )){
				$(".news_content_text").html(window.langContent['double'][dataLanguage].replace('undefined',''));
			}else{
				$(".news_content_text").html(window.langContent['single'][dataLanguage].replace('undefined','') );
			}
			$('.save_news_img').fadeIn(200);
			window.nowLanguage = dataLanguage;
			return;
		}
	}
	var oldContent = window.newsContent.split(split_str,2)[1].split('<br>');
	$(".news_content_text").html('<div class="loading"><img  src="'+window.Think['PUBLIC']+'/images/ajax-loader.gif"></div>');
	wx.sendData(U('corp/index/muLanguageTranslation'),"sContent="+encodeURIComponent(window.newsContent)+'&splitStr='+split_str+'&contentLanguage='+contentlanguage+'&toLanguage='+dataLanguage+'&random='+Math.random(),function(data){
		window.nowLanguage = data.toLanguage;
		var contentArr = data.sContent.split(split_str,2);
		var newContents = contentArr[1].split('<br>');
		var langArr =[data.contentLanguage,data.toLanguage];
		var html = '';
		if(newContents == ThinkPHP.LANG.TRANSLATE_QUOTA_NOT_ENOUGH){
			window.noLang = true;
			$(".news_content_text").html(('　　'+$.trim(newContents)).replace(/　　/g,'<span class="content_space">　　</span>'));
			return;
		}
		for (var i in oldContent){
			if(i != 'remove'){
				if(newContents[i].indexOf('<img') >-1){
					html += oldContent[i]+'<br>'+'';
				}else{
					html += oldContent[i]+'<br>'+newContents[i]+'<br>';
				}
			}
		}
		
		//标题更改
		$(".only-title").html($.trim(contentArr[0]));
		//内容更改
		html = $.trim(html);
		contentArr[1] = $.trim(contentArr[1]);
		if(data.contentLanguage != data.toLanguage && checked != undefined && !(langArr.indexOf('zh-cn') > -1 && langArr.indexOf('zh-tw') > -1 )){
			window.langContent["single"][data.toLanguage] += ((/^\<img/.test(contentArr[1])?'':'　　')+contentArr[1]).replace(/　　/g,'<span class="content_space">　　</span>');
			$(".news_content_text").html(((/^\<img/.test(html)?'':'　　')+html).replace(/　　/g,'<span class="content_space">　　</span>'));
			window.langContent["double"][data.toLanguage] += $(".news_content_text").html();
			if(html != ThinkPHP.LANG.TRANSLATE_QUOTA_NOT_ENOUGH){
				$('.save_news_img').fadeIn(200);
			}
		}else{
			window.langContent["double"][data.toLanguage] += ((/^\<img/.test(html)?'':'　　')+html).replace(/　　/g,'<span class="content_space">　　</span>');
			$(".news_content_text").html(((/^\<img/.test(contentArr[1])?'':'　　')+contentArr[1]).replace(/　　/g,'<span class="content_space">　　</span>'));
			window.langContent["single"][data.toLanguage] += $(".news_content_text").html();
			if(contentArr[1] != ThinkPHP.LANG.TRANSLATE_QUOTA_NOT_ENOUGH){
				$('.save_news_img').fadeIn(200);
			}
		}
	});
});


/**
 * 是否包含图片判断
 * @param unknown $content
 * @return boolean
 */
function isInCludeImg(content) {
    if(content.indexOf('{060}') > -1) {
        return 1;
    }else{
        return 0;
    }
}
// num为传入的值，n为保留的小数位
function fomatFloat(num,n){
	var f = parseFloat(num);
	if(isNaN(f)){
		return false;
	}
	f = Math.round(num*Math.pow(10, n))/Math.pow(10, n); // n 幂
	var s = f.toString();
	var rs = s.indexOf('.');
	//判定如果是整数，增加小数点再补0
	if(rs < 0){
		rs = s.length;
		s += '.';
	}
	while(s.length <= rs + n){
		s += '0';
	}
	return s;
}

//将个数位日期转为07
function seven(s) {
	return s < 10 ? '0' + s: s;
}

//执行下载
function download(req_url,param,callback){
	var form=$("<form>");//定义一个form表单
	form.attr("style","display:none");
	form.attr("target","");
	form.attr("method","post");
	
	$("body").append(form);//将表单放置在web中
	req_url = encodeURI(req_url)+'?';
	if(param != undefined) {
		var index = 0;
		var input = $("<input>");
		param.forEach(function(v) {
			if(index%2 == 0) {
				input = $("<input>");
				input.attr("type","hidden");
				input.attr("name",v);
				req_url += '&'+v+'=';
			}else{
				input.attr("value",v);
				form.append(input);
				req_url += encodeURIComponent(v);
			}
			index++;
		});
	}
	form.attr("action",req_url);
	form.submit();//表单提交 
	
	if(callback) {
		callback();
	}
}

function uuid() {
    return 'xxxxxxxxxxxx4xxxyxxxxxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0,
            v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}
//checkbox判断半选
function checkboxSelect(){
	$('.checkboxselect').each(function(){
		var id = $(this).attr('id');
		var length = $('input[data-input="'+id+'"]').length;
		var checkedLength = 0;
		$('input[data-input="'+id+'"]').each(function(){
			if($(this)[0].checked){
				checkedLength++;
			}
		})
		if(length == checkedLength){
			$('#'+id)[0].indeterminate = false;
		}else if(checkedLength == 0){
			$('#'+id)[0].indeterminate = false;
		}else{
			$('#'+id)[0].indeterminate = true;
		}
	});
}

function noAuthAlert(){
	var tip_title = ThinkPHP.LANG.CONFIRM_TIP;
	var tip_yes = ThinkPHP.LANG.GROUP_ADD_YES;
	var num = 1;
	var text = ThinkPHP.LANG.NOT_AUTHORITY;
	{
		wx.alert(text,function(){},{tip_title:tip_title,tip_yes:tip_yes,tip_width:"352px",attachBg: 1, height: 650}
		);
	};
	return;
}