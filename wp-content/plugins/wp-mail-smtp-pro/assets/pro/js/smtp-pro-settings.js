/* eslint-disable no-prototype-builtins */
/* global wp_mail_smtp_pro, ajaxurl */
'use strict';

var WPMailSMTP = window.WPMailSMTP || {};
WPMailSMTP.Admin = WPMailSMTP.Admin || {};
WPMailSMTP.Admin.Settings = WPMailSMTP.Admin.Settings || {};

/**
 * WP Mail SMTP Admin area module.
 *
 * @since 1.5.0
 */
WPMailSMTP.Admin.Settings.Pro = WPMailSMTP.Admin.Settings.Pro || ( function( document, window, $ ) {

	/**
	 * Private functions and properties.
	 *
	 * @since 1.5.0
	 *
	 * @type {object}
	 */
	var __private = {

		/**
		 * Whether the email is valid.
		 *
		 * @since 1.5.0
		 *
		 * @param {string} email Email address.
		 *
		 * @returns {boolean} Whether email is valid or not.
		 */
		isEmailValid: function( email ) {
			var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test( String( email ).toLowerCase() );
		},
	};

	/**
	 * Public functions and properties.
	 *
	 * @since 1.5.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Flag variable if AJAX request is being processed.
		 *
		 * @since 2.4.0
		 */
		doingAjax: false,

		/**
		 * Start the engine. DOM is not ready yet, use only to init something.
		 *
		 * @since 1.5.0
		 */
		init: function() {

			// Do that when DOM is ready.
			$( app.ready );
		},

		/**
		 * DOM is fully loaded.
		 *
		 * @since 1.5.0
		 */
		ready: function() {

			app.pageHolder = $( '.wp-mail-smtp-tab-settings' );

			app.bindActions();
		},

		/**
		 * Process all generic actions/events, mostly custom that were fired by our API.
		 *
		 * @since 1.5.0
		 */
		bindActions: function() {

			app.license.bindActions();
			app.amazonses.bindActions();
			app.amazonses.loadIdentities();
			app.multisite.bindActions();
		},

		/**
		 * License management.
		 *
		 * @since 1.5.0
		 *
		 * @type {object}
		 */
		license: {

			/**
			 * Generate a notice about performed action.
			 *
			 * @since 1.5.0
			 *
			 * @param {string} noticeType CSS class that represents the notice type.
			 * @param {string} message    Message to display to a user.
			 *
			 * @returns {string} Process HTML ready to be inserted into DOM.
			 */
			getNoticeHtml: function( noticeType, message ) {
				return '<div class="notice ' + noticeType + ' wp-mail-smtp-license-notice is-dismissible"><p>' + message + '</p>';
			},

			/**
			 * Process all license-related actions/events.
			 *
			 * @since 1.5.0
			 */
			bindActions: function() {

				app.pageHolder.on( 'keydown', '#wp-mail-smtp-setting-license-key', this.inputEnter );
				app.pageHolder.on( 'click', '#wp-mail-smtp-setting-license-key-verify', this.verify );
				app.pageHolder.on( 'click', '#wp-mail-smtp-setting-license-key-deactivate', this.deactivate );
				app.pageHolder.on( 'click', '#wp-mail-smtp-setting-license-key-refresh', this.refresh );
			},

			/**
			 * Verify a license key. Ajaxified.
			 *
			 * @since 1.5.0
			 *
			 * @param {object} event jQuery event.
			 */
			verify: function( event ) {

				event.preventDefault();

				var $btn = jQuery( event.target ),
					$row = $btn.closest( '.wp-mail-smtp-setting-row' ),
					$licenseKey = $( '#wp-mail-smtp-setting-license-key', $row ),
					data = {
						action: 'wp_mail_smtp_pro_license_ajax',
						task: 'license_verify',
						nonce: $( '#wp-mail-smtp-setting-license-nonce', $row ).val(),
						license: $licenseKey.val()
					};

				$btn.prop( 'disabled', true );

				$.post( ajaxurl, data, function( response ) {

					var message,
						icon,
						type;

					if ( response.success ) {
						message = response.data.message;
						icon    = 'check-circle-solid-green';
						type    = 'green';

						$row.find( '.type, .desc, #wp-mail-smtp-setting-license-key-deactivate' ).show();
						$row.find( '.type strong' ).text( response.data.type );
						$licenseKey.prop( 'disabled', true );
					} else {
						message = response.data;
						icon    = 'exclamation-circle-regular-red';
						type    = 'red';

						$row.find( '.type, .desc, #wp-mail-smtp-setting-license-key-deactivate' ).hide();
						$licenseKey.prop( 'disabled', false );
					}

					app.license.displayModal( message, icon, type );

					$btn.prop( 'disabled', false );

				} ).fail( function( xhr ) {
					console.log( xhr.responseText );
				} );
			},

			/**
			 * Trigger license verification with enter key press in license key input.
			 *
			 * @since 2.4.0
			 *
			 * @param {object} event jQuery event.
			 */
			inputEnter: function( event ) {

				if ( event.keyCode === 13 ) {
					event.preventDefault();

					$( '#wp-mail-smtp-setting-license-key-verify' ).trigger( 'click' );
				}
			},

			/**
			 * Deactivate a license key. Ajaxified.
			 *
			 * @since 1.5.0
			 *
			 * @param {object} event jQuery event.
			 */
			deactivate: function( event ) {

				event.preventDefault();

				var $btn = jQuery( event.target ),
					$row = $btn.closest( '.wp-mail-smtp-setting-row' ),
					data = {
						action: 'wp_mail_smtp_pro_license_ajax',
						task: 'license_deactivate',
						nonce: $( '#wp-mail-smtp-setting-license-nonce', $row ).val()
					};

				$btn.prop( 'disabled', true );

				$.post( ajaxurl, data, function( response ) {

					var message = response.data,
						icon,
						type;

					if ( response.success ) {
						icon = 'check-circle-solid-green';
						type = 'green';

						$row.find( '#wp-mail-smtp-setting-license-key' ).val( '' );
						$row.find( '.type strong' ).text( 'lite' );
						$row.find( '.desc, #wp-mail-smtp-setting-license-key-deactivate' ).hide();
					} else {
						icon = 'exclamation-circle-regular-red';
						type = 'red';
					}

					$( '#wp-mail-smtp-setting-license-key', $row ).prop( 'disabled', false );

					app.license.displayModal( message, icon, type );

					$btn.prop( 'disabled', false );

				} ).fail( function( xhr ) {
					console.log( xhr.responseText );
				} );
			},

			/**
			 * Refresh a license key (get its type/status). Ajaxified.
			 *
			 * @since 1.5.0
			 *
			 * @param {object} event jQuery event.
			 */
			refresh: function( event ) {

				event.preventDefault();

				var $btn = jQuery( event.target ),
					$row = $btn.closest( '.wp-mail-smtp-setting-row' ),
					data = {
						action: 'wp_mail_smtp_pro_license_ajax',
						task: 'license_refresh',
						nonce: $( '#wp-mail-smtp-setting-license-nonce', $row ).val()
					};

				$btn.prop( 'disabled', true );

				$.post( ajaxurl, data, function( response ) {

					var message,
						icon,
						type;

					if ( response.success ) {
						message = response.data.message;
						icon    = 'check-circle-solid-green';
						type    = 'green';

						$row.find( '.type strong' ).text( response.data.type );
					} else {
						message = response.data;
						icon    = 'exclamation-circle-regular-red';
						type    = 'red';

						$row.find( '.desc, #wp-mail-smtp-setting-license-key-deactivate' ).hide();
						$( '#wp-mail-smtp-setting-license-key', $row ).prop( 'disabled', false );
					}

					app.license.displayModal( message, icon, type );

					$btn.prop( 'disabled', false );

				} ).fail( function( xhr ) {
					console.log( xhr.responseText );
				} );
			},

			/**
			 * Display the modal with provided text and icon.
			 *
			 * @since 2.1.0
			 *
			 * @param {string} message The message to be displayed in the modal.
			 * @param {string} icon    The icon name from /assets/images/font-awesome/ to be used in modal.
			 * @param {string} type    The type of the message (red, green, orange, blue, purple, dark).
			 */
			displayModal: function( message, icon, type ) {

				$.alert( {
					backgroundDismiss: true,
					escapeKey: true,
					animationBounce: 1,
					type: type,
					title: false,
					icon: '"></i><img src="' + wp_mail_smtp_pro.plugin_url + '/assets/images/font-awesome/' + icon + '.svg" style="width: 40px; height: 40px;" alt=""><i class="',
					content: message,
					buttons: {
						confirm: {
							text: wp_mail_smtp_pro.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ]
						}
					}
				} );
			}
		},

		/**
		 * AmazonSES specific methods.
		 *
		 * @since 1.5.0
		 *
		 * @type {object}
		 */
		amazonses: {

			/**
			 * Process all AmazonSES actions/events.
			 *
			 * @since 1.5.0
			 */
			bindActions: function() {

				$( document ).on( 'click', '.js-wp-mail-smtp-providers-amazonses-register-identity', this.processIdentityRegistration );
				$( document ).on( 'change', '.js-wp-mail-smtp-providers-amazonses-register-identity-radio-button', this.processIdentityTypeToggling );
				$( document ).on( 'click', '.js-wp-mail-smtp-providers-amazonses-txt-record button', this.processTxtCodeCopy );
				$( document ).on( 'blur', '#wp-mail-smtp-providers-amazonses-domain-input', function() {
					var $this = $( this );

					// Cleanup from the protocol, otherwise SES API will fail.
					$this.val(
						$this.val().replace( 'https://', '' ).replace( 'http://', '' )
					);
				} );
				$( document ).on( 'keydown', '#wp-mail-smtp-providers-amazonses-domain-input, #wp-mail-smtp-providers-amazonses-email-input', function( event ) {
					if ( event.which === 13 ) {
						$( '.js-wp-mail-smtp-providers-amazonses-register-identity' ).trigger( 'click' );
					}
				} );
				app.pageHolder.on( 'click', '.js-wp-mail-smtp-providers-amazonses-register-identity-modal-button', this.openRegisterIdentityModal );
				app.pageHolder.on( 'click', '.js-wp-mail-smtp-providers-amazonses-identity-delete', this.processIdentityDelete );
				app.pageHolder.on( 'click', '.js-wp-mail-smtp-providers-amazonses-email-resend', this.processEmailResend );
				app.pageHolder.on( 'click', '.js-wp-mail-smtp-providers-amazonses-domain-dns-record', this.displayDnsRecord );
				$( 'form', app.pageHolder ).on( 'submit', this.maybePreventSettingsSave );

				$( document ).on( 'focus', '.js-wp-mail-smtp-providers-amazonses-txt-record input', function() {
					$( this ).trigger( 'select' );
				} );
			},

			/**
			 * Load the SES identities setting on page load via AJAX.
			 *
			 * @since 2.4.0
			 */
			loadIdentities: function() {

				if ( $( '.js-wp-mail-smtp-setting-mailer-radio-input:checked' ).val() !== 'amazonses' ) {
					return;
				}

				var $identitiesWrapper = $( '.js-wp-mail-smtp-ses-identities-setting' );
				var nonce = $identitiesWrapper.siblings( 'input[name="wp_mail_smtp_pro_amazonses_load_ses_identities"]' ).val();

				if ( typeof nonce !== 'undefined' && nonce.length ) {
					$.ajax( {
						url: ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'wp_mail_smtp_pro_providers_ajax',
							task: 'load_ses_identities',
							mailer: 'amazonses',
							nonce: nonce
						},
						beforeSend: function() {
							app.doingAjax = true;
						}
					} )
						.done( function( response ) {
							if ( response.hasOwnProperty( 'success' ) && response.success ) {
								$identitiesWrapper.html( response.data );
							} else {
								$identitiesWrapper.html( '<p class="response response-error">' + wp_mail_smtp_pro.ses_text_no_identities + '</p>' );
							}
						} )
						.fail( function() {
							$identitiesWrapper.html( '<p class="response response-error">' + wp_mail_smtp_pro.ses_text_no_identities + '</p>' );
						} )
						.always( function() {
							app.doingAjax = false;
						} );
				}
			},

			/**
			 * Process the identity type radio button toggling.
			 *
			 * @since 2.4.0
			 */
			processIdentityTypeToggling: function() {

				var $formWrapper = $( this ).closest( '#wp-mail-smtp-providers-amazonses-register-identity' );
				var $domainInput = $formWrapper.find( '#wp-mail-smtp-providers-amazonses-domain-input' );
				var $domainDesc = $formWrapper.find( '#wp-mail-smtp-providers-amazonses-domain-desc' );
				var $emailInput = $formWrapper.find( '#wp-mail-smtp-providers-amazonses-email-input' );
				var $emailDesc = $formWrapper.find( '#wp-mail-smtp-providers-amazonses-email-desc' );

				if ( this.value === 'domain' ) {
					$domainInput.show();
					$domainDesc.show();
					$emailInput.hide();
					$emailDesc.hide();
				} else {
					$domainInput.hide();
					$domainDesc.hide();
					$emailInput.show();
					$emailDesc.show();
				}
			},

			/**
			 * Process the click on an Verify button.
			 *
			 * @since 2.4.0
			 *
			 * @param {object} event jQuery event.
			 *
			 * @returns {boolean} Whether identity registration processed or not.
			 */
			processIdentityRegistration: function( event ) {

				event.preventDefault();

				var $btn = $( event.target );
				var $formWrapper = $btn.closest( '#wp-mail-smtp-providers-amazonses-register-identity' );
				var $domainInput = $formWrapper.find( '#wp-mail-smtp-providers-amazonses-domain-input' );
				var $emailInput = $formWrapper.find( '#wp-mail-smtp-providers-amazonses-email-input' );
				var nonce = $formWrapper.find( 'input[name="wp_mail_smtp_pro_amazonses_register_identity"]' ).val();
				var type =  $formWrapper.find( 'input[name="identity-type"]:checked' ).val();
				var value = ( type === 'email' ) ? $emailInput.val() : $domainInput.val();

				if ( $btn.hasClass( 'disabled' ) ) {
					return false;
				}

				if ( type === 'email' && ! __private.isEmailValid( value ) ) {
					$formWrapper.find( 'p.response' ).remove();
					$formWrapper.append( '<p class="response error">' + wp_mail_smtp_pro.ses_text_email_invalid + '</p>' );

					return false;
				}

				if ( value.length && nonce.length ) {

					// Send ajax request.
					$.ajax(
						app.amazonses.getIdentityRegistrationRequestData(
							type,
							value,
							nonce,
							function() {
								$formWrapper.find( 'p.response' ).remove();
								$btn.html( wp_mail_smtp_pro.loader_white_small ).addClass( 'disabled with-loader' );
								app.doingAjax = true;
							}
						)
					)
						.done( function( response ) {
							if ( response.hasOwnProperty( 'success' ) && response.success ) {
								$domainInput.val( '' );
								$emailInput.val( '' );

								app.amazonses.loadIdentities();
							}

							$formWrapper.slideUp( 500, function() {
								$formWrapper.empty().append( response.data ).slideDown( 300, function() {
									$( '.js-wp-mail-smtp-btn-close' ).show();
								} );
							} );
						} )
						.fail( function() {
							$formWrapper.slideUp( 500, function() {
								$formWrapper.empty().append( '<p>' + wp_mail_smtp_pro.ses_text_smth_wrong + '</p>' ).slideDown( 300, function() {
									$( '.js-wp-mail-smtp-btn-close' ).show();
								} );
							} );
						} )
						.always( function() {
							$btn.removeClass( 'disabled' );
							app.doingAjax = false;
						} );
				}
			},

			/**
			 * Prepare the AJAX data for identity registration request.
			 *
			 * @since 2.4.0
			 *
			 * @param {string}   type       The type of identity: "email" or "domain".
			 * @param {string}   value      The value of the identity.
			 * @param {string}   nonce      The WP nonce for security.
			 * @param {Function} beforeSend The function to execute before the AJAX request is triggered.
			 *
			 * @returns {object} AJAX data object.
			 */
			getIdentityRegistrationRequestData: function( type, value, nonce, beforeSend ) {

				return {
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'wp_mail_smtp_pro_providers_ajax',
						task: 'identity_registration',
						mailer: 'amazonses',
						type: type,
						value: value,
						nonce: nonce
					},
					beforeSend: beforeSend
				};
			},

			/**
			 * Open the register identity modal window.
			 *
			 * @since 2.4.0
			 *
			 * @param {object} event jQuery event.
			 */
			openRegisterIdentityModal: function( event ) {

				event.preventDefault();

				$.alert( {
					backgroundDismiss: false,
					escapeKey: true,
					animationBounce: 1,
					closeIcon: true,
					type: 'blue',
					boxWidth: '550px',
					title: wp_mail_smtp_pro.ses_add_identity_modal_title,
					content: wp_mail_smtp_pro.ses_add_identity_modal_content,
					buttons: {
						cancel: {
							text: wp_mail_smtp_pro.ses_text_done,
							btnClass: 'js-wp-mail-smtp-btn-close btn-hide btn-confirm',
						}
					},
					onOpenBefore: function() {
						this.$body.addClass( 'wp-mail-smtp-providers-amazonses-identity-modal' );
						this.$body.addClass( 'wp-mail-smtp-providers-amazonses-register-identity-modal' );
					}
				} );
			},

			/**
			 * Process the click on an Delete link for emails or domains.
			 *
			 * @since 2.4.0
			 *
			 * @param {object} event jQuery event.
			 */
			processIdentityDelete: function( event ) {

				event.preventDefault();

				var $link = $( event.target ).closest( 'a' );
				var value = $link.data( 'identity' );
				var type = $link.data( 'type' );
				var nonce = $link.data( 'nonce' ).toString();

				app.amazonses.deleteIdentity( $link, type, value, nonce );
			},

			/**
			 * Open the delete identity confirm and process the deletion if user confirms.
			 *
			 * @since 2.4.0
			 *
			 * @param {object} $link jQuery object of the link that was clicked.
			 * @param {string} type  The type of the identity ("email" or "domain").
			 * @param {string} value The actual email address or domain name.
			 * @param {string} nonce The WP nonce for security.
			 */
			deleteIdentity: function( $link, type, value, nonce ) {

				$.confirm( {
					backgroundDismiss: false,
					escapeKey: true,
					animationBounce: 1,
					type: 'orange',
					boxWidth: '450px',
					icon: '"></i><img src="' + wp_mail_smtp_pro.plugin_url + '/assets/images/font-awesome/exclamation-circle-solid-orange.svg" style="width: 40px; height: 40px;" alt="' + wp_mail_smtp_pro.icon + '"><i class="',
					title: false,
					content: wp_mail_smtp_pro['ses_text_' + type + '_delete'],
					buttons: {
						confirm: {
							text: wp_mail_smtp_pro.ses_text_yes,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
							action: function() {
								app.amazonses.deleteIdentityAction( $link, type, value, nonce );
							}
						},
						cancel: {
							text: wp_mail_smtp_pro.ses_text_cancel,
							btnClass: 'btn-cancel',
						}
					}
				} );
			},

			/**
			 * The identity delete action.
			 * Verify passed data and process an AJAX request.
			 *
			 * @since 2.4.0
			 *
			 * @param {object} $link jQuery object of the link that was clicked.
			 * @param {string} type  The type of the identity ("email" or "domain").
			 * @param {string} value The actual email address or domain name.
			 * @param {string} nonce The WP nonce for security.
			 */
			deleteIdentityAction: function( $link, type, value, nonce ) {

				if ( value.length && nonce.length ) {

					// Send ajax request.
					$.ajax( {
						url: ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'wp_mail_smtp_pro_providers_ajax',
							task: 'identity_delete',
							mailer: 'amazonses',
							value: value,
							nonce: nonce,
						},
						beforeSend: function() {
							app.doingAjax = true;
						}
					} )
						.done( function( response ) {
							if ( response.hasOwnProperty( 'success' ) && response.success ) {
								$link.closest( 'tr' ).fadeOut( 'fast', function() {
									this.remove();
								} );
							} else {
								alert( response.data );
							}
						} )
						.fail( function() {
							alert( wp_mail_smtp_pro.ses_text_smth_wrong );
						} )
						.always( function() {
							app.doingAjax = false;
						} );
				}
			},

			/**
			 * Process the click on an Resend link.
			 *
			 * @since 1.5.0
			 * @since 2.4.0 AJAX request changes.
			 *
			 * @param {object} event jQuery event.
			 *
			 * @returns {boolean} Whether email was resent.
			 */
			processEmailResend: function( event ) {

				event.preventDefault();

				var $link = $( event.target ).closest( 'a' );
				var email = $link.data( 'email' );
				var nonce = $link.data( 'nonce' ).toString();

				if ( $link.hasClass( 'disabled' ) ) {
					return false;
				}

				if ( ! __private.isEmailValid( email ) ) {
					alert( wp_mail_smtp_pro.ses_text_smth_wrong );
					return false;
				}

				if ( email.length && nonce.length ) {

					// Send ajax request.
					$.ajax(
						app.amazonses.getIdentityRegistrationRequestData(
							'email',
							email,
							nonce,
							function() {
								$link.addClass( 'disabled' );
								$link.text( wp_mail_smtp_pro.ses_text_sending );
								app.doingAjax = true;
							}
						)
					)
						.done( function( response ) {
							if ( response.hasOwnProperty( 'success' ) && response.success ) {
								$link
									.html( '<span class="dashicons dashicons-yes"></span> ' + wp_mail_smtp_pro.ses_text_sent )
									.fadeOut( 1000, function() {
										$( this ).text( wp_mail_smtp_pro.ses_text_resend );
										$( this ).fadeIn( 'fast' );
									} );
							} else {
								$link
									.html( '<span class="dashicons dashicons-no"></span> ' + wp_mail_smtp_pro.ses_text_resend_failed )
									.addClass( 'error' );
							}
						} )
						.fail( function() {
							alert( wp_mail_smtp_pro.ses_text_smth_wrong );
						} )
						.always( function() {
							$link.removeClass( 'disabled' );
							app.doingAjax = false;
						} );
				}
			},

			/**
			 * Open a modal window and display DNS TXT record info.
			 *
			 * @since 2.4.0
			 *
			 * @param {object} event jQuery event.
			 */
			displayDnsRecord: function( event ) {

				event.preventDefault();

				var $link   = $( event.target ).closest( 'a' ),
					domain    = '_amazonses.' + $link.data( 'domain' ),
					txtRecord = $link.data( 'txt-record' );

				$.alert( {
					backgroundDismiss: true,
					escapeKey: true,
					animationBounce: 1,
					type: 'blue',
					boxWidth: '550px',
					title: wp_mail_smtp_pro.ses_text_dns_txt_title,
					content: wp_mail_smtp_pro.ses_text_dns_txt_content.replace( /%name%/g, domain ).replace( /%value%/g, txtRecord ),
					buttons: {
						confirm: {
							text: wp_mail_smtp_pro.ses_text_done,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ]
						},
					},
					onOpenBefore: function() {
						this.$body.addClass( 'wp-mail-smtp-providers-amazonses-identity-modal' );
						this.$body.addClass( 'wp-mail-smtp-providers-amazonses-dns-records-modal' );
					},
				} );
			},

			/**
			 * Maybe prevent plugin settings save/submit if an AJAX request is being processed.
			 *
			 * @since 2.4.0
			 *
			 * @returns {boolean} False if the plugin settings save/submit should be prevented.
			 */
			maybePreventSettingsSave: function() {

				if ( app.doingAjax === true ) {
					return false;
				}
			},

			/**
			 * Process the TXT record code copy.
			 *
			 * @param {object} event jQuery event.
			 */
			processTxtCodeCopy: function( event ) {

				event.preventDefault();

				var target = $( this ).siblings( 'input' );

				target.select();
				document.execCommand( 'Copy' );

				var $buttonIcon = $( this ).find( '.dashicons' );

				$buttonIcon
					.removeClass( 'dashicons-admin-page' )
					.addClass( 'wp-mail-smtp-dashicons-yes-alt-green' )
					.fadeOut( 1000, 'swing', function() {
						$buttonIcon
							.removeClass( 'wp-mail-smtp-dashicons-yes-alt-green' )
							.addClass( 'dashicons-admin-page' )
							.fadeIn( 200 );
					} );
			}
		},

		/**
		 * Multisite specific methods.
		 *
		 * @since 2.6.0
		 *
		 * @type {object}
		 */
		multisite: {

			/**
			 * Register all multisite events.
			 *
			 * @since 2.6.0
			 */
			bindActions: function() {
				$( document ).on( 'click', '.js-wp-mail-smtp-clear-network-wide-error-notices', this.clearErrorMessages );
			},

			/**
			 * AJAX call to clear the error notices.
			 *
			 * @since 2.6.0
			 *
			 * @param {object} event The jQuery event object.
			 *
			 * @returns {boolean} If additional processing was skipped.
			 */
			clearErrorMessages: function( event ) {
				event.preventDefault();

				if ( app.doingAjax ) {
					return false;
				}

				$.ajax( {
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'wp_mail_smtp_pro_multisite_clear_error_notices',
						_ajax_nonce: wp_mail_smtp_pro.nonce
					},
					beforeSend: function() {
						app.doingAjax = true;
					}
				} )
					.done( function( response ) {
						if ( response.success ) {
							window.location.reload();
							return false;
						}
					} )
					.always( function() {
						app.doingAjax = false;
					} );
			}
		}
	};

	// Provide access to public functions/properties.
	return app;
}( document, window, jQuery ) );

// Initialize.
WPMailSMTP.Admin.Settings.Pro.init();
