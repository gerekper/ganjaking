//TinyMCE Button
(function($) {

    tinymce.create('tinymce.plugins.YITH_WC_Surveys_Shortcode', {
        init : function(ed, url) {
            ed.addButton('surveys_shortcode', {
                title : 'Add YITH Surveys',
                onclick : function() {
                    $('#surveys_shortcode').click();
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "YITH WooCommerce Surveys",
                author : 'YITHEMES',
                authorurl : 'http://yithemes.com/',
                infourl : 'http://yithemes.com/',
                version : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('surveys_shortcode', tinymce.plugins.YITH_WC_Surveys_Shortcode);

})(jQuery);