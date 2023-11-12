( function( $ ) {

	UAELRegistrationForm = {

		/**
		 * Check Password Strength
		 *
		 */
		_checkPasswordStrength: function( $scope ) {

			var strength;
			var password = $scope.find( 'input[type=password]' ).val();
			var pass_wrap = $scope.find( '.uael-pass-wrapper' );
			var pass_notice = $scope.find( '.uael-pass-notice' );

			pass_wrap.css( 'display', 'block' );

			if( uaelRegistration.wp_version ){
				strength = wp.passwordStrength.meter( password, wp.passwordStrength.userInputDisallowedList(), password );				
			} else {
				strength = wp.passwordStrength.meter( password, wp.passwordStrength.userInputBlacklist(), password );
			}

			
			switch ( strength ) {
				case -1:
					// pass_notice.html( pwsL10n.unknown ).css( 'color', '#cfcfcf' );
					break;
				case 2:
					pass_notice.html( pwsL10n.bad ).css( 'color', '#e07757' );
					break;
				case 3:
					pass_notice.html( pwsL10n.good ).css( 'color', '#f0ad4e' );
					break;
				case 4:
					pass_notice.html( pwsL10n.strong ).css( 'color', '#5cb85c' );
					break;
				case 5:
					pass_notice.html( pwsL10n.mismatch ).css( 'color', '#f0ad4e' );
					break;
				default:
					pass_notice.html( pwsL10n['short'] ).css( 'color', '#d9534f' );
			}

		},

		/**
		 * Display error messages
		 *
		 */
		_printErrorMessages: function( $scope, form_field, message ) {

			var $is_error = form_field.next().hasClass( 'uael-register-field-message' );

			if( $is_error ) {
				return;
			} else {
				form_field.after( '<span class="uael-register-field-message"><span class="uael-register-error">' + message + '</span></span>' );
			}
		},

		/**
		 * Submit form action
		 *
		 */
		_submitForm: function( $this, widget_wrapper, $scope ) {
			var ajaxurl = uaelRegistration.ajax_url;
			var $password =	'';
			var $invalid_field = false;
			var user_email_regex = /\S+@\S+\.\S+/;
			var is_password_exists = false;
			var form_wrap = $scope.find( '.uael-registration-form' );
			var redirect_url = form_wrap.attr( 'data-redirect-url' );
			var button_text = $this.find( '.uael-registration-submit' );
			var user_pass = form_wrap.find( '.form-field-password' );
			var form_fields = form_wrap.find( '.uael-input-fields' );
			var is_secure = true;
			var user_data = {};
			var recaptcha_field = $scope.find( '.uael-g-recaptcha-field' );
			var acceptance = $scope.find( 'input[name=uae-terms-checkbox]' );
			var acceptance_field = $scope.find( '.elementor-field-subgroup' );
			var acceptance_wrap = $scope.find( '.acceptance-field' );

			if( acceptance_wrap.length > 0 ) {
				if( !acceptance.is( ':checked' ) ) {
					$invalid_field = true;
					acceptance_field.next().show();
				}
				$scope.find( '.elementor-acceptance-field' ).on( 'click', function() {
					$scope.find( '.uael-register-acceptance-message' ).hide();
				});		
			}
			
			user_data['page_id'] = widget_wrapper.data( 'page-id' );
			user_data['widget_id'] = $scope.data( 'id' );

			if( recaptcha_field.length > 0 ) {
				user_data['is_recaptcha_enabled'] = 'yes';
				user_data['recaptcha_token'] = form_wrap.find( '.uael-g-recaptcha-response' ).val();
			}

			form_fields.each( function( item ) {

				var $this = $( this );
				var form_field = $this.find( '.elementor-field' );
				var field_type = form_field.attr( 'name' );
				var is_required = $this.hasClass( 'elementor-field-required' );
				var field_text = form_field.val();

				if ( form_field.length > 0 && '' !== field_text ) {
					if ( 'email' === field_type ) {
						field_text = $.trim( field_text );
						if ( user_email_regex.test( field_text ) ) {

							form_field.siblings( '.uael-register-field-message' ).hide();

						} else {
							$invalid_field = true;
							UAELRegistrationForm._printErrorMessages( $scope, form_field, uaelRegistration.invalid_mail );
						}
					}

					if( 'password' === is_password_exists ) {
						is_password_exists = true;
					}

					if ( 'confirm_pass' === field_type ) {
						if ( user_pass.val() !== form_field.val() ) {
							// var error_message = form_field.next().hasClass( 'uael-register-field-message' );
							$invalid_field = true;
							UAELRegistrationForm._printErrorMessages( $scope, form_field, uaelRegistration.pass_unmatch );
						}
					} else if( '' !== field_text && '' !== field_type ) {
						user_data[field_type] = field_text;
					}					

				} else if ( form_field.length > 0 && is_required ) {

					$invalid_field = true;
					UAELRegistrationForm._printErrorMessages( $scope, form_field, uaelRegistration.required );

				}

				if( form_field.hasClass( 'uael-regform-set-field' ) && '' !== field_text ) {
					is_secure = false;
					$scope.find( '.uael-registration-message' ).addClass( 'error' ).text( "Invalid Form!" );
				}

				user_data[field_type] = field_text;

			});

			if( is_secure ) {

				user_data['auto_login'] = form_wrap.attr( 'data-auto_login' );
				user_data['send_email'] = form_wrap.attr( 'data-send_email' );

				if( false == is_password_exists ) {
					user_data['send_email_password'] = 'yes';
				} else {
					user_data['send_email_password'] = 'no';
				}
							
				var nonce = $scope.find('input#uael-reg-form-nonce').val();
				
				$scope.find( '.uael-registration-message' ).text( "" ).removeClass( 'success error' );

				if ( ! $invalid_field ) {
					$.post( ajaxurl, {
						action: 'uael_register_user',
						data: user_data,
						nonce: nonce,
						method: 'post',
						dataType: 'json',
						beforeSend: function () {

							form_wrap.animate({
								opacity: '0.45'
							}, 500 ).addClass( 'uael-form-waiting' );

							if( ! button_text.hasClass( 'disabled' ) && ! $invalid_field ) {
								button_text.addClass( 'disabled' );
								button_text.append( '<span class="uael-form-loader"></span>' );
							}

							user_pass.siblings( '.uael-pass-wrapper' ).remove();
						},
					}, function( data ) {

						if( recaptcha_field.length > 0 ) {
							recaptcha_id = recaptcha_field.attr( 'data-widgetid' );
							grecaptcha.reset( recaptcha_id );
							grecaptcha.execute( recaptcha_id );
						}

						if ( data.success === true ) {

							if ( 'yes' === user_data['auto_login'] && ( ( 'undefined' === typeof redirect_url ) || ( '' === redirect_url ) ) ) {
								location.reload();
							} else if ( ( 'undefined' !== typeof redirect_url ) && ( '' !== redirect_url ) ) {
								window.setTimeout( function () {
									window.location = redirect_url;
								});
							}

							if( 'yes' === form_wrap.attr( 'data-hide_form' ) ) {
								form_wrap.find( '.elementor-form' ).remove();
							} else {
								button_text.find( '.uael-form-loader' ).remove();
								button_text.removeClass( 'disabled' );
								$scope.find( '.uael-register-field-message' ).remove();

								$scope.find( '.elementor-form' ).trigger( 'reset' );
							}

							form_wrap.animate({
								opacity: '1'
							}, 100 ).removeClass( 'uael-form-waiting' );

							if( 'yes' !== form_wrap.attr( 'data-hide_form' ) ) {
								$success_text = widget_wrapper.data( 'success-message' );
		                    	$scope.find( '.uael-registration-message' ).removeClass( 'error' ).addClass( 'success' ).text( $success_text );
							}

						} else {

							form_wrap.animate({
								opacity: '1'
							}, 100 ).removeClass( 'uael-form-waiting' );

							button_text.find( '.uael-form-loader' ).remove();
							button_text.removeClass( 'disabled' );
							$scope.find( '.uael-register-field-message' ).remove();

							jQuery.each( data.error, function ( key, message ) {

								var error_field = $scope.find( '.form-field-' + key );

								UAELRegistrationForm._printErrorMessages( $scope, error_field, message );
							});

							if( undefined === data.error ) {
								$error_text = widget_wrapper.data( 'error-message' );
								$scope.find( '.uael-registration-message' ).removeClass( 'success' ).addClass( 'error' ).text( $error_text );
							}
						}
					});
				} else {
					return false;
				}
			}
		}
	}

	window.onLoadUAEReCaptcha = function() {
		var reCaptchaFields = $( '.uael-g-recaptcha-field' ),
			widgetID;
		if ( reCaptchaFields.length > 0 ) {
			reCaptchaFields.each( function() {
				var self 		= $( this ),
				 	attrWidget 	= self.attr( 'data-widgetid' );

				// Avoid re-rendering as it's throwing API error
				if ( ( typeof attrWidget !== typeof undefined && attrWidget !== false ) ) {
					return;
				} else {
					widgetID = grecaptcha.render( $( this ).attr( 'id' ), { 
						sitekey : self.data( 'sitekey' ),
						callback: function( response ) {
							if ( response != '' ) {
								self.append( jQuery( '<input>', {
									type: 'hidden',
									value: response,
									class: 'uael-g-recaptcha-response'
								}));
							}
						}
					});
					self.attr( 'data-widgetid', widgetID );
				}
			});
		}
	};

	/**
	 * Registration Form handler Function.
	 *
	 */
	var WidgetUAELRegistrationFormHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope )
			return;
		
		var scope_id = $scope.data( 'id' );
		var submit_button = $scope.find( '.uael-register-submit' );
		var password_field = $scope.find( 'input[type=password]' );
		var widget_wrapper = $scope.find( '.uael-registration-form' );
		var form_wrapper = widget_wrapper.find( '.elementor-form' );

		if( 'yes' == widget_wrapper.data( 'strength-check' ) ) {
			password_field.on( 'keyup', function( e ) {
				UAELRegistrationForm._checkPasswordStrength( $scope );
			});
		}

		password_field.on( 'focusout', function( e ) {

			if( '' === $scope.find( 'input[type=password]' ).val() ) {
				$scope.find( '.uael-pass-wrapper' ).css( 'display', 'none' );
			}
		});

		$scope.find( '.elementor-field' ).on( 'keyup', function( e ) {
			$( this ).siblings( '.uael-register-field-message' ).remove();
		});

		if( $scope.find( '.uael-recaptcha-alert' ).length > 0 ) {

			submit_button.addClass( 'uael-submit-disabled' );

		} else {

			var recaptcha_field = $scope.find( '.uael-g-recaptcha-field' );
			submit_button.removeClass( 'uael-submit-disabled' );

			if ( elementorFrontend.isEditMode() && undefined == recaptcha_field.attr( 'data-widgetid' ) ) {
				onLoadUAEReCaptcha();
			}

			if( recaptcha_field.length > 0 ) {
				grecaptcha.ready( function () {
					recaptcha_id = recaptcha_field.attr( 'data-widgetid' );
					grecaptcha.execute( recaptcha_id );
				});
			}

		}

		/**
		 * Validate form on submit button click.
		 *
		 */
		submit_button.on( 'click', function() {
			event.preventDefault();

			var $this = $( this );
			UAELRegistrationForm._submitForm( $this, widget_wrapper, $scope );

		} );

	};

	$( window ).on( 'elementor/frontend/init', function () {

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-registration-form.default', WidgetUAELRegistrationFormHandler );

	});

} )( jQuery );
