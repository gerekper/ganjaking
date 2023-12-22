var Widget_DCE_Dynamicposts_grid_filters_Handler = function ($scope, $) {
	if (elementorFrontend.isEditMode()) {
		return;
	}
	let elementSettings = dceGetElementSettings($scope);
	let container = $scope.find('.dce-posts-container.dce-skin-grid .dce-posts-wrapper');
	let layoutMode = elementSettings[ dceDynamicPostsSkinPrefix+'grid_type' ];
	let rtl = Boolean( elementSettings[ 'rtl' ] );

	const onFilterChange = (filter) => {
		container.isotope({
			filter: filter,
		});
		// Match Height when layout is complete
		if( elementSettings.grid_filters_match_height ) {
			container.on( 'layoutComplete', function(event, laidOutItems ) {
				jQuery.fn.matchHeight._update();
			});
		}
		return false;
	}
	let defaultFilter;
	let $select = $scope.find('.dce-filters select');
	if ($select.length) {
		// select skin:
		defaultFilter = $select.val();
		$select.on('change', () => {
			onFilterChange($select.val());
		})
	} else {
		defaultFilter = $scope.find('.dce-filters .filter-active a').attr('data-filter');
		let $filterItems = $scope.find('.dce-filters .filters-item');
		$filterItems.on('click', 'a', function (e) {
			e.preventDefault();
			$(this).parent().siblings().removeClass('filter-active');
			$(this).parent().addClass('filter-active');
			let filterValue = $(this).attr('data-filter');
			onFilterChange( filterValue );
		});
	}

	container.imagesLoaded(() => {
		container.isotope({
			itemSelector: '.dce-post-item',
			layoutMode: 'masonry' === layoutMode ? 'masonry' : 'fitRows',
			sortBy: 'original-order',
			filter: defaultFilter,
			percentPosition: true,
			originLeft: ! rtl,
			masonry: {
				horizontalOrder: true,
				columnWidth: '.dce-post-item'
			}
		});
	});


};

jQuery(window).on('elementor/frontend/init', function () {
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.grid-filters', Widget_DCE_Dynamicposts_grid_filters_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.grid-filters', Widget_DCE_Dynamicposts_grid_filters_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.grid-filters', Widget_DCE_Dynamicposts_grid_filters_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products-on-sale.grid-filters', Widget_DCE_Dynamicposts_grid_filters_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.grid-filters', Widget_DCE_Dynamicposts_grid_filters_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.grid-filters', Widget_DCE_Dynamicposts_grid_filters_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.grid-filters', Widget_DCE_Dynamicposts_grid_filters_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.grid-filters', Widget_DCE_Dynamicposts_grid_filters_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.grid-filters', Widget_DCE_Dynamicposts_grid_filters_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.grid-filters', Widget_DCE_Dynamicposts_grid_filters_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-metabox-relationship.grid-filters', Widget_DCE_Dynamicposts_grid_filters_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-acf-relationship.grid-filters', Widget_DCE_Dynamicposts_grid_filters_Handler);
});
