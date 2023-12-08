/**
 * Start hover box widget script
 */

(function($, elementor) {

    'use strict';

    var widgetHoverBox = function($scope, $) {


        var $hoverBox = $scope.find('.bdt-ep-hover-box'),
            $settings = $hoverBox.data('settings');

        var iconBx = document.querySelectorAll('#' + $settings.box_id + ' .bdt-ep-hover-box-item');
        var contentBx = document.querySelectorAll('#' + $settings.box_id + ' .bdt-ep-hover-box-content');

        for (var i = 0; i < iconBx.length; i++) {
            iconBx[i].addEventListener($settings.mouse_event, function() {
                for (var i = 0; i < contentBx.length; i++) {
                    contentBx[i].className = 'bdt-ep-hover-box-content'
                }
                document.getElementById(this.dataset.id).className = 'bdt-ep-hover-box-content active';

                for (var i = 0; i < iconBx.length; i++) {
                    iconBx[i].className = 'bdt-ep-hover-box-item';
                }
                this.className = 'bdt-ep-hover-box-item active';

            })
        }

    };

    var widgetHoverBoxFlexure = function($scope, $) {
        var $hoverBoxFlexure = $scope.find('.bdt-ep-hover-box'),
            $settings = $hoverBoxFlexure.data('settings');
            
       var iconBox = $($hoverBoxFlexure).find('.bdt-ep-hover-box-item');

       $(iconBox).on($settings.mouse_event, function(){
        var target = $(this).attr('data-id');
        $('#'+target).siblings().removeClass('active');
        $('[data-id="' + target + '"]').siblings().removeClass('active');
        if($settings.mouse_event == 'click'){
            $('#'+target).toggleClass('active');
            $('[data-id="' + target + '"]').toggleClass('active');
            $('[data-id="' + target + '"]').siblings().addClass('invisiable');
            $($hoverBoxFlexure).find('.bdt-ep-hover-box-item.invisiable').on('click', function(){
                $('[data-id="' + target + '"]').siblings().addClass('invisiable');
                $('[data-id="' + target + '"]').addClass('invisiable');
            });
            $($hoverBoxFlexure).find('.bdt-ep-hover-box-item.active').on('click', function(){
                $('[data-id="' + target + '"]').siblings().removeClass('invisiable');
                $('[data-id="' + target + '"]').removeClass('invisiable');
            });

        }else{
            $('#'+target).addClass('active');
            $('[data-id="' + target + '"]').addClass('active');
            $('[data-id="' + target + '"]').siblings().addClass('invisiable');
        }
       });
       if($settings.mouse_event == 'mouseover'){
        $(iconBox).on('mouseleave', function(){
            var target = $(this).attr('data-id');
            $('#'+target).siblings().removeClass('active');
            $('#'+target).removeClass('active');
            $('[data-id="' + target + '"]').siblings().removeClass('active');
            $('[data-id="' + target + '"]').removeClass('active');
            $('[data-id="' + target + '"]').siblings().removeClass('invisiable');
        });
        }

    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-box.default', widgetHoverBox);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-box.bdt-envelope', widgetHoverBox);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-box.bdt-flexure', widgetHoverBoxFlexure);
    });

}(jQuery, window.elementorFrontend));

/**
 * End hover box widget script
 */

