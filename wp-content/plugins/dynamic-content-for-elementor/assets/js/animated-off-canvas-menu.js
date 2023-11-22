(
	function ( $ ) {
		var WidgetElements_AnimatedOffcanvasMenu = function ( $scope,$ ) {
			var elementSettings = dceGetElementSettings( $scope );
			var id_scope = $scope.attr( 'data-id' );
			var animatedoffcanvasmenu = '#animatedoffcanvasmenu-' + id_scope;
			var menu_position = elementSettings.aocm_position;
			var class_menu_li = animatedoffcanvasmenu + ' .dce-menu-aocm ul#dce-ul-menu > li';
			var class_template_before = animatedoffcanvasmenu + ' .dce-template-after';
			var class_hamburger = '.dce-button-hamburger';
			var class_modal = animatedoffcanvasmenu + ' .dce-menu-aocm';
			var class_sidebg = animatedoffcanvasmenu + ' .dce-bg';
			var class_quit = animatedoffcanvasmenu + ' .dce-menu-aocm .dce-close';
			var items_menu = $scope.find( class_menu_li + ', ' + class_template_before );
			var rate_menuside_desktop = Number( elementSettings.animatedoffcanvasmenu_rate.size );
			var rate_menuside_tablet = Number( elementSettings.animatedoffcanvasmenu_rate_tablet.size );
			var rate_menuside_mobile = Number( elementSettings.animatedoffcanvasmenu_rate_mobile.size );
			var rate_menuside = rate_menuside_desktop;
			var side_background = elementSettings.side_background;

			if( side_background ) {
				var time_side_background_opening = Number( elementSettings.time_side_background_opening.size ) / 1000;
			}
			var time_menu_pane_opening = Number( elementSettings.time_menu_pane_opening.size ) / 1000;
			var time_menu_list_opening = Number( elementSettings.time_menu_list_opening.size ) / 1000;
			var time_menu_list_stagger = Number( elementSettings.time_menu_list_stagger.size ) / 1000;

			var deviceMode = $( 'body' ).attr( 'data-elementor-device-mode' );

			if ( deviceMode == 'tablet' && rate_menuside_tablet ) {
				rate_menuside = rate_menuside_tablet;
			} else if ( deviceMode == 'mobile' && rate_menuside_mobile ) {
				rate_menuside = rate_menuside_mobile;
			}

			var closeMenu = function () {
				timescale = 10;
				tl.reversed( ! tl.reversed() );
				$( class_quit ).fadeOut();
				$( class_hamburger ).find( '.con' ).removeClass( 'actived' ).removeClass( 'open' );

				if ( ! elementorFrontend.isEditMode() ) {
					$( 'body,html' ).removeClass( 'dce-off-canvas-menu-open' );
				}
			};
			// GSAP animations Timeline
			var tl = new gsap.timeline( { paused:true } );
			tl.set( class_modal,{
				width:0,
			} );
			if( side_background ) {
				if ( $( animatedoffcanvasmenu ).find( 'dce-bg' ) ) {
					if ( menu_position == 'right' ) {
						tl.set( class_sidebg,{
							right:rate_menuside + '%',
						} );
					} else {
						tl.set( class_sidebg,{
							left:rate_menuside + '%',
						} );
					}
				}
			}
			tl.to(
				class_modal,
				{
					duration:time_menu_pane_opening,
					width:rate_menuside + '%',
					ease:Expo.easeOut,
					delay:0
				}
			);
			if( side_background ) {
				if ( $( animatedoffcanvasmenu ).find( 'dce-bg' ) ) {
					tl.to(
						class_sidebg,
						{
							duration:time_side_background_opening,
							width:( 100 - rate_menuside ) + '%',
							ease:Expo.easeInOut,
							delay:0
						}
					);
				}
			}
			tl.from(
				items_menu,
				{
					y:'12%',
					opacity:0,
					ease:Expo.easeOut,
					stagger: time_menu_list_stagger,
					duration: time_menu_list_opening,
				},
				0.1
			);

			tl.to(
				class_quit,
				{
					duration: time_menu_pane_opening,
					scale:1,
					ease:Expo.easeInOut,
					delay:0
				},
				0
			);

			tl.reverse();

			// Events
			$scope.on( "click", class_hamburger, function ( e ) {
				e.preventDefault();
				$( class_quit ).fadeIn( time_menu_pane_opening, function () {
					$( this ).removeClass( "close-hidden" );
				} );
				tl.reversed( ! tl.reversed() );
				$( this ).find( '.con' ).toggleClass( 'actived' );

				if ( ! elementorFrontend.isEditMode() ) {
					$( 'body, html' ).addClass( 'dce-off-canvas-menu-open' );
				}
				return false;
			} );

			$( animatedoffcanvasmenu ).on( "click",'a:not(.dce-close):not(.no-link)',function ( e ) {
				closeMenu();
			} );

			$( document ).on( "click", class_quit, function ( e ) {
				e.preventDefault();
				closeMenu();
				$( class_quit ).fadeOut( time_menu_pane_opening,function () {
					$( this ).addClass( "close-hidden" );
				} );

				return false;
			} );
			$( document ).on( 'keyup',function ( e ) {
				if ( e.keyCode == 27 && $( ".dce-off-canvas-menu-open" ).length ) {
					closeMenu();
					$( class_quit ).fadeOut( time_menu_pane_opening,function () {
						$( this ).addClass( "close-hidden" );
					} );
				}
			} );

			if ( ! side_background ) {
				// Close the menu on click outside
				$(document).click(function(e) {
					if( ! $('body').hasClass( 'dce-off-canvas-menu-open' ) ) {
						return;
					}
					var $target = $(e.target);
					if( !$target.closest( animatedoffcanvasmenu ).length ) {
						e.preventDefault();
						closeMenu();
						$( class_quit ).fadeOut( time_menu_pane_opening,function () {
							$( this ).addClass( "close-hidden" );
						} );
					}
				});
			}

			$( '.animatedoffcanvasmenu ul > li.menu-item-has-children > .menu-item-wrap' ).append( '<span class="indicator-child no-transition">+</span>' );
			$( '.animatedoffcanvasmenu ul li span a:not([href])' ).addClass( "no-link no-transition" );
			$( '.animatedoffcanvasmenu ul li span a[href="#"]' ).addClass( "no-link no-transition" );

			// Accordion Menu
			$( '.animatedoffcanvasmenu ul > li.menu-item-has-children > .menu-item-wrap .indicator-child' ).click( function ( e ) {
				e.preventDefault();
				$( this ).closest( 'li' ).find( '> .sub-menu' ).not( ':animated' ).slideToggle();
			} );

			// Menu Items without href or with #
			$( '.animatedoffcanvasmenu ul li span a.no-link' ).click( function ( e ) {
				e.preventDefault();
				$( this ).closest( 'li' ).find( '> .sub-menu' ).not( ':animated' ).slideToggle();
			} );


			if ( ! elementorFrontend.isEditMode() ) {
				$( animatedoffcanvasmenu ).prependTo( "body" );
			}
		};

		$( window ).on( 'elementor/frontend/init',function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/dce-animatedoffcanvasmenu.default', WidgetElements_AnimatedOffcanvasMenu );
		} );
	}
)( jQuery );
