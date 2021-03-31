jQuery( document ).ready( function( $ ) {


	$.JiltPromotions = {}


	/**
	 * Jilt Plugin Installation modal handler.
	 *
	 * @since 1.1.0
	 */
	$.JiltPromotions.InstallPluginModal = class InstallPluginModal {


		/**
		 * Constructor.
		 *
		 * @since 1.1.0
		 *
		 * @param {object} options
		 */
		constructor( options ) {

			this.options = {
				messageID : options.messageID || '',
				target    : options.target    || '',
				onClose   : options.onClose   || '',
			};

			if ( '' === this.options.messageID ) {
				console.log( 'InstallPluginModal: missing messageID' )
				return;
			}

			if ( '' === this.options.target ) {
				this.options.target = 'sv-wc-jilt-promotions-' + this.options.messageID + '-modal';
			}

			this.initialize();
			this.open();
		}


		/**
		 * Initializes the modal.
		 *
		 * @since 1.1.0
		 */
		initialize() {

			// remove any existing install modal event handlers
			$( document ).off( 'click.jilt-install-modal' );
			$( document ).off( 'wc_backbone_modal_removed.jilt-install-modal' );

			// when the install button is clicked
			$( document ).on( 'click.jilt-install-modal', '#sv-wc-jilt-install-button-install', ( event ) => {
				this.onInstall( event );
			} );

			// when the newly opened modal is closed
			$( document.body ).on( 'wc_backbone_modal_removed.jilt-install-modal', ( event, target ) => {

				if ( target === this.options.target ) {
					this.onClose( event );
				}
			} );
		}


		/**
		 * Opens the modal.
		 *
		 * @since 1.1.0
		 */
		open() {

			// ensures there are no other modals opened
			$( '#wc-backbone-modal-dialog .modal-close' ).trigger( 'click' );

			new $.WCBackboneModal.View( {
				target: this.options.target,
			} );
		}


		/**
		 * Fires when the user clicks on the install button from the modal prompt.
		 *
		 * @since 1.1.0
		 *
		 * @param {_Event} event install click event
		 */
		onInstall( event ) {

			event.preventDefault();

			$( '#sv-wc-jilt-install-modal .wc-backbone-modal-content' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );

			$.post(

				ajaxurl,
				{
					action:   'sv_wc_jilt_install_jilt',
					nonce:     window.sv_wc_jilt_prompt_install.nonces.install_plugin,
					prompt_id: this.options.messageID,
				}

			).then( function( response ) {

				if ( response.success && response.data.redirect_url ) {

					window.location = response.data.redirect_url;

				} else {

					console.error( response );

					$( '#sv-wc-jilt-install-modal article' ).html( sv_wc_jilt_prompt_install.i18n.install_error );

					$( '#sv-wc-jilt-install-button-install' ).hide();
				}

			} ).fail( function() {

				$( '#sv-wc-jilt-install-modal article' ).html( sv_wc_jilt_prompt_install.i18n.install_error );

				$( '#sv-wc-jilt-install-button-install' ).hide();

			} ).always( function() {

				$( '#sv-wc-jilt-install-modal .wc-backbone-modal-content' ).unblock();
			} );
		}


		/**
		 * Fires when the user closes the install prompt modal.
		 *
		 * @since 1.1.0
		 *
		 * @param {_Event} event modal close event
		 */
		onClose( event ) {

			event.preventDefault();

			if ( this.options.onClose ) {
				$( document ).trigger( this.options.onClose )
			}

			return true;
		}


	}


	/**
	 * Messages handler.
	 *
	 * @since 1.1.0
	 */
	$.JiltPromotions.Messages = class Messages {


		/**
		 * Enables a message in AJAX.
		 *
		 * @since 1.1.0
		 *
		 * @param {string} messageID message identifier
		 */
		static enableMessage( messageID ) {
			Messages.handleMessage( messageID, 'sv_wc_jilt_enable_message', window.sv_wc_jilt_prompt_install.nonces.enable_message )
		}


		/**
		 * Dismisses a message in AJAX.
		 *
		 * @since 1.1.0
		 *
		 * @param {string} messageID message identifier
		 */
		static dismissMessage( messageID ) {
			Messages.handleMessage( messageID, 'sv_wc_jilt_dismiss_message', window.sv_wc_jilt_prompt_install.nonces.dismiss_message )
		}


		/**
		 * Handles a message status (helper method).
		 *
		 * @since 1.1.0
		 *
		 * @param {string} messageID message identifier
		 * @param {string} action AJAX action
		 * @param {string} nonce security key
		 */
		static handleMessage( messageID, action, nonce ) {

			var data = {
				action:     action,
				nonce:      nonce,
				message_id: messageID,
			}

			$.post( ajaxurl, data, function( response ) {
				if ( ! response.success ) {
					console.error( response );
				}
			} );
		}


	}


	/**
	 * Notice handler
	 */
	$.JiltPromotions.Notice = class Notice {


		/**
		 * Constructor.
		 *
		 * @since 1.1.0
		 *
		 * @param {jQuery} $element a jQuery element that represents a notice
		 */
		constructor( $element ) {

			this.$element = $element;
			this.messageID = $element.data( 'message-id' );

			if ( ! this.messageID ) {
				console.log( 'Notice: missing data-message-id attribute' );
				return;
			}

			this.initialize();
		}


		/**
		 * Initializes the notice
		 *
		 * @since 1.1.0
		 */
		initialize() {

			// remove all existing promotional notice event handlers
			this.$element.off( 'click.jilt-promotional-notice' );

			this.$element.on( 'click.jilt-promotional-notice', '.sv-wc-jilt-prompt-primary-action', ( event ) =>  {
				this.onInstall( event );
			} );

			this.$element.on( 'click.jilt-promotional-notice', '.notice-dismiss', ( event ) => {
				this.onDismiss( event );
			} );
		}


		/**
		 * Fires when the user clicks on the primary action for the notice.
		 *
		 * @since 1.1.0
		 *
		 * @param {_Event} event click event
		 */
		onInstall( event ) {

			event.preventDefault();

			new $.JiltPromotions.InstallPluginModal( {
				messageID: this.messageID,
				target: 'sv-wc-jilt-promotions-install-plugin-modal',
			} );
		}


		/**
		 * Fires when the user dismisses the notice.
		 *
		 * @since 1.1.0
		 *
		 * @param {_Event} event click event
		 */
		onDismiss( event ) {

			$.JiltPromotions.Messages.dismissMessage( this.messageID );
		}


	}


	// initializes all promotional notices on the page
	$( '.sv-wc-jilt-promotional-notice' ).each( function() {
		new jQuery.JiltPromotions.Notice( $( this ) );
	} );


} );
