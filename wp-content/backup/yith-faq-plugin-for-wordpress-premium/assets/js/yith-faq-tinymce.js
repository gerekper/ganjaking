jQuery(function ($) {

    //TinyMCE Button
    tinymce.create('tinymce.plugins.YITH_FAQ_Plugin_for_Wordpress', {
        init         : function (ed, url) {
            ed.addButton('yfwp_shortcode', {
                title  : yfwp_shortcode.title,
                onclick: function () {
                    $('#yfwp_shortcode').click();
                }
            });
        },
        createControl: function (n, cm) {
            return null;
        },
        getInfo      : function () {
            return {
                longname : 'YITH FAQ Plugin for WordPress',
                author   : 'YITHEMES',
                authorurl: 'https://yithemes.com/',
                infourl  : 'https://yithemes.com/',
                version  : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('yfwp_shortcode', tinymce.plugins.YITH_FAQ_Plugin_for_Wordpress);

});