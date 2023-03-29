/**
 * Created by Administrator on 2018/11/22 0022.
 */
//文本框输入
$('.all-source').live('click',function(){
   $(this).next().toggle(200);
});

//确定选择的属性
$('.source-sure').live('click',function(){
    var data_attr = '';
    var data_attr_show = '';
    var _length = $(this).prev().find('.data—list input:checked').length;
    $(this).prev().find('.data—list input').each(function(v){
        if($(this).attr('checked')){
            //记录选中的值
            data_attr += $(this).val() + ',';
            data_attr_show += $(this).next().attr('data-show') + ',';
        }
    });
    //去掉最后一个逗号(如果不需要去掉，就不用写)
    if (data_attr.length > 0) {
        data_attr = data_attr.substr(0,data_attr.length - 1);
    }
    if (data_attr_show.length > 0) {
    	data_attr_show = data_attr_show.substr(0,data_attr.length - 1);
    }
    if($(this).prev().find('.source_p_all input').attr('checked')){
        $(this).parent().prev().attr('data-name','all');
        $(this).parent().prev().find('.all-source-text').html('-'+ThinkPHP.LANG.ALL+'-');
    }else{
        $(this).parent().prev().attr('data-name',data_attr);
        $(this).parent().prev().find('.all-source-text').html(data_attr_show);
    }
    window.chooise = 'attr';
    $(this).parent().fadeOut(200);
    getNewsList(1);
    change_source_checked_count($(this));
    if(_length == 0) {
        $(this).prev().find('p input').attr('checked','checked');
        $(this).parent().prev().attr('data-name','all');
        $(this).parent().prev().find('.all-source-text').html('-'+ThinkPHP.LANG.ALL+'-');
    }
});
/*全选操作*/
function change_source_checked_count(obj_this) {
    var length = $(obj_this).parent().parent().find('.data—list input').length;
    var checkedCount = 0;
    $(obj_this).parent().parent().find('.data—list input').each(function() {
        if($(this).attr('checked')) {
            checkedCount++;
        }else{
            $(obj_this).parent().parent().find('.source_p_all input').removeAttr('checked');
        }
    });
    if( checkedCount == length){
        $(obj_this).parent().parent().find('.source_p_all input').attr('checked','checked');
    }
}
/*全选操作*/
function checked_all_source(obj_this) {
    var checkedCount = 0;
    if($(obj_this).attr('checked')) {
        $(obj_this).parent().parent().find('.data—list input').each(function() {
            if($(this).attr('disabled') !== 'disabled' && !$(this).attr('checked')) {
                $(this).attr('checked','checked');
                checkedCount++;
            }
        });
    }else{
        $(obj_this).parent().parent().find('.data—list input').each(function() {
            if($(this).attr('disabled') !== 'disabled' && $(this).attr('checked')) {
                $(this).removeAttr('checked');
                checkedCount--;
            }
        });
    }
    change_source_checked_count();
}