function vcMasonry($el) {
    var $el = $el;
    if (!document.getElementById('isotope-js')) {
        let d = document;
        let wf = d.createElement('script'), s = d.scripts[d.scripts.length - 1];
        wf.src = js_porto_vars.theme_url + '/js/libs/isotope.pkgd.min.js';
        wf.id = 'isotope-js'
        s.parentNode.insertBefore(wf, s);
    }
    if (!jQuery.fn.isotope) {
        document.getElementById('isotope-js').addEventListener('load', function () {
            $el.themeMasonry(JSON.parse($el.attr('data-plugin-options')));
        })
    }
    else {
        window.requestAnimationFrame(function () {
            $el.themeMasonry(JSON.parse($el.attr('data-plugin-options')));
        });
    }
}

(function ($) {
    window.vcv.on('ready', function (action, id, attr) {
        if (action !== 'merge') {
            if (id === undefined && !window.parent.document.getElementById('vcv-layout')) {
                $('.vce-porto-masonry-wrapper [data-plugin-masonry]').each(function () {
                    // Masonry
                    vcMasonry($(this));
                })
                return;
            }
            var $el = id ? $('#el-' + id).closest('.vce-porto-masonry-wrapper').find('.porto-grid') : undefined;
            if ($el && $el.length > 0) {
                if ($el.attr('data-children') != $el.find('.porto-grid-item').length) return;
                $el.removeData('__masonry');
                let elements = []
                if ($('#el-' + id).parent().length > 0 && action == 'add' && $el.data('isotope')) {

                    if (!($('#el-' + id).parent().get(0) instanceof HTMLElement)) {
                        Object.setPrototypeOf($('#el-' + id).parent().get(0), HTMLElement.prototype);
                    }
                    elements.push($('#el-' + id).parent()[0])
                    $el.isotope('reloadItems')
                }
                if ($('#el-' + id).parent().length == 0 && action == 'update' && $el.data('isotope')) {
                    elements.push($('#el-' + id).parent())
                    $el.isotope('remove', elements)
                }
                // Masonry
                vcMasonry($el);
            }
        }
    });
})(window.jQuery)