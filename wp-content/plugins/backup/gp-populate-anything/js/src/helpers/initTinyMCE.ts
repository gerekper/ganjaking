declare global {
    interface Window {
        tinymce: typeof import('tinymce') & {
            Env: any
            $: JQueryStatic
            editors: any
        }
        tinyMCEPreInit: any
        wpActiveEditor: any
        quicktags: any
    }
}

export default function initTinyMCE() {

    for (var id in window.tinymce.editors) {
        window.tinymce.EditorManager.remove(window.tinymce.editors[id]);
    }

    (function () {
        var init, id, $wrap;

        if (typeof window.tinymce !== 'undefined') {
            if (window.tinymce.Env.ie && window.tinymce.Env.ie < 11) {
                window.tinymce.$('.wp-editor-wrap ').removeClass('tmce-active').addClass('html-active');
                return;
            }

            for (id in window.tinyMCEPreInit.mceInit) {
                init = window.tinyMCEPreInit.mceInit[id];
                $wrap = window.tinymce.$('#wp-' + id + '-wrap');

                if (($wrap.hasClass('tmce-active') || !window.tinyMCEPreInit.qtInit.hasOwnProperty(id)) && !init.wp_skip_init) {
                    window.tinymce.init(init);

                    if (!window.wpActiveEditor) {
                        window.wpActiveEditor = id;
                    }
                }
            }
        }

        if (typeof window.quicktags !== 'undefined') {
            for (id in window.tinyMCEPreInit.qtInit) {
                window.quicktags(window.tinyMCEPreInit.qtInit[id]);

                if (!window.wpActiveEditor) {
                    window.wpActiveEditor = id;
                }
            }
        }
    }());

}
