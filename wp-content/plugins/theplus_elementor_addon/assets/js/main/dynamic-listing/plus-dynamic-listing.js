/*Dynamic Listing*/
( function( $ ) {
	"use strict";
	var WidgetDynamicListingHandler = function ($scope, $) {
        var containerJs = $scope[0].querySelectorAll('.dynamic-listing');
		var container = $scope.find('.dynamic-listing');

			if(container.hasClass('.dynamic-listing.dynamic-listing-style-1')){
				$('.dynamic-listing.dynamic-listing-style-1 .grid-item .blog-list-content').on('mouseenter',function() {
					$(this).find(".post-hover-content").slideDown(300)				
				});
				$('.dynamic-listing.dynamic-listing-style-1 .grid-item .blog-list-content').on('mouseleave',function() {
					$(this).find(".post-hover-content").slideUp(300)				
				});
			}

			$(document).ready(function () {
				if($('.tp-child-filter-enable').length){
					$( ".tp-child-filter-enable.pt-plus-filter-post-category .category-filters li a" ).on( "click", function(event) {
						event.preventDefault();
						var get_filter = $(this).data("filter"),
						get_filter_remove_dot = get_filter.split('.').join(""),  
						get_sub_class = 'cate-parent-',
						get_filter_add_class = get_sub_class.concat(get_filter_remove_dot);

						if(get_filter_remove_dot=="*" && get_filter_remove_dot !=undefined){
							$(this).closest(".post-filter-data").find(".category-filters-child").removeClass( "active");
						}else{
							$(this).closest(".post-filter-data").find(".category-filters-child").removeClass( "active");
							$(this).closest(".post-filter-data").find(".category-filters-child."+get_filter_add_class).addClass( "active");
						}
					});
				}
			});
			
			/**Relayout*/
			if( containerJs[0] && elementorFrontend.isEditMode() ){
				var layoutType = (containerJs[0].dataset && containerJs[0].dataset.layoutType) ? containerJs[0].dataset.layoutType : '';
				
					Resizelayout(layoutType, 4000)
					
					function Resizelayout( loadlayout, duration=500 ) {	
						if (loadlayout == 'layoutType' || loadlayout == 'masonry') {
							let FindGrid = containerJs[0].querySelectorAll(`.list-isotope .post-inner-loop`);
							if( FindGrid.length ){
								setTimeout(function(){
									jQuery(FindGrid[0]).isotope('reloadItems').isotope();
								}, duration);
							}
						}
					}
			}
	};

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-dynamic-listing.default', WidgetDynamicListingHandler);
	});
})(jQuery);