
if (typeof portoInitStatCounter == 'undefined') {
    function portoInitStatCounter($elements) {
        'use strict';

        if (typeof $elements == "undefined") {
            $elements = jQuery("body");
        }
        var $stats = $elements.find( '.stats-block' );
        $stats.each(function() {
            function initCounter(obj) {
                var endNum = parseFloat(jQuery(obj).find('.stats-number').attr('data-counter-value'));
                var Num = (jQuery(obj).find('.stats-number').attr('data-counter-value'))+' ';
                var speed = parseInt(jQuery(obj).find('.stats-number').attr('data-speed'));
                var ID = jQuery(obj).find('.stats-number').attr('data-id');
                var sep = jQuery(obj).find('.stats-number').attr('data-separator');
                var dec = jQuery(obj).find('.stats-number').attr('data-decimal');
                var dec_count = Num.split(".");
                if(dec_count[1]){
                    dec_count = dec_count[1].length-1;
                } else {
                    dec_count = 0;
                }
                var grouping = true;
                if(dec == "none"){
                    dec = "";
                }
                if(sep == "none"){
                    grouping = false;
                } else {
                    grouping = true;
                }
                var settings = {
                    useEasing : true,
                    useGrouping : grouping,
                    separator : sep,
                    decimal : dec
                }
                var counter = new countUp(ID, 0, endNum, dec_count, speed, settings),
                    endTrigger = function() {
                        if (jQuery('#' + ID).next('.counter_suffix').length > 0) {
                            jQuery('#' + ID).next('.counter_suffix').css('display', 'inline');
                        }
                    };
                setTimeout(function(){
                    counter.start(endTrigger);
                },500);
            }
            if (jQuery.fn.appear) {
                jQuery(this).appear(function() {
                    initCounter(this);
                }, {
                    accX: 0,
                    accY: -150
                });
            } else {
                initCounter(this);
            }
        });
    }
}

jQuery(document).ready(function($) {
    'use strict';

    portoInitStatCounter();
    $(document.body).on('porto_refresh_vc_content', function(event, $elements) {
        portoInitStatCounter($elements);
    });
});