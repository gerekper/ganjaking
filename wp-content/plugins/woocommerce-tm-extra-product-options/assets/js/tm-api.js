/**
 * The following API is shared on both frontend and backend.
 *
 * No external libraries are loaded in this file.
 *
 * 1. Polyfills
 * 2. themeComplete plugin JS API
 * 3. themeComplete jQuery extensions
 */
/*jshint bitwise: false*/
/**
 * 1. Polyfills
 *
 * The following are required methods for
 * the String, Number and Object prototype
 */
( function() {
	'use strict';
	/**
	 * Number.isFinite
	 *
	 * The Number.isFinite() method determines whether the passed value is a finite number.
	 *
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isFinite
	 */
	if ( Number.isFinite === undefined ) {
		Number.isFinite = function( value ) {
			return typeof value === 'number' && isFinite( value );
		};
	}

	/**
	 * Number.isInteger
	 *
	 * The Number.isInteger() method determines whether the passed value is an integer.
	 *
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isInteger
	 */
	Number.isInteger =
		Number.isInteger ||
		function( value ) {
			return typeof value === 'number' && isFinite( value ) && Math.floor( value ) === value;
		};

	/**
	 * Number.isNaN
	 *
	 * The Number.isNaN() method determines whether the passed value is NaN and its type is Number.
	 * It is a more robust version of the original, global isNaN().
	 *
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isNaN
	 */
	if ( Number.isNaN === undefined ) {
		/* eslint-disable no-self-compare */
		Number.isNaN = function( value ) {
			return value !== value;
		};
		/* eslint-disable no-self-compare */
	}

	/**
	 * String.prototype.startsWith()
	 *
	 * The startsWith() method determines whether a string begins with
	 * the characters of a specified string, returning true or false as appropriate.
	 *
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/startsWith
	 */
	if ( ! String.prototype.startsWith ) {
		Object.defineProperty( String.prototype, 'startsWith', {
			value: function( search, pos ) {
				return this.substr( ! pos || pos < 0 ? 0 : +pos, search.length ) === search;
			}
		} );
	}

	/**
	 * String.prototype.endsWith()
	 *
	 * The endsWith() method determines whether a string ends with
	 * the characters of a specified string, returning true or false as appropriate.
	 *
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/endsWith
	 */
	if ( ! String.prototype.endsWith ) {
		Object.defineProperty( String.prototype, 'endsWith', {
			value: function( search, this_len ) {
				if ( this_len === undefined || this_len > this.length ) {
					this_len = this.length;
				}
				return this.substring( this_len - search.length, this_len ) === search;
			}
		} );
	}

	/**
	 * String.prototype.isNumeric()
	 *
	 * Determines whether a string contains a numeric value
	 *
	 * https://stackoverflow.com/questions/18082/validate-decimal-numbers-in-javascript-isnumeric
	 */
	if ( ! String.prototype.isNumeric ) {
		Object.defineProperty( String.prototype, 'isNumeric', {
			value: function() {
				return ! isNaN( parseFloat( this ) ) && isFinite( this );
			}
		} );
	}

	/**
	 * Object.assign()
	 *
	 * The Object.assign() method is used to copy the values of all enumerable own properties
	 * from one or more source objects to a target object. It will return the target object.
	 *
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/assign
	 */
	if ( typeof Object.assign !== 'function' ) {
		/* eslint-disable no-unused-vars */
		// Must be writable: true, enumerable: false, configurable: true
		Object.defineProperty( Object, 'assign', {
			value: function assign( target, varArgs ) {
				// .length of function is 2
				var to;
				var index;
				var nextSource;
				var nextKey;

				if ( target === undefined || target === null ) {
					// TypeError if undefined or null
					throw new TypeError( 'Cannot convert undefined or null to object' );
				}

				to = Object( target );

				for ( index = 1; index < arguments.length; index += 1 ) {
					nextSource = arguments[ index ];

					if ( nextSource !== undefined && nextSource !== null ) {
						// Skip over if undefined or null
						for ( nextKey in nextSource ) {
							// Avoid bugs when hasOwnProperty is shadowed
							if ( Object.prototype.hasOwnProperty.call( nextSource, nextKey ) ) {
								to[ nextKey ] = nextSource[ nextKey ];
							}
						}
					}
				}
				return to;
			},
			writable: true,
			configurable: true
		} );
		/* eslint-enable no-unused-vars */
	}

	/**
	 * Array.find()
	 *
	 * The find() method returns the value of the first element in the provided array that satisfies
	 * the provided testing function.
	 *
	 * https://tc39.github.io/ecma262/#sec-array.prototype.find
	 */

	if ( ! Array.prototype.find ) {
		Object.defineProperty( Array.prototype, 'find', {
			value: function( predicate ) {
				var o;
				var len;
				var thisArg;
				var k;
				var kValue;

				// 1. Let O be ? ToObject(this value).
				if ( this === null || this === undefined || this === false ) {
					throw TypeError( '"this" is null or not defined' );
				}

				o = Object( this );

				// 2. Let len be ? ToLength(? Get(O, "length")).
				// eslint-disable-next-line no-bitwise
				len = o.length >>> 0;

				// 3. If IsCallable(predicate) is false, throw a TypeError exception.
				if ( typeof predicate !== 'function' ) {
					throw TypeError( 'predicate must be a function' );
				}

				// 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
				thisArg = arguments[ 1 ];

				// 5. Let k be 0.
				k = 0;

				// 6. Repeat, while k < len
				while ( k < len ) {
					// a. Let Pk be ! ToString(k).
					// b. Let kValue be ? Get(O, Pk).
					// c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
					// d. If testResult is true, return kValue.
					kValue = o[ k ];
					if ( predicate.call( thisArg, kValue, k, o ) ) {
						return kValue;
					}
					// e. Increase k by 1.
					k++;
				}

				// 7. Return undefined.
				return undefined;
			},
			configurable: true,
			writable: true
		} );
	}

	/**
	 * Array.findIndex()
	 *
	 * The findIndex() method returns the index of the first element in the array that satisfies the
	 * provided testing function. Otherwise, it returns -1, indicating that no element passed the test.
	 *
	 * https://tc39.github.io/ecma262/#sec-array.prototype.findindex
	 */

	if ( ! Array.prototype.findIndex ) {
		Object.defineProperty( Array.prototype, 'findIndex', {
			value: function( predicate ) {
				var o;
				var len;
				var thisArg;
				var k;
				var kValue;

				// 1. Let O be ? ToObject(this value).
				if ( this === null || this === undefined || this === false ) {
					throw new TypeError( '"this" is null or not defined' );
				}

				o = Object( this );

				// 2. Let len be ? ToLength(? Get(O, "length")).
				// eslint-disable-next-line no-bitwise
				len = o.length >>> 0;

				// 3. If IsCallable(predicate) is false, throw a TypeError exception.
				if ( typeof predicate !== 'function' ) {
					throw new TypeError( 'predicate must be a function' );
				}

				// 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
				thisArg = arguments[ 1 ];

				// 5. Let k be 0.
				k = 0;

				// 6. Repeat, while k < len
				while ( k < len ) {
					// a. Let Pk be ! ToString(k).
					// b. Let kValue be ? Get(O, Pk).
					// c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
					// d. If testResult is true, return k.
					kValue = o[ k ];
					if ( predicate.call( thisArg, kValue, k, o ) ) {
						return k;
					}
					// e. Increase k by 1.
					k++;
				}

				// 7. Return -1.
				return -1;
			},
			configurable: true,
			writable: true
		} );
	}

	/**
	 * Function.prototype.bind()
	 *
	 * The bind() method creates a new function that, when called, has its this keyword set to the
	 * provided value, with a given sequence of arguments preceding any provided when the new function is called.
	 *
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function/bind
	 */
	if ( ! Function.prototype.bind ) {
		( function() {
			var ArrayPrototypeSlice = Array.prototype.slice;
			Function.prototype.bind = function() {
				var thatFunc = this;
				var thatArg = arguments[ 0 ];
				var args = ArrayPrototypeSlice.call( arguments, 1 );
				if ( typeof thatFunc !== 'function' ) {
					// closest thing possible to the ECMAScript 5
					// internal IsCallable function
					throw new TypeError( 'Function.prototype.bind - ' + 'what is trying to be bound is not callable' );
				}
				return function() {
					args.push.apply( args, arguments );
					return thatFunc.apply( thatArg, args );
				};
			};
		}() );
	}
}() );

