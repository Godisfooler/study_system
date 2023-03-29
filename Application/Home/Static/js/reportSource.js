/**
 * Created by Administrator on 2018/11/22 0022.
 */
//文本框输入
$('.all-source').live('click',function(){
   $('.source-box').toggle(200);
});
//数据源匹配
$("#named").live('keyup',function () {
    $("#deleteSource").show();
    window.q = $.trim($(this).val());
    if ($("#named").val().length <= 0) {
        $(".source-list").show();//如果什么都没填，跳出，保持全部显示状态
        $(".source_p_all").show();
        $("#deleteSource").hide();
        sourceAddCheck();
        return;
    }
    if(window.setTimeOutBar != undefined) {
        clearTimeout(window.setTimeOutBar);
    }
    window.setTimeOutBar = setTimeout(function(){
        $(".source-list").css('display', 'none');//如果填了，先将所有的选项隐藏
        cc();
        if ($("#named").val().length <= 0) {
            $(".source_p_all").show();
        }
    },500);
});
function cc(){
    for (var i = 0; i < $("#sourceListCheck .source-list").length; i++) {
        //模糊匹配，将所有匹配项显示[匹配首字母]
        if (simplized($("#sourceListCheck .source-list label").eq(i).text().toLowerCase()).indexOf(simplized(window.q.toLowerCase())) != -1) {
            $("#sourceListCheck .source-list").eq(i).show();
        }
    }

}
//确定选择的数据源
$('.source-sure').live('click',function(){
	window.page = 1;
    var count = 0;
    var arr_list = [];
    window.checkedSiteList = [];
    $('#sourceListCheck .source-list input').each(function(v){
        if($(this).attr('checked')){
            count++;
            //记录选中的值
            $(this).next().attr('data-name','checked');
            var html = $(this).next().html();
            arr_list.push(html);
        }
    })
    $('.all-source-text').html(ThinkPHP.LANG.SOURCE_CHECKED);
    $('.source-box').fadeOut(200);
    window.checkedSiteList = arr_list;
    showNewsList(0);
    $("#named").val('');
    $(".source-list").show();
    sourceAddCheck();
    $("#deleteSource").hide();
});
/*全选操作*/
function change_source_checked_count(obj_this) {
    sourceAddCheck();
}
function sourceAddCheck() {
    var newsCanAdd = false;
    //全选是否勾选判断
    var checkedAll = true;
    var checkedNotAll = true;
    $('#sourceListCheck input[type=checkbox]').each(function (){
        if($(this).attr('checked') && $(this).attr('disabled') != 'disabled') {
            newsCanAdd = true;
        }
        if($(this).is(":visible") && !$(this).attr('checked')) {
            checkedAll = false;
        }else if($(this).is(":visible")){
            checkedNotAll = false;
        }
    });

    if(checkedAll && !checkedNotAll) {
        $('#source_list_all').attr('checked','checked');
    }
    if(!checkedAll){
        $('#source_list_all').removeAttr('checked');
    }
}
/*全选操作*/
function checked_all_source(obj_this) {
    var checkedCount = 0;
    if($(obj_this).attr('checked')) {
        $('#sourceListCheck').find('input[type=checkbox]:visible').each(function() {
            if($(this).attr('disabled') !== 'disabled' && !$(this).attr('checked')) {
                $(this).attr('checked','checked');
                checkedCount++;
            }
        });
    }else{
        $('#sourceListCheck').find('input[type=checkbox]:visible').each(function() {
            if($(this).attr('disabled') !== 'disabled' && $(this).attr('checked')) {
                $(this).removeAttr('checked');
                checkedCount--;
            }
        });
    }
    sourceAddCheck();
}