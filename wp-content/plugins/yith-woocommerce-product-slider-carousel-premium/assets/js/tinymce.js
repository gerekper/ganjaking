//TinyMCE Button
(function($) {

    var image_url='';
    tinymce.create('tinymce.plugins.YITH_WooCommerce_Product_Slider', {
        init : function(ed, url) {
            if( image_url == '' || image_url == null ) url=url+'/../images/icon_shortcode.png';
            else  url = image_url;
            ed.addButton('ywcps_shortcode', {
                title : 'Add YITH Product Slider',
                image : url,
                onclick : function() {
                    $('#ywcps_shortcode').click();
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "YITH WooCommerce Product Slider Carousel",
                author : 'YITHEMES',
                authorurl : 'http://yithemes.com/',
                infourl : 'http://yithemes.com/',
                version : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('ywcps_shortcode', tinymce.plugins.YITH_WooCommerce_Product_Slider);

})(jQuery);