( function( $ ) {
	var device_mode;


	OffCanvas = {

		/**
		* Invoke Show Off-Canvas
		*/
		_show: function( canvas_id ) {

			var $canvas_element = $( '#offcanvas-' + canvas_id );

			var wrap_width = $canvas_element.width() + 'px';

			var body = $( 'body' );
			var html = $( 'html' );

			/* If Off-Canvas at Left position */
			if( $canvas_element.hasClass( 'position-at-left' ) ) {

				body.css( 'margin-left' , '0' );
				$canvas_element.css( 'left', '0' );

				/* If Push Transition is enabled */
				if( $canvas_element.hasClass( 'uael-offcanvas-type-push' ) ) {

					body.addClass( 'uael-offcanvas-animating' ).css({
						position: 'absolute',
						width: '100%',
						'margin-left' : wrap_width,
						'margin-right' : 'auto'
					});

				}

				$canvas_element.addClass( 'uael-offcanvas-show' );

			} else {

				body.css( 'margin-right', '0' );
				$canvas_element.css( 'right', '0' );

				/* If Push Transition is enabled */
				if( $canvas_element.hasClass( 'uael-offcanvas-type-push' ) ) {

					body.addClass( 'uael-offcanvas-animating' ).css({
						position: 'absolute',
						width: '100%',
						'margin-left' : '-' + wrap_width,
						'margin-right' : 'auto',
					});
				}

				$canvas_element.addClass( 'uael-offcanvas-show' );
			}

			if( $canvas_element.hasClass( 'uael-offcanvas-scroll-disable' ) ) {
				html.addClass( 'uael-offcanvas-enabled' );
			}

			device_mode = body.data( 'elementor-device-mode' );
			if( 'mobile' == device_mode ){
			    html.addClass( 'uael-off-canvas-overlay' );
			}
		},

		/**
		 * Invoke Close Off-Canvas
		 */
		_close: function( canvas_id ) {
			var $canvas_element = $( '#offcanvas-' + canvas_id );

			var wrap_width = $canvas_element.width() + 'px';

			var body = $( 'body' );
			var html = $( 'html' );

			/* If Off-Canvas at Left position */
			if( $canvas_element.hasClass( 'position-at-left' ) ) {

				$canvas_element.css( 'left', '-' + wrap_width );

				/* If Push Transition  is enabled*/
				if( $canvas_element.hasClass( 'uael-offcanvas-type-push' ) ) {

					body.css({
						position: '',
						'margin-left' : '',
						'margin-right' : '',
					});

					setTimeout( function() {
						body.removeClass( 'uael-offcanvas-animating' ).css({
							width: '',
						});
					}, 300 );
				}

				$canvas_element.removeClass( 'uael-offcanvas-show' );

			} else {
				$canvas_element.css( 'right', '-' + wrap_width );

				/* If Push Transition is enabled */
				if( $canvas_element.hasClass( 'uael-offcanvas-type-push' ) ) {

					body.css({
						position: '',
						'margin-right' : '',
						'margin-left' : '',
					});

					setTimeout( function() {
						body.removeClass( 'uael-offcanvas-animating' ).css({
							width: '',
						});
					}, 300 );
				}

				$canvas_element.removeClass( 'uael-offcanvas-show' );
			}

			html.removeClass( 'uael-offcanvas-enabled' );

			device_mode = body.data( 'elementor-device-mode' );
			if( 'mobile' == device_mode ){
			    html.removeClass( 'uael-off-canvas-overlay' );
			}
		},
	}

		/**
		* Trigger open Off Canvas On Click Button/Icon
		*/
		$( document ).off( 'click.opentrigger' ).on( 'keyup click.opentrigger', '.uael-offcanvas-trigger', function(e) {

			var canvas_id = $( this ).closest( '.elementor-element' ).data( 'id' );
			var selector = $( '.uaoffcanvas-' + canvas_id );
			var trigger_on = selector.data( 'trigger-on' );

			if( 'icon' == trigger_on || 'button' == trigger_on ) {
				if( 'keyup' == e.type ) {
					var code = (e.keyCode ? e.keyCode : e.which);
        			if (code == 13) {
						OffCanvas._show( canvas_id );
					}
				} else {
					OffCanvas._show( canvas_id );
				}
			}
		} );

		/*
		* uael_offcanvas_init trigger
		*/
		$( document ).on( 'uael_offcanvas_init', function( e, node_id ) {

			/*
			* Close on ESC
			*/
			$( document).on( 'keyup', function(e) {
				if ( e.keyCode == 27 )
				{
					$( '.uael-offcanvas-parent-wrapper' ).each( function() {
						var $this = $( this );
						var canvas_id = $this.closest( '.elementor-element' ).data( 'id' );
						var close_on_esc = $this.data( 'close-on-esc' );

						if( 'yes' == close_on_esc ) {
							OffCanvas._close( canvas_id );
						}
					});
				}

			});

			/**
			* Close on Icon
			*/
			$( '.uael-offcanvas-close' ).on( 'click', function () {
					var canvas_id = $( this ).closest( '.elementor-element' ).data( 'id' );
					OffCanvas._close( canvas_id );

			});

			/**
			* Close On Overlay Click
			*/
			$( '.uael-offcanvas-overlay' ).off('click.overlaytrigger').on( 'click.overlaytrigger', function( e ) {

				$( '.uael-offcanvas-parent-wrapper' ).each( function() {
					var $this = $( this );
					var canvas_id = $this.closest( '.elementor-element' ).data( 'id' );
					var close_on_overlay = $this.data( 'close-on-overlay' );

					if( 'yes' == close_on_overlay ) {
						OffCanvas._close( canvas_id );
					}
				});
			});

			/**
			* If Preview-Mode is ON
			*/
			if( $( '#offcanvas-' + node_id ).hasClass( 'uael-show-preview' ) ) {
				setTimeout( function() {
						OffCanvas._show( node_id );
				}, 400 );
			} else {
				setTimeout( function() {
						OffCanvas._close( node_id );
				}, 400 );
			}

		} );

		/* On Load page event */
		$( document ).ready( function( e ) {

			$( '.uael-offcanvas-parent-wrapper' ).each( function() {

				var $this = $( this );
				var tmp_id = $this.attr( 'id' );
				var canvas_id = tmp_id.replace( '-overlay', '' );
				var trigger_on = $this.data( 'trigger-on' );
				var custom = $this.data( 'custom' );
				var custom_id = $this.data( 'custom-id' );

				// Custom Class click event
				if( 'custom' == trigger_on ) {
					if( 'undefined' != typeof custom && '' != custom ) {
						var custom_selectors = custom.split( ',' );
						if( custom_selectors.length > 0 ) {
							for( var i = 0; i < custom_selectors.length; i++ ) {
								if( 'undefined' != typeof custom_selectors[i] && '' != custom_selectors[i] ) {
									$( '.' + custom_selectors[i] ).css( "cursor", "pointer" );
									$( document ).on( 'click', '.' + custom_selectors[i], function() {
										OffCanvas._show( canvas_id );
									} );
								}
							}
						}
					}
				}

				// Custom ID click event
				if( 'custom_id' == trigger_on ) {
					if( 'undefined' != typeof custom_id && '' != custom_id ) {
						var custom_selectors = custom_id.split( ',' );
						if( custom_selectors.length > 0 ) {
							for( var i = 0; i < custom_selectors.length; i++ ) {
								if( 'undefined' != typeof custom_selectors[i] && '' != custom_selectors[i] ) {
									$( '#' + custom_selectors[i] ).css( "cursor", "pointer" );
									$( document ).on( 'click', '#' + custom_selectors[i], function() {
										OffCanvas._show( canvas_id );
									} );
								}
							}
						}
					}
				}
			} );

		} );

		/**
		 * Off-Canvas handler Function.
		 *
		 */
		var WidgetOffCanvasHandler = function( $scope, $ ) {

			if ( 'undefined' == typeof $scope )
				return;

			var id = $scope.data( 'id' );
			var parent_wrap = $scope.find( '.uael-offcanvas-parent-wrapper' );
			var wrap_menu_item = parent_wrap.data( 'wrap-menu-item' );

			if ( $scope.hasClass('elementor-hidden-desktop') ) {
	        	parent_wrap.addClass( 'uael-offcanvas-hide-desktop' );
			}

			if ( $scope.hasClass('elementor-hidden-tablet') ) {
	        	parent_wrap.addClass( 'uael-offcanvas-hide-tablet' );
			}

			if ( $scope.hasClass('elementor-hidden-phone') ) {
	        	parent_wrap.addClass( 'uael-offcanvas-hide-phone' );
			}

			$( document ).trigger( 'uael_offcanvas_init', [ $scope.data( 'id' ) ] );

			if( 'yes' == wrap_menu_item ) {

				$scope.find( 'div.uael-offcanvas-has-submenu-container' ).removeClass( 'uael-offcanvas-sub-menu-active' );
				// Wrap submenu JS.
				$scope.find( '.sub-menu' ).each( function() {

					var parent = $( this ).closest( '.menu-item' );

					$scope.find( parent ).addClass( 'uael-offcanvas-parent-has-child' );
				});

				var submenu_container = $scope.find( '.uael-offcanvas-parent-has-child .uael-offcanvas-has-submenu-container a' );

				submenu_container.attr( 'aria-haspopup', 'true' );
				submenu_container.attr( 'aria-expanded', 'false' );

				// On parent menu link
				$( '.elementor-element-' + id + ' div.uael-offcanvas-has-submenu-container' ).off( 'click' ).on( 'click', function( event ) {

					var $this = $( this );

					if( $( '.elementor-element-' + id ).hasClass( 'uael-off-canvas-link-redirect-child' ) ) {

						if( $this.hasClass( 'uael-offcanvas-sub-menu-active' ) ) {

							if( ! $this.next().hasClass( 'uael-offcanvas-sub-menu-open' ) ) {

								event.preventDefault();

								$this.find( 'a' ).attr( 'aria-expanded', 'false' );
								$this.removeClass( 'uael-offcanvas-sub-menu-active' );
								$this.next().removeClass( 'uael-offcanvas-sub-menu-open' );
							} else {

								$this.find( 'a' ).attr( 'aria-expanded', 'false' );
								$this.removeClass( 'uael-offcanvas-sub-menu-active' );
								$this.next().removeClass( 'uael-offcanvas-sub-menu-open' );
							}
						} else {

							event.preventDefault();

							$this.find( 'a' ).attr( 'aria-expanded', 'true' );
							$this.addClass( 'uael-offcanvas-sub-menu-active' );
							$this.next().addClass( 'uael-offcanvas-sub-menu-open' );
						}
					}

				});

				// On icon click
				$( '.elementor-element-' + id + ' .uael-offcanvas-menu-toggle' ).off( 'click keyup' ).on( 'click keyup', function( event ) {

					var $this = $( this );
					var active_menu = $this.parent().parent();
					var active_toggle = $this.parent();

					if( $this.parent().parent().hasClass( 'uael-offcanvas-menu-active' ) ) {

						event.preventDefault();
						active_menu.removeClass( 'uael-offcanvas-menu-active' );
						active_toggle.attr( 'aria-expanded', 'false' );

					} else {

						event.preventDefault();
						active_menu.addClass( 'uael-offcanvas-menu-active' );
						active_toggle.attr( 'aria-expanded', 'true' );
					}
				});
			}

		};

		$( window ).on( 'elementor/frontend/init', function () {

			elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-offcanvas.default', WidgetOffCanvasHandler );

		});

} )( jQuery );
