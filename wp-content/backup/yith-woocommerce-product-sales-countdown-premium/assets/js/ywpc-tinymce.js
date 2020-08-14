jQuery(function ($) {

    //TinyMCE Button
    tinymce.create('tinymce.plugins.YITH_WooCommerce_Product_Countdown', {
        init         : function (ed, url) {
            ed.addButton('ywpc_shortcode', {
                title  : ywpc_shortcode.lightbox_title,
                onclick: function () {
                    $('#ywpc_shortcode').click();
                }
            });
        },
        createControl: function (n, cm) {
            return null;
        },
        getInfo      : function () {
            return {
                longname : 'YITH WooCommerce Product Countdown',
                author   : 'YITHEMES',
                authorurl: 'https://yithemes.com/',
                infourl  : 'https://yithemes.com/',
                version  : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('ywpc_shortcode', tinymce.plugins.YITH_WooCommerce_Product_Countdown);

});
