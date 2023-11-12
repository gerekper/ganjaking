(function ($) {

	var BSFExtensionInstaller = {

		init: function () {
			$(document).on('click', '.bsf-install-button', BSFExtensionInstaller._installNow);
			$( document ).on('wp-plugin-installing'      , BSFExtensionInstaller._pluginInstalling);
			$( document ).on('wp-plugin-install-error'   , BSFExtensionInstaller._installError);
			$( document ).on('wp-plugin-install-success' , BSFExtensionInstaller._installSuccess);
		},

		/**
		 * Install Now
		 */
		_installNow: function (event) {
			event.preventDefault();

			var $button = jQuery(event.target),
				$document = jQuery(document);

			if ( $button.hasClass('updating-message') || $button.hasClass('button-disabled') ) {
				return;
			}

			if (wp.updates.shouldRequestFilesystemCredentials && !wp.updates.ajaxLocked) {
				wp.updates.requestFilesystemCredentials(event);

				$document.on('credential-modal-cancel', function () {
					var $message = $('.install-now.updating-message');

					$message
						.removeClass('updating-message')
						.text(wp.updates.l10n.installNow);

					wp.a11y.speak(wp.updates.l10n.updateCancel, 'polite');
				});
			}

			wp.updates.installPlugin({
				slug: $button.data('slug')
			});
		},

		/**
		 * Install Success
		 */
		_installSuccess: function( event, response ) {

			event.preventDefault();

			// Transform the 'Install' button into an 'Activate' button.
			var $init = $( '.bsf-extension-' + response.slug ).data('init');
			var nonce = $( '#bsf_activate_extension_nonce' ).val();

			// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
			setTimeout( function() {

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						'action'   : 'bsf-extention-activate',
						'init'     : $init,
						'security' : nonce
					},
				})
				.done(function (result) {

					if( result.success ) {
						$ext = $('.bsf-extension-' + response.slug );
						$ext.addClass('bsf-plugin-installed');
						$ext.find('.bsf-install-button').addClass('bsf-plugin-installed-button').html('Installed <i class="dashicons dashicons-yes"></i>');
						$ext.find('.bsf-extension-start-install').removeClass('show-install');

					}
				});

			}, 1200 );

		},

		/**
		 * Plugin Installation Error.
		 */
		_installError: function( event, response ) {
			var $card = $( '.bsf-extension-' + response.slug + ' .bsf-extension-start-install-content')
			$card.html( '<h2>' + response.errorMessage + '</h2>' );
		},

		/**
		 * Installing Plugin
		 */
		_pluginInstalling: function(event, args) {
			event.preventDefault();
			$('.bsf-extension-' + args.slug + ' .bsf-extension-start-install').addClass('show-install');
		},
	}

    /**
	 * Initialize BSFExtensionInstaller
	 */
	$(function () {
		BSFExtensionInstaller.init();
	});
})(jQuery);