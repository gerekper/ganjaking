/* global jQuery, wcbk_admin, bk, ajaxurl */
( function ( $ ) {
	var blockParams     = bk.blockParams,
		reloadFragments = function () {
			$.ajax( {
						type   : "GET",
						url    : document.location.href,
						success: function ( response ) {
							if ( response ) {
								var tabWrapper    = $( 'h2.nav-tab-wrapper, .yith-plugin-fw__panel__menu__wrapper' ).first(),
									newTabWrapper = $( response ).find( 'h2.nav-tab-wrapper, .yith-plugin-fw__panel__menu__wrapper' ).first();

								if ( tabWrapper.length && newTabWrapper.length ) {
									var opened = tabWrapper.find( '.yith-plugin-fw__panel__menu-item.yith-plugin-fw--open' );

									if ( opened.length ) {
										opened.each( function () {
											var itemId  = $( this ).attr( 'id' ),
												newItem = newTabWrapper.find( '#' + itemId );

											newItem.addClass( 'yith-plugin-fw--open' );

											newItem.find( '.yith-plugin-fw__panel__submenu' ).show();
										} );
									}

									tabWrapper.html( newTabWrapper.html() );
									return;
								}
							}
							// If something goes wrong, reload the page.
							window.location.reload();
						}
					}
			);
		};


	$( '.yith-wcbk-modules .module' ).on( 'change', '.module__active-toggle .on_off', function () {
		var checkbox      = $( this ),
			moduleWrapper = checkbox.closest( '.module' ),
			moduleKey     = moduleWrapper.data( 'module' ),
			needsReload   = !!moduleWrapper.data( 'needsReload' ),
			data          = {
				module  : moduleKey,
				action  : 'yith_wcbk_modules_action',
				request : 'switch_module_activation',
				context : 'admin',
				security: wcbk_admin.nonces.modulesAction,
				active  : checkbox.is( ':checked' ) ? 'yes' : 'no'
			};

		moduleWrapper.block( blockParams );

		var onFailure = function ( response ) {
			console.log( response );
		};

		$.ajax( {
					type    : "POST",
					data    : data,
					url     : ajaxurl,
					success : function ( response ) {
						if ( response.success && needsReload ) {
							reloadFragments();
						}

						if ( !response.success ) {
							onFailure();
						}
					},
					error   : function ( response ) {
						onFailure( response );
					},
					complete: function () {
						moduleWrapper.unblock();
					}
				} );
	} );

} )( jQuery );