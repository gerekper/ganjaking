(function ($) {
	// For Elementor >= 3.16:
	const handleInfiniteScrollInsideTab = ( $tabpanel, $grid ) => {
		var observer = new MutationObserver(function(_, observer) {
			$grid.isotope('layout');
			// only needed the first time the tab is activated:
			observer.disconnect();
		});
		var observerConfig = {
			attributes: true,
			attributeFilter: ["class"]
		};
		// observe only needed if tab not active on loading:
		if ( ! $tabpanel.hasClass('e-active') ) {
			observer.observe($tabpanel[0], observerConfig);
		}
	}

	// For Elementor < 3.16:
	const handleInfiniteScrollInsideTabLegacy = ( $tablist, $grid ) => {
		var observer = new MutationObserver(function(_, observer) {
			$grid.isotope('layout');
			// only needed the first time the tab is activated:
			observer.disconnect();
		});
		var observerConfig = {
			attributes: true,
			attributeFilter: ["class"]
		};
		// find our tab:
		let $tab = $tablist.children().has($grid);
		// observe only needed if tab not active on loading:
		if ( ! $tab.hasClass('e-active') ) {
			observer.observe($tab[0], observerConfig);
		}
	}

	window.Widget_DCE_Dynamicposts_grid_Handler = function ($scope, $) {
		var elementSettings = dceGetElementSettings($scope);
		var id_scope = $scope.attr('data-id');
		var grid = $scope.find('.dce-posts-container.dce-skin-grid .dce-posts-wrapper');
		var masonryGrid = null;
		var isMasonryEnabled = false;
		let byRow = elementSettings.grid_match_height_by_row || elementSettings.grid_filters_match_height_by_row;

		const findMatchHeightSlices = () => {
			let matchHeightEls
			if( elementSettings.style_items === 'template' ) {
				if( $scope.find( '.dce-post-block .elementor-inner-section' ).length ) {
					matchHeightEls = [];
					$scope.find('.dce-post-block').first().find('.elementor-inner-section').each((i) => {
						let $els = $scope.find('.dce-post-block').map((_,$e) => {
							return jQuery($e).find('.elementor-inner-section')[i]
						})
						matchHeightEls.push($els);
					});
				} else if ( $scope.find( '.dce-post-block .elementor-top-section' ).length ) {
					let selector = '.dce-post-block .elementor-top-section';
					matchHeightEls = [ $scope.find(selector) ];
				} else {
					// select all top level containers:
					matchHeightEls = [ $scope.find('.e-con').not($scope.find('.e-con .e-con')) ];
				}
			} else {
				let selector = '.dce-post-block';
				matchHeightEls = [ $scope.find(selector) ];
			}
			return matchHeightEls;
		}

		const findAndMatchHeight = () => {
			slices = findMatchHeightSlices();
			for (const $els of slices ) {
				$els.matchHeight({byRow: byRow});
			}
		}

		// MASONRY
		function activeMasonry(){
			masonryGrid = grid.masonry({
				itemSelector: '.dce-post-item',
				masonry: {
					horizontalOrder: true,
				},
			});
			isMasonryEnabled = true;
		}
		function layoutMasonry(){
			if( elementSettings[dceDynamicPostsSkinPrefix+'grid_type'] != 'masonry' ){
				masonryGrid.masonry('destroy');
				isMasonryEnabled = false;
			}else{
				masonryGrid.masonry();
			}
		}

		if( elementSettings.grid_match_height || elementSettings.grid_filters_match_height ) {
			findAndMatchHeight();
		}

		if( elementSettings[dceDynamicPostsSkinPrefix+'grid_type'] == 'masonry' ) {
			activeMasonry();
		}

		// When Search&Filter request is completed
		$(document).on("sf:ajaxfinish", ".searchandfilter", function( e, data ) {
			// Add inline CSS for background url
			var allArticles = document.querySelectorAll(".dce-dynamic-posts-collection .elementor-section, .dce-dynamic-posts-collection .elementor-column, .dce-dynamic-posts-collection .elementor-widget, .dce-dynamic-posts-collection .e-container, .dce-dynamic-posts-collection .e-con");
			allArticles.forEach(function(article) {
				dce.addCssForBackground( article );
			});
		});

		// InfiniteScroll
		if (! elementorFrontend.isEditMode() && elementSettings.infiniteScroll_enable) {
			var elementorElement = '.elementor-element-' + id_scope;
			var is_history = Boolean( elementSettings.infiniteScroll_enable_history ) ? 'replace' : false;
			var $gridContainer = $scope.find('.dce-posts-container.dce-skin-grid .dce-posts-wrapper.dce-wrapper-grid');
			var $layoutMode = elementSettings[dceDynamicPostsSkinPrefix+'grid_type'];
			var $grid = $gridContainer.isotope({
				itemSelector: '.dce-post-item',
				layoutMode: 'masonry' === $layoutMode ? 'masonry' : 'fitRows',
				sortBy: 'original-order',
				percentPosition: true,
				masonry: {
					columnWidth: '.dce-post-item'
				}
			});
			$grid.imagesLoaded().progress( function() {
				$grid.isotope('layout');
			});

			let $tabpanel = $grid.closest('[role="tabpanel"]');
			if ( $tabpanel.length) {
				handleInfiniteScrollInsideTab( $tabpanel, $grid );
			} else {
				// For Elementor < 3.16
				let $tablist = $grid.closest('[role="tablist"]');
				if ( $tablist.length) {
					handleInfiniteScrollInsideTabLegacy( $tablist, $grid );
				}
			}

			// Apply link to template when layout is complete
			if(
				false === elementorFrontend.isEditMode() &&
					'yes' === elementSettings.templatemode_linkable
			){
				$gridContainer.on( 'append.infiniteScroll', function( event, title, path ) {
					$scope.find('.dce-post.dce-post-item[data-post-link]').click(function() {
						window.location.assign( $(this).attr("data-post-link") );
						return false;
					});
				});
			}

			// Match Height when layout is complete
			if( elementSettings.grid_match_height || elementSettings.grid_filters_match_height ) {
				$gridContainer.on( 'append.infiniteScroll', function( event, title, path ) {
					findAndMatchHeight();
					$gridContainer.isotope('layout');
				});
			}

			// Reload the template after using Infinite Scroll
			if( 'template' === elementSettings.style_items ) {
				$gridContainer.on( 'append.infiniteScroll', function( event, title, path ) {
					if ( elementorFrontend) {
						if ( elementorFrontend.elementsHandler.runReadyTrigger ) {
							var widgets = $('.dce-dynamic-posts-collection').find('.elementor-widget');
							widgets.each(function (i) {
								elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
								elementorFrontend.hooks.doAction('frontend/element_ready/global', jQuery(this), jQuery);
							});

						}
					}

					// Add inline CSS for background
					var allArticles = document.querySelectorAll(".dce-dynamic-posts-collection .elementor-section, .dce-dynamic-posts-collection .elementor-column, .dce-dynamic-posts-collection .elementor-widget, .dce-dynamic-posts-collection .e-container, .dce-dynamic-posts-collection .e-con");
					allArticles.forEach(function(article) {
						dce.addCssForBackground( article );
					});
				});

				// When Search&Filter request is completed
				$(document).on("sf:ajaxfinish", ".searchandfilter", function( e, data ) {
					if ( elementorFrontend) {
						if( elementSettings.grid_match_height || elementSettings.grid_filters_match_height ) {
							findAndMatchHeight();
						}
						// Template Linkable
						$scope.find('.dce-post.dce-post-item[data-post-link]').click(function() {
							window.location.assign( $(this).attr("data-post-link") );
							return false;
						});
					}
				});
			}

			var iso = $grid.data('isotope');

			if (jQuery(elementorElement + ' .pagination__next').length) {
				var infiniteScroll_options = {
					path: elementorElement + ' .pagination__next',
					history: is_history,
					append: elementorElement + ' .dce-post.dce-post-item',
					outlayer: iso,
					status: elementorElement + ' .page-load-status',
					hideNav: elementorElement + '.pagination',
					scrollThreshold: 'scroll' === elementSettings.infiniteScroll_trigger ? true : false,
					loadOnScroll: 'scroll' === elementSettings.infiniteScroll_trigger ? true : false,
					onInit: function () {
						this.on('load', function () {
						});
					}
				}
				if (elementSettings.infiniteScroll_trigger == 'button') {
					// load pages on button click
					infiniteScroll_options['button'] = elementorElement + ' .view-more-button';
				}
				infScroll = $gridContainer.infiniteScroll(infiniteScroll_options);

				// fix for infinitescroll + masonry
				var nElements = jQuery(elementorElement + ' .dce-post-item:visible').length; // initial length

				$gridContainer.on( 'append.infiniteScroll', function( event, response, path, items ) {
					setTimeout(function(){
						var nElementsVisible = jQuery(elementorElement + ' .dce-post-item:visible').length;
						if (nElementsVisible <= nElements) {
							// force another load
							$gridContainer.infiniteScroll('loadNextPage');
						}
					}, 1000);
				});
			}
		}

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

		// Callback function executed when mutations occur
		var Dyncontel_MutationObserverCallback = function(mutationsList, observer) {
			for(var mutation of mutationsList) {
				if (mutation.type == 'attributes') {
					if (mutation.attributeName === 'class') {
						if (isMasonryEnabled) {
							layoutMasonry();
						}
					}
				}
			}
		};
		dceObserveElement($scope[0], Dyncontel_MutationObserverCallback);
	};

	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.grid', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.grid', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.grid', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-products-cart-on-sale.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.grid', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-upsells.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.grid', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-woo-product-crosssells.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.grid', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.grid', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.grid', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.grid', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.grid', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-metabox-relationship.grid', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-metabox-relationship.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-acf-relationship.grid', Widget_DCE_Dynamicposts_grid_Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-acf-relationship.grid-filters', Widget_DCE_Dynamicposts_grid_Handler);
	});
})(jQuery);