// jQuery Mask Plugin v1.14.15
// github.com/igorescobar/jQuery-Mask-Plugin
window.jQuery.jMaskGlobals = {
	maskElements: '.tc-extra-product-options input'
};

/**
 * 2. themeComplete plugin JS API
 */

( function( $ ) {
	'use strict';

	$.epoAPI = {};
	$.epoAPI.error = false;
	$.epoAPI.math = {};
	$.epoAPI.dom = {};
	$.epoAPI.util = {};
	$.epoAPI.locale = {};
	$.epoAPI.template = {};

	$.epoAPI.math.toFloat = function( s ) {
		var n;

		if ( ! ( typeof s === 'string' || typeof s === 'number' ) || isNaN( s ) ) {
			return 0;
		}
		n = parseFloat( s );
		if ( isNaN( n ) ) {
			return s;
		}

		return n;
	};

	$.epoAPI.math.toInt = function( s ) {
		var n;

		if ( ! ( typeof s === 'string' || typeof s === 'number' ) || isNaN( s ) || s === '' ) {
			return 0;
		}
		n = parseInt( s, 10 );
		if ( isNaN( n ) ) {
			return s;
		}

		return n;
	};

	/**
	 * Rounds a number
	 * modified from http://locutus.io/php/math/round/
	 */
	$.epoAPI.math.round = function( value, precision, mode ) {
		var m;
		var f;
		var isHalf;
		var n;
		var sgn; // helper variables

		// making sure precision is integer
		precision = parseInt( precision, 10 );
		if ( ! Number.isFinite( precision ) ) {
			precision = 0;
		}
		m = Math.pow( 10, precision );
		value = value * m;

		// sign of the number
		if ( typeof value === 'number' ) {
			if ( value ) {
				if ( value < 0 ) {
					sgn = -1;
				} else {
					sgn = 1;
				}
			} else {
				sgn = 0;
			}
		} else {
			sgn = 0;
		}

		isHalf = value % 1 === 0.5 * sgn;
		f = Math.floor( value );

		if ( isHalf ) {
			switch ( mode ) {
				case 'PHP_ROUND_HALF_DOWN':
					// rounds .5 toward zero
					if ( sgn < 0 ) {
						n = 1;
					} else {
						n = 0;
					}
					value = f + n;
					break;
				case 'PHP_ROUND_HALF_EVEN':
					// rouds .5 towards the next even integer
					value = f + ( ( f % 2 ) * sgn );
					break;
				case 'PHP_ROUND_HALF_ODD':
					// rounds .5 towards the next odd integer
					n = f % 2;
					if ( n ) {
						n = 0;
					} else {
						n = 1;
					}
					value = f + n;
					break;
				default:
					// rounds .5 away from zero
					if ( sgn > 0 ) {
						n = 1;
					} else {
						n = 0;
					}
					value = f + n;
			}
		}

		if ( ! isHalf ) {
			value = Math.round( value );
		}

		return value / m;
	};

	$.epoAPI.dom.id = function( id ) {
		if ( typeof id === 'undefined' ) {
			return id;
		}
		if ( ! ( typeof id === 'string' || typeof id === 'number' ) || ( typeof id === 'number' && isNaN( id ) ) ) {
			return id.toString();
		}
		return id.toString().replace( /(%|:|\.|\[|\]|,|=)/g, '\\$1' );
	};

	$.epoAPI.dom.scroll = function() {
		var scrollLeft;
		var scrollTop;

		if ( window.pageYOffset ) {
			scrollTop = window.pageYOffset;
			scrollLeft = window.pageXOffset;
		} else if ( document.documentElement && document.documentElement.scrollTop ) {
			scrollTop = document.documentElement.scrollTop;
			scrollLeft = document.documentElement.scrollLeft;
		} else if ( document.body ) {
			scrollTop = document.body.scrollTop;
			scrollLeft = document.body.scrollLeft;
		}

		return { left: scrollLeft, top: scrollTop };
	};

	$.epoAPI.dom.size = function() {
		var totalDocumentHeight;
		var totalDocumentWidth;
		var fullHeight;
		var fullWidth;
		var visibleWidth;
		var visibleHeight;

		if ( window.innerHeight && window.scrollMaxY ) {
			totalDocumentWidth = window.innerWidth + window.scrollMaxX;
			totalDocumentHeight = window.innerHeight + window.scrollMaxY;
		} else if ( document.body.scrollHeight > document.body.offsetHeight ) {
			totalDocumentWidth = document.body.scrollWidth;
			totalDocumentHeight = document.body.scrollHeight;
		} else {
			totalDocumentWidth = document.body.offsetWidth;
			totalDocumentHeight = document.body.offsetHeight;
		}

		if ( window.innerHeight ) {
			if ( document.documentElement.clientWidth ) {
				visibleWidth = document.documentElement.clientWidth;
			} else {
				visibleWidth = window.innerWidth;
			}
			visibleHeight = window.innerHeight;
		} else if ( document.documentElement && document.documentElement.clientHeight ) {
			visibleWidth = document.documentElement.clientWidth;
			visibleHeight = document.documentElement.clientHeight;
		} else if ( document.body ) {
			visibleWidth = document.body.clientWidth;
			visibleHeight = document.body.clientHeight;
		}
		if ( totalDocumentHeight < visibleHeight ) {
			fullHeight = visibleHeight;
		} else {
			fullHeight = totalDocumentHeight;
		}
		if ( totalDocumentWidth < visibleWidth ) {
			fullWidth = visibleWidth;
		} else {
			fullWidth = totalDocumentWidth;
		}

		return {
			fullWidth: fullWidth,
			fullHeight: fullHeight,

			visibleWidth: visibleWidth,
			visibleHeight: visibleHeight,

			totalWidth: totalDocumentWidth,
			totalHeight: totalDocumentHeight
		};
	};

	// JQuery trim is deprecated, provide a trim method based on String.prototype.trim
	$.epoAPI.util.trim = function( str ) {
		if ( typeof str === 'string' ) {
			// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/trim#Polyfill
			return str.replace( /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '' );
		}
		return str;
	};

	$.epoAPI.util.parseJSON = function( s ) {
		var parsedjson;
		var JSON = window.JSON;

		try {
			parsedjson = JSON.parse( s + '' );
			if ( parsedjson && typeof parsedjson === 'object' && parsedjson !== null ) {
				return parsedjson;
			}
		} catch ( err ) {
			$.epoAPI.error = err;
			return false;
		}

		return false;
	};

	$.epoAPI.util.decodeHTML = function( html ) {
		var txt = document.createElement( 'textarea' );
		txt.innerHTML = html;
		return txt.value;
	};

	$.epoAPI.util.getStorage = function( type ) {
		var storage;
		var x;
		try {
			storage = window[ type ];
			x = '__storage_test__';
			storage.setItem( x, x );
			storage.removeItem( x );
			return storage;
		} catch ( e ) {
			return false;
		}
	};

	$.epoAPI.locale.getSystemDecimalSeparator = function() {
		var n = 1.1;

		n = /^1(.+)1$/.exec( n.toLocaleString() )[ 1 ];

		return n;
	};

	$.epoAPI.template.html = function( template, obj ) {
		var $template_html = template( obj );

		$template_html = $template_html.replace( '/*<![CDATA[*/', '' );
		$template_html = $template_html.replace( '/*]]>*/', '' );

		return $template_html;
	};

	$.epoAPI.filters = {};

	$.epoAPI.addFilter = function( $tag, $function_to_add, $priority, $accepted_args ) {
		var $idx;

		$priority = parseInt( $priority, 10 );
		if ( isNaN( $priority ) ) {
			$priority = 10;
		}
		$accepted_args = parseInt( $accepted_args, 10 );
		if ( isNaN( $accepted_args ) ) {
			$accepted_args = 1;
		}
		$idx = $function_to_add + '_' + $priority;
		if ( ! $.epoAPI.filters[ $tag ] ) {
			$.epoAPI.filters[ $tag ] = {};
		}
		if ( ! $.epoAPI.filters[ $tag ][ $priority ] ) {
			$.epoAPI.filters[ $tag ][ $priority ] = {};
		}
		$.epoAPI.filters[ $tag ][ $priority ][ $idx ] = {
			func: $function_to_add,
			accepted_args: $accepted_args
		};

		return true;
	};

	$.epoAPI.removeFilter = function( $tag, $function_to_remove, $priority ) {
		var $idx;
		$priority = parseInt( $priority, 10 );
		if ( isNaN( $priority ) ) {
			$priority = 10;
		}
		$idx = $function_to_remove + '_' + $priority;

		if ( $.epoAPI.filters[ $tag ] && $.epoAPI.filters[ $tag ][ $priority ] && $.epoAPI.filters[ $tag ][ $priority ][ $idx ] ) {
			delete $.epoAPI.filters[ $tag ][ $priority ][ $idx ];
			return true;
		}

		return false;
	};

	$.epoAPI.applyFilter = function( $tag, $value ) {
		var $args = $.makeArray( arguments );
		var priorities;

		$args.splice( 0, 1 );

		if ( ! $.epoAPI.filters[ $tag ] ) {
			return $value;
		}

		priorities = $.epoAPI.filters[ $tag ];
		$.each( priorities, function( i, el ) {
			$.each( el, function( i2, el2 ) {
				var func = el2.func;

				if ( func instanceof Function ) {
					$value = func.apply( null, $args );
				} else if ( window[ func ] && window[ func ] instanceof Function ) {
					$value = window[ func ].apply( null, $args );
				}
			} );
		} );

		return $value;
	};

	// Backwards compatibility (to be removed in the future)
	$.tc_add_filter = $.epoAPI.addFilter;
	$.tc_remove_filter = $.epoAPI.removeFilter;
	$.tc_apply_filters = $.epoAPI.applyFilter;
}( window.jQuery ) );

