( function( $ ) {

	var loadStatus = true;
	var count = 1;
	var loader = '';
	var total = 0;
	var isElEditMode = false;

	/**
	 * Function to fetch widget settings.
	 */
	 var getWidgetSettings = function ($element) {
		var widgetSettings = {},
			modelCID       = $element.data( 'model-cid' );

		if ( isElEditMode && modelCID ) {
			var settings     = elementorFrontend.config.elements.data[ modelCID ],
				settingsKeys = elementorFrontend.config.elements.keys[ settings.attributes.widgetType || settings.attributes.elType ];

			jQuery.each(
				settings.getActiveControls(),
				function( controlKey ) {
					if ( -1 !== settingsKeys.indexOf( controlKey ) ) {
						widgetSettings[ controlKey ] = settings.attributes[ controlKey ];
					}
				}
			);
		} else {
			widgetSettings = $element.data( 'settings' ) || {};
		}

		return widgetSettings;
	};


	function _equal_height( slider_wrapper ) {

		var post_wrapper = slider_wrapper.find('.uael-post-wrapper'),
            post_active = slider_wrapper.find('.slick-active'),
            max_height = -1,
            wrapper_height = -1,
            equal_height = slider_wrapper.data( 'equal-height' ),
            post_active_height = -1;

        if ( 'yes' != equal_height ) {
        	return;
        }

        post_active.each( function( i ) {

            var $this = $( this ),
				this_height = $this.outerHeight(),
                blog_post = $this.find( '.uael-post__bg-wrap' ),
                blog_post_height = blog_post.outerHeight();

            if( max_height < blog_post_height ) {
                max_height = blog_post_height;
                post_active_height = max_height + 15;
            }

            if ( wrapper_height < this_height ) {
                wrapper_height = this_height
            }
        });

        post_active.each( function( i ) {
            var selector = $( this ).find( '.uael-post__bg-wrap' );
            selector.animate({ height: max_height }, { duration: 200, easing: 'linear' });
        });

        slider_wrapper.find('.slick-list.draggable').animate({ height: post_active_height }, { duration: 200, easing: 'linear' });

        max_height = -1;
        wrapper_height = -1;

        post_wrapper.each(function() {

            var $this = jQuery( this ),
                selector = $this.find( '.uael-post__bg-wrap' ),
                blog_post_height = selector.outerHeight();

            if ( $this.hasClass('slick-active') ) {
                return true;
            }

            selector.css( 'height', blog_post_height );
        });

	}

	var WidgetUAELPostGridHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope ) {
			return;
		}

		var selector = $scope.find( '.uael-post-grid__inner' );

		if ( selector.length < 1 ) {
			return;
		}

		loader = $scope.find( '.uael-post-inf-loader' );

		var $tabs_dropdown = $scope.find('.uael-filters-dropdown-list');

		$( 'html' ).on( 'click', function() {
			$tabs_dropdown.removeClass( 'show-list' );
		});

		$scope.on( 'click', '.uael-filters-dropdown-button', function(e) {
			e.stopPropagation();
			$tabs_dropdown.toggleClass( 'show-list' );
		});

		var post_grid = $scope.find( '.uael-post-grid' );
		var	layout = post_grid.data( 'layout' ),
			structure = post_grid.data( 'structure' );
		
		var filter_cat;

		if ( 'masonry' == structure ) {
			if (typeof $scope.imagesLoaded !== 'undefined' && typeof $scope.imagesLoaded === 'function') {
				$scope.imagesLoaded( function(e) {

					selector.isotope({
						layoutMode: layout,
						itemSelector: '.uael-post-wrapper',
					});

				});
			}
		}

		$scope.find( '.uael-post__header-filter' ).off( 'click' ).on( 'click', function(e) {
			$this = $( this );
			$this.siblings().removeClass( 'uael-filter__current' );
			$this.addClass( 'uael-filter__current' );

			var filterValue = $this.attr( 'data-filter' );
			var def_filter = '';

			if( '*' === filterValue ) {
				filter_cat = post_grid.data( 'filter-default' );
			} else {
				filter_cat = filterValue.substr(1);
			}

			if( post_grid.data( 'default-filter' ) ) {
				def_filter = post_grid.data( 'default-filter' );
			} else {
				def_filter = post_grid.data( 'filter-default' );
			}

			var str_text = $scope.find( '.uael-filter__current' ).first().text();
			$scope.find( '.uael-filters-dropdown-button' ).text( str_text );

			count = 1;

			_uaelPostAjax( $scope, $this );

		});

		$scope.find( '.uael-post__header-filter' ).off( 'keyup' ).on( 'keyup', function( e ) {
			$this = $( this );
			if ( 9 == e.keyCode ) {
				$this.siblings().removeClass( 'uael-filter__current' );
				$this.addClass( 'uael-filter__current' );

				var filterValue = $this.attr( 'data-filter' );
				var def_filter = '';

				if( '*' === filterValue ) {
					filter_cat = post_grid.data( 'filter-default' );
				} else {
					filter_cat = filterValue.substr(1);
				}

				if( post_grid.data( 'default-filter' ) ){
					def_filter = post_grid.data( 'default-filter' );
				} else {
					def_filter = post_grid.data( 'filter-default' );
				}

				var str_text = $scope.find( '.uael-filter__current' ).text();
				str_text = str_text.substring( def_filter.length, str_text.length );
				$scope.find( '.uael-filters-dropdown-button' ).text( str_text );

					count = 1;
				}
				if ( 13 === e.keyCode ) {
					_uaelPostAjax( $scope, $this );
				}

		});

		if ( $scope.find( '.uael-post__header' ).children().length > 0 ) {

			var default_filter = $scope.find( '.uael-post-grid' ).data( 'default-filter' );
			var cat_id 	       = window.location.hash.substring(1);
			var pattern        = new RegExp( "^[\\w\\-]+$" );

			if( '' !== cat_id && pattern.test( cat_id ) ) {
				$scope.find( '.uael-post__header-filter' ).each( function( key, value ) {
					var $this = $( this );
					var current_filter = $this.attr('data-filter');
					if ( cat_id == current_filter.split('.').join("") ) {
						$this.trigger( 'click' );
						$this.trigger( 'keyup' );
					}
				});
			}

			if ( 'undefined' != typeof default_filter && '' != default_filter ) {
				$scope.find( '.uael-post__header-filter' ).each( function( key, value ) {
					var $this = $( this );
					if ( default_filter == $this.text() ) {
						$this.trigger( 'click' );
						$this.trigger( 'keyup' );
					}
				} );
			}
		}

		if ( 'carousel' == structure ) {

			var slider_wrapper 	= $scope.find( '.uael-post-grid' ),
				slider_selector = slider_wrapper.find( '.uael-post-grid__inner' ),
				slider_options 	= slider_wrapper.data( 'post_slider' );

			$scope.imagesLoaded( function() {

				slider_selector.slick( slider_options );
				_equal_height( slider_wrapper );
			});

			slider_wrapper.on( 'afterChange', function() {
				_equal_height( slider_wrapper );
			} );


			$( window ).on( 'resize',function() {
				$( "#log" ).append( "<div>Handler for .resize() called.</div>" );
			});
		}

		if ( selector.hasClass( 'uael-post-infinite-scroll' ) && selector.hasClass( 'uael-post-infinite__event-scroll' ) ) {

			if ( 'main' == $scope.find( '.uael-post-grid' ).data( 'query-type' ) ) {
				return;
			}

			var windowHeight50 = jQuery( window ).outerHeight() / 1.25;

			$( window ).on( 'scroll', function () {

				if( elementorFrontend.isEditMode() ) {
					loader.show();
					return false;
				}

				if( ( $( window ).scrollTop() + windowHeight50 ) >= ( $scope.find( '.uael-post-wrapper:last' ).offset().top ) ) {

					var $args = {
						'page_id' : $scope.find( '.uael-post-grid' ).data('page'),
						'widget_id' : $scope.data( 'id' ),
						'filter' : $scope.find( '.uael-filter__current' ).data( 'filter' ),
						'skin' : $scope.find( '.uael-post-grid' ).data( 'skin' ),
						'page_number' : $scope.find( '.uael-post__footer .uael-grid-pagination' ).data( 'next-page' )
					};

					total = $scope.find( '.uael-post__footer .uael-grid-pagination' ).data( 'total' );

					if( true == loadStatus ) {

						if ( count < total ) {
							loader.show();
							_callAjax( $scope, $args, true );
							count++;
							loadStatus = false;
						}

					}
				}
			} );
		}

	}
	
	$( document ).on( 'click', '.uael-post__load-more', function( e ) {

		$scope = $( this ).closest( '.elementor-widget-uael-posts' );
		if ( 'main' == $scope.find( '.uael-post-grid' ).data( 'query-type' ) ) {
			return;
		}

		e.preventDefault();

		if( elementorFrontend.isEditMode() ) {
			loader.show();
			return false;
		}

		var $args = {
			'page_id' : $scope.find( '.uael-post-grid' ).data('page'),
			'widget_id' : $scope.data( 'id' ),
			'filter' : $scope.find( '.uael-filter__current' ).data( 'filter' ),
			'skin' : $scope.find( '.uael-post-grid' ).data( 'skin' ),
			'page_number' : ( count + 1 )
		};

		total = $scope.find( '.uael-post__footer .uael-grid-pagination' ).data( 'total' );

		if( true == loadStatus ) {

			if ( count < total ) {
				loader.show();
				$( this ).hide();
				_callAjax( $scope, $args, true );
				count++;
				loadStatus = false;
			}

		}
	} );

	$( 'body' ).on( 'click', '.uael-grid-pagination .page-numbers', function( e ) {

		$scope = $( this ).closest( '.elementor-widget-uael-posts' );
		var elementSettings = getWidgetSettings( $scope );
		var searchString = "show_filters";
		
		var found = Object.keys(elementSettings).filter(function(key) {
			return elementSettings[key] === 'ajax';
		});
		var is_filters = Object.keys(elementSettings).filter(function(key) {
			return key.includes( searchString );
		});

		if ( ! found.length && ! is_filters.length ) {
   			return;
		}

		var post_grid = $scope.find( '.uael-post-grid' );
		if ( 'main' == post_grid.data( 'query-type' ) ) {
			return;
		}

		e.preventDefault();

		$scope.find( '.uael-post-grid .uael-post-wrapper' ).last().after( '<div class="uael-post-loader"><div class="uael-loader"></div><div class="uael-loader-overlay"></div></div>' );

		var page_number = 1;
		var curr = parseInt( $scope.find( '.uael-grid-pagination .page-numbers.current' ).html() );
		var $this = $( this );
		if ( $this.hasClass( 'next' ) ) {
			page_number = curr + 1;
		} else if ( $this.hasClass( 'prev' ) ) {
			page_number = curr - 1;
		} else {
			page_number = $this.html();
		}

		$scope.find( '.uael-post-grid .uael-post-wrapper' ).last().after( '<div class="uael-post-loader"><div class="uael-loader"></div><div class="uael-loader-overlay"></div></div>' );

		var $args = {
			'page_id' : post_grid.data('page'),
			'widget_id' : $scope.data( 'id' ),
			'filter' : $scope.find( '.uael-filter__current' ).data( 'filter' ),
			'skin' : post_grid.data( 'skin' ),
			'page_number' : page_number
		};

		var offset_top = post_grid.data( 'offset-top' );
		if( '' != post_grid.data('filter-default') ){
			offset_top = $scope.find('.uael-post__header-filters').outerHeight() + parseFloat( $scope.find( '.uael-post__header .uael-post__header-filters-wrap').css( 'marginBottom' ));
		}

		$('html, body').animate({
			scrollTop: ( ( $scope.find( '.uael-post__body' ).offset().top ) - offset_top )
		}, 'slow');

		_callAjax( $scope, $args );

	} );

	var _uaelPostAjax = function( $scope, $this ) {

		$scope.find( '.uael-post-grid .uael-post-wrapper' ).last().after( '<div class="uael-post-loader"><div class="uael-loader"></div><div class="uael-loader-overlay"></div></div>' );

		var $args = {
			'page_id' : $scope.find( '.uael-post-grid' ).data('page'),
			'widget_id' : $scope.data( 'id' ),
			'filter' : $this.data( 'filter' ),
			'skin' : $scope.find( '.uael-post-grid' ).data( 'skin' ),
			'page_number' : 1
		};

		_callAjax( $scope, $args );
	}

	var _callAjax = function( $scope, $obj, $append ) {

		$.ajax({
			url: uael_posts_script.ajax_url,
			data: {
				action: 'uael_get_post',
				page_id : $obj.page_id,
				widget_id: $obj.widget_id,
				category: $obj.filter,
				skin: $obj.skin,
				page_number : $obj.page_number,
				nonce : uael_posts_script.posts_nonce,
			},
			dataType: 'json',
			type: 'POST',
			success: function( data ) {

				$scope.find( '.uael-post-loader' ).remove();

				var sel = $scope.find( '.uael-post-grid__inner' );

				if( 'news' == $obj.skin ) {
					sel = $scope.find( '.uael-post-grid' );
				}

				if ( true == $append ) {
					var html_str = data.data.html;
					html_str = html_str.replace( 'uael-post-wrapper-featured', '' );

					sel.append( html_str );
				} else {
					sel.html( data.data.html );
				}

				$scope.find( '.uael-post__footer' ).html( data.data.pagination );

				var layout = $scope.find( '.uael-post-grid' ).data( 'layout' ),
					structure = $scope.find( '.uael-post-grid' ).data( 'structure' ),
					selector = $scope.find( '.uael-post-grid__inner' );

				if (
					( 'normal' == structure || 'masonry' == structure ) &&
					'' != layout
				) {
					if (typeof $scope.imagesLoaded !== 'undefined' && typeof $scope.imagesLoaded === 'function') {
						$scope.imagesLoaded( function(e) {
							selector.isotope( 'reloadItems' );
							selector.isotope({
								layoutMode: layout,
								itemSelector: '.uael-post-wrapper',
								animate: false
							});
						});
					}
				}

				//	Complete the process 'loadStatus'
				loadStatus = true;
				if ( true == $append ) {
					loader.hide();
					$scope.find( '.uael-post__load-more' ).show();
				}

				if( count == total ) {
					$scope.find( '.uael-post__load-more' ).hide();
				}
			}
		});
	}

	$( window ).on( 'elementor/frontend/init', function () {

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-posts.classic', WidgetUAELPostGridHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-posts.event', WidgetUAELPostGridHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-posts.card', WidgetUAELPostGridHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-posts.feed', WidgetUAELPostGridHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-posts.news', WidgetUAELPostGridHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-posts.business', WidgetUAELPostGridHandler );

	});

} )( jQuery );
