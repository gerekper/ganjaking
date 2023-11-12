( function( $ ) {

	/**
	 * Nav Menu handler Function.
	 *
	 */
	var WidgetUAELNavMenuHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope )
			return;

		var id = $scope.data( 'id' );
		var parent = $( '.elementor-element-' + id );
		var wrapper = $scope.find('.elementor-widget-uael-nav-menu ');
		var layout = $( '.elementor-element-' + id + ' .uael-nav-menu' ).data( 'layout' );
		var flyout_data = $( '.uael-flyout-wrapper' ).data( 'flyout-class' );
		var url = window.location.href;
		var custom_menu = $scope.find( '.uael-nav-menu-custom li' );
		var saved_content = $scope.find( '.saved-content' );
		var last_item = parent.find('.uael-nav-menu' ).data( 'last-item' );
		var last_item_flyout = parent.find('.uael-flyout-wrapper' ).data( 'last-item' );
		var last_menu_item = parent.find('li.menu-item:last-child a.uael-menu-item' );

		var cta_classes = {
			_addClassesCta: function (){
				last_menu_item.parent().addClass( 'elementor-button-wrapper' );
				last_menu_item.addClass( 'elementor-button' );
			},

			_removeClassesCta: function (){
				last_menu_item.parent().removeClass( 'elementor-button-wrapper' );
				last_menu_item.removeClass( 'elementor-button' );
			}
		}

		$( 'div.uael-has-submenu-container' ).removeClass( 'sub-menu-active' );

		_sizeCal( id );

		_toggleClick( id );

		_handleSinglePageMenu( id, layout );


		if( 'horizontal' !== layout ){

			_eventClick( id );
		}else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches ) {

			_eventClick( id );
		}else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches ) {

			_eventClick( id );
		} else if( 'horizontal' == layout && $( '.elementor-element-' + id ).hasClass( 'uael-submenu-open-click' ) && window.matchMedia( "( min-width: 1025px )" ).matches ){

			_eventClick( id );
		}

		_borderClass( id, cta_classes );

		$( '.elementor-element-' + id + ' .uael-nav-menu-icon' ).off( 'click keyup' ).on( 'click keyup', function() {

			_openMenu( id );
		} );

		$( '.elementor-element-' + id + ' .uael-flyout-close' ).off( 'click keyup' ).on( 'click keyup', function() {

			_closeMenu( id );
		} );

		$( '.elementor-element-' + id + ' .uael-flyout-overlay' ).off( 'click' ).on( 'click', function() {

			_closeMenu( id );
		} );


		$scope.find( '.sub-menu' ).each( function() {

			var parent = $( this ).closest( '.menu-item' );

			$scope.find( parent ).addClass( 'parent-has-child' );
			$scope.find( parent ).removeClass( 'parent-has-no-child' );
		});

		if( ( 'cta' == last_item || 'cta' == last_item_flyout ) && 'expandible' != layout ){
			cta_classes._addClassesCta();
		}

		saved_content.each( function() {

			var parent_content = $( this ).closest( '.sub-menu' );

			$scope.find( parent_content ).addClass( 'parent-has-template' );
			$scope.find( parent_content ).removeClass( 'parent-do-not-have-template' );
		});

		if( 'horizontal' == $( '.uael-nav-menu' ).data( 'menu-layout' ) ) {

			saved_content.each( function() {

				var parent_css = $( this ).data( 'left-pos' );

				$( this ).closest( '.sub-menu' ).css( 'left',  parent_css + '%' );
			});
		}

		custom_menu.each( function(){
			var $this = $( this );
			var href = $this.find( 'a' ).attr( 'href' );
			if( url == href ){
			   var parentClass = $this.parent( 'ul' ).hasClass( 'sub-menu' );
			   if( parentClass ) {
				$this.addClass( 'custom-submenu-active' );
				$this.parents( '.uael-nav-menu-custom li' ).addClass( 'custom-menu-active' );
			   }else {
				$this.addClass( 'custom-menu-active' );
			   }
			}
		});

		$( window ).on( 'resize', function(){

			_sizeCal( id );

			if( 'horizontal' !== layout ) {

				_eventClick( id );
			}else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches ) {

				_eventClick( id );
			}else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches ) {

				_eventClick( id );
			} else if( 'horizontal' == layout && $( '.elementor-element-' + id ).hasClass( 'uael-submenu-open-click' ) && window.matchMedia( "( min-width: 1025px )" ).matches ){

				_eventClick( id );
			}

			if( 'horizontal' == layout && window.matchMedia( "( min-width: 977px )" ).matches){

				$( '.elementor-element-' + id + ' div.uael-has-submenu-container' ).next().css( 'position', 'absolute');
			}

			if( 'expandible' == layout || 'flyout' == layout ){

				_toggleClick( id );
			}else if ( 'vertical' == layout || 'horizontal' == layout ) {
				if( window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile'))){

					_toggleClick( id );
				}else if ( window.matchMedia( "( max-width: 1024px )" ).matches && $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') ) {

					_toggleClick( id );
				}
			}

				_borderClass( id, cta_classes );

		});

		var submenu_parent_template = $scope.find( '.sub-menu.parent-has-template' );
		submenu_parent_template.css( 'box-shadow', 'none' );
		submenu_parent_template.css( 'border', 'none' );
		submenu_parent_template.css( 'border-radius', '0' );

		var padd = $( '.elementor-element-' + id + ' ul.sub-menu li a' ).css( 'paddingLeft' );
		    padd = parseFloat( padd );
            padd = padd + 20;

        $( '.elementor-element-' + id + ' ul.sub-menu li a.uael-sub-menu-item' ).css( 'paddingLeft', padd + 'px' );

		//Top Distance functionality
		var parent_settings = parent.data('settings');

		if ( parent_settings && parent_settings.distance_from_menu ) {
			var top_value = parent_settings.distance_from_menu.size + 'px';
			var style_tag = document.createElement('style');
			style_tag.innerHTML = `
				nav ul li.menu-item ul.sub-menu::before {
					height: ${top_value};
					top: -${top_value};
				}
			`;
			document.head.appendChild(style_tag);
		}

        // Acessibility functions
		var submenu_container = $scope.find( '.parent-has-child .uael-has-submenu-container a' );
		var nav_toggle = $scope.find( '.uael-nav-menu__toggle' );
  		submenu_container.attr( 'aria-haspopup', 'true' );
  		submenu_container.attr( 'aria-expanded', 'false' );

  		nav_toggle.attr( 'aria-haspopup', 'true' );
  		nav_toggle.attr( 'aria-expanded', 'false' );

		if ( window.matchMedia( "( max-width: 1024px )" ).matches && $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') ) {
			nav_toggle.find('i').attr('aria-hidden', 'false');
		}

		if ( window.matchMedia( "( max-width: 767px )" ).matches && $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile') ) {
			nav_toggle.find('i').attr('aria-hidden', 'false');
		}

  		// End of accessibility functions

		$( document ).trigger( 'uael_nav_menu_init', id );

		$( '.elementor-element-' + id + ' div.uael-has-submenu-container' ).on( 'keyup', function(e){

			var $this = $( this );
			var $parent_div = $this.parent();

		  	if( $parent_div.hasClass( 'menu-active' ) ) {

		  		$parent_div.removeClass( 'menu-active' );

		  		$parent_div.next().find('ul').css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );
		  		$parent_div.prev().find('ul').css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );

		  		$parent_div.next().find( 'div.uael-has-submenu-container' ).removeClass( 'sub-menu-active' );
		  		$parent_div.prev().find( 'div.uael-has-submenu-container' ).removeClass( 'sub-menu-active' );
			}else {

				$parent_div.next().find('ul').css( { 'height': '0', 'opacity': '0', 'visibility': 'hidden' } );
		  		$parent_div.prev().find('ul').css( { 'height': '0', 'opacity': '0', 'visibility': 'hidden' } );

		  		$parent_div.next().find( 'div.uael-has-submenu-container' ).removeClass( 'sub-menu-active' );
		  		$parent_div.prev().find( 'div.uael-has-submenu-container' ).removeClass( 'sub-menu-active' );

				$parent_div.siblings().find( '.uael-has-submenu-container a' ).attr( 'aria-expanded', 'false' );

				$parent_div.next().removeClass( 'menu-active' );
		  		$parent_div.prev().removeClass( 'menu-active' );

				event.preventDefault();

				$parent_div.addClass( 'menu-active' );

				if( 'horizontal' !== layout ){

					$this.addClass( 'sub-menu-active' );
				} else if( 'horizontal' == layout && $( '.elementor-element-' + id ).hasClass( 'uael-submenu-open-click' ) && window.matchMedia( "( min-width: 1025px )" ).matches ){

					$this.addClass( 'sub-menu-active' );
				}

				$this.find( 'a' ).attr( 'aria-expanded', 'true' );

				$this.next().css( { 'visibility': 'visible', 'height': 'auto', 'opacity': '1' } );

				if ( 'horizontal' !== layout ) {

		  			$this.next().css( 'position', 'relative');
				} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile'))) {

  					$this.next().css( 'position', 'relative');
				} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches ) {

  					if ( $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') ) {

  						$this.next().css( 'position', 'relative');
  					} else if ( $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-none') ) {

  						$this.next().css( 'position', 'absolute');
  					}
  				} else if( 'horizontal' == layout && $( '.elementor-element-' + id ).hasClass( 'uael-submenu-open-click' ) && window.matchMedia( "( min-width: 1025px )" ).matches ){

  					$this.next().css( 'position', 'absolute');
  				}
			}
		});

		$( '.elementor-element-' + id + ' li.menu-item' ).on( 'keyup', function(e){
			var $this = $( this );

	 		$this.next().find( 'a' ).attr( 'aria-expanded', 'false' );
	 		$this.prev().find( 'a' ).attr( 'aria-expanded', 'false' );


	  		$this.siblings().removeClass( 'menu-active' );
	  		$this.next().find( 'div.uael-has-submenu-container' ).removeClass( 'sub-menu-active' );
		  	$this.prev().find( 'div.uael-has-submenu-container' ).removeClass( 'sub-menu-active' );

		});
	};


	function _openMenu( id ) {

		var $flyout_content = $( '#uael-flyout-content-id-' + id );
		var layout = $( '#uael-flyout-content-id-' + id ).data( 'layout' );
		var layout_type = $( '#uael-flyout-content-id-' + id ).data( 'flyout-type' );
		var wrap_width = $flyout_content.width() + 'px';
		var container = $( '.elementor-element-' + id + ' .uael-flyout-container .uael-side.uael-flyout-' + layout );

		$( '.elementor-element-' + id + ' .uael-flyout-overlay' ).fadeIn( 100 );

		if( 'left' == layout ) {

			$( 'body' ).css( 'margin-left' , '0' );
			container.css( 'left', '0' );

			if( 'push' == layout_type ) {

				$( 'body' ).addClass( 'uael-flyout-animating' ).css({
					position: 'absolute',
					width: '100%',
					'margin-left' : wrap_width,
					'margin-right' : 'auto'
				});
			}
		} else {

			$( 'body' ).css( 'margin-right', '0' );
			container.css( 'right', '0' );

			if( 'push' == layout_type ) {

				$( 'body' ).addClass( 'uael-flyout-animating' ).css({
					position: 'absolute',
					width: '100%',
					'margin-left' : '-' + wrap_width,
					'margin-right' : 'auto',
				});
			}
		}
	}

	function _closeMenu( id ) {

		var $flyout_content = $( '#uael-flyout-content-id-' + id );
		var layout    = $flyout_content.data( 'layout' );
		var wrap_width = $flyout_content.width() + 'px';
		var layout_type = $flyout_content.data( 'flyout-type' );
		var container = $( '.elementor-element-' + id + ' .uael-flyout-container .uael-side.uael-flyout-' + layout );

		$( '.elementor-element-' + id + ' .uael-flyout-overlay' ).fadeOut( 100 );

		if( 'left' == layout ) {

			container.css( 'left', '-' + wrap_width );

			if( 'push' == layout_type ) {

				$( 'body' ).css({
					position: '',
					'margin-left' : '',
					'margin-right' : '',
				});

				setTimeout( function() {
					$( 'body' ).removeClass( 'uael-flyout-animating' ).css({
						width: '',
					});
				});
			}
		} else {
			container.css( 'right', '-' + wrap_width );

			if( 'push' == layout_type ) {

				$( 'body' ).css({
					position: '',
					'margin-right' : '',
					'margin-left' : '',
				});

				setTimeout( function() {
					$( 'body' ).removeClass( 'uael-flyout-animating' ).css({
						width: '',
					});
				});
			}
		}


	}

	function _eventClick( id ){

		var layout = $( '.elementor-element-' + id + ' .uael-nav-menu' ).data( 'layout' );

		$( '.elementor-element-' + id + ' div.uael-has-submenu-container' ).off( 'click' ).on( 'click', function( event ) {

			var $this = $( this );
			var $next_item = $this.next();

			if( $( '.elementor-element-' + id ).hasClass( 'uael-link-redirect-child' ) ) {

			  	if( $this.hasClass( 'sub-menu-active' ) ) {

			  		if( ! $next_item.hasClass( 'sub-menu-open' ) ) {

			  			$this.find( 'a' ).attr( 'aria-expanded', 'false' );

			  			if( 'horizontal' !== layout ){

							event.preventDefault();
			  				$next_item.css( 'position', 'relative' );
						} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile'))) {

							event.preventDefault();
			  				$next_item.css( 'position', 'relative' );
						} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches && ( $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile'))) {

							event.preventDefault();
			  				$next_item.css( 'position', 'relative' );
						} else if( 'horizontal' == layout && $( '.elementor-element-' + id ).hasClass( 'uael-submenu-open-click' ) && window.matchMedia( "( min-width: 1025px )" ).matches ){

							event.preventDefault();
			  				$next_item.css( 'position', 'absolute' );
						}

						$this.removeClass( 'sub-menu-active' );
						$this.nextAll('.sub-menu').removeClass( 'sub-menu-open' );
						$this.nextAll('.sub-menu').css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );
						$this.nextAll('.sub-menu').css( { 'transition': 'none'} );
			  		} else {

			  			$this.find( 'a' ).attr( 'aria-expanded', 'false' );

			  			$this.removeClass( 'sub-menu-active' );
						$this.nextAll('.sub-menu').removeClass( 'sub-menu-open' );
						$this.nextAll('.sub-menu').css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );
						$this.nextAll('.sub-menu').css( { 'transition': 'none'} );

						if ( 'horizontal' !== layout ){

							$next_item.css( 'position', 'relative' );
						} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile'))) {

							$next_item.css( 'position', 'relative' );

						} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches && ( $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile'))) {

							$next_item.css( 'position', 'absolute' );
						} else if( 'horizontal' == layout && $( '.elementor-element-' + id ).hasClass( 'uael-submenu-open-click' ) && window.matchMedia( "( min-width: 1025px )" ).matches ) {

							$next_item.css( 'position', 'absolute' );
						}
			  		}
				} else {

						var siblings = $( '.elementor-element-' + id ).find( 'div.uael-has-submenu-container' );

						if( $( this ).parent().parent().hasClass( 'uael-nav-menu' ) && 'horizontal' == layout && (
							( window.matchMedia( "( min-width: 1025px )" ).matches && $( '.elementor-element-' + id ).hasClass( 'uael-submenu-open-click' ) ) ||
							( window.matchMedia( "( min-width: 768px )" ).matches && ( $( '.elementor-element-' + id ).hasClass( 'uael-nav-menu__breakpoint-mobile' ) || $( '.elementor-element-' + id ).hasClass( 'uael-nav-menu__breakpoint-none' ) ) ) ||
							( window.matchMedia( "( max-width: 767px )" ).matches && $( '.elementor-element-' + id ).hasClass( 'uael-nav-menu__breakpoint-none' ) )
							) ){

							siblings.find( 'a' ).attr( 'aria-expanded', 'false' );
							siblings.removeClass( 'sub-menu-active' );
				  			siblings.next().removeClass( 'sub-menu-open' );
				  			siblings.next().css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );
						}

						$this.find( 'a' ).attr( 'aria-expanded', 'true' );

						if ( 'horizontal' !== layout ) {

							event.preventDefault();
				  			$next_item.css( 'position', 'relative');
						} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile'))) {

							event.preventDefault();
		  					$next_item.css( 'position', 'relative');
						} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches ) {
							event.preventDefault();

		  					if ( $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') ) {

		  						$next_item.css( 'position', 'relative');
		  					} else if ( $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-none') ) {

		  						$next_item.css( 'position', 'absolute');
		  					}
		  				} else if( 'horizontal' == layout && $( '.elementor-element-' + id ).hasClass( 'uael-submenu-open-click' ) && window.matchMedia( "( min-width: 1025px )" ).matches ){

		  					$next_item.css( 'position', 'absolute');
		  				}

					$this.addClass( 'sub-menu-active' );
					$this.nextAll('.sub-menu').addClass( 'sub-menu-open' );
					$this.nextAll('.sub-menu').css( { 'visibility': 'visible', 'opacity': '1', 'height': 'auto' } );
					$this.nextAll('.sub-menu').css( { 'transition': '0.3s ease'} );
				}
			}

		});

		$( '.elementor-element-' + id + ' .uael-menu-toggle' ).off( 'click keyup' ).on( 'click keyup',function( event ) {

			var $this = $( this );

		  	if( $this.parent().parent().hasClass( 'menu-active' ) ) {

	  			event.preventDefault();

				$this.parent().parent().removeClass( 'menu-active' );
				$this.parent().parent().next().css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );

				if ( 'horizontal' !== layout ) {

		  			$this.parent().parent().next().css( 'position', 'relative');
				} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile'))) {

  					$this.parent().parent().next().css( 'position', 'relative');
				} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches ) {

  					if ( $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') ) {

  						$this.parent().parent().next().css( 'position', 'relative');
  					} else if ( $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-none') ) {

  						$this.parent().parent().next().css( 'position', 'absolute');
  					}
  				} else if( 'horizontal' == layout && $( '.elementor-element-' + id ).hasClass( 'uael-submenu-open-click' ) && window.matchMedia( "( min-width: 1025px )" ).matches ){

  					$this.parent().parent().next().css( 'position', 'absolute');
  				}
			}else {

				event.preventDefault();

				var siblings = $( '.elementor-element-' + id ).find( 'div.uael-has-submenu-container' );

				if( $( this ).parent().parent().hasClass( 'uael-nav-menu' ) && 'horizontal' == layout && (
					( window.matchMedia( "( min-width: 1025px )" ).matches && $( '.elementor-element-' + id ).hasClass( 'uael-submenu-open-click' ) ) ||
					( window.matchMedia( "( min-width: 768px )" ).matches && ( $( '.elementor-element-' + id ).hasClass( 'uael-nav-menu__breakpoint-mobile' ) || $( '.elementor-element-' + id ).hasClass( 'uael-nav-menu__breakpoint-none' ) ) ) ||
					( window.matchMedia( "( max-width: 767px )" ).matches && $( '.elementor-element-' + id ).hasClass( 'uael-nav-menu__breakpoint-none' ) )
					) ){

					siblings.removeClass( 'menu-active' );
					siblings.next().css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );
				}

				$this.parent().parent().addClass( 'menu-active' );
				$this.parent().parent().next().css( { 'visibility': 'visible', 'opacity': '1', 'height': 'auto' } );

				if ( 'horizontal' !== layout ) {

		  			$this.parent().parent().next().css( 'position', 'relative');
				} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile'))) {

  					$this.parent().parent().next().css( 'position', 'relative');
				} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches ) {

  					if ( $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') ) {

  						$this.parent().parent().next().css( 'position', 'relative');
  					} else if ( $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-none') ) {

  						$this.parent().parent().next().css( 'position', 'absolute');
  					}
  				} else if( 'horizontal' == layout && $( '.elementor-element-' + id ).hasClass( 'uael-submenu-open-click' ) && window.matchMedia( "( min-width: 1025px )" ).matches ){

  					$this.parent().parent().next().css( 'position', 'absolute');
  				}
			}
		});
	}

	function _sizeCal( id ){

		$( '.elementor-element-' + id + ' li.menu-item' ).each( function() {

			var $this = $( this );
			var sub_menu = $this.find( 'ul.sub-menu' );

			var dropdown_width = $this.data('dropdown-width');
			var dropdown_pos = $this.data('dropdown-pos');
			var win_width = $( window ).width();

			if ( 'column' == dropdown_width ){

				var closeset_column = $( '.elementor-element-' + id).closest('.elementor-column');
				if ( 0 == closeset_column.length ) {
					var closeset_column = $( '.elementor-element-' + id).closest('.e-con--column');
					if ( 0 == closeset_column.length ) {
						var closeset_column = $( '.elementor-element-' + id).closest('.e-con');
					}
				}
				var width = closeset_column.outerWidth();

				if( $( 'body' ).hasClass( 'rtl' ) ) {
					var column_right = ( win_width - ( closeset_column.offset().left + closeset_column.outerWidth() ) );
					var template_right = ( win_width - ( $this.offset().left + $this.outerWidth() ) );
					var col_pos =  column_right - template_right;
					sub_menu.css( 'right', col_pos + 'px' );
				} else {

					var col_pos = closeset_column.offset().left - $this.offset().left;
					sub_menu.css('left', col_pos + 'px' );
				}

				sub_menu.css('width', width + 'px' );
			}else if ('section' == dropdown_width) {

				var closest_section = $( '.elementor-element-' + id).closest('.elementor-section');
				if ( 0 == closest_section.length ) {
					$('div[data-elementor-type="header"] > div[data-element_type="container"]:first-child').addClass('elementor-section');
					var closest_section = $( '.elementor-element-' + id).closest('.elementor-section');
				}
				var width = closest_section.outerWidth();

				sub_menu.css('width', width + 'px' );

				if ( closest_section && $this ) {
					if ( $( 'body' ).hasClass( 'rtl' ) ) {
						var sec_right = closest_section.offset() ? (win_width - (closest_section.offset().left + closest_section.outerWidth())) : 0;
						var template_right = $this.offset() ? (win_width - ($this.offset().left + $this.outerWidth())) : 0;
						var sec_pos = sec_right - template_right;
						sub_menu.css( 'right', sec_pos + 'px' );
					} else {
						var sec_pos = closest_section.offset() && $this.offset() ? (closest_section.offset().left - $this.offset().left) : 0;
						sub_menu.css( 'left', sec_pos + 'px' );
					}
				}
			}else if ( 'widget' == dropdown_width ){

				var nav_widget = $('.elementor-element-' + id + '.elementor-widget-uael-nav-menu');
				var width = nav_widget.outerWidth();

				if( $( 'body' ).hasClass( 'rtl' ) ) {

					var widget_right = ( win_width - ( nav_widget.offset().left + nav_widget.outerWidth() ) );
					var template_right = ( win_width - ( $this.offset().left + $this.outerWidth() ) );
					var widget_pos = widget_right - template_right;
					sub_menu.css( 'right', widget_pos + 'px' );
				} else {

					var widget_pos = nav_widget.offset().left - $this.offset().left;
					sub_menu.css( 'left', widget_pos + 'px' );
				}

				sub_menu.css('width', width + 'px' );
			}else if ('container' == dropdown_width) {

				var container = $( '.elementor-element-' + id).closest('.elementor-container');
				if ( 0 == container.length ) {
					$('div[data-elementor-type="header"] > div[data-element_type="container"]:first-child').addClass('elementor-container');
					var container = $( '.elementor-element-' + id).closest('.elementor-container');
				}
				var width = container.outerWidth();
				if ( container && $this ){

					if( $( 'body' ).hasClass( 'rtl' ) ) {

						var container_right = container.offset() ? ( win_width - ( container.offset().left + container.outerWidth() ) ) : 0;
						var template_right = $this.offset() ? ( win_width - ( $this.offset().left + $this.outerWidth() ) ) : 0;
						var widget_pos = container_right - template_right;
						sub_menu.css( 'right', widget_pos + 'px' );
					} else {

						var cont_pos = container.offset() && $this.offset() ? ( container.offset().left - $this.offset().left ) : 0;
						sub_menu.css( 'left', cont_pos + 'px' );
					}

					sub_menu.css( 'width', width + 'px' );
				}
			}

			if('center' == dropdown_pos && ( 'default' == dropdown_width || 'custom' == dropdown_width) ) {

				var parent = $this.find('.uael-has-submenu-container').outerWidth();
				var section_width = sub_menu.outerWidth();
				var left_pos = ( section_width - parent );

				left_pos = left_pos / 2;

				if( $( 'body' ).hasClass( 'rtl' ) ) {

					sub_menu.css('right', '-' + left_pos + 'px');
				} else {
					sub_menu.css('left', '-' + left_pos + 'px');
				}
			}else if ('right' == dropdown_pos && ( 'default' == dropdown_width || 'custom' == dropdown_width) ) {

				sub_menu.css('left', 'auto');
				sub_menu.css('right', '0');
			}
			else if ('left' == dropdown_pos && ( 'default' == dropdown_width || 'custom' == dropdown_width) && $( 'body' ).hasClass( 'rtl' ) ) {

				sub_menu.css('right', 'auto');
				sub_menu.css('left', '0');
			}
		});
	}

	function _borderClass( id, cta_classes ){

		var parent = $( '.elementor-element-' + id );
		var last_item = parent.find('.uael-nav-menu' ).data( 'last-item' );
		var last_item_flyout = parent.find('.uael-flyout-wrapper' ).data( 'last-item' );
		var layout = parent.find('.uael-nav-menu' ).data( 'layout' );
		var last_menu_item = parent.find('li.menu-item:last-child a.uael-menu-item' );

		var nav_element = $( '.elementor-element-' + id + ' nav');

		nav_element.removeClass('uael-dropdown');

		if ( window.matchMedia( "( max-width: 767px )" ).matches ) {

			if( $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-mobile') || $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet')){

				nav_element.addClass('uael-dropdown');
				if( ( 'cta' == last_item || 'cta' == last_item_flyout ) && 'expandible' != layout ){
					cta_classes._removeClassesCta();
				}
			}else{

				nav_element.removeClass('uael-dropdown');
				if( ( 'cta' == last_item || 'cta' == last_item_flyout ) && 'expandible' != layout ){
					cta_classes._addClassesCta();
				}
			}
		}else if ( window.matchMedia( "( max-width: 1024px )" ).matches ) {

			if( $( '.elementor-element-' + id ).hasClass('uael-nav-menu__breakpoint-tablet') ) {

				nav_element.addClass('uael-dropdown');
				if (('cta' == last_item || 'cta' == last_item_flyout) && 'expandible' != layout) {
					cta_classes._removeClassesCta();
				}
			} else {

				nav_element.removeClass('uael-dropdown');
				if (('cta' == last_item || 'cta' == last_item_flyout) && 'expandible' != layout) {
					cta_classes._addClassesCta();
				}
			}
		} else {
			if (('cta' == last_item || 'cta' == last_item_flyout) && 'expandible' != layout) {
				cta_classes._addClassesCta();
			}
		}
	}

	function _toggleClick( id ){

		var nav_toggle = $( '.elementor-element-' + id + ' .uael-nav-menu__toggle' );
		var nav_toggle_next = nav_toggle.next();
		var element = $( '.elementor-element-' + id );

		if ( nav_toggle.hasClass( 'uael-active-menu-full-width' ) ) {

			nav_toggle_next.css( 'left', '0' );

			var width = element.closest('.elementor-section').outerWidth();
			var sec_pos = element.closest('.elementor-section').offset().left - nav_toggle_next.offset().left;

			nav_toggle_next.css( 'width', width + 'px' );
			nav_toggle_next.css( 'left', sec_pos + 'px' );
		}

		nav_toggle.off( 'click keyup' ).on( 'click keyup', function( event ) {

			var $this = $( this );
			var $selector = $this.next();
			var $element = $( '.elementor-element-' + id );
			var $nav_element = $( '.elementor-element-' + id + ' nav' );

			if ( $this.hasClass( 'uael-active-menu' ) ) {

				var full_width = $selector.data( 'full-width' );
				var toggle_icon = $nav_element.data( 'toggle-icon' );

				$element.find( '.uael-nav-menu-icon' ).html( toggle_icon );

				$this.removeClass( 'uael-active-menu' );
				$this.attr( 'aria-expanded', 'false' );

				if ( 'yes' == full_width ){

					$this.removeClass( 'uael-active-menu-full-width' );

					$selector.css( 'width', 'auto' );
					$selector.css( 'left', '0' );
					$selector.css( 'z-index', '0' );
				}
			} else {

				var full_width = $selector.data( 'full-width' );
				var close_icon = $nav_element.data( 'close-icon' );

				$element.find( '.uael-nav-menu-icon' ).html( close_icon );

				$this.addClass( 'uael-active-menu' );
				$this.attr( 'aria-expanded', 'true' );

				if ( 'yes' == full_width ){

					$this.addClass( 'uael-active-menu-full-width' );
					var $element_section = $element.closest('.elementor-section');
					if (0 == $element_section.length){
						$element_section = $element.closest('.e-con');
					}

					var width = $element_section.outerWidth();
					var sec_pos = $element_section.offset().left - $selector.offset().left;

					$selector.css( 'width', width + 'px' );
					$selector.css( 'left', sec_pos + 'px' );
					$selector.css( 'z-index', '9999' );
				}
			}

			if( $nav_element.hasClass( 'menu-is-active' ) ) {
				$nav_element.removeClass( 'menu-is-active' );
			}else {
				$nav_element.addClass( 'menu-is-active' );
			}
		} );
	}

	function _handleSinglePageMenu( id, layout ) {
		$( '.elementor-element-' + id + ' ul.uael-nav-menu li a' ).on(
			'click',
			function () {
				var $this      = $( this );
				var link       = $this.attr( 'href' );
				var linkValue  = '';
				var menuToggle = $( '.elementor-element-' + id + ' .uael-nav-menu__toggle' );
				var subToggle  = $( '.elementor-element-' + id + ' .uael-menu-toggle' )
				if ( link.includes( '#' ) ) {
					var index = link.indexOf( '#' );
					linkValue = link.slice( index + 1 );
				}
				if ( linkValue.length > 0 ) {
					if ( 'expandible' == layout ) {
						menuToggle.trigger( "click" );
						if ($this.hasClass( 'uael-sub-menu-item' )) {
							subToggle.trigger( "click" );
						}
					} else {
						if ( window.matchMedia( '(max-width: 1024px)' ).matches && ( 'horizontal' == layout || 'vertical' == layout ) ) {
							menuToggle.trigger( "click" );
							if ($this.hasClass( 'uael-sub-menu-item' )) {
								subToggle.trigger( "click" );
							}
						} else {
							if ($this.hasClass( 'uael-sub-menu-item' )) {
								_closeMenu( id );
								subToggle.trigger( "click" );
							}
							_closeMenu( id );
						}
					}
				}
			}
		);
	}

	$( document ).on( 'uael_nav_menu_init', function( e, id ){

		_sizeCal( id );
	});

	$( window ).on( 'elementor/frontend/init', function () {

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-nav-menu.default', WidgetUAELNavMenuHandler );

	});

} )( jQuery );