/*
 * Formatting functions
 *
 * Code modifiled from accounting.js
 * http://openexchangerates.github.io/accounting.js/
 */

( function( $ ) {
	'use strict';

	/**
	 * Check and normalise the value of precision (must be positive integer)
	 */
	function checkPrecision( val, base ) {
		val = Math.round( Math.abs( val ) );
		return isNaN( val ) ? base : val;
	}

	/**
	 * Takes a string/array of strings, removes all formatting/cruft and returns the raw float value
	 */
	function unformat( value, decimal ) {
		var regex;
		var unformatted;

		// Recursively unformat arrays:
		if ( Array.isArray( value ) ) {
			return value.map( value, function( val ) {
				return unformat( val, decimal );
			} );
		}

		// Fails silently (need decent errors):
		value = value || 0;

		// Return the value as-is if it's already a number:
		if ( typeof value === 'number' ) {
			return value;
		}

		// Default decimal point comes from settings, but could be set to eg. "," in opts:
		decimal = decimal || '.';

		// Build regex to strip out everything except digits, decimal point and minus sign:
		regex = new RegExp( '[^0-9-' + decimal + ']', [ 'g' ] );
		unformatted = parseFloat(
			( '' + value )
				.replace( /\((?=\d+)(.*)\)/, '-$1' ) // replace bracketed values with negatives
				.replace( regex, '' ) // strip out any cruft
				.replace( decimal, '.' ) // make sure decimal point is standard
		);

		// This will fail silently which may cause trouble, let's wait and see:
		return ! isNaN( unformatted ) ? unformatted : 0;
	}

	/**
	 * Implementation of toFixed() that treats floats more like decimals
	 *
	 * Fixes binary rounding issues (eg. (0.615).toFixed(2) === "0.61") that present
	 * problems for accounting- and finance-related software.
	 */
	function toFixed( value, precision ) {
		var exponentialForm;
		var rounded;
		var finalResult;

		precision = checkPrecision( precision, 2 );

		exponentialForm = Number( unformat( value ) + 'e' + precision );
		rounded = Math.round( exponentialForm );
		finalResult = Number( rounded + 'e-' + precision ).toFixed( precision );
		return finalResult;
	}

	/**
	 * Format into currency or a number, with comma-separated thousands and custom precision/decimal places
	 *
	 * opts = {
	 *     symbol: "$",    // currency symbol is '$'
	 *     format: "%s%v", // controls output: %s = symbol, %v = value (can be object, see docs)
	 *     decimal: ".",   // decimal point separator
	 *     thousand: ",",  // thousands separator
	 *     precision: 2,   // decimal places
	 * }
	 *
	 */
	function format( number, opts ) {
		var formats;
		var useFormat;
		var negative;
		var base;
		var mod;

		// Resursively format arrays:
		if ( Array.isArray( number ) ) {
			return number.map( number, function( val ) {
				return format( val, opts );
			} );
		}

		// Clean up number:
		number = unformat( number );

		// Do not format if opts are not correct or missing
		if ( ! opts ) {
			return number;
		}

		// Clean up precision
		opts.precision = checkPrecision( opts.precision );

		// Format currency
		if ( opts.format && opts.symbol ) {
			// Check format (returns object with pos, neg and zero):
			formats = {
				pos: opts.format,
				neg: opts.format.replace( '-', '' ).replace( '%v', '-%v' ),
				zero: opts.format
			};

			// Choose which format to use for this value:
			useFormat = number > 0 ? formats.pos : number < 0 ? formats.neg : formats.zero;

			// Return with currency symbol added:
			opts.opts = {
				precision: opts.precision,
				thousand: opts.thousand,
				decimal: opts.decimal
			};

			number = useFormat.replace( '%s', opts.symbol ).replace( '%v', format( Math.abs( number ), opts.opts ) );

			// Format number
		} else {
			// Do some calc:
			negative = number < 0 ? '-' : '';
			base = parseInt( toFixed( Math.abs( number || 0 ), opts.precision ), 10 ) + '';
			mod = base.length > 3 ? base.length % 3 : 0;

			// Format the number:
			number = negative + ( mod ? base.substr( 0, mod ) + opts.thousand : '' ) + base.substr( mod ).replace( /(\d{3})(?=\d)/g, '$1' + opts.thousand ) + ( opts.precision ? opts.decimal + toFixed( Math.abs( number ), opts.precision ).split( '.' )[ 1 ] : '' );
		}

		return number;
	}

	$.epoAPI.math.unformat = unformat;
	$.epoAPI.math.format = format;
	$.epoAPI.math.toFixed = toFixed;
}( window.jQuery ) );

