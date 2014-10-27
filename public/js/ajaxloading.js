var ajaxLoading = (function($){
    var me = this;

    me.init = function(){
        $(document).bind("show-ajax-loading", me.show);
        $(document).bind("hide-ajax-loading", me.hide);
    }

    me.show = function(){
        $(".waiting-bg").fadeIn();
        $(".waiting-icon-container").fadeIn();
    }
    me.hide = function(){
        $(".waiting-bg").fadeOut();
        $(".waiting-icon-container").fadeOut();
    }
    return me;
})(jQuery);

$(document).ready(function(){
    ajaxLoading.init();
    // $("form").bind("submit.ajaxLoading", function(e){
    //     ajaxLoading.show();
    // });
});