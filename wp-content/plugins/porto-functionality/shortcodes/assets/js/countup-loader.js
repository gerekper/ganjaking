
if (typeof portoInitStatCounter == 'undefined') {
    function portoInitStatCounter($elements) {
        'use strict';

        if (typeof $elements == "undefined") {
            $elements = jQuery("body");
        }
        var $stats = $elements.find( '.stats-block' );

        var initCounter = function(obj) {
            if (typeof obj == 'undefined') {
                obj = this;
            }
            var $obj = jQuery(obj),
                endNum = parseFloat($obj.find('.stats-number').attr('data-counter-value'));
            var Num = ($obj.find('.stats-number').attr('data-counter-value'))+' ';
            var speed = parseInt($obj.find('.stats-number').attr('data-speed'));
            var ID = $obj.find('.stats-number').attr('data-id');
            var sep = $obj.find('.stats-number').attr('data-separator');
            var dec = $obj.find('.stats-number').attr('data-decimal');
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
                useEasing : false,
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
        };

        if (window.theme && theme.intObs) {
            theme.intObs(jQuery.makeArray($stats), initCounter, -50);
        } else {
            $stats.each(function() {
                initCounter(this);
            });
        }
    }
}

jQuery(document).ready(function($) {
    'use strict';

    portoInitStatCounter();
    $(document.body).on('porto_refresh_vc_content', function(event, $elements) {
        portoInitStatCounter($elements);
    });
});