/**
 * 3. themeComplete jQuery extensions
 *
 **/
( function( $ ) {
	'use strict';

	var expo;
	var rCRLF = /\r?\n/g;
	var rcheckableType = /^(?:checkbox|radio)$/i;
	var rsubmitterTypes = /^(?:submit|button|image|reset|file)$/i;
	var rsubmittable = /^(?:input|select|textarea|keygen)/i;

	// Extend jQuery easing
	if ( ! $.easing.easeInExpo ) {
		expo = function( p ) {
			return Math.pow( p, 6 );
		};
		$.easing.easeInExpo = expo;
		$.easing.easeOutExpo = function( p ) {
			return 1 - expo( 1 - p );
		};
		$.easing.easeInOutExpo = function( p ) {
			return p < 0.5 ? expo( p * 2 ) / 2 : 1 - ( expo( ( p * -2 ) + 2 ) / 2 );
		};
	}

	// Add jQuery plugins
	$.fn.extend( {
		// Create checkbox switch
		tmcheckboxes: function() {
			var tm_settings_wrap_checkbox = this.find( ":checkbox:not('.tm-default-checkbox')" ).not( '.wp-tab-panel :checkbox, .tm-weekdays-picker-wrap :checkbox, .tm-months-picker-wrap :checkbox' );
			tm_settings_wrap_checkbox.wrap( '<label class="tm-epo-switch-wrapper-label"></label>' );
			tm_settings_wrap_checkbox.wrap( '<span class="tm-epo-switch-wrapper tc"></span>' );
			tm_settings_wrap_checkbox.after( '<span class="tc-label tm-epo-switch tc"></span>' );
			return this;
		},

		// Encode a set of HTML elements as an array of names and values.
		// Elements do not need to be inside a form
		tcSerializeArray: function() {
			return this.find( ':input' )
				.filter( function() {
					var type = this.type;

					// Use .is( ":disabled" ) so that fieldset[disabled] works
					return this.name && ! $( this ).is( ':disabled' ) && rsubmittable.test( this.nodeName ) && ! rsubmitterTypes.test( type ) && ( this.checked || ! rcheckableType.test( type ) );
				} )
				.map( function( i, elem ) {
					var val = $( this ).val();

					if ( val === null ) {
						return null;
					}

					if ( Array.isArray( val ) ) {
						return $.map( val, function( thisval ) {
							return { name: elem.name, value: thisval.replace( rCRLF, '\r\n' ) };
						} );
					}

					return { name: elem.name, value: val.replace( rCRLF, '\r\n' ) };
				} )
				.get();
		},

		// convert element to a valid JSON object
		tcSerializeObject: function() {
			var o = {};
			var a = this.tcSerializeArray();

			$.each( a, function() {
				if ( o[ this.name ] !== undefined ) {
					if ( this.name.endsWith( '[]' ) ) {
						if ( ! o[ this.name ].push ) {
							o[ this.name ] = [ o[ this.name ] ];
						}
						o[ this.name ].push( this.value || '' );
					} else {
						o[ this.name ] = this.value || '';
					}
				} else {
					o[ this.name ] = this.value || '';
				}
			} );

			return o;
		},

		// Scroll container to a specific element
		tcScrollTo: function( obj, duration, offset ) {
			var element = this;

			obj = $( obj );
			if ( obj.length === 0 ) {
				return this;
			}
			if ( ! duration ) {
				duration = 0;
			}
			if ( ! offset ) {
				offset = 0;
			}
			if ( element[ 0 ].self === window ) {
				element = $( 'html, body' );
			} else {
				if ( element.find( '.woodmart-scroll-content' ).length ) {
					element = element.find( '.woodmart-scroll-content' );
				}
				if ( ! element.offset() ) {
					element = $( 'html, body' );
				} else {
					offset = offset + ( element.scrollTop() - element.offset().top );
				}
			}

			return element.animate(
				{
					scrollTop: $( obj ).offset().top + offset
				},
				duration
			);
		},

		/**
		 * Textarea and select clone() bug workaround | Spencer Tipping
		 * Licensed under the terms of the MIT source code license
		 * https://github.com/spencertipping/jquery.fix.clone/blob/master/jquery.fix.clone.js
		 */
		tcClone: function() {
			var i;
			var l;
			var j;
			var m;
			var result = $.fn.clone.apply( this, arguments );
			var textareas = this.find( 'textarea' ).add( this.filter( 'textarea' ) );
			var resultTextareas = result.find( 'textarea' ).add( result.filter( 'textarea' ) );
			var selects = this.find( 'select' ).add( this.filter( 'select' ) );
			var resultSelects = result.find( 'select' ).add( result.filter( 'select' ) );

			for ( i = 0, l = textareas.length; i < l; i += 1 ) {
				$( resultTextareas[ i ] ).val( $( textareas[ i ] ).val() );
			}
			for ( i = 0, l = selects.length; i < l; i += 1 ) {
				for ( j = 0, m = selects[ i ].options.length; j < m; j += 1 ) {
					if ( selects[ i ].options[ j ].selected === true ) {
						resultSelects[ i ].options[ j ].selected = true;
					}
				}
			}

			return result;
		}
	} );
}( window.jQuery ) );
