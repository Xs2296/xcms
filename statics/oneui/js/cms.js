var Xcms = function () {

    // 页面提示
    var tips = function ($msg, $url) {
        $url  = $url || '';
        if($url != ''){
            var $info = 'success';
            var $icon = 'fa fa-check';
        }else{
            var $info = 'danger';
            var $icon = 'fa fa-times';
        }
        var $notify         = jQuery(this);
        var $notifyType     = $notify.data('notify-type') ? $notify.data('notify-type') : $info;
        var $notifyFrom     = $notify.data('notify-from') ? $notify.data('notify-from') : 'top';
        var $notifyAlign    = $notify.data('notify-align') ? $notify.data('notify-align') : 'center';
        var $notifyIcon     = $notify.data('notify-icon') ? $notify.data('notify-icon') : $icon;
        var $notifyUrl      = $notify.data('notify-url') ? $notify.data('notify-url') : $url;
        jQuery.notify({
                icon: $notifyIcon,
                message: $msg,
                url: $notifyUrl
            },
            {
                element: 'body',
                type: $notifyType,
                allow_dismiss: true,
                newest_on_top: true,
                showProgressbar: false,
                placement: {
                    from: $notifyFrom,
                    align: $notifyAlign
                },
                offset: 20,
                spacing: 10,
                z_index: 99999,
                delay: 5000,
                timer: 1000,
                animate: {
                    enter: 'animated fadeIn',
                    exit: 'animated fadeOutDown'
                }
            }
        );
    };


    var pageLoader = function ($mode) {
        var $loadingEl = jQuery('#loading');
        $mode          = $mode || 'show';
        if ($mode === 'show') {
            if ($loadingEl.length) {
                $loadingEl.fadeIn(250);
            } else {
                jQuery('body').prepend('<div id="loading" style="background-color: rgba(0,0,0,0.62);z-index: 99998;position: fixed;padding: 10px;border-radius: 4px;margin-left: -50px;margin-top: -24px;color: #FFF;left: 50%;top: 50%;"><div class="loading-box"><i class="fa fa-2x fa-cog fa-spin" style="margin-right: 5px;"></i> <span class="loding-text" style="margin-top: 3px;display: inline-block;">加载中...</span></div></div>');
            }
        } else if ($mode === 'hide') {
            if ($loadingEl.length) {
                $loadingEl.fadeOut(250);
            }
        }
        return false;
    };


    var xpost = function($url,$datas){
        pageLoader();
        $.post($url, $datas, function (res) {
            pageLoader('hide');
            if (res.code) {
                tips(res.msg, res.url);
                setTimeout(function () {
                    location.href = res.url;
                }, 1500);
            } else {
                tips(res.msg);
            }
            return false;
        }).fail(function () {
            pageLoader('hide');
            tips('服务器错误~');
        });
        return false;
    }


    return {
        // 初始化
        init: function () {
        },
        // 页面加载提示
        loading: function ($mode) {
            pageLoader($mode);
        },
        // 页面提示
        notify: function ($msg, $url) {
            tips($msg, $url);
        },
        // Ajax提交
        xpost: function($url,$datas){
            xpost($url,$datas);
        }
    };
}();

jQuery(function () {
    Xcms.init();
});