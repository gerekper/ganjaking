var Widget_DCE_Dynamicposts_accordion_Handler = function ($scope, $) {

	let elementSettings = dceGetElementSettings($scope);
	let id_scope = $scope.data('id');
	let wrapper = $scope.find('ul.dce-posts-wrapper');
	let accordionStart = elementSettings.accordion_start;
	let icon = wrapper.data('dce-icon');
	let iconActive = wrapper.data('dce-icon-active');
	let activeIndex;

	if ( accordionStart === 'none' ) {
		activeIndex = false;
	} else if( accordionStart === 'first' ) {
		activeIndex = 1;
	} else if( accordionStart === 'custom' ) {
		activeIndex = elementSettings.accordion_start_custom || 1;
	} else {
		let elements = $scope.find( 'ul.dce-posts-wrapper .dce-post' ).length;
		activeIndex = [];
		for (let i = 0; i <= elements; i++) {
			activeIndex[i] = i;
		}
	}

	// AccordionJS
	let accordionJs = function ( wrapper, closeOtherSections, speed, activeIndex ) {
		wrapper.accordionjs({
			// Allow self close.(data-close-able)
			closeAble: true,
	
			// Close other sections.(data-close-other)
			closeOther: Boolean(closeOtherSections),
	
			// Animation Speed.(data-slide-speed)
			slideSpeed: speed,
	
			// The section open on first init. A number from 1 to X or false.(data-active-index)
			activeIndex: activeIndex,
		});
	}
	accordionJs( wrapper, elementSettings.accordion_close_other_sections, elementSettings.accordion_speed.size, activeIndex );

    // Scroll Reveal
    var on_scrollReveal = function(){
		var runRevAnim = function(dir){
        	var el = $( this );
            var i = $( this ).index();

            if(dir == 'down'){
               	setTimeout(function(){
               		el.addClass('animate');
               	}, 100 * i);
                // play
            }else if(dir == 'up'){
                el.removeClass('animate');
                // stop
            }
        };
        var waypointRevOptions = {
            offset: '100%',
            triggerOnce: false
        };
        elementorFrontend.waypoint($scope.find('.dce-post-item'), runRevAnim, waypointRevOptions);

    };
    on_scrollReveal();
};

jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.accordion', Widget_DCE_Dynamicposts_accordion_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.accordion', Widget_DCE_Dynamicposts_accordion_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.accordion', Widget_DCE_Dynamicposts_accordion_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.accordion', Widget_DCE_Dynamicposts_accordion_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.accordion', Widget_DCE_Dynamicposts_accordion_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.accordion', Widget_DCE_Dynamicposts_accordion_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.accordion', Widget_DCE_Dynamicposts_accordion_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.accordion', Widget_DCE_Dynamicposts_accordion_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.accordion', Widget_DCE_Dynamicposts_accordion_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.accordion', Widget_DCE_Dynamicposts_accordion_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-metabox-relationship.accordion', Widget_DCE_Dynamicposts_accordion_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-acf-relationship.accordion', Widget_DCE_Dynamicposts_accordion_Handler);
});
