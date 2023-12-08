/*Advance typography*/(function ($) {
	"use strict";
	var WidgetAdvancedTypographyHandler = function($scope, $) {
        let Wrapper = $scope[0].querySelectorAll('.pt-plus-adv-typo-wrapper');

        if( Wrapper.length > 0 ){
            let typo_circular = Wrapper[0].querySelectorAll('.typo_circular'),
                typo_blend_mode = Wrapper[0].querySelectorAll('.typo_bg_based_text');

            if( typo_circular.length > 0 ){
                typo_circular.forEach(function(self){
                    
                    if( self.closest('.plus-content-editor')){
                        jQuery( ".offcanvas-toggle-btn" ).on("click",function() {
                            setTimeout(function(){
                                self.closest('.plus-content-editor').querySelectorAll('.typo_circular').forEach(function(self){                                   
                                    let ids = self.id,
                                        custom_radius = self.dataset.customRadius,
                                        custom_reversed = self.dataset.customReversed,
                                        custom_resize = self.dataset.customResize;
                
                                        if( custom_reversed == 'yes' ){
                                            var circular_option = new CircleType(document.getElementById(ids)).dir(-1).radius(custom_radius);                                        
                                        }else {
                                            var circular_option = new CircleType(document.getElementById(ids)).radius(custom_radius);
                                        }
                                        if( custom_resize == 'yes' ){
                                            $(window).on("resize",function() {                                
                                                circular_option.radius(circular_option.element.offsetWidth / 2);                                            
                                            });
                                        }                                   
                                });                            
                            }, 500);
                        });                        
                    }else{
                        let ids = self.id,
                            custom_radius = self.dataset.customRadius,
                            custom_reversed = self.dataset.customReversed,
                            custom_resize = self.dataset.customResize;

                            if( custom_reversed == 'yes' ){
                                var circular_option = new CircleType(document.getElementById(ids)).dir(-1).radius(custom_radius);
                            }else {
                                var circular_option = new CircleType(document.getElementById(ids)).radius(custom_radius);
                            }
                            if( custom_resize == 'yes' ){
                                $(window).on("resize",function() {                                
                                    circular_option.radius(circular_option.element.offsetWidth / 2);
                                });
                            }
                    }                    
                
                });
            }

            if( typo_blend_mode.length > 0 ){
                let mode = typo_blend_mode[0].dataset.blendMode,
                    fixed_mode = $scope[0].parentElement.querySelectorAll('.elementor-fixed'),
                    absolute_mode = $scope[0].parentElement.querySelectorAll('.elementor-absolute');

                if( fixed_mode.length > 0 ){
                    fixed_mode[0].style.cssText = "mix-blend-mode:"+mode+";";
                }
                if( absolute_mode.length > 0 ){
                    absolute_mode[0].style.cssText = "mix-blend-mode:"+mode+";";
                }
            }
        }
	};

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-advanced-typography.default', WidgetAdvancedTypographyHandler);
	});
})(jQuery);