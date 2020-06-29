/* Google Map */
//resize map
jQuery(document).ready(function(){
    'use strict';
    function resize_porto_map() {
        jQuery('.porto-map-wrapper').each(function(i,wrapelement){

            var wrap = jQuery(wrapelement).attr('id');

            if(typeof wrap === 'undefined' || wrap === '')
                return false;

            var map = jQuery(wrapelement).find('.porto_google_map').attr('id');
            var map_override = jQuery('#'+map).attr('data-map_override');

            var is_relative = 'true';

            jQuery('#'+map).css({'margin-left' : 0 });
            jQuery('#'+map).css({'right' : 0 });

            var ancenstor = jQuery('#'+wrap).parent();
            var parent = ancenstor;
            if(map_override=='full'){
                ancenstor= jQuery('body');
                is_relative = 'false';
            }
            if(map_override=='ex-full'){
                ancenstor= jQuery('html');
                is_relative = 'false';
            }
            if( ! isNaN(map_override)){
                for(var i=0;i<map_override;i++){
                    if(ancenstor.prop('tagName')!='HTML'){
                        ancenstor = ancenstor.parent();
                    }else{
                        break;
                    }
                }
            }

            if(map_override == 0 || map_override == '0')
                var w = ancenstor.width();
            else
                var w = ancenstor.outerWidth();

            var a_left = ancenstor.offset().left;
            var left = jQuery('#'+map).offset().left;
            var calculate_left = a_left - left;

            jQuery('#'+map).css({'width':w});
            if(map_override != 0 || map_override != '0') {
                
                jQuery('#'+map).css({'margin-left' : calculate_left });
            }

            if(map_override == 'full') {
                if ( jQuery( 'body' ).hasClass('rtl') ) {
                    var mapDiv = jQuery('#'+map);

                    var rt = (jQuery(window).width() - (mapDiv.offset().left + mapDiv.outerWidth()));
                    jQuery('#'+map).css({'right' : -rt });
                }
            }

        });
    }
    resize_porto_map();
    jQuery(window).load(function(){
        resize_porto_map();
    });
    jQuery(window).resize(function(){
        resize_porto_map();
    });
    jQuery('.ui-tabs').bind('tabsactivate', function(event, ui) {
       if(jQuery(this).find('.porto-map-wrapper').length > 0)
        {
            resize_porto_map();
        }
    });
    jQuery('.ui-accordion').bind('accordionactivate', function(event, ui) {
       if(jQuery(this).find('.porto-map-wrapper').length > 0)
        {
            resize_porto_map();
        }
    });
    jQuery(document).on('onPortoModalPopupOpen', function(){
        resize_porto_map();
    });
    jQuery(document).on('portoMapResize',function(){
        resize_porto_map();
    });
});