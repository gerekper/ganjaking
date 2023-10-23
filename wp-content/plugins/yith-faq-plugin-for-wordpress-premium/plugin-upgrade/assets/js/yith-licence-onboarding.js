/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

;(function( $ ) {
	"use strict";

	// ES5 to better compatibility.
	var activationForm = {

		form: $( '#activation-form' ),

		init: function() {
			if ( ! this.form.length ) {
				return false;
			}

			this.form.on( 'submit', this.submit.bind( this ) );
			this.form.on( 'focusout', 'input[name="licence_key"]', this.validateField.bind( this ) );
			this.form.on( 'focusout', 'input[name="email"]', this.validateField.bind( this ) );
		},

		getFormData: function() {
			return this.form.serializeArray();
		},

		addError: function( input, name ) {
			input.addClass( 'validation-error' );
			this.addErrorMessage( input, onboardingJS.error.replace( '%field%', onboardingJS[name] ) );
		},

		addErrorMessage: function( input, error, $position ) {
			if ( ! input.siblings( '.error' ).length ) {
				if ( 'before' === $position ) {
					input.before( '<span class="error">' + error + '</span>' );
				} else {
					input.after( '<span class="error">' + error + '</span>' );
				}
			} else {
				input.siblings( '.error' ).html( error );
			}
		},

		removeError: function( input ) {
			input.removeClass( 'validation-error' );
			input.next( '.error' ).remove();
		},

		validateField: function( event ) {
			var input = $( event.currentTarget ),
				name = input.attr( 'name' ),
				value = input.val(),
				regex;

			if ( 'licence_key' === name ) {
				regex = new RegExp( /^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$/g );
			} else if ( 'email' === name ) {
				regex = new RegExp( /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i );
			}

			if ( ! value || (typeof regex !== 'undefined' && ! regex.test( value.toLowerCase() )) ) {
				this.addError( input, name );
			} else {
				this.removeError( input );
			}
		},

		validateFields: function() {
			this.form.find( 'input[name="email"]' ).focusout();
			this.form.find( 'input[name="licence_key"]' ).focusout();

			return ! this.form.find( '.validation-error' ).length;
		},

		submit: function( event ) {
			event.preventDefault();

			// Double validate data before send request.
			if ( ! this.validateFields() ) {
				return false;
			}

			var self = this,
				wrap = self.form.closest( '#content' );

			$.ajax( {
				url: onboardingJS.ajaxurl,
				data: self.getFormData(),
				type: 'POST',
				dataType: 'json',
				beforeSend: function() {
					wrap.addClass( 'loading' );
				},
				success: function( response ) {
					if ( response ) {
						if ( response.activated ) {
							var template = wp.template( 'success-message' );
							wrap.hide().html( template() ).fadeIn();

						} else {
							self.addErrorMessage( self.form.find( 'input[type="submit"]' ), response.error, 'before' );
						}
					} else {
						self.addErrorMessage( self.form.find( 'input[type="submit"]' ), onboardingJS.server, 'before' )
					}

					if ( typeof response.debug !== 'undefined' ) {
						console.log( response.debug );
					}
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					console.log( jqXHR, textStatus, errorThrown );
				},
				complete: function() {
					wrap.removeClass( 'loading' );
				}
			} );
		}
	}

	activationForm.init();

})( jQuery );
