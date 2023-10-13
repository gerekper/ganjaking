/* global wcbk_admin, adminpage, wp */
jQuery( function ( $ ) {
	"use strict";

	var __ = wp.i18n.__;

	/**
	 * Onoff
	 */
	var yith_wcbk_onoff = {
		init  : function () {
			$( document ).on( 'click', '.yith-wcbk-printer-field__on-off', yith_wcbk_onoff.update );
		},
		update: function ( event ) {
			var onoff        = $( event.target ).closest( '.yith-wcbk-printer-field__on-off' ),
				hidden_input = onoff.find( '.yith-wcbk-printer-field__on-off__value' ).first(),
				value        = hidden_input ? hidden_input.val() : 'no';

			if ( value === 'yes' ) {
				hidden_input.val( 'no' );
				onoff.removeClass( 'yith-wcbk-printer-field__on-off--enabled' );
			} else {
				hidden_input.val( 'yes' );
				onoff.addClass( 'yith-wcbk-printer-field__on-off--enabled' );
			}
			hidden_input.trigger( 'change' );
		}
	};

	yith_wcbk_onoff.init();

	/**
	 * Time Select
	 */
	var yith_wcbk_timeselect = {
		container: '.yith-wcbk-time-select__container',
		hour     : '.yith-wcbk-time-select-hour',
		minute   : '.yith-wcbk-time-select-minute',
		separator: ':',
		init     : function () {
			var self = yith_wcbk_timeselect;

			$( document ).on( 'change', self.hour + ', ' + self.minute, self.update );
		},
		update   : function ( event ) {
			var self      = yith_wcbk_timeselect,
				container = $( event.target ).closest( self.container ),
				hour      = container.find( self.hour ).first(),
				minute    = container.find( self.minute ).first(),
				input     = container.find( 'input' ).first();

			input.val( hour.val() + self.separator + minute.val() ).trigger( 'change' );
		}
	};

	yith_wcbk_timeselect.init();


	/**
	 * Select2 - Select All | Deselect All
	 */
	var select_all_btn   = $( '.yith-wcbk-select2-select-all' ),
		deselect_all_btn = $( '.yith-wcbk-select2-deselect-all' );

	deselect_all_btn.each( function () {
		var _currentButton      = $( this ),
			select_id           = $( this ).data( 'select-id' ),
			target_select       = $( '#' + select_id ),
			_checkForVisibility = function () {
				if ( target_select.val() && target_select.val().length ) {
					_currentButton.show();
				} else {
					_currentButton.hide();
				}
			};

		target_select.on( 'change', _checkForVisibility );
		_checkForVisibility();
	} );

	select_all_btn.on( 'click', function () {
		var _currentButton      = $( this ),
			select_id           = $( this ).data( 'select-id' ),
			target_select       = $( '#' + select_id ),
			_checkForVisibility = function () {
				if ( target_select.find( 'option:not(:selected)' ).length ) {
					_currentButton.show();
				} else {
					_currentButton.hide();
				}
			};

		target_select.find( 'option' ).prop( 'selected', true );
		target_select.on( 'change', _checkForVisibility );
		target_select.trigger( 'change' );
	} );

	deselect_all_btn.on( 'click', function () {
		var select_id     = $( this ).data( 'select-id' ),
			target_select = $( '#' + select_id );

		target_select.find( 'option' ).prop( 'selected', false );
		target_select.trigger( 'change' );
	} );


	/**
	 * Delete Logs Confirmation
	 */
	$( '#yith-wcbk-logs' ).on( 'click', 'h2 a.page-title-action', function ( event ) {
		event.stopImmediatePropagation();
		return window.confirm( wcbk_admin.i18n_delete_log_confirmation );
	} );


	/**
	 * Tip Tip
	 */
	$( document ).on( 'yith-wcbk-init-tiptip', function () {
		// Remove any lingering tooltips
		$( '#tiptip_holder' ).removeAttr( 'style' );
		$( '#tiptip_arrow' ).removeAttr( 'style' );
		$( '.tips' ).tipTip( {
								 'attribute': 'data-tip',
								 'fadeIn'   : 50,
								 'fadeOut'  : 50,
								 'delay'    : 200
							 } );
	} ).trigger( 'yith-wcbk-init-tiptip' );


	/**
	 * Date Picker
	 */
	$( document ).on( 'yith-wcbk-init-datepickers', function () {
		$( '.yith-wcbk-admin-date-picker' ).yith_wcbk_datepicker();
	} ).trigger( 'yith-wcbk-init-datepickers' );


	/**
	 *  Copy on Clipboard
	 */
	var copy_to_clipboard_tip = false;
	$( document ).on( 'click', '.yith-wcbk-copy-to-clipboard', function ( event ) {
		var target           = $( this ),
			selector_to_copy = target.data( 'selector-to-copy' ),
			obj_to_copy      = $( selector_to_copy );

		if ( obj_to_copy.length > 0 ) {
			copy_to_clipboard_tip && copy_to_clipboard_tip.remove() && ( copy_to_clipboard_tip = false );

			if ( !copy_to_clipboard_tip ) {
				copy_to_clipboard_tip = $( '<div id="yith-wcbk-copy-to-clipboard__copied">' + wcbk_admin.i18n_copied + '</div>' );
				$( 'body' ).append( copy_to_clipboard_tip );
			}

			copy_to_clipboard_tip.hide();


			var temp  = $( "<input>" ),
				value = obj_to_copy.is( 'input' ) ? obj_to_copy.val() : obj_to_copy.html();
			$( 'body' ).append( temp );

			temp.val( value ).select();
			document.execCommand( "copy" );

			temp.remove();

			copy_to_clipboard_tip.css( {
										   left: target.offset().left + target.outerWidth() / 2 - copy_to_clipboard_tip.outerWidth() / 2,
										   top : target.offset().top - copy_to_clipboard_tip.outerHeight() - 7
									   } )
				.fadeIn().delay( 1000 ).fadeOut();
		}
	} );


	/**
	 *  Show conditional: show/hide element based on other element value
	 *
	 *  rules is an array of key-value object.
	 *  Rules are executed in OR; on the contrary, the rule object is checked for each property in AND.
	 */
	$( '.yith-wcbk-show-conditional' ).hide().each( function () {
		var $show_conditional = $( this ),
			fieldId           = $show_conditional.data( 'field-id' ) || '',
			value             = $show_conditional.data( 'value' ) || '',
			rules             = $show_conditional.data( 'rules' ) || [];

		// handle old behavior, through fieldId and value.
		if ( !rules.length && fieldId && value ) {
			var uniqueRule        = {};
			uniqueRule[ fieldId ] = value;
			rules                 = [uniqueRule];
		}

		if ( rules.length ) {

			var checkVisibility = function () {
				var isVisible = false;
				for ( var i in rules ) {
					var rule          = rules[ i ],
						ruleIsVisible = true;

					for ( var id in rule ) {
						var theField       = $( '#' + id ),
							value          = rule[ id ],
							isCheckbox     = theField.is( 'input[type=checkbox]' ),
							valueToCompare = !isCheckbox ? theField.val() : ( theField.is( ':checked' ) ? 'yes' : 'no' );

						value = value.split( '|' ); // Allow setting values in "OR".

						if ( value.indexOf( valueToCompare ) < 0 ) {
							ruleIsVisible = false;
							break;
						}
					}

					if ( ruleIsVisible ) {
						isVisible = true;
						break;
					}
				}

				return isVisible;
			};

			for ( var i in rules ) {
				var rule = rules[ i ];

				for ( var id in rule ) {
					var theField = $( '#' + id );

					theField.on( 'change keyup', function () {
						if ( checkVisibility() ) {
							$show_conditional.show();
						} else {
							$show_conditional.hide();
						}
					} ).trigger( 'change' );
				}
			}
		}
	} );


	/**
	 *  Move
	 */
	$( '.yith-wcbk-move' ).each( function () {
		var $to_move = $( this ),
			after    = $to_move.data( 'after' );

		if ( after.length > 0 ) {
			$to_move.insertAfter( after ).show();
		}
	} );


	/**
	 *  Date Time Fields
	 */
	$( '.yith-wcbk-date-time-field' ).each( function () {
		var $dateTime  = $( this ),
			dateAnchor = $( this ).data( 'date' ),
			timeAnchor = $( this ).data( 'time' ),
			$date      = $( dateAnchor ).first(),
			$time      = $( timeAnchor ).first(),
			update     = function () {
				$dateTime.val( $date.val() + ' ' + $time.val() );
			};

		$date.on( 'change', update );
		$time.on( 'change', update );
	} );

	/**
	 *  Logs
	 */
	$( document ).on( 'click', '#yith-wcbk-logs-tab-table td.description-column .expand:not(.disabled)', function ( e ) {
		var open               = $( e.target ),
			description_column = open.closest( 'td.description-column' );
		description_column.toggleClass( 'expanded' );
	} );

	/**
	 * Google Calendar: Float button saving
	 */
	$( document ).on( 'yith-plugin-fw-float-save-button-after-saving', function ( event, response ) {
		var googleCalendarLeft = $( '#yith-wcbk-google-calendar-tab__main' );

		if ( googleCalendarLeft.length && response ) {
			var newContent = $( response ).find( '#yith-wcbk-google-calendar-tab__main' );
			if ( newContent.length ) {
				googleCalendarLeft.html( newContent.html() );
			}
		}

	} );

	/**
	 * Fields and deps
	 */
	var costsIncludedInShownPrice                   = $( '#yith-wcbk-costs-included-in-shown-price input[type=checkbox]' ),
		showDurationUnitInPrice                     = $( '#yith-wcbk-show-duration-unit-in-price' ),
		showDurationUnitInPriceRow                  = showDurationUnitInPrice.closest( '.yith-plugin-fw__panel__option, tr.yith-plugin-fw-panel-wc-row' ),
		replaceDaysWithWeeks                        = $( '#yith-wcbk-replace-days-with-weeks-in-price' ),
		replaceDaysWithWeeksRow                     = replaceDaysWithWeeks.closest( '.yith-plugin-fw__panel__option, tr.yith-plugin-fw-panel-wc-row' ),
		showPricesForServices                       = $( '#yith-wcbk-show-service-prices' ),
		showDescriptionsForServices                 = $( '#yith-wcbk-show-service-descriptions' ),
		serviceInfoLayoutRow                        = $( '#yith-wcbk-service-info-layout' ).closest( '.yith-plugin-fw__panel__option, tr.yith-plugin-fw-panel-wc-row' ),
		checkCostsIncludedInShowPriceDepsVisibility = function () {
			var checkedCosts      = costsIncludedInShownPrice.filter( ':checked' ),
				checkedCostValues = checkedCosts.map(
					function () {
						return $( this ).val();
					}
				).get();

			if ( checkedCostValues.includes( 'base-price' ) && !checkedCostValues.includes( 'extra-costs' ) && !checkedCostValues.includes( 'services' ) ) {
				showDurationUnitInPriceRow.show();
				if ( 'yes' === showDurationUnitInPrice.val() ) {
					replaceDaysWithWeeksRow.show();
				} else {
					replaceDaysWithWeeksRow.hide();
				}
			} else {
				showDurationUnitInPriceRow.hide();
				replaceDaysWithWeeksRow.hide();
			}
		},
		checkServiceInfoLayoutVisibility            = function () {
			if ( showPricesForServices.is( ':checked' ) || showDescriptionsForServices.is( ':checked' ) ) {
				serviceInfoLayoutRow.show();
			} else {
				serviceInfoLayoutRow.hide();
			}
		};

	costsIncludedInShownPrice.on( 'change', function () {
		var checkedCosts = costsIncludedInShownPrice.filter( ':checked' );
		if ( !checkedCosts.length ) {
			$( this ).prop( "checked", true );
		}

		checkCostsIncludedInShowPriceDepsVisibility();
	} );

	showDurationUnitInPrice.on( 'change', checkCostsIncludedInShowPriceDepsVisibility );

	showPricesForServices.on( 'change', checkServiceInfoLayoutVisibility );
	showDescriptionsForServices.on( 'change', checkServiceInfoLayoutVisibility );

	checkCostsIncludedInShowPriceDepsVisibility();
	checkServiceInfoLayoutVisibility();

	/**
	 * Day-Month field
	 */
	var bkDayMonth = {
		init       : function () {
			$( document ).on( 'change', '.yith-wcbk-day-month__day, .yith-wcbk-day-month__month ', bkDayMonth.update );
		},
		update     : function ( event ) {
			var wrap  = $( event.target ).closest( '.yith-wcbk-day-month' ),
				input = wrap.find( '.yith-wcbk-day-month__value' ).first(),
				day   = wrap.find( '.yith-wcbk-day-month__day' ).val(),
				month = wrap.find( '.yith-wcbk-day-month__month' ).val(),
				value = bkDayMonth.formatValue( month, day );

			wrap.data( 'value', value );
			input.val( value ).trigger( 'change' );
		},
		formatValue: function ( month, day ) {
			if ( day < 10 ) {
				day = '0' + day;
			}

			if ( month < 10 ) {
				month = '0' + month;
			}

			return month + '-' + day;
		}
	};

	bkDayMonth.init();

	/**
	 * Admin Media uploader
	 */

	$( document ).on(
		'yith-wcbk-init-fields:admin-media',
		function () {
			$( '.yith-wcbk-admin-media:not(.yith-wcbk-admin-media--initialized)' ).each(
				function () {
					var self = {};

					if ( typeof wp !== 'undefined' && typeof wp.media !== 'undefined' ) {

						self.container = $( this );
						self.dom       = {
							field      : self.container.find( '.yith-wcbk-admin-media__field' ),
							clear      : self.container.find( '.yith-wcbk-admin-media__clear' ),
							image      : self.container.find( '.yith-wcbk-admin-media__image' ),
							placeholder: self.container.find( '.yith-wcbk-admin-media__placeholder' )
						};

						self.uploader = wp.media(
							{
								title   : __( 'Choose image', 'yith-booking-for-woocommerce' ),
								library : {
									type: ''
								},
								button  : {
									text: __( 'Choose image', 'yith-booking-for-woocommerce' )
								},
								multiple: false
							}
						);

						self.uploader.on( 'select', function () {
							var attachment = self.uploader.state().get( 'selection' ).first().toJSON();

							self.dom.image.attr( 'src', attachment.url );
							self.dom.field.val( attachment.id );

							self.container.addClass( 'yith-wcbk-admin-media--has-image' );
						} );


						self.openUploader = function () {
							self.uploader.open();
						};

						self.handleClear = function ( e ) {
							e.stopPropagation();
							self.dom.image.attr( 'src', '' );
							self.dom.field.val( '' );
							self.container.removeClass( 'yith-wcbk-admin-media--has-image' );
						};

						self.container.on( 'click', self.openUploader );
						self.dom.clear.on( 'click', self.handleClear );
					}
				}
			);
		}
	).trigger( 'yith-wcbk-init-fields:admin-media' );

	/**
	 * CPT Publish button
	 */
	if ( typeof adminpage !== 'undefined' && ['post-php', 'post-new-php'].indexOf( adminpage ) >= 0 ) {
		var postTypeSaving = {
			dom                      : {
				actions  : $( '#yith-wcbk-post-type__actions' ),
				save     : $( '#yith-wcbk-post-type__save' ),
				floatSave: $( '#yith-wcbk-post-type__float-save' )
			},
			init                     : function () {
				var self = postTypeSaving;
				if ( self.dom.save.length ) {
					self.dom.save.on( 'click', self.onSaveClick );
					self.dom.floatSave.on( 'click', self.onFloatSaveClick );

					document.addEventListener( 'scroll', self.handleFloatSaveVisibility, { passive: true } );
					$( window ).on( 'resize', self.handleFloatSaveVisibility );
					self.handleFloatSaveVisibility();
				}
			},
			isInViewport             : function ( el ) {
				var rect     = el.get( 0 ).getBoundingClientRect(),
					viewport = {
						width : window.innerWidth || document.documentElement.clientWidth,
						height: window.innerHeight || document.documentElement.clientHeight
					};
				return (
					rect.top >= 0 &&
					rect.left >= 0 &&
					rect.top <= viewport.height &&
					rect.left <= viewport.width
				);
			},
			handleFloatSaveVisibility: function () {
				if ( postTypeSaving.isInViewport( postTypeSaving.dom.save ) ) {
					postTypeSaving.dom.floatSave.removeClass( 'visible' );
				} else {
					postTypeSaving.dom.floatSave.addClass( 'visible' );
				}
			},
			onSaveClick              : function () {
				$( window ).off( 'beforeunload.edit-post' );

				$( this ).block(
					{
						message   : null,
						overlayCSS: {
							background: 'transparent',
							opacity   : 0.6
						}
					}
				);
			},
			onFloatSaveClick         : function () {
				postTypeSaving.dom.save.trigger( 'click' );
			}
		};

		postTypeSaving.init();
	}

	// Disable WooCommerce check for changes
	$( function () {
		if ( wcbk_admin.disableWcCheckForChanges ) {
			$( 'input, textarea, select, checkbox' ).on( 'change', function () {
				window.onbeforeunload = '';
			} );
		}
	} );
} );