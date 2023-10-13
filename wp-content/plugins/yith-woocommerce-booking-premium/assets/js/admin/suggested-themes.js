/* global wcbk_admin, adminpage */
jQuery( function ( $ ) {
	"use strict";

	var $document = $( document );

	var installThemeSuccess       = function ( response ) {
			var $themeRow = $( '.theme[data-slug=' + response.slug + ']' ),
				$message;

			$message = $themeRow.find( '.theme__action' )
				.removeClass( 'updating-message' )
				.addClass( 'updated-message disabled' )
				.text( wcbk_admin.i18n.themeInstalled );

			setTimeout( function () {
				if ( response.activateUrl ) {
					// Transform the 'Install' button into an 'Activate' button.
					$message.removeClass( 'theme-install updated-message disabled' )
					if ( response.activateUrl.search( 'action=enable' ) ) {
						$message
							.addClass( 'theme-network-enable' )
							.text( wcbk_admin.i18n.themeNetworkEnable );
					} else {
						$message
							.attr( 'href', response.activateUrl )
							.addClass( 'activate' )
							.text( wcbk_admin.i18n.themeActivate );
					}
				}
			}, 2000 );
		},
		installThemeError         = function ( response ) {
			var $themeRow = $( '.theme[data-slug=' + response.slug + ']' ),
				$message;

			$message = $themeRow.find( '.theme__action' )
				.removeClass( 'updating-message' )
				.addClass( 'error disabled' )
				.text( wcbk_admin.i18n.themeInstallationFailed );

			setTimeout( function () {
				$message
					.removeClass( 'error disabled' )
					.text( wcbk_admin.i18n.themeInstall );

			}, 2000 );
		},
		installThemeHandler       = function ( e ) {
			var $message = $( e.target ),
				name     = $message.data( 'name' ),
				slug     = $message.data( 'slug' );

			e.preventDefault();

			if ( $message.is( 'disabled' ) ) {
				return;
			}

			$message
				.addClass( 'updating-message' )
				.text( wcbk_admin.i18n.themeInstalling );

			wp.updates.ajax(
				'install-theme',
				{
					slug   : slug,
					success: installThemeSuccess,
					error  : installThemeError
				}
			)
		},
		networkEnableThemeHandler = function ( e ) {
			var $message = $( e.target ),
				name     = $message.data( 'name' ),
				slug     = $message.data( 'slug' );

			e.preventDefault();

			if ( $message.is( 'disabled' ) ) {
				return;
			}

			$message
				.addClass( 'updating-message' )
				.text( wcbk_admin.i18n.themeNetworkEnabling );

			var data = {
				type    : 'network-enable',
				action  : 'yith_wcbk_theme_action',
				security: wcbk_admin.nonces.themeAction,
				slug    : slug,
				name    : name
			};

			$.ajax(
				{
					type   : "POST",
					data   : data,
					url    : ajaxurl,
					success: function ( response ) {
						$message.removeClass( 'theme-network-enable updating-message' );
						if ( response.success ) {
							$message
								.addClass( 'updated-message disabled' )
								.text( wcbk_admin.i18n.themeNetworkEnabled );

							if ( response.activateUrl ) {
								setTimeout( function () {
									$message
										.attr( 'href', response.activateUrl )
										.removeClass( 'updated-message disabled' )
										.addClass( 'activate' )
										.text( wcbk_admin.i18n.themeActivate );

								}, 2000 );
							}
						} else {
							$message
								.addClass( 'error disabled' )
								.text( response.error );
						}
					}
				}
			);
		};

	$document.on( 'click', '.yith-wcbk-suggested-themes .theme-install', installThemeHandler )
	$document.on( 'click', '.yith-wcbk-suggested-themes .theme-network-enable', networkEnableThemeHandler )

} );