( function( window, document, $ ) {
	'use strict';

	var tcAPI = {};
	var TMEPOJS = window.TMEPOJS;
	var wp = window.wp;
	var TMEPOQTRANSLATEXJS = window.TMEPOQTRANSLATEXJS;
	var noUiSlider = window.noUiSlider;
	var ClipboardEvent = window.ClipboardEvent;
	var DataTransfer = window.DataTransfer;
	var lateVariationEvent = [];
	var tmLazyloadContainer = false;
	var variationsFormIsLoaded = false;
	var jBody = $( 'body' );
	var jWindow = $( window );
	var jDocument = $( document );
	var errorObject;
	var FloatingTotalsBox;
	var currentAjaxButton;
	var errorContainer = $( window );
	var tcmexp = window.tcmexp;
	var _ = window._;

	var getLocalInputDecimalSeparator = function() {
		if ( TMEPOJS.tm_epo_global_input_decimal_separator === '' ) {
			return TMEPOJS.currency_format_decimal_sep;
		}
		return $.epoAPI.locale.getSystemDecimalSeparator();
	};

	var getLocalDecimalSeparator = function() {
		if ( TMEPOJS.tm_epo_global_displayed_decimal_separator === '' ) {
			return TMEPOJS.currency_format_decimal_sep;
		}
		return $.epoAPI.locale.getSystemDecimalSeparator();
	};

	var getLocalThousandSeparator = function() {
		if ( TMEPOJS.tm_epo_global_displayed_decimal_separator === '' ) {
			return TMEPOJS.currency_format_thousand_sep;
		}
		return $.epoAPI.locale.getSystemDecimalSeparator();
	};

	var getEpoDelay = function() {
		if ( TMEPOJS.tm_epo_start_animation_delay ) {
			return TMEPOJS.tm_epo_start_animation_delay;
		} else if ( window.tc_epo_delay ) {
			return window.tc_epo_delay;
		}
		return 500;
	};

	var getEpoAnimationDelay = function() {
		if ( TMEPOJS.tm_epo_animation_delay ) {
			return TMEPOJS.tm_epo_start_animation_delay;
		} else if ( window.tc_epo_animation_delay ) {
			return window.tc_epo_animation_delay;
		}
		return 500;
	};

	if ( ! TMEPOJS || ! wp ) {
		return;
	}

	// Set update event for Lazy Load XT
	if ( TMEPOJS.tm_epo_no_lazy_load === 'no' && $.lazyLoadXT ) {
		$.extend( $.lazyLoadXT, {
			autoInit: false,
			updateEvent: $.lazyLoadXT.updateEvent + ' tmlazy'
		} );
	}

	tcAPI.localInputDecimalSeparator = getLocalInputDecimalSeparator();
	tcAPI.localDecimalSeparator = getLocalDecimalSeparator();
	tcAPI.localThousandSeparator = getLocalThousandSeparator();
	tcAPI.epoDelay = getEpoDelay();
	tcAPI.epoAnimationDelay = getEpoAnimationDelay();
	tcAPI.getElementFromFieldCache = [];
	tcAPI.epoSelector = '.tc-extra-product-options';
	tcAPI.associatedEpoSelector = '.tc-extra-product-options-inline';
	tcAPI.associatedEpoCart = '.tc-epo-element-product-container-cart';
	tcAPI.addToCartSelector = "input[name='add-to-cart']";
	tcAPI.tcAddToCartSelector = 'input.tc-add-to-cart';
	tcAPI.qtySelector = "input.qty,input[name='quantity'],select.qty,.drop-down-button #qty,.plus-minus-button #qty,.slider-input #amount";
	tcAPI.associateQtySelector = 'input.tm-qty-alt';
	tcAPI.addToCartButtonSelector = '.add_to_cart_button, .single_add_to_cart_button';
	tcAPI.compositeSelector = '.bto_item,.component';
	tcAPI.nativeProductPriceSelector = '.woocommerce div.product p.price';
	tcAPI.associatedNativeProductPriceSelector = '.product-price span.price';
	tcAPI.templateEngine = $.epoAPI.applyFilter( 'tc_adjust_templateEngine', {
		price: wp.template( 'tc-price' ),
		sale_price: wp.template( 'tc-sale-price' ),
		tc_chars_remanining: wp.template( 'tc-chars-remanining' ),
		tc_final_totals: wp.template( 'tc-final-totals' ),
		tc_floating_box: wp.template( 'tc-floating-box' ),
		tc_floating_box_nks: wp.template( 'tc-floating-box-nks' ),
		tc_formatted_price: wp.template( 'tc-formatted-price' ),
		tc_formatted_sale_price: wp.template( 'tc-formatted-sale-price' ),
		tc_lightbox: wp.template( 'tc-lightbox' ),
		tc_lightbox_zoom: wp.template( 'tc-lightbox-zoom' ),
		tc_section_pop_link: wp.template( 'tc-section-pop-link' ),
		tc_upload_messages: wp.template( 'tc-upload-messages' )
	} );

	// make API available to 3rd party plugins
	$.tcAPI = function() {
		return tcAPI;
	};
	// method for accessing internal api variables
	$.tcAPIGet = function( name ) {
		return tcAPI[ name ];
	};

	// method for setting  internal api variables
	$.tcAPISet = function( name, value ) {
		tcAPI[ name ] = value;
	};

	if ( $.tc_validator ) {
		$.extend( $.tc_validator.messages, {
			required: TMEPOJS.tm_epo_global_validator_messages.required,
			email: TMEPOJS.tm_epo_global_validator_messages.email,
			url: TMEPOJS.tm_epo_global_validator_messages.url,
			number: TMEPOJS.tm_epo_global_validator_messages.number,
			digits: TMEPOJS.tm_epo_global_validator_messages.digits,
			maxlengthsingle: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.maxlengthsingle ),
			maxlength: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.maxlength ),
			minlengthsingle: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.minlengthsingle ),
			minlength: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.minlength ),
			max: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.max ),
			min: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.min ),
			step: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.step ),
			lettersonly: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.lettersonly ),
			lettersspaceonly: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.lettersspaceonly ),
			alphanumeric: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.alphanumeric ),
			alphanumericunicode: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.alphanumericunicode ),
			alphanumericunicodespace: $.tc_validator.format( TMEPOJS.tm_epo_global_validator_messages.alphanumericunicodespace )
		} );
	}

	/*

     ASCII Digits
     \u0030-\u0039

     Latin Alphabet
     \u0041-\u005A\u0061-\u007A

     Latin-1 Supplement
     \u00C0-\u00D6\u00D8-\u00F6\u00F8-\u00FF

     Latin Extended-A
     \u0100-\u0148\u014A-\u017F

     Latin Extended-B
     \u0180-\u01BF\u01C4-\u024F

     Latin Extended Additional
     \u1E02-\u1EF3

     Greek and Coptic
     \u0370-\u03FF

     Cyrillic
     \u0400-\u04FF

     Japanese Hiragana
     \u3040-\u309f
     Japanese Katakana
     \u30a0-\u30ff
     Japanese Kanji (common & uncommon)
     \u4e00-\u9faf
     Japanese Kanji (rare)
     \u3400-\u4dbf

     \u0030-\u0039\u0041-\u005A\u0061-\u007A\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u00FF\u0100-\u0148\u014A-\u017F\u0180-\u01BF\u01C4-\u024F\u1E02-\u1EF3\u0370-\u03FF\u0400-\u04FF\u3040-\u309f\u30a0-\u30ff\u4e00-\u9faf\u3400-\u4dbf

     */
	if ( $.tc_validator ) {
		$.tc_validator.addMethod(
			'alphanumeric',
			function( value, element ) {
				return this.optional( element ) || /^[a-zA-Z0-9.-]+$/i.test( value );
			},
			$.tc_validator.messages.alphanumeric
		);

		$.tc_validator.addMethod(
			'lettersonly',
			function( value, element ) {
				return this.optional( element ) || /^[a-z]+$/i.test( value );
			},
			$.tc_validator.messages.lettersonly
		);

		$.tc_validator.addMethod(
			'lettersspaceonly',
			function( value, element ) {
				return this.optional( element ) || /^[a-z,\u0020]+$/i.test( value );
			},
			$.tc_validator.messages.lettersspaceonly
		);

		$.tc_validator.addMethod(
			'alphanumericunicode',
			function( value, element ) {
				return (
					this.optional( element ) ||
					/^[\u0030-\u0039\u0041-\u005A\u0061-\u007A\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u00FF\u0100-\u0148\u014A-\u017F\u0180-\u01BF\u01C4-\u024F\u1E02-\u1EF3\u0370-\u03FF\u0400-\u04FF\u3040-\u309f\u30a0-\u30ff\u4e00-\u9faf\u3400-\u4dbf]+$/i.test(
						value
					)
				);
			},
			$.tc_validator.messages.alphanumericunicode
		);

		$.tc_validator.addMethod(
			'alphanumericunicodespace',
			function( value, element ) {
				return (
					this.optional( element ) ||
					/^[\u0030-\u0039\u0041-\u005A\u0061-\u007A\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u00FF\u0100-\u0148\u014A-\u017F\u0180-\u01BF\u01C4-\u024F\u1E02-\u1EF3\u0370-\u03FF\u0400-\u04FF\u3040-\u309f\u30a0-\u30ff\u4e00-\u9faf\u3400-\u4dbf,\u0020]+$/i.test(
						value
					)
				);
			},
			$.tc_validator.messages.alphanumericunicodespace
		);
	}

	$.epoAPI.util.escapeSelector = ( function() {
		/* original escape string
		 *  /([!"#$%&'()*+,./:;<=>?@[\]^`{|}~])/g;
		 */
		var selectorEscape = /([!"$%&'()*+,/:;<=>?@[\]^`{|}~])/g;
		return function( selector ) {
			return selector.replace( selectorEscape, '\\$1' );
		};
	}() );

	$.epoAPI.util.unformat = function( o ) {
		var a = $.epoAPI.math.unformat( o, tcAPI.localInputDecimalSeparator );
		var n = parseFloat( a );

		if ( ! Number.isFinite( n ) ) {
			return a;
		}
		return n;
	};

	$.epoAPI.util.parseParams = function( string, decode ) {
		if ( typeof string !== 'string' || string.split === undefined ) {
			return [];
		}
		return string
			.split( '&' )
			.map( function( value ) {
				var obj = {};

				if ( decode === true ) {
					value = decodeURIComponent( value.replace( /\+/g, '%20' ) );
				}

				value = value.split( '=' ).map( function( v ) {
					var a = v.split( '?' );

					if ( a.length > 1 ) {
						return a[ 1 ];
					}
					return v;
				} );

				if ( value.length > 1 ) {
					obj[ value[ 0 ] ] = value[ 1 ];
				}

				return obj;
			} )
			.filter( function( n ) {
				return n !== null;
			} )
			.reduce( function( current, next ) {
				return Object.assign( {}, current, next );
			}, {} );
	};

	if ( ! $.tmempty ) {
		$.tmempty = function( obj ) {
			var emptyValues = [ undefined, null, false, 0, '', '0' ];
			var isEmptyValue =
				emptyValues.filter( function( item ) {
					return obj === item;
				} ).length === 1;
			var isEmptyObject = false;

			if ( typeof obj === 'object' ) {
				isEmptyObject =
					Object.keys( obj ).filter( function( key ) {
						return Object.prototype.hasOwnProperty.call( obj, key );
					} ).length === 0;
				return isEmptyObject;
			}

			return isEmptyValue || isEmptyObject;
		};
	}

	if ( ! $.tmType ) {
		$.tmType = function( obj ) {
			return Object.prototype.toString
				.call( obj )
				.match( /\s([a-zA-Z]+)/ )[ 1 ]
				.toLowerCase();
		};
	}

	if ( ! $.is_on_screen ) {
		$.fn.is_on_screen = function() {
			// we don't use jWindow because we want the current window object
			var win = $( window );
			var scroll = $.epoAPI.dom.scroll();
			var bounds = this.offset();
			var viewport = {
				top: scroll.top,
				left: scroll.left
			};

			viewport.right = viewport.left + win.width();
			viewport.bottom = viewport.top + win.height();
			bounds.right = bounds.left + this.outerWidth();
			bounds.bottom = bounds.top + this.outerHeight();

			return ! ( viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom );
		};
	}

	if ( ! $().tmtoggle ) {
		$.fn.tmtoggle = function() {
			var elements = this;
			var is_one_open_for_accordion = false;
			var init_done = 0;

			if ( elements.length === 0 ) {
				return;
			}

			elements.each( function() {
				var t = $( this );
				var headers;
				var wrap;
				var wraps;

				if ( ! t.data( 'tm-toggle-init' ) ) {
					t.data( 'tm-toggle-init', 1 );
					headers = t.find( '.tm-toggle' );
					wrap = t.find( '.tm-collapse-wrap' );
					wraps = $( '.tm-collapse.tmaccordion' ).find( '.tm-toggle' );
					if ( headers.length === 0 || wrap.length === 0 ) {
						return;
					}

					if ( wrap.is( '.closed' ) ) {
						$( wrap ).removeClass( 'closed open' ).addClass( 'closed' ).hide();
						$( headers ).find( '.tm-arrow' ).removeClass( 'tcfa-angle-down tcfa-angle-up' ).addClass( 'tcfa-angle-down' );
						$( headers ).removeClass( 'toggle-header-open toggle-header-closed' ).addClass( 'toggle-header-closed' );
					} else {
						$( wrap ).removeClass( 'closed open' ).addClass( 'open' ).show();
						$( headers ).find( '.tm-arrow' ).removeClass( 'tcfa-angle-down tcfa-angle-up' ).addClass( 'tcfa-angle-up' );
						$( headers ).removeClass( 'toggle-header-open toggle-header-closed' ).addClass( 'toggle-header-open' );
						is_one_open_for_accordion = true;
					}

					headers.each( function( i, header ) {
						$( header ).on( 'closewrap.tmtoggle', function() {
							if ( t.is( '.tmaccordion' ) && $( wrap ).is( '.closed' ) ) {
								return;
							}
							$( wrap ).removeClass( 'closed open' ).addClass( 'closed' );
							$( this ).find( '.tm-arrow' ).removeClass( 'tcfa-angle-down tcfa-angle-up' ).addClass( 'tcfa-angle-down' );
							$( this ).removeClass( 'toggle-header-open toggle-header-closed' ).addClass( 'toggle-header-closed' );
							$( wrap ).removeClass( 'tm-animated fadeIn' );
							if ( t.is( '.tmaccordion' ) ) {
								$( wrap ).animate( { height: 'toggle' }, 100, function() {
									$( wrap ).hide();
								} );
							} else {
								$( wrap ).animate( { height: 'toggle' }, 100, function() {
									$( wrap ).hide();
								} );
							}
							jWindow.trigger( 'tmlazy' );
						} );

						$( header ).on( 'openwrap.tmtoggle', function() {
							if ( t.is( '.tmaccordion' ) ) {
								$( wraps ).not( $( this ) ).trigger( 'closewrap.tmtoggle' );
							}
							$( wrap ).removeClass( 'closed open' ).addClass( 'open' );
							$( this ).find( '.tm-arrow' ).removeClass( 'tcfa-angle-down tcfa-angle-up' ).addClass( 'tcfa-angle-up' );
							$( this ).removeClass( 'toggle-header-open toggle-header-closed' ).addClass( 'toggle-header-open' );
							$( wrap ).show().removeClass( 'tm-animated fadeIn' ).addClass( 'tm-animated fadeIn' );
							setTimeout( function() {
								jWindow.trigger( 'tmlazy' );
							}, 200 );
							if ( init_done && t.is( '.tmaccordion' ) && ! t.is_on_screen() ) {
								jWindow.tcScrollTo( $( header ) );
							}
						} );

						$( header ).on( 'click.tmtoggle', function( e ) {
							e.preventDefault();
							if ( $( wrap ).is( '.closed' ) ) {
								$( this ).trigger( 'openwrap.tmtoggle' );
							} else {
								$( this ).trigger( 'closewrap.tmtoggle' );
							}
						} );

						$( header )
							.find( '.tm-qty' )
							.closest( '.cpf_hide_element' )
							.find( '.tm-epo-field' )
							.on( 'change.cpf', function() {
								$( header ).trigger( 'openwrap.tmtoggle' );
							} );
					} );
				}
			} );
			if ( undefined === window.tc_accordion_closed_on_page_load && ! is_one_open_for_accordion && elements.filter( '.tmaccordion' ).length > 0 ) {
				elements.filter( '.tmaccordion' ).first().find( '.tm-toggle' ).trigger( 'openwrap.tmtoggle' );
			}
			init_done = 1;
			return elements;
		};
	}

	if ( ! $().tmpoplink ) {
		$.fn.tmpoplink = function() {
			var elements = this;
			var floatbox_template;

			if ( elements.length === 0 ) {
				return;
			}

			floatbox_template = function( data ) {
				return $.epoAPI.template.html( wp.template( 'tc-cart-options-popup' ), {
					title: data.title,
					id: data.id,
					html: data.html,
					close: TMEPOJS.i18n_close
				} );
			};

			return elements.each( function() {
				var t = $( this );
				var id;
				var title;
				var html;
				var $_html;

				if ( t.is( '.tc-poplink' ) ) {
					return;
				}
				t.addClass( 'tc-poplink' );
				id = $( this ).attr( 'href' );
				title = $( this ).attr( 'data-title' );
				html = $( id ).html();
				if ( ! title ) {
					title = TMEPOJS.i18n_addition_options;
				}
				$_html = floatbox_template( {
					id: 'temp_for_floatbox_insert',
					html: html,
					title: title
				} );

				t.on( 'click.tmpoplink', function( e ) {
					$.tcFloatBox( {
						fps: 1,
						ismodal: false,
						refresh: 100,
						width: '80%',
						height: '80%',
						classname: 'flasho tm_wrapper',
						data: $_html
					} );

					e.preventDefault();
				} );
			} );
		};
	}

	// Taxes setup
	function get_price_including_tax( price, _cart, element, force ) {
		var taxable;
		var tax_rate;
		var prices_include_tax;
		var is_vat_exempt;
		var non_base_location_prices;
		var taxes_of_one;
		var base_taxes_of_one;
		var modded_taxes_of_one;
		var current_variation;

		if ( ! Number.isFinite( parseFloat( price ) ) ) {
			price = 0;
		}
		price = price * 10000;
		if ( _cart ) {
			taxable = _cart.attr( 'data-taxable' );
			tax_rate = _cart.attr( 'data-tax-rate' );
			prices_include_tax = _cart.attr( 'data-prices-include-tax' );
			is_vat_exempt = _cart.attr( 'data-is-vat-exempt' );
			non_base_location_prices = _cart.attr( 'data-non-base-location-prices' );
			taxes_of_one = _cart.attr( 'data-taxes-of-one' );
			base_taxes_of_one = _cart.attr( 'data-base-taxes-of-one' );
			modded_taxes_of_one = _cart.attr( 'data-modded-taxes-of-one' );

			if ( _cart.data( 'current_variation' ) !== undefined ) {
				current_variation = _cart.data( 'current_variation' );
				taxable = current_variation.tc_is_taxable;
				tax_rate = current_variation.tc_tax_rate;
				non_base_location_prices = current_variation.tc_non_base_location_prices;
				taxes_of_one = current_variation.tc_taxes_of_one;
				base_taxes_of_one = current_variation.tc_base_taxes_of_one;
				modded_taxes_of_one = current_variation.tc_modded_taxes_of_one;
			}

			if ( element ) {
				if ( element.data( 'tax-obj' ) ) {
					tax_rate = element.data( 'tax-obj' );
					if ( tax_rate.has_fee === 'no' ) {
						taxable = false;
					} else if ( tax_rate.has_fee === 'yes' ) {
						taxable = true;
					}
					tax_rate = tax_rate.tax_rate;
				}
			}
			if ( taxable ) {
				if ( prices_include_tax === '1' && ! force ) {
					if ( is_vat_exempt === '1' ) {
						if ( non_base_location_prices === '1' ) {
							price = parseFloat( price ) - ( taxes_of_one * price );
						} else {
							price = parseFloat( price ) - ( base_taxes_of_one * price );
						}
					} else if ( non_base_location_prices === '1' ) {
						price = parseFloat( price ) - ( base_taxes_of_one * price ) + ( modded_taxes_of_one * price );
					}
				} else {
					price = parseFloat( price ) * ( 1 + ( tax_rate / 100 ) );
				}
			}
		}
		price = price / 10000;

		return price;
	}

	function get_price_excluding_tax( price, _cart, element, force ) {
		var taxable;
		var tax_rate;
		var base_taxes_of_one;
		var prices_include_tax;
		var current_variation;

		if ( ! Number.isFinite( parseFloat( price ) ) ) {
			price = 0;
		}
		price = price * 10000;

		if ( _cart ) {
			taxable = _cart.attr( 'data-taxable' );
			tax_rate = _cart.attr( 'data-tax-rate' );
			base_taxes_of_one = _cart.attr( 'data-base-taxes-of-one' );
			prices_include_tax = _cart.attr( 'data-prices-include-tax' );

			if ( _cart.data( 'current_variation' ) !== undefined ) {
				current_variation = _cart.data( 'current_variation' );
				taxable = current_variation.tc_is_taxable;
				tax_rate = current_variation.tc_tax_rate;
				base_taxes_of_one = current_variation.tc_base_taxes_of_one;
			}
			if ( element ) {
				if ( element.data( 'tax-obj' ) ) {
					tax_rate = element.data( 'tax-obj' );
					if ( tax_rate.has_fee === 'no' ) {
						taxable = false;
					} else if ( tax_rate.has_fee === 'yes' ) {
						taxable = true;
					}
					tax_rate = tax_rate.tax_rate;
				}
			}

			if ( ( taxable && prices_include_tax === '1' ) || force ) {
				price = parseFloat( price ) - parseFloat( base_taxes_of_one * price );
			}
		}
		price = price / 10000;

		return price;
	}

	function tm_set_tax_price( value, _cart, element ) {
		var tax_display_mode;

		if ( ! Number.isFinite( parseFloat( value ) ) ) {
			value = 0;
		}
		if ( _cart ) {
			tax_display_mode = _cart.attr( 'data-tax-display-mode' );
			if ( tax_display_mode === 'incl' ) {
				value = get_price_including_tax( value, _cart, element );
			} else {
				value = get_price_excluding_tax( value, _cart, element );
			}
		}
		return value;
	}

	// Return a formatted currency value
	function formatPrice( value, args ) {
		var data;
		if ( ! args ) {
			args = {};
		}
		data = $.extend( {
			symbol: '',
			format: '',
			decimal: tcAPI.localDecimalSeparator,
			thousand: tcAPI.localThousandSeparator,
			precision: TMEPOJS.currency_format_num_decimals
		}, args );

		return $.epoAPI.math.format( value, data );
	}

	// Return a formatted currency value
	function tm_set_price_( value, sign, inc_tax_string ) {
		return (
			sign +
			formatPrice( value, { symbol: TMEPOJS.currency_format_symbol, format: TMEPOJS.currency_format } ) +
			inc_tax_string
		);
	}

	// Return a formatted currency value
	function tm_set_price( value, _cart, notax, taxstring, element ) {
		var inc_tax_string = '';
		var val;
		var sign = TMEPOJS.plus_sign + ' ';

		if ( ! notax ) {
			value = tm_set_tax_price( value, _cart, element );
		}

		val = Math.abs( value );

		if ( TMEPOJS.tm_epo_global_options_price_sign === 'minus' ) {
			sign = '';
		}
		if ( value < 0 ) {
			sign = TMEPOJS.minus_sign + ' ';
		}

		if ( _cart && taxstring ) {
			inc_tax_string = _cart.attr( 'data-tax-string' );
		}
		if ( inc_tax_string === undefined ) {
			inc_tax_string = '';
		}

		return tm_set_price_( val, sign, inc_tax_string );
	}

	// FloatingTotalsBox plugin
	FloatingTotalsBox = function( this_epo_totals_container, is_quickview, main_cart ) {
		this.this_epo_totals_container = this_epo_totals_container;
		this.is_quickview = is_quickview;
		this.main_cart = main_cart;

		if ( ! is_quickview && TMEPOJS.floating_totals_box && TMEPOJS.floating_totals_box !== 'disable' && main_cart && this_epo_totals_container.length ) {
			this.init();
			return this;
		}

		return false;
	};

	FloatingTotalsBox.prototype = {
		constructor: FloatingTotalsBox,

		onUpdate: function() {
			var tm_epo_totals_html = this.this_epo_totals_container.data( 'tm-html' );
			var tm_floating_box_data = this.this_epo_totals_container.data( 'tm-floating-box-data' );
			var values_obj = [];
			var floatingBoxHtml;
			var floatingBoxaddToCartButton;

			if ( tm_floating_box_data && tm_floating_box_data.length ) {
				$.each( tm_floating_box_data, function( i, row ) {
					if ( row.title === '' ) {
						row.title = '&nbsp;';
					}
					if ( row.value === '' ) {
						row.value = '&nbsp;';
					}
					if ( ! row.title ) {
						row.title = '&nbsp;';
					} else {
						row.title = $( '<div>' + row.title + '</div>' );
						row.title.find( 'span' ).remove();
						row.title = row.title.html();
					}

					if ( this.is_nks ) {
						if ( row.label_show !== '' ) {
							row.title = '';
						}
						if ( row.value_show !== '' ) {
							row.value = '';
						}
					}

					values_obj.push( {
						label_show: row.label_show,
						value_show: row.value_show,
						title: row.title,
						value: row.value,
						quantity: row.quantity,
						price: tm_set_price( row.price, this.this_epo_totals_container, true, false )
					} );
				} );
			}

			if ( ! ( ( tm_epo_totals_html && tm_epo_totals_html !== '' ) || this.is_nks ) ) {
				tm_epo_totals_html = '';
				this.floatingBox.hide();
			}
			if ( values_obj && ! values_obj.length ) {
				values_obj.push( {
					label_show: 'hidden',
					value_show: 'hidden',
					title: '',
					value: '',
					quantity: 0,
					price: 0
				} );
			}

			floatingBoxHtml = $.epoAPI.template.html( this.engineTemplate, {
				html_before: TMEPOJS.floating_totals_box_html_before,
				html_after: TMEPOJS.floating_totals_box_html_after,
				option_label: TMEPOJS.i18n_option_label,
				option_value: TMEPOJS.i18n_option_value,
				option_qty: TMEPOJS.i18n_option_qty,
				option_price: TMEPOJS.i18n_option_price,
				values: values_obj,
				totals: tm_epo_totals_html
			} );

			this.floatingBox.html( floatingBoxHtml );
			this.onUpdateScroll();

			if ( TMEPOJS.floating_totals_box_add_button === 'yes' ) {
				floatingBoxaddToCartButton = this.main_cart.find( tcAPI.addToCartButtonSelector ).first();
				floatingBoxaddToCartButton
					.tcClone()
					.addClass( 'tc-add-to-cart-button' )
					.on( 'click', function() {
						floatingBoxaddToCartButton.trigger( 'click' );
					} )
					.appendTo( this.floatingBox );
			}
		},

		onUpdateScroll: function() {
			if ( TMEPOJS.floating_totals_box_visibility === 'always' ) {
				this.floatingBox.show();
				return;
			}

			if ( jWindow.scrollTop() > $.epoAPI.math.toFloat( TMEPOJS.floating_totals_box_pixels ) || this.is_nks_alt ) {
				if ( ( this.floatingBox.is( ':hidden' ) && ! this.floatingBox.is( ':empty' ) ) || this.is_nks_alt ) {
					if ( this.is_nks === false ) {
						this.floatingBox.fadeIn();
					} else {
						this.floatingBox.show();
					}
				} else if ( ! this.floatingBox.is( ':hidden' ) && this.floatingBox.is( ':empty' ) ) {
					if ( this.is_nks === false ) {
						this.floatingBox.fadeOut();
					} else {
						this.floatingBox.hide();
					}
				}
			} else if ( ! this.floatingBox.is( ':hidden' ) ) {
				if ( this.is_nks === false ) {
					this.floatingBox.fadeOut();
				} else {
					this.floatingBox.hide();
				}
			}
		},

		addEvents: function() {
			this.onUpdate();

			this.main_cart.on( 'tm-epo-after-update', this.onUpdate.bind( this ) );

			if ( this.is_nks === false ) {
				jWindow.on( 'scroll', this.onUpdateScroll.bind( this ) );
			}
		},

		init: function() {
			this.floatingBox = $( '<div class="tm-floating-box ' + TMEPOJS.floating_totals_box + '"></div>' );
			this.nks_selector = $( '.tm-floating-box-nks' ).first();
			this.alt_selector = $( '.tm-floating-box-alt' ).first();
			this.engineTemplate = tcAPI.templateEngine.tc_floating_box;
			this.is_nks = false;
			this.is_nks_alt = false;

			if ( this.nks_selector.length > 0 ) {
				this.is_nks = true;
				this.floatingBox.removeClass( 'top left right bottom' ).appendTo( this.nks_selector ).show();
			} else if ( this.alt_selector.length > 0 ) {
				this.floatingBox.removeClass( 'top left right bottom' ).appendTo( this.alt_selector ).hide();
			} else {
				this.floatingBox.appendTo( 'body' ).hide();
			}

			if ( this.nks_selector.length > 0 || this.alt_selector.length > 0 ) {
				this.is_nks_alt = true;
				this.engineTemplate = tcAPI.templateEngine.tc_floating_box_nks;
			}

			this.addEvents();
		}
	};

	$.tcFloatingTotalsBox = function( this_epo_totals_container, is_quickview, main_cart ) {
		var data = false;

		if ( this_epo_totals_container && this_epo_totals_container.length && this_epo_totals_container.data( 'tcfloatingtotalsbox' ) === undefined ) {
			data = new FloatingTotalsBox( this_epo_totals_container, is_quickview, main_cart );
			this_epo_totals_container.data( 'tcfloatingtotalsbox', data );
		}

		return data;
	};

	$.tc_product_image = {};
	$.tc_product_image_store = {};

	// replace obj1 values with obj2 values
	$.tc_replace_object_values = function( obj1, obj2 ) {
		Object.keys( obj1 ).forEach( function( x ) {
			Object.keys( obj1[ x ] ).forEach( function( attr ) {
				if ( undefined !== obj2[ x ] && undefined !== obj2[ x ][ attr ] && Object.prototype.hasOwnProperty.call( obj2[ x ], attr ) ) {
					obj1[ x ][ attr ] = obj2[ x ][ attr ];
				}
			} );
		} );
		return obj1;
	};
	// copy obj2 values to obj1
	$.tc_maybe_copy_object_values = function( obj1, obj2 ) {
		Object.keys( obj2 ).forEach( function( x ) {
			Object.keys( obj2[ x ] ).forEach( function( attr ) {
				if ( undefined !== obj2[ x ] && Object.prototype.hasOwnProperty.call( obj2[ x ], attr ) && undefined !== obj2[ x ][ attr ] && ( undefined === obj1[ x ] || undefined === obj1[ x ][ attr ] ) ) {
					if ( undefined === obj1[ x ] ) {
						obj1[ x ] = {};
					}
					obj1[ x ][ attr ] = obj2[ x ][ attr ];
				}
			} );
		} );
		return obj1;
	};

	$.tc_pre_populate_store = function() {
		var obj = {};

		obj[ 0 ] = {};
		obj[ 1 ] = {};
		obj[ 2 ] = {};
		obj[ 3 ] = {};

		obj[ 0 ].src = '';
		obj[ 0 ].srcset = '';
		obj[ 0 ].sizes = '';
		obj[ 0 ].title = '';
		obj[ 0 ].alt = '';
		obj[ 0 ][ 'data-src' ] = '';
		obj[ 0 ][ 'data-large_image' ] = '';
		obj[ 0 ][ 'data-large_image_width' ] = '';
		obj[ 0 ][ 'data-large_image_height' ] = '';
		obj[ 1 ][ 'data-thumb' ] = '';
		obj[ 2 ].src = '';
		obj[ 3 ].href = '';
		obj[ 3 ].title = '';

		return obj;
	};

	$.tc_populate_store = function( img, product_element ) {
		var $gallery_img = product_element.find( '.flex-control-nav li:eq(0) img' );
		var $gallery_wrapper = product_element.find( '.woocommerce-product-gallery__wrapper ' );
		var $product_img_wrap = $gallery_wrapper.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 );
		var $product_img = img;
		var $product_link = img.closest( 'a' );
		var obj = {};

		obj[ 0 ] = {};
		obj[ 1 ] = {};
		obj[ 2 ] = {};
		obj[ 3 ] = {};

		obj[ 0 ].src = $product_img.attr( 'src' );
		obj[ 0 ].srcset = $product_img.attr( 'srcset' );
		obj[ 0 ].sizes = $product_img.attr( 'sizes' );
		obj[ 0 ].title = $product_img.attr( 'title' );
		obj[ 0 ].alt = $product_img.attr( 'alt' );
		obj[ 0 ][ 'data-src' ] = $product_img.attr( 'data-src' );
		obj[ 0 ][ 'data-large_image' ] = $product_img.attr( 'data-large_image' );
		obj[ 0 ][ 'data-large_image_width' ] = $product_img.attr( 'data-large_image_width' );
		obj[ 0 ][ 'data-large_image_height' ] = $product_img.attr( 'data-large_image_height' );
		obj[ 1 ][ 'data-thumb' ] = $product_img_wrap.attr( 'data-thumb' );
		obj[ 2 ].src = $gallery_img.attr( 'src' );
		obj[ 3 ].href = $product_link.attr( 'href' );
		obj[ 3 ].title = $product_link.attr( 'title' );

		return obj;
	};

	$.tc_maybe_copy_object_values_from_img = function( obj1, img, product_element ) {
		var $gallery_img = product_element.find( '.flex-control-nav li:eq(0) img' );
		var $gallery_wrapper = product_element.find( '.woocommerce-product-gallery__wrapper ' );
		var $product_img_wrap = $gallery_wrapper.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 );
		var $product_img = img;
		var $product_link = img.closest( 'a' );
		var attrs;
		var attr;
		var attrs_product_img = [ 'src', 'srcset', 'sizes', 'title', 'alt', 'data-src', 'data-large_image', 'data-large_image_width', 'data-large_image_height', 'large-image' ];
		var attrs_product_img_wrap = [ 'data-thumb' ];
		var attrs_gallery_img = [ 'src' ];
		var attrs_product_link = [ 'href', 'title' ];
		var all = [ $product_img, $product_img_wrap, $gallery_img, $product_link ];
		var attrs_all = [ attrs_product_img, attrs_product_img_wrap, attrs_gallery_img, attrs_product_link ];
		all.forEach( function( item, index ) {
			if ( undefined !== item && undefined !== item[ 0 ] ) {
				attrs = item[ 0 ].attributes;

				$.each( attrs, function() {
					if ( this.specified ) {
						attr = this.name;

						if ( $.inArray( attr, attrs_all[ index ] ) !== -1 && ( undefined === obj1[ index ] || ( undefined !== obj1[ index ] && undefined === obj1[ index ][ attr ] ) ) ) {
							if ( undefined === obj1[ index ] ) {
								obj1[ index ] = {};
							}
							obj1[ index ][ attr ] = this.value;
						}
					}
				} );
			}
		} );

		return obj1;
	};

	/**
	 * Stores a default attribute for an element so it can be reset later
	 */
	$.fn.tc_set_attr = function( attr, value, id ) {
		if ( undefined === id ) {
			id = 0;
		}
		if ( undefined === $.tc_product_image[ id ] || ( undefined !== $.tc_product_image[ id ] && undefined === $.tc_product_image[ id ][ attr ] ) ) {
			if ( undefined === $.tc_product_image[ id ] ) {
				$.tc_product_image[ id ] = {};
			}
			$.tc_product_image[ id ][ attr ] = '';
			if ( this.attr( attr ) ) {
				$.tc_product_image[ id ][ attr ] = this.attr( attr );
			}
		}
		if ( false === value ) {
			this.removeAttr( attr );
		} else {
			this.attr( attr, value );
		}
	};

	/**
	 * Reset a default attribute for an element so it can be reset later
	 */
	$.fn.tc_reset_attr = function( attr, id ) {
		if ( undefined === id ) {
			id = 0;
		}
		if ( undefined === $.tc_product_image[ id ] ) {
			return;
		}
		if ( undefined !== $.tc_product_image[ id ][ attr ] ) {
			this.attr( attr, $.tc_product_image[ id ][ attr ] );
		}
		delete $.tc_product_image[ id ][ attr ];
	};

	$.fn.tc_update_attr = function( attr, id ) {
		if ( undefined === id ) {
			id = 0;
		}
		if ( undefined !== $.tc_product_image[ id ] ) {
			$.tc_product_image[ id ][ attr ] = this.attr( attr );
		}
	};

	$.fn.tc_image_update = function( dom, image ) {
		var element = $( dom );
		var $form = this;
		var $image = $( image );
		var epo_object = $form.data( 'epo_object' );
		var image_info;
		var $product_img;
		var product_element = epo_object.main_product.closest( '#product-' + epo_object.product_id );
		var $product_element = product_element;
		var $product_link;
		var use_image_info;

		if ( product_element.length <= 0 ) {
			$product_element = epo_object.main_product.closest( '.post-' + epo_object.product_id );
		}

		if ( element.is( 'select' ) ) {
			element = element.children( 'option:selected' );
		}
		image_info = element.data( 'image-variations' );

		if ( TMEPOJS.tm_epo_global_product_image_selector !== '' ) {
			$product_img = $( TMEPOJS.tm_epo_global_product_image_selector );
		} else {
			$product_img = $product_element.find( 'a.woocommerce-main-image img, img.woocommerce-main-image,a img' ).not( '.thumbnails img,.product_list_widget img,img.emoji,a.woocommerce-product-gallery__trigger img' ).first();
		}
		$product_link = $product_img.closest( 'a' );

		if ( $product_img.length > 1 ) {
			$product_img = $product_img.first();
		}

		if ( element && image_info && $image.length > 0 ) {
			$image.removeAttr( 'data-o_src' ).removeAttr( 'data-o_title' ).removeAttr( 'data-o_alt' ).removeAttr( 'data-o_srcset' ).removeAttr( 'data-o_sizes' ).removeAttr( 'srcset' ).removeAttr( 'sizes' );

			use_image_info = image_info.imagep;
			if ( ! image_info.imagep.image_link ) {
				use_image_info = image_info.image;
			}

			$image.attr( 'title', use_image_info.image_title );
			$image.attr( 'alt', use_image_info.image_alt );
			if ( use_image_info.image_srcset ) {
				$image.attr( 'srcset', use_image_info.image_srcset );
			}
			if ( use_image_info.image_sizes ) {
				$image.attr( 'sizes', use_image_info.image_sizes );
			}

			$product_img.tc_set_attr( 'title', use_image_info.image_title );
			$product_img.tc_set_attr( 'alt', use_image_info.image_alt );

			$product_img.tc_set_attr( 'data-large-image', use_image_info.image_link );
			if ( $product_img.data.wc27_zoom_target ) {
				$product_img.data.wc27_zoom_target.tc_set_attr( 'data-thumb', use_image_info.image_link );
				$product_element.find( '.flex-control-nav li:eq(0) img' ).tc_set_attr( 'src', use_image_info.image_link );
			}

			$product_link.tc_set_attr( 'href', use_image_info.image_link );
			$product_link.tc_set_attr( 'title', use_image_info.image_caption );
		} else {
			$product_img.tc_reset_attr( 'title' );
			$product_img.tc_reset_attr( 'alt' );

			$product_img.tc_reset_attr( 'data-large-image' );
			if ( $product_img.data.wc27_zoom_target ) {
				$product_img.data.wc27_zoom_target.tc_reset_attr( 'data-thumb' );
				$product_element.find( '.flex-control-nav li:eq(0) img' ).tc_reset_attr( 'src' );
			}

			$product_link.tc_reset_attr( 'href' );
			$product_link.tc_reset_attr( 'title' );
		}
	};

	// variations checker
	$.fn.tm_find_matching_variations = function( product_variations, settings ) {
		var matching = [];
		var i;
		var variation;

		if ( product_variations ) {
			for ( i = 0; i < product_variations.length; i += 1 ) {
				variation = product_variations[ i ];

				if ( $.fn.tm_variations_match( variation.attributes, settings ) ) {
					matching.push( variation );
				}
			}
		}

		return matching;
	};

	$.fn.tm_variations_match = function( attrs1, attrs2 ) {
		var match = true;
		var val1;
		var val2;

		Object.keys( attrs1 ).forEach( function( x ) {
			if ( Object.prototype.hasOwnProperty.call( attrs1, x ) ) {
				val1 = attrs1[ x ];
				val2 = attrs2[ x ];

				if ( val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2 ) {
					match = false;
				}
			}
		} );

		return match;
	};

	function get_element_from_field( element ) {
		var $element = $( element );
		var data_uniqid;
		var the_epo_id;
		var _class;
		var epoContainer;

		if ( $element.length === 0 ) {
			return;
		}

		if ( $element.is( '.cpf-section' ) ) {
			return element.find( '.tm-epo-field' );
		}
		data_uniqid = $element.attr( 'data-uniqid' );
		epoContainer = $element.closest( '.tc-extra-product-options' );
		the_epo_id = epoContainer.attr( 'data-epo-id' );

		if ( ! epoContainer.is( '.reactivate' ) && tcAPI.getElementFromFieldCache && tcAPI.getElementFromFieldCache[ the_epo_id ] && tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] ) {
			return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];
		}
		_class = $element
			.attr( 'class' )
			.split( ' ' )
			.map( function( cls ) {
				if ( cls.indexOf( 'cpf-type-', 0 ) !== -1 ) {
					return cls;
				}
				return null;
			} )
			.filter( function( v ) {
				if ( v !== null && v !== undefined ) {
					return v;
				}
				return null;
			} );

		if ( _class.length > 0 ) {
			_class = _class[ 0 ];
			if ( _class === 'cpf-type-product' ) {
				if ( $element.is( '.cpf-type-product-mode-product' ) ) {
					_class = 'cpf-type-checkbox';
				} else if ( $element.is( '.cpf-type-product-dropdown' ) ) {
					_class = 'cpf-type-select';
				} else {
					_class = 'cpf-type-radio';
				}
			}
			switch ( _class ) {
				case 'cpf-type-radio':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-radio' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-checkbox':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-checkbox' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-select':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-select' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-textarea':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-textarea' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-textfield':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-textfield' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-color':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tm-color-picker' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-range':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-range' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-date':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.find( '.tm-epo-field.tmcp-date' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];

				case 'cpf-type-variations':
					tcAPI.getElementFromFieldCache[ the_epo_id ] = [];
					tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ] = $element.closest( '.cpf-section' ).find( '.tm-epo-field.tm-epo-variation-element' );
					return tcAPI.getElementFromFieldCache[ the_epo_id ][ data_uniqid ];
			}
		}
	}

	// tc-lightbox
	if ( ! $().tclightbox ) {
		$.fn.tclightbox = function() {
			var elements = this;

			if ( elements.length === 0 ) {
				return;
			}

			return elements.each( function() {
				var $this = $( this );
				var _imgsrc;
				var _label;
				var _input;
				var tclightboxwrap;
				var _img_button;
				var preload_img;

				if ( $this.is( '.tcinit' ) ) {
					return;
				}
				_imgsrc = $this.attr( 'src' ) || $this.attr( 'data-original' );
				_label = $this.closest( 'label' );
				_input = _label.closest( '.tmcp-field-wrap' ).find( ".tm-epo-field[id='" + _label.attr( 'for' ) + "']" );
				_imgsrc = _input.attr( 'data-imagel' ) || _input.attr( 'data-imagep' ) || _input.attr( 'data-image' ) || _imgsrc;

				if ( ! _imgsrc ) {
					return;
				}

				$this.addClass( 'tcinit' ).before( $.epoAPI.template.html( tcAPI.templateEngine.tc_lightbox, {} ) );
				tclightboxwrap = $this.prev();

				$this.wrap( "<div class='tc-lightbox-image-wrap'/>" );
				$this.after( tclightboxwrap );

				_img_button = tclightboxwrap.find( '.tc-lightbox-button' );
				preload_img = new Image();
				preload_img.src = _imgsrc;
				preload_img.onload = function() {
					_img_button.addClass( 'tcinit' ).on( 'click.tclightbox', function( buttonevent ) {
						var size;
						var _img;

						if ( $( '.tc-closing.tc-lightbox' ).length > 0 ) {
							return;
						}

						size = $.epoAPI.dom.size();
						_img = $( '<img>' )
							.addClass( 'tc-lightbox-img' )
							.attr( 'src', _imgsrc )
							.css( 'maxHeight', size.visibleHeight + 'px' )
							.css( 'maxWidth', size.visibleWidth + 'px' );

						$.tcFloatBox( {
							fps: 1,
							ismodal: false,
							refresh: 'fixed',
							width: 'auto',
							height: 'auto',
							top: '0%',
							left: '0%',
							classname: 'flasho tc-lightbox',
							animateIn: 'tc-lightbox-zoomIn',
							animateOut: 'tc-lightbox-zoomOut',
							data: $.epoAPI.template.html( tcAPI.templateEngine.tc_lightbox_zoom, { img: _img[ 0 ].outerHTML } ),
							zIndex: 102001,
							cancelClass: '.tc-lightbox-img, .tc-lightbox-button-close',
							unique: true
						} );

						buttonevent.preventDefault();
					} );
				};
			} );
		};
	}

	// Start Section popup
	if ( ! $().tmsectionpoplink ) {
		$.fn.tmsectionpoplink = function() {
			var elements = this;

			if ( elements.length === 0 ) {
				return;
			}

			return elements.each( function() {
				var $this = $( this );
				var id;
				var title;
				var section;
				var clicked;
				var _ovl;
				var cancelfunc;

				if ( $this.data( 'tmsectionpoplink' ) ) {
					return;
				}

				$this.data( 'tmsectionpoplink', true );
				id = $this.attr( 'data-sectionid' );
				title = TMEPOJS.i18n_addition_options;
				section = $this.closest( ".cpf-section[data-uniqid='" + id + "']" );
				clicked = false;
				_ovl = $( '<div class="fl-overlay"></div>' ).css( {
					zIndex: parseInt( $this.zIndex, 10 ) - 1,
					opacity: 0.8
				} );
				cancelfunc = function() {
					var pop = $( '#tm-section-pop-up' );
					pop.parents().removeClass( 'noanimated' );

					_ovl.unbind().remove();
					pop.after( section );
					pop.remove();

					section.find( '.tm-section-link' ).show();
					section.find( '.tm-section-pop' ).hide();
				};

				if ( $this.attr( 'data-title' ) ) {
					title = $this.attr( 'data-title' );
				}

				$this.on( 'click.tmsectionpoplink', function( e ) {
					var pop;

					e.preventDefault();
					clicked = false;
					_ovl.appendTo( 'body' ).on( 'click', cancelfunc );

					section.before(
						$.epoAPI.template.html( tcAPI.templateEngine.tc_section_pop_link, {
							title: title,
							close: TMEPOJS.i18n_close
						} )
					);

					pop = $( '#tm-section-pop-up' );

					pop.find( '.float_editbox' ).prepend( section );

					section.find( '.tm-section-link' ).hide();
					section.find( '.tm-section-pop' ).show();

					pop.parents().addClass( 'noanimated' );

					pop.find( '.floatbox-cancel' ).on( 'click', function() {
						if ( clicked ) {
							return;
						}
						clicked = true;
						cancelfunc();
					} );
					jWindow.trigger( 'tmlazy' );
					jWindow.trigger( 'tmsectionpoplink' );
				} );
			} );
		};
	} // End Section popup

	function getVariationIdSelector( currentCart ) {
		var variationIdSelector = "input[name^='variation_id']";

		if ( currentCart.find( 'input.variation_id' ).length > 0 ) {
			variationIdSelector = 'input.variation_id';
		}

		return variationIdSelector;
	}

	function getVariationIdElement( currentCart, not ) {
		var variationIdSelector = getVariationIdSelector( currentCart );
		var variationIdElement = currentCart.find( variationIdSelector );

		if ( not ) {
			variationIdElement = variationIdElement.not( not );
		}

		return variationIdElement;
	}

	function getCurrentVariation( currentCart ) {
		return currentCart.find( getVariationIdSelector( currentCart ) ).val() || 0;
	}

	function getQtyElement( currentCart ) {
		return currentCart.find( tcAPI.qtySelector ).last();
	}

	function getCurrentQty( currentCart ) {
		return parseFloat( getQtyElement( currentCart ).val() );
	}

	function add_variation_event( name, selector, func ) {
		lateVariationEvent[ lateVariationEvent.length ] = {
			name: name,
			selector: selector,
			func: func
		};
	}

	function field_is_active( field, nochecks ) {
		var hideElement;
		var singleField;

		field = $( field );
		if ( field.is( '.cpf_hide_element' ) ) {
			hideElement = field;
			field = field.find( '.tmcp-field, .tmcp-fee-field' );
		} else {
			hideElement = field.closest( '.cpf_hide_element' );
		}

		if ( hideElement.data( 'isactive' ) !== false && hideElement.closest( '.cpf-section' ).data( 'isactive' ) !== false ) {
			singleField = field.first();
			field.prop( 'disabled', false );

			if ( TMEPOJS.tm_epo_show_only_active_quantities !== 'yes' ) {
				if ( singleField.is( ':radio, .cpf-type-radio' ) || singleField.is( ':checkbox, .cpf-type-checkbox' ) ) {
					field.filter( ':checked' ).closest( '.tmcp-field-wrap' ).find( '.tm-qty' ).prop( 'disabled', false );
					field.not( ':checked' ).closest( '.tmcp-field-wrap' ).find( '.tm-qty' ).prop( 'disabled', true );
				} else if ( singleField.is( 'select, .cpf-type-select' ) ) {
					if ( singleField.val() ) {
						hideElement.find( '.tm-qty' ).prop( 'disabled', false );
					} else {
						hideElement.find( '.tm-qty' ).prop( 'disabled', true );
					}
				} else if ( singleField.val() ) {
					hideElement.find( '.tm-qty' ).prop( 'disabled', false );
				} else {
					hideElement.find( '.tm-qty' ).prop( 'disabled', true );
				}
			} else if ( ! nochecks ) {
				hideElement.find( '.tm-quantity' ).trigger( 'showhide.cpfcustom' );
			}

			if ( ! singleField.is( '.cpf_hide_element' ) ) {
				field.removeClass( 'tcdisabled' ).addClass( 'tcenabled' );

				if ( field.is( '.tmcp-upload' ) ) {
					if ( field.next( '.tmcp-upload-hidden' ).length ) {
						field.next( '.tmcp-upload-hidden' ).removeClass( 'tcdisabled' ).addClass( 'tcenabled' ).prop( 'disabled', false );
					}
				}
			}

			hideElement.removeClass( 'tc-container-disabled' ).addClass( 'tc-container-enabled' );

			field.trigger( {
				type: 'tm-field-is-active',
				field: field,
				value: true
			} );

			return true;
		}

		if ( ! field.is( '.cpf_hide_element' ) ) {
			field.prop( 'disabled', true ).removeClass( 'tcenabled' ).addClass( 'tcdisabled' );
			hideElement.find( '.tm-qty' ).prop( 'disabled', true );
			if ( field.is( '.tmcp-upload' ) ) {
				if ( field.next( '.tmcp-upload-hidden' ).length ) {
					field.next( '.tmcp-upload-hidden' ).removeClass( 'tcenabled' ).addClass( 'tcdisabled' ).prop( 'disabled', true );
				}
			}
		}

		hideElement.removeClass( 'tc-container-enabled' ).addClass( 'tc-container-disabled' );

		field.trigger( {
			type: 'tm-field-is-active',
			field: field,
			value: false
		} );

		return false;
	}

	function tm_variation_check_match( element, val2, operator ) {
		var $element = $( element );
		var epoId = $element.attr( 'data-epo_id' );
		var productId = $element.attr( 'data-product_id' );

		var variationsForm = $( ".variations_form[data-epo_id='" + epoId + "'][data-product_id='" + productId + "']" );
		var val1;
		var variationIdSelector = "input[name^='variation_id']";
		var $variationId;

		variationsForm = $.epoAPI.applyFilter( 'tm_variation_check_match_variationsForm', variationsForm, epoId, productId, $element );

		if ( variationsForm.length === 0 ) {
			return false;
		}

		$variationId = variationsForm.find( variationIdSelector );

		if ( $variationId.length === 0 ) {
			variationIdSelector = 'input.variation_id';

			$variationId = variationsForm.find( variationIdSelector );

			if ( $variationId.length === 0 ) {
				$variationId = variationsForm.closest( '.tc-epo-element-product-li-container' ).find( '.product-variation-id' );
			}
		}

		if ( element !== null && val2 !== null && element !== undefined && val2 !== undefined && element !== false && val2 !== false ) {
			if ( val2 ) {
				val2 = parseInt( val2, 10 );
			} else {
				val2 = -1;
			}
		}

		val1 = parseInt( $variationId.val(), 10 );

		if ( ! Number.isFinite( val1 ) ) {
			val1 = '';
		}
		if ( ! Number.isFinite( val2 ) ) {
			val2 = '';
		}

		switch ( operator ) {
			case 'is':
				return val1 !== '' && val1 === val2;

			case 'isnot':
				return val1 !== '' && val1 !== val2;

			case 'isempty':
				return val1 === '' || val1 === 0;

			case 'isnotempty':
				return val1 !== '' && val1 !== 0;

			case 'startswith':
				return val1.toString().startsWith( val2 );

			case 'endswith':
				return val1.toString().endsWith( val2 );

			case 'greaterthan':
				return parseFloat( val1 ) > parseFloat( val2 );

			case 'lessthan':
				return parseFloat( val1 ) < parseFloat( val2 );

			case 'greaterthanequal':
				return parseFloat( val1 ) >= parseFloat( val2 );

			case 'lessthanequal':
				return parseFloat( val1 ) <= parseFloat( val2 );
		}
		return false;
	}

	function tm_check_match( val1, val2, operator ) {
		if ( val1 !== null && val2 !== null ) {
			val1 = encodeURIComponent( val1 );
			if ( $.qtranxj_split ) {
				//backwards compatible
				val2 = encodeURIComponent( $.qtranxj_split( decodeURIComponent( val2 ) )[ TMEPOQTRANSLATEXJS.language ] );
			} else {
				//backwards compatible
				val2 = encodeURIComponent( decodeURIComponent( val2 ) );
			}

			if ( val1 ) {
				val1 = val1.toLowerCase();
			} else {
				val1 = '';
			}
			if ( val2 ) {
				val2 = val2.toLowerCase();
			} else {
				val2 = '';
			}
		} else {
			return false;
		}

		val1 = val1.toString();
		val2 = val2.toString();

		switch ( operator ) {
			case 'is':
				return val1 !== null && val1 === val2;

			case 'isnot':
				return val1 !== null && val1 !== val2;

			case 'isempty':
				return ! ( val1 !== 'undefined' && val1 !== undefined && val1 !== '' );

			case 'isnotempty':
				return val1 !== 'undefined' && val1 !== undefined && val1 !== '';

			case 'startswith':
				return val1.startsWith( val2 );

			case 'endswith':
				return val1.endsWith( val2 );

			case 'greaterthan':
				return parseFloat( val1 ) > parseFloat( val2 );

			case 'lessthan':
				return parseFloat( val1 ) < parseFloat( val2 );

			case 'greaterthanequal':
				return parseFloat( val1 ) >= parseFloat( val2 );

			case 'lessthanequal':
				return parseFloat( val1 ) <= parseFloat( val2 );
		}

		return false;
	}

	function tm_check_section_match( elements, operator ) {
		var all_checked = true;
		var val;
		var all_elements = elements.find( '.cpf_hide_element' );
		var radio_checked;
		var checkbox_checked;
		var noSplit = false;

		if ( elements.is( '.tc-hidden' ) ) {
			if ( operator === 'isnotempty' ) {
				return false;
			} else if ( operator === 'isempty' ) {
				return true;
			}
		}

		$( all_elements ).each( function( j, element ) {
			var _class;

			element = $( element );
			if ( field_is_active( element ) ) {
				_class = element
					.attr( 'class' )
					.split( ' ' )
					.map( function( cls ) {
						if ( cls.indexOf( 'cpf-type-', 0 ) !== -1 ) {
							return cls;
						}
						return null;
					} )
					.filter( function( v ) {
						if ( v !== null && v !== undefined ) {
							return v;
						}
						return null;
					} );

				if ( _class.length > 0 ) {
					_class = _class[ 0 ];
					if ( _class === 'cpf-type-product' ) {
						noSplit = true;
						if ( element.is( '.cpf-type-product-mode-product' ) ) {
							_class = 'cpf-type-checkbox';
						} else if ( element.is( '.cpf-type-product-dropdown' ) ) {
							_class = 'cpf-type-select';
						} else {
							_class = 'cpf-type-radio';
						}
					}
					switch ( _class ) {
						case 'cpf-type-radio':
							radio_checked = element.find( 'input.tm-epo-field.tmcp-radio:checked' );
							if ( operator === 'isnotempty' ) {
								all_checked = all_checked && radio_checked.length > 0;
								if ( radio_checked.length > 0 ) {
									val = radio_checked.length;
								}
							} else if ( operator === 'isempty' ) {
								all_checked = all_checked && radio_checked.length === 0;
							}
							break;

						case 'cpf-type-checkbox':
							checkbox_checked = element.find( 'input.tm-epo-field.tmcp-checkbox:checked' );
							if ( operator === 'isnotempty' ) {
								all_checked = all_checked && checkbox_checked.length > 0;
								if ( checkbox_checked.length > 0 ) {
									val = checkbox_checked.length;
								}
							} else if ( operator === 'isempty' ) {
								all_checked = all_checked && checkbox_checked.length === 0;
							}
							break;

						case 'cpf-type-select':
							val = element.find( 'select.tm-epo-field.tmcp-select' ).val();
							if ( val && ! noSplit ) {
								val = val.slice( 0, val.lastIndexOf( '_' ) );
							}
							break;

						case 'cpf-type-textarea':
							val = element.find( 'textarea.tm-epo-field.tmcp-textarea' ).val();
							break;

						case 'cpf-type-textfield':
							val = element.find( 'input.tm-epo-field.tmcp-textfield' ).val();
							break;

						case 'cpf-type-color':
							val = element.find( 'input.tm-epo-field.tm-color-picker' ).val();
							break;

						case 'cpf-type-range':
							val = element.find( 'input.tm-epo-field.tmcp-range' ).val();
							break;
					}
					all_checked = all_checked && tm_check_match( val, '', operator );
				} else {
					all_checked = all_checked && false;
				}
			}
		} );

		return all_checked;
	}

	function tm_check_field_match( f ) {
		var element = $( f.element );
		var operator = f.operator;
		var value = f.value;
		var val;
		var radio_checked;
		var checkbox_checked;
		var ret;
		var _class;
		var noSplit = false;

		if ( ! element.length ) {
			return false;
		}
		if ( element.is( '.cpf-section' ) ) {
			return tm_check_section_match( element, operator );
		}
		_class = element
			.attr( 'class' )
			.split( ' ' )
			.map( function( cls ) {
				if ( cls.indexOf( 'cpf-type-', 0 ) !== -1 ) {
					return cls;
				}
				return null;
			} )
			.filter( function( v ) {
				if ( v !== null && v !== undefined ) {
					return v;
				}
				return null;
			} );

		if ( _class.length > 0 ) {
			_class = _class[ 0 ];
			if ( _class === 'cpf-type-product' ) {
				noSplit = true;
				if ( element.is( '.cpf-type-product-mode-product' ) ) {
					_class = 'cpf-type-checkbox';
				} else if ( element.is( '.cpf-type-product-dropdown' ) ) {
					_class = 'cpf-type-select';
				} else {
					_class = 'cpf-type-radio';
				}
			}
			switch ( _class ) {
				case 'cpf-type-radio':
					radio_checked = element.find( 'input.tm-epo-field.tmcp-radio:checked' );

					if ( operator === 'is' || operator === 'isnot' ) {
						if ( radio_checked.length === 0 ) {
							return false;
						}
						val = element.find( 'input.tm-epo-field.tmcp-radio:checked' ).val();
						if ( val && ! noSplit ) {
							val = val.slice( 0, val.lastIndexOf( '_' ) );
						}
					}
					if ( operator === 'isnotempty' ) {
						return radio_checked.length > 0;
					}
					if ( operator === 'isempty' ) {
						return radio_checked.length === 0;
					}
					break;
				case 'cpf-type-checkbox':
					checkbox_checked = element.find( 'input.tm-epo-field.tmcp-checkbox:checked' );

					if ( operator === 'is' || operator === 'isnot' ) {
						if ( checkbox_checked.length === 0 ) {
							return false;
						}
						ret = false;
						checkbox_checked.each( function( i, el ) {
							val = $( el ).val();
							if ( val && ! noSplit ) {
								val = val.slice( 0, val.lastIndexOf( '_' ) );
							}
							if ( tm_check_match( val, value, operator ) ) {
								ret = true;
							} else if ( operator === 'isnot' ) {
								ret = false;
								return false;
							}
						} );
						return ret;
					}
					if ( operator === 'isnotempty' ) {
						return checkbox_checked.length > 0;
					}
					if ( operator === 'isempty' ) {
						return checkbox_checked.length === 0;
					}
					break;

				case 'cpf-type-select':
					val = element.find( 'select.tm-epo-field.tmcp-select' ).val();
					if ( val && ! noSplit ) {
						val = val.slice( 0, val.lastIndexOf( '_' ) );
					}
					break;

				case 'cpf-type-textarea':
					val = element.find( 'textarea.tm-epo-field.tmcp-textarea' ).val();
					break;

				case 'cpf-type-textfield':
					val = element.find( 'input.tm-epo-field.tmcp-textfield' ).val();
					break;

				case 'cpf-type-color':
					val = element.find( 'input.tm-epo-field.tm-color-picker' ).val();
					break;

				case 'cpf-type-range':
					val = element.find( 'input.tm-epo-field.tmcp-range' ).val();
					break;

				case 'cpf-type-variations':
					return tm_variation_check_match( element, value, operator );
			}

			return tm_check_match( val, value, operator );
		}
		return false;
	}

	function tm_check_rules( o, theevent ) {
		o.each( function() {
			var $this = $( this );
			var matches = parseInt( $this.data( 'matches' ), 10 );
			var toggle = $this.data( 'toggle' );
			var what = $this.data( 'what' );
			var fields = $this.data( 'fields' );
			var checked = 0;
			var show = false;

			switch ( toggle ) {
				case 'show':
					show = false;
					break;
				case 'hide':
					show = true;
					break;
			}

			$.each( fields, function( i, field ) {
				var fia = true;

				if ( theevent === 'cpflogic' ) {
					fia = field_is_active( $( field.element ) );
				}
				if ( fia && tm_check_field_match( field ) ) {
					checked = parseInt( checked, 10 ) + 1;
				}
			} );

			if ( what === 'all' ) {
				if ( matches === checked ) {
					show = ! show;
				}
			} else if ( checked > 0 ) {
				show = ! show;
			}
			if ( show ) {
				if ( theevent === 'cpflogic' && ! $this.data( 'did_initial_activation' ) ) {
					$this.find( '.tm-epo-field' ).each( function( i, el ) {
						el = $( el );
						if ( ! el.data( 'initial_activation' ) && ! $this.closest( '.cpf-section' ).is( '.tc-hidden' ) && field_is_active( el ) ) {
							el.trigger( 'tc_element_epo_rules' );
							el.data( 'initial_activation', 1 );
						}
					} );
					$this.data( 'did_initial_activation', 1 );
				}

				$this.removeClass( 'tc-hidden' );
			} else {
				$this.addClass( 'tc-hidden' );
			}
			$this.data( 'isactive', show );
		} );
	}

	function run_cpfdependson( obj ) {
		var iscpfdependson;
		var last_activate_field = [];

		if ( ! $( obj ).length ) {
			obj = 'body';
		}
		obj = $( obj );
		iscpfdependson = obj.find( '.iscpfdependson' );
		iscpfdependson.each( function( i, elements ) {
			$( elements ).each( function( j, el ) {
				tm_check_rules( $( el ) );
			} );
		} );
		iscpfdependson.each( function( i, elements ) {
			$( elements ).each( function( j, el ) {
				tm_check_rules( $( el ), 'cpflogic' );
			} );
		} );
		iscpfdependson.each( function( i, elements ) {
			$( elements ).each( function( j, o ) {
				o = $( o );
				if ( o.is( '.cpf-section' ) ) {
					o = o.find( '.cpf_hide_element' );
				}
				o.each( function( theindex, theelement ) {
					field_is_active( $( theelement ).find( '.tm-epo-field' ) );
				} );
			} );
		} );
		if ( $().selectric ) {
			$( '.tm-extra-product-options select' ).selectric( 'refresh' );
		}
		setTimeout( function() {
			$( '.tm-owl-slider' ).each( function() {
				$( this ).trigger( 'refresh.owl.carousel' );
			} );
		}, 200 );

		obj.find( '.tm-product-image:checked,select.tm-product-image' ).each( function() {
			var t = $( this );
			if ( field_is_active( t ) && t.val() !== '' ) {
				last_activate_field.push( t );
			}
		} );
		if ( last_activate_field.length ) {
			last_activate_field[ last_activate_field.length - 1 ].trigger( 'tm_trigger_product_image' );
		}

		jWindow.trigger( 'cpflogicrun' );
		jWindow.trigger( 'tmlazy' );
		jWindow.trigger( 'cpflogicdone' );
	}

	// Start Conditional logic
	if ( ! $().cpfdependson ) {
		$.fn.cpfdependson = function( fields, toggle, what, refresh ) {
			var elements = this;
			var matches = 0;

			if ( elements.length === 0 || typeof fields !== 'object' ) {
				return;
			}

			if ( ! toggle ) {
				toggle = 'show';
			}
			if ( ! what ) {
				what = 'all';
			}

			$.each( fields, function( i, field ) {
				var get_element;
				var $this_epo_container;

				if ( typeof fields !== 'object' ) {
					return true;
				}

				get_element = get_element_from_field( field.element );

				if ( get_element && get_element.length > 0 ) {
					get_element.each( function( ii, element ) {
						var $element = $( element );
						var $pid1;
						var $epo_id1;
						var _events = 'change.cpflogic';

						// this essentially only work for the plugin so we use
						// cache and not recalcualte each time
						if ( ! $this_epo_container ) {
							$pid1 = '.tm-product-id-' + $element.closest( '.tc-extra-product-options' ).attr( 'data-product-id' );
							$epo_id1 = "[data-epo-id='" + $element.closest( '.tc-extra-product-options' ).attr( 'data-epo-id' ) + "']";
							$this_epo_container = $( '.tc-extra-product-options' + $pid1 + $epo_id1 );
						}

						if ( element && $element.length > 0 && ( ! $element.data( 'tmhaslogicevents' ) || refresh ) ) {
							if ( $element.is( '.tm-epo-variation-element' ) ) {
								// associated product event prefixes are added later
								add_variation_event( 'found_variation.tmlogic', false, function() {
									run_cpfdependson( $this_epo_container );
									jWindow.trigger( 'tm-do-epo-update' );
								} );
								add_variation_event( 'hide_variation.tmlogic', false, function() {
									run_cpfdependson( $this_epo_container );
									jWindow.trigger( 'tm-do-epo-update' );
								} );
							} else {
								if ( $element.is( ':text' ) || $element.is( 'textarea' ) ) {
									_events = 'change.cpflogic input.cpflogic';
								}
								$element.off( _events ).on( _events, function() {
									run_cpfdependson( $this_epo_container );
								} );
							}
							$element.data( 'tmhaslogicevents', 1 );
						}
					} );

					matches = parseInt( matches, 10 ) + 1;
				}
			} );

			elements.each( function() {
				var $this = $( this );
				var show = false;

				$this.data( 'matches', matches ).data( 'toggle', toggle ).data( 'what', what ).data( 'fields', fields );

				switch ( toggle ) {
					case 'show':
						show = false;
						break;
					case 'hide':
						show = true;
						break;
				}
				if ( show ) {
					$this.removeClass( 'tc-hidden' );
				} else {
					$this.addClass( 'tc-hidden' );
				}

				$this.data( 'isactive', show );
			} );

			elements.addClass( 'iscpfdependson' ).data( 'iscpfdependson', 1 );
			return elements.each( function() {
				$( this ).addClass( 'is-epo-depend' );
			} );
		};

		$.fn.run_cpfdependson = function() {
			run_cpfdependson();
		};
	}

	$.tcepo = {
		formSubmitEvents: {},

		oneOptionIsSelected: {},

		initialActivation: {},

		// Holds the active precentage of total current price type fields
		lateFieldsPrices: {},

		errorObject: {}
	};

	function validate_logic( l ) {
		return typeof l === 'object' && 'toggle' in l && 'what' in l && 'rules' in l && l.rules.length > 0;
	}

	// The following loops are required for the logic to work on composite products that have custom variations
	function cpf_section_logic( obj ) {
		var root_element = $( obj );
		var all_sections = root_element.find( '.cpf-section' );
		var search_obj;
		var cpf_section;
		var sect;
		var logic;
		var haslogic;
		var fields;
		var section;
		var element;
		var operator;
		var value;
		var obj_section;
		var obj_element;

		if ( root_element.is( '.cpf-section' ) ) {
			search_obj = false;
		} else {
			search_obj = all_sections;
		}

		root_element.each( function( j, obj_el ) {
			if ( $( obj_el ).is( '.cpf-section' ) ) {
				cpf_section = $( obj_el );
			} else {
				cpf_section = $( obj_el ).find( '.cpf-section' );
			}

			cpf_section.each( function( index, el ) {
				sect = $( el );
				logic = sect.data( 'logic' );
				haslogic = parseInt( sect.data( 'haslogic' ), 10 );
				fields = [];

				if ( haslogic === 1 && validate_logic( logic ) ) {
					$.each( logic.rules, function( i, rule ) {
						if ( rule ) {
							section = rule.section;
							element = rule.element;
							operator = rule.operator;
							value = rule.value;

							if ( search_obj ) {
								obj_section = search_obj.filter( "[data-uniqid='" + section + "']" );
								if ( element !== section ) {
									obj_element = obj_section.find( '.cpf_hide_element' ).eq( element );
								} else {
									obj_element = obj_section;
								}
							} else if ( element !== section ) {
								obj_element = root_element.find( '.cpf_hide_element' ).eq( element );
							} else {
								obj_element = obj_section;
							}

							fields.push( {
								element: obj_element,
								operator: operator,
								value: value
							} );
						}
					} );
					if ( ! sect.data( 'iscpfdependson' ) ) {
						sect.data( 'cpfdependson-fields', fields );
						sect.cpfdependson( fields, logic.toggle, logic.what );
					} else {
						sect.cpfdependson( sect.data( 'cpfdependson-fields' ), logic.toggle, logic.what, true );
					}
				}
			} );
		} );
	}

	function cpf_element_logic( obj ) {
		var root_element = $( obj );
		var all_sections = root_element.find( '.cpf-section' );
		var search_obj;
		var current_element;
		var logic;
		var haslogic;
		var section;
		var element;
		var operator;
		var value;
		var obj_section;
		var obj_element;

		if ( root_element.is( '.cpf-section' ) ) {
			search_obj = false;
		} else {
			search_obj = all_sections;
		}

		root_element.find( '.cpf_hide_element' ).each( function( index, el ) {
			var fields = [];

			current_element = $( el );
			logic = current_element.data( 'logic' );
			haslogic = parseInt( current_element.data( 'haslogic' ), 10 );

			if ( haslogic === 1 && validate_logic( logic ) ) {
				$.each( logic.rules, function( i, rule ) {
					if ( rule ) {
						section = rule.section;
						element = rule.element;
						operator = rule.operator;
						value = rule.value;

						if ( search_obj ) {
							obj_section = search_obj.filter( "[data-uniqid='" + section + "']" );
							if ( element !== section ) {
								obj_element = obj_section.find( '.cpf_hide_element' ).eq( element );
							} else {
								obj_element = obj_section;
							}
						} else if ( element !== section ) {
							obj_element = root_element.find( '.cpf_hide_element' ).eq( element );
						} else {
							obj_element = obj_section;
						}

						fields.push( {
							element: obj_element,
							operator: operator,
							value: value
						} );
					}
				} );

				if ( ! current_element.data( 'iscpfdependson' ) ) {
					current_element.data( 'cpfdependson-fields', fields );
					current_element.cpfdependson( fields, logic.toggle, logic.what );
				} else {
					current_element.cpfdependson( current_element.data( 'cpfdependson-fields' ), logic.toggle, logic.what, true );
				}
			}
		} );
	} // End Conditional logic

	// Return a formatted currency value without tax
	function tm_set_price_without_tax( value, _cart ) {
		var taxable;
		var tax_rate;
		var tax_display_mode;
		var prices_include_tax;

		if ( _cart ) {
			taxable = _cart.attr( 'data-taxable' );
			tax_rate = _cart.attr( 'data-tax-rate' );
			tax_display_mode = _cart.attr( 'data-tax-display-mode' );
			prices_include_tax = _cart.attr( 'data-prices-include-tax' );

			if ( taxable && tax_display_mode === 'incl' && prices_include_tax !== '1' ) {
				value = parseFloat( value ) / ( 1 + ( tax_rate / 100 ) );
			}
		}

		return value;
	}

	// Return a formatted currency value
	function tm_set_price_totals( value, _cart, notax, taxstring, element ) {
		var inc_tax_string = '';
		var sign = '';
		var val;

		if ( ! notax ) {
			value = tm_set_tax_price( value, _cart, element );
		}
		val = Math.abs( value );
		if ( _cart && taxstring ) {
			inc_tax_string = _cart.attr( 'data-tax-string' );
		}
		if ( inc_tax_string === undefined ) {
			inc_tax_string = '';
		}

		if ( value < 0 ) {
			sign = TMEPOJS.minus_sign + ' ';
		}

		return tm_set_price_( val, sign, inc_tax_string );
	}

	function tm_force_update_price( obj, price, formated_price, original_price, original_formated_price ) {
		tm_update_price( obj, price, formated_price, original_price, original_formated_price, true );
	}

	function tm_update_price( obj, price, formated_price, original_price, original_formated_price, force ) {
		var $obj = $( obj );
		var w;
		var $ba_amount;
		var priceobj;
		var f;

		if ( $obj.length === 0 ) {
			return;
		}

		w = $obj.closest( '.tmcp-field-wrap' );
		f = w.find( '.tm-epo-field' );

		if ( ! force && f.attr( 'data-no-price-change' ) === '1' && f.data( 'price-changed' ) ) {
			return;
		}

		price = $.epoAPI.applyFilter( 'tc_adjust_update_price_price', price ); //number
		formated_price = $.epoAPI.applyFilter( 'tc_adjust_update_price_formated_price', formated_price, price ); //formatted
		original_price = $.epoAPI.applyFilter( 'tc_adjust_update_price_original_price', original_price ); //number
		original_formated_price = $.epoAPI.applyFilter( 'tc_adjust_update_price_original_formated_price', original_formated_price, original_price ); //formatted

		if ( ! Number.isFinite( parseFloat( original_price ) ) ) {
			original_price = 0;
		}
		if ( ! Number.isFinite( parseFloat( price ) ) ) {
			price = 0;
		}

		$ba_amount = w.find( '.before-amount,.after-amount' );
		priceobj = {
			price: formated_price,
			original_price: original_formated_price
		};

		if ( ( TMEPOJS.tm_epo_auto_hide_price_if_zero === 'yes' && $.tmempty( price ) === false ) || TMEPOJS.tm_epo_auto_hide_price_if_zero !== 'yes' ) {
			if ( $.tmempty( price ) === true || ( ! force && f.length > 0 && ( f.attr( 'data-no-price' ) === '1' || ( f.attr( 'data-type' ) === 'variable' && ! f.data( 'price' ) ) || ( f.is( '.tmcp-select' ) && ! f.children( 'option:selected' ).data( 'price' ) ) ) ) ) {
				$obj.empty();
				$ba_amount.addClass( 'tm-hidden' );
			} else {
				if ( original_price && original_price !== undefined && parseFloat( original_price ) !== parseFloat( price ) ) {
					$obj.html( $.epoAPI.template.html( tcAPI.templateEngine.sale_price, { price: priceobj } ) );
				} else {
					$obj.html( $.epoAPI.template.html( tcAPI.templateEngine.price, { price: priceobj } ) );
				}
				$ba_amount.removeClass( 'tm-hidden' );
			}
		} else {
			$obj.empty();
			$ba_amount.addClass( 'tm-hidden' );
		}
	}

	function get_variation_current_settings( form, epoObject ) {
		var current_settings = {};

		if ( epoObject.thisForm ) {
			form = epoObject.thisForm;
		}

		form.find( '.variations select, .tc-epo-variable-product-selector' ).each( function() {
			var attribute_name;
			var value;

			// Get attribute name from data-attribute_name, or from input name
			// if it doesn't exist
			if ( typeof $( this ).data( 'attribute_name' ) !== 'undefined' ) {
				attribute_name = $( this ).data( 'attribute_name' );
			} else {
				attribute_name = $( this ).attr( 'name' );
			}

			// Encode entities
			value = $( this ).val();

			// Add to settings array
			current_settings[ attribute_name ] = value;
		} );

		return current_settings;
	}

	function do_tm_custom_variations_update( form, all_variations, epoObject ) {
		var check_if_all_are_not_set = [];
		var formSettings = get_variation_current_settings( form, epoObject );
		var redo_check = true;

		form.find( '.cpf-type-variations' ).each( function( i, el ) {
			var t = $( el ).find( '.tm-epo-variation-element' );
			var id;
			var v;
			var exists = false;

			check_if_all_are_not_set[ i ] = true;

			if ( t.is( 'select' ) ) {
				id = $.epoAPI.dom.id( t.attr( 'data-tm-for-variation' ) );
				v = t.val();
				if ( v ) {
					check_if_all_are_not_set[ i ] = false;
				}
				t.children( 'option' ).each( function( x, o ) {
					exists = false;
					form.find( "[data-attribute_name='attribute_" + id + "']" )
						.children( 'option' )
						.each( function() {
							if ( $( this ).attr( 'value' ) === $( o ).attr( 'value' ) ) {
								exists = true;
								return false;
							}
						} );
					if ( ! exists ) {
						$( o ).attr( 'disabled', 'disabled' ).hide();
					} else {
						$( o ).removeAttr( 'disabled' ).show();
					}
				} );
			} else {
				t.each( function( x, oe ) {
					var o = $( oe );
					var li = o.closest( 'li' );
					var input = li.find( '.tm-epo-variation-element' );
					var this_settings = $.extend( true, {}, formSettings );
					var matching_variations;
					var variation;
					var is_in_stock;

					id = o.attr( 'data-tm-for-variation' );
					v = o.val();
					if ( o.is( ':checked' ) ) {
						check_if_all_are_not_set[ i ] = false;
					}

					this_settings[ 'attribute_' + id ] = v;

					matching_variations = $.fn.tm_find_matching_variations( all_variations, this_settings );
					variation = matching_variations.shift();

					is_in_stock = variation && 'is_in_stock' in variation && variation.is_in_stock;

					if ( ! variation || ! is_in_stock ) {
						o.attr( 'disabled', 'disabled' ).addClass( 'tm-disabled' );

						input.attr( 'disabled', 'disabled' );
						input.attr( 'data-tm-disabled', 'disabled' );

						li.addClass( 'tm-attribute-disabled' ).fadeTo( 'fast', 0.5 );
						if ( ! is_in_stock ) {
							li.find( 'label' ).off();
						}
					} else {
						o.removeAttr( 'disabled' ).removeClass( 'tm-disabled' );
						li.removeClass( 'tm-attribute-disabled' ).fadeTo( 'fast', 1, function() {
							$( this ).css( 'opacity', '' );
						} );
						input.removeAttr( 'disabled' );
						input.removeAttr( 'data-tm-disabled' );
					}
				} );
			}
		} );

		if ( check_if_all_are_not_set ) {
			check_if_all_are_not_set.shift();

			$.each( check_if_all_are_not_set, function( i, el ) {
				if ( el === false ) {
					redo_check = false;
					return false;
				}
			} );
			if ( redo_check ) {
				form.find( '.cpf-type-variations' )
					.first()
					.each( function( i, el ) {
						var t;
						var li;
						var input;

						t = $( el ).find( '.tm-epo-variation-element' );

						if ( ! t.is( 'select' ) ) {
							t.each( function( x, o ) {
								o = $( o );
								li = o.closest( 'li' );
								input = li.find( '.tm-epo-variation-element' );
								o.removeAttr( 'disabled' ).removeClass( 'tm-disabled' );
								li.removeClass( 'tm-attribute-disabled' ).stop().css( 'opacity', '' );
								input.removeAttr( 'disabled' );
								input.removeAttr( 'data-tm-disabled' );
							} );
						}
					} );
			}
		}
	}

	function tm_custom_variations_update( form, epoObject ) {
		var data;
		var all_variations = form.data( 'product_variations' );
		var product_id = parseInt( form.data( 'product_id' ), 10 );
		var globalVariationObject = form.data( 'globalVariationObject' ) || false;

		if ( ! product_id ) {
			product_id = form.data( 'tc_product_id' );
		}

		if ( ! product_id && form.is( tcAPI.compositeSelector ) ) {
			data = form.find( '.component_options' ).data( 'options_data' );
			product_id = data[ 0 ].option_id;
			if ( ! all_variations ) {
				all_variations = form.find( '.details.component_data' ).data( 'product_variations' );
			}
		}

		if ( ! epoObject.is_associated ) {
			// Fallback to window property if not set - backwards compat
			if ( ! all_variations && window.product_variations && window.product_variations.product_id ) {
				all_variations = window.product_variations.product_id;
			}
			if ( ! all_variations && window.product_variations ) {
				all_variations = window.product_variations;
			}
			if ( ! all_variations && window[ 'product_variations_' + product_id ] ) {
				all_variations = window[ 'product_variations_' + product_id ];
			}
		}

		if ( ! all_variations ) {
			if ( ! globalVariationObject ) {
				data = {
					action: 'woocommerce_tm_get_variations_array',
					post_id: product_id
				};
				$.post(
					TMEPOJS.ajax_url,
					data,
					function( response ) {
						globalVariationObject = response;
						form.data( 'globalVariationObject', response );
						do_tm_custom_variations_update( form, globalVariationObject.variations, epoObject );
					},
					'json'
				);
			} else {
				do_tm_custom_variations_update( form, globalVariationObject.variations, epoObject );
			}

			return;
		}
		// may need 2.4 check for woocommerce_ajax_variation_threshold
		do_tm_custom_variations_update( form, all_variations, epoObject );
	}

	function tm_fix_stock( cart, html ) {
		var custom_variations;
		var section;

		if ( html === undefined ) {
			return false;
		}
		cart = $( cart );
		custom_variations = cart.find( '.tm-epo-variation-element' ).first();
		section = custom_variations.closest( '.tm-epo-variation-section' );

		if ( custom_variations.length ) {
			section.find( '.tm-stock' ).remove();
			section.append( '<div class="tm-stock">' + html + '</div>' );
			return true;
		}
		cart.find( '.tm-stock' ).remove();
		cart.find( '.variations' ).after( '<div class="tm-stock">' + html + '</div>' );
		return true;
	}

	function tm_fix_stock_tmepo( $this, form ) {
		var stock;

		if ( TMEPOJS.tm_epo_global_move_out_of_stock === 'no' ) {
			return;
		}
		stock = $this.find( '.woocommerce-variation-availability' ).last();
		if ( ! stock.length ) {
			stock = $this.find( '.stock' ).last();
		}

		if ( stock.length ) {
			form.find( '.tm-stock' ).remove();
			if ( tm_fix_stock( form, stock.prop( 'outerHTML' ) ) ) {
				stock.remove();
			}
		} else {
			form.find( '.tm-stock' ).remove();
		}
	}

	function get_main_input_id( main_product, product, id ) {
		var selector = '';
		var inputid;

		if ( id ) {
			selector = selector + "[value='" + id + "']";
		}
		if ( ! product ) {
			product = main_product;
		}
		inputid = product.find( tcAPI.addToCartSelector + selector );
		if ( inputid.length === 0 ) {
			inputid = product.find( tcAPI.tcAddToCartSelector + selector );
		}
		return inputid.last();
	}

	function get_main_form( main_product, product, selector, id ) {
		if ( ! selector ) {
			selector = 'form';
		}
		return get_main_input_id( main_product, product, id ).closest( selector );
	}

	function get_main_cart( main_product, product, selector, id ) {
		return get_main_form( main_product, product, selector, id );
	}

	function tm_get_native_prices_block( obj ) {
		var selector = $.epoAPI.applyFilter( 'tcGetNativePricesBlockSelector', '.single_variation .price', obj );

		return $( obj ).find( selector ).not( '.tc-price' );
	}

	// URL replacement setup
	function tm_set_url_fields() {
		jDocument.on( 'click.cpfurl change.cpfurl tmredirect', '.use_url_container .tmcp-radio, .use_url_container .tmcp-radio+label', function( e ) {
			var data_url = $( this ).attr( 'data-url' );
			if ( data_url ) {
				if ( window.location !== data_url ) {
					e.preventDefault();
					window.location = data_url;
				}
			}
		} );
		jDocument.on( 'change.cpfurl tmredirect', '.use_url_container .tmcp-select', function( e ) {
			var data_url = $( this ).children( 'option:selected' ).attr( 'data-url' );
			if ( data_url ) {
				if ( window.location !== data_url ) {
					e.preventDefault();
					window.location = data_url;
				}
			}
		} );
	}

	function tm_floating_totals( this_epo_totals_container, is_quickview, main_cart ) {
		$.tcFloatingTotalsBox( this_epo_totals_container, is_quickview, main_cart );
	}

	function tm_show_hide_add_to_cart_button( main_product, epoObject, one_option_is_selected ) {
		var button;
		var has_epo;
		var this_epo_container;

		if ( typeof epoObject === 'object' ) {
			has_epo = epoObject.has_epo;
			this_epo_container = epoObject.this_epo_container;
			has_epo = has_epo && ( this_epo_container.find( '.tmcp-fee-field' ).length || this_epo_container.find( '.tmcp-field' ).not( '.cpf-type-variations .tmcp-field' ).length );
		} else {
			has_epo = epoObject;
		}

		// Hide cart button check
		if ( has_epo && TMEPOJS.tm_epo_hide_add_cart_button === 'yes' ) {
			button = main_product.find( tcAPI.addToCartButtonSelector ).first();
			if ( one_option_is_selected ) {
				button.removeClass( 'tc-hide-add-to-cart-button' );
			} else {
				button.addClass( 'tc-hide-add-to-cart-button' );
			}
		}
	}

	function goto_error_item( item, epoEventId ) {
		var el = $.tcepo.errorObject[ epoEventId ] || item;
		var elsection;
		var elsectionlink;
		var elcpf_hide_element;
		var pos;

		if ( el ) {
			if ( TMEPOJS.tm_epo_disable_error_scroll !== 'yes' ) {
				elsection = el.closest( '.cpf-section' );
				elsectionlink = elsection.find( '.tm-section-link' );
				elcpf_hide_element = el.closest( '.cpf_hide_element' );

				if ( elsection.find( '.tm-toggle' ).length ) {
					elsection.find( '.tm-toggle' ).trigger( 'openwrap.tmtoggle' );
				}
				if ( ! window.tc_validation_offset ) {
					window.tc_validation_offset = -100;
				}
				if ( elsection.is( '.section_popup' ) ) {
					errorContainer.tcScrollTo( elsectionlink, 300, window.tc_validation_offset );
					elsectionlink.trigger( 'click.tmsectionpoplink' );
				} else if ( elsection.is( '.tm-owl-slider-section' ) ) {
					pos = el.closest( '.owl-item' ).index();
					elsection.find( '.tcowl-carousel' ).trigger( 'to.owl.carousel', [ pos, 100 ] );
					setTimeout( function() {
						elsection.find( '.tcowl-carousel' ).trigger( 'refresh.owl.carousel' );

						if ( elcpf_hide_element.length > 0 ) {
							errorContainer.tcScrollTo( elcpf_hide_element, 300, window.tc_validation_offset );
						}
					}, 200 );
				} else if ( elcpf_hide_element.length > 0 ) {
					errorContainer.tcScrollTo( elcpf_hide_element, 300, window.tc_validation_offset );
				}
			}

			if ( ! item ) {
				$.tcepo.errorObject[ epoEventId ] = false;
			}
		}
	}

	function tm_limit_c_selection( field, prevent ) {
		var allowed = parseInt( field.attr( 'data-limit' ), 10 );
		var checked = false;
		var val;
		var t;
		var q;

		if ( allowed > 0 ) {
			checked = 0;
			field
				.closest( '.tm-extra-product-options-checkbox' )
				.find( "input.tm-epo-field[type='checkbox']:checked" )
				.each( function() {
					t = $( this );
					q = t.closest( 'li.tmcp-field-wrap' ).find( 'input.tm-qty' );
					if ( q.length > 0 ) {
						val = parseInt( q.val(), 10 );
						if ( val <= 0 ) {
							val = 1;
						}
						checked = parseInt( checked, 10 ) + val;
					} else {
						checked = parseInt( checked, 10 ) + 1;
					}
				} );
			if ( checked > allowed ) {
				if ( prevent ) {
					field.prop( 'checked', '' ).trigger( 'change' );
				}
				return false;
			}
		}
		return true;
	}

	function tm_exact_c_selection( field, prevent ) {
		var allowed = parseInt( field.attr( 'data-exactlimit' ), 10 );
		var checked = false;
		var val;
		var t;
		var q;

		if ( allowed > 0 ) {
			checked = 0;
			field
				.closest( '.tm-extra-product-options-checkbox' )
				.find( "input.tm-epo-field[type='checkbox']:checked" )
				.each( function() {
					t = $( this );
					q = t.closest( 'li.tmcp-field-wrap' ).find( 'input.tm-qty' );
					if ( q.length > 0 ) {
						val = parseInt( q.val(), 10 );
						if ( val <= 0 ) {
							val = 1;
						}
						checked = parseInt( checked, 10 ) + val;
					} else {
						checked = parseInt( checked, 10 ) + 1;
					}
				} );
			if ( checked > allowed ) {
				if ( prevent ) {
					field.prop( 'checked', '' ).trigger( 'change' );
				}
				return false;
			}
		}
		return true;
	}

	function tm_limit_cont( fields, main_product, epoEventId ) {
		var checkall = true;
		var first_error_obj = false;
		var limit;
		var eln;
		var checked;
		var t;
		var val;
		var q;
		var ew;
		var em;
		var message;
		var field;

		fields.each( function() {
			field = $( this );
			limit = field.find( "[type='checkbox'][data-limit]" );
			if ( limit.length && field_is_active( limit ) ) {
				eln = parseInt( limit.attr( 'data-limit' ), 10 );
				checked = 0;
				field.find( "input.tm-epo-field[type='checkbox']:checked" ).each( function() {
					t = $( this );
					q = t.closest( 'li.tmcp-field-wrap' ).find( 'input.tm-qty' );
					if ( q.length > 0 ) {
						val = parseInt( q.val(), 10 );
						if ( val <= 0 ) {
							val = 1;
						}
						checked = parseInt( checked, 10 ) + val;
					} else {
						checked = parseInt( checked, 10 ) + 1;
					}
				} );
				ew = field.closest( '.cpf_hide_element' );
				em = ew.find( 'div.tm-error-min' );

				if ( eln < checked ) {
					checkall = false;
					first_error_obj = field;
					if ( eln === 1 ) {
						message = TMEPOJS.tm_epo_global_validator_messages.epolimitsingle.replace( '{0}', eln );
					} else {
						message = TMEPOJS.tm_epo_global_validator_messages.epolimit.replace( '{0}', eln );
					}
					if ( em.length ) {
						em.remove();
					}
					if ( TMEPOJS.tm_epo_global_error_label_placement === 'before' ) {
						field.before( '<div class="tm-error-min tm-error">' + message + '</div>' );
					} else {
						field.after( '<div class="tm-error-min tm-error">' + message + '</div>' );
					}
					main_product.find( tcAPI.addToCartButtonSelector ).first().removeClass( 'disabled loading fpd-disabled' ).removeAttr( 'disabled' );
				} else {
					em.remove();
				}
			}
		} );
		if ( first_error_obj ) {
			$.tcepo.errorObject[ epoEventId ] = first_error_obj;
		}
		return checkall;
	}

	function tm_check_limit_cont( limit_cont, main_product, epoEventId ) {
		$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
			trigger: function() {
				return tm_limit_cont( limit_cont, main_product, epoEventId );
			},
			on_true: function() {
				return true;
			},
			on_false: function() {
				goto_error_item( $( limit_cont ).find( '.tm-epo-field' ).first(), epoEventId );
				return true;
			}
		};
	}

	function tm_exactlimit_cont( fields, main_product, epoEventId ) {
		var checkall = true;
		var first_error_obj = false;
		var exactlimit;
		var eln;
		var checked;
		var t;
		var val;
		var q;
		var ew;
		var em;
		var message;
		var field;

		fields.each( function() {
			field = $( this );
			exactlimit = field.find( "[type='checkbox'][data-exactlimit]" );
			if ( exactlimit.length && field_is_active( exactlimit ) ) {
				eln = parseInt( exactlimit.attr( 'data-exactlimit' ), 10 );
				checked = 0;
				field.find( "input.tm-epo-field[type='checkbox']:checked" ).each( function() {
					t = $( this );
					q = t.closest( 'li.tmcp-field-wrap' ).find( 'input.tm-qty' );
					if ( q.length > 0 ) {
						val = parseInt( q.val(), 10 );
						if ( val <= 0 ) {
							val = 1;
						}
						checked = parseInt( checked, 10 ) + val;
					} else {
						checked = parseInt( checked, 10 ) + 1;
					}
				} );
				ew = field.closest( '.cpf_hide_element' );
				em = ew.find( 'div.tm-error-min' );

				if ( eln !== checked ) {
					checkall = false;
					first_error_obj = field;
					if ( eln === 1 ) {
						message = TMEPOJS.tm_epo_global_validator_messages.epoexactsingle.replace( '{0}', eln );
					} else {
						message = TMEPOJS.tm_epo_global_validator_messages.epoexact.replace( '{0}', eln );
					}
					if ( em.length ) {
						em.remove();
					}
					if ( TMEPOJS.tm_epo_global_error_label_placement === 'before' ) {
						field.before( '<div class="tm-error-min tm-error">' + message + '</div>' );
					} else {
						field.after( '<div class="tm-error-min tm-error">' + message + '</div>' );
					}
					main_product.find( tcAPI.addToCartButtonSelector ).first().removeClass( 'disabled loading fpd-disabled' ).removeAttr( 'disabled' );
				} else {
					em.remove();
				}
			}
		} );
		if ( first_error_obj ) {
			$.tcepo.errorObject[ epoEventId ] = first_error_obj;
		}
		return checkall;
	}

	function tm_check_exactlimit_cont( exactlimit_cont, main_product, epoEventId ) {
		$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
			trigger: function() {
				return tm_exactlimit_cont( exactlimit_cont, main_product, epoEventId );
			},
			on_true: function() {
				return true;
			},
			on_false: function() {
				goto_error_item( $( exactlimit_cont ).find( '.tm-epo-field' ).first(), epoEventId );
				return true;
			}
		};
	}

	function tm_minimumlimit_cont( fields, epoEventId ) {
		var checkall = true;
		var first_error_obj = false;
		var eln;
		var checked;
		var t;
		var val;
		var q;
		var ew;
		var em;
		var message;
		var field;

		fields.each( function() {
			var minimumlimit;

			field = $( this );
			minimumlimit = field.find( "[type='checkbox'][data-minimumlimit]" );

			if ( minimumlimit.length && field_is_active( minimumlimit ) ) {
				eln = parseInt( minimumlimit.attr( 'data-minimumlimit' ), 10 );
				checked = 0;
				field.find( "input.tm-epo-field[type='checkbox']:checked" ).each( function() {
					t = $( this );
					q = t.closest( 'li.tmcp-field-wrap' ).find( 'input.tm-qty' );
					if ( q.length > 0 ) {
						val = parseInt( q.val(), 10 );
						if ( val <= 0 ) {
							val = 1;
						}
						checked = parseInt( checked, 10 ) + val;
					} else {
						checked = parseInt( checked, 10 ) + 1;
					}
				} );
				ew = field.closest( '.cpf_hide_element' );
				em = ew.find( 'div.tm-error-min' );
				if ( eln > checked ) {
					checkall = false;
					first_error_obj = field;
					if ( eln === 1 ) {
						message = TMEPOJS.tm_epo_global_validator_messages.epominsingle.replace( '{0}', eln );
					} else {
						message = TMEPOJS.tm_epo_global_validator_messages.epomin.replace( '{0}', eln );
					}
					if ( em.length ) {
						em.remove();
					}
					if ( TMEPOJS.tm_epo_global_error_label_placement === 'before' ) {
						field.before( '<div class="tm-error-min tm-error">' + message + '</div>' );
					} else {
						field.after( '<div class="tm-error-min tm-error">' + message + '</div>' );
					}
				} else {
					em.remove();
				}
			}
		} );

		if ( first_error_obj ) {
			$.tcepo.errorObject[ epoEventId ] = first_error_obj;
		}

		return checkall;
	}

	function tm_check_minimumlimit_cont( minimumlimit_cont, epoEventId ) {
		$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
			trigger: function() {
				return tm_minimumlimit_cont( minimumlimit_cont, epoEventId );
			},
			on_true: function() {
				return true;
			},
			on_false: function() {
				goto_error_item( false, epoEventId );
				return true;
			}
		};
	}

	function cleanPrice( price ) {
		if ( price === null ) {
			return 0;
		}

		if ( typeof price === 'object' ) {
			price = price[ 0 ];
		}

		if ( ! Number.isFinite( parseFloat( price ) ) ) {
			price = 0;
		}

		return price;
	}

	function tm_apply_dpd( price, totals, apply, force ) {
		price = cleanPrice( price );

		if ( apply ) {
			price = $.epoAPI.applyFilter( 'tc_apply_dpd', price, totals, apply, force );
		}

		return price;
	}

	function tm_calculate_product_regular_price( totals, allowfalse ) {
		var price = 0;

		if ( totals.length > 0 ) {
			price = totals.data( 'regular-price' );
		}

		price = $.epoAPI.applyFilter( 'tc_calculate_product_regular_price', price, totals );

		if ( allowfalse && price === false ) {
			return false;
		}
		price = parseFloat( price );

		if ( ! Number.isFinite( price ) ) {
			price = 0;
		}

		return price;
	}

	function tm_calculate_product_price( totals, allowfalse ) {
		var price = 0;

		if ( totals.length > 0 ) {
			price = totals.data( 'price' );
		}

		price = $.epoAPI.applyFilter( 'tc_calculate_product_price', price, totals );

		if ( allowfalse && price === false ) {
			return false;
		}
		price = parseFloat( price );

		if ( ! Number.isFinite( price ) ) {
			price = 0;
		}

		return price;
	}

	function calculateMathPrice( price, thisElement, epoObject, noevents ) {
		var formula = price.toString();
		var val = 0;
		var matches;
		var match;
		var elementWrap;
		var element;
		var reg;
		var elementPrice = 0;
		var pos;
		var type;
		var id;
		var thisVal;
		var thisValForced;
		var thisElementWrap = thisElement.closest( '.cpf_hide_element' );
		var tc_totals_ob = epoObject.this_epo_totals_container.data( 'tc_totals_ob' );
		var thisElementIndex = thisElementWrap.find( '.tmcp-field, .tmcp-fee-field' ).filter( ':checked' ).index( thisElement );
		var thisElementIndexForced = thisElementWrap.find( '.tmcp-field, .tmcp-fee-field' ).index( thisElement );
		var this_epo_container = epoObject.is_associated ? epoObject.this_epo_container : epoObject.this_epo_container.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector );

		if ( thisElementIndex === -1 && ! thisElement.is( '.tmcp-checkbox, .tmcp-radio' ) ) {
			thisElementIndex = 0;
		}

		if ( ! noevents && ! thisElement.data( 'addedtcEpoBeforeOptionPriceCalculation' ) ) {
			jWindow.on( 'tcEpoBeforeOptionPriceCalculation.math', function() {
				tm_element_epo_rules( epoObject, thisElement, undefined, undefined, undefined, true );
			} );
			jWindow.on( 'tcEpoAfterOptionPriceCalculation.math', function() {
				thisElement.data( 'fetchOptionPrices', false );
				thisElement.data( 'fetchOptionPrices-forced', false );
				thisElement.data( 'fetchOptionPrices-fee', false );
				thisElement.data( 'fetchOptionPrices-fee-forced', false );
			} );
			thisElement.data( 'addedtcEpoBeforeOptionPriceCalculation', 1 );
		}

		if ( thisElement.is( '.tmcp-field' ) ) {
			if ( thisElement.data( 'fetchOptionPrices' ) ) {
				thisVal = thisElement.data( 'fetchOptionPrices' );
				thisValForced = thisElement.data( 'fetchOptionPrices-forced' );
			} else {
				thisVal = fetchOptionPrices( epoObject, thisElementWrap, '.tmcp-field', 0, 0, [], true );
				thisValForced = fetchOptionPrices( epoObject, thisElementWrap, '.tmcp-field', 0, 0, [], true, true );
				thisElement.data( 'fetchOptionPrices', thisVal );
				thisElement.data( 'fetchOptionPrices-forced', thisValForced );
			}
		} else if ( thisElement.is( '.tmcp-fee-field' ) ) {
			if ( thisElement.data( 'fetchOptionPrices-fee' ) ) {
				thisVal = thisElement.data( 'fetchOptionPrices-fee' );
				thisValForced = thisElement.data( 'fetchOptionPrices-fee-forced' );
			} else {
				thisVal = fetchOptionPrices( epoObject, thisElementWrap, '.tmcp-fee-field', 0, 0, [], true );
				thisValForced = fetchOptionPrices( epoObject, thisElementWrap, '.tmcp-fee-field', 0, 0, [], true, true );
				thisElement.data( 'fetchOptionPrices-fee', thisVal );
				thisElement.data( 'fetchOptionPrices-fee-forced', thisValForced );
			}
		}

		if ( tc_totals_ob !== undefined ) {
			// product quantity
			formula = formula.replace( /{quantity}/g, $.epoAPI.math.unformat( tc_totals_ob.qty, tcAPI.localInputDecimalSeparator ) );
			// original product price
			formula = formula.replace( /{product_price}/g, $.epoAPI.math.unformat( tc_totals_ob.product_price, tcAPI.localInputDecimalSeparator ) );
		} else {
			// product quantity
			formula = formula.replace( /{quantity}/g, 0 );
			// original product price
			formula = formula.replace( /{product_price}/g, 0 );
		}

		if ( thisValForced.floatingBoxData !== undefined && thisValForced.floatingBoxData[ thisElementIndexForced ] !== undefined ) {
			formula = formula
				// the option/element value
				.replace( /{this.value}/g, $.epoAPI.math.unformat( thisValForced.floatingBoxData[ thisElementIndexForced ].valueText, tcAPI.localInputDecimalSeparator ) )
				// the option/element value length
				.replace( /{this.value.length}/g, thisValForced.floatingBoxData[ thisElementIndexForced ].valueText.length );
		} else {
			formula = formula
				// the option/element value
				.replace( /{this.value}/g, '' )
				// the option/element value length
				.replace( /{this.value.length}/g, 0 );
		}

		formula = formula
			// the number of options the user has selected
			.replace( /{this.count}/g, thisVal.floatingBoxData.length )
			// the total option quantity of this element
			.replace(
				/{this.count.quantity}/g,
				thisVal.floatingBoxData
					.map( function( x ) {
						return x.quantity;
					} )
					.reduce( function( acc, thisval ) {
						return $.epoAPI.math.toFloat( acc ) + $.epoAPI.math.toFloat( thisval );
					}, 0 )
			)
			// the option quantity of this element
			.replace( /{this.quantity}/g, thisElement.data( 'tm-quantity' ) );

		if ( formula.match( /\{(\s)*?field\.([^}]*)}/ ) ) {
			matches = formula.match( /\{(\s)*?field\.([^}]*)}/g );
			matches.forEach( function( field ) {
				match = field.match( /\{(\s)*?field\.([^}]*)}/ );
				if ( undefined !== match[ 2 ] && 'string' === typeof match[ 2 ] ) {
					pos = match[ 2 ].lastIndexOf( '.' );
					if ( pos !== -1 ) {
						id = match[ 2 ].substr( 0, pos );
						type = match[ 2 ].substr( pos + 1 );

						if ( $.inArray( type, [ 'price', 'value', 'quantity', 'count' ] ) !== -1 ) {
							elementWrap = this_epo_container.find( "[data-uniqid='" + $.epoAPI.util.escapeSelector( id ) + "']" );

							if ( elementWrap.length ) {
								element = elementWrap.find( '.tmcp-field, .tmcp-fee-field' );

								if ( ! noevents && ! thisElement.data( 'addedfieldtcEpoBeforeOptionPriceCalculation' ) ) {
									jWindow.on( 'tcEpoBeforeOptionPriceCalculation.math', function() {
										tm_element_epo_rules( epoObject, thisElement, undefined, undefined, undefined, true );
										thisElement.trigger( 'tm-math-select-change-html-all' );
									} );
									thisElement.data( 'addedfieldtcEpoBeforeOptionPriceCalculation', 1 );
								}

								element = element.first();
								val = 0;

								if ( elementWrap.is( '.tc-container-enabled' ) ) {
									if ( element.is( '.tmcp-field' ) ) {
										val = fetchOptionPrices( epoObject, elementWrap, '.tmcp-field', 0, 0, [], true );
									} else if ( element.is( '.tmcp-fee-field' ) ) {
										val = fetchOptionPrices( epoObject, elementWrap, '.tmcp-fee-field', 0, 0, [], true );
									}

									switch ( type ) {
										// element price
										case 'price':
											val = val.total;
											break;

										// element value
										case 'value':
											if ( val.floatingBoxData ) {
												val = val.floatingBoxData
													.map( function( x ) {
														return $.epoAPI.math.unformat( x.valueText, tcAPI.localInputDecimalSeparator );
													} )
													.reduce( function( acc, thisval ) {
														return $.epoAPI.math.toFloat( acc ) + $.epoAPI.math.toFloat( thisval );
													}, 0 );
											}
											break;

										// element quantity
										case 'quantity':
											if ( val.floatingBoxData ) {
												val = val.floatingBoxData
													.map( function( x ) {
														return x.quantity;
													} )
													.reduce( function( acc, thisval ) {
														return $.epoAPI.math.toFloat( acc ) + $.epoAPI.math.toFloat( thisval );
													}, 0 );
											}
											break;

										// number of element options the user has selected
										case 'count':
											if ( val.floatingBoxData ) {
												val = val.floatingBoxData.length;
											}
											break;
									}
									val = $.epoAPI.math.toFloat( val );
									if ( ! Number.isFinite( val ) ) {
										val = 0;
									}
								}
							} else {
								val = 0;
							}

							reg = new RegExp( match[ 0 ] );
							formula = ! Number.isFinite( val ) ? formula.replace( reg, "'" + val + "'" ) : formula.replace( reg, val );
						}
					}
				}
			} );
		}

		try {
			elementPrice = tcmexp.eval( formula );
		} catch ( e ) {
			elementPrice = 0;
		}

		return elementPrice;
	}

	/**
	 * Set field price rules
	 */
	function get_price_type( epoObject, obj ) {
		var element = $( obj );
		var setter = element;
		var cart;
		var current_variation;
		var rules;
		var rulestype;
		var _rulestype;
		var pricetype;
		var variation_id_selector;
		var _tmcpulwrap;

		cart = epoObject.main_cart;
		variation_id_selector = "input[name^='variation_id']";
		if ( cart.find( 'input.variation_id' ).length > 0 ) {
			variation_id_selector = 'input.variation_id';
		}
		current_variation = cart.find( variation_id_selector ).val();
		// Get current woocommerce variation
		if ( ! current_variation ) {
			current_variation = 0;
		}

		if ( element.is( 'select' ) ) {
			setter = element.find( 'option:selected' );
		}

		rules = $.epoAPI.util.parseJSON( setter.attr( 'data-rules' ) );
		rulestype = $.epoAPI.util.parseJSON( setter.attr( 'data-rulestype' ) );

		pricetype = '';
		if ( typeof rules === 'object' ) {
			if ( typeof rulestype === 'object' ) {
				if ( current_variation in rulestype ) {
					pricetype = rulestype[ current_variation ];
				} else {
					_rulestype = $.epoAPI.util.parseJSON( element.closest( '.tmcp-ul-wrap' ).attr( 'data-rulestype' ) );
					if ( typeof _rulestype === 'object' ) {
						if ( current_variation in _rulestype ) {
							pricetype = _rulestype[ current_variation ];
						} else {
							pricetype = rulestype[ 0 ];
						}
					} else {
						pricetype = rulestype[ 0 ];
					}
				}
			} else {
				rulestype = $.epoAPI.util.parseJSON( element.closest( '.tmcp-ul-wrap' ).attr( 'data-ulestype' ) );
				if ( typeof rulestype === 'object' ) {
					if ( current_variation in rulestype ) {
						pricetype = rulestype[ current_variation ];
					} else {
						pricetype = rulestype[ 0 ];
					}
				}
			}
		} else {
			_tmcpulwrap = element.closest( '.tmcp-ul-wrap' );
			rules = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-rules' ) );

			if ( typeof rules === 'object' ) {
				if ( typeof rulestype === 'object' ) {
					if ( current_variation in rulestype ) {
						pricetype = rulestype[ current_variation ];
					} else {
						_rulestype = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-rulestype' ) );
						if ( typeof _rulestype === 'object' ) {
							if ( current_variation in _rulestype ) {
								pricetype = _rulestype[ current_variation ];
							} else {
								pricetype = rulestype[ 0 ];
							}
						} else {
							pricetype = rulestype[ 0 ];
						}
					}
				} else {
					rulestype = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-rulestype' ) );
					if ( typeof rulestype === 'object' ) {
						if ( current_variation in rulestype ) {
							pricetype = rulestype[ current_variation ];
						} else {
							pricetype = rulestype[ 0 ];
						}
					}
				}
			}
		}

		if ( typeof pricetype === 'object' ) {
			pricetype = pricetype[ 0 ];
		}
		if ( element.is( '.tmcp-fee-field' ) ) {
			if ( $.inArray( pricetype, [ 'fee', 'stepfee', 'currentstepfee' ] ) !== -1 ) {
				pricetype = '';
			}
		}

		return pricetype;
	}

	/**
	 * Set field price rules
	 */
	function tm_element_epo_rules( epoObject, obj, args, setter_override, noremove, manthnoevent ) {
		var element = $( obj );
		var setter = element;
		var cart;
		var current_variation;
		var bundleid;
		var epoTotalsContainer;
		var apply_dpd;
		var product_price;
		var product_original_price;
		var is_range_field = element.is( '.tmcp-range' );
		var rules;
		var rulestype;
		var original_rules;
		var _rules;
		var _rulestype;
		var _original_rules;
		var pricetype;
		var price;
		var original_price;
		var formatted_price;
		var original_formatted_price;
		var textlength;
		var freechars;
		var min_value;
		var main_product = epoObject.main_product;
		var epoEventId = epoObject.epoEventId;
		var this_epo_totals_container = epoObject.this_epo_totals_container;
		var variation_id_selector;
		var _tmcpulwrap;
		var cart_total;
		var per_product_pricing = true;

		if ( element.data( 'associated_price_set' ) ) {
			return;
		}

		if ( ! args ) {
			cart = epoObject.main_cart;
			if ( cart.data( 'per_product_pricing' ) !== undefined ) {
				per_product_pricing = cart.data( 'per_product_pricing' );
			}
			variation_id_selector = "input[name^='variation_id']";
			if ( cart.find( 'input.variation_id' ).length > 0 ) {
				variation_id_selector = 'input.variation_id';
			}
			current_variation = cart.find( variation_id_selector ).val();

			bundleid = $.epoAPI.applyFilter( 'tc_get_bundleid', cart.attr( 'data-product_id' ), cart );

			// Get current woocommerce variation
			if ( ! current_variation ) {
				current_variation = 0;
			}

			epoTotalsContainer = $.epoAPI.applyFilter( 'tc_get_totals_container', this_epo_totals_container, element, main_product, bundleid );

			product_price = tm_calculate_product_price( epoTotalsContainer );
			product_original_price = tm_calculate_product_regular_price( epoTotalsContainer );
			apply_dpd = epoTotalsContainer.data( 'fields-price-rules' );
		} else {
			cart = args.cart;
			current_variation = args.current_variation;

			bundleid = args.bundleid;
			epoTotalsContainer = args.epoTotalsContainer;
			product_price = args.product_price;
			product_original_price = args.product_original_price;
			apply_dpd = args.apply_dpd;
			per_product_pricing = args.per_product_pricing;
		}

		product_price = $.epoAPI.applyFilter( 'tc_alter_product_price', product_price, element, cart, epoTotalsContainer, bundleid );
		product_original_price = $.epoAPI.applyFilter( 'tc_alter_product_original_price', product_original_price, element, cart, epoTotalsContainer );

		if ( product_price === false || ! per_product_pricing ) {
			return;
		}

		if ( element.is( 'select' ) ) {
			setter = element.find( 'option:selected' );
		}
		if ( setter_override ) {
			setter = setter_override;
		}

		rules = $.epoAPI.util.parseJSON( setter.attr( 'data-rules' ) );
		rulestype = $.epoAPI.util.parseJSON( setter.attr( 'data-rulestype' ) );
		original_rules = $.epoAPI.util.parseJSON( setter.attr( 'data-original-rules' ) );

		if ( original_rules === undefined ) {
			original_rules = rules;
		}

		pricetype = '';
		if ( typeof rules === 'object' ) {
			if ( current_variation in rules ) {
				price = rules[ current_variation ];
				original_price = original_rules[ current_variation ];
			} else {
				_rules = $.epoAPI.util.parseJSON( element.closest( '.tmcp-ul-wrap' ).attr( 'data-rules' ) );
				_original_rules = element.closest( '.tmcp-ul-wrap' ).data( 'original-rules' );

				if ( typeof _rules === 'object' ) {
					if ( current_variation in _rules ) {
						price = _rules[ current_variation ];
					} else {
						price = rules[ 0 ];
					}
				} else {
					price = rules[ 0 ];
				}

				if ( typeof _original_rules === 'object' ) {
					if ( current_variation in _original_rules ) {
						original_price = _original_rules[ current_variation ];
					} else {
						original_price = original_rules[ 0 ];
					}
				} else {
					original_price = original_rules[ 0 ];
				}
			}

			if ( typeof rulestype === 'object' ) {
				if ( current_variation in rulestype ) {
					pricetype = rulestype[ current_variation ];
				} else {
					_rulestype = $.epoAPI.util.parseJSON( element.closest( '.tmcp-ul-wrap' ).attr( 'data-rulestype' ) );
					if ( typeof _rulestype === 'object' ) {
						if ( current_variation in _rulestype ) {
							pricetype = _rulestype[ current_variation ];
						} else {
							pricetype = rulestype[ 0 ];
						}
					} else {
						pricetype = rulestype[ 0 ];
					}
				}
			} else {
				rulestype = $.epoAPI.util.parseJSON( element.closest( '.tmcp-ul-wrap' ).attr( 'data-ulestype' ) );
				if ( typeof rulestype === 'object' ) {
					if ( current_variation in rulestype ) {
						pricetype = rulestype[ current_variation ];
					} else {
						pricetype = rulestype[ 0 ];
					}
				}
			}
		} else {
			_tmcpulwrap = element.closest( '.tmcp-ul-wrap' );
			rules = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-rules' ) );
			original_rules = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-original-rules' ) );

			if ( typeof rules === 'object' ) {
				if ( current_variation in rules ) {
					price = rules[ current_variation ];
				} else {
					price = rules[ 0 ];
				}
				if ( typeof original_rules === 'object' ) {
					if ( current_variation in original_rules ) {
						original_price = original_rules[ current_variation ];
					} else {
						original_price = original_rules[ 0 ];
					}
				} else {
					original_price = price;
				}

				if ( typeof rulestype === 'object' ) {
					if ( current_variation in rulestype ) {
						pricetype = rulestype[ current_variation ];
					} else {
						_rulestype = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-rulestype' ) );
						if ( typeof _rulestype === 'object' ) {
							if ( current_variation in _rulestype ) {
								pricetype = _rulestype[ current_variation ];
							} else {
								pricetype = rulestype[ 0 ];
							}
						} else {
							pricetype = rulestype[ 0 ];
						}
					}
				} else {
					rulestype = $.epoAPI.util.parseJSON( _tmcpulwrap.attr( 'data-rulestype' ) );
					if ( typeof rulestype === 'object' ) {
						if ( current_variation in rulestype ) {
							pricetype = rulestype[ current_variation ];
						} else {
							pricetype = rulestype[ 0 ];
						}
					}
				}
			}
		}

		if ( typeof pricetype === 'object' ) {
			pricetype = pricetype[ 0 ];
		}
		if ( element.is( '.tmcp-fee-field' ) ) {
			if ( $.inArray( pricetype, [ 'fee', 'stepfee', 'currentstepfee' ] ) !== -1 ) {
				pricetype = '';
			}
			apply_dpd = 0;
		}

		if ( noremove === undefined ) {
			if ( element.is( 'select' ) ) {
				element.find( 'option' ).removeClass( 'tm-epo-late-field' ).removeData( 'tm-price-for-late islate' );
			} else {
				setter.removeClass( 'tm-epo-late-field' ).removeData( 'tm-price-for-late islate' );
			}
		}

		price = cleanPrice( price );
		original_price = cleanPrice( original_price );

		switch ( pricetype ) {
			case '':
				price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd );
				original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd );
				break;
			case 'math':
				price = calculateMathPrice( price, element, epoObject, manthnoevent );
				price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd );
				original_price = calculateMathPrice( original_price, element, epoObject, true );
				original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd );
				break;
			case 'percent_cart_total':
				cart_total = parseFloat( TMEPOJS.cart_total );

				if ( ! Number.isFinite( cart_total ) ) {
					cart_total = 0;
				}
				price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * cart_total;
				original_price = ( tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) / 100 ) * cart_total;
				break;

			case 'percent':
				price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price;
				original_price = ( tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) / 100 ) * product_original_price;
				break;
			case 'percentcurrenttotal':
				//price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd );
				$.tcepo.lateFieldsPrices[ epoEventId ].push( {
					setter: setter,
					price: price,
					original_price: original_price,
					bundleid: bundleid,
					pricetype: pricetype
				} );
				setter.data( 'tm-price-for-late', price ).data( 'tm-original-price-for-late', original_price ).data( 'islate', 1 ).addClass( 'tm-epo-late-field' );
				price = 0;
				original_price = 0;
				break;
			case 'fixedcurrenttotal':
				price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd );
				$.tcepo.lateFieldsPrices[ epoEventId ].push( {
					setter: setter,
					price: price,
					original_price: original_price,
					bundleid: bundleid,
					pricetype: pricetype
				} );
				setter.data( 'tm-price-for-late', price ).data( 'tm-original-price-for-late', original_price ).data( 'islate', 1 ).addClass( 'tm-epo-late-field' );
				price = 0;
				original_price = 0;
				break;
			case 'word':
				price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * ( setter.val().split( /\w+/ ).length - 1 );
				original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) * ( setter.val().split( /\w+/ ).length - 1 );
				break;
			case 'wordpercent':
				price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price * ( setter.val().split( /\w+/ ).length - 1 );
				original_price = ( tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) / 100 ) * product_original_price * ( setter.val().split( /\w+/ ).length - 1 );
				break;
			case 'wordnon':
				freechars = parseInt( setter.attr( 'data-freechars' ), 10 );
				if ( ! Number.isFinite( freechars ) ) {
					freechars = 0;
				}
				textlength = setter.val().split( /\w+/ ).length - 1 - freechars;
				if ( textlength < 0 ) {
					textlength = 0;
				}
				price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * textlength;
				original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) * textlength;
				break;
			case 'wordpercentnon':
				freechars = parseInt( setter.attr( 'data-freechars' ), 10 );
				if ( ! Number.isFinite( freechars ) ) {
					freechars = 0;
				}
				textlength = setter.val().split( /\w+/ ).length - 1 - freechars;
				if ( textlength < 0 ) {
					textlength = 0;
				}
				price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price * textlength;
				original_price = ( tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) / 100 ) * product_original_price * textlength;
				break;

			case 'char':
				price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * setter.val().length;
				original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) * setter.val().length;
				break;
			case 'charpercent':
				price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price * setter.val().length;
				original_price = ( tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) / 100 ) * product_original_price * setter.val().length;
				break;
			case 'charnospaces':
				price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * setter.val().replace( /\s/g, '' ).length;
				original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) * setter.val().replace( /\s/g, '' ).length;
				break;
			case 'charnofirst':
				textlength = setter.val().length - 1;
				if ( textlength < 0 ) {
					textlength = 0;
				}
				price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * textlength;
				original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) * textlength;
				break;

			case 'charnon':
				freechars = parseInt( setter.attr( 'data-freechars' ), 10 );
				if ( ! Number.isFinite( freechars ) ) {
					freechars = 0;
				}
				textlength = setter.val().length - freechars;
				if ( textlength < 0 ) {
					textlength = 0;
				}
				price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * textlength;
				original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) * textlength;
				break;
			case 'charpercentnon':
				freechars = parseInt( setter.attr( 'data-freechars' ), 10 );
				if ( ! Number.isFinite( freechars ) ) {
					freechars = 0;
				}
				textlength = setter.val().length - freechars;
				if ( textlength < 0 ) {
					textlength = 0;
				}
				price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price * textlength;
				original_price = ( tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) / 100 ) * product_original_price * textlength;
				break;
			case 'charnonnospaces':
				freechars = parseInt( setter.attr( 'data-freechars' ), 10 );
				if ( ! Number.isFinite( freechars ) ) {
					freechars = 0;
				}
				textlength = setter.val().replace( /\s/g, '' ).length - freechars;
				if ( textlength < 0 ) {
					textlength = 0;
				}
				price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * textlength;
				original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) * textlength;
				break;
			case 'charpercentnonnospaces':
				freechars = parseInt( setter.attr( 'data-freechars' ), 10 );
				if ( ! Number.isFinite( freechars ) ) {
					freechars = 0;
				}
				textlength = setter.val().replace( /\s/g, '' ).length - freechars;
				if ( textlength < 0 ) {
					textlength = 0;
				}
				price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price * textlength;
				original_price = ( tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) / 100 ) * product_original_price * textlength;
				break;

			case 'charpercentnofirst':
				textlength = setter.val().length - 1;
				if ( textlength < 0 ) {
					textlength = 0;
				}
				price = ( tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) / 100 ) * product_price * textlength;
				original_price = ( tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) / 100 ) * product_original_price * textlength;
				break;
			case 'step':
				if ( is_range_field ) {
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * setter.val();
					original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) * $.epoAPI.math.toFloat( setter.val() );
				} else {
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * $.epoAPI.math.toFloat( setter.val() );
					original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) * $.epoAPI.math.toFloat( setter.val() );
				}
				break;
			case 'currentstep':
				if ( is_range_field ) {
					price = tm_apply_dpd( setter.val(), epoTotalsContainer, apply_dpd );
					original_price = tm_apply_dpd( setter.val(), epoTotalsContainer, apply_dpd );
				} else {
					price = tm_apply_dpd( $.epoAPI.math.toFloat( setter.val() ), epoTotalsContainer, apply_dpd );
					original_price = tm_apply_dpd( $.epoAPI.math.toFloat( setter.val() ), epoTotalsContainer, apply_dpd );
				}
				break;
			case 'intervalstep':
				if ( is_range_field ) {
					min_value = parseFloat( $( '.tm-range-picker[data-field-id="' + setter.attr( 'id' ) + '"]' ).attr( 'data-min' ) );
					price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * ( setter.val() - min_value );
					original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) * ( setter.val() - min_value );
				}
				break;
			case 'row':
				price = tm_apply_dpd( price, epoTotalsContainer, apply_dpd ) * ( ( setter.val().match( /(\r\n|\n|\r)/gm ) || [] ).length + 1 );
				original_price = tm_apply_dpd( original_price, epoTotalsContainer, apply_dpd ) * ( ( setter.val().match( /(\r\n|\n|\r)/gm ) || [] ).length + 1 );
				break;
		}

		if ( element.data( 'tm-quantity' ) ) {
			price = price * parseFloat( element.data( 'tm-quantity' ) );
			original_price = original_price * parseFloat( element.data( 'tm-quantity' ) );
		}

		formatted_price = tm_set_price( price, epoTotalsContainer, false, false, setter );
		original_formatted_price = tm_set_price( original_price, epoTotalsContainer, false, false, setter );

		element.data( 'price_set', 1 );
		setter.data( 'price_set', 1 );
		setter.data( 'raw_price', price );
		setter.data( 'raw_original_price', original_price );
		setter.data( 'price', tm_set_tax_price( price, epoTotalsContainer, setter ) );
		setter.data( 'original_price', tm_set_tax_price( original_price, epoTotalsContainer, setter ) );
		if ( ! setter_override ) {
			tm_update_price( setter.closest( '.tmcp-field-wrap' ).find( '.tc-price' ), price, formatted_price, original_price, original_formatted_price );
			element.data( 'price-changed', 1 );
		}
	}

	function tm_epo_rules( epoObject, theCart ) {
		var all_carts;
		var variation_id_selector;
		var per_product_pricing = true;
		var current_variation;
		var bundleid;
		var epoContainer;
		var epoTotalsContainer;
		var apply_dpd;
		var rules;
		var original_rules;
		var price;
		var formatted_price;
		var original_price;
		var original_formatted_price;
		var product_price;
		var product_original_price;
		var all_fields;
		var active_fields;
		var args;
		var main_product = epoObject.main_product;
		var epoEventId = epoObject.epoEventId;
		var this_epo_container = epoObject.this_epo_container;
		var this_epo_totals_container = epoObject.this_epo_totals_container;

		if ( ! theCart ) {
			all_carts = main_product.find( '.cart' );
		} else {
			all_carts = theCart;
		}
		if ( all_carts.length <= 0 ) {
			return;
		}

		$.tcepo.lateFieldsPrices[ epoEventId ] = [];

		all_carts.toArray().forEach( function( cart ) {
			cart = $( cart );
			variation_id_selector = "input[name^='variation_id']";
			if ( cart.find( 'input.variation_id' ).length > 0 ) {
				variation_id_selector = 'input.variation_id';
			}

			if ( cart.data( 'per_product_pricing' ) !== undefined ) {
				per_product_pricing = cart.data( 'per_product_pricing' );
			}

			//per_product_pricing = $.epoAPI.applyFilter("tc_get_per_product_pricing", per_product_pricing, cart);
			current_variation = cart.find( variation_id_selector ).val();
			bundleid = $.epoAPI.applyFilter( 'tc_get_bundleid', cart.attr( 'data-product_id' ), cart );

			// get current woocommerce variation
			if ( ! current_variation ) {
				current_variation = 0;
			}

			epoContainer = $.epoAPI.applyFilter( 'tc_get_epo_container', this_epo_container, cart, main_product, bundleid );
			epoTotalsContainer = $.epoAPI.applyFilter( 'tc_get_totals_container', this_epo_totals_container, cart, main_product, bundleid );

			// WooCommerce Dynamic Pricing & Discounts
			apply_dpd = epoTotalsContainer.data( 'fields-price-rules' );

			// set initial prices for all fields
			if ( ! epoContainer.data( 'tm_rules_init_done' ) ) {
				if ( epoTotalsContainer.data( 'force-quantity' ) ) {
					cart.find( tcAPI.qtySelector ).val( epoTotalsContainer.data( 'force-quantity' ) );
				}
				epoContainer.toArray().forEach( function( el ) {
					$( el ).closest( '.tmcp-field-wrap' ).find( '.tm-epo-field' ).data( 'tm-quantity', $( el ).val() );
				} );

				epoContainer
					.find( '.tmcp-attributes, .tmcp-elements' )
					.toArray()
					.forEach( function( element ) {
						element = $( element );
						rules = $.epoAPI.util.parseJSON( element.attr( 'data-rules' ) );
						original_rules = $.epoAPI.util.parseJSON( element.attr( 'data-original-rules' ) );

						// if rule doesn't exit then init an empty rule
						if ( typeof rules !== 'object' ) {
							rules = {
								0: '0'
							};
						}
						if ( typeof original_rules !== 'object' ) {
							original_rules = {
								0: '0'
							};
						}
						if ( typeof rules === 'object' ) {
							// we skip price validation test so that every field has at least a price of 0
							price = tm_apply_dpd( rules[ $.epoAPI.math.toFloat( current_variation ) ], epoTotalsContainer, apply_dpd );
							formatted_price = tm_set_price( price, epoTotalsContainer );
							original_price = tm_apply_dpd( original_rules[ $.epoAPI.math.toFloat( current_variation ) ], epoTotalsContainer, apply_dpd );
							original_formatted_price = tm_set_price( original_price, epoTotalsContainer );

							element
								.find( '.tmcp-field, .tmcp-fee-field ' )
								.toArray()
								.forEach( function( el ) {
									el = $( el );
									if ( per_product_pricing ) {
										if ( el.attr( 'data-no-price' ) === '1' ) {
											price = 0;
											original_price = 0;
										}
										el.data( 'raw_price', price );
										el.data( 'raw_original_price', original_price );

										el.data( 'price', tm_set_tax_price( price, epoTotalsContainer, el ) );
										el.data( 'original_price', tm_set_tax_price( original_price, epoTotalsContainer, el ) );

										tm_update_price( el.closest( '.tmcp-field-wrap' ).find( '.tc-price' ), price, formatted_price, original_price, original_formatted_price );
									} else {
										el.data( 'price', 0 );
										el.data( 'original_price', 0 );
										el.closest( '.tmcp-field-wrap' ).find( '.amount' ).empty();
									}
								} );
						}
					} );
				epoContainer.data( 'tm_rules_init_done', 1 );
			}

			// skip specific field rules if per_product_pricing is false
			if ( ! per_product_pricing ) {
				return true;
			}

			product_price = tm_calculate_product_price( epoTotalsContainer );
			product_original_price = tm_calculate_product_regular_price( epoTotalsContainer );

			args = {
				cart: cart,
				current_variation: current_variation,
				bundleid: bundleid,
				epoTotalsContainer: epoTotalsContainer,
				product_price: product_price,
				product_original_price: product_original_price,
				apply_dpd: apply_dpd,
				per_product_pricing: per_product_pricing
			};

			all_fields = epoContainer.find( '.tmcp-field,.tmcp-sub-fee-field,.tmcp-fee-field' );
			if ( ! epoObject.is_associated ) {
				all_fields = all_fields.not( tcAPI.associatedEpoSelector + ' ' + '.tmcp-field,' + tcAPI.associatedEpoSelector + ' ' + '.tmcp-sub-fee-field,' + tcAPI.associatedEpoSelector + ' ' + '.tmcp-fee-field' );
			}
			active_fields = all_fields.filter( '.tcenabled' );

			// note: find a better way if any
			if ( ! $.tcepo.initialActivation[ epoEventId ] || ( active_fields.length === 0 && all_fields.length > 0 ) ) {
				all_fields.each( function() {
					field_is_active( $( this ) );
				} );

				$.tcepo.initialActivation[ epoEventId ] = true;
			}

			//  apply specific field rules
			all_fields.filter( '.tcenabled' ).each( function( index, element ) {
				tm_element_epo_rules( epoObject, element, args );
			} );

			all_fields.each( function( index, element ) {
				$( element ).on( 'tc_element_epo_rules', function() {
					tm_element_epo_rules( epoObject, element, args );
				} );
			} );
		} );
	}

	function add_late_fields_prices( epoObject, originalProductPrice, options_total, original_options_total, bid, _cart, applydpd ) {
		var total = 0;
		var originalTotal = 0;
		var price;
		var originalPrice;
		var priceType;
		var setter;
		var id;
		var hidden;
		var bundleid;
		var realSetter;
		var productId;
		var epoId;
		var formattedPrice;
		var originalFormattedPrice;
		var epoEventId = epoObject.epoEventId;
		var taxPrice;
		var taxOriginalPrice;
		var hiddenName;
		var productPrice;
		var apply_dpd;

		if ( applydpd !== undefined ) {
			apply_dpd = applydpd;
		} else {
			apply_dpd = epoObject.this_epo_totals_container.data( 'fields-price-rules' );
		}
		productPrice = originalProductPrice;

		$.tcepo.lateFieldsPrices[ epoEventId ].forEach( function( field ) {
			price = field.price;
			originalPrice = field.original_price;
			priceType = field.pricetype;
			setter = field.setter;
			bundleid = field.bundleid;
			realSetter = setter;

			if ( priceType === 'percentcurrenttotal' ) {
				hiddenName = '_hidden';
			} else {
				hiddenName = '_hiddenfixed';
			}

			if ( setter.is( 'option' ) ) {
				realSetter = setter.closest( 'select' );
			}

			productPrice = parseFloat( $.epoAPI.applyFilter( 'tc_alter_product_price', originalProductPrice, realSetter, _cart, epoObject.this_epo_totals_container, bid ) );

			id = $.epoAPI.dom.id( realSetter.attr( 'name' ) );
			productId = $( '.tc-totals-form.tm-totals-form-' + _cart.attr( 'data-cart-id' ) ).attr( 'data-product-id' );
			epoId = $( '.tc-totals-form.tm-totals-form-' + _cart.attr( 'data-cart-id' ) ).attr( 'data-epo-id' );
			//workaround to support composite products
			hidden = $( '.tc-extra-product-options.tm-product-id-' + productId + "[data-epo-id='" + epoId + "']" ).find( '#' + id + hiddenName );

			if ( bundleid === bid ) {
				if ( priceType === 'percentcurrenttotal' ) {
					price = ( parseFloat( tm_apply_dpd( price, epoObject.this_epo_totals_container, apply_dpd ) ) / 100 ) * parseFloat( productPrice + options_total );
					originalPrice = ( parseFloat( tm_apply_dpd( originalPrice, epoObject.this_epo_totals_container, apply_dpd ) ) / 100 ) * parseFloat( productPrice + original_options_total );
				} else if ( priceType === 'fixedcurrenttotal' ) {
					price = parseFloat( tm_apply_dpd( price, epoObject.this_epo_totals_container, apply_dpd ) ) + parseFloat( productPrice + options_total );
					originalPrice = parseFloat( tm_apply_dpd( originalPrice, epoObject.this_epo_totals_container, apply_dpd ) ) + parseFloat( productPrice + original_options_total );
				}
				if ( realSetter.data( 'tm-quantity' ) ) {
					price = price * parseFloat( realSetter.data( 'tm-quantity' ) );
					originalPrice = originalPrice * parseFloat( realSetter.data( 'tm-quantity' ) );
				}

				if ( setter.data( 'isset' ) === 1 && field_is_active( setter ) ) {
					total = total + price;
					originalTotal = originalTotal + originalPrice;
				}

				taxPrice = tm_set_tax_price( price, _cart, setter );
				taxOriginalPrice = tm_set_tax_price( originalPrice, _cart, setter );

				formattedPrice = tm_set_price( price, _cart, false, false, setter );
				originalFormattedPrice = tm_set_price( originalPrice, _cart, false, false, setter );
				setter.data( 'price', taxPrice );
				setter.data( 'pricew', taxPrice );
				setter.data( 'original_price', taxOriginalPrice );
				setter.data( 'original_pricew', taxOriginalPrice );

				tm_update_price( setter.closest( '.tmcp-field-wrap' ).find( '.tc-price' ), taxPrice, formattedPrice, taxOriginalPrice, originalFormattedPrice );

				if ( hidden.length === 0 ) {
					realSetter.before( '<input type="hidden" id="' + id + hiddenName + '" name="' + id + hiddenName + '" value="' + tm_set_price_without_tax( price, _cart ) + '" />' );
				}
				if ( setter.is( '.tm-epo-field.tmcp-radio' ) ) {
					if ( setter.is( ':checked' ) ) {
						hidden.val( tm_set_price_without_tax( price, _cart ) );
					}
				} else {
					hidden.val( tm_set_price_without_tax( price, _cart ) );
				}
			} else if ( setter.data( 'pricew' ) !== undefined ) {
				formattedPrice = tm_set_price( setter.data( 'pricew' ), _cart, true, false, setter );
				originalFormattedPrice = '';

				if ( setter.data( 'original_pricew' ) !== undefined ) {
					originalFormattedPrice = tm_set_price( setter.data( 'original_pricew' ), _cart, true, false, setter );
				}

				tm_update_price( setter.closest( '.tmcp-field-wrap' ).find( '.tc-price' ), setter.data( 'pricew' ), formattedPrice, setter.data( 'original_pricew' ), originalFormattedPrice );
			}
		} );
		//$.tcepo.lateFieldsPrices[ epoEventId ] = [];

		return [ total, originalTotal ];
	}

	function tc_add_dimensions( epoObject ) {
		var selectors = [ '.tm-variation-ul-color', '.tm-variation-ul-image', '.tm-variation-ul-radiostart', '.tm-variation-ul-radioend', 'ul.use_images_container', 'ul.use_colors_container' ];
		var main_product = epoObject.main_product;
		var lis;
		var cpf_section;
		var el;
		var ew;

		$( '.tc-extra-product-options' ).addClass( 'tc-show-hidden' );

		selectors.forEach( function( selector ) {
			main_product
				.find( selector )
				.toArray()
				.forEach( function( ul ) {
					$( ul )
						.toArray()
						.forEach( function( s ) {
							s = $( s );
							lis = s.find( '.tmhexcolorimage-li-nowh' );
							if ( lis.length ) {
								cpf_section = s.closest( '.cpf-section' );
								el = lis.first();

								ew = 100;
								if ( cpf_section.length && cpf_section.find( '.tm-collapse-wrap.closed' ).length ) {
									cpf_section.find( '.tm-collapse-wrap' ).removeClass( 'closed' ).show();
									if ( lis.is( '.tc-mode-endcolor, .tc-mode-startcolor' ) ) {
										ew = el.css( 'line-height' );
									} else {
										ew = el.width() - 1 + 'px';
									}
									cpf_section.find( '.tm-collapse-wrap' ).addClass( 'closed' ).hide();
								} else if ( lis.is( '.tc-mode-endcolor, .tc-mode-startcolor' ) ) {
									ew = el.css( 'line-height' );
								} else {
									ew = el.width() - 1 + 'px';
								}

								lis.find( '.tmhexcolorimage' ).css( {
									'min-width': ew,
									'min-height': ew
								} );
							}
						} );
				} );
		} );

		$( '.tc-extra-product-options' ).removeClass( 'tc-show-hidden' );
	}

	function tm_lazyload() {
		var container;

		if ( TMEPOJS.tm_epo_no_lazy_load === 'yes' ) {
			return;
		}

		if ( tmLazyloadContainer ) {
			container = $( tmLazyloadContainer ).find( 'img.tmlazy' );
		} else {
			container = $( '.tc-extra-product-options img.tmlazy' );
		}

		container.lazyLoadXT();
		container.on( 'lazyshow', function() {
			jWindow.trigger( 'lazyLoadXToncomplete' );
		} );
	}

	function tm_css_styles( obj ) {
		var slider;
		var display;

		if ( ! obj ) {
			return;
		}

		obj.find( '.tm-owl-slider-section' ).each( function() {
			slider = $( this );
			display = slider.css( 'display' );

			slider.find( '.tm-slide' ).first().before( '<div class="tm-owl-slider"></div>' );
			slider.find( '.tm-slide' ).appendTo( slider.find( '.tm-owl-slider' ) );

			slider = slider.find( '.tm-owl-slider' );
			display = slider.css( 'display' );

			slider
				.show()
				.addClass( 'tcowl-carousel' )
				.tmowlCarousel( {
					rtl: TMEPOJS.isRTL === '1',
					dots: false,
					nav: true,
					items: 1,
					autoHeight: true,
					mouseDrag: false,
					touchDrag: true,
					//navigation:true,
					navText: [ TMEPOJS.i18n_prev_text, TMEPOJS.i18n_next_text ],
					navClass: [ 'owl-prev button', 'owl-next button' ],
					navElement: 'a',
					loop: false,
					navRewind: false
				} );

			slider.css( 'display', display );
		} );
	}

	function tm_set_color_pickers( obj ) {
		if ( ! obj ) {
			return;
		}
		if ( $( obj ).length ) {
			$( obj ).spectrum( {
				type: 'color',
				theme: 'epo',
				showButtons: true,
				clickoutFiresChange: false,
				chooseText: TMEPOJS.closeText,
				cancelText: TMEPOJS.i18n_cancel
			} );
			$( obj ).spectrum( 'enable' );
		}
	}

	function tm_set_lightbox( obj ) {
		if ( ! obj ) {
			return;
		}
		if ( $( obj ).length ) {
			jDocument.ready( function() {
				$( obj ).tclightbox();
			} );
		}
	}

	function has_active_changes_product_image( field ) {
		var uic = field.closest( '.tmcp-field-wrap' ).find( 'label img' );
		var src = $( uic ).first().attr( 'data-original' );

		if ( field.is( 'select.tm-product-image' ) ) {
			field = field.children( 'option:selected' );
		}

		if ( ! src ) {
			src = $( uic ).first().attr( 'src' );
		}
		if ( ! src ) {
			src = field.attr( 'data-image' );
		}
		if ( field.attr( 'data-imagep' ) ) {
			src = field.attr( 'data-imagep' );
		}
		if ( src ) {
			return true;
		}

		return false;
	}

	function tm_set_upload_fields( epoObject ) {
		var field;
		var dT;
		var name;
		var file;
		var selector = epoObject.is_associated
			? epoObject.this_epo_container.find( '.tm-epo-field.tmcp-upload' )
			: epoObject.this_epo_container.find( '.tm-epo-field.tmcp-upload' ).not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' .tm-epo-field.tmcp-upload' );

		try {
			selector
				.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector + ' .tm-epo-field.tmcp-upload' )
				.not( '.tm-multiple-file-upload' )
				.toArray()
				.forEach( function( el ) {
					field = $( el );
					if ( ClipboardEvent || DataTransfer ) {
						dT = new ClipboardEvent( '' ).clipboardData || new DataTransfer();
						dT.items.add( new File( [ field.attr( 'data-file' ) ], field.attr( 'data-filename' ) ) );
						el.files = dT.files;
					}
					name = field.attr( 'name' );
					file = field.attr( 'data-file' );
					field.trigger( 'change.tcupload' );
					field.after( '<input type="hidden" class="tmcp-upload-hidden" name="' + name + '" value="' + file + '" />' );
					field.removeAttr( 'data-file data-filename' );
				} );
		} catch ( err ) {
			window.console.log( err );
			$( '.tm-epo-field.tmcp-upload' ).not( '.tm-multiple-file-upload' ).addClass( 'tc-nodt' );
			errorObject = err;
		}
	}

	function tm_set_upload_rules( epoObject ) {
		var epoEventId = epoObject.epoEventId;
		var this_epo_container = epoObject.this_epo_container;

		if ( TMEPOJS.tm_epo_upload_popup === 'yes' ) {
			$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
				trigger: function() {
					return true;
				},
				on_true: function() {
					var upload_fields = this_epo_container.data( 'num_uploads' );
					var thisPopup;
					var ajaxSuccessFunc;
					if ( upload_fields && Object.keys( upload_fields ).length ) {
						thisPopup = $.tcFloatBox( {
							fps: 1,
							ismodal: true,
							refresh: 'fixed',
							width: '50%',
							height: '300px',
							classname: 'flasho tm_wrapper',
							data: $.epoAPI.template.html( tcAPI.templateEngine.tc_upload_messages, {
								files: upload_fields,
								title: TMEPOJS.i18n_uploading_files,
								message: TMEPOJS.i18n_uploading_message
							} )
						} );
						ajaxSuccessFunc = function() {
							thisPopup.destroy();
							jDocument.off( 'ajaxSuccess', ajaxSuccessFunc );
						};
						jDocument.on( 'ajaxSuccess', ajaxSuccessFunc );
					}

					return true;
				},
				on_false: function() {
					return true;
				}
			};
		}
	}

	function tm_set_checkboxes_rules( epoObject ) {
		var this_epo_container = epoObject.this_epo_container;
		var main_product = epoObject.main_product;
		var epoEventId = epoObject.epoEventId;
		var limit_cont = this_epo_container.find( '.tm-limit' );
		var exactlimit_cont = this_epo_container.find( '.tm-exactlimit' );
		var minimumlimit_cont = this_epo_container.find( '.tm-minimumlimit' );

		// Limit checkbox selection
		this_epo_container.on( 'change.cpflimit', 'input.tm-epo-field.tmcp-checkbox', function() {
			var $this = $( this );
			tm_limit_c_selection( $this, true );
			tm_exact_c_selection( $this, true );
		} );
		if ( limit_cont.length ) {
			tm_check_limit_cont( limit_cont, main_product, epoEventId );
		}

		// Exact value checkbox check (Todo:check for isvisible)
		if ( exactlimit_cont.length ) {
			tm_check_exactlimit_cont( exactlimit_cont, main_product, epoEventId );
		}

		// Minimum number checkbox check (Todo:check for isvisible)
		if ( minimumlimit_cont.length ) {
			tm_check_minimumlimit_cont( minimumlimit_cont, epoEventId );
		}
	}

	function tm_theme_specific_actions( epoObject ) {
		var this_epo_totals_container = epoObject.this_epo_totals_container;
		var this_epo_container = epoObject.this_epo_container;
		var theme_name = this_epo_totals_container.attr( 'data-theme-name' );
		var all_epo_selects;
		var blaszok_selects;

		if ( theme_name ) {
			theme_name = theme_name.toLowerCase();
			all_epo_selects = this_epo_container.find( 'select' );

			switch ( theme_name ) {
				case 'flatsome':
				case 'flatsome-child':
				case 'flatsome child':
					all_epo_selects.wrap( '<div class="custom select-wrapper"/>' );
					break;

				case 'avada':
				case 'avada-child':
				case 'avada child':
					all_epo_selects.wrap( '<div class="avada-select-parent tm-select-parent"></div>' );
					$( '<div class="select-arrow">&#xe61f;</div>' ).appendTo( this_epo_container.find( '.tm-select-parent' ) );
					if ( window.calc_select_arrow_dimensions ) {
						window.calc_select_arrow_dimensions();
						jWindow.on( 'tmsectionpoplink cpflogicdone', function() {
							window.calc_select_arrow_dimensions();
						} );
					} else if ( window.calcSelectArrowDimensions ) {
						window.calcSelectArrowDimensions();
						jWindow.on( 'tmsectionpoplink cpflogicdone', function() {
							window.calcSelectArrowDimensions();
						} );
					}
					break;

				case 'bazar':
				case 'bazar-child':
				case 'bazar child':
					all_epo_selects.wrap( '<div class="tm-select-wrapper select-wrapper"/>' );
					break;

				case 'blaszok':
				case 'blaszok-child':
				case 'blaszok child':
					blaszok_selects = function() {
						setTimeout( function() {
							$( '.tm-extra-product-options select' )
								.not( '.hasCustomSelect' )
								.filter( ':visible' )
								.each( function() {
									if ( ! $( this ).is( '.mpcthSelect' ) ) {
										$( this ).width( $( this ).outerWidth() );
										$( this ).customSelect( { customClass: 'mpcthSelect' } );
									}
								} );
						}, 100 );
					};
					jWindow.on( 'cpflogicrun', function() {
						blaszok_selects();
					} );
					jWindow.on( 'epo_options_visible', function() {
						blaszok_selects();
					} );

					break;

				case 'handmade':
				case 'handmade child theme':
					$( '.tm-owl-slider.tcowl-carousel' ).addClass( 'manual' );
					break;
			}

			jWindow.trigger( 'tm-theme-specific-actions', {
				epo: {
					theme_name: theme_name,
					all_epo_selects: all_epo_selects
				}
			} );
		}

		// Fix added +/- quantity button on most themes.
		jDocument.off( 'click.cpf', '.quantity:not(.buttons_added) .minus, .quantity:not(.buttons_added) .plus' ).on( 'click.cpf', '.quantity:not(.buttons_added) .minus, .quantity:not(.buttons_added) .plus', function() {
			$( this ).closest( '.quantity' ).find( tcAPI.qtySelector ).trigger( 'change.cpf' );
		} );
	}

	function tm_custom_variations( epoObject, form, item_id, $main_product, $epo_holder ) {
		var epoEventId = epoObject.epoEventId;
		var variation_id_selector = "input[name^='variation_id']";
		var tm_epo_variation_section;
		var li_variations;
		var composite_load_test;
		var form_event;
		var type;
		var name;
		var selector;
		var func;
		var i;
		var eventName = epoObject.is_associated ? 'tc_variation_form.tmlogic' : 'wc_variation_form.tmlogic';
		var eventNamePrefix = epoObject.is_associated ? 'tc_' : '';
		var variationsForm = epoObject.variations_form;
		var variationsTable = epoObject.is_associated ? variationsForm.find( '.tc-epo-element-variations' ) : variationsForm.find( '.variations' );
		var resetSelector = epoObject.is_associated ? '.tc-epo-element-variable-reset-variations' : '.reset_variations';

		variationsForm.attr( 'data-epo_id', epoObject.epo_id );

		if ( form.find( 'input.variation_id' ).length > 0 ) {
			variation_id_selector = 'input.variation_id';
		}
		if ( $epo_holder.find( '.tm-epo-variation-element' ).length || $epo_holder.data( 'tm-epo-variation-element' ) ) {
			tm_epo_variation_section = $epo_holder.find( '.tm-epo-variation-section' ).first();
			tm_epo_variation_section.find( '.cpf-type-variations' ).attr( 'data-epo_id', epoObject.epo_id ).attr( 'data-product_id', variationsForm.attr( 'data-product_id' ) );

			$epo_holder.data( 'tm-epo-variation-element', tm_epo_variation_section.find( '.tm-epo-variation-element' ) );

			if ( item_id && item_id !== 'main' && ! epoObject.is_associated ) {
				// on composite

				variationsForm = epoObject.form;
				variationsTable = variationsForm.find( '.composite_component[data-item_id="' + item_id + '"]' ).find( '.variations' );
				variationsForm.attr( 'data-epo_id', epoObject.epo_id );

				if ( variationsTable.length === 0 ) {
					return;
				}

				li_variations = tm_epo_variation_section.closest( 'li.tm-extra-product-options-field' );
				if ( ! tm_epo_variation_section.is( '.tm-hidden' ) ) {
					variationsTable.hide();
				}

				variationsTable.after( tm_epo_variation_section.addClass( 'tm-extra-product-options nopadding' ) );
				if ( li_variations.is( ':empty' ) ) {
					li_variations.hide();
				}

				if ( ! tm_epo_variation_section.is( '.section_popup' ) ) {
					tm_epo_variation_section.removeClass( 'tc-cell' );
					tm_epo_variation_section.wrap( "<div class='tc-styled-variations'></div>" );
				} else {
					tm_epo_variation_section.wrap( "<div class='tc-styled-variations tc-row'></div>" );
				}

				composite_load_test = false;
				form.off( eventName ).on( eventName, function() {
					composite_load_test = true;
					variationsForm.on( 'click.tmlogic', '.reset_variations', function() {
						tm_epo_variation_section.find( 'select.tm-epo-variation-element' ).val( '' ).children( 'option' ).removeAttr( 'disabled' ).show();
						tm_epo_variation_section.find( '.tm-epo-variation-element' ).removeAttr( 'disabled' ).removeClass( 'tm-disabled' ).removeAttr( 'checked' ).prop( 'checked', false ).closest( 'li' ).show();
						jWindow.trigger( 'tmlazy' );
						tm_epo_variation_section.find( '.tm-epo-variation-element' ).trigger( 'tm_trigger_product_image' );
						tm_epo_variation_section.find( 'li' ).removeClass( 'tc-active tm-attribute-disabled' ).css( 'opacity', '' );
					} );

					// Disable option fields that are unavaiable for current set of attributes
					form.off( 'woocommerce_update_variation_values_tmlogic' ).on( 'woocommerce_update_variation_values_tmlogic', function() {
						tm_custom_variations_update( form, epoObject );
					} );
					for ( i = 0; i < lateVariationEvent.length; i += 1 ) {
						form_event = lateVariationEvent[ i ];
						type = typeof form_event;
						if ( type === 'object' ) {
							name = typeof form_event.name === 'string' || false;
							selector = typeof form_event.selector === 'string' || false;
							func = typeof form_event.func === 'function' || false;
							if ( name && func ) {
								if ( selector === "input[name='variation_id']" ) {
									selector = variation_id_selector;
								}
								if ( form_event.selector ) {
									form.data( 'tm-styled-variations', 1 )
										.off( eventNamePrefix + form_event.name, form_event.selector )
										.on( eventNamePrefix + form_event.name, form_event.selector, form_event.func );
								} else {
									form.data( 'tm-styled-variations', 1 )
										.off( eventNamePrefix + form_event.name )
										.on( eventNamePrefix + form_event.name, form_event.func );
								}
							}
						}
					}
					lateVariationEvent = [];
					tm_epo_variation_section.find( '.tm-epo-variation-element' ).last().trigger( 'tm_epo_variation_element_change' );
				} );
				jDocument.ready( function() {
					if ( composite_load_test === false ) {
						form.trigger( eventName );
					}
				} );
			} else {
				if ( tm_epo_variation_section.length ) {
					if ( ! tm_epo_variation_section.is( '.tm-hidden' ) ) {
						variationsTable.hide();
					}

					li_variations = tm_epo_variation_section.closest( 'li.tm-extra-product-options-field' );

					variationsTable.after( tm_epo_variation_section.addClass( 'tm-extra-product-options nopadding' ) );
					if ( li_variations.is( ':empty' ) ) {
						li_variations.hide();
					}

					if ( ! tm_epo_variation_section.is( '.section_popup' ) ) {
						tm_epo_variation_section.removeClass( 'tc-cell' );
						tm_epo_variation_section.wrap( "<div class='tc-styled-variations'></div>" );
					} else {
						tm_epo_variation_section.wrap( "<div class='tc-styled-variations tc-row'></div>" );
					}

					variationsForm.off( 'click.tmlogic', resetSelector ).on( 'click.tmlogic', resetSelector, function() {
						tm_epo_variation_section.find( 'select.tm-epo-variation-element' ).val( '' ).children( 'option' ).removeAttr( 'disabled' ).show();
						tm_epo_variation_section.find( '.tm-epo-variation-element' ).removeAttr( 'disabled' ).removeClass( 'tm-disabled' ).removeAttr( 'checked' ).prop( 'checked', false ).closest( 'li' ).show();
						jWindow.trigger( 'tmlazy' );
						tm_epo_variation_section.find( '.tm-epo-variation-element' ).trigger( 'tm_trigger_product_image' );
						tm_epo_variation_section.find( 'li' ).removeClass( 'tc-active tm-attribute-disabled' ).css( 'opacity', '' );
					} );
				}

				// Disable option fields that are unavaiable for current set of attributes
				variationsForm.off( 'woocommerce_update_variation_values_tmlogic' ).on( 'woocommerce_update_variation_values_tmlogic', function() {
					tm_custom_variations_update( variationsForm, epoObject );
				} );

				for ( i = 0; i < lateVariationEvent.length; i += 1 ) {
					form_event = lateVariationEvent[ i ];
					type = typeof form_event;
					if ( type === 'object' ) {
						name = typeof form_event.name === 'string' || false;
						selector = typeof form_event.selector === 'string' || false;
						func = typeof form_event.func === 'function' || false;
						if ( name && func ) {
							if ( selector === "input[name='variation_id']" ) {
								selector = variation_id_selector;
							}
							if ( form_event.selector ) {
								variationsForm
									.data( 'tm-styled-variations', 1 )
									.off( eventNamePrefix + form_event.name, form_event.selector )
									.on( eventNamePrefix + form_event.name, form_event.selector, form_event.func );
							} else {
								variationsForm
									.data( 'tm-styled-variations', 1 )
									.off( eventNamePrefix + form_event.name )
									.on( eventNamePrefix + form_event.name, form_event.func );
							}
						}
					}
				}
				lateVariationEvent = [];
				tm_epo_variation_section.find( '.tm-epo-variation-element' ).last().trigger( 'tm_epo_variation_element_change' );
			}

			// global event for custom variations
			$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
				trigger: function() {
					return true;
				},
				on_true: function() {
					tm_epo_variation_section.find( '.tm-epo-variation-element' ).attr( 'disabled', 'disabled' );
					return true;
				},
				on_false: function() {
					tm_epo_variation_section.find( '.tm-epo-variation-element' ).removeAttr( 'disabled' );
				}
			};

			$( document.body ).on( 'added_to_cart', function() {
				tm_epo_variation_section.find( '.tm-epo-variation-element' ).removeAttr( 'disabled' );
			} );
		}
	}

	function repopulate_backup_image_atts( img, product_element ) {
		var $gallery_img = product_element.find( '.flex-control-nav li:eq(0) img' );
		var $gallery_wrapper = product_element.find( '.woocommerce-product-gallery__wrapper ' );
		var $product_img_wrap = $gallery_wrapper.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 );
		var $product_img = img;
		var $product_link = img.closest( 'a' );

		$product_img.attr( 'data-o_' + 'src', $.tc_product_image_store[ 0 ].src );
		$product_img.attr( 'data-o_' + 'srcset', $.tc_product_image_store[ 0 ].srcset );
		$product_img.attr( 'data-o_' + 'sizes', $.tc_product_image_store[ 0 ].sizes );
		$product_img.attr( 'data-o_' + 'title', $.tc_product_image_store[ 0 ].title );
		$product_img.attr( 'data-o_' + 'alt', $.tc_product_image_store[ 0 ].alt );
		$product_img.attr( 'data-o_' + 'data-src', $.tc_product_image_store[ 0 ][ 'data-src' ] );
		$product_img.attr( 'data-o_' + 'data-large_image', $.tc_product_image_store[ 0 ][ 'data-large_image' ] );
		$product_img.attr( 'data-o_' + 'data-large_image_width', $.tc_product_image_store[ 0 ][ 'data-large_image_width' ] );
		$product_img.attr( 'data-o_' + 'data-large_image_height', $.tc_product_image_store[ 0 ][ 'data-large_image_height' ] );
		$product_img_wrap.attr( 'data-o_' + 'data-thumb', $.tc_product_image_store[ 1 ][ 'data-thumb' ] );
		if ( $.tc_product_image_store[ 2 ] ) {
			$gallery_img.attr( 'data-o_' + 'src', $.tc_product_image_store[ 2 ].src );
		}

		$product_link.attr( 'data-o_' + 'href', $.tc_product_image_store[ 3 ].href );
		$product_link.attr( 'data-o_' + 'title', $.tc_product_image_store[ 3 ].title );
	}

	function reset_saved_image( img, product_element ) {
		var $gallery_img = product_element.find( '.flex-control-nav li:eq(0) img' );
		var $gallery_wrapper = product_element.find( '.woocommerce-product-gallery__wrapper ' );
		var $product_img_wrap = $gallery_wrapper.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 );
		var $product_img = img;
		var $product_link = img.closest( 'a' );

		// backup current product image attributes
		if ( ! $.isEmptyObject( $.tc_product_image ) ) {
			$.tc_product_image_store = $.tc_pre_populate_store();
			$.tc_product_image_store = $.tc_maybe_copy_object_values( $.tc_product_image_store, $.tc_product_image );
		} else {
			$.tc_product_image_store = $.tc_populate_store( img, product_element );
		}

		$product_img.tc_update_attr( 'src', 0 );
		$product_img.tc_update_attr( 'srcset', 0 );
		$product_img.tc_update_attr( 'sizes', 0 );
		$product_img.tc_update_attr( 'title', 0 );
		$product_img.tc_update_attr( 'alt', 0 );
		$product_img.tc_update_attr( 'data-src', 0 );
		$product_img.tc_update_attr( 'data-large_image', 0 );
		$product_img.tc_update_attr( 'data-large_image_width', 0 );
		$product_img.tc_update_attr( 'data-large_image_height', 0 );
		$product_img_wrap.tc_update_attr( 'data-thumb', 1 );
		$gallery_img.tc_update_attr( 'src', 2 );

		$product_link.tc_update_attr( 'href', 3 );
		$product_link.tc_update_attr( 'title', 3 );
	}

	function image_update( data, img, product_element ) {
		var $gallery_img = product_element.find( '.flex-control-nav li:eq(0) img' );
		var $gallery_wrapper = product_element.find( '.woocommerce-product-gallery__wrapper ' );
		var $product_img_wrap = $gallery_wrapper.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 );
		var $product_img = img;
		var $product_link = img.closest( 'a' );

		if ( data && data.image_link && data.image_link && data.image_link.length > 1 ) {
			if ( data.full_src === null || data.full_src === '' ) {
				data.full_src = data.image_link;
			}
			if ( data.full_src_w === null || data.full_src_w === '' ) {
				data.full_src_w = $product_img.attr( 'data-large_image_width' );
			}
			if ( data.full_src_h === null || data.full_src_h === '' ) {
				data.full_src_h = $product_img.attr( 'data-large_image_height' );
			}
			if ( $product_img.length ) {
				if ( ! data.image_srcset ) {
					data.image_sizes = false;
				}
				if ( ! data.image_sizes ) {
					data.image_srcset = false;
				}
				$product_img.tc_set_attr( 'src', data.image_link, 0 );
				$product_img.tc_set_attr( 'srcset', data.image_srcset, 0 );
				$product_img.tc_set_attr( 'sizes', data.image_sizes, 0 );
				$product_img.tc_set_attr( 'title', data.image_title, 0 );
				$product_img.tc_set_attr( 'alt', data.image_alt, 0 );
				$product_img.tc_set_attr( 'data-src', data.full_src, 0 );
				$product_img.tc_set_attr( 'data-large_image', data.full_src, 0 );
				$product_img.tc_set_attr( 'data-large_image_width', data.full_src_w, 0 );
				$product_img.tc_set_attr( 'data-large_image_height', data.full_src_h, 0 );
				$product_img_wrap.tc_set_attr( 'data-thumb', data.image_link, 1 );
			}
			if ( $gallery_img.length ) {
				$gallery_img.tc_set_attr( 'src', data.image_link, 2 );
			}
			if ( $product_link.length ) {
				$product_link.tc_set_attr( 'href', data.full_src, 3 );
				$product_link.tc_set_attr( 'title', data.image_caption, 3 );
			}
		} else {
			if ( $product_img.length ) {
				$product_img.tc_reset_attr( 'src', 0 );
				$product_img.tc_reset_attr( 'srcset', 0 );
				$product_img.tc_reset_attr( 'sizes', 0 );
				$product_img.tc_reset_attr( 'title', 0 );
				$product_img.tc_reset_attr( 'alt', 0 );
				$product_img.tc_reset_attr( 'data-src', 0 );
				$product_img.tc_reset_attr( 'data-large_image', 0 );
				$product_img.tc_reset_attr( 'data-large_image_width', 0 );
				$product_img.tc_reset_attr( 'data-large_image_height', 0 );
				$product_img_wrap.tc_reset_attr( 'data-thumb', 1 );
			}
			if ( $gallery_img.length ) {
				$gallery_img.tc_reset_attr( 'src', 2 );
			}
			if ( $product_link.length ) {
				$product_link.tc_reset_attr( 'href', 3 );
				$product_link.tc_reset_attr( 'title', 3 );
			}
		}
	}

	function get_main_product_image( epoObject, product_element ) {
		var img;

		if ( epoObject.is_associated ) {
			img = product_element.find( '.tc-product-image .wp-post-image' ).first();
		} else if ( TMEPOJS.tm_epo_global_product_image_selector !== '' ) {
			img = $( TMEPOJS.tm_epo_global_product_image_selector );
		} else {
			img = product_element.find( '.woocommerce-product-gallery__image:not(.clone), .woocommerce-product-gallery__image--placeholder:not(.clone)' ).eq( 0 ).find( '.wp-post-image' ).first();
			if ( img.length === 0 ) {
				img = product_element.find( 'a.woocommerce-main-image img, img.woocommerce-main-image,a img' ).not( '.thumbnails img,.product_list_widget img' ).first();
			}
			if ( img.length === 0 ) {
				img = product_element.find( '.elementor-widget-ae-post-image .wp-post-image' ).first();
			}
		}

		if ( $( img ).length > 1 ) {
			img = $( img ).first();
		}

		return img;
	}

	function get_product_element( epoObject ) {
		var main_product;
		var product_id;
		var product_element;

		if ( epoObject.is_associated ) {
			return epoObject.main_product.closest( '.tc-epo-element-product-container' );
		}

		main_product = epoObject.main_product;
		product_id = epoObject.product_id;
		product_element = main_product.closest( '#product-' + product_id );

		if ( product_element.length <= 0 ) {
			product_element = main_product.closest( '.post-' + product_id );
		}

		return product_element;
	}

	function gallery_compatibility_actions( gallery_type, clone_image, preload_img, visible, event_data, $form, product_element ) {
		var gallery;
		var _elements;
		var ge;
		var galleryWidth;
		var zoomEnabled;
		var image;
		var zoom_options;

		for ( gallery in gallery_type ) {
			if ( Object.prototype.hasOwnProperty.call( gallery_type, gallery ) ) {
				gallery = gallery_type[ gallery ];

				if ( gallery.enabled ) {
					switch ( gallery.type ) {
						case 'yith':
							if ( ! clone_image ) {
								if ( ! visible ) {
									gallery.yith_wcmg_zoom.attr( 'href', gallery._yith_wcmg_default_zoom );
								} else {
									gallery.yith_wcmg_zoom.attr( 'href', gallery.yith_wcmg_default_zoom );
								}
								if ( gallery.element.data( 'yith_magnifier' ) ) {
									gallery.element.yith_magnifier( 'destroy' );
								}

								gallery.element.yith_magnifier( window.yith_magnifier_options );
							} else {
								clone_image.attr( 'srcset', preload_img ).attr( 'src-orig', preload_img );

								if ( gallery.element.data( 'yith_magnifier' ) ) {
									gallery.element.yith_magnifier( 'destroy' );
								}
								_elements = {
									elements: {
										zoom: $( '.yith_magnifier_zoom' ),
										zoomImage: clone_image,
										gallery: $( '.yith_magnifier_gallery li a' )
									}
								};

								gallery.element.yith_magnifier( $.extend( true, {}, window.yith_magnifier_options, _elements ) );
							}
							break;
						case 'iosslider':
							setTimeout(
								function( g ) {
									g.element.iosSlider( 'update' );
								}.bind( null, gallery ),
								150
							);
							break;
						case 'flexslider':
							jWindow.trigger( 'resize' );
							break;
						case 'elevatezoom':
							gallery.element.each(
								function( g, j ) {
									var elevateZoom = j( this ).data( 'elevateZoom' );
									if ( typeof elevateZoom !== 'undefined' ) {
										elevateZoom.swaptheimage( g, g );
									}
								}.bind( null, preload_img, $ )
							);
							break;
						case 'easyzoom':
							gallery.element.swap( null, preload_img );
							break;
						case 'easyzoom-flatsome':
							gallery.element.swap( preload_img, preload_img );
							break;
						case 'woocommerce':
							gallery.element.flexslider( 0 );
							ge = gallery.element;
							window.setTimeout(
								function( g, w ) {
									g.trigger( 'woocommerce_gallery_init_zoom' );
									w.trigger( 'resize' );
								}.bind( null, ge, jWindow ),
								10
							);
							break;
						case 'zoom':
							if ( product_element ) {
								galleryWidth = product_element.find( '.woocommerce-product-gallery--with-images' ).width();
								zoomEnabled = false;

								image = gallery.element.find( 'img.wp-post-image' );
								if ( image.attr( 'data-large_image_width' ) > galleryWidth ) {
									zoomEnabled = true;
								}

								if ( zoomEnabled ) {
									zoom_options = {
										touch: false
									};
									if ( 'ontouchstart' in window ) {
										zoom_options.on = 'click';
									}

									gallery.element.trigger( 'zoom.destroy' );
									gallery.element.zoom( zoom_options );
								} else {
									gallery.element.trigger( 'zoom.destroy' );
								}
							}
							break;
					}
				}
			}
		}

		jWindow.trigger( 'tm_gallery_compatibility_actions', {
			event_data: event_data,
			product_element: product_element,
			form: $form
		} );
	}

	function get_gallery_type( epoObject, img, product_element ) {
		// YITH WooCommerce Zoom Magnifier
		var is_yith_wcmg;
		var yith_wcmg;
		var yith_wcmg_zoom;
		var yith_wcmg_default_zoom;
		var _yith_wcmg_default_zoom;
		var yith_wcmg_default_image;

		// iosslider – Touch Enabled, Responsive jQuery Horizontal Content
		// Slider/Carousel/Image Gallery Plugin
		var is_iosSlider;
		var is_iosSlider_element;

		// ThemeFusion flexslider
		var is_flexslider;
		var is_flexslider_element;

		// elevateZoom A Jquery Image Zoom Plugin
		var is_elevateZoom;
		var is_elevateZoom_obj;

		// EasyZoom jQuery image zoom plugin
		var is_easyzoom;
		var is_easyzoom_element;

		// new flatsome easyzoom
		var is_easyzoom_flatsome;
		var is_easyzoom_flatsome_element;

		// WooCommerce 2.7x gallery
		var is_wc27_gallery;
		var is_wc27_gallery_element;
		var wc27_zoom_target;
		var wc_single_product_params;
		var zoom_target_temp;

		// fn.zoom
		var is_zoom_enabled;
		var zoom_images;
		var gallery;

		if ( epoObject.is_associated ) {
			return {};
		}

		// YITH WooCommerce Zoom Magnifier
		is_yith_wcmg = false;
		yith_wcmg = $( '.images' );
		yith_wcmg_zoom = $( '.yith_magnifier_zoom' );
		yith_wcmg_default_zoom = yith_wcmg.find( '.yith_magnifier_zoom' ).first().attr( 'href' );
		_yith_wcmg_default_zoom = yith_wcmg_default_zoom;
		yith_wcmg_default_image = yith_wcmg.find( '.yith_magnifier_zoom img' ).first().attr( 'src' );

		// iosslider – Touch Enabled, Responsive jQuery Horizontal Content
		// Slider/Carousel/Image Gallery Plugin
		is_iosSlider = false;
		is_iosSlider_element = $( '.iosSlider.product-gallery-slider,.iosSlider.product-slider' );

		// ThemeFusion flexslider
		is_flexslider = false;
		is_flexslider_element = product_element.find( '.images .fusion-flexslider' );

		// elevateZoom A Jquery Image Zoom Plugin
		is_elevateZoom = img.data( 'elevateZoom' ) || false;
		is_elevateZoom_obj = product_element.find( 'div.product-images .woocommerce-main-image' );

		// EasyZoom jQuery image zoom plugin
		is_easyzoom = false;
		is_easyzoom_element = product_element.find( '.images .easyzoom' );

		// new flatsome easyzoom
		is_easyzoom_flatsome = false;
		is_easyzoom_flatsome_element = product_element.find( '.images .easyzoom' );

		// WooCommerce 2.7x gallery
		is_wc27_gallery = false;
		is_wc27_gallery_element = product_element.find( '.woocommerce-product-gallery' );
		wc27_zoom_target = false;
		wc_single_product_params = window.wc_single_product_params;

		// fn.zoom
		is_zoom_enabled = typeof $.fn.zoom === 'function' && wc_single_product_params && wc_single_product_params.zoom_enabled;
		zoom_images = false;

		if ( window.yith_magnifier_options && yith_wcmg.data( 'yith_magnifier' ) ) {
			is_yith_wcmg = true;
		}

		if ( is_iosSlider_element.length && is_iosSlider_element.iosSlider ) {
			is_iosSlider = true;
		}

		if ( is_flexslider_element.length && is_flexslider_element.flexslider ) {
			is_flexslider = true;
		}

		if ( is_easyzoom_element.length && is_easyzoom_element.filter( '.images .easyzoom.first' ).data( 'easyZoom' ) ) {
			is_easyzoom_element = is_easyzoom_element.filter( '.images .easyzoom.first' ).data( 'easyZoom' );
			is_easyzoom = true;
		}

		if ( ! is_easyzoom ) {
			is_easyzoom_flatsome_element = product_element.find( '.images .has-image-zoom .slide' );
			if ( is_easyzoom_flatsome_element.length && is_easyzoom_flatsome_element.filter( '.images .has-image-zoom .slide.first' ).data( 'easyZoom' ) ) {
				is_easyzoom_flatsome_element = is_easyzoom_flatsome_element.filter( '.images .has-image-zoom .slide.first' ).data( 'easyZoom' );
				is_easyzoom_flatsome = true;
			}
		}

		jWindow.on( 'load', function() {
			setTimeout( function() {
				if ( is_easyzoom_element.length && is_easyzoom_element.data( 'easyZoom' ) ) {
					is_easyzoom_element = is_easyzoom_element.data( 'easyZoom' );
					is_easyzoom = true;
				}
				if ( is_easyzoom_flatsome_element.length && is_easyzoom_flatsome_element.data( 'easyZoom' ) ) {
					is_easyzoom_flatsome_element = is_easyzoom_flatsome_element.data( 'easyZoom' );
					is_easyzoom_flatsome = true;
				}
			}, 150 );
		} );

		if ( is_wc27_gallery_element.length && is_wc27_gallery_element.data( 'flexslider' ) ) {
			is_wc27_gallery = true;

			if ( typeof $.fn.zoom === 'function' && wc_single_product_params && wc_single_product_params.zoom_enabled ) {
				zoom_target_temp = img.closest( '.woocommerce-product-gallery__image' );

				if ( zoom_target_temp.length > 0 && img.width() > $( '.woocommerce-product-gallery' ).width() ) {
					wc27_zoom_target = zoom_target_temp;
					img.data.wc27_zoom_target = wc27_zoom_target;
				}
			}
		}

		if ( ! is_wc27_gallery && is_zoom_enabled ) {
			zoom_images = product_element.find( '.woocommerce-product-gallery__image' );
		}

		gallery = {
			is_yith_wcmg: {
				type: 'yith',
				enabled: is_yith_wcmg,
				element: yith_wcmg,
				yith_wcmg_zoom: yith_wcmg_zoom,
				_yith_wcmg_default_zoom: _yith_wcmg_default_zoom,
				yith_wcmg_default_image: yith_wcmg_default_image
			},
			is_iosSlider: {
				type: 'iosslider',
				enabled: is_iosSlider,
				element: is_iosSlider_element
			},
			is_flexslider: {
				type: 'flexslider',
				enabled: is_flexslider,
				element: is_flexslider_element
			},
			is_elevateZoom: {
				type: 'elevatezoom',
				enabled: is_elevateZoom,
				element: is_elevateZoom_obj
			},
			is_easyzoom: {
				type: 'easyzoom',
				enabled: is_easyzoom,
				element: is_easyzoom_element
			},
			is_easyzoom_flatsome: {
				type: 'easyzoom-flatsome',
				enabled: is_easyzoom_flatsome,
				element: is_easyzoom_flatsome_element
			},
			is_wc27_gallery: {
				type: 'woocommerce',
				enabled: is_wc27_gallery,
				element: is_wc27_gallery_element
			},
			is_zoom_enabled: {
				type: 'zoom',
				enabled: ! is_wc27_gallery && is_zoom_enabled,
				element: zoom_images
			}
		};

		return gallery;
	}

	function tm_product_image_self( epoObject ) {
		var this_epo_container = epoObject.is_associated ? epoObject.this_epo_container : epoObject.this_epo_container.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector );
		var variationsForm = epoObject.variations_form;
		var main_product = epoObject.main_product;
		var $form = epoObject.form;
		var img;
		var gallery_type;
		var product_element = get_product_element( epoObject );
		var last_active_field = [];
		var t;
		var el;
		var el_current;
		var imp;
		var selector;
		var data;
		var eventNamePrefix = epoObject.is_associated ? 'tc_' : '';

		img = get_main_product_image( epoObject, product_element );
		gallery_type = get_gallery_type( epoObject, img, product_element );

		if ( $( img ).length > 0 ) {
			$form.on( eventNamePrefix + 'reset_image.tcpi', function() {
				// restore product image atts from backup
				$.tc_product_image = $.tc_replace_object_values( $.tc_product_image, $.tc_product_image_store );

				last_active_field = [];

				$( this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' ) )
					.add( variationsForm.find( '.tm-epo-variation-section' ).first().find( '.tm-product-image:checked,select.tm-product-image' ) )
					.each( function() {
						t = $( this );
						if ( field_is_active( t ) && t.val() !== '' ) {
							last_active_field.push( t );
						}
					} );
				if ( last_active_field.length ) {
					last_active_field[ last_active_field.length - 1 ].trigger( 'tm_trigger_product_image' );
				} else {
					$.tc_product_image_store = $.tc_populate_store( img, product_element );
				}
			} );

			$form.on( eventNamePrefix + 'found_variation.tcpi', function() {
				reset_saved_image( img, product_element );

				last_active_field = [];
				$( this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' ) )
					.add( variationsForm.find( '.tm-epo-variation-section' ).first().find( '.tm-product-image:checked,select.tm-product-image' ) )
					.each( function() {
						t = $( this );
						if ( field_is_active( t ) && t.val() !== '' ) {
							last_active_field.push( t );
						}
					} );
				if ( last_active_field.length ) {
					repopulate_backup_image_atts( img, product_element );
					last_active_field[ last_active_field.length - 1 ].trigger( 'tm_trigger_product_image' );
				}
			} );

			$.tc_product_image_store = $.tc_maybe_copy_object_values_from_img( $.tc_product_image_store, img, product_element );

			main_product.off( 'tm_change_product_image' ).on( 'tm_change_product_image', function( evt, event_data ) {
				evt.stopImmediatePropagation();

				el = event_data.element;
				el_current = event_data.element_current;
				if ( el && el_current ) {
					imp = el.data( 'imagep' );
					selector = imp !== '' ? 'imagep' : 'image';
					data = event_data.element_current.data( 'image-variations' );

					if ( data ) {
						data = data[ selector ];
					}

					if ( data === undefined ) {
						return;
					}

					last_active_field = [];
					$( this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' ) )
						.add( variationsForm.find( '.tm-epo-variation-section' ).first().find( '.tm-product-image:checked,select.tm-product-image' ) )
						.each( function() {
							t = $( this );
							if ( t.is( 'option' ) ) {
								t = t.closest( 'select' );
							}
							if ( field_is_active( t ) && t.val() !== '' ) {
								last_active_field.push( t );
							}
						} );

					if ( last_active_field.length ) {
						if ( ! last_active_field[ last_active_field.length - 1 ].is( el ) ) {
							return;
						}
					}

					image_update( data, img, product_element );

					gallery_compatibility_actions( gallery_type, img, data.image_link, false, event_data, $form, product_element );
				}
			} );

			main_product.off( 'tm_restore_product_image' ).on( 'tm_restore_product_image', function( evt, event_data ) {
				evt.stopImmediatePropagation();

				el = event_data.element;
				last_active_field = [];

				if ( el ) {
					$( this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' ) )
						.add( variationsForm.find( '.tm-epo-variation-section' ).first().find( '.tm-product-image:checked,select.tm-product-image' ) )
						.each( function() {
							t = $( this );
							if ( field_is_active( t ) && t.val() !== '' ) {
								last_active_field.push( t );
							}
						} );
					if ( last_active_field.length ) {
						if ( ! last_active_field[ last_active_field.length - 1 ].is( el ) ) {
							last_active_field[ last_active_field.length - 1 ].trigger( 'tm_trigger_product_image' );
							return;
						}
					}
				}

				image_update( false, img, product_element );
				gallery_compatibility_actions( gallery_type, false, img.attr( 'src' ), false, event_data, $form, product_element );
			} );

			last_active_field = [];
			$( this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' ) )
				.add( variationsForm.find( '.tm-epo-variation-section' ).first().find( '.tm-product-image:checked,select.tm-product-image' ) )
				.each( function() {
					t = $( this );
					if ( field_is_active( t ) && t.val() !== '' ) {
						last_active_field.push( t );
					}
				} );
			if ( last_active_field.length ) {
				last_active_field[ last_active_field.length - 1 ].trigger( 'tm_trigger_product_image' );
			}
		}

		jWindow.trigger( 'tm_product_image_loaded' );
	}

	function tm_product_image_inline( epoObject ) {
		var this_epo_container = epoObject.is_associated ? epoObject.this_epo_container : epoObject.this_epo_container.not( tcAPI.epoSelector + ' ' + tcAPI.associatedEpoSelector );
		var main_product = epoObject.main_product;
		var product_id = epoObject.product_id;
		var $form = epoObject.form;
		var img;
		var product_element = get_product_element( epoObject );
		var gallery_type;
		var a;
		var img_src_original;
		var img_width;
		var img_height;
		var last_active_field = [];
		var t;

		img = get_main_product_image( epoObject, product_element );

		gallery_type = get_gallery_type( epoObject, img, product_element );

		if ( $( img ).length > 0 ) {
			img.data( 'tm-current-image', false );
			a = img.closest( 'a' );
			img_src_original = img.attr( 'src' );
			img_width = img.width();
			img_height = img.height();

			main_product.off( 'tm_change_product_image' ).on( 'tm_change_product_image', function( evt, e ) {
				var variation_element_section;
				var is_variation_element;
				var $this_epo_container;
				var tm_last_visible_image_element;
				var last_activate_field = [];
				var tm_current_image_element_id;
				var can_show_image;
				var $main_product;
				var $current_product_element;
				var preload_width;
				var preload_height;
				var current_cloned_image;
				var preloader;
				var clone_image;
				var preload_img;
				var preload_img_onerror;

				variation_element_section = e.element.closest( '.cpf-section' );
				is_variation_element = variation_element_section.is( '.tm-epo-variation-section' );
				$this_epo_container = e.epo_holder;
				if ( is_variation_element ) {
					$this_epo_container = variation_element_section;
				}
				tm_last_visible_image_element = $this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' );
				last_activate_field = [];
				tm_current_image_element_id = e.element.attr( 'id' );
				can_show_image = true;
				$main_product = e.main_product;
				$current_product_element = $main_product.closest( '#product-' + product_id );
				preload_width = img_width;
				preload_height = img_height;
				preloader = $( "<div class='blockUI blockOverlay tm-preloader-img'></div>" );

				if ( $current_product_element.length <= 0 ) {
					$current_product_element = $main_product.closest( '.post-' + product_id );
				}

				current_cloned_image = $current_product_element.find( '#' + tm_current_image_element_id + '_tmimage' );
				if ( current_cloned_image.length === 0 ) {
					current_cloned_image = img;
				}

				preload_img_onerror = function() {
					preloader.remove();
					$form.tc_image_update( false );
					img.data( 'tm-current-image', false );
					$current_product_element.find( '.tm-clone-product-image' ).hide();
					img.show();
				};

				if ( e.src === current_cloned_image.attr( 'src' ) && current_cloned_image.is( ':visible' ) ) {
					return;
				}

				if ( e.src === false ) {
					preload_img_onerror();
					return;
				}

				preloader.css( {
					width: preload_width,
					height: preload_height
				} );

				// Get last active field
				tm_last_visible_image_element.each( function() {
					t = $( this );
					if (
						field_is_active( t ) &&
						has_active_changes_product_image( t ) &&
						tm_check_field_match( {
							element: t.closest( '.cpf_hide_element' ),
							operator: 'isnotempty',
							value: ''
						} )
					) {
						last_activate_field.push( t );
					}
				} );
				// Get last active image
				if ( last_activate_field.length ) {
					tm_last_visible_image_element = last_activate_field[ last_activate_field.length - 1 ];
				}

				if ( tm_last_visible_image_element.attr( 'id' ) !== e.element.attr( 'id' ) ) {
					can_show_image = false;
				}

				clone_image = img.tcClone();
				preload_img = new Image();
				clone_image.removeAttr( 'data-o_src' ).removeAttr( 'data-o_title' ).removeAttr( 'data-o_alt' ).removeAttr( 'data-o_srcset' ).removeAttr( 'data-o_sizes' ).removeAttr( 'srcset' ).removeAttr( 'sizes' );

				if ( can_show_image ) {
					img.before( preloader );
				}

				gallery_type.is_yith_wcmg.yith_wcmg_default_zoom = gallery_type.is_yith_wcmg.element.find( '.yith_magnifier_zoom' ).first().attr( 'href' );
				gallery_type.is_yith_wcmg.yith_wcmg_default_image = gallery_type.is_yith_wcmg.element.find( '.yith_magnifier_zoom img' ).first().attr( 'src' );

				preload_img.onerror = function() {
					preload_img_onerror();
				};

				preload_img.onload = function() {
					if ( 'naturalHeight' in this ) {
						if ( this.naturalHeight + this.naturalWidth === 0 ) {
							this.onerror();
							return;
						}
					} else if ( this.width + this.height === 0 ) {
						this.onerror();
						return;
					}
					$current_product_element.find( '#' + tm_current_image_element_id + '_tmimage' ).remove();
					$current_product_element.find( '.tm-clone-product-image' ).hide();
					clone_image.prop( 'src', preload_img.src ).hide();

					img.hide().after( clone_image );

					clone_image.css( 'opacity', 0 ).show();

					gallery_compatibility_actions( gallery_type, clone_image, preload_img.src );

					preloader.animate(
						{
							opacity: 0
						},
						750,
						'easeOutExpo',
						function() {
							preloader.remove();
						}
					);
					clone_image.animate(
						{
							opacity: 1
						},
						window.tc_epo_image_animation_delay || 1500,
						'easeOutExpo',
						function() {}
					);

					jWindow.trigger( 'tm_change_product_image_loaded', {
						src: e.src,
						element: e.element,
						main_product: e.main_product,
						epo_holder: e.epo_holder
					} );
				};

				clone_image
					.attr( 'id', tm_current_image_element_id + '_tmimage' )
					.addClass( 'tm-clone-product-image' )
					.hide();

				if ( clone_image.attr( 'src-orig' ) ) {
					clone_image.attr( 'src-orig', e.src );
				}

				if ( can_show_image ) {
					preload_img.src = e.src;

					$form.tc_image_update( e.element, clone_image );

					img.data( 'tm-current-image', tm_current_image_element_id );

					jWindow.trigger( 'tm_change_product_image_show', {
						src: e.src,
						element: e.element,
						main_product: e.main_product,
						epo_holder: e.epo_holder
					} );
				} else {
					clone_image.prop( 'src', e.src ).hide();
					img.after( clone_image );
				}

				jWindow.trigger( 'tm_change_product_image_end', {
					src: e.src,
					element: e.element,
					main_product: e.main_product,
					epo_holder: e.epo_holder
				} );
			} );

			main_product.off( 'tm_restore_product_image' ).on( 'tm_restore_product_image', function( evt, e ) {
				var tm_current_image_element_id;
				var $main_product;
				var $current_product_element;
				var variation_element_section;
				var is_variation_element;
				var current_element;
				var current_image_replacement;
				var found;
				var is_it_visible;
				var len;
				var el_to_check;
				var imgSrc;
				var $this_epo_container;
				var i;

				jWindow.trigger( 'tm_restore_product_image_pre', {
					element: e.element,
					main_product: e.main_product,
					epo_holder: e.epo_holder
				} );
				tm_current_image_element_id = e.element.attr( 'id' );
				$main_product = e.main_product;
				$current_product_element = $main_product.closest( '#product-' + product_id );
				variation_element_section = e.element.closest( '.cpf-section' );
				is_variation_element = variation_element_section.is( '.tm-epo-variation-section' );
				found = false;
				imgSrc = img_src_original;
				$this_epo_container = e.epo_holder;
				if ( is_variation_element ) {
					$this_epo_container = variation_element_section;
				}

				if ( $current_product_element.length <= 0 ) {
					$current_product_element = $main_product.closest( '.post-' + product_id );
				}

				is_it_visible = $current_product_element.find( '#' + tm_current_image_element_id + '_tmimage' ).is( ':visible' );

				$current_product_element.find( '#' + tm_current_image_element_id + '_tmimage' ).remove();

				if ( $current_product_element.find( '.tm-clone-product-image' ).length === 0 ) {
					img.show();
					img.data( 'tm-current-image', false );
					$form.tc_image_update( false );
				} else {
					if ( ! is_it_visible ) {
						jWindow.trigger( 'tm_restore_product_image_loaded_exit', {
							element: e.element,
							main_product: e.main_product,
							epo_holder: e.epo_holder
						} );
						return;
					}

					len = $current_product_element.find( '.tm-clone-product-image' ).length;
					tm_current_image_element_id = img.data( 'tm-current-image' );

					for ( i = len - 1; i >= 0; i -= 1 ) {
						current_image_replacement = $current_product_element.find( '.tm-clone-product-image' ).eq( i );
						current_element = current_image_replacement.attr( 'id' ).replace( '_tmimage', '' );
						el_to_check = $this_epo_container.find( "[id='" + current_element + "']" );

						if ( el_to_check.is( ':checked' ) && el_to_check.closest( '.cpf_hide_element' ).is( ':visible' ) ) {
							$current_product_element.find( '.tm-clone-product-image' ).eq( i ).show();
							a.attr( 'href', $current_product_element.find( '.tm-clone-product-image' ).eq( i ).prop( 'src' ) );
							img.data( 'tm-current-image', current_element );
							found = true;
							break;
						} else {
							$current_product_element.find( '.tm-clone-product-image' ).eq( i ).hide();
						}
					}
					if ( ! found ) {
						img.show();
						img.data( 'tm-current-image', false );
						$form.tc_image_update( false );
					} else {
						$current_product_element.find( '#' + tm_current_image_element_id + '_tmimage' ).remove();
					}
				}

				if ( found ) {
					imgSrc = current_image_replacement.attr( 'src' );
				}

				gallery_compatibility_actions( gallery_type, false, imgSrc, $current_product_element.find( '.tm-clone-product-image' ).filter( ':visible' ).length );

				jWindow.trigger( 'tm_restore_product_image_loaded', {
					element: e.element,
					main_product: e.main_product,
					epo_holder: e.epo_holder
				} );
			} );

			main_product.off( 'tm_attempt_product_image' ).on( 'tm_attempt_product_image', function( evt, e ) {
				var $main_product;
				var $current_product_element;
				var variation_element_section;
				var is_variation_element;
				var $this_epo_container;
				var tm_last_visible_image_element;
				var last_activate_field;
				var tm_last_visible_image_element_id;
				var current_image_replacement;
				var current_element;
				var found;
				var tm_current_image_element_id;
				var len;
				var imgSrc;
				var el_to_check;
				var tmcie_id;
				var i;

				$main_product = e.main_product;
				$current_product_element = $main_product.closest( '#product-' + product_id );
				if ( e.element ) {
					variation_element_section = e.element.closest( '.cpf-section' );
				} else {
					variation_element_section = $( $main_product.find( '.tm-epo-variation-section' ).first(), e.epo_holder );
				}
				is_variation_element = variation_element_section.is( '.tm-epo-variation-section' );
				$this_epo_container = e.epo_holder;
				if ( is_variation_element ) {
					$this_epo_container = variation_element_section;
				}
				tm_last_visible_image_element = $this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' );
				last_activate_field = [];
				tm_last_visible_image_element_id = '';
				found = false;
				tm_current_image_element_id = img.data( 'tm-current-image' );
				imgSrc = img_src_original;

				if ( $current_product_element.length <= 0 ) {
					$current_product_element = $main_product.closest( '.post-' + product_id );
				}

				$this_epo_container = $main_product.find( '.tm-epo-variation-section' ).first().add( e.epo_holder );
				tm_last_visible_image_element = $this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' );

				tm_last_visible_image_element.each( function() {
					t = $( this );
					if (
						field_is_active( t ) &&
						has_active_changes_product_image( t ) &&
						tm_check_field_match( {
							element: t.closest( '.cpf_hide_element' ),
							operator: 'isnotempty',
							value: ''
						} )
					) {
						last_activate_field.push( t );
					}
				} );

				if ( last_activate_field.length ) {
					tm_last_visible_image_element = last_activate_field[ last_activate_field.length - 1 ];
					tm_last_visible_image_element_id = tm_last_visible_image_element.attr( 'id' );
				}

				if ( last_activate_field.length && tm_last_visible_image_element.length && ( ! tm_current_image_element_id || tm_last_visible_image_element_id !== tm_current_image_element_id ) ) {
					tm_last_visible_image_element.last().trigger( 'tm_trigger_product_image' );
					return;
				}

				tmcie_id = $this_epo_container.find( "[id='" + tm_current_image_element_id + "']" ).closest( '.cpf_hide_element' );
				if ( ! tm_current_image_element_id || ( tmcie_id.data( 'isactive' ) !== false && tmcie_id.closest( '.cpf-section' ).data( 'isactive' ) !== false ) ) {
					return;
				}

				$current_product_element.find( '#' + tm_current_image_element_id + '_tmimage' ).remove();
				len = $current_product_element.find( '.tm-clone-product-image' ).length;

				if ( len === 0 ) {
					img.show();
					img.data( 'tm-current-image', false );
					$form.tc_image_update( false );
				} else {
					for ( i = len - 1; i >= 0; i -= 1 ) {
						current_image_replacement = $current_product_element.find( '.tm-clone-product-image' ).eq( i );
						current_element = current_image_replacement.attr( 'id' ).replace( '_tmimage', '' );
						el_to_check = $this_epo_container.find( "[id='" + current_element + "']" );

						if ( el_to_check.is( ':checked' ) && el_to_check.closest( '.cpf_hide_element' ).is( ':visible' ) ) {
							$current_product_element.find( '.tm-clone-product-image' ).eq( i ).show();
							a.attr( 'href', $current_product_element.find( '.tm-clone-product-image' ).eq( i ).prop( 'src' ) );
							img.data( 'tm-current-image', current_element );
							found = true;
							break;
						} else {
							$current_product_element.find( '.tm-clone-product-image' ).eq( i ).hide();
						}
					}

					if ( ! found ) {
						img.show();
						img.data( 'tm-current-image', false );
						$form.tc_image_update( false );
					}
				}

				if ( found ) {
					imgSrc = current_image_replacement.attr( 'src' );
				}

				gallery_compatibility_actions( gallery_type, false, imgSrc, $current_product_element.find( '.tm-clone-product-image' ).filter( ':visible' ).length );
			} );

			$( this_epo_container.find( '.tm-product-image:checked,select.tm-product-image' ) )
				.add( main_product.find( '.tm-epo-variation-section' ).first().find( '.tm-product-image:checked,select.tm-product-image' ) )
				.each( function() {
					t = $( this );
					if ( field_is_active( t ) && t.val() !== '' ) {
						last_active_field.push( t );
					}
				} );
			if ( last_active_field.length ) {
				last_active_field[ last_active_field.length - 1 ].trigger( 'tm_trigger_product_image' );
			}
		}

		jWindow.trigger( 'tm_product_image_loaded' );
	}

	function tm_product_image( epoObject ) {
		setTimeout( function() {
			if ( TMEPOJS.tm_epo_global_product_image_mode === 'inline' ) {
				tm_product_image_inline( epoObject );
			} else {
				tm_product_image_self( epoObject );
			}
		}, window.tc_epo_product_image_setup_delay || 0 );
	}

	function tc_compatibility( epoObject ) {
		jWindow.trigger( 'tm-epo-compatibility', {
			epo: epoObject
		} );
	}

	// Range picker setup
	function tm_set_range_pickers( obj ) {
		if ( ! noUiSlider ) {
			return;
		}
		obj.find( '.tm-range-picker' )
			.toArray()
			.forEach( function( picker ) {
				var el = $( picker );
				var $decimals = el.attr( 'data-step' ).split( '.' );
				var $tmfid = obj.find( '#' + $.epoAPI.dom.id( el.attr( 'data-field-id' ) ) );
				var $min = parseFloat( el.attr( 'data-min' ) );
				var $max = parseFloat( el.attr( 'data-max' ) );
				var $start = parseFloat( el.attr( 'data-start' ) );
				var $step = parseFloat( el.attr( 'data-step' ) );
				var $show_picker_value = el.attr( 'data-show-picker-value' );
				var $show_label = el.closest( 'li' ).find( '.tm-show-picker-value' );
				var $noofpips = parseFloat( el.attr( 'data-noofpips' ) );
				var $pips = null;
				var $tmh;

				if ( el.data( 'tc-picker-init' ) ) {
					return;
				}
				el.data( 'tc-picker-init', 1 );

				if ( $decimals.length === 1 ) {
					$decimals = 0;
				} else {
					$decimals = $decimals[ 1 ].length;
				}
				if ( ! Number.isFinite( $min ) ) {
					$min = 0;
				}
				if ( ! Number.isFinite( $max ) ) {
					$max = 0;
				}
				if ( $max <= $min ) {
					$max = parseFloat( $max ) + 1;
				}
				$start = $.epoAPI.math.unformat( $start, tcAPI.localDecimalSeparator );
				if ( ! Number.isFinite( $start ) ) {
					$start = 0;
				}
				$start = formatPrice( $start, { precision: $decimals } );
				if ( ! Number.isFinite( $step ) ) {
					$step = 0;
				}
				if ( ! Number.isFinite( $noofpips ) ) {
					$noofpips = 10;
				}
				if ( $noofpips < 2 ) {
					$noofpips = 2;
				}

				if ( el.attr( 'data-pips' ) === 'yes' ) {
					$pips = {
						mode: 'count',
						values: $noofpips,
						filter: function( value, type ) {
							value = parseFloat( $.epoAPI.math.toFixed( value, $decimals ) );

							if ( $step <= 0 ) {
								return 0;
							}

							if ( type === 1 ) {
								if ( ! Number.isInteger( value ) ) {
									return 2;
								}
							}

							return type;
						},
						format: {
							from: function( value ) {
								return $.epoAPI.math.unformat( value, tcAPI.localInputDecimalSeparator );
							},
							to: function( value ) {
								return formatPrice( value, { precision: $decimals } );
							}
						},
						density: 2
					};
				}

				noUiSlider.create( el.get( 0 ), {
					direction: TMEPOJS.text_direction,
					start: $start,
					step: $step,
					connect: 'lower',
					// Configure tapping, or make the selected range dragable.
					behaviour: 'tap',
					// Full number format support.
					format: {
						from: function( value ) {
							return $.epoAPI.math.unformat( value, tcAPI.localInputDecimalSeparator );
						},
						to: function( value ) {
							return formatPrice( value, { precision: $decimals } );
						}
					},
					// Support for non-linear ranges by adding intervals.
					range: {
						min: [ $min ],
						max: [ $max ]
					},
					pips: $pips,
					tooltips: {
						from: function( value ) {
							return $.epoAPI.math.unformat( value, tcAPI.localInputDecimalSeparator );
						},
						to: function( value ) {
							return formatPrice( value, { precision: $decimals } );
						}
					}
				} );

				$tmh = el.find( '.noUi-handle-lower' );
				el.get( 0 ).noUiSlider.on( 'slide', function() {
					$tmh.trigger( 'tmmovetooltip' );
					$tmfid.trigger( 'change.cpf' );
				} );
				el.get( 0 ).noUiSlider.on( 'update', function( values, handle ) {
					values[ handle ] = $.epoAPI.math.unformat( values[ handle ], tcAPI.localDecimalSeparator );
					handle = 0; //fixes rtl issue.
					if ( $show_picker_value !== 'left' && $show_picker_value !== 'right' ) {
						$tmh.attr(
							'title',
							formatPrice( values[ handle ], { precision: $decimals } )
						);
					}
					$tmfid.val( values[ handle ] ).trigger( 'change' );
					if ( $show_picker_value !== '' ) {
						$show_label.html(
							formatPrice( values[ handle ], { precision: $decimals } )
						);
					}
				} );

				if ( $show_picker_value !== '' ) {
					$show_label.html( $start );
				}

				if ( $show_picker_value !== 'left' && $show_picker_value !== 'right' ) {
					$tmh.attr( 'title', $start );
					el.addClass( 'noUi-show-tooltip' );
				}
			} );
	}

	function validate_date_with_options( date, inputElement ) {
		var input = $( inputElement );
		var inst = $.tm_datepicker._getInst( input[ 0 ] );
		var enabled_only_dates = input.data( 'tc-enabled_only_dates' );
		var disabled_weekdays = input.data( 'tc-disabled_weekdays' );
		var disabled_months = input.data( 'tc-disabled_months' );
		var disabled_dates = input.data( 'tc-disabled_dates' );
		var format = input.data( 'tc-format' );
		var day = date.getDay();
		var month = date.getDay() + 1;
		var string;

		if ( ! $.tm_datepicker._isInRange( inst, date ) ) {
			return false;
		}
		if ( enabled_only_dates !== '' ) {
			string = $.tm_datepicker.formatDate( format, date );
			return enabled_only_dates.indexOf( string ) !== -1;
		}
		if ( disabled_weekdays.indexOf( day.toString() ) !== -1 ) {
			return false;
		}
		if ( disabled_months.indexOf( month.toString() ) !== -1 ) {
			return false;
		}
		if ( disabled_dates !== '' ) {
			string = $.tm_datepicker.formatDate( format, date );
			return disabled_dates.indexOf( string ) === -1;
		}
		return true;
	}

	function correctDate( days ) {
		var sign, testDate, count, added, noOfDaysToAdd;
		if ( days.toString().isNumeric() ) {
			sign = days === 0 ? days : ( days > 0 ? 1 : -1 );
			if ( sign !== 0 ) {
				testDate = new Date();
				count = 1;
				added = false;
				noOfDaysToAdd = Math.abs( days );
				while ( count <= noOfDaysToAdd ) {
					if ( added === false ) {
						added = 0;
					}
					testDate.setDate( testDate.getDate() + ( 1 * sign ) );
					added++;
					if ( testDate.getDay() !== 0 && testDate.getDay() !== 6 ) {
						count++;
					}
				}
				if ( added !== false ) {
					days = added * sign;
				}
			}
		}
		return days;
	}
	// Date and time picker setup
	function tm_set_datepicker( obj ) {
		var inputIds;
		var elem;
		var timepickerSelector = '.tm-epo-timepicker';

		if ( ! $.tm_datepicker ) {
			return;
		}

		inputIds = $( 'input' )
			.map( function() {
				return this.id;
			} )
			.get()
			.join( ' ' );

		elem = document.createElement( 'input' );
		elem.setAttribute( 'type', 'date' );

		if ( elem.type === 'text' ) {
			timepickerSelector = '.tm-epo-system-timepicker';
		}

		obj.find( timepickerSelector )
			.toArray()
			.forEach( function( el ) {
				var field = $( el );
				var _mintime = null;
				var _maxtime = null;
				var format = field.attr( 'data-time-format' ).trim();
				var date_theme = field.attr( 'data-time-theme' ).trim();
				var date_theme_size = field.attr( 'data-time-theme-size' ).trim();
				var date_theme_position = field.attr( 'data-time-theme-position' ).trim();
				var data_tranlation_hour = field.attr( 'data-tranlation-hour' ).trim();
				var data_tranlation_minute = field.attr( 'data-tranlation-minute' ).trim();
				var data_tranlation_second = field.attr( 'data-tranlation-second' ).trim();

				field.attr( 'type', 'text' );

				if ( field.attr( 'data-min-time' ).trim() !== '' ) {
					_mintime = field.attr( 'data-min-time' ).trim();
				}
				if ( field.attr( 'data-max-time' ).trim() !== '' ) {
					_maxtime = field.attr( 'data-max-time' ).trim();
				}

				if ( field.attr( 'data-custom-time-format' ).trim() !== '' ) {
					format = field.attr( 'data-custom-time-format' ).trim();
				}
				if ( ! data_tranlation_hour ) {
					data_tranlation_hour = TMEPOJS.hourText;
				}
				if ( ! data_tranlation_minute ) {
					data_tranlation_minute = TMEPOJS.minuteText;
				}
				if ( ! data_tranlation_second ) {
					data_tranlation_second = TMEPOJS.secondText;
				}

				field.tm_timepicker( {
					isRTL: TMEPOJS.isRTL,
					hourText: data_tranlation_hour,
					minuteText: data_tranlation_minute,
					secondText: data_tranlation_second,
					timeFormat: format,
					minTime: _mintime,
					maxTime: _maxtime,
					closeText: TMEPOJS.closeText,
					showOn: 'both',
					buttonText: '',

					beforeShow: function( input, inst ) {
						$( inst.dpDiv )
							.removeClass( inputIds )
							.removeClass( 'tm-ui-skin-epo tm-ui-skin-epo-black tm-datepicker-medium tm-datepicker-small tm-datepicker-large tm-datepicker-normal tm-datepicker-top tm-datepicker-bottom' )
							.addClass( this.id + ' tm-bsbb-all tm-ui-skin-' + date_theme + ' tm-timepicker tm-datepicker tm-datepicker-' + date_theme_position + ' tm-datepicker-' + date_theme_size )
							.appendTo( 'body' );

						jDocument.off( 'click', '.tm-ui-dp-overlay' ).on( 'click', '.tm-ui-dp-overlay', function() {
							field.tm_timepicker( 'hide' );
						} );
						jBody.addClass( 'tm-static' );
						field.prop( 'readonly', true );

						jWindow.trigger( {
							type: 'tm-timepicker-beforeShow',
							input: input,
							inst: inst
						} );
					},
					onClose: function() {
						jBody.removeClass( 'tm-static' );
						field.prop( 'readonly', false );
						field.trigger( 'change' );
					}
				} );
				$( '#ui-tm-datepicker-div' ).hide();
			} );

		obj.find( '.tm-epo-datepicker' )
			.toArray()
			.forEach( function( el ) {
				var field = $( el );
				var startDate = parseInt( field.attr( 'data-start-year' ).trim(), 10 );
				var endDate = parseInt( field.attr( 'data-end-year' ).trim(), 10 );
				var minDate = field.attr( 'data-min-date' ).trim();
				var maxDate = field.attr( 'data-max-date' ).trim();
				var disabled_dates = field.attr( 'data-disabled-dates' ).trim();
				var enabled_only_dates = field.attr( 'data-enabled-only-dates' ).trim();
				var exlude_disabled = field.attr( 'data-exlude-disabled' ).trim();
				var disabled_weekdays = field.attr( 'data-disabled-weekdays' ).trim().split( ',' );
				var disabled_months = field.attr( 'data-disabled-months' ).trim().split( ',' );
				var format = field.attr( 'data-date-format' ).trim();
				var show = field.attr( 'data-date-showon' ).trim();
				var default_date = field.attr( 'data-date-defaultdate' ).trim();
				var date_theme = field.attr( 'data-date-theme' ).trim();
				var date_theme_size = field.attr( 'data-date-theme-size' ).trim();
				var date_theme_position = field.attr( 'data-date-theme-position' ).trim();
				var $split;
				var $index;
				var $split2;
				var $index2;

				if ( disabled_dates !== '' ) {
					$split = disabled_dates.split( ',' );
					$index = disabled_dates.indexOf( ',' );

					if ( $index !== -1 && $split.length > 0 ) {
						disabled_dates = $split;
					}
				}
				if ( enabled_only_dates !== '' ) {
					$split2 = enabled_only_dates.split( ',' );
					$index2 = enabled_only_dates.indexOf( ',' );

					if ( $index2 !== -1 && $split2.length > 0 ) {
						enabled_only_dates = $split2;
					}
				}

				if ( minDate === '' ) {
					if ( startDate === '' ) {
						minDate = null;
					} else {
						minDate = new Date( startDate, 1 - 1, 1 );
					}
				} else if ( exlude_disabled ) {
					minDate = correctDate( minDate );
				}
				if ( maxDate === '' ) {
					if ( endDate === '' ) {
						maxDate = null;
					} else {
						maxDate = new Date( endDate, 12 - 1, 31 );
					}
				} else if ( exlude_disabled ) {
					maxDate = correctDate( maxDate );
				}

				field.data( 'tc-enabled_only_dates', enabled_only_dates );
				field.data( 'tc-disabled_weekdays', disabled_weekdays );
				field.data( 'tc-disabled_months', disabled_months );
				field.data( 'tc-disabled_dates', disabled_dates );
				field.data( 'tc-format', format );

				field.tm_datepicker( {
					monthNames: TMEPOJS.monthNames,
					monthNamesShort: TMEPOJS.monthNamesShort,
					dayNames: TMEPOJS.dayNames,
					dayNamesShort: TMEPOJS.dayNamesShort,
					dayNamesMin: TMEPOJS.dayNamesMin,
					isRTL: TMEPOJS.isRTL,
					showOtherMonths: true,
					selectOtherMonths: true,
					showOn: show,
					defaultDate: default_date,
					buttonText: '',
					showButtonPanel: true,
					firstDay: TMEPOJS.first_day,
					closeText: TMEPOJS.closeText,
					currentText: TMEPOJS.currentText,
					dateFormat: format,
					minDate: minDate,
					maxDate: maxDate,
					onSelect: function() {
						var input = $( this );
						var id = '#' + $.epoAPI.dom.id( input.attr( 'id' ) );
						var date = input.tm_datepicker( 'getDate' );
						var day = '';
						var month = '';
						var year = '';
						var day_field = obj.find( id + '_day' );
						var month_field = obj.find( id + '_month' );
						var year_field = obj.find( id + '_year' );
						var string;
						var ld;

						if ( date ) {
							day = date.getDate();
							month = date.getMonth() + 1;
							year = date.getFullYear();
							string = $.tm_datepicker.formatDate( format, date );
							if (
								disabled_months.indexOf( month.toString() ) !== -1 ||
								disabled_weekdays.indexOf( date.getDay().toString() ) !== -1 ||
								disabled_dates.indexOf( string ) !== -1 ||
								( enabled_only_dates !== '' && enabled_only_dates.indexOf( string ) === -1 )
							) {
								ld = input.data( 'tm-last-date' );
								if ( input.data( 'tm-last-date' ) ) {
									ld = input.data( 'tm-last-date' );
								} else {
									ld = '';
								}
								input.val( ld );
								input.tm_datepicker( 'setDate', ld );
								if ( ld ) {
									date = input.tm_datepicker( 'getDate' );
									day = date.getDate();
									month = date.getMonth() + 1;
									year = date.getFullYear();
								} else {
									day = '';
									month = '';
									year = '';
								}
							}
						}

						day_field.val( day );
						month_field.val( month );
						year_field.val( year );

						input.data( 'tm-last-date', input.val() );
					},
					beforeShow: function( input, inst ) {
						$( inst.dpDiv )
							.removeClass( inputIds )
							.removeClass( 'tm-datepicker-normal tm-datepicker-top tm-datepicker-bottom' )
							.addClass( this.id + ' tm-bsbb-all tm-ui-skin-' + date_theme + ' tm-datepicker tm-datepicker-' + date_theme_position + ' tm-datepicker-' + date_theme_size )
							.appendTo( 'body' );

						jDocument.off( 'click', '.tm-ui-dp-overlay' ).on( 'click', '.tm-ui-dp-overlay', function() {
							field.tm_datepicker( 'hide' );
						} );
						jDocument.off( 'click', '.ui-tm-datepicker-current' ).on( 'click', '.ui-tm-datepicker-current', function() {
							var tempDate = new Date(),
								today = $.tm_datepicker._daylightSavingAdjust( new Date( tempDate.getFullYear(), tempDate.getMonth(), tempDate.getDate() ) );
							var day = today.getDay();
							var month = today.getMonth() + 1;
							var id = '#' + inst.id.replace( /\\\\/g, '\\' );
							var check = false;
							var string;
							var date = field.tm_datepicker( 'getDate' );

							if ( enabled_only_dates !== '' ) {
								string = $.tm_datepicker.formatDate( format, date );
								check = enabled_only_dates.indexOf( string ) !== -1;
							} else if ( disabled_months.indexOf( month.toString() ) !== -1 || disabled_weekdays.indexOf( day.toString() ) !== -1 ) {
								check = false;
							} else {
								if ( disabled_dates !== '' ) {
									string = $.tm_datepicker.formatDate( format, date );
									return [ disabled_dates.indexOf( string ) === -1, '' ];
								}
								check = true;
							}
							if ( check ) {
								$.tm_datepicker._setDate( inst, today );
								$.tm_datepicker._gotoToday( id );
							}
						} );
						jBody.addClass( 'tm-static' );
						field.prop( 'readonly', true );

						jWindow.trigger( {
							type: 'tm-datepicker-beforeShow',
							input: input,
							inst: inst
						} );
					},
					onClose: function() {
						jBody.removeClass( 'tm-static' );
						field.prop( 'readonly', false );
						field.removeAttr( 'readonly' );
						field.trigger( 'change' );
					},
					beforeShowDay: function( date ) {
						var day = date.getDay();
						var month = date.getMonth() + 1;
						var string;

						if ( enabled_only_dates !== '' ) {
							string = $.tm_datepicker.formatDate( format, date );
							return [ enabled_only_dates.indexOf( string ) !== -1, '' ];
						}
						if ( disabled_months.indexOf( month.toString() ) !== -1 || disabled_weekdays.indexOf( day.toString() ) !== -1 ) {
							return [ false, '' ];
						}
						if ( disabled_dates !== '' ) {
							string = $.tm_datepicker.formatDate( format, date );
							return [ disabled_dates.indexOf( string ) === -1, '' ];
						}
						return [ true, '' ];
					}
				} );

				$( '#ui-tm-datepicker-div' ).hide();
			} );

		obj.find( '.tmcp-date-select' )
			.on( 'change.cpf', function() {
				var id = '#' + $.epoAPI.dom.id( $( this ).attr( 'data-tm-date' ) );
				var input = obj.find( id );
				var format = input.attr( 'data-date-format' );
				var day = obj.find( id + '_day' ).val();
				var month = obj.find( id + '_month' ).val();
				var year = obj.find( id + '_year' ).val();
				var dateFormat = $.tm_datepicker.formatDate( format, new Date( year, parseInt( month, 10 ) - 1, day ) );

				if ( day > 0 && month > 0 && year > 0 ) {
					input.tm_datepicker( 'setDate', dateFormat );
					input.trigger( 'change' );
				} else {
					input.val( '' );
					input.trigger( 'change.cpf' );
				}
			} )
			.on( 'focus.cpf', function() {
				var id = '#' + $.epoAPI.dom.id( $( this ).attr( 'data-tm-date' ) );
				var input = obj.find( id );
				var day_select = obj.find( id + '_day' );
				var month_select = obj.find( id + '_month' );
				var year_select = obj.find( id + '_year' );
				var day = day_select.val();
				var month = month_select.val();
				var year = year_select.val();
				var _select = $( this );

				if ( ( year !== '' && month !== '' && day !== '' ) || ( year !== '' && month !== '' && day === '' ) || ( day !== '' && year !== '' && month === '' ) || ( day !== '' && month !== '' && year === '' ) ) {
					_select
						.find( 'option' )
						.toArray()
						.forEach( function( element ) {
							var option = $( element );
							var val = option.val();
							var date_string = year + '-' + month + '-' + day;
							var d;

							if ( _select.is( '.tmcp-date-day' ) ) {
								if ( year === '' || month === '' ) {
									return;
								}
								date_string = year + '-' + month + '-' + val;
							} else if ( _select.is( '.tmcp-date-month' ) ) {
								if ( year === '' || day === '' ) {
									return;
								}
								date_string = year + '-' + val + '-' + day;
							} else if ( _select.is( '.tmcp-date-year' ) ) {
								if ( day === '' || month === '' ) {
									return;
								}
								date_string = val + '-' + month + '-' + day;
							}

							if ( val !== '' ) {
								try {
									d = $.tm_datepicker.parseDate( 'yy-mm-dd', date_string );
									if ( d ) {
										if ( validate_date_with_options( d, input ) ) {
											option.prop( 'disabled', false );
										} else {
											option.prop( 'disabled', true );
										}
									}
								} catch ( err ) {
									window.console.log( err );

									option.prop( 'disabled', true );
									errorObject = err;
								}
							}
						} );
				} else {
					day_select.find( 'option' ).prop( 'disabled', false );
					month_select.find( 'option' ).prop( 'disabled', false );
					year_select.find( 'option' ).prop( 'disabled', false );
				}
			} );

		jWindow.on( 'resizestart', function() {
			var activeElement = $( document.activeElement );

			if ( activeElement.is( '.hasDatepicker' ) ) {
				activeElement.data( 'resizestarted', true );

				// we don't use jWindow here because we want the current window width
				if ( $( window ).width() < 768 ) {
					activeElement.data( 'resizewidth', true );
					return;
				}
				activeElement.tm_datepicker( 'hide' );
			}
		} );
		jWindow.on( 'resizestop', function() {
			var activeElement = $( document.activeElement );

			if ( activeElement.is( '.hasDatepicker' ) && activeElement.data( 'resizestarted' ) ) {
				if ( activeElement.data( 'resizewidth' ) ) {
					activeElement.tm_datepicker( 'hide' );
				}
				activeElement.tm_datepicker( 'show' );
			}
			activeElement.data( 'resizestarted', false );
			activeElement.data( 'resizewidth', false );
		} );
	}

	function apply_submit_events( epoObject ) {
		var epoEventId = epoObject.epoEventId;
		var main_product = epoObject.main_product;
		var type;
		var form_is_submit = ! $.tcepo.formSubmitEvents[ epoEventId ].some( function( form_event ) {
			return typeof form_event && ( typeof form_event.trigger === 'function' || false ) && ! form_event.trigger();
		} );

		$.tcepo.formSubmitEvents[ epoEventId ].forEach( function( form_event ) {
			type = typeof form_event;
			if ( type === 'object' ) {
				if ( form_is_submit ) {
					form_event.on_true();
				} else {
					form_event.on_false();
				}
			}
		} );

		if ( ! form_is_submit ) {
			setTimeout( function() {
				main_product.find( tcAPI.addToCartButtonSelector ).first().removeClass( 'disabled' ).removeClass( 'loading' ).removeAttr( 'disabled' ).removeClass( 'fpd-disabled' );
			}, 100 );
		}

		jWindow.trigger( 'tm-apply-submit-events', {
			epo: {
				form_is_submit: form_is_submit
			}
		} );

		return form_is_submit;
	}

	function tm_apply_validation( epoObject ) {
		var form = epoObject.form;
		var this_epo_container = epoObject.this_epo_container;
		var main_product = epoObject.main_product;
		var epoEventId = epoObject.epoEventId;
		var validation_rules;
		var has_rules;

		if ( TMEPOJS.tm_epo_global_enable_validation === 'yes' ) {
			validation_rules = {};

			this_epo_container
				.find( '.tmcp-ul-wrap' )
				.toArray()
				.forEach( function( tmcpulwrap ) {
					var field;
					var field_name;
					var subField;
					var subFieldName;
					var subRule;
					var productField;

					tmcpulwrap = $( tmcpulwrap );
					has_rules = tmcpulwrap.data( 'tm-validation' );
					if ( has_rules && $.tmType( has_rules ) === 'object' ) {
						field = tmcpulwrap.find( '.tm-epo-field' );
						field_name = field.first().attr( 'name' );
						if ( tmcpulwrap.is( '.tm-extra-product-options-radio.tm-element-ul-radio' ) ) {
							field_name = field.last().attr( 'name' );
							validation_rules[ field_name ] = has_rules;
						} else if ( tmcpulwrap.is( '.tm-extra-product-options-checkbox.tm-element-ul-checkbox' ) ) {
							field.each( function( f, fname ) {
								if ( 'required' in has_rules ) {
									has_rules.required = function( elem ) {
										var len = tmcpulwrap.find( 'input.tm-epo-field.tmcp-checkbox:checked' ).length;
										if ( len === 0 ) {
											if ( field.last().attr( 'name' ) === $( elem ).attr( 'name' ) ) {
												return true;
											}
											return false;
										}
										return len <= 0;
									};
								}
								validation_rules[ $( fname ).attr( 'name' ) ] = has_rules;
							} );
						} else if ( tmcpulwrap.is( '.tm-extra-product-options-product.tm-element-ul-product' ) ) {
							if ( 'required' in has_rules ) {
								subField = tmcpulwrap.find( '.product-variation-id' );
								subFieldName = subField.first().attr( 'name' );
								productField = tmcpulwrap.find( '.tc-epo-field-product' ).first();
								subRule = {
									required: function() {
										if ( productField.is( 'select' ) && field_is_active( productField, true ) ) {
											if ( productField.children( ':selected' ).attr( 'data-type' ) === 'variable' ) {
												return true;
											}
										}
										return false;
									}
								};
								validation_rules[ subFieldName ] = subRule;
							}
							validation_rules[ field_name ] = has_rules;
						} else {
							validation_rules[ field_name ] = has_rules;
						}
					}
				} );

			form.removeData( 'tc_validator' );
			form.tc_validate( {
				focusInvalid: false,
				ignore:
					tcAPI.qtySelector +
					",.tcdisabled,.tmcp-upload-hidden,#wc_bookings_field_duration,input.tm-qty:hidden[type='number'],input.input-text.qty,.ignore,.variations select,.tc-epo-variable-product-selector,.tm-extra-product-options-variations input,.tm-extra-product-options-variations select,input:not(.tc-extra-product-options input),select:not(.tc-extra-product-options select)",
				rules: validation_rules,
				errorClass: 'tm-error',
				validClass: 'tm-valid',
				errorElement: 'label',
				errorPlacement: function( error, element ) {
					if ( element.is( '.tm-epo-field.tmcp-radio' ) || element.is( '.tm-epo-field.tmcp-checkbox' ) || element.is( '.tm-epo-field.tmcp-radio' ) ) {
						if ( TMEPOJS.tm_epo_global_error_label_placement === 'before' ) {
							error.prependTo( element.closest( '.tmcp-ul-wrap' ).parent() );
						} else {
							error.appendTo( element.closest( '.tmcp-ul-wrap' ).parent() );
						}
					} else if ( TMEPOJS.tm_epo_global_error_label_placement === 'before' ) {
						error.prependTo( element.closest( '.tmcp-field-wrap' ) );
					} else {
						error.appendTo( element.closest( '.tmcp-field-wrap' ) );
					}
					return false;
				},
				invalidHandler: function( event, validator ) {
					jWindow.trigger( 'tm-invalidHandler', {
						epo: {
							validator: validator
						}
					} );
					setTimeout( function() {
						main_product.find( tcAPI.addToCartButtonSelector ).first().removeClass( 'disabled' ).removeClass( 'loading' ).removeAttr( 'disabled' ).removeClass( 'fpd-disabled' );
					}, 100 );
					if ( validator.errorList && validator.errorList[ 0 ] && validator.errorList[ 0 ].element ) {
						goto_error_item( $( validator.errorList[ 0 ].element ), epoEventId );
					}
				},
				submitHandler: function() {
					var ajaxSuccessFunc;
					if ( ! epoObject.is_quickview ) {
						main_product.find( tcAPI.addToCartButtonSelector ).first().addClass( 'disabled' );
						ajaxSuccessFunc = function() {
							main_product.find( tcAPI.addToCartButtonSelector ).first().removeClass( 'disabled' );
							jDocument.off( 'ajaxSuccess', ajaxSuccessFunc );
						};
						jDocument.on( 'ajaxSuccess', ajaxSuccessFunc );
					}
					return apply_submit_events( epoObject );
				}
			} );

			// This should handle most ajax based add to cart solutions
			form.find( '.single_add_to_cart_button' ).on( 'click', function( e ) {
				if ( ! form.tc_validate().form() ) {
					e.preventDefault();
					e.stopImmediatePropagation();
				}
			} );

			return true;
		}
		return false;
	}

	function tm_form_submit_event( epoObject ) {
		var form = epoObject.form;
		var epoEventId = epoObject.epoEventId;

		jWindow.trigger( 'tm-from-submit', {
			epo: epoObject,
			functions: {
				tm_apply_validation: tm_apply_validation,
				apply_submit_events: apply_submit_events
			}
		} );
		if ( ! tm_apply_validation( epoObject ) && $.tcepo.formSubmitEvents[ epoEventId ].length ) {
			form.on( 'submit', function() {
				apply_submit_events( epoObject );
			} );
		}
	}

	function found_variation_tmepo( dataObject ) {
		var totalsHolder = dataObject.totalsHolder;
		var totalsHolderContainer = dataObject.totalsHolderContainer;
		var currentCart = dataObject.currentCart;
		var variationForm = dataObject.variationForm;
		var variation = dataObject.variation;
		var variations = totalsHolder.data( 'variations' );
		var product_price;

		totalsHolder.data( 'current_variation', variation );

		/**
		 * Currency converters that don't allow multi currency checkout will fail the following if statement
		 *
		 * if (variation.display_price!=undefined) {
		 *     product_price = variation.display_price;
		 *     totalsHolder.data('price', product_price);
		 * } else ...
		 *
		 */
		if ( variations && variation && variation.variation_id && variations[ variation.variation_id ] !== undefined ) {
			product_price = variations[ variation.variation_id ];
			totalsHolder.data( 'price', product_price );
			// Fancy product Designer
			totalsHolder.removeData( 'tcprice' );
		} else if ( variation && 'display_price' in variation ) {
			product_price = variation.display_price;
			totalsHolder.data( 'price', product_price );
			totalsHolder.data( 'priceIsWithDiscount', '1' );
			// Fancy product Designer
			totalsHolder.removeData( 'tcprice' );
		} else if ( variation && $( variation.price_html ).find( '.amount:last' ).length ) {
			product_price = $( variation.price_html ).find( '.amount:last' ).text();
			product_price = product_price.replace( TMEPOJS.currency_format_thousand_sep, '' );
			product_price = product_price.replace( TMEPOJS.currency_format_decimal_sep, '.' );
			product_price = product_price.replace( /[^0-9.]/g, '' );
			product_price = parseFloat( product_price );
			totalsHolder.data( 'price', product_price );
			// Fancy product Designer
			totalsHolder.removeData( 'tcprice' );
		}

		totalsHolderContainer.find( '.cpf-product-price' ).val( product_price );

		// This must be run every time to get correct results for percent price types
		// if set set norules then discount will not auto work upon chosing a variation
		if ( ! variationForm.data( 'tm-styled-variations' ) ) {
			currentCart.trigger( {
				type: 'tm-epo-update'
			} );
		}
	}

	function fetchOptionPrices( epoObject, epoHolder, selector, total, original_total, floatingBoxData, showTotal, forced ) {
		var obj = epoHolder.find( selector );

		if ( epoObject.is_associated === false ) {
			obj = obj.not( tcAPI.associatedEpoSelector + ' ' + selector );
		}
		if ( ! total ) {
			total = 0;
		}
		if ( ! original_total ) {
			original_total = 0;
		}
		if ( ! floatingBoxData ) {
			floatingBoxData = [];
		}

		if ( ! forced ) {
			obj = obj.filter( '.tcenabled' );
		}

		obj.toArray().forEach( function( tmcpfield ) {
			var field = $( tmcpfield );
			var _value = '';
			var fieldval;
			var field_div = field.closest( '.cpf_hide_element' );
			var field_wrap = field.closest( '.tmcp-field-wrap' );
			var field_label_show = field_div.attr( 'data-fblabelshow' );
			var field_value_show = field_div.attr( 'data-fbvalueshow' );
			var field_title = '';
			var option_quantity = field_wrap.find( '.tm-qty' ).val();
			var option_price;
			var option_price_taxed;
			var option_original_price;
			var option_original_price_taxed;
			var liw;
			var cri;
			var tl;
			var options;
			var forrangepicker;
			var $decimals;
			var _valueText;
			var setter;

			if ( field_label_show === '' ) {
				field_title = field_div.find( '.tm-epo-element-label' ).html();
			}

			if ( option_quantity === undefined ) {
				option_quantity = '';
			}
			if ( field.is( ':checkbox, :radio, :input' ) ) {
				option_price = 0;
				option_price_taxed = 0;
				option_original_price = 0;
				option_original_price_taxed = 0;
				if ( field.is( '.tmcp-checkbox, .tmcp-radio' ) ) {
					if ( forced || field.is( ':checked' ) ) {
						option_price = field.data( 'raw_price' );
						option_price_taxed = field.data( 'price' );
						option_original_price = field.data( 'raw_original_price' );
						option_original_price_taxed = field.data( 'original_price' );
						showTotal = true;
						field.data( 'isset', 1 );
						liw = field.closest( 'li.tmcp-field-wrap' );
						cri = liw.find( '.checkbox_image,.radio_image' );
						_value = '';
						_valueText = '';

						tl = field.closest( 'li.tmcp-field-wrap' ).find( '.tm-label' );
						if ( tl.length ) {
							_value = tl.html();
							_valueText = _value;
						}

						if ( cri.length ) {
							_value = _value + cri.clone().addClass( 'tc-img-floating' )[ 0 ].outerHTML;
						}

						if ( field.is( '.use_images' ) ) {
							_value = liw.find( '.tc-label' ).first().html();
							_valueText = _value;
							if ( cri.length ) {
								_value = _value + '<img class="tc-img-floating" src="' + field.attr( 'data-image' ) + '"';
							}
						}
						floatingBoxData.push( {
							title: field_title,
							value: _value,
							valueText: _valueText,
							price: option_price_taxed,
							original_price: option_original_price_taxed,
							quantity: option_quantity,
							label_show: field_label_show,
							value_show: field_value_show
						} );
					} else {
						field.data( 'isset', 0 );
					}
				} else if ( field.is( '.tmcp-select' ) ) {
					setter = field.find( 'option:selected' );
					option_price = setter.data( 'raw_price' );
					option_price_taxed = setter.data( 'price' );
					option_original_price = setter.data( 'raw_original_price' );
					option_original_price_taxed = setter.data( 'original_price' );

					options = field.children( 'option:selected' );
					if ( ! ( options.val() === '' && options.attr( 'data-rulestype' ) === '' ) ) {
						showTotal = true;
					}

					field.find( 'option' ).data( 'isset', 0 );
					setter.data( 'isset', 1 );

					if ( ! ( setter.val() === '' && setter.attr( 'data-rulestype' ) === '' ) ) {
						_value = setter.attr( 'data-text' );

						floatingBoxData.push( {
							title: field_title,
							value: _value,
							valueText: _value,
							price: option_price_taxed,
							original_price: option_original_price_taxed,
							quantity: option_quantity,
							label_show: field_label_show,
							value_show: field_value_show
						} );
					}
				} else {
					fieldval = field.val();
					if ( field.is( "[type='file']" ) ) {
						fieldval = fieldval.replace( 'C:\\fakepath\\', '' );
					}
					if ( fieldval ) {
						if ( field.is( '.tmcp-range' ) && fieldval === '0' ) {
							field.data( 'isset', 0 );
						} else {
							option_price = field.data( 'raw_price' );
							option_price_taxed = field.data( 'price' );
							option_original_price = field.data( 'raw_original_price' );
							option_original_price_taxed = field.data( 'original_price' );
							showTotal = true;
							field.data( 'isset', 1 );

							_value = fieldval;
							if ( field.is( '.tmcp-range' ) ) {
								forrangepicker = $( ".tm-range-picker[data-field-id='" + field.attr( 'id' ) + "']" );
								$decimals = forrangepicker.attr( 'data-step' ).split( '.' );
								if ( $decimals.length === 1 ) {
									$decimals = 0;
								} else {
									$decimals = $decimals[ 1 ].length;
								}
								_value = formatPrice( _value, { precision: $decimals } );
							}

							floatingBoxData.push( {
								title: field_title,
								value: _value,
								valueText: _value,
								price: option_price_taxed,
								original_price: option_original_price_taxed,
								quantity: option_quantity,
								label_show: field_label_show,
								value_show: field_value_show
							} );
						}
					} else {
						field.data( 'isset', 0 );
					}
				}
				if ( ! option_price ) {
					option_price = 0;
				}
				if ( ! option_price_taxed ) {
					option_price_taxed = 0;
				}
				if ( ! option_original_price ) {
					option_original_price = 0;
				}
				if ( ! option_original_price_taxed ) {
					option_original_price_taxed = 0;
				}
				total = parseFloat( total ) + parseFloat( option_price );
				original_total = parseFloat( original_total ) + parseFloat( option_original_price );
			}
		} );

		return {
			total: total,
			original_total: original_total,
			floatingBoxData: floatingBoxData,
			showTotal: showTotal,
			elementsLength: obj.length
		};
	}

	function show_product_html( thisEpoObject, thisMainProduct, thisVariableProductContainer, type, $this, currentCart, variableProductContainers, isTrigger ) {
		var epoObjectCopy = $.extend( true, {}, thisEpoObject );
		var item_tm_extra_product_options = thisVariableProductContainer.find( tcAPI.associatedEpoSelector );
		var item = thisVariableProductContainer;
		var newEpoObject;
		var showOnly = true;

		variableProductContainers.addClass( 'tm-hidden' );

		if ( type === 'variable' ) {
			if ( ! thisVariableProductContainer.is( '.tc-init-variations' ) ) {
				thisVariableProductContainer.addClass( 'variations_form' );
				setTimeout( function() {
					newEpoObject = tm_init_epo( item, false, item_tm_extra_product_options.attr( 'data-product-id' ), item_tm_extra_product_options.attr( 'data-epo-id' ), $this, epoObjectCopy );
					thisVariableProductContainer.addClass( 'tc-init-variations' );
					thisVariableProductContainer.tc_product_variation_form( $this, currentCart, variableProductContainers, newEpoObject );
				}, 40 );
				showOnly = false;
			} else {
				thisVariableProductContainer.trigger( 'refresh.tc-variation-form' );
			}
		} else if ( ! thisVariableProductContainer.is( '.tc-init-product' ) ) {
			thisVariableProductContainer.addClass( 'tc-init-product' );
			variableProductContainers.find( '.tc-epo-element-variable-product' ).removeClass( 'variations_form' );
			variableProductContainers.find( '.tc-epo-element-variations' ).removeClass( 'variations' );
			setTimeout( function() {
				tm_init_epo( item, false, item_tm_extra_product_options.attr( 'data-product-id' ), item_tm_extra_product_options.attr( 'data-epo-id' ), $this, epoObjectCopy );
			}, 20 );
			showOnly = false;
		}

		thisVariableProductContainer.removeClass( 'tm-hidden' );
		if ( showOnly ) {
			jWindow.trigger( 'cpflogicdone' );
		}

		if ( isTrigger === undefined && TMEPOJS.tm_epo_global_product_element_scroll === 'yes' ) {
			jWindow.tcScrollTo( thisVariableProductContainer, 200, $.epoAPI.math.toFloat( TMEPOJS.tm_epo_global_product_element_scroll_offset ) );
		}
	}

	function epoEventHandlers( epoObject, cartContainer, alternativeCart ) {
		// if cartContainer & alternativeCart is defined we are on a non default product (eg. composite product)
		var product_id = epoObject.product_id;
		var main_product = epoObject.main_product;
		var main_cart = epoObject.main_cart;
		var this_epo_container = epoObject.this_epo_container;
		var this_totals_container = epoObject.this_totals_container;
		var this_epo_totals_container = epoObject.this_epo_totals_container;
		var epoEventId = epoObject.epoEventId;
		var main_epo_inside_form = epoObject.main_epo_inside_form;
		var epo_id_selector = epoObject.epo_id_selector;
		var epo_id = epoObject.epo_id;
		var product_id_selector = epoObject.product_id_selector;
		var itemId = 'main';
		var epoHolder;
		var totalsHolderContainer;
		var totalsHolder;
		var currentCart;
		var variation_id_selector;
		var this_product_type;
		var variationForm;
		var qtyElement;
		var finalTotalBoxMode;
		var eventName = epoObject.is_associated ? 'tc-variation-form' : 'wc-variation-form';
		var eventNamePrefix = epoObject.is_associated ? 'tc_' : '';
		var epoVariationSection;

		// Non default product (eg. composite product)
		if ( alternativeCart && cartContainer ) {
			itemId = $.epoAPI.applyFilter( 'tc_get_item_id', cartContainer.attr( 'data-item_id' ), cartContainer );
			epoHolder = main_product.find( '.tm-extra-product-options.tm-cart-' + itemId );
			totalsHolderContainer = main_product.find( '.tm-totals-form-' + itemId );
			totalsHolder = main_product.find( '.tm-epo-totals.tm-cart-' + itemId );
			variationForm = cartContainer.find( '.variations_form' ).first();
			// Default product
		} else {
			if ( ! main_cart || main_cart.length === 0 ) {
				if ( this_epo_container.is( '.tc-shortcode' ) ) {
					main_cart = main_product;
				} else {
					main_cart = get_main_cart( main_product, main_product, 'form', product_id );
				}
			}
			cartContainer = main_cart.parent();
			epoHolder = this_epo_container;
			totalsHolderContainer = this_totals_container;
			totalsHolder = this_epo_totals_container;
			variationForm = epoObject.variations_form;
		}

		if ( epoObject.is_associated ) {
			itemId = epoHolder.attr( 'data-cart-id' );
		}

		currentCart = alternativeCart || main_cart;
		totalsHolder.data( 'tm_for_cart', currentCart );

		variation_id_selector = getVariationIdSelector( currentCart );
		qtyElement = getQtyElement( currentCart );

		totalsHolder.data( 'variationIdElement', getVariationIdElement( currentCart, '.wceb_picker_wrap ' + variation_id_selector ) );
		totalsHolder.data( 'qty_element', qtyElement );

		this_product_type = totalsHolder.data( 'type' );

		variationForm.data( 'tc_product_id', product_id );

		finalTotalBoxMode = totalsHolder.attr( 'data-tm-epo-final-total-box' );

		jWindow.on( 'epoCalculateRules', function( event, dataObject ) {
			if ( event && dataObject && dataObject.currentCart ) {
				tm_epo_rules( epoObject, dataObject.currentCart );
			}
		} );

		tm_epo_rules( epoObject, currentCart );

		// update price amount for select elements
		epoHolder
			.find( 'select.tm-epo-field' )
			.off( 'tm-select-change-html' )
			.on( 'tm-select-change-html', function() {
				var field;
				var formatted_price;
				var original_formatted_price;
				var e_tip;
				var e_description;
				var sign;

				if ( alternativeCart && main_cart && main_cart.data( 'per_product_pricing' ) !== undefined && ! main_cart.data( 'per_product_pricing' ) ) {
					return;
				}

				field = $( this );
				formatted_price = tm_set_price( field.find( 'option:selected' ).data( 'price' ), totalsHolder, true, false, field );
				original_formatted_price = tm_set_price( field.find( 'option:selected' ).data( 'original_price' ), totalsHolder, false, false, field );
				e_tip = field.closest( '.tmcp-field-wrap' ).find( '.tc-tooltip' );
				e_description = field.closest( '.tmcp-field-wrap' ).find( '.tc-inline-description' );

				tm_update_price( field.closest( '.tmcp-field-wrap' ).find( '.tc-price' ), field.find( 'option:selected' ).data( 'price' ), formatted_price, field.find( 'option:selected' ).data( 'original_price' ), original_formatted_price );

				if ( e_tip.length > 0 ) {
					e_tip.attr( 'data-tm-tooltip-html', field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) ).trigger( 'tc-tooltip-html-changed' );
				}

				if ( e_description.length > 0 ) {
					if ( field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) ) {
						e_description.html( field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) );
					} else {
						e_description.html( '' );
					}
				}

				if (
					( field.find( 'option:selected' ).attr( 'data-hide-amount' ) === '0' || TMEPOJS.tm_epo_show_price_inside_option_hidden_even === 'yes' ) &&
					TMEPOJS.tm_epo_show_price_inside_option === 'yes' &&
					field.find( 'option:selected' ).attr( 'data-text' )
				) {
					if (
						( TMEPOJS.tm_epo_auto_hide_price_if_zero === 'yes' && $.tmempty( field.find( 'option:selected' ).data( 'price' ) ) === false ) ||
						( TMEPOJS.tm_epo_auto_hide_price_if_zero !== 'yes' && field.find( 'option:selected' ).attr( 'data-price' ) !== '' )
					) {
						sign = '';
						field.find( 'option:selected' ).html( field.find( 'option:selected' ).attr( 'data-text' ) + ' (' + sign + formatted_price + ')' );
					}
				}

				if ( field.val() === '' ) {
					e_tip.addClass( 'tm-hidden' );
				} else if ( field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) ) {
					e_tip.removeClass( 'tm-hidden' );
				} else {
					e_tip.addClass( 'tm-hidden' );
				}
			} )
			.off( 'tm-math-select-change-html-all tm-select-change-html-all' )
			.on( 'tm-math-select-change-html-all tm-select-change-html-all', function( event ) {
				var field;
				var e_tip;
				var e_description;
				var thisoption;
				var divider;
				var thisformatted_price;

				if ( alternativeCart && main_cart && main_cart.data( 'per_product_pricing' ) !== undefined && ! main_cart.data( 'per_product_pricing' ) ) {
					return;
				}

				field = $( this );
				e_tip = field.closest( '.tmcp-field-wrap' ).find( '.tc-tooltip' );
				e_description = field.closest( '.tmcp-field-wrap' ).find( '.tc-inline-description' );

				if ( e_tip.length > 0 ) {
					e_tip.attr( 'data-tm-tooltip-html', field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) ).trigger( 'tc-tooltip-html-changed' );
				}

				if ( e_description.length > 0 ) {
					if ( field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) ) {
						e_description.html( field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) );
					} else {
						e_description.html( '' );
					}
				}

				if ( TMEPOJS.tm_epo_show_price_inside_option === 'yes' ) {
					field.find( 'option' ).each( function() {
						thisoption = $( this );
						if ( ! thisoption.val() ) {
							return true;
						}

						if ( event.type === 'tm-select-change-html-all' ) {
							thisoption.removeClass( 'tm-epo-late-field' ).removeData( 'tm-price-for-late islate' );
							tm_element_epo_rules( epoObject, field, undefined, thisoption, 1 );
						} else if ( event.type === 'tm-math-select-change-html-all' ) {
							//thisoption.removeClass("tm-epo-late-field").removeData("tm-price-for-late islate");
							tm_element_epo_rules( epoObject, field, undefined, thisoption, 1, true );
						}
						divider = 1;

						if ( TMEPOJS.tm_epo_multiply_price_inside_option !== 'yes' ) {
							divider = field.data( 'tm-quantity' );
						}

						if ( ! divider ) {
							divider = 1;
						}

						thisformatted_price = tm_set_price( thisoption.data( 'price' ) / divider, totalsHolder, true, false, field );

						if ( ( thisoption.attr( 'data-hide-amount' ) === '0' || TMEPOJS.tm_epo_show_price_inside_option_hidden_even === 'yes' ) && thisoption.attr( 'data-text' ) ) {
							if ( ( TMEPOJS.tm_epo_auto_hide_price_if_zero === 'yes' && $.tmempty( thisoption.data( 'price' ) ) === false ) || ( TMEPOJS.tm_epo_auto_hide_price_if_zero !== 'yes' && thisoption.attr( 'data-price' ) !== '' ) ) {
								thisoption.html( thisoption.attr( 'data-text' ) + ' (' + thisformatted_price + ')' );
							}
						}
					} );
				}

				if ( field.val() === '' ) {
					e_tip.addClass( 'tm-hidden' );
				} else if ( field.find( 'option:selected' ).attr( 'data-tm-tooltip-html' ) ) {
					e_tip.removeClass( 'tm-hidden' );
				} else {
					e_tip.addClass( 'tm-hidden' );
				}
			} )
			.off( 'tm-select-change' )
			.on( 'tm-select-change', function() {
				var field;

				if ( alternativeCart && main_cart && main_cart.data( 'per_product_pricing' ) !== undefined && ! main_cart.data( 'per_product_pricing' ) ) {
					return;
				}

				field = $( this );
				field.trigger( 'tm-select-change-html' );
				field.trigger( 'tm-select-change-html-all' );

				currentCart.trigger( {
					type: 'tm-epo-update',
					norules: 1,
					element: field
				} );
			} );

		// Element quantity selector
		epoHolder
			.off( 'focus.cpf', '.tm-quantity .tm-qty' )
			.on( 'focus.cpf', '.tm-quantity .tm-qty', function() {
				var qtyField = $( this );
				var field = qtyField.closest( '.tmcp-field-wrap' ).find( '.tm-epo-field' );
				var currentVal = parseFloat( qtyField.val() );
				var max = parseFloat( qtyField.attr( 'max' ) );
				var min = parseFloat( qtyField.attr( 'min' ) );
				var step = qtyField.attr( 'step' );
				var check1 = tm_limit_c_selection( field, false );
				var check2 = tm_exact_c_selection( field, false );
				var check3 = true;

				// Format values
				if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) {
					currentVal = 0;
				}
				if ( max === '' || max === 'NaN' ) {
					max = '';
				}
				if ( min === '' || min === 'NaN' ) {
					min = 0;
				}
				if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) {
					step = 1;
				}

				if ( currentVal < min || currentVal > max ) {
					check3 = false;
				}

				if ( check1 && check2 && check3 ) {
					qtyField.data( 'tm-prev-value', currentVal );
				} else {
					qtyField.data( 'tm-prev-value', min );
				}
			} )
			.off( 'change.cpf', '.tm-quantity .tm-qty' )
			.on( 'change.cpf', '.tm-quantity .tm-qty', function( event, data ) {
				var qtyField = $( this );
				var field = qtyField.closest( '.tmcp-field-wrap' ).find( '.tm-epo-field' );
				var currentVal = parseFloat( qtyField.val() );
				var max = parseFloat( qtyField.attr( 'max' ) );
				var min = parseFloat( qtyField.attr( 'min' ) );
				var step = qtyField.attr( 'step' );
				var check1 = tm_limit_c_selection( field, false );
				var check2 = tm_exact_c_selection( field, false );
				var check3 = true;

				// Format values
				if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) {
					currentVal = 0;
				}
				if ( max === '' || max === 'NaN' ) {
					max = '';
				}
				if ( min === '' || min === 'NaN' ) {
					min = 0;
				}
				if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) {
					step = 1;
				}

				if ( currentVal < min || currentVal > max ) {
					check3 = false;
				}

				if ( check1 && check2 && check3 ) {
					if ( ! epoObject.noEpoUpdate ) {
						field.data( 'tm-quantity', qtyField.val() ).trigger( 'change', data );
					} else {
						field.data( 'tm-quantity', qtyField.val() ).trigger( 'change.cpf', data ).trigger( 'change.cpfproduct', data );
					}
					field.trigger( 'tm-select-change-html-all' );
				} else if ( qtyField.data( 'tm-prev-value' ) ) {
					qtyField.val( qtyField.data( 'tm-prev-value' ) );
				} else {
					qtyField.val( min );
				}

				qtyField.trigger( 'cpf-changed' );
			} )
			.off( 'tmaddquantity', '.tm-quantity .tm-qty' )
			.on( 'tmaddquantity', '.tm-quantity .tm-qty', function() {
				var qtyField = $( this );
				var field = qtyField.closest( '.tmcp-field-wrap' ).find( '.tm-epo-field' );

				field.data( 'tm-quantity', qtyField.val() );
			} );

		// Insert characters remaining for text-areas and text-fields
		epoHolder.find( '.tmcp-textfield.tm-epo-field[maxlength],textarea.tm-epo-field[maxlength]' ).each( function() {
			var field = $( this );
			var html = $.epoAPI.template.html( tcAPI.templateEngine.tc_chars_remanining, {
				maxlength: field.attr( 'maxlength' ),
				characters_remaining: TMEPOJS.i18n_characters_remaining
			} );

			field.after( $( html ) );
		} );
		epoHolder
			.find( 'input.tm-epo-field[maxlength],textarea.tm-epo-field[maxlength]' )
			.off( 'change.tc_maxlen input.tc_maxlen' )
			.on( 'change.tc_maxlen input.tc_maxlen', function() {
				var field = $( this );

				field
					.closest( '.tmcp-field-wrap' )
					.find( '.tc-chars-remanining' )
					.html( parseInt( field.attr( 'maxlength' ), 10 ) - parseInt( field.val().length, 10 ) );
			} );

		// Change product image event
		epoHolder
			.find( '.tm-epo-field' )
			.off( 'tm_trigger_product_image' )
			.on( 'tm_trigger_product_image', function() {
				var field = $( this );
				var currentElement;
				var uic;
				var variation_element_section;
				var is_variation_element;
				var src;

				if ( field.is( '.tm-product-image:checkbox, .tm-product-image:radio, select.tm-product-image' ) ) {
					uic = field.closest( '.tmcp-field-wrap' ).find( 'label img' );
					variation_element_section = field.closest( '.cpf-section' );
					is_variation_element = variation_element_section.is( '.tm-epo-variation-section' );

					currentElement = field;
					if ( field.is( 'select.tm-product-image' ) ) {
						currentElement = field.children( 'option:selected' );
					}
					if (
						$( uic ).length > 0 ||
						( is_variation_element && currentElement.attr( 'data-image' ) !== undefined ) ||
						( currentElement.attr( 'data-image' ) !== undefined && currentElement.attr( 'data-image' ) !== '' ) ||
						( currentElement.attr( 'data-imagep' ) !== undefined && currentElement.attr( 'data-imagep' ) !== '' )
					) {
						if ( field.is( ':checked' ) || ( field.is( 'select.tm-product-image' ) && field.val() !== '' && ( field.find( 'option:selected' ).attr( 'data-rules' ) !== '' || field.is( '.tm-epo-variation-element' ) ) ) ) {
							src = $( uic ).first().attr( 'data-original' );

							if ( ! src && ! is_variation_element ) {
								src = $( uic ).first().attr( 'src' );
							}
							if ( ! src ) {
								src = currentElement.attr( 'data-image' );
							}
							if ( currentElement.attr( 'data-imagep' ) ) {
								src = currentElement.attr( 'data-imagep' );
							}
							if ( src ) {
								main_product.trigger( 'tm_change_product_image', {
									src: src,
									element: field,
									element_current: currentElement,
									main_product: main_product,
									epo_holder: epoHolder
								} );
							} else {
								main_product.trigger( 'tm_change_product_image', {
									src: false,
									element: field,
									element_current: currentElement,
									main_product: main_product,
									epo_holder: epoHolder
								} );
							}
						} else {
							main_product.trigger( 'tm_restore_product_image', {
								element: field,
								element_current: currentElement,
								main_product: main_product,
								epo_holder: epoHolder
							} );
						}
					} else {
						main_product.trigger( 'tm_restore_product_image', {
							element: field,
							element_current: currentElement,
							main_product: main_product,
							epo_holder: epoHolder
						} );
					}
				} else {
					main_product.trigger( 'tm_attempt_product_image', {
						element: field,
						element_current: currentElement,
						main_product: main_product,
						epo_holder: epoHolder
					} );
				}
			} );

		epoHolder
			.find( '.tm-quantity' )
			.off( 'showhide.cpfcustom' )
			.on( 'showhide.cpfcustom', function() {
				var quantity_selector = $( this );
				var field = quantity_selector.closest( '.tmcp-field-wrap' ).find( '.tm-epo-field' );
				var show = false;
				var tmqty;
				var tmqtyval;
				var tmqtymin;
				var radios;

				if ( ! field.is( '.tm-epo-variation-element' ) ) {
					if ( field.is( 'select' ) ) {
						if ( field.val() !== '' ) {
							show = true;
						}
					} else if ( field.is( ':checkbox' ) ) {
						if ( field.is( ':checked' ) ) {
							show = true;
						}
					} else if ( field.is( ':radio' ) ) {
						if ( field.is( ':checked' ) ) {
							show = true;
							if ( TMEPOJS.tm_epo_show_only_active_quantities === 'yes' ) {
								radios = field.closest( '.cpf_hide_element' ).find( '.tm-epo-field.tmcp-radio' );
								radios.each( function() {
									$( this ).closest( '.tmcp-field-wrap' ).find( '.tm-quantity' ).hide();
								} );
							}
						}
					} else if ( field.val() ) {
						show = true;
					}

					tmqty = quantity_selector.find( '.tm-qty' );
					tmqtyval = tmqty.val();
					tmqtymin = tmqty.attr( 'min' ) || '';

					if ( show ) {
						if ( TMEPOJS.tm_epo_show_only_active_quantities === 'yes' ) {
							quantity_selector.show();
						}

						tmqty.removeClass( 'ignore' ).prop( 'disabled', false );
					} else {
						if ( TMEPOJS.tm_epo_show_only_active_quantities === 'yes' ) {
							quantity_selector.hide();
							if ( ! tmqtyval ) {
								tmqty.val( tmqtymin );
							}
						}

						tmqty.addClass( 'ignore' ).prop( 'disabled', true );
					}

					setTimeout( function() {
						quantity_selector.closest( '.tcowl-carousel' ).trigger( 'refresh.owl.carousel' );
					}, 200 );
				}
			} );

		epoHolder
			.find( '.tm-epo-field' )
			.off( 'change.cpfcustom' )
			.on( 'change.cpfcustom', function() {
				$( this ).closest( '.tmcp-field-wrap' ).find( '.tm-quantity' ).trigger( 'showhide.cpfcustom' );
			} );

		epoHolder
			.find( '.tm-epo-field' )
			.filter( ':checkbox:checked, :radio:checked' )
			.each( function() {
				$( this ).closest( '.tmcp-field-wrap' ).addClass( 'tc-active' );
			} );

		epoHolder
			.find( '.tm-epo-field' )
			.off( 'change.cpf' )
			.on( 'change.cpf', function( event, data ) {
				var field = $( this );
				var is_li = field.closest( '.tmcp-field-wrap' );
				var is_ul = field.closest( '.tmcp-ul-wrap' );
				var is_replace;

				if ( field.is( ':checkbox, :radio' ) ) {
					if ( field.is( ':radio' ) ) {
						if ( ! data ) {
							is_ul.find( '.tmcp-field-wrap' ).removeClass( 'tc-active' );
						}
					}
					if ( field.is( ':checked' ) ) {
						is_li.addClass( 'tc-active' );
					} else {
						is_li.removeClass( 'tc-active' );
					}
				}

				if ( ! field.is( '.tm-epo-variation-element' ) ) {
					if ( field.is( '.use_images:checkbox, .use_images:radio' ) && field.attr( 'data-imagec' ) ) {
						is_replace = is_li.find( '.radio_image,.checkbox_image' ).first();
						if ( is_replace.length > 0 ) {
							if ( field.is( ':checked' ) ) {
								is_replace.prop( 'src', field.attr( 'data-imagec' ) );
							} else {
								is_replace.prop( 'src', field.attr( 'data-image' ) );
							}
						}
					}

					if ( field.is( '.use_images:radio' ) ) {
						field
							.closest( '.cpf-type-radio' )
							.find( '.use_images:radio' )
							.not( field )
							.each( function() {
								var r = $( this );
								r.closest( '.tmcp-field-wrap' ).find( '.radio_image' ).first().prop( 'src', r.attr( 'data-image' ) );
							} );
					}

					if ( field.is( '.tmcp-range' ) ) {
						field.trigger( 'change.cpflogic' );
					}
					if ( field.is( 'select' ) ) {
						field.trigger( 'tm-select-change' );
					} else {
						if ( field.is( '.tmcp-radio' ) ) {
							field
								.closest( '.cpf_hide_element' )
								.find( '.tm-quantity .tm-qty' )
								.each( function() {
									if ( ! $( this ).closest( 'li.tmcp-field-wrap' ).find( '.tmcp-radio' ).is( ':checked' ) ) {
										$( this ).attr( 'disabled', 'disabled' );
									} else {
										$( this ).removeAttr( 'disabled' );
									}
								} );
						}

						currentCart.trigger( {
							type: 'tm-epo-update',
							norules: 1,
							element: field
						} );
					}
				}

				field.trigger( 'tm_trigger_product_image' );
				setTimeout( function() {
					$( '.tm-owl-slider' ).each( function() {
						$( this ).trigger( 'refresh.owl.carousel' );
					} );
				}, 200 );
				main_product.trigger( 'tm_attempt_product_image', {
					element: field,
					main_product: main_product,
					epo_holder: epoHolder
				} );
			} );

		epoHolder
			.find( '.tm-has-clearbutton .tm-epo-field' )
			.off( 'change.cpfclearbutton' )
			.on( 'change.cpfclearbutton cpfclearbutton', function() {
				var field = $( this );
				var radioResetElement;
				var fieldWrap = field.closest( '.tmcp-field-wrap' );

				if ( field.is( ':checked' ) ) {
					radioResetElement = field.closest( '.cpf_hide_element' ).find( '.tm-epo-reset-radio' ).removeClass( 'tm-hidden' );
					fieldWrap.append( radioResetElement );
				}
			} );

		epoHolder
			.find( '.tm-epo-reset-radio' )
			.off( 'click.cpf' )
			.on( 'click.cpf', function() {
				var radioResetElement = $( this );
				var fieldContainer = radioResetElement.closest( '.cpf_hide_element' );
				var checkedRadios = fieldContainer.find( '.tm-epo-field.tmcp-radio:checked' );

				if ( checkedRadios.length ) {
					checkedRadios.removeAttr( 'checked' ).prop( 'checked', false );
					//checkedRadios.trigger( 'change.cpflogic' );
					//checkedRadios.trigger( 'change.cpf' );
					checkedRadios.trigger( 'change', { forced: 1 } );
					if ( checkedRadios.is( '.tc-epo-field-product' ) ) {
						//checkedRadios.trigger( 'change.cpfproduct', { forced: 1 } );
					}
				}

				radioResetElement.addClass( 'tm-hidden' );
			} );

		if ( _ && _.debounce ) {
			epoHolder.find( '.tm-epo-field.tmcp-textarea,.tm-epo-field.tmcp-textfield' ).keyup(
				_.debounce( function() {
					$( this ).trigger( 'change.cpf' );
				}, 10 )
			);
		}

		epoHolder
			.find( '.tm-epo-field.tmcp-upload' )
			.off( 'change.cpfv change.tcupload' )
			.on( 'change.cpfv change.tcupload', function() {
				var field = $( this );
				var label = field.closest( 'label' );
				var li = field.closest( '.tmcp-field-wrap' );
				var cpfUploadContainer = li.find( '.cpf-upload-container' );
				var name = li.find( '.tm-filename' );
				var val = field.val().replace( 'C:\\fakepath\\', '' );
				var valHidden = field.attr( 'data-file' );
				var num_uploads;
				var windowURL = window.URL || window.webkitURL;
				var file = this.files[ 0 ];
				var image;
				var uploadPreview = li.find( '.tc-upload-preview' );

				if ( cpfUploadContainer.length && name.length <= 0 ) {
					name = $( '<span class="tm-filename"></span>' );
					label.after( name );
				}
				name.html( val );
				num_uploads = epoHolder.data( 'num_uploads' );
				if ( ! num_uploads ) {
					num_uploads = [];
				}
				if ( val !== '' ) {
					num_uploads[ field.closest( '.cpf_hide_element' ).attr( 'data-uniqid' ) ] = val;
				}
				epoHolder.data( 'num_uploads', num_uploads );
				field.next( '.tmcp-upload-hidden' ).remove();

				if ( ( val || valHidden ) && TMEPOJS.tm_epo_upload_inline_image_preview === 'yes' && windowURL && windowURL.createObjectURL && file ) {
					if ( ! uploadPreview.length ) {
						uploadPreview = $( '<div class="tc-upload-preview"></div>' );
						li.find( 'label.tm-epo-field-label' ).after( uploadPreview );
					}
					uploadPreview.empty();
					image = new Image();
					image.onload = function() {
						var src = this.src;
						uploadPreview.html( '<img src="' + src + '"></div>' );
					};
					if ( valHidden ) {
						image.src = valHidden;
					} else {
						image.src = windowURL.createObjectURL( file );
					}
				}
			} );

		qtyElement
			.off( 'change.cpf' )
			.on( 'change.cpf', function() {
				var field = $( this );

				currentCart.trigger( 'tm-epo-check-dpd' );
				field.data( 'tm-prev-value', field.val() );
				// It is required than when you update the product quantity
				// to recalculate the option price to accommodate price types
				// that depend on quantity
				currentCart.trigger( {
					type: 'tm-epo-update',
					norules: 2
				} );
			} )
			.data( 'tm-prev-value', qtyElement.val() );

		// Product element variable product support
		epoHolder
			.find( '.cpf-type-product .tc-epo-field-product' )
			.off( 'change.cpfproduct' )
			.on( 'change.cpfproduct', function( e, data ) {
				var $this = $( this );
				var value;
				var type;
				var selected;
				var variableProductContainers;
				var thisVariableProductContainer;
				var productContainerWraps = $this.closest( '.cpf_hide_element' ).find( '.tc-epo-element-product-container-wrap' );
				var thisProductContainerWrap = $this.closest( '.tmcp-field-wrap' ).find( '.tc-epo-element-product-container-wrap' );
				var hasProductContainerWrap = thisProductContainerWrap.length > 0;
				var postData;
				var skip = false;
				var isTrigger = 1000;
				var qtyalt;
				var associatedSetter = $this;
				var productPrice;
				var originalProductPrice;
				var elementContainer = $this.closest( '.cpf_hide_element' );

				if ( data && data.forced === 2 ) {
					return;
				}

				if ( e.isTrigger !== undefined && $this.data( 'triggeredonce' ) && ! ( data && data.forced ) ) {
					return;
				}

				$this.data( 'triggeredonce', 1 );

				if ( ! $this.is( ':checkbox' ) ) {
					isTrigger = e.isTrigger;
				}

				if ( $this.is( ':checkbox' ) ) {
					qtyalt = productContainerWraps.find( tcAPI.associateQtySelector );
					if ( $this.is( ':checked' ) ) {
						productContainerWraps.addClass( 'tc-active-product' );
						if ( qtyalt.val() === '0' ) {
							if ( qtyalt.attr( 'min' ) === '0' ) {
								qtyalt.val( 1 ).trigger( 'change' );
							} else {
								qtyalt.val( qtyalt.attr( 'min' ) ).trigger( 'change' );
							}
						}
					} else {
						productContainerWraps.removeClass( 'tc-active-product' );
						qtyalt.val( 0 );
						qtyalt.closest( '.tm-quantity-alt' ).removeClass( 'tm-hidden' );
						qtyalt.closest( '.tm-quantity-alt' ).find( '.single_add_to_cart_product' ).trigger( 'cpfqtybutton' );
						productContainerWraps.find( tcAPI.associatedEpoCart ).trigger( 'tm-epo-update' );
					}

					value = $this.val();
					type = $this.attr( 'data-type' );
					elementContainer.find( '.tc-epo-element-product-li-container' ).removeClass( 'tm-hidden' );
				} else if ( $this.is( ':radio' ) ) {
					if ( ! $this.is( ':checked' ) ) {
						if ( hasProductContainerWrap ) {
							thisProductContainerWrap.addClass( 'tm-hidden' );
						}
						if ( ! ( data && data.forced ) ) {
							return;
						}
						skip = true;
					} else if ( hasProductContainerWrap ) {
						productContainerWraps.addClass( 'tm-hidden' );
						thisProductContainerWrap.removeClass( 'tm-hidden' );
					}
					if ( ! skip ) {
						value = $this.val();
						type = $this.attr( 'data-type' );
						elementContainer.find( '.tc-epo-element-product-li-container' ).removeClass( 'tm-hidden' );
					}
				} else if ( $this.is( 'select' ) ) {
					selected = $this.children( ':selected' );
					associatedSetter = selected;
					value = $this.val();
					type = selected.attr( 'data-type' );
					elementContainer.find( '.tc-epo-element-product-li-container' ).removeClass( 'tm-hidden' );
				} else {
					return;
				}

				variableProductContainers = elementContainer.find( '.tc-epo-element-product-container' );
				thisVariableProductContainer = variableProductContainers.filter( '[data-product_id="' + value + '"]' );

				if ( ! value ) {
					variableProductContainers.addClass( 'tm-hidden' );
					productPrice = $.epoAPI.util.parseJSON( $this.attr( 'data-rules' ) );
					originalProductPrice = $.epoAPI.util.parseJSON( $this.attr( 'data-original-rules' ) );
					associatedSetter.data( 'associated_price_set', 1 );
					associatedSetter.data( 'price_set', 1 );
					associatedSetter.data( 'raw_price', productPrice[ 0 ] );
					associatedSetter.data( 'raw_original_price', originalProductPrice[ 0 ] );
					associatedSetter.data( 'price', productPrice[ 0 ] );
					associatedSetter.data( 'original_price', originalProductPrice[ 0 ] );
					tm_force_update_price(
						associatedSetter
							.closest( '.tmcp-field-wrap' )
							.find( '.tc-price' )
							.not( tcAPI.associatedEpoSelector + ' .tc-price' ),
						productPrice[ 0 ],
						tm_set_price_totals( productPrice[ 0 ] ),
						originalProductPrice[ 0 ],
						tm_set_price_totals( originalProductPrice[ 0 ] )
					);

					return;
				}

				if ( thisVariableProductContainer.length === 0 ) {
					postData = {
						action: 'wc_epo_get_associated_product_html',
						product_id: value,
						mode: elementContainer.attr( 'data-mode' ),
						layout_mode: elementContainer.attr( 'data-product-layout-mode' ),
						uniqid: elementContainer.attr( 'data-uniqid' ),
						name: $this.attr( 'name' ),
						quantity_min: elementContainer.attr( 'data-quantity-min' ),
						quantity_max: elementContainer.attr( 'data-quantity-max' ),
						priced_individually: elementContainer.attr( 'data-priced-individually' ),
						discount: elementContainer.attr( 'data-discount' ),
						discount_type: elementContainer.attr( 'data-discount-type' ),
						show_image: elementContainer.attr( 'data-show-image' ),
						show_title: elementContainer.attr( 'data-show-title' ),
						show_price: elementContainer.attr( 'data-show-price' ),
						show_description: elementContainer.attr( 'data-show-description' ),
						show_meta: elementContainer.attr( 'data-show-meta' )
					};

					elementContainer.block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );
					$.ajax( {
						url: TMEPOJS.ajax_url,
						type: 'POST',
						data: postData,
						dataType: 'json',
						success: function( response ) {
							if ( response.result === 200 ) {
								thisVariableProductContainer = $( response.html );
								if ( hasProductContainerWrap ) {
									$this.closest( '.tmcp-field-wrap' ).find( '.tc-epo-element-product-container-wrap' ).empty().append( thisVariableProductContainer );
								} else {
									elementContainer.find( '.tc-epo-element-product-container-wrap' ).append( thisVariableProductContainer );
								}
								show_product_html( epoObject, main_product, thisVariableProductContainer, type, $this, currentCart, variableProductContainers, isTrigger );
							}
						},
						complete: function() {
							elementContainer.unblock();
						}
					} );
				} else {
					show_product_html( epoObject, main_product, thisVariableProductContainer, type, $this, currentCart, variableProductContainers, isTrigger );
					thisVariableProductContainer.find( tcAPI.associateQtySelector ).trigger( 'change' );
				}
			} );

		$( document )
			.off( 'click.cpfqtybutton cpfqtybutton', '.cpf-type-product .single_add_to_cart_product' )
			.on( 'click.cpfqtybutton cpfqtybutton', '.cpf-type-product .single_add_to_cart_product', function() {
				var $this = $( this );
				var qtyalt = $this.closest( '.tm-quantity-alt' ).find( tcAPI.associateQtySelector );
				var isAdd = $this.is( '.alt' );
				var productContainerWraps = $this.closest( '.cpf_hide_element' ).find( '.tc-epo-element-product-container-wrap' );

				if ( $this.data( 'inittriggeredonce' ) ) {
					if ( isAdd ) {
						productContainerWraps.addClass( 'tc-active-product' );
						if ( qtyalt.val() === '0' ) {
							if ( qtyalt.attr( 'min' ) === '0' ) {
								qtyalt.val( 1 ).trigger( 'change' );
							} else {
								qtyalt.val( qtyalt.attr( 'min' ) ).trigger( 'change' );
							}
						}
						$this.removeClass( 'alt' ).text( $this.attr( 'data-remove' ) );
					} else {
						productContainerWraps.removeClass( 'tc-active-product' );
						qtyalt.val( 0 ).trigger( 'change' );
						qtyalt.closest( '.tm-quantity-alt' ).removeClass( 'tm-hidden' );
						$this.addClass( 'alt' ).text( $this.attr( 'data-add' ) );
					}
				}

				$this.data( 'inittriggeredonce', 1 );

				productContainerWraps.find( tcAPI.associatedEpoCart ).trigger( 'tm-epo-update' );
			} );

		$( document )
			.off( 'change.cpfqtyalt', '.cpf-type-product ' + tcAPI.associateQtySelector )
			.on( 'change.cpfqtyalt', '.cpf-type-product ' + tcAPI.associateQtySelector, function() {
				var $this = $( this );
				var epoField = $this.closest( '.cpf_hide_element' ).find( '.tm-epo-field' ).not( '.tc-epo-element-product-li-container .tm-epo-field' );
				var addButton = $this.closest( '.tm-quantity-alt' ).find( '.single_add_to_cart_product' );
				var checked = epoField.filter( ':checked' );
				var qty;
				var qtyMin;
				var qtyMax;

				qtyMin = $.epoAPI.math.toInt( $this.attr( 'min' ) );
				qtyMax = $.epoAPI.math.toInt( $this.attr( 'max' ) );

				if ( epoField.is( ':checkbox' ) ) {
					if ( checked.length === 0 && $this.val() !== '0' ) {
						epoField.prop( 'checked', true ).trigger( 'change' );
						checked = epoField.filter( ':checked' );
					} else if ( $this.val() === '0' && checked.length ) {
						epoField.prop( 'checked', false ).trigger( 'change' );
					}
				}

				if ( epoField.is( ':radio' ) || epoField.is( ':checkbox' ) ) {
					if ( checked.length === 0 ) {
						return;
					}
					qty = checked.closest( '.tmcp-field-wrap' ).find( 'input.tm-qty' );
				} else {
					qty = epoField.closest( '.tmcp-field-wrap' ).find( 'input.tm-qty' );
				}

				if ( qty.length === 0 ) {
					return;
				}

				if ( qtyMin ) {
					qty.attr( 'min', qtyMin );
				}
				if ( qtyMax ) {
					qty.attr( 'max', qtyMax );
				}

				$this.closest( '.tc-epo-element-product-container' ).find( tcAPI.associatedEpoCart ).trigger( 'tm-epo-update' );
				qty.val( $this.val() );
				epoField.trigger( 'change.cpfproduct', { forced: 2 } );
				if ( addButton.data( 'inittriggeredonce' ) ) {
					if ( $this.val() === '0' ) {
						addButton.removeClass( 'alt' );
					} else {
						addButton.addClass( 'alt' );
					}
				}
				addButton.trigger( 'cpfqtybutton' );
			} );

		// Global custom update event
		currentCart.off( 'tm-epo-update' ).on( 'tm-epo-update', function( event ) {
			var cart = $( this );
			var bundleid;
			var productPrice = false;
			var rawProductPrice = 0;
			var total = 0;
			var original_total = 0;
			var showTotal = false;
			var cartQty;
			var elementQty = 1;
			var priceOverrideMode;
			var perProductPricing = true;
			var floatingBoxData = [];
			var currentVariation;
			var cart_fee_options_total = 0;
			var cart_fee_options_original_total = 0;
			var _total;
			var _original_total;
			var late_total_price;
			var tc_totals_ob = {};
			var formatted_options_total;
			var formatted_options_original_total;
			var formatted_fees_total;
			var formatted_fees_original_total;
			var formatted_final_total;
			var formatted_final_original_total;
			var extraFee = 0;
			var product_total_price;
			var product_total_original_price;
			var calculateFinalProductPrice;
			var total_plus_fee;
			var original_total_plus_fee;
			var product_total_price_without_options;
			var formatted_unit_price;
			var formatted_unit_original_price;
			var html;
			var show_options_total = false;
			var show_fees_total = false;
			var formatted_extra_fee = '';
			var show_extra_fee = false;
			var show_final_total = false;
			var hide_native_price;
			var update_native_html;
			var _fprice;
			var _f_regular_price;
			var customerPriceFormat;
			var currentEpoObject;
			var raw_total;
			var raw__total;
			var raw_original_total;
			var raw__original_total;
			var raw_cart_fee_options_total;
			var raw_cart_fee_options_original_total;
			var raw_total_plus_fee;
			var raw_original_total_plus_fee;
			var raw_product_total_price_without_options;
			var raw_product_total_price;
			var raw_product_total_original_price;
			var fetch;
			var customer_price_format_wrap_start = '';
			var customer_price_format_wrap_end = '';
			var associatedSetter;
			var associatedPrice;
			var associatedRawPrice;
			var associatedOriginalPrice;
			var associatedRawOriginalPrice;
			var associatedFormattedPrice;
			var associatedFormattedOriginalPrice;
			var nativeProductPriceSelector;
			var elementsLength;

			if ( event.epoObject ) {
				currentEpoObject = $.extend( true, {}, event.epoObject );
			} else {
				currentEpoObject = $.extend( true, {}, epoObject );
			}

			if ( ! currentEpoObject ) {
				return;
			}

			if ( currentEpoObject.noEpoUpdate ) {
				return;
			}

			bundleid = $.epoAPI.applyFilter( 'tc_get_bundleid', cart.attr( 'data-product_id' ), cart );
			priceOverrideMode = totalsHolder.attr( 'data-price-override' );
			cartQty = getCurrentQty( cart );
			currentVariation = getCurrentVariation( cart );

			if ( currentEpoObject.associated_connect && currentEpoObject.associated_connect.length === 1 ) {
				cartQty = parseFloat(
					currentEpoObject.main_product
						.find( tcAPI.associateQtySelector )
						.not( tcAPI.associatedEpoSelector + ' ' + tcAPI.qtySelector )
						.last()
						.val()
				);
			}

			event.stopImmediatePropagation();

			productPrice = $.epoAPI.applyFilter( 'tcGetCurrentProductPrice', tm_calculate_product_price( totalsHolder, true ), currentCart, totalsHolder );

			rawProductPrice = productPrice;

			productPrice = $.epoAPI.applyFilter( 'tcCalculateCurrentProductPrice', productPrice, {
				epo: currentEpoObject,
				alternativeCart: alternativeCart,
				cart: cart,
				main_product: main_product
			} );

			if ( ! Number.isFinite( cartQty ) ) {
				if ( totalsHolder.attr( 'data-is-sold-individually' ) || getQtyElement( cart ).length === 0 ) {
					cartQty = 1;
				}
			}

			// needed for inital math calculation
			tc_totals_ob = {
				qty: cartQty,
				product_price: productPrice
			};
			totalsHolder.data( 'tc_totals_ob', tc_totals_ob );

			if ( ! event.norules ) {
				tm_epo_rules( currentEpoObject, cart );
			} else if ( event.norules ) {
				if ( event.norules === 1 ) {
					tm_element_epo_rules( currentEpoObject, event.element );
				}
				$.tcepo.lateFieldsPrices[ epoEventId ] = [];

				epoHolder
					.find( '.tm-epo-late-field' )
					.toArray()
					.forEach( function( setter ) {
						setter = $( setter );
						setter.data( 'price', 0 );
						$.tcepo.lateFieldsPrices[ epoEventId ].push( {
							setter: setter,
							price: setter.data( 'tm-price-for-late' ),
							original_price: setter.data( 'tm-original-price-for-late' ),
							bundleid: bundleid,
							pricetype: get_price_type( currentEpoObject, setter )
						} );
					} );
			}

			if ( currentEpoObject.associated_connect && currentEpoObject.associated_connect.length === 1 ) {
				associatedSetter = currentEpoObject.associated_connect;
				if ( currentEpoObject.associated_connect.is( 'select' ) ) {
					associatedSetter = currentEpoObject.associated_connect.find( 'option:selected' );
				}
			}

			// No reason to continue if the product price is invalid
			if ( productPrice === false ) {
				totalsHolder.empty();
				if ( currentEpoObject.associated_connect && currentEpoObject.associated_connect.length === 1 ) {
					tm_force_update_price(
						associatedSetter
							.closest( '.tmcp-field-wrap' )
							.find( '.tc-price' )
							.not( tcAPI.associatedEpoSelector + ' .tc-price' ),
						0,
						'',
						0,
						''
					);
				}
				return;
			}

			elementQty = $.epoAPI.applyFilter( 'tcAlterElementQty', elementQty, {
				epo: currentEpoObject,
				alternativeCart: alternativeCart,
				currentCart: currentCart,
				main_product: main_product
			} );

			if ( currentCart.data( 'per_product_pricing' ) !== undefined ) {
				perProductPricing = currentCart.data( 'per_product_pricing' );
			}

			perProductPricing = $.epoAPI.applyFilter( 'tcCalculatePerProductPricing', perProductPricing, {
				epo: currentEpoObject,
				alternativeCart: alternativeCart,
				cart: cart,
				main_product: main_product
			} );

			if ( main_epo_inside_form && TMEPOJS.tm_epo_totals_box_placement === 'woocommerce_before_add_to_cart_button' ) {
				if ( ( this_product_type === 'variable' || this_product_type === 'variable-subscription' ) && ! totalsHolder.data( 'moved_inside' ) ) {
					totalsHolder.data( 'moved_inside', 1 );
				}
			}

			jWindow.trigger( 'tcEpoBeforeOptionPriceCalculation', {
				epo: currentEpoObject,
				alternativeCart: currentEpoObject,
				this_product_type: this_product_type,
				cart: cart,
				totalsHolder: totalsHolder
			} );

			fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tmcp-field', total, original_total, floatingBoxData, showTotal );
			total = fetch.total;
			original_total = fetch.original_total;
			floatingBoxData = fetch.floatingBoxData;
			showTotal = fetch.showTotal;
			elementsLength = fetch.elementsLength;

			totalsHolder.data( 'tm-floating-box-data', floatingBoxData );

			fetch = fetchOptionPrices( currentEpoObject, epoHolder, '.tmcp-fee-field', cart_fee_options_total, cart_fee_options_original_total, floatingBoxData, showTotal );
			cart_fee_options_total = fetch.total;
			cart_fee_options_original_total = fetch.original_total;
			floatingBoxData = fetch.floatingBoxData;
			showTotal = fetch.showTotal;
			elementsLength = elementsLength + fetch.elementsLength;

			jWindow.trigger( 'tcEpoAfterOptionPriceCalculation', {
				epo: currentEpoObject,
				alternativeCart: currentEpoObject,
				this_product_type: this_product_type,
				cart: cart,
				totalsHolder: totalsHolder
			} );

			$.tcepo.oneOptionIsSelected[ epoEventId ] = showTotal;
			tm_show_hide_add_to_cart_button( main_product, elementsLength, $.tcepo.oneOptionIsSelected[ epoEventId ] );

			showTotal = $.epoAPI.applyFilter( 'tcFinalTotalsBoxVisibility', showTotal, {
				epo: currentEpoObject,
				alternativeCart: alternativeCart,
				cart: cart,
				main_product: main_product,
				totalsHolder: totalsHolder,
				this_epo_totals_container: this_epo_totals_container
			} );

			if ( cart_fee_options_total > 0 ) {
				showTotal = true;
			}

			if ( alternativeCart && ! perProductPricing ) {
				showTotal = false;
			}

			if (
				finalTotalBoxMode === 'pxq' ||
				finalTotalBoxMode === 'hide' ||
				finalTotalBoxMode === 'normal' ||
				finalTotalBoxMode === 'options' ||
				finalTotalBoxMode === 'final' ||
				finalTotalBoxMode === 'hideoptionsifzero' ||
				finalTotalBoxMode === 'optionsiftotalnotzero'
			) {
				showTotal = true;
			}

			if ( cartQty > 1 ) {
				showTotal = true;
			}
			if ( ( this_product_type === 'variable' || this_product_type === 'variable-subscription' ) && ! $.epoAPI.math.toFloat( currentVariation ) ) {
				showTotal = false;
			}

			// Original price + options price type requires this here.
			_total = total;
			_original_total = original_total;

			late_total_price = add_late_fields_prices( currentEpoObject, parseFloat( productPrice ), parseFloat( _total ), parseFloat( _original_total ), bundleid, totalsHolder );

			if ( finalTotalBoxMode === 'disable' ) {
				showTotal = false;
			}
			if ( finalTotalBoxMode === 'disable_change' || TMEPOJS.tm_epo_change_variation_price === 'yes' || TMEPOJS.tm_epo_change_original_price === 'yes' ) {
				showTotal = true;
			}

			if ( currentEpoObject.is_associated && ! perProductPricing ) {
				showTotal = false;
			}

			if ( TMEPOJS.tm_epo_total_price_as_unit_price === 'yes' ) {
				cartQty = 1;
			}

			product_total_price = parseFloat( productPrice * cartQty );

			if ( TMEPOJS.extraFee ) {
				extraFee = parseFloat( TMEPOJS.extraFee );
				if ( ! Number.isFinite( extraFee ) ) {
					extraFee = 0;
				}
			}

			calculateFinalProductPrice = $.epoAPI.applyFilter( 'tcCalculateFinalProductPrice', false, {
				alternativeCart: alternativeCart,
				product_price: productPrice,
				product_total_price: product_total_price,
				v_product_price: rawProductPrice,
				tm_set_tax_price: tm_set_tax_price,
				main_product: main_product,
				totalsHolder: totalsHolder,
				cartQty: cartQty
			} );

			if (
				calculateFinalProductPrice !== false &&
				typeof calculateFinalProductPrice === 'object' &&
				Object.prototype.hasOwnProperty.call( calculateFinalProductPrice, 'productPrice' ) &&
				Object.prototype.hasOwnProperty.call( calculateFinalProductPrice, 'productTotalPrice' )
			) {
				productPrice = calculateFinalProductPrice.productPrice;
				product_total_price = calculateFinalProductPrice.productTotalPrice;
			}

			_total = _total + late_total_price[ 0 ];
			_total = $.epoAPI.applyFilter( 'tc_adjust_options_price_per_unit', _total, product_total_price );
			total = parseFloat( _total * cartQty * elementQty );
			total = $.epoAPI.applyFilter( 'tc_adjust_options_total_price', total, cartQty, elementQty, _total );

			_original_total = _original_total + late_total_price[ 1 ];
			_original_total = $.epoAPI.applyFilter( 'tc_adjust_options_price_per_unit', _original_total, product_total_price );
			original_total = parseFloat( _original_total * cartQty * elementQty );
			original_total = $.epoAPI.applyFilter( 'tc_adjust_options_total_price', original_total, cartQty, elementQty, _original_total );

			if ( priceOverrideMode === '1' && parseFloat( total ) > 0 ) {
				productPrice = 0;
				rawProductPrice = 0;
				product_total_price = parseFloat( productPrice * cartQty );
			}

			product_total_price = $.epoAPI.applyFilter( 'tc_adjust_product_total_price_without_options', product_total_price );

			total = $.epoAPI.applyFilter( 'tcAdjustTotal', total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				cart: cart,
				main_product: main_product
			} );
			original_total = $.epoAPI.applyFilter( 'tcAdjustTotal', original_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				cart: cart,
				main_product: main_product
			} );

			total = parseFloat( $.epoAPI.applyFilter( 'tc_adjust_total', total, totalsHolder ) );
			cart_fee_options_total = parseFloat( $.epoAPI.applyFilter( 'tc_adjust_totals_fee', cart_fee_options_total, totalsHolder ) );
			original_total = parseFloat( $.epoAPI.applyFilter( 'tc_adjust_total', original_total, totalsHolder ) );
			cart_fee_options_original_total = parseFloat( $.epoAPI.applyFilter( 'tc_adjust_totals_fee', cart_fee_options_original_total, totalsHolder ) );

			total_plus_fee = total + cart_fee_options_total;
			original_total_plus_fee = original_total + cart_fee_options_original_total;

			raw_total = total;
			raw__total = _total;
			raw_cart_fee_options_total = cart_fee_options_total;
			raw_total_plus_fee = total_plus_fee;

			raw_original_total = original_total;
			raw__original_total = _original_total;
			raw_cart_fee_options_original_total = cart_fee_options_original_total;
			raw_original_total_plus_fee = original_total_plus_fee;

			raw_product_total_price_without_options = product_total_price;
			raw_product_total_price = parseFloat( product_total_price + total_plus_fee + extraFee );
			raw_product_total_original_price = parseFloat( product_total_price + original_total_plus_fee + extraFee );

			productPrice = tm_set_tax_price( productPrice, totalsHolder );
			product_total_price = tm_set_tax_price( product_total_price, totalsHolder );
			total = tm_set_tax_price( total, totalsHolder );
			_total = tm_set_tax_price( _total, totalsHolder );
			cart_fee_options_total = tm_set_tax_price( cart_fee_options_total, totalsHolder );
			total_plus_fee = tm_set_tax_price( total_plus_fee, totalsHolder );
			original_total = tm_set_tax_price( original_total, totalsHolder );
			_original_total = tm_set_tax_price( _original_total, totalsHolder );
			cart_fee_options_original_total = tm_set_tax_price( cart_fee_options_original_total, totalsHolder );
			original_total_plus_fee = tm_set_tax_price( original_total_plus_fee, totalsHolder );
			extraFee = tm_set_tax_price( extraFee, totalsHolder );

			formatted_options_total = tm_set_price_totals( total, totalsHolder, true, true );
			formatted_fees_total = tm_set_price_totals( cart_fee_options_total, totalsHolder, true, true );
			formatted_options_original_total = tm_set_price_totals( original_total, totalsHolder, true, true );
			formatted_fees_original_total = tm_set_price_totals( cart_fee_options_original_total, totalsHolder, true, true );

			product_total_price_without_options = product_total_price;
			product_total_price = parseFloat( product_total_price + total_plus_fee + extraFee );
			product_total_price = $.epoAPI.applyFilter( 'tc_adjust_product_total_price', product_total_price, total_plus_fee, extraFee, total, cart_fee_options_total, totalsHolder );
			product_total_original_price = parseFloat( product_total_price + original_total_plus_fee + extraFee );
			product_total_original_price = $.epoAPI.applyFilter( 'tc_adjust_product_total_price', product_total_price, original_total_plus_fee, extraFee, original_total, cart_fee_options_original_total, totalsHolder );

			formatted_final_total = tm_set_price_totals( product_total_price, totalsHolder, true, true );
			formatted_final_original_total = tm_set_price_totals( product_total_original_price, totalsHolder, true, true );
			formatted_unit_price = tm_set_price_totals( parseFloat( productPrice + parseFloat( _total ) ), totalsHolder, true, true );
			formatted_unit_original_price = tm_set_price_totals( parseFloat( productPrice + parseFloat( _original_total ) ), totalsHolder, true, true );
			if ( TMEPOJS.tm_epo_fees_on_unit_price === 'yes' ) {
				formatted_unit_price = tm_set_price_totals( parseFloat( productPrice + parseFloat( _total ) + parseFloat( parseFloat( cart_fee_options_total ) / cartQty ) ), totalsHolder, true, true );
				formatted_unit_original_price = tm_set_price_totals( parseFloat( productPrice + parseFloat( _original_total ) + parseFloat( parseFloat( cart_fee_options_original_total ) / cartQty ) ), totalsHolder, true, true );
			}

			if ( finalTotalBoxMode !== 'pxq' && finalTotalBoxMode !== 'final' && finalTotalBoxMode !== 'hide' && ! ( total_plus_fee === 0 && finalTotalBoxMode === 'hideoptionsifzero' ) ) {
				if ( ! ( total === 0 && finalTotalBoxMode === 'hideoptionsifzero' ) || finalTotalBoxMode === 'options' ) {
					show_options_total = true;
				}
				if ( cart_fee_options_total !== 0 ) {
					show_fees_total = true;
				}
			}
			if ( extraFee ) {
				show_extra_fee = true;
				formatted_extra_fee = tm_set_price_totals( extraFee, totalsHolder, true, true );
			}
			if ( formatted_final_total && finalTotalBoxMode !== 'options' && finalTotalBoxMode !== 'optionsiftotalnotzero' ) {
				show_final_total = true;
			}

			// Backwards compatibility
			formatted_unit_price = $.epoAPI.applyFilter( 'tc_adjust_formatted_unit_price', formatted_unit_price, productPrice, _total, cart_fee_options_total, cartQty );
			formatted_options_total = $.epoAPI.applyFilter( 'tc_adjust_formatted_options_total', formatted_options_total, total, _total, cartQty );
			formatted_fees_total = $.epoAPI.applyFilter( 'tc_adjust_formatted_fees_total', formatted_fees_total, cart_fee_options_total );
			formatted_final_total = $.epoAPI.applyFilter( 'tc_adjust_formatted_final_total', formatted_final_total, product_total_price, product_total_price_without_options, total_plus_fee, extraFee, cartQty );

			formatted_unit_price = $.epoAPI.applyFilter( 'tcAdjustFormattedUnitPrice', formatted_unit_price, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				productPrice: productPrice,
				_total: _total,
				total_plcart_fee_options_totals_fee: cart_fee_options_total,
				cartQty: cartQty
			} );
			formatted_unit_original_price = $.epoAPI.applyFilter( 'tcAdjustFormattedUnitOriginalPrice', formatted_unit_original_price, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				productPrice: productPrice,
				_original_total: _original_total,
				total_plcart_fee_options_totals_fee: cart_fee_options_total,
				cartQty: cartQty
			} );

			formatted_options_total = $.epoAPI.applyFilter( 'tcAdjustFormattedOptionsTotal', formatted_options_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				total: total,
				_total: _total,
				total_plus_fee: total_plus_fee,
				extraFee: extraFee,
				cartQty: cartQty
			} );
			formatted_options_original_total = $.epoAPI.applyFilter( 'tcAdjustFormattedOptionsOriginalTotal', formatted_options_original_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				original_total: original_total,
				_original_total: _original_total,
				original_total_plus_fee: original_total_plus_fee,
				extraFee: extraFee,
				cartQty: cartQty
			} );

			formatted_fees_total = $.epoAPI.applyFilter( 'tcAdjustFormattedFeesTotal', formatted_fees_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				cart_fee_options_total: cart_fee_options_total,
				extraFee: extraFee,
				cartQty: cartQty
			} );
			formatted_fees_original_total = $.epoAPI.applyFilter( 'tcAdjustFormattedFeesOriginalTotal', formatted_fees_original_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				cart_fee_options_original_total: cart_fee_options_original_total,
				extraFee: extraFee,
				cartQty: cartQty
			} );

			formatted_final_total = $.epoAPI.applyFilter( 'tcAdjustFormattedFinalTotal', formatted_final_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				product_total_price: product_total_price,
				product_total_price_without_options: product_total_price_without_options,
				total_plus_fee: total_plus_fee,
				extraFee: extraFee,
				cartQty: cartQty
			} );
			formatted_final_original_total = $.epoAPI.applyFilter( 'tcAdjustFormattedFinalOriginalTotal', formatted_final_original_total, {
				epo: currentEpoObject,
				totalsHolder: totalsHolder,
				event: event,
				product_total_original_price: product_total_original_price,
				product_total_price_without_options: product_total_price_without_options,
				original_total_plus_fee: original_total_plus_fee,
				extraFee: extraFee,
				cartQty: cartQty
			} );

			tc_totals_ob = {
				qty: cartQty,
				product_price: productPrice,
				raw_product_price: rawProductPrice,
				late_total_prices: late_total_price,
				late_total_price: tm_set_tax_price( late_total_price[ 0 ], totalsHolder ),
				late_total_original_price: tm_set_tax_price( late_total_price[ 1 ], totalsHolder ),

				raw_options_price_per_unit: raw__total,
				raw_options_total_price: raw_total,
				raw_cart_fee_options_total_price: raw_cart_fee_options_total,
				raw_total_plus_fee: raw_total_plus_fee,

				raw_options_original_price_per_unit: raw__original_total,
				raw_options_original_total_price: raw_original_total,
				raw_cart_fee_options_original_total_price: raw_cart_fee_options_original_total,
				raw_original_total_plus_fee: raw_original_total_plus_fee,

				raw_product_total_price: raw_product_total_price,
				raw_product_total_original_price: raw_product_total_original_price,
				raw_product_total_price_without_options: raw_product_total_price_without_options,

				options_price_per_unit: _total,
				options_total_price: total,
				cart_fee_options_total_price: cart_fee_options_total,
				total_plus_fee: total_plus_fee,

				options_original_price_per_unit: _original_total,
				options_original_total_price: original_total,
				cart_fee_options_total_original_price: cart_fee_options_original_total,
				original_total_plus_fee: original_total_plus_fee,

				product_total_price: product_total_price,
				product_total_original_price: product_total_original_price,
				product_total_price_without_options: product_total_price_without_options,

				formatted_unit_price: formatted_unit_price,
				formatted_options_total: formatted_options_total,
				formatted_fees_total: formatted_fees_total,
				formatted_final_total: formatted_final_total,

				formatted_unit_original_price: formatted_unit_original_price,
				formatted_options_original_total: formatted_options_original_total,
				formatted_fees_original_total: formatted_fees_original_total,
				formatted_final_original_total: formatted_final_original_total,

				formatted_extra_fee: formatted_extra_fee,

				show_options_total: show_options_total,
				show_fees_total: show_fees_total,
				show_extra_fee: show_extra_fee,
				show_final_total: show_final_total,

				unit_price: TMEPOJS.i18n_unit_price,
				show_unit_price: TMEPOJS.tm_epo_show_unit_price === 'yes',
				options_total: TMEPOJS.i18n_options_total,
				fees_total: TMEPOJS.i18n_fees_total,
				extra_fee: TMEPOJS.i18n_extra_fee,
				final_total: TMEPOJS.i18n_final_total
			};

			tc_totals_ob = $.epoAPI.applyFilter( 'tc_adjust_tc_totals_ob', tc_totals_ob, {
				epo_object: currentEpoObject,
				showTotal: showTotal,
				epoHolder: epoHolder,
				totalsHolder: totalsHolder,
				tm_set_price: tm_set_price,
				tm_set_price_totals: tm_set_price_totals,
				product_total_price: product_total_price,
				product_price: productPrice,
				qty: cartQty
			} );

			currentEpoObject.tc_totals_ob = tc_totals_ob;

			if ( tc_totals_ob.showTotal !== undefined ) {
				showTotal = tc_totals_ob.showTotal;
			}

			html = $.epoAPI.template.html( tcAPI.templateEngine.tc_final_totals, tc_totals_ob );

			totalsHolder.data( 'tm-html', html );
			totalsHolder.data( 'tc_totals_ob', tc_totals_ob );

			if ( currentEpoObject.associated_connect && currentEpoObject.associated_connect.length === 1 ) {
				if ( currentEpoObject.associated_connect.attr( 'data-no-price' ) === '1' ) {
					associatedPrice = 0;
					associatedRawPrice = 0;
					associatedOriginalPrice = 0;
					associatedRawOriginalPrice = 0;
					associatedFormattedPrice = '';
					associatedFormattedOriginalPrice = '';
					tm_force_update_price(
						associatedSetter
							.closest( '.tmcp-field-wrap' )
							.find( '.tc-price' )
							.not( tcAPI.associatedEpoSelector + ' .tc-price' ),
						associatedPrice,
						associatedFormattedPrice,
						associatedOriginalPrice,
						associatedFormattedOriginalPrice
					);
				} else {
					associatedPrice = tc_totals_ob.product_price + tc_totals_ob.options_price_per_unit + ( tc_totals_ob.cart_fee_options_total_price / tc_totals_ob.qty );
					associatedRawPrice = tc_totals_ob.raw_product_price + tc_totals_ob.raw_options_price_per_unit + ( tc_totals_ob.raw_cart_fee_options_total_price / tc_totals_ob.qty );
					associatedOriginalPrice = tc_totals_ob.product_price + tc_totals_ob.options_original_price_per_unit + ( tc_totals_ob.cart_fee_options_total_original_price / tc_totals_ob.qty );
					associatedRawOriginalPrice = tc_totals_ob.raw_product_price + tc_totals_ob.raw_options_original_price_per_unit + ( tc_totals_ob.raw_cart_fee_options_original_total_price / tc_totals_ob.qty );

					associatedFormattedPrice = tm_set_price( associatedPrice, currentEpoObject.this_epo_totals_containe, false, false, associatedSetter );
					associatedFormattedOriginalPrice = tm_set_price( associatedOriginalPrice, currentEpoObject.this_epo_totals_containe, false, false, associatedSetter );

					tm_force_update_price(
						associatedSetter
							.closest( '.tmcp-field-wrap' )
							.find( '.tc-price' )
							.not( tcAPI.associatedEpoSelector + ' .tc-price' ),
						associatedPrice,
						associatedFormattedPrice,
						associatedOriginalPrice,
						associatedFormattedOriginalPrice
					);

					currentEpoObject.associated_connect.data( 'tm-quantity', tc_totals_ob.qty );

					if ( currentEpoObject.associated_connect.data( 'tm-quantity' ) ) {
						associatedPrice = associatedPrice * parseFloat( currentEpoObject.associated_connect.data( 'tm-quantity' ) );
						associatedRawPrice = associatedRawPrice * parseFloat( currentEpoObject.associated_connect.data( 'tm-quantity' ) );
						associatedOriginalPrice = associatedOriginalPrice * parseFloat( currentEpoObject.associated_connect.data( 'tm-quantity' ) );
						associatedRawOriginalPrice = associatedRawOriginalPrice * parseFloat( currentEpoObject.associated_connect.data( 'tm-quantity' ) );
					}

					associatedFormattedPrice = tm_set_price( associatedPrice, currentEpoObject.this_epo_totals_containe, false, false, associatedSetter );
					associatedFormattedOriginalPrice = tm_set_price( associatedOriginalPrice, currentEpoObject.this_epo_totals_containe, false, false, associatedSetter );
				}

				currentEpoObject.associated_connect.data( 'price_set', 1 );
				associatedSetter.data( 'associated_price_set', 1 );
				associatedSetter.data( 'price_set', 1 );
				associatedSetter.data( 'raw_price', associatedRawPrice );
				associatedSetter.data( 'raw_original_price', associatedRawOriginalPrice );
				associatedSetter.data( 'price', tm_set_tax_price( associatedPrice, currentEpoObject.this_epo_totals_containe, associatedSetter ) );
				associatedSetter.data( 'original_price', tm_set_tax_price( associatedOriginalPrice, currentEpoObject.this_epo_totals_containe, associatedSetter ) );

				currentEpoObject.associated_connect.data( 'price-changed', 1 );

				setTimeout( function() {
					currentEpoObject.mainEpoObject.main_cart.trigger( {
						type: 'tm-epo-update',
						norules: 2
					} );
				}, 20 );
			}

			jWindow.trigger( 'tcEpoAfterCalculateTotals', {
				epo: currentEpoObject,
				alternativeCart: alternativeCart,
				this_product_type: this_product_type,
				cart: cart,
				bundleid: bundleid,
				totalsObject: tc_totals_ob,
				main_product: main_product,
				per_product_pricing: perProductPricing
			} );

			hide_native_price = $.epoAPI.applyFilter( 'hide_native_price', true );

			if ( showTotal && cartQty > 0 ) {
				// hide native prices
				if ( finalTotalBoxMode === 'disable_change' || TMEPOJS.tm_epo_change_variation_price === 'yes' ) {
					if ( hide_native_price === true && finalTotalBoxMode !== 'disable' && finalTotalBoxMode !== 'disable_change' ) {
						tm_get_native_prices_block( cart ).hide();
					} else {
						tm_get_native_prices_block( cart ).show();
					}
				}

				if (
					finalTotalBoxMode === 'disable' ||
					finalTotalBoxMode === 'disable_change' ||
					( ( finalTotalBoxMode === 'hideifoptionsiszero' || finalTotalBoxMode === 'optionsiftotalnotzero' ) && total_plus_fee === 0 ) ||
					finalTotalBoxMode === 'hide'
				) {
					html = '';
					totalsHolder.html( html );
				} else {
					totalsHolder.html( html );

					jWindow.trigger( 'tc-totals-container', {
						epo: tc_totals_ob,
						totals_holder: totalsHolder,
						data: {
							epo_object: currentEpoObject,
							tm_set_price: tm_set_price,
							tm_set_price_totals: tm_set_price_totals,
							product_total_price: product_total_price,
							product_price: productPrice,
							qty: cartQty
						},
						tm_epo_js: TMEPOJS
					} );
				}

				if ( formatted_final_total && product_total_price >= 0 ) {
					update_native_html = tm_get_native_prices_block( cart );
					_fprice = formatPrice( product_total_price );

					if ( priceOverrideMode === '1' && parseFloat( original_total_plus_fee + extraFee ) > 0 ) {
						_f_regular_price = parseFloat( original_total_plus_fee + extraFee );
					} else {
						_f_regular_price = parseFloat( parseFloat( totalsHolder.data( 'regular-price' ) * cartQty ) + original_total_plus_fee + extraFee );
					}

					_f_regular_price = formatPrice( _f_regular_price );

					if ( TMEPOJS.customer_price_format ) {
						customer_price_format_wrap_start = TMEPOJS.customer_price_format_wrap_start;
						customer_price_format_wrap_end = TMEPOJS.customer_price_format_wrap_end;
						customerPriceFormat = TMEPOJS.customer_price_format;
						_fprice = customerPriceFormat.replace( '__PRICE__', _fprice ).replace( '__CODE__', TMEPOJS.current_currency );
						if ( ! totalsHolder.data( 'is-on-sale' ) ) {
							_f_regular_price = customerPriceFormat.replace( '__PRICE__', _f_regular_price ).replace( '__CODE__', TMEPOJS.current_currency );
						}
					}

					_fprice = $.epoAPI.applyFilter( 'tc_adjust_native_price', _fprice, product_total_price );
					_f_regular_price = $.epoAPI.applyFilter( 'tc_adjust_native_regular_price', _f_regular_price, product_total_price );
					_f_regular_price = tm_set_tax_price( _f_regular_price, totalsHolder );

					if ( finalTotalBoxMode === 'disable_change' || TMEPOJS.tm_epo_change_variation_price === 'yes' ) {
						if ( totalsHolder.data( 'is-on-sale' ) ) {
							update_native_html
								.html(
									$.epoAPI.util.decodeHTML(
										$.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_sale_price, {
											price: _f_regular_price,
											sale_price: _fprice,
											customer_price_format_wrap_start: customer_price_format_wrap_start,
											customer_price_format_wrap_end: customer_price_format_wrap_end
										} )
									)
								)
								.show();
						} else {
							update_native_html
								.html(
									$.epoAPI.util.decodeHTML(
										$.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_price, {
											price: _fprice,
											customer_price_format_wrap_start: customer_price_format_wrap_start,
											customer_price_format_wrap_end: customer_price_format_wrap_end
										} )
									)
								)
								.show();
						}
					}

					if ( finalTotalBoxMode === 'disable_change' || TMEPOJS.tm_epo_change_original_price === 'yes' ) {
						if ( currentEpoObject.associated_connect ) {
							nativeProductPriceSelector = currentEpoObject.main_product.find( tcAPI.associatedNativeProductPriceSelector );
						} else {
							nativeProductPriceSelector = $( tcAPI.nativeProductPriceSelector );
						}

						if ( ! alternativeCart || main_product.find( '.cpf-bto-price' ).length === 0 ) {
							if ( nativeProductPriceSelector.data( 'tc-original-html' ) === undefined ) {
								nativeProductPriceSelector.data( 'tc-original-html', nativeProductPriceSelector.html() );
							}
							if ( product_total_price > 0 ) {
								if ( totalsHolder.data( 'is-on-sale' ) && ! ( priceOverrideMode === '1' && parseFloat( original_total_plus_fee + extraFee ) > 0 ) ) {
									nativeProductPriceSelector
										.html(
											$.epoAPI.util.decodeHTML(
												$.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_sale_price, {
													price: _f_regular_price,
													sale_price: _fprice,
													customer_price_format_wrap_start: customer_price_format_wrap_start,
													customer_price_format_wrap_end: customer_price_format_wrap_end
												} )
											)
										)
										.show();
								} else {
									nativeProductPriceSelector
										.html(
											$.epoAPI.util.decodeHTML(
												$.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_price, {
													price: _fprice,
													customer_price_format_wrap_start: customer_price_format_wrap_start,
													customer_price_format_wrap_end: customer_price_format_wrap_end
												} )
											)
										)
										.show();
								}
							} else if ( this_product_type && this_product_type !== 'composite' ) {
								if ( currentEpoObject.associated_connect ) {
									nativeProductPriceSelector.html( $.epoAPI.util.decodeHTML( TMEPOJS.assoc_current_free_text ) );
								} else {
									nativeProductPriceSelector.html( $.epoAPI.util.decodeHTML( TMEPOJS.current_free_text ) );
								}
							}
						}

						jWindow.trigger( 'tcEpoMaybeChangePriceHtml', {
							epo: currentEpoObject,
							alternativeCart: alternativeCart,
							this_product_type: this_product_type,
							cart: cart,
							bundleid: bundleid,
							totalsObject: tc_totals_ob,
							main_product: main_product,
							nativePrice: _fprice
						} );
					}
				}

				if ( alternativeCart ) {
					main_cart.trigger( {
						type: 'tm-epo-update',
						norules: 1
					} );
				} else {
					this_epo_totals_container.data( 'is_active', true );
				}
			} else {
				if ( currentEpoObject.associated_connect ) {
					nativeProductPriceSelector = currentEpoObject.main_product.find( tcAPI.associatedNativeProductPriceSelector );
					nativeProductPriceSelector.html( nativeProductPriceSelector.data( 'tc-original-html' ) );
				}

				tm_get_native_prices_block( cart ).each( function() {
					var $npb = $( this );
					if ( ! $npb.data( 'tm-original-html' ) ) {
						$npb.data( 'tm-original-html', $npb.html() );
					} else {
						$npb.html( $npb.data( 'tm-original-html' ) );
					}
				} );

				if ( rawProductPrice === 0 && TMEPOJS.tm_epo_remove_free_price_label === 'yes' ) {
					tm_get_native_prices_block( cart ).hide();
				} else if ( ( finalTotalBoxMode === 'disable_change' || TMEPOJS.tm_epo_change_variation_price === 'yes' ) && ! ( hide_native_price === true && finalTotalBoxMode !== 'disable' && finalTotalBoxMode !== 'disable_change' ) ) {
					tm_get_native_prices_block( cart ).show();
				}

				totalsHolder.empty();

				if ( alternativeCart ) {
					main_cart.trigger( {
						type: 'tm-epo-update',
						norules: 1
					} );
				}
			}

			main_cart.trigger( 'tm-epo-after-update', {
				container: cartContainer
			} );

			jWindow.trigger( 'tc-epo-after-update', {
				epo: tc_totals_ob,
				totals_holder: totalsHolder,
				data: {
					epo_object: currentEpoObject,
					add_late_fields_prices: add_late_fields_prices,
					tm_set_price: tm_set_price,
					tm_set_price_totals: tm_set_price_totals,
					product_total_price: product_total_price,
					product_price: productPrice,
					qty: cartQty,
					bundleid: bundleid,
					currentCart: currentCart
				},
				tm_epo_js: TMEPOJS
			} );
		} );

		if ( this_product_type === 'variable' || this_product_type === 'variable-subscription' ) {
			epoVariationSection = epoHolder.find( '.tm-epo-variation-section' ).first();

			// Custom variation events
			epoVariationSection
				.find( '.tm-epo-reset-variation' )
				.off( 'click.cpfv' )
				.on( 'click.cpfv', function() {
					var field = $( this );
					var id = $.epoAPI.dom.id( field.attr( 'data-tm-for-variation' ) );
					var section = field.closest( '.cpf-type-variations' );
					var inputs = field.closest( '.cpf_hide_element' ).find( '.tm-epo-variation-element' );
					var lis = field.closest( '.cpf_hide_element' ).find( '.tmcp-field-wrap' );

					inputs.removeAttr( 'checked' ).prop( 'checked', false );
					lis.removeClass( 'tc-active' );
					variationForm
						.find( "[data-attribute_name='attribute_" + id + "']" )
						.val( '' )
						.trigger( 'change' );
					variationForm.find( "[data-attribute_name='attribute_" + id + "']" ).trigger( 'focusin' );

					main_product
						.find( '.cpf-type-variations' )
						.not( section )
						.each( function( i, el ) {
							variationForm.find( "[data-attribute_name='attribute_" + $.epoAPI.dom.id( $( el ).find( '.tm-epo-variation-element' ).first().attr( 'data-tm-for-variation' ) ) + "']" ).trigger( 'focusin' );
						} );
					field.blur();
					variationForm.trigger( 'woocommerce_update_variation_values_tmlogic' );
				} );

			epoVariationSection
				.find( 'input.tm-epo-variation-element,input.tm-epo-variation-element + span' )
				.off( 'mouseup.cpfv' )
				.on( 'mouseup.cpfv', function() {
					var field = $( this );
					var id;

					if ( field.is( 'span' ) ) {
						field = field.prev( 'input' );
					}
					if ( field.attr( 'disabled' ) ) {
						variationForm.find( '.reset_variations' ).trigger( 'click' );
					}
					id = $.epoAPI.dom.id( field.attr( 'data-tm-for-variation' ) );
					variationForm.find( "[data-attribute_name='attribute_" + id + "']" ).trigger( 'focusin' );
				} );

			epoVariationSection
				.off( 'click.' + eventName + '.tmepo', '.reset_variations, .tc-epo-element-variable-reset-variations' )
				.on( 'click.' + eventName + '.tmepo', '.reset_variations, .tc-epo-element-variable-reset-variations', { _epoObject: epoObject }, function( event ) {
					var _nativeProductPriceSelector;
					if ( finalTotalBoxMode === 'disable_change' || TMEPOJS.tm_epo_change_original_price === 'yes' ) {
						if ( ! alternativeCart || main_product.find( '.cpf-bto-price' ).length === 0 ) {
							if ( event.data._epoObject.associated_connect ) {
								_nativeProductPriceSelector = event.data._epoObject.main_product.find( tcAPI.associatedNativeProductPriceSelector );
							} else {
								_nativeProductPriceSelector = $( tcAPI.nativeProductPriceSelector );
							}
							if ( _nativeProductPriceSelector.data( 'tc-original-html' ) ) {
								_nativeProductPriceSelector.html( _nativeProductPriceSelector.data( 'tc-original-html' ) );
							}
						}
					}
					variationForm.find( '.variations .reset_variations, .tc-epo-element-variable-reset-variations' ).first().trigger( 'click' );
				} );

			epoVariationSection
				.find( '.tm-epo-variation-element' )
				.off( 'change.cpfv tm_epo_variation_element_change' )
				.on( 'change.cpfv tm_epo_variation_element_change', function( e ) {
					var field = $( this );
					var id = $.epoAPI.dom.id( field.attr( 'data-tm-for-variation' ) );
					var value = field.val();
					var section = field.closest( '.cpf-type-variations' );
					var nativeSelect = variationForm.find( "[data-attribute_name='attribute_" + id + "']" );
					var exists;

					if ( ! ( e && e.type && e.type === 'tm_epo_variation_element_change' ) ) {
						exists = false;
						nativeSelect.each( function() {
							if ( this.value === value ) {
								exists = true;
								return false;
							}
						} );
						if ( ! exists ) {
							nativeSelect.trigger( 'focusin' );
						}
						nativeSelect.val( value ).trigger( 'change' );
					}

					if ( ! value ) {
						nativeSelect.trigger( 'focusin' );
					}

					main_product
						.find( '.cpf-type-variations' )
						.not( section )
						.each( function() {
							variationForm.find( '#' + $.epoAPI.dom.id( $( this ).find( '.tm-epo-variation-element' ).first().attr( 'data-tm-for-variation' ) ) ).trigger( 'focusin' );
						} );

					field.blur();
					variationForm.trigger( 'woocommerce_update_variation_values_tmlogic' );
				} )
				.off( 'focusin.cpfv' )
				.on( 'focusin.cpfv', function() {
					var field = $( this );
					var id;

					if ( ! field.is( 'select' ) ) {
						return;
					}

					id = $.epoAPI.dom.id( field.attr( 'data-tm-for-variation' ) );
					variationForm.find( "[data-attribute_name='attribute_" + id + "']" ).trigger( 'focusin' );
					variationForm.trigger( 'woocommerce_update_variation_values_tmlogic' );
				} );

			variationForm.off( eventNamePrefix + 'found_variation.tmepo tm_fix_stock', '.single_variation_wrap' ).on( eventNamePrefix + 'found_variation.tmepo tm_fix_stock', '.single_variation_wrap', function() {
				tm_fix_stock_tmepo( $( this ), cartContainer );
			} );

			// update prices when a variation is found
			variationForm
				.off( eventNamePrefix + 'found_variation.tmepo' )
				.on( eventNamePrefix + 'found_variation.tmepo', function( event, variation ) {
					var form = $( this );

					totalsHolder.data( 'is-on-sale', variation.tc_is_on_sale );
					totalsHolder.data( 'regular-price', variation.display_regular_price );

					jWindow.trigger( 'tm-epo-found-variation', {
						epo: epoObject,
						totalsHolder: totalsHolder,
						totalsHolderContainer: totalsHolderContainer,
						currentCart: currentCart,
						variationForm: form,
						variation: variation
					} );

					found_variation_tmepo( {
						totalsHolder: totalsHolder,
						totalsHolderContainer: totalsHolderContainer,
						currentCart: currentCart,
						variationForm: form,
						variation: variation
					} );

					tm_fix_stock_tmepo( form, cartContainer );
				} )
				.off( eventNamePrefix + 'hide_variation.tmepo' )
				.on( eventNamePrefix + 'hide_variation.tmepo', { _epoObject: epoObject }, function( event ) {
					var _nativeProductPriceSelector;
					if ( finalTotalBoxMode === 'disable_change' || TMEPOJS.tm_epo_change_original_price === 'yes' ) {
						if ( ! alternativeCart || main_product.find( '.cpf-bto-price' ).length === 0 ) {
							if ( event.data._epoObject.associated_connect ) {
								_nativeProductPriceSelector = event.data._epoObject.main_product.find( tcAPI.associatedNativeProductPriceSelector );
							} else {
								_nativeProductPriceSelector = $( tcAPI.nativeProductPriceSelector );
							}
							if ( _nativeProductPriceSelector.data( 'tc-original-html' ) ) {
								_nativeProductPriceSelector.html( _nativeProductPriceSelector.data( 'tc-original-html' ) );
							}
						}
					}
					totalsHolder.data( 'price', false );
					// Fancy product Designer
					totalsHolder.removeData( 'tcprice' );
					currentCart.trigger( {
						type: 'tm-epo-update',
						norules: 2
					} );
				} )
				.off( eventNamePrefix + 'check_variations.tmepo' )
				.on( eventNamePrefix + 'check_variations.tmepo', function() {
					var data = {};
					var chosen = 0;
					var reset = epoVariationSection.find( '.reset_variations' );

					variationForm.find( '.variations select, .tc-epo-variable-product-selector' ).each( function() {
						var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
						var value = $( this ).val() || '';

						if ( value.length > 0 ) {
							chosen++;
						}

						data[ attribute_name ] = value;
					} );

					if ( chosen > 0 ) {
						if ( reset.css( 'visibility' ) === 'hidden' ) {
							reset.css( 'visibility', 'visible' ).hide().fadeIn();
						}
					} else {
						reset.css( 'visibility', 'hidden' );
					}
				} )
				.trigger( eventNamePrefix + 'check_variations' );

			tm_custom_variations( epoObject, cartContainer, itemId, main_product, epoHolder );
		}

		epoHolder.find( 'select.tm-epo-field' ).trigger( 'tm-select-change-html' );
		epoHolder.find( '.tm-quantity .tm-qty' ).trigger( 'change.cpf', { init: 1 } );
		epoHolder.find( '.tm-quantity' ).trigger( 'showhide.cpfcustom' );
		epoHolder.find( '.tm-has-clearbutton .tm-epo-field:checked' ).trigger( 'cpfclearbutton' );

		tc_add_dimensions( epoObject );

		jWindow.on( 'tm-do-epo-update', function() {
			// This must be run every time to get correct results for percent price types
			// if set set norules then discount will not auto work upon chosing a variation
			currentCart.trigger( {
				type: 'tm-epo-update'
				//"norules": 2
			} );
		} );

		jWindow.trigger( 'tm-epo-init-events', {
			epo: {
				epo_id: epo_id,
				form: epoObject.form,
				currentCart: currentCart,
				cart_container: cartContainer,
				epo_holder: epoHolder,
				totals_holder_container: totalsHolderContainer,
				totals_holder: totalsHolder,
				main_cart: main_cart,
				main_epo_inside_form: main_epo_inside_form,
				product_id_selector: product_id_selector,
				epo_id_selector: epo_id_selector,
				product_id: product_id,
				this_epo_container: this_epo_container,
				this_totals_container: this_totals_container,
				this_epo_totals_container: this_epo_totals_container
			}
		} );

		jWindow.trigger( 'epoEventHandlers', {
			epo: epoObject,
			currentCart: currentCart,
			cartContainer: cartContainer,
			qtyElement: qtyElement,
			epoHolder: epoHolder,
			totalsHolderContainer: totalsHolderContainer,
			totalsHolder: totalsHolder,
			variationForm: variationForm,
			variation_id_selector: variation_id_selector,
			main_epo_inside_form: main_epo_inside_form,
			this_product_type: this_product_type,
			get_price_excluding_tax: get_price_excluding_tax,
			get_price_including_tax: get_price_including_tax
		} );

		// show final totals
		if ( finalTotalBoxMode !== 'disable' && finalTotalBoxMode !== 'disable_change' && finalTotalBoxMode !== 'hide' ) {
			totalsHolderContainer.addClass( 'tc-show' );
		}

		// show extra options
		jWindow.trigger( 'epo_options_before_visible' );

		if ( TMEPOJS.tm_epo_progressive_display === 'yes' ) {
			setTimeout( function() {
				epoHolder
					.css( 'opacity', 0 )
					.addClass( 'tc-show' )
					.animate(
						{
							opacity: 1
						},
						tcAPI.epoAnimationDelay,
						'easeOutExpo',
						function() {
							jWindow.trigger( 'epo_options_visible' );
							jWindow.trigger( 'tmlazy' );
						}
					);
			}, tcAPI.epoDelay );
		} else {
			epoHolder.addClass( 'tc-show' );
			jWindow.trigger( 'epo_options_visible' );
			jWindow.trigger( 'tmlazy' );
		}

		main_product.addClass( 'tc-init' );
	}

	function run_wc_variation_form_cpf( epoObject ) {
		var form = epoObject.variations_form;
		var cart = epoObject.main_cart;
		var this_epo_container = epoObject.this_epo_container;
		var eventName = epoObject.is_associated ? 'tc_variation_form.cpf' : 'wc_variation_form.cpf';

		form.off( eventName ).on( eventName, function() {
			if ( form.data( 'epo_loaded' ) ) {
				return;
			}

			// Start Condition Logic
			cpf_section_logic( this_epo_container );
			cpf_element_logic( this_epo_container );

			// Init field price rules
			$.tcepo.lateFieldsPrices[ epoObject.epoEventId ] = [];

			epoEventHandlers( epoObject );
			tm_set_upload_fields( epoObject );
			tm_product_image( epoObject );

			epoObject.noEpoUpdate = false;

			setTimeout( function() {
				run_cpfdependson( this_epo_container );
				cart.trigger( {
					type: 'tm-epo-update',
					rules: 'init'
				} );
			}, 10 );

			form.data( 'epo_loaded', true );
		} );

		if ( variationsFormIsLoaded ) {
			form.trigger( eventName );
		}

		jWindow.trigger( 'epo-after-init', { epo: epoObject } );
	}

	function detect_variation_swatches_interval( epoObject ) {
		var $id = requestAnimationFrame( function() {
			detect_variation_swatches_interval( epoObject );
		} );
		var obj = epoObject.variations_form;
		var bound = obj.data( 'bound' );
		var eventName = epoObject.is_associated ? 'tc_variation_form.cpf' : 'wc_variation_form.cpf';

		if ( bound ) {
			cancelAnimationFrame( $id );
			run_wc_variation_form_cpf( epoObject );
			obj.trigger( eventName );
		}
	}

	function manualInitEPO( epoObject, item, itemCart, itemEpoContainer, main_product ) {
		var epoObjectOriginal = $.extend( true, {}, epoObject );
		var product_id = itemEpoContainer.attr( 'data-product-id' );
		var epo_id = itemEpoContainer.attr( 'data-epo-id' );
		var product_id_selector = '.tm-product-id-' + product_id;
		var epo_id_selector = "[data-epo-id='" + epo_id + "']";
		var epoEventId = 'p' + product_id + 'e' + epo_id;

		epoObject.isManual = true;

		$.tcepo.formSubmitEvents[ epoEventId ] = [];
		$.tcepo.errorObject[ epoEventId ] = false;
		$.tcepo.initialActivation[ epoEventId ] = false;

		epoObject.product_id = product_id;
		epoObject.product_id_selector = product_id_selector;
		epoObject.epo_id = epo_id;
		epoObject.epo_id_selector = epo_id_selector;
		epoObject.epoEventId = epoEventId;
		epoObject.noEpoUpdate = true;
		epoObject.thisForm = item;

		tm_lazyload();
		main_product.find( '.tm-collapse' ).tmtoggle();
		main_product.find( '.tm-section-link' ).tmsectionpoplink();

		tm_set_datepicker( item );
		tm_set_range_pickers( item );
		tm_css_styles( item );
		tm_set_color_pickers( itemEpoContainer.find( '.tm-color-picker' ) );
		tm_set_lightbox( itemEpoContainer.find( '.tc-lightbox-image' ).not( '.tm-extra-product-options-variations .radio_image' ) );

		// Start Condition Logic
		cpf_section_logic( itemEpoContainer );
		cpf_element_logic( itemEpoContainer );
		run_cpfdependson( itemEpoContainer );

		$.tcToolTip( item.find( '.tm-tooltip' ) );
		epoEventHandlers( epoObject, item, itemCart );

		epoObject.noEpoUpdate = false;

		itemCart.trigger( {
			type: 'tm-epo-update',
			norules: 2
		} );
		setTimeout( function() {
			epoObject.main_cart.trigger( {
				type: 'tm-epo-update',
				epoObject: epoObjectOriginal,
				norules: 1
			} );
		}, 200 );
		tm_fix_stock_tmepo( itemCart, item );
	}

	function tm_init_epo( main_product, is_quickview, product_id, epo_id, associated_connect, mainEpoObject, reactivate ) {
		// Holds the main cart when using Composite Products
		var main_cart = false;
		var main_epo_inside_form = false;
		var main_totals_inside_form = false;
		var epoEventId;
		var has_epo = typeof product_id !== 'undefined';
		var not_has_epo = false;
		var add_to_cart_field;
		var product_id_selector;
		var epo_id_selector;
		var this_epo_container;
		var this_totals_container;
		var this_epo_totals_container;
		var epo_object;
		var variations_form;
		var detect_variation_swatches = $( '.variation_form_section .variations-table' ).length > 0;
		var is_associated = false;

		main_product = $( main_product );

		if ( main_product.is( '.tc-init' ) && ! reactivate ) {
			return true;
		}

		if ( is_quickview ) {
			errorContainer = main_product;
		} else {
			errorContainer = $( window );
		}

		jWindow.trigger( 'tm-epo-init-start' );

		if ( ! has_epo ) {
			if ( main_product.is( '.product' ) ) {
				not_has_epo = true;
				has_epo = jBody.find( tcAPI.epoSelector ).length;
			}
		}

		// return if product has no extra options and the totals box is not enabled for all products
		if ( ! has_epo && TMEPOJS.tm_epo_enable_final_total_box_all === 'no' && ! main_product.is( '.tm-no-options-composite' ) ) {
			jWindow.trigger( 'tm-epo-init-end-no-options' );
			return;
		}

		// set the main_product variable again for products that have no extra options
		if ( not_has_epo ) {
			jWindow.trigger( 'tm-epo-init-no-options' );
			if ( main_product.is( '.product' ) && ! ( main_product.is( '.tm-no-options-pxq' ) || main_product.is( '.tm-no-options-composite' ) ) ) {
				main_product = jBody;
			}
		}

		if ( ! product_id ) {
			add_to_cart_field = main_product.find( tcAPI.addToCartSelector ).last();
			if ( add_to_cart_field.length > 0 ) {
				product_id = add_to_cart_field.val();
			} else {
				add_to_cart_field = $( '.tc-totals-form.tm-totals-form-main' );
				product_id = add_to_cart_field.attr( 'data-product-id' );
			}
			if ( ! product_id ) {
				product_id = '';
			}
		}

		if ( ! epo_id ) {
			epo_id = parseInt( main_product.find( 'input.tm-epo-counter' ).last().val(), 10 );

			if ( ! Number.isFinite( epo_id ) ) {
				epo_id = '';
			}
		}

		product_id_selector = '.tm-product-id-' + product_id;
		epo_id_selector = "[data-epo-id='" + epo_id + "']";
		this_epo_container = $( '.tc-extra-product-options' + product_id_selector + epo_id_selector );
		this_totals_container = $( '.tc-totals-form' + product_id_selector + epo_id_selector );
		this_epo_totals_container = $( '.tc-epo-totals' + product_id_selector + epo_id_selector );
		variations_form = main_product.find( '.variations_form' ).not( '.composite_component .variations_form' ).first();
		epoEventId = 'p' + product_id + 'e' + epo_id;

		if ( variations_form && variations_form.attr( 'data-product_id' ) ) {
			if ( variations_form.attr( 'data-product_id' ) !== product_id ) {
				variations_form = main_product.find( ".variations_form[data-product_id='" + product_id + "']" );
			}
		}

		main_cart = get_main_cart( main_product, main_product, 'form', product_id );
		if ( main_cart.length === 0 ) {
			if ( main_product.is( '.tc-shortcode-wrap' ) ) {
				main_cart = get_main_cart( this_totals_container, this_totals_container, '.tc-totals-form', product_id );
			} else if ( main_product.is( '.tc-epo-element-product-container' ) ) {
				main_cart = main_product.find( tcAPI.associatedEpoCart );
				// should never be 0
				if ( main_cart.length === 0 ) {
					main_cart = this_epo_container.parent( tcAPI.associatedEpoSelector );
					if ( main_cart.length === 0 ) {
						main_cart = main_product.find( '.tc-epo-element-product-container-right' );
					}
				}
				is_associated = true;
			}
		}

		if ( is_associated && variations_form.length === 0 && main_product.is( '.variations_form' ) ) {
			variations_form = main_product;
		}

		$.tcepo.formSubmitEvents[ epoEventId ] = [];
		$.tcepo.errorObject[ epoEventId ] = false;
		$.tcepo.initialActivation[ epoEventId ] = false;

		if ( main_cart.find( tcAPI.epoSelector ).length > 0 ) {
			main_epo_inside_form = true;
		}
		if ( main_cart.find( '.tc-totals-form' ).length > 0 ) {
			main_totals_inside_form = true;
		}

		if ( ! main_totals_inside_form ) {
			$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
				trigger: function() {
					return true;
				},
				on_true: function() {
					// hidden fields see totals.php
					var epos_hidden = $( '.tc-totals-form.tm-product-id-' + product_id + "[data-epo-id='" + epo_id + "']" ).tcClone();
					var formepo = $( '<div class="tm-hidden tm-formepo-normal"></div>' );

					main_cart.find( '.tm-formepo-normal' ).remove();
					formepo.append( epos_hidden );
					main_cart.append( formepo );
					return true;
				},
				on_false: function() {
					setTimeout( function() {
						$( '.tm-formepo-normal' ).remove();
					}, 100 );
				}
			};
		}
		if ( ! main_epo_inside_form ) {
			$.tcepo.formSubmitEvents[ epoEventId ][ $.tcepo.formSubmitEvents[ epoEventId ].length ] = {
				trigger: function() {
					return true;
				},
				on_true: function() {
					// visible fields
					var epos = $( tcAPI.epoSelector + '.tm-product-id-' + product_id + "[data-epo-id='" + epo_id + "']" )
						.tcClone()
						.addClass( 'formepo' );
					var formepo = $( '<div class="tm-hidden tm-formepo"></div>' );

					main_cart.find( '.tm-formepo' ).remove();
					formepo.append( epos );

					main_cart.append( formepo );
					return true;
				},
				on_false: function() {
					setTimeout( function() {
						$( '.tm-formepo' ).remove();
					}, 100 );
				}
			};
		}

		epo_object = {
			main_product: main_product,
			main_cart: main_cart,
			epo_id: epo_id,
			form: get_main_form( main_product ),
			main_epo_inside_form: main_epo_inside_form,
			product_id_selector: product_id_selector,
			epo_id_selector: epo_id_selector,
			product_id: product_id,
			this_epo_container: this_epo_container,
			this_totals_container: this_totals_container,
			this_epo_totals_container: this_epo_totals_container,
			qtySelector: tcAPI.qtySelector,
			manualInitEPO: manualInitEPO,
			epoEventId: epoEventId,
			variations_form: variations_form,
			has_epo: has_epo,
			is_quickview: is_quickview,
			is_associated: is_associated,
			is_epo_shortcode: this_epo_container.is( '.tc-shortcode' ),
			mainEpoObject: mainEpoObject,
			associated_connect: associated_connect,
			noEpoUpdate: true
		};

		$( epo_object.form ).data( 'epo_object', epo_object );

		main_cart.data( 'product_id', product_id ).data( 'epo_id', epo_id ).data( 'product_id_selector', product_id_selector ).data( 'epo_id_selector', epo_id_selector );

		tm_set_checkboxes_rules( epo_object );
		tm_set_upload_rules( epo_object );
		tm_set_datepicker( this_epo_container );
		tm_set_range_pickers( this_epo_container );
		tm_set_url_fields();

		$.tcToolTip( this_epo_container.find( '.tm-tooltip' ) );

		this_epo_container.find( '.tm-collapse' ).tmtoggle();
		this_epo_container.find( '.tm-section-link' ).tmsectionpoplink();

		if ( reactivate ) {
			this_epo_container.addClass( 'reactivate' );
		}

		if ( variations_form.length > 0 ) {
			if ( reactivate ) {
				variations_form.data( 'epo_loaded', false );
			}
			this_epo_totals_container.data( 'price', false );
			if ( detect_variation_swatches ) {
				detect_variation_swatches_interval( epo_object );
			} else {
				run_wc_variation_form_cpf( epo_object );
			}
		} else {
			setTimeout( function() {
				// Start Condition Logic
				cpf_section_logic( this_epo_container );
				cpf_element_logic( this_epo_container );
				run_cpfdependson( this_epo_container );

				// Init field price rules
				$.tcepo.lateFieldsPrices[ epoEventId ] = [];
				epoEventHandlers( epo_object );
				tm_set_upload_fields( epo_object );
				tm_product_image( epo_object );

				jWindow.trigger( 'epo-after-init-in-timeout', { epo: epo_object } );

				main_cart.trigger( 'tm-epo-check-dpd' );
				epo_object.noEpoUpdate = false;
				main_cart.trigger( {
					type: 'tm-epo-update',
					rules: 'init'
				} );
			}, 20 );
			jWindow.trigger( 'epo-after-init', { epo: epo_object } );
		}

		tm_lazyload();

		tm_css_styles( this_epo_container );
		tm_set_color_pickers( this_epo_container.find( '.tm-color-picker' ) );
		tm_set_lightbox( this_epo_container.find( '.tc-lightbox-image' ).not( '.tm-extra-product-options-variations .radio_image' ) );
		tm_theme_specific_actions( epo_object );
		tc_compatibility( epo_object );

		if ( ! is_associated ) {
			tm_floating_totals( this_epo_totals_container, is_quickview, main_cart );
			tm_form_submit_event( epo_object );
			tm_show_hide_add_to_cart_button( main_product, epo_object, $.tcepo.oneOptionIsSelected[ epoEventId ] );
		}
		jWindow.on( 'cpflogicdone', function() {
			tc_add_dimensions( epo_object );
		} );

		jWindow.trigger( 'tm-epo-init-end', { epo: epo_object } );

		return epo_object;
	}

	function manual_init( container, reactivate ) {
		var $this = $( container );
		var product_id = $this.attr( 'data-product-id' );
		var epo_id = $this.attr( 'data-epo-id' );
		var quickview_floating = false;
		var testForm = $this.parent();
		if ( ! testForm.is( 'form' ) ) {
			testForm = $this.closest( 'form' );
			if ( ! testForm.is( 'form' ) ) {
				testForm = $this.parent();
			}
		}
		testForm = testForm.parent();

		tm_init_epo( testForm, quickview_floating, product_id, epo_id, undefined, undefined, reactivate );
	}

	function init_epo_plugin() {
		var epo_container;
		var epo_options_container;

		if ( TMEPOJS.tm_epo_no_lazy_load === 'no' ) {
			$.extend( $.lazyLoadXT, {
				autoInit: false,
				selector: 'img.tmlazy',
				srcAttr: 'data-original',
				visibleOnly: false,
				updateEvent: $.lazyLoadXT.updateEvent + ' tmlazy'
			} );
		}
		/*
		 * tm-no-options-pxq = product has not options but the "Enable Final total box for all products" is on
		 * tm-no-options-composite = product is a composite product with no options but at least one of its bundles have options
		 */
		epo_container = $( '.tm-no-options-pxq, .tm-no-options-composite' );

		if ( epo_container.length > 0 ) {
			// Special cases
			// -------------
			// Price x Quantity display (.tm-no-options-pxq) & composite
			// without option but a component has extra options
			// (.tm-no-options-composite)

			epo_container.each( function( loop_index, product_wrap ) {
				tm_init_epo( $( product_wrap ), false );
			} );
		}

		// The setTimeout is used for compatibility with
		// skeleton screen mode that some themes have

		setTimeout( function() {
			try {
				// new main way of calling tm_init_epo
				// -----------------------------------
				// Normal product pages

				epo_options_container = $( tcAPI.epoSelector ).not( tcAPI.associatedEpoSelector + ' ' + tcAPI.epoSelector + ', .tm-no-options-pxq, .tm-no-options-composite, .wc-product-table ' + tcAPI.epoSelector );

				if ( epo_options_container.length > 0 ) {
					epo_options_container.each( function() {
						var $this = $( this );
						var product_id = $this.attr( 'data-product-id' );
						var epo_id = $this.attr( 'data-epo-id' );
						var quickview_floating = false;
						var jProductWrap;
						var addInputs = false;

						// First check if we are in a loop.
						jProductWrap = $this.closest( '.tc-after-shop-loop.tm-has-options' );

						if ( jProductWrap.length === 0 ) {
							// Check based on native add to cart selector.
							jProductWrap = $( tcAPI.addToCartSelector + "[value='" + product_id + "']" )
								.closest( 'form,.cart' )
								.first()
								.parent();
							// Check based on plugin add to cart selector.
							if ( jProductWrap.length === 0 ) {
								jProductWrap = $( tcAPI.tcAddToCartSelector + "[value='" + product_id + "']" )
									.closest( 'form,.cart' )
									.first()
									.parent();

								if ( jProductWrap.length === 0 ) {
									// Check if we are in a shortcode
									jProductWrap = $this.closest( 'form,.cart' ).first().parent( '.tm-has-options' );
									if ( jProductWrap.length === 0 ) {
										if ( $this.is( '.tc-shortcode' ) ) {
											jProductWrap = $this.wrap( '<div class="tc-shortcode-wrap tc-wrap-' + epo_id + '"></div>' );
											jProductWrap = $this.parent();
										}
										if ( jProductWrap.length > 0 ) {
											addInputs = true;
										}
									}
								}
							}
						} else {
							addInputs = true;
						}

						if ( jProductWrap.length > 0 ) {
							if ( addInputs ) {
								// in shop (variation logic will not work here)
								quickview_floating = true;
								$this
									.closest( 'form,.cart' )
									.first()
									.append( $( '<input name="add-to-cart" value="' + product_id + '" type="hidden" />' ) );
								$this.closest( 'form,.cart' ).first().append( $( '<input type="hidden" value="" class="variation_id" name="variation_id">' ) );
							}

							if ( jProductWrap.is( 'form' ) ) {
								jProductWrap = jProductWrap.parent();
							}

							tm_init_epo( jProductWrap, quickview_floating, product_id, epo_id );
						}
					} );
				}
			} catch ( err ) {
				window.console.log( err );
				errorObject = err;
			}
		}, 1 );
	}

	$.tcepo.tm_init_epo = function( main_product, is_quickview, product_id, epo_id ) {
		tm_init_epo( main_product, is_quickview, product_id, epo_id );
	};

	jWindow.on( 'tc_manual_init', function( evt, container ) {
		var reactivate;
		if ( 'container' in container && 'reactivate' in container ) {
			reactivate = container.reactivate;
			container = container.container;
		}
		manual_init( container, reactivate );
	} );

	jDocument.ready( function() {
		tcAPI = $.epoAPI.applyFilter( 'tc_api', tcAPI );

		jWindow.on( 'lazyLoadXToncomplete', function() {
			$( '.tm-owl-slider' ).each( function() {
				$( this ).trigger( 'refresh.owl.carousel' );
			} );
		} );

		jWindow.on( 'tc_init_epo_plugin', function( evt ) {
			init_epo_plugin( evt );
		} );

		jWindow.on( 'tcShowLastError', function() {
			window.console.log( errorObject );
		} );

		$( '.ajax_add_to_cart' ).on( 'click.tcajax', function() {
			currentAjaxButton = $( this );
		} );

		$.ajaxPrefilter( function( options, originalOptions ) {
			var found = false;
			var hashes;
			var hash;
			var i;
			var params;
			var $thisbutton;
			var _data;
			var _urldata;
			var _pid;
			var epos;
			var _cpf_product_price;
			var form_prefix;
			var obj;
			var oldData;
			var formData;

			if ( TMEPOJS.tm_epo_enable_in_shop === 'yes' ) {
				hashes = options.url.split( '?' );

				if ( hashes && hashes.length >= 1 ) {
					hashes = hashes[ 1 ];
					if ( hashes ) {
						hash = hashes.split( '&' );
						for ( i = 0; i < hash.length; i += 1 ) {
							params = hash[ i ].split( '=' );
							if ( params.length >= 1 ) {
								if ( params[ 0 ] && params[ 1 ] && params[ 0 ] === 'wc-ajax' && params[ 1 ] === 'add_to_cart' ) {
									found = true;
								}
							}
						}
						if ( found ) {
							options.originalsuccess = options.success;
							options.success = function( response ) {
								if ( response && response.error && response.product_url ) {
									if ( currentAjaxButton && currentAjaxButton.length === 1 ) {
										$thisbutton = currentAjaxButton;
									}
									$thisbutton = $( ".ajax_add_to_cart[data-product_id='" + originalOptions.data.product_id + "']" );
									$thisbutton.removeClass( 'added' );
									$thisbutton.removeClass( 'loading' );
								} else {
									options.originalsuccess.call( null, response );
								}
							};
						}
					}
				}
			}

			if ( FormData && originalOptions.data ) {
				_data = originalOptions.data;
				if ( typeof originalOptions.data === 'string' ) {
					_data = $.epoAPI.util.parseParams( originalOptions.data );
				}
				_urldata = [];
				if ( originalOptions.url && originalOptions.url.indexOf ) {
					_urldata = $.epoAPI.util.parseParams( originalOptions.url.slice( originalOptions.url.indexOf( '?' ) + 1 ) );
				}

				if ( 'quantity' in _data && ( _data.product_id || _data[ 'add-to-cart' ] || _urldata.product_id || _urldata[ 'add-to-cart' ] || _data.tcaddtocart ) ) {
					_pid = _data.product_id || _data[ 'add-to-cart' ] || _urldata.product_id || _urldata[ 'add-to-cart' ] || _data.tcaddtocart;
					if ( currentAjaxButton && currentAjaxButton.length === 1 && currentAjaxButton.closest( '.tm-has-options' ).length === 1 ) {
						epos = currentAjaxButton.closest( '.tm-has-options' ).find( '.tc-extra-product-options.tm-product-id-' + _pid );
					} else {
						epos = $( '.tc-extra-product-options.tm-product-id-' + _pid );
					}

					if ( epos.length > 1 ) {
						if ( epos.filter( '.formepo' ) ) {
							epos = epos.filter( '.formepo' );
						} else {
							epos = epos.first();
						}
					}
					if ( epos.length === 1 ) {
						_cpf_product_price = $( '.tc-totals-form.tm-product-id-' + _pid )
							.find( '.cpf-product-price' )
							.val();
						form_prefix = $( '.tc-totals-form.tm-product-id-' + _pid )
							.find( '.tc_form_prefix' )
							.val();
						obj = {
							tcajax: 1,
							tcaddtocart: _pid,
							cpf_product_price: _cpf_product_price
						};
						if ( form_prefix ) {
							obj.tc_form_prefix = form_prefix;
						}

						options.data = $.epoAPI.util.parseParams( options.data, true );
						options.data = $.extend( options.data, epos.tcSerializeObject(), obj );
						oldData = options.data;
						formData = new FormData();

						Object.keys( oldData ).forEach( function( key ) {
							formData.append( key, oldData[ key ] );
						} );

						epos.find( ':file' )
							.toArray()
							.forEach( function( el ) {
								for ( i = 0; i < $( el )[ 0 ].files.length; i++ ) {
									formData.append( $( el ).attr( 'name' ), $( el )[ 0 ].files[ i ] );
								}
							} );

						options.data = formData;
						options.contentType = false;
						options.cache = false;
						options.processData = false;
					}
				}
			}
		} );

		jDocument.ajaxSuccess( function( event, request, settings ) {
			// quickview plugins
			var qv_container = TMEPOJS.quickview_array || 'null';
			var fromaddons = TMEPOJS.quickview_container || 'null';
			var added = {};
			var selectors;
			var container;
			var product_id;
			var epo_id;
			var noProductCheck;
			var testContainer;
			var parsedUrl;
			var time = 1;

			$( '.tm-formepo-normal' ).remove();
			$( '.tm-formepo' ).remove();

			//fix for menu cart pop up
			$( '.tm-cart-link' ).tmpoplink();

			qv_container = $.epoAPI.util.parseJSON( qv_container );

			fromaddons = $.epoAPI.util.parseJSON( fromaddons );

			for ( selectors in fromaddons ) {
				if ( Object.prototype.hasOwnProperty.call( fromaddons, selectors ) ) {
					added[ fromaddons[ selectors ][ 0 ] ] = $( fromaddons[ selectors ][ 1 ] );
				}
			}

			$.extend( qv_container, added );

			Object.keys( qv_container ).forEach( function( key ) {
				noProductCheck = false;
				container = $( qv_container[ key ] );

				if ( key === 'woodmart_quick_shop' ) {
					parsedUrl = $.epoAPI.util.parseParams( settings.url );
					if ( parsedUrl.action === 'woodmart_quick_shop' ) {
						testContainer = $( $.epoAPI.util.escapeSelector( qv_container[ key ] + '.post-' + parsedUrl.id ) );
						if ( testContainer.length ) {
							container = testContainer;
							noProductCheck = true;
						}
					}
				}
				if ( key === 'woodmart_quick_view' ) {
					parsedUrl = $.epoAPI.util.parseParams( settings.url );
					if ( parsedUrl.action === 'woodmart_quick_view' ) {
						testContainer = $( $.epoAPI.util.escapeSelector( qv_container[ key ] + '.post-' + parsedUrl.id ) );
						if ( testContainer.length ) {
							container = testContainer;
							noProductCheck = true;
						}
					}
				}

				if ( key === 'quickview_pro' ) {
					parsedUrl = settings.url.split( '/' );
					if ( parsedUrl.length ) {
						testContainer = $( $.epoAPI.util.escapeSelector( qv_container[ key ] + ' .post-' + parsedUrl[ parsedUrl.length - 1 ] ) );
						if ( testContainer.length ) {
							container = testContainer;
							noProductCheck = true;
						}
					}
				}

				if ( key === 'wp_food' || key === 'jet_popup_get_content' ) {
					noProductCheck = true;
				}

				if ( key === 'woofood' && settings.data ) {
					parsedUrl = $.epoAPI.util.parseParams( settings.data );
					if ( parsedUrl.action === 'woofood_quickview_ajax' ) {
						testContainer = testContainer = container.find( 'form' ).parent();
						if ( testContainer.length ) {
							container = testContainer;
							noProductCheck = true;
						}
					}
				}

				if ( container.find( '.product' ).length === 0 && container.is( '.product' ) ) {
					noProductCheck = true;
				}

				if ( key !== 'yith_quick_view_plugin' && container.length && ( container.find( '.product' ).length > 0 || noProductCheck ) ) {
					container.removeClass( 'tc-init' );

					if (
						key === 'fusion_quick_view_load' ||
						key === 'jet_popup_get_content' ||
						key === 'wp_food' ||
						key === 'woodmart_quick_shop' ||
						key === 'woodmart_quick_view' ||
						key === 'lightboxpro' ||
						key === 'jckqv_quick_view' ||
						key === 'yith_quick_view_plugin' ||
						key === 'theme_flatsome'
					) {
						variationsFormIsLoaded = true;
					}
					tmLazyloadContainer = container;

					if ( key === 'fusion_quick_view_load' ) {
						time = 1400;
					}

					setTimeout( function() {
						product_id = tmLazyloadContainer.find( tcAPI.epoSelector ).attr( 'data-product-id' );
						epo_id = tmLazyloadContainer.find( tcAPI.epoSelector ).attr( 'data-epo-id' );
						if ( key === 'woodmart_quick_shop' ) {
							container.addClass( 'has-options' );
						}

						// Reset element cache
						tcAPI.getElementFromFieldCache = [];
						tm_init_epo( tmLazyloadContainer, true, product_id, epo_id );
						jWindow.trigger( 'tmlazy' );
						jWindow.trigger( 'tm_epo_loaded_quickview' );
						if ( $.jMaskGlobals ) {
							tmLazyloadContainer.find( $.jMaskGlobals.maskElements ).each( function() {
								var t = $( this );

								if ( t.attr( 'data-mask' ) ) {
									t.mask( t.attr( 'data-mask' ) );
								}
							} );
						}
					}, time );
				}
			} );
		} );

		init_epo_plugin();

		$( '.tm-cart-link' ).tmpoplink();
		jBody.on( 'updated_checkout', function() {
			$( '.tm-cart-link' ).tmpoplink();
		} );

		jWindow.trigger( 'tmlazy' );

		jWindow.trigger( 'tm_epo_loaded' );
	} );

	jDocument.ready( function() {
		// Fix several custom quantity buttons on themes
		jDocument.on( 'click', '.quantity .jckqv-qty-spinner, .quantity .ui-spinner-button', function() {
			$( this ).closest( '.quantity' ).find( 'input.qty' ).trigger( 'change' );
		} );

		// Sober theme quickview fix
		jBody.on( 'sober_quickview_opened', function() {
			var product_id;
			var epo_id;

			tmLazyloadContainer = $( '#quick-view-modal' );

			product_id = tmLazyloadContainer.find( tcAPI.epoSelector ).attr( 'data-product-id' );
			epo_id = tmLazyloadContainer.find( tcAPI.epoSelector ).attr( 'data-epo-id' );

			tm_init_epo( tmLazyloadContainer, true, product_id, epo_id );
			jWindow.trigger( 'tmlazy' );
			jWindow.trigger( 'tm_epo_loaded_quickview' );
			if ( $.jMaskGlobals ) {
				tmLazyloadContainer.find( $.jMaskGlobals.maskElements ).each( function() {
					var t = $( this );

					if ( t.attr( 'data-mask' ) ) {
						t.mask( t.attr( 'data-mask' ) );
					}
				} );
			}
		} );

		// bulk variations forms plugin
		$( '#wholesale_form' ).on( 'submit', function() {
			var _product_id = $( 'form.cart' ).find( tcAPI.addToCartSelector ).val();
			// visible fields
			var epos = $( tcAPI.epoSelector + ".tm-cart-main[data-product-id='" + _product_id + "']" ).tcClone();
			// hidden fields see totals.php
			var epos_hidden = $( ".tm-totals-form-main[data-product-id='" + _product_id + "']" ).tcClone();
			var formepo = $( "<div class='tm-hidden tm-formepo'></div>" );

			formepo.append( epos );
			formepo.append( epos_hidden );
			$( this ).append( formepo );
			return true;
		} );

		// Disable quote button if option validation fails
		jDocument.on( 'click', '#add_to_quote', function( e ) {
			var form;
			var epo_id;
			var epos;

			if ( TMEPOJS && TMEPOJS.tm_epo_global_enable_validation === 'yes' ) {
				form = $( this ).parents( 'form' );
				epo_id = form.find( '.tm-epo-counter' ).val();
				epos = $( tcAPI.epoSelector + "[data-epo-id='" + epo_id + "']" );

				//not validated
				if ( form.length > 0 && epos.length > 0 && ! form.tc_validate().form() ) {
					e.stopImmediatePropagation();
				}
			}
		} );

		// PayPal for WooCommerce (PayPal Express Checkout button fix)
		$( '.single_add_to_cart_button.paypal_checkout_button' ).on( 'click', function( event ) {
			// this is the selector used by the paypal checkout plugin
			var form = $( '.cart' );
			var validator;

			if ( form.data( 'tc_validator' ) ) {
				validator = form.data( 'tc_validator' );
				if ( validator.errorList ) {
					event.stopImmediatePropagation();
				}
			}
		} );

		$( '.wc-product-table' ).on( 'init.wcpt', function( event, table ) {
			table.$table.find( tcAPI.epoSelector ).addClass( 'hidden' );
			table.$table.find( 'thead tr' ).append( '<th>&nbsp;</th>' );
			setTimeout( function() {
				table.$table.find( '.cart:not(.cart_group)' ).each( function() {
					var epo = $( this ).find( tcAPI.epoSelector );
					var tr = epo.closest( 'tr' );

					$( "<td class='wc-product-table-epo'></td>" ).appendTo( tr ).append( epo );
					$( window ).trigger( 'tc_manual_init', epo );
					epo.removeClass( 'hidden' );
				} );
			}, 500 );
		} );
	} );
}( window, document, window.jQuery ) );
