var array_unique_noempty, element_box;

jQuery(
	function ( $ ) {

		var checked = {};

		$( '.checkbox-column a' ).click(
			function ( e ) {

				e.preventDefault();

				var column   = $( this ).attr( 'id' ).replace( '-lnk', '' ),
					table    = $( this ).parent().parent().parent().parent(),
					checkbox = '.' + column + '-cb';

				if ( ! ( column in checked ) || checked[ column ] === false ) {

					table.find( checkbox ).prop( 'checked', true );
					checked[ column ] = true;

				} else {

					table.find( checkbox ).prop( 'checked', false );
					checked[ column ] = false;

				}

			}
		);

		var sms_gateway = $( '#ywsn_sms_gateway' );

		if ( typeof sms_gateway !== 'undefined' ) {
			set_gateway( sms_gateway.val() );
			gateway_special_options( sms_gateway.val() );

		}

		sms_gateway.on(
			'change',
			function () {
				set_gateway( sms_gateway.val() );
				gateway_special_options( sms_gateway.val() );
			}
		);

		function set_gateway( value ) {

			$( 'option', sms_gateway ).each(
				function () {

					var desc  = $( '#' + $( this ).val().toLowerCase() + '_title-description' ),
						table = desc.next( 'table' ),
						title = desc.prev( 'h2' );

					if ( $( this ).val() === value ) {

						desc.show();
						table.show();
						title.show();
						toggle_required_field( table, true );

					} else {

						desc.hide();
						table.hide();
						title.hide();
						toggle_required_field( table, false );

					}

				}
			);

		}

		function gateway_special_options( value ) {

			var from_number = $( '#ywsn_from_number' ),
				sender_id   = $( '#ywsn_from_asid' );

			switch ( value ) {

				case 'YWSN_Jazz':
				case 'YWSN_Trend_Mens':
				case 'YWSN_Sabeq_Alarabia':
				case 'YWSN_Bulk_SMS_SA':
				case 'YWSN_Bulk_SMS':
				case 'YWSN_Crystalwebtechs':
				case 'YWSN_Mobily_WS':
				case 'YWSN_Netpowers':
				case 'YWSN_SMSAPI':
				case 'YWSN_Uaedes':
					from_number.parent().parent().parent().hide();
					from_number.prop( 'required', false );
					sender_id.parent().parent().parent().hide();
					break;

				default:
					from_number.parent().parent().parent().show();
					from_number.prop( 'required', true );
					sender_id.parent().parent().parent().show();
			}

		}

		function toggle_required_field( obj, required ) {

			$( 'input', obj ).each(
				function () {
					$( this ).prop( 'required', required );
				}
			);

		}

		$( document ).ready(
			function () {
				"use strict";

				$( '#ywsn_sms_test_message' ).change(
					function () {

						var option    = $( 'option:selected', this ).val(),
							write_sms = $( '.ywsn-write-sms' );

						if ( option === 'write-sms' ) {

							write_sms.show();

						} else {

							write_sms.hide();

						}

					}
				).change();

				$( '.ywsn-custom-message' ).on(
					'change keyup input',
					function () {

						var length     = $( this ).val().length,
							chars      = ywsn_admin.sms_length - length,
							char_count = $( '.ywsn-char-count' );

						char_count.find( 'span' ).text( chars );

						if ( chars < 0 ) {
							char_count.addClass( 'maxed-out' );
						} else {
							char_count.removeClass( 'maxed-out' );
						}

					}
				);

				element_box.init();

			}
		);

		$( 'body' )
			.on(
				'click',
				'button.ywsn-send-test-sms',
				function () {

					var result       = $( '.ywsn-send-result' ),
						phone        = $( '.ywsn-test-sms' ).val(),
						country      = $( '#ywsn_sms_test_message_country' ).val(),
						message_type = $( '#ywsn_sms_test_message' ).val(),
						container    = $( '.ywsn-sms-send-wrapper' ),
						message      = '';

					container.addClass( 'processing' );
					container.block(
						{
							message   : null,
							overlayCSS: {
								background: '#fff',
								opacity   : 0.6
							}
						}
					);

					result
						.show()
						.removeClass( 'send-progress send-fail send-success' );

					if ( message_type === '' ) {
						result
							.addClass( 'send-fail' )
							.html( ywsn_admin.sms_no_message );
						container
							.removeClass( 'processing' )
							.unblock();
						return;
					}

					switch ( message_type ) {
						case 'write-sms':
							message = $( '.ywsn-custom-message' ).val();
							break;

						default:
							message = $( '#ywsn_message_' + message_type ).val()
					}

					if ( message === '' ) {
						result
							.addClass( 'send-fail' )
							.html( ywsn_admin.sms_empty_message );
						container
							.removeClass( 'processing' )
							.unblock();
						return;
					}

					if ( phone === '' ) {
						result
							.addClass( 'send-fail' )
							.html( ywsn_admin.sms_wrong );
						container
							.removeClass( 'processing' )
							.unblock();
						return;
					}

					var data = {
						action : 'ywsn_send_sms',
						phone  : phone,
						message: message,
						country: country
					};

					result
						.addClass( 'send-progress' )
						.html( ywsn_admin.sms_before_send );

					$.post(
						ywsn_admin.ajax_url,
						data,
						function ( response ) {

							result.removeClass( 'send-progress' );
							container
								.removeClass( 'processing' )
								.unblock();

							if ( response.success === true ) {
								result
									.addClass( 'send-success' )
									.html( ywsn_admin.sms_after_send );
							} else {
								result
									.addClass( 'send-fail' )
									.html( response.error );
							}

						}
					);

				}
			)
			.on(
				'click',
				'button.ywsn-send-sms',
				function () {

					var result      = $( '.ywsn-send-result' ),
						container   = $( '.ywsn-sms-metabox' ),
						receive_sms = $( '#_ywsn_receive_sms' ).is( ':checked' ),
						message     = $( '.ywsn-custom-message' ).val();

					container
						.addClass( 'processing' )
						.block(
							{
								message   : null,
								overlayCSS: {
									background: '#fff',
									opacity   : 0.6
								}
							}
						);

					if ( ywsn_admin.sms_customer_notification === 'requested' && ! receive_sms ) {

						if ( ! window.confirm( ywsn_admin.sms_manual_send_advice ) ) {
							container
								.removeClass( 'processing' )
								.unblock();
							return;
						}

					}

					result
						.show()
						.removeClass( 'send-progress send-fail send-success' );

					if ( message === '' ) {

						result
							.addClass( 'send-fail' )
							.html( ywsn_admin.sms_empty_message );
						container
							.removeClass( 'processing' )
							.unblock();
						return;

					}

					var data = {
						action     : 'ywsn_send_sms',
						object_id  : $( '#YWSN_object_id' ).val(),
						object_type: $( '#YWSN_object_type' ).val(),
						message    : message
					};

					result
						.addClass( 'send-progress' )
						.html( ywsn_admin.sms_before_send );

					$.post(
						ywsn_admin.ajax_url,
						data,
						function ( response ) {

							result
								.removeClass( 'send-progress' );
							container
								.removeClass( 'processing' )
								.unblock();

							if ( response.success === true ) {

								result
									.addClass( 'send-success' )
									.html( ywsn_admin.sms_after_send );

							} else {

								result
									.addClass( 'send-fail' )
									.html( response.error );

							}

							if ( $( '#YWSN_object_type' ).val() === 'booking' ) {
								$( 'ul.booking-notes' ).prepend( response.note );
							} else {
								$( 'ul.order_notes' ).prepend( response.note );
							}

							$( '.ywsn-custom-message' ).val( '' );
							$( '.ywsn-char-count span' ).text( ywsn_admin.sms_length );

						}
					);

				}
			);

		array_unique_noempty = function ( array ) {
			var out = [];

			$.each(
				array,
				function ( key, val ) {
					val = $.trim( val );

					if ( val && $.inArray( val, out ) === -1 ) {
						out.push( val );
					}
				}
			);

			return out;
		};

		element_box = {
			clean: function ( tags ) {

				tags = tags.replace( /\s*,\s*/g, ',' ).replace( /,+/g, ',' ).replace( /[,\s]+$/, '' ).replace( /^[,\s]+/, '' );

				return tags;
			},

			parseTags: function ( el ) {
				var id             = el.id,
					num            = id.split( '-check-num-' )[ 1 ],
					element_box    = $( el ).closest( '.ywcc-checklist-div' ),
					values         = element_box.find( '.ywcc-values' ),
					current_values = values.val().split( ',' ),
					new_elements   = [];

				delete current_values[ num ];

				$.each(
					current_values,
					function ( key, val ) {
						val = $.trim( val );
						if ( val ) {
							new_elements.push( val );
						}
					}
				);

				values.val( this.clean( new_elements.join( ',' ) ) );

				this.quickClicks( element_box );
				return false;
			},

			quickClicks: function ( el ) {

				var values      = $( '.ywcc-values', el ),
					values_list = $( '.ywcc-value-list ul', el ),
					id          = $( el ).attr( 'id' ),
					current_values;

				if ( ! values.length ) {
					return;
				}

				current_values = values.val().split( ',' );
				values_list.empty();

				$.each(
					current_values,
					function ( key, val ) {

						var item, xbutton;

						val = $.trim( val );

						if ( ! val ) {
							return;
						}

						item    = $( '<li class="select2-selection__choice" />' );
						xbutton = $( '<span id="' + id + '-check-num-' + key + '" class="select2-selection__choice__remove" tabindex="0"></span>' );

						xbutton.on(
							'click keypress',
							function ( e ) {

								if ( e.type === 'click' || e.keyCode === 13 ) {

									if ( e.keyCode === 13 ) {
										$( this ).closest( '.ywcc-checklist-div' ).find( 'input.ywcc-insert' ).focus();
									}

									element_box.parseTags( this );
								}

							}
						);

						item.prepend( val ).prepend( xbutton );

						values_list.append( item );

					}
				);
			},

			flushTags: function ( el, a, f ) {
				var current_values,
					new_values,
					text,
					values  = $( '.ywcc-values', el ),
					add_new = $( 'input.ywcc-insert', el );

				a = a || false;

				text = a ? $( a ).text() : add_new.val();

				if ( 'undefined' === typeof ( text ) ) {
					return false;
				}

				current_values = values.val();
				new_values     = current_values ? current_values + ',' + text : text;
				new_values     = this.clean( new_values );
				new_values     = array_unique_noempty( new_values.split( ',' ) ).join( ',' );
				values.val( new_values );

				this.quickClicks( el );

				if ( ! a ) {
					add_new.val( '' );
				}
				if ( 'undefined' === typeof ( f ) ) {
					add_new.focus();
				}

				return false;

			},

			init: function () {
				var ajax_div = $( '.ywcc-checklist-ajax' );

				$( '.ywcc-checklist-div' ).each(
					function () {
						element_box.quickClicks( this );
					}
				);

				$( 'input.ywcc-insert', ajax_div ).keyup(
					function ( e ) {
						if ( 13 === e.which ) {
							element_box.flushTags( $( this ).closest( '.ywcc-checklist-div' ) );
							return false;
						}
					}
				).keypress(
					function ( e ) {
						if ( 13 === e.which ) {
							e.preventDefault();
							return false;
						}
					}
				);

			}
		};

	}
);
