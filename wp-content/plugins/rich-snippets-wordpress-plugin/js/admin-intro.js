var rich_snippets_admin_intro;

(
		function () {
			'use strict';

			rich_snippets_admin_intro = function () {
				this.code_field         = null;
				this.message_div        = null;
				this.verify_button      = null;
				this.privacy_aggreement = null;

				this.init = function () {
					var self = this;

					this.code_field         = jQuery( '.wpb-rs-main-cc-code' );
					this.message_div        = jQuery( '.wpb-rs-intro-tab-activation-messages' );
					this.verify_button      = jQuery( '.wpb-rs-activation-button' );
					this.privacy_aggreement = jQuery( '.wpb-rs-privacy-agree' );

					this.code_field.on( 'keyup focusout', function () {
						self.toggle_verify_button();
					} );

					this.privacy_aggreement.on( 'click', function () {
						self.toggle_verify_button();
					} );

					this.toggle_verify_button();

					this.verify_button.on( 'click', function ( e ) {
						e.preventDefault();
						self.verify();
					} );

				};

				this.on_error = function ( jqXHR, text_status, thrown_error ) {

					window.console.error( text_status, thrown_error, jqXHR );

					if ( jqXHR && jqXHR.hasOwnProperty( 'responseJSON' ) && jqXHR.responseJSON.hasOwnProperty( 'message' ) ) {
						this.message_div.html( '<div class="notice notice-error"><p>' + jqXHR.responseJSON.message + '</p></div>' );
					} else {
						this.message_div.html( '<div class="notice notice-error"><p>' + text_status + '</p></div>' );
					}

					setTimeout( function () {
						self.message_div.find( '.notice' ).hide( 1000, function () {
							jQuery( this ).remove();
						} );
					}, 15000 );

				};

				this.verify = function () {
					var self = this;

					if ( self.verify_button.hasClass( 'disabled' ) ) {
						return false;
					}

					var purchase_code = this.code_field.val();

					jQuery.ajax( {
						'url'       : WPB_RS_ADMIN.rest_url + '/admin/verify/',
						'dataType'  : 'json',
						'beforeSend': function ( xhr ) {
							xhr.setRequestHeader( 'X-WP-Nonce', WPB_RS_ADMIN.nonce );
							self.verify_button.addClass( 'installing' );
						},
						'data'      : {
							'purchase_code': purchase_code
						},
						'method'    : 'GET'
					} ).fail( function ( jqXHR, text_status, thrown_error ) {
						self.on_error( jqXHR, text_status, thrown_error );
						self.verify_button.removeClass( 'installing' );
					} ).done( function ( response, textStatus, jqXHR ) {

						if ( !response ) {
							jqXHR.responseJSON = {
								'message': WPB_RS_ADMIN.translations.activation_no_content_err.replace( '%d', '1' )
							};
							on_error( jqXHR, textStatus, '' );
							return false;
						}

						if ( !response.hasOwnProperty( 'verified' ) ) {
							jqXHR.responseJSON = {
								'message': WPB_RS_ADMIN.translations.activation_no_content_err.replace( '%d', '2' )
							};
							on_error( jqXHR, textStatus, '' );
							return false;
						}

						self.message_div.html( '<div class="notice notice-success"><p>' + WPB_RS_ADMIN.translations.activated + '</p></div>' ).show();

						jQuery( '.wpb-rss-not-active-info' ).hide();

						confetti();

						setTimeout( function () {
							window.location = WPB_RS_ADMIN.redirect_url;
						}, 3000 );

					} );
				};

				this.toggle_verify_button = function () {
					if ( '' === this.code_field.val() || !this.privacy_aggreement.prop( 'checked' ) ) {
						if ( !this.verify_button.hasClass( 'disabled' ) ) {
							this.verify_button.addClass( 'disabled' );
						}
					} else {
						if ( this.verify_button.hasClass( 'disabled' ) ) {
							this.verify_button.removeClass( 'disabled' );
						}
					}
				};
			};

			jQuery( document ).ready( function () {
				new rich_snippets_admin_intro().init();
			} );

		}
)();



