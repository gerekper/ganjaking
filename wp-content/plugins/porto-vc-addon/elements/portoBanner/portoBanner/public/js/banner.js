function vcParallax($el) {
    var $el = $el;
    if (!document.getElementById('skrollr-js')) {
        let d = document;
        let wf = d.createElement('script'), s = d.scripts[d.scripts.length - 1];
        wf.src = js_porto_vars.theme_url + '/js/libs/skrollr.min.js';
        wf.id = 'skrollr-js'
        s.parentNode.insertBefore(wf, s);
    }
    if (typeof skrollr == 'undefined') {
        document.getElementById('skrollr-js').addEventListener('load', function () {
            $el.themeParallax(JSON.parse($el.attr('data-plugin-options')));
        })
    }
    else {
        window.requestAnimationFrame(function () {
            $el.themeParallax(JSON.parse($el.attr('data-plugin-options')));
        });
    }
}
function vcVideoBackground($el) {
    var $el = $el;
    if (!document.getElementById("jquery-vide-js")) {
        let d = document;
        let wf = d.createElement('script'), s = d.scripts[d.scripts.length - 1];
        wf.src = js_porto_vars.theme_url + '/js/libs/jquery.vide.min.js';
        wf.id = 'jquery-vide-js'
        s.parentNode.insertBefore(wf, s);
    }
    if (!jQuery.fn.vide) {
        document.getElementById("jquery-vide-js").addEventListener('load', function () {
            $el.themePluginVideoBackground(JSON.parse($el.attr('data-plugin-options')));
        })
    }
    else {
        window.requestAnimationFrame(function () {
            $el.themePluginVideoBackground(JSON.parse($el.attr('data-plugin-options')));
        });
    }

}

(function ($) {
    window.vcv.on('ready', function (action, id, attr) {
        if (action !== 'merge') {
            if (id === undefined && !window.parent.document.getElementById('vcv-layout')) {
                $('.vce-element-porto-banner [data-plugin-video-background]').each(function () {
                    if ($(this).find("video").length > 0) {
                        $(this).find(".video-overlay").remove();
                        $(this).find("video").parent().remove();
                    }
                    vcVideoBackground($(this));
                })
                $('.vce-element-porto-banner [data-plugin-parallax]').each(function () {
                    if ($(this).find(".parallax-background").length > 0) {
                        $(this).find(".parallax-background").remove();
                    }
                    vcParallax($(this));
                })
                return;
            }
            var $el = id ? $('#el-' + id + '>.porto-ibanner') : undefined;
            if ($el && $el.length > 0) {
                if (attr.changedAttribute == 'parallax' || attr.changedAttribute == 'bannerVideo' || attr.changedAttribute == 'bannerImage') {
                    if ($el.find(".parallax-background").length > 0) {
                        $el.find(".parallax-background").remove();
                        $el.removeData('__parallax');
                    }
                    if ($el.find("video").length > 0) {
                        $el.find(".video-overlay").remove();
                        $el.find("video").parent().remove();
                        $el.removeData('__videobackground');
                        $el.removeData('video-path');
                    }
                }
                // parallax
                if ($el.hasClass('has-parallax-bg')) {
                    vcParallax($el);
                }
                // bannerVideo
                if ($el.hasClass("section-video")) {
                    vcVideoBackground($el);
                }
            }
        }
    });
})(window.jQuery)