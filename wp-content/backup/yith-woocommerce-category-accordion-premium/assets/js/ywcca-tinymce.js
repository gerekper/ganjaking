jQuery(function ($) {

    //TinyMCE Button
    var image_url = '';
    tinymce.create('tinymce.plugins.YITH_WooCommerce_Category_Accordion', {
        init : function(ed, url) {
            ed.addButton('ywcca_shortcode', {
                title : 'Add Shortcode',
                image : url+'/../images/icon-accordion.png',
                onclick : function() {
                    $('#ywcca_shortcode').click();
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo      : function () {
            return {
                longname : 'YITH WooCommerce Category Accordion',
                author   : 'YITHEMES',
                authorurl: 'http://yithemes.com/',
                infourl  : 'http://yithemes.com/',
                version  : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('ywcca_shortcode', tinymce.plugins.YITH_WooCommerce_Category_Accordion);

});
