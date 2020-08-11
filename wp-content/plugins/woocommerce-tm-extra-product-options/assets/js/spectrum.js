// TinyColor v1.4.1
// https://github.com/bgrins/TinyColor
// Brian Grinstead, MIT License
// reformatted code by themeComplete
( function( Math ) {
	'use strict';

	var trimLeft = /^\s+/,
		trimRight = /\s+$/,
		tinyCounter = 0,
		mathRound = Math.round,
		mathMin = Math.min,
		mathMax = Math.max,
		mathRandom = Math.random;

	var names = {
		aliceblue: 'f0f8ff',
		antiquewhite: 'faebd7',
		aqua: '0ff',
		aquamarine: '7fffd4',
		azure: 'f0ffff',
		beige: 'f5f5dc',
		bisque: 'ffe4c4',
		black: '000',
		blanchedalmond: 'ffebcd',
		blue: '00f',
		blueviolet: '8a2be2',
		brown: 'a52a2a',
		burlywood: 'deb887',
		burntsienna: 'ea7e5d',
		cadetblue: '5f9ea0',
		chartreuse: '7fff00',
		chocolate: 'd2691e',
		coral: 'ff7f50',
		cornflowerblue: '6495ed',
		cornsilk: 'fff8dc',
		crimson: 'dc143c',
		cyan: '0ff',
		darkblue: '00008b',
		darkcyan: '008b8b',
		darkgoldenrod: 'b8860b',
		darkgray: 'a9a9a9',
		darkgreen: '006400',
		darkgrey: 'a9a9a9',
		darkkhaki: 'bdb76b',
		darkmagenta: '8b008b',
		darkolivegreen: '556b2f',
		darkorange: 'ff8c00',
		darkorchid: '9932cc',
		darkred: '8b0000',
		darksalmon: 'e9967a',
		darkseagreen: '8fbc8f',
		darkslateblue: '483d8b',
		darkslategray: '2f4f4f',
		darkslategrey: '2f4f4f',
		darkturquoise: '00ced1',
		darkviolet: '9400d3',
		deeppink: 'ff1493',
		deepskyblue: '00bfff',
		dimgray: '696969',
		dimgrey: '696969',
		dodgerblue: '1e90ff',
		firebrick: 'b22222',
		floralwhite: 'fffaf0',
		forestgreen: '228b22',
		fuchsia: 'f0f',
		gainsboro: 'dcdcdc',
		ghostwhite: 'f8f8ff',
		gold: 'ffd700',
		goldenrod: 'daa520',
		gray: '808080',
		green: '008000',
		greenyellow: 'adff2f',
		grey: '808080',
		honeydew: 'f0fff0',
		hotpink: 'ff69b4',
		indianred: 'cd5c5c',
		indigo: '4b0082',
		ivory: 'fffff0',
		khaki: 'f0e68c',
		lavender: 'e6e6fa',
		lavenderblush: 'fff0f5',
		lawngreen: '7cfc00',
		lemonchiffon: 'fffacd',
		lightblue: 'add8e6',
		lightcoral: 'f08080',
		lightcyan: 'e0ffff',
		lightgoldenrodyellow: 'fafad2',
		lightgray: 'd3d3d3',
		lightgreen: '90ee90',
		lightgrey: 'd3d3d3',
		lightpink: 'ffb6c1',
		lightsalmon: 'ffa07a',
		lightseagreen: '20b2aa',
		lightskyblue: '87cefa',
		lightslategray: '789',
		lightslategrey: '789',
		lightsteelblue: 'b0c4de',
		lightyellow: 'ffffe0',
		lime: '0f0',
		limegreen: '32cd32',
		linen: 'faf0e6',
		magenta: 'f0f',
		maroon: '800000',
		mediumaquamarine: '66cdaa',
		mediumblue: '0000cd',
		mediumorchid: 'ba55d3',
		mediumpurple: '9370db',
		mediumseagreen: '3cb371',
		mediumslateblue: '7b68ee',
		mediumspringgreen: '00fa9a',
		mediumturquoise: '48d1cc',
		mediumvioletred: 'c71585',
		midnightblue: '191970',
		mintcream: 'f5fffa',
		mistyrose: 'ffe4e1',
		moccasin: 'ffe4b5',
		navajowhite: 'ffdead',
		navy: '000080',
		oldlace: 'fdf5e6',
		olive: '808000',
		olivedrab: '6b8e23',
		orange: 'ffa500',
		orangered: 'ff4500',
		orchid: 'da70d6',
		palegoldenrod: 'eee8aa',
		palegreen: '98fb98',
		paleturquoise: 'afeeee',
		palevioletred: 'db7093',
		papayawhip: 'ffefd5',
		peachpuff: 'ffdab9',
		peru: 'cd853f',
		pink: 'ffc0cb',
		plum: 'dda0dd',
		powderblue: 'b0e0e6',
		purple: '800080',
		rebeccapurple: '663399',
		red: 'f00',
		rosybrown: 'bc8f8f',
		royalblue: '4169e1',
		saddlebrown: '8b4513',
		salmon: 'fa8072',
		sandybrown: 'f4a460',
		seagreen: '2e8b57',
		seashell: 'fff5ee',
		sienna: 'a0522d',
		silver: 'c0c0c0',
		skyblue: '87ceeb',
		slateblue: '6a5acd',
		slategray: '708090',
		slategrey: '708090',
		snow: 'fffafa',
		springgreen: '00ff7f',
		steelblue: '4682b4',
		tan: 'd2b48c',
		teal: '008080',
		thistle: 'd8bfd8',
		tomato: 'ff6347',
		turquoise: '40e0d0',
		violet: 'ee82ee',
		wheat: 'f5deb3',
		white: 'fff',
		whitesmoke: 'f5f5f5',
		yellow: 'ff0',
		yellowgreen: '9acd32'
	};

	// `{ 'name1': 'val1' }` becomes `{ 'val1': 'name1' }`
	var flip = function( o ) {
		var flipped = {};
		var i;
		for ( i in o ) {
			if ( Object.prototype.hasOwnProperty.call( o, i ) ) {
				flipped[ o[ i ] ] = i;
			}
		}
		return flipped;
	};

	var hexNames = flip( names );

	var matchers = ( function() {
		// <http://www.w3.org/TR/css3-values/#integers>
		var CSS_INTEGER = '[-\\+]?\\d+%?';

		// <http://www.w3.org/TR/css3-values/#number-value>
		var CSS_NUMBER = '[-\\+]?\\d*\\.\\d+%?';

		// Allow positive/negative integer/number.  Don't capture the either/or, just the entire outcome.
		var CSS_UNIT = '(?:' + CSS_NUMBER + ')|(?:' + CSS_INTEGER + ')';

		// Actual matching.
		// Parentheses and commas are optional, but not required.
		// Whitespace can take the place of commas or opening paren
		var PERMISSIVE_MATCH3 = '[\\s|\\(]+(' + CSS_UNIT + ')[,|\\s]+(' + CSS_UNIT + ')[,|\\s]+(' + CSS_UNIT + ')\\s*\\)?';
		var PERMISSIVE_MATCH4 = '[\\s|\\(]+(' + CSS_UNIT + ')[,|\\s]+(' + CSS_UNIT + ')[,|\\s]+(' + CSS_UNIT + ')[,|\\s]+(' + CSS_UNIT + ')\\s*\\)?';

		return {
			CSS_UNIT: new RegExp( CSS_UNIT ),
			rgb: new RegExp( 'rgb' + PERMISSIVE_MATCH3 ),
			rgba: new RegExp( 'rgba' + PERMISSIVE_MATCH4 ),
			hsl: new RegExp( 'hsl' + PERMISSIVE_MATCH3 ),
			hsla: new RegExp( 'hsla' + PERMISSIVE_MATCH4 ),
			hsv: new RegExp( 'hsv' + PERMISSIVE_MATCH3 ),
			hsva: new RegExp( 'hsva' + PERMISSIVE_MATCH4 ),
			hex3: /^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,
			hex6: /^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/,
			hex4: /^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,
			hex8: /^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/
		};
	}() );

	// `isValidCSSUnit`
	// Take in a single string / number and check to see if it looks like a CSS unit
	// (see `matchers` above for definition).
	function isValidCSSUnit( color ) {
		return !! matchers.CSS_UNIT.exec( color );
	}

	// Parse a base-16 hex value into a base-10 integer
	function parseIntFromHex( val ) {
		return parseInt( val, 16 );
	}

	// Converts a hex value to a decimal
	function convertHexToDecimal( h ) {
		return parseIntFromHex( h ) / 255;
	}

	// `stringInputToObject`
	// Permissive string parsing.  Take in a number of formats, and output an object
	// based on detected format.  Returns `{ r, g, b }` or `{ h, s, l }` or `{ h, s, v}`
	function stringInputToObject( color ) {
		var named = false;
		var match;
		color = color.replace( trimLeft, '' ).replace( trimRight, '' ).toLowerCase();

		if ( names[ color ] ) {
			color = names[ color ];
			named = true;
		} else if ( color === 'transparent' ) {
			return { r: 0, g: 0, b: 0, a: 0, format: 'name' };
		}

		// Try to match string input using regular expressions.
		// Keep most of the number bounding out of this function - don't worry about [0,1] or [0,100] or [0,360]
		// Just return an object and let the conversion functions handle that.
		// This way the result will be the same whether the TinyColor is initialized with string or object.

		if ( ( match = matchers.rgb.exec( color ) ) ) {
			return { r: match[ 1 ], g: match[ 2 ], b: match[ 3 ] };
		}
		if ( ( match = matchers.rgba.exec( color ) ) ) {
			return { r: match[ 1 ], g: match[ 2 ], b: match[ 3 ], a: match[ 4 ] };
		}
		if ( ( match = matchers.hsl.exec( color ) ) ) {
			return { h: match[ 1 ], s: match[ 2 ], l: match[ 3 ] };
		}
		if ( ( match = matchers.hsla.exec( color ) ) ) {
			return { h: match[ 1 ], s: match[ 2 ], l: match[ 3 ], a: match[ 4 ] };
		}
		if ( ( match = matchers.hsv.exec( color ) ) ) {
			return { h: match[ 1 ], s: match[ 2 ], v: match[ 3 ] };
		}
		if ( ( match = matchers.hsva.exec( color ) ) ) {
			return { h: match[ 1 ], s: match[ 2 ], v: match[ 3 ], a: match[ 4 ] };
		}
		if ( ( match = matchers.hex8.exec( color ) ) ) {
			return {
				r: parseIntFromHex( match[ 1 ] ),
				g: parseIntFromHex( match[ 2 ] ),
				b: parseIntFromHex( match[ 3 ] ),
				a: convertHexToDecimal( match[ 4 ] ),
				format: named ? 'name' : 'hex8'
			};
		}
		if ( ( match = matchers.hex6.exec( color ) ) ) {
			return {
				r: parseIntFromHex( match[ 1 ] ),
				g: parseIntFromHex( match[ 2 ] ),
				b: parseIntFromHex( match[ 3 ] ),
				format: named ? 'name' : 'hex'
			};
		}
		if ( ( match = matchers.hex4.exec( color ) ) ) {
			return {
				r: parseIntFromHex( match[ 1 ] + '' + match[ 1 ] ),
				g: parseIntFromHex( match[ 2 ] + '' + match[ 2 ] ),
				b: parseIntFromHex( match[ 3 ] + '' + match[ 3 ] ),
				a: convertHexToDecimal( match[ 4 ] + '' + match[ 4 ] ),
				format: named ? 'name' : 'hex8'
			};
		}
		if ( ( match = matchers.hex3.exec( color ) ) ) {
			return {
				r: parseIntFromHex( match[ 1 ] + '' + match[ 1 ] ),
				g: parseIntFromHex( match[ 2 ] + '' + match[ 2 ] ),
				b: parseIntFromHex( match[ 3 ] + '' + match[ 3 ] ),
				format: named ? 'name' : 'hex'
			};
		}

		return false;
	}

	function validateWCAG2Parms( parms ) {
		// return valid WCAG2 parms for isReadable.
		// If input parms are invalid, return {"level":"AA", "size":"small"}
		var level, size;
		parms = parms || { level: 'AA', size: 'small' };
		level = ( parms.level || 'AA' ).toUpperCase();
		size = ( parms.size || 'small' ).toLowerCase();
		if ( level !== 'AA' && level !== 'AAA' ) {
			level = 'AA';
		}
		if ( size !== 'small' && size !== 'large' ) {
			size = 'small';
		}
		return { level: level, size: size };
	}

	// Utilities
	// ---------

	// Return a valid alpha value [0,1] with all invalid values being set to 1
	function boundAlpha( a ) {
		a = parseFloat( a );

		if ( isNaN( a ) || a < 0 || a > 1 ) {
			a = 1;
		}

		return a;
	}

	// Check to see if string passed in is a percentage
	function isPercentage( n ) {
		return typeof n === 'string' && n.indexOf( '%' ) !== -1;
	}

	// Need to handle 1.0 as 100%, since once it is a number, there is no difference between it and 1
	// <http://stackoverflow.com/questions/7422072/javascript-how-to-detect-number-as-a-decimal-including-1-0>
	function isOnePointZero( n ) {
		return typeof n === 'string' && n.indexOf( '.' ) !== -1 && parseFloat( n ) === 1;
	}

	// Take input from [0, n] and return it as [0, 1]
	function bound01( n, max ) {
		var processPercent;
		if ( isOnePointZero( n ) ) {
			n = '100%';
		}

		processPercent = isPercentage( n );
		n = mathMin( max, mathMax( 0, parseFloat( n ) ) );

		// Automatically convert percentage into number
		if ( processPercent ) {
			n = parseInt( n * max, 10 ) / 100;
		}

		// Handle floating point rounding errors
		if ( Math.abs( n - max ) < 0.000001 ) {
			return 1;
		}

		// Convert into [0, 1] range if it isn't already
		return ( n % max ) / parseFloat( max );
	}

	// Force a number between 0 and 1
	function clamp01( val ) {
		return mathMin( 1, mathMax( 0, val ) );
	}

	// Force a hex value to have 2 characters
	function pad2( c ) {
		return c.length === 1 ? '0' + c : '' + c;
	}

	// Replace a decimal with it's percentage value
	function convertToPercentage( n ) {
		if ( n <= 1 ) {
			n = ( n * 100 ) + '%';
		}

		return n;
	}

	// Converts a decimal to a hex value
	function convertDecimalToHex( d ) {
		return Math.round( parseFloat( d ) * 255 ).toString( 16 );
	}

	// Conversion Functions
	// --------------------

	// `rgbToHsl`, `rgbToHsv`, `hslToRgb`, `hsvToRgb` modified from:
	// <http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript>

	// `rgbToRgb`
	// Handle bounds / percentage checking to conform to CSS color spec
	// <http://www.w3.org/TR/css3-color/>
	// *Assumes:* r, g, b in [0, 255] or [0, 1]
	// *Returns:* { r, g, b } in [0, 255]
	function rgbToRgb( r, g, b ) {
		return {
			r: bound01( r, 255 ) * 255,
			g: bound01( g, 255 ) * 255,
			b: bound01( b, 255 ) * 255
		};
	}

	// `rgbToHsl`
	// Converts an RGB color value to HSL.
	// *Assumes:* r, g, and b are contained in [0, 255] or [0, 1]
	// *Returns:* { h, s, l } in [0,1]
	function rgbToHsl( r, g, b ) {
		var max;
		var min;
		var h;
		var s;
		var l;
		var d;

		r = bound01( r, 255 );
		g = bound01( g, 255 );
		b = bound01( b, 255 );

		max = mathMax( r, g, b );
		min = mathMin( r, g, b );
		l = ( max + min ) / 2;

		if ( max === min ) {
			h = s = 0; // achromatic
		} else {
			d = max - min;
			s = l > 0.5 ? d / ( 2 - max - min ) : d / ( max + min );
			switch ( max ) {
				case r:
					h = ( ( g - b ) / d ) + ( g < b ? 6 : 0 );
					break;
				case g:
					h = ( ( b - r ) / d ) + 2;
					break;
				case b:
					h = ( ( r - g ) / d ) + 4;
					break;
			}

			h /= 6;
		}

		return { h: h, s: s, l: l };
	}

	// `hslToRgb`
	// Converts an HSL color value to RGB.
	// *Assumes:* h is contained in [0, 1] or [0, 360] and s and l are contained [0, 1] or [0, 100]
	// *Returns:* { r, g, b } in the set [0, 255]
	function hslToRgb( h, s, l ) {
		var r;
		var g;
		var b;
		var q;
		var p;

		h = bound01( h, 360 );
		s = bound01( s, 100 );
		l = bound01( l, 100 );

		function hue2rgb( pp, qq, t ) {
			if ( t < 0 ) {
				t += 1;
			}
			if ( t > 1 ) {
				t -= 1;
			}
			if ( t < 1 / 6 ) {
				return pp + ( ( qq - pp ) * 6 * t );
			}
			if ( t < 1 / 2 ) {
				return qq;
			}
			if ( t < 2 / 3 ) {
				return pp + ( ( qq - pp ) * ( ( 2 / 3 ) - t ) * 6 );
			}
			return pp;
		}

		if ( s === 0 ) {
			r = g = b = l; // achromatic
		} else {
			q = l < 0.5 ? l * ( 1 + s ) : l + s - ( l * s );
			p = ( 2 * l ) - q;
			r = hue2rgb( p, q, h + ( 1 / 3 ) );
			g = hue2rgb( p, q, h );
			b = hue2rgb( p, q, h - ( 1 / 3 ) );
		}

		return { r: r * 255, g: g * 255, b: b * 255 };
	}

	// `rgbToHsv`
	// Converts an RGB color value to HSV
	// *Assumes:* r, g, and b are contained in the set [0, 255] or [0, 1]
	// *Returns:* { h, s, v } in [0,1]
	function rgbToHsv( r, g, b ) {
		var max;
		var min;
		var h;
		var s;
		var v;
		var d;

		r = bound01( r, 255 );
		g = bound01( g, 255 );
		b = bound01( b, 255 );

		max = mathMax( r, g, b );
		min = mathMin( r, g, b );
		v = max;

		d = max - min;
		s = max === 0 ? 0 : d / max;

		if ( max === min ) {
			h = 0; // achromatic
		} else {
			switch ( max ) {
				case r:
					h = ( ( g - b ) / d ) + ( g < b ? 6 : 0 );
					break;
				case g:
					h = ( ( b - r ) / d ) + 2;
					break;
				case b:
					h = ( ( r - g ) / d ) + 4;
					break;
			}
			h /= 6;
		}
		return { h: h, s: s, v: v };
	}

	// `hsvToRgb`
	// Converts an HSV color value to RGB.
	// *Assumes:* h is contained in [0, 1] or [0, 360] and s and v are contained in [0, 1] or [0, 100]
	// *Returns:* { r, g, b } in the set [0, 255]
	function hsvToRgb( h, s, v ) {
		var i;
		var f;
		var p;
		var q;
		var t;
		var mod;
		var r;
		var g;
		var b;

		h = bound01( h, 360 ) * 6;
		s = bound01( s, 100 );
		v = bound01( v, 100 );

		i = Math.floor( h );
		f = h - i;
		p = v * ( 1 - s );
		q = v * ( 1 - ( f * s ) );
		t = v * ( 1 - ( ( 1 - f ) * s ) );
		mod = i % 6;
		r = [ v, q, p, p, t, v ][ mod ];
		g = [ t, v, v, q, p, p ][ mod ];
		b = [ p, p, t, v, v, q ][ mod ];

		return { r: r * 255, g: g * 255, b: b * 255 };
	}

	// `rgbToHex`
	// Converts an RGB color to hex
	// Assumes r, g, and b are contained in the set [0, 255]
	// Returns a 3 or 6 character hex
	function rgbToHex( r, g, b, allow3Char ) {
		var hex = [ pad2( mathRound( r ).toString( 16 ) ), pad2( mathRound( g ).toString( 16 ) ), pad2( mathRound( b ).toString( 16 ) ) ];

		// Return a 3 character hex if possible
		if ( allow3Char && hex[ 0 ].charAt( 0 ) === hex[ 0 ].charAt( 1 ) && hex[ 1 ].charAt( 0 ) === hex[ 1 ].charAt( 1 ) && hex[ 2 ].charAt( 0 ) === hex[ 2 ].charAt( 1 ) ) {
			return hex[ 0 ].charAt( 0 ) + hex[ 1 ].charAt( 0 ) + hex[ 2 ].charAt( 0 );
		}

		return hex.join( '' );
	}

	// `rgbaToHex`
	// Converts an RGBA color plus alpha transparency to hex
	// Assumes r, g, b are contained in the set [0, 255] and
	// a in [0, 1]. Returns a 4 or 8 character rgba hex
	function rgbaToHex( r, g, b, a, allow4Char ) {
		var hex = [ pad2( mathRound( r ).toString( 16 ) ), pad2( mathRound( g ).toString( 16 ) ), pad2( mathRound( b ).toString( 16 ) ), pad2( convertDecimalToHex( a ) ) ];

		// Return a 4 character hex if possible
		if ( allow4Char && hex[ 0 ].charAt( 0 ) === hex[ 0 ].charAt( 1 ) && hex[ 1 ].charAt( 0 ) === hex[ 1 ].charAt( 1 ) && hex[ 2 ].charAt( 0 ) === hex[ 2 ].charAt( 1 ) && hex[ 3 ].charAt( 0 ) === hex[ 3 ].charAt( 1 ) ) {
			return hex[ 0 ].charAt( 0 ) + hex[ 1 ].charAt( 0 ) + hex[ 2 ].charAt( 0 ) + hex[ 3 ].charAt( 0 );
		}

		return hex.join( '' );
	}

	// `rgbaToArgbHex`
	// Converts an RGBA color to an ARGB Hex8 string
	// Rarely used, but required for "toFilter()"
	function rgbaToArgbHex( r, g, b, a ) {
		var hex = [ pad2( convertDecimalToHex( a ) ), pad2( mathRound( r ).toString( 16 ) ), pad2( mathRound( g ).toString( 16 ) ), pad2( mathRound( b ).toString( 16 ) ) ];

		return hex.join( '' );
	}

	// Given a string or object, convert that input to RGB
	// Possible string inputs:
	//
	//     "red"
	//     "#f00" or "f00"
	//     "#ff0000" or "ff0000"
	//     "#ff000000" or "ff000000"
	//     "rgb 255 0 0" or "rgb (255, 0, 0)"
	//     "rgb 1.0 0 0" or "rgb (1, 0, 0)"
	//     "rgba (255, 0, 0, 1)" or "rgba 255, 0, 0, 1"
	//     "rgba (1.0, 0, 0, 1)" or "rgba 1.0, 0, 0, 1"
	//     "hsl(0, 100%, 50%)" or "hsl 0 100% 50%"
	//     "hsla(0, 100%, 50%, 1)" or "hsla 0 100% 50%, 1"
	//     "hsv(0, 100%, 100%)" or "hsv 0 100% 100%"
	//
	function inputToRGB( color ) {
		var rgb = { r: 0, g: 0, b: 0 };
		var a = 1;
		var s = null;
		var v = null;
		var l = null;
		var ok = false;
		var format = false;

		if ( typeof color === 'string' ) {
			color = stringInputToObject( color );
		}

		if ( typeof color === 'object' ) {
			if ( isValidCSSUnit( color.r ) && isValidCSSUnit( color.g ) && isValidCSSUnit( color.b ) ) {
				rgb = rgbToRgb( color.r, color.g, color.b );
				ok = true;
				format = String( color.r ).substr( -1 ) === '%' ? 'prgb' : 'rgb';
			} else if ( isValidCSSUnit( color.h ) && isValidCSSUnit( color.s ) && isValidCSSUnit( color.v ) ) {
				s = convertToPercentage( color.s );
				v = convertToPercentage( color.v );
				rgb = hsvToRgb( color.h, s, v );
				ok = true;
				format = 'hsv';
			} else if ( isValidCSSUnit( color.h ) && isValidCSSUnit( color.s ) && isValidCSSUnit( color.l ) ) {
				s = convertToPercentage( color.s );
				l = convertToPercentage( color.l );
				rgb = hslToRgb( color.h, s, l );
				ok = true;
				format = 'hsl';
			}

			if ( Object.prototype.hasOwnProperty.call( color, 'a' ) ) {
				a = color.a;
			}
		}

		a = boundAlpha( a );

		return {
			ok: ok,
			format: color.format || format,
			r: mathMin( 255, mathMax( rgb.r, 0 ) ),
			g: mathMin( 255, mathMax( rgb.g, 0 ) ),
			b: mathMin( 255, mathMax( rgb.b, 0 ) ),
			a: a
		};
	}

	function TinyColor( color, opts ) {
		var rgb;
		color = color ? color : '';
		opts = opts || {};

		// If input is already a TinyColor, return itself
		if ( color instanceof TinyColor ) {
			return color;
		}
		// If we are called as a function, call using new instead
		if ( ! ( this instanceof TinyColor ) ) {
			return new TinyColor( color, opts );
		}

		rgb = inputToRGB( color );
		this._originalInput = color;
		this._r = rgb.r;
		this._g = rgb.g;
		this._b = rgb.b;
		this._a = rgb.a;
		this._roundA = mathRound( 100 * this._a ) / 100;
		if ( opts.format ) {
			this._format = opts.format;
		} else {
			this._format = rgb.format;
		}

		this._gradientType = opts.gradientType;

		// Don't let the range of [0,255] come back in [0,1].
		// Potentially lose a little bit of precision here, but will fix issues where
		// .5 gets interpreted as half of the total, instead of half of 1
		// If it was supposed to be 128, this was already taken care of by `inputToRgb`
		if ( this._r < 1 ) {
			this._r = mathRound( this._r );
		}
		if ( this._g < 1 ) {
			this._g = mathRound( this._g );
		}
		if ( this._b < 1 ) {
			this._b = mathRound( this._b );
		}

		this._ok = rgb.ok;
		tinyCounter = tinyCounter + 1;
		this._tc_id = tinyCounter;
	}

	function newTinyColor( color, opts ) {
		return new TinyColor( color, opts );
	}

	// Modification Functions
	// ----------------------
	// Thanks to less.js for some of the basics here
	// <https://github.com/cloudhead/less.js/blob/master/lib/less/functions.js>

	function desaturate( color, amount ) {
		var hsl;
		amount = amount === 0 ? 0 : amount || 10;
		hsl = newTinyColor( color ).toHsl();
		hsl.s -= amount / 100;
		hsl.s = clamp01( hsl.s );
		return newTinyColor( hsl );
	}

	function saturate( color, amount ) {
		var hsl;
		amount = amount === 0 ? 0 : amount || 10;
		hsl = newTinyColor( color ).toHsl();
		hsl.s += amount / 100;
		hsl.s = clamp01( hsl.s );
		return newTinyColor( hsl );
	}

	function greyscale( color ) {
		return newTinyColor( color ).desaturate( 100 );
	}

	function lighten( color, amount ) {
		var hsl;
		amount = amount === 0 ? 0 : amount || 10;
		hsl = newTinyColor( color ).toHsl();
		hsl.l += amount / 100;
		hsl.l = clamp01( hsl.l );
		return newTinyColor( hsl );
	}

	function brighten( color, amount ) {
		var rgb;
		amount = amount === 0 ? 0 : amount || 10;
		rgb = newTinyColor( color ).toRgb();
		rgb.r = mathMax( 0, mathMin( 255, rgb.r - mathRound( 255 * -( amount / 100 ) ) ) );
		rgb.g = mathMax( 0, mathMin( 255, rgb.g - mathRound( 255 * -( amount / 100 ) ) ) );
		rgb.b = mathMax( 0, mathMin( 255, rgb.b - mathRound( 255 * -( amount / 100 ) ) ) );
		return newTinyColor( rgb );
	}

	function darken( color, amount ) {
		var hsl;
		amount = amount === 0 ? 0 : amount || 10;
		hsl = newTinyColor( color ).toHsl();
		hsl.l -= amount / 100;
		hsl.l = clamp01( hsl.l );
		return newTinyColor( hsl );
	}

	// Spin takes a positive or negative amount within [-360, 360] indicating the change of hue.
	// Values outside of this range will be wrapped into this range.
	function spin( color, amount ) {
		var hsl = newTinyColor( color ).toHsl();
		var hue = ( hsl.h + amount ) % 360;
		hsl.h = hue < 0 ? 360 + hue : hue;
		return newTinyColor( hsl );
	}

	// Combination Functions
	// ---------------------
	// Thanks to jQuery xColor for some of the ideas behind these
	// <https://github.com/infusion/jQuery-xcolor/blob/master/jquery.xcolor.js>

	function complement( color ) {
		var hsl = newTinyColor( color ).toHsl();
		hsl.h = ( hsl.h + 180 ) % 360;
		return newTinyColor( hsl );
	}

	function triad( color ) {
		var hsl = newTinyColor( color ).toHsl();
		var h = hsl.h;
		return [ newTinyColor( color ), newTinyColor( { h: ( h + 120 ) % 360, s: hsl.s, l: hsl.l } ), newTinyColor( { h: ( h + 240 ) % 360, s: hsl.s, l: hsl.l } ) ];
	}

	function tetrad( color ) {
		var hsl = newTinyColor( color ).toHsl();
		var h = hsl.h;
		return [ newTinyColor( color ), newTinyColor( { h: ( h + 90 ) % 360, s: hsl.s, l: hsl.l } ), newTinyColor( { h: ( h + 180 ) % 360, s: hsl.s, l: hsl.l } ), newTinyColor( { h: ( h + 270 ) % 360, s: hsl.s, l: hsl.l } ) ];
	}

	function splitcomplement( color ) {
		var hsl = newTinyColor( color ).toHsl();
		var h = hsl.h;
		return [ newTinyColor( color ), newTinyColor( { h: ( h + 72 ) % 360, s: hsl.s, l: hsl.l } ), newTinyColor( { h: ( h + 216 ) % 360, s: hsl.s, l: hsl.l } ) ];
	}

	/*function analogous(color, results, slices) {
     results = results || 6;
     slices = slices || 30;

     var hsl = newTinyColor(color).toHsl();
     var part = 360 / slices;
     var ret = [newTinyColor(color)];

     for (hsl.h = ((hsl.h - (part * results >> 1)) + 720) % 360; --results;) {
     hsl.h = (hsl.h + part) % 360;
     ret.push(newTinyColor(hsl));
     }
     return ret;
     }

     function monochromatic(color, results) {
     var hsv;
     var h;
     var s;
     var v;
     var ret;
     var modification;
     results = results || 6;
     hsv = newTinyColor(color).toHsv();
     h = hsv.h;
     s = hsv.s;
     v = hsv.v;
     ret = [];
     modification = 1 / results;

     while (results--) {
     ret.push(newTinyColor({h: h, s: s, v: v}));
     v = (v + modification) % 1;
     }

     return ret;
     }*/

	TinyColor.prototype = {
		isDark: function() {
			return this.getBrightness() < 128;
		},
		isLight: function() {
			return ! this.isDark();
		},
		isValid: function() {
			return this._ok;
		},
		getOriginalInput: function() {
			return this._originalInput;
		},
		getFormat: function() {
			return this._format;
		},
		getAlpha: function() {
			return this._a;
		},
		getBrightness: function() {
			//http://www.w3.org/TR/AERT#color-contrast
			var rgb = this.toRgb();
			return ( ( rgb.r * 299 ) + ( rgb.g * 587 ) + ( rgb.b * 114 ) ) / 1000;
		},
		getLuminance: function() {
			//http://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef
			var rgb = this.toRgb();
			var RsRGB, GsRGB, BsRGB, R, G, B;
			RsRGB = rgb.r / 255;
			GsRGB = rgb.g / 255;
			BsRGB = rgb.b / 255;

			if ( RsRGB <= 0.03928 ) {
				R = RsRGB / 12.92;
			} else {
				R = Math.pow( ( RsRGB + 0.055 ) / 1.055, 2.4 );
			}
			if ( GsRGB <= 0.03928 ) {
				G = GsRGB / 12.92;
			} else {
				G = Math.pow( ( GsRGB + 0.055 ) / 1.055, 2.4 );
			}
			if ( BsRGB <= 0.03928 ) {
				B = BsRGB / 12.92;
			} else {
				B = Math.pow( ( BsRGB + 0.055 ) / 1.055, 2.4 );
			}
			return ( 0.2126 * R ) + ( 0.7152 * G ) + ( 0.0722 * B );
		},
		setAlpha: function( value ) {
			this._a = boundAlpha( value );
			this._roundA = mathRound( 100 * this._a ) / 100;
			return this;
		},
		toHsv: function() {
			var hsv = rgbToHsv( this._r, this._g, this._b );
			return { h: hsv.h * 360, s: hsv.s, v: hsv.v, a: this._a };
		},
		toHsvString: function() {
			var hsv = rgbToHsv( this._r, this._g, this._b );
			var h = mathRound( hsv.h * 360 ),
				s = mathRound( hsv.s * 100 ),
				v = mathRound( hsv.v * 100 );
			return this._a === 1 ? 'hsv(' + h + ', ' + s + '%, ' + v + '%)' : 'hsva(' + h + ', ' + s + '%, ' + v + '%, ' + this._roundA + ')';
		},
		toHsl: function() {
			var hsl = rgbToHsl( this._r, this._g, this._b );
			return { h: hsl.h * 360, s: hsl.s, l: hsl.l, a: this._a };
		},
		toHslString: function() {
			var hsl = rgbToHsl( this._r, this._g, this._b );
			var h = mathRound( hsl.h * 360 ),
				s = mathRound( hsl.s * 100 ),
				l = mathRound( hsl.l * 100 );
			return this._a === 1 ? 'hsl(' + h + ', ' + s + '%, ' + l + '%)' : 'hsla(' + h + ', ' + s + '%, ' + l + '%, ' + this._roundA + ')';
		},
		toHex: function( allow3Char ) {
			return rgbToHex( this._r, this._g, this._b, allow3Char );
		},
		toHexString: function( allow3Char ) {
			return '#' + this.toHex( allow3Char );
		},
		toHex8: function( allow4Char ) {
			return rgbaToHex( this._r, this._g, this._b, this._a, allow4Char );
		},
		toHex8String: function( allow4Char ) {
			return '#' + this.toHex8( allow4Char );
		},
		toRgb: function() {
			return { r: mathRound( this._r ), g: mathRound( this._g ), b: mathRound( this._b ), a: this._a };
		},
		toRgbString: function() {
			return this._a === 1 ? 'rgb(' + mathRound( this._r ) + ', ' + mathRound( this._g ) + ', ' + mathRound( this._b ) + ')' : 'rgba(' + mathRound( this._r ) + ', ' + mathRound( this._g ) + ', ' + mathRound( this._b ) + ', ' + this._roundA + ')';
		},
		toPercentageRgb: function() {
			return {
				r: mathRound( bound01( this._r, 255 ) * 100 ) + '%',
				g: mathRound( bound01( this._g, 255 ) * 100 ) + '%',
				b: mathRound( bound01( this._b, 255 ) * 100 ) + '%',
				a: this._a
			};
		},
		toPercentageRgbString: function() {
			return this._a === 1
				? 'rgb(' + mathRound( bound01( this._r, 255 ) * 100 ) + '%, ' + mathRound( bound01( this._g, 255 ) * 100 ) + '%, ' + mathRound( bound01( this._b, 255 ) * 100 ) + '%)'
				: 'rgba(' + mathRound( bound01( this._r, 255 ) * 100 ) + '%, ' + mathRound( bound01( this._g, 255 ) * 100 ) + '%, ' + mathRound( bound01( this._b, 255 ) * 100 ) + '%, ' + this._roundA + ')';
		},
		toName: function() {
			if ( this._a === 0 ) {
				return 'transparent';
			}

			if ( this._a < 1 ) {
				return false;
			}

			return hexNames[ rgbToHex( this._r, this._g, this._b, true ) ] || false;
		},
		toFilter: function( secondColor ) {
			var hex8String = '#' + rgbaToArgbHex( this._r, this._g, this._b, this._a );
			var secondHex8String = hex8String;
			var gradientType = this._gradientType ? 'GradientType = 1, ' : '';
			var s;
			if ( secondColor ) {
				s = newTinyColor( secondColor );
				secondHex8String = '#' + rgbaToArgbHex( s._r, s._g, s._b, s._a );
			}

			return 'progid:DXImageTransform.Microsoft.gradient(' + gradientType + 'startColorstr=' + hex8String + ',endColorstr=' + secondHex8String + ')';
		},
		toString: function( format ) {
			var formatSet = !! format;
			var formattedString;
			var hasAlpha;
			var needsAlphaFormat;
			format = format || this._format;

			formattedString = false;
			hasAlpha = this._a < 1 && this._a >= 0;
			needsAlphaFormat = ! formatSet && hasAlpha && ( format === 'hex' || format === 'hex6' || format === 'hex3' || format === 'hex4' || format === 'hex8' || format === 'name' );

			if ( needsAlphaFormat ) {
				// Special case for "transparent", all other non-alpha formats
				// will return rgba when there is transparency.
				if ( format === 'name' && this._a === 0 ) {
					return this.toName();
				}
				return this.toRgbString();
			}
			if ( format === 'rgb' ) {
				formattedString = this.toRgbString();
			}
			if ( format === 'prgb' ) {
				formattedString = this.toPercentageRgbString();
			}
			if ( format === 'hex' || format === 'hex6' ) {
				formattedString = this.toHexString();
			}
			if ( format === 'hex3' ) {
				formattedString = this.toHexString( true );
			}
			if ( format === 'hex4' ) {
				formattedString = this.toHex8String( true );
			}
			if ( format === 'hex8' ) {
				formattedString = this.toHex8String();
			}
			if ( format === 'name' ) {
				formattedString = this.toName();
			}
			if ( format === 'hsl' ) {
				formattedString = this.toHslString();
			}
			if ( format === 'hsv' ) {
				formattedString = this.toHsvString();
			}

			return formattedString || this.toHexString();
		},
		clone: function() {
			return newTinyColor( this.toString() );
		},

		_applyModification: function( fn, args ) {
			var color = fn.apply( null, [ this ].concat( [].slice.call( args ) ) );
			this._r = color._r;
			this._g = color._g;
			this._b = color._b;
			this.setAlpha( color._a );
			return this;
		},
		lighten: function() {
			return this._applyModification( lighten, arguments );
		},
		brighten: function() {
			return this._applyModification( brighten, arguments );
		},
		darken: function() {
			return this._applyModification( darken, arguments );
		},
		desaturate: function() {
			return this._applyModification( desaturate, arguments );
		},
		saturate: function() {
			return this._applyModification( saturate, arguments );
		},
		greyscale: function() {
			return this._applyModification( greyscale, arguments );
		},
		spin: function() {
			return this._applyModification( spin, arguments );
		},

		_applyCombination: function( fn, args ) {
			return fn.apply( null, [ this ].concat( [].slice.call( args ) ) );
		},
		/*analogous: function () {
         return this._applyCombination(analogous, arguments);
         },*/
		complement: function() {
			return this._applyCombination( complement, arguments );
		},
		/*monochromatic: function () {
         return this._applyCombination(monochromatic, arguments);
         },*/
		splitcomplement: function() {
			return this._applyCombination( splitcomplement, arguments );
		},
		triad: function() {
			return this._applyCombination( triad, arguments );
		},
		tetrad: function() {
			return this._applyCombination( tetrad, arguments );
		}
	};

	// If input is an object, force 1 into "1.0" to handle ratios properly
	// String input requires "1.0" as input, so 1 will be treated as 1
	TinyColor.fromRatio = function( color, opts ) {
		var newColor;
		var i;
		if ( typeof color === 'object' ) {
			newColor = {};
			for ( i in color ) {
				if ( Object.prototype.hasOwnProperty.call( color, i ) ) {
					if ( i === 'a' ) {
						newColor[ i ] = color[ i ];
					} else {
						newColor[ i ] = convertToPercentage( color[ i ] );
					}
				}
			}
			color = newColor;
		}

		return newTinyColor( color, opts );
	};

	// `equals`
	// Can be called with any TinyColor input
	TinyColor.equals = function( color1, color2 ) {
		if ( ! color1 || ! color2 ) {
			return false;
		}
		return newTinyColor( color1 ).toRgbString() === newTinyColor( color2 ).toRgbString();
	};

	TinyColor.random = function() {
		return TinyColor.fromRatio( {
			r: mathRandom(),
			g: mathRandom(),
			b: mathRandom()
		} );
	};

	// Utility Functions
	// ---------------------

	TinyColor.mix = function( color1, color2, amount ) {
		var rgb1;
		var rgb2;
		var p;
		var rgba;

		amount = amount === 0 ? 0 : amount || 50;

		rgb1 = newTinyColor( color1 ).toRgb();
		rgb2 = newTinyColor( color2 ).toRgb();

		p = amount / 100;

		rgba = {
			r: ( ( rgb2.r - rgb1.r ) * p ) + rgb1.r,
			g: ( ( rgb2.g - rgb1.g ) * p ) + rgb1.g,
			b: ( ( rgb2.b - rgb1.b ) * p ) + rgb1.b,
			a: ( ( rgb2.a - rgb1.a ) * p ) + rgb1.a
		};

		return newTinyColor( rgba );
	};

	// Readability Functions
	// ---------------------
	// <http://www.w3.org/TR/2008/REC-WCAG20-20081211/#contrast-ratiodef (WCAG Version 2)

	// `contrast`
	// Analyze the 2 colors and returns the color contrast defined by (WCAG Version 2)
	TinyColor.readability = function( color1, color2 ) {
		var c1 = newTinyColor( color1 );
		var c2 = newTinyColor( color2 );
		return ( Math.max( c1.getLuminance(), c2.getLuminance() ) + 0.05 ) / ( Math.min( c1.getLuminance(), c2.getLuminance() ) + 0.05 );
	};

	// `isReadable`
	// Ensure that foreground and background color combinations meet WCAG2 guidelines.
	// The third argument is an optional Object.
	//      the 'level' property states 'AA' or 'AAA' - if missing or invalid, it defaults to 'AA';
	//      the 'size' property states 'large' or 'small' - if missing or invalid, it defaults to 'small'.
	// If the entire object is absent, isReadable defaults to {level:"AA",size:"small"}.

	// *Example*
	//    TinyColor.isReadable("#000", "#111") => false
	//    TinyColor.isReadable("#000", "#111",{level:"AA",size:"large"}) => false
	TinyColor.isReadable = function( color1, color2, wcag2 ) {
		var readability = TinyColor.readability( color1, color2 );
		var wcag2Parms, out;

		out = false;

		wcag2Parms = validateWCAG2Parms( wcag2 );
		switch ( wcag2Parms.level + wcag2Parms.size ) {
			case 'AAsmall':
			case 'AAAlarge':
				out = readability >= 4.5;
				break;
			case 'AAlarge':
				out = readability >= 3;
				break;
			case 'AAAsmall':
				out = readability >= 7;
				break;
		}
		return out;
	};

	// `mostReadable`
	// Given a base color and a list of possible foreground or background
	// colors for that base, returns the most readable color.
	// Optionally returns Black or White if the most readable color is unreadable.
	// *Example*
	//    TinyColor.mostReadable(TinyColor.mostReadable("#123", ["#124", "#125"],{includeFallbackColors:false}).toHexString(); // "#112255"
	//    TinyColor.mostReadable(TinyColor.mostReadable("#123", ["#124", "#125"],{includeFallbackColors:true}).toHexString();  // "#ffffff"
	//    TinyColor.mostReadable("#a8015a", ["#faf3f3"],{includeFallbackColors:true,level:"AAA",size:"large"}).toHexString(); // "#faf3f3"
	//    TinyColor.mostReadable("#a8015a", ["#faf3f3"],{includeFallbackColors:true,level:"AAA",size:"small"}).toHexString(); // "#ffffff"
	TinyColor.mostReadable = function( baseColor, colorList, args ) {
		var bestColor = null;
		var bestScore = 0;
		var readability;
		var includeFallbackColors;
		var level;
		var size;
		var i;
		args = args || {};
		includeFallbackColors = args.includeFallbackColors;
		level = args.level;
		size = args.size;

		for ( i = 0; i < colorList.length; i += 1 ) {
			readability = TinyColor.readability( baseColor, colorList[ i ] );
			if ( readability > bestScore ) {
				bestScore = readability;
				bestColor = newTinyColor( colorList[ i ] );
			}
		}

		if ( TinyColor.isReadable( baseColor, bestColor, { level: level, size: size } ) || ! includeFallbackColors ) {
			return bestColor;
		}
		args.includeFallbackColors = false;
		return TinyColor.mostReadable( baseColor, [ '#fff', '#000' ], args );
	};

	// Big List of Colors
	// ------------------
	// <http://www.w3.org/TR/css3-color/#svg-color>
	TinyColor.names = names;

	// Make it easy to access colors via `hexNames[hex]`
	TinyColor.hexNames = hexNames;

	// Node: Export function
	if ( typeof window.module !== 'undefined' && window.module.exports ) {
		window.module.exports = TinyColor;
	} else if ( typeof window.define === 'function' && window.define.amd ) { // AMD/requirejs: Define the module
		window.define( function() {
			return TinyColor;
		} );
	} else { // Browser: Expose to window
		window.tinycolor = TinyColor;
	}
}( Math ) );

// Spectrum Colorpicker v2.0
// https://github.com/seballot/spectrum
// Author: Brian Grinstead
// License: MIT

( function( factory ) {
	'use strict';

	if ( typeof window.define === 'function' && window.define.amd ) {
		// AMD
		window.define( [ 'jquery' ], factory );
	} else if ( typeof exports === 'object' && typeof module === 'object' ) {
		// CommonJS
		window.module.exports = factory( window.require( 'jquery' ) );
	} else {
		// Browser
		factory( window.jQuery );
	}
}( function( $ ) {
	'use strict';

	var dataID = 'spectrum.id';
	var tinycolor = window.tinycolor;
	var defaultOpts = {
			// Callbacks
			beforeShow: noop,
			move: noop,
			change: noop,
			show: noop,
			hide: noop,

			// Options
			color: false,
			flat: false, // Deprecated - use type instead
			type: 'color', // text, color, component or flat
			showInput: false,
			allowEmpty: false,
			showButtons: true,
			clickoutFiresChange: true,
			showInitial: false,
			showPalette: false,
			showPaletteOnly: false,
			hideAfterPaletteSelect: false,
			togglePaletteOnly: false,
			showSelectionPalette: true,
			localStorageKey: false,
			appendTo: 'body',
			maxSelectionSize: 8,
			locale: 'en',
			cancelText: 'cancel',
			chooseText: 'choose',
			togglePaletteMoreText: 'more',
			togglePaletteLessText: 'less',
			clearText: 'Clear Color Selection',
			preferredFormat: 'hex',
			className: '', // Deprecated - use containerClassName and replacerClassName instead.
			containerClassName: '',
			replacerClassName: '',
			showAlpha: false,
			theme: 'epo',
			palette: [
				[ '#000000', '#444444', '#5b5b5b', '#999999', '#bcbcbc', '#eeeeee', '#f3f6f4', '#ffffff' ],
				[ '#f44336', '#744700', '#ce7e00', '#8fce00', '#2986cc', '#16537e', '#6a329f', '#c90076' ],
				[ '#f4cccc', '#fce5cd', '#fff2cc', '#d9ead3', '#d0e0e3', '#cfe2f3', '#d9d2e9', '#ead1dc' ],
				[ '#ea9999', '#f9cb9c', '#ffe599', '#b6d7a8', '#a2c4c9', '#9fc5e8', '#b4a7d6', '#d5a6bd' ],
				[ '#e06666', '#f6b26b', '#ffd966', '#93c47d', '#76a5af', '#6fa8dc', '#8e7cc3', '#c27ba0' ],
				[ '#cc0000', '#e69138', '#f1c232', '#6aa84f', '#45818e', '#3d85c6', '#674ea7', '#a64d79' ],
				[ '#990000', '#b45f06', '#bf9000', '#38761d', '#134f5c', '#0b5394', '#351c75', '#741b47' ],
				[ '#660000', '#783f04', '#7f6000', '#274e13', '#0c343d', '#073763', '#20124d', '#4c1130' ]
			],
			selectionPalette: [],
			disabled: false,
			offset: null
		},
		spectrums = [],
		IE = !! /msie/i.exec( window.navigator.userAgent ),
		rgbaSupport = ( function() {
			var contains = function( str, substr ) {
				return ( '' + str ).indexOf( substr ) !== 1;
			};
			var elem = document.createElement( 'div' );
			var style = elem.style;
			style.cssText = 'background-color:rgba(0,0,0,.5)';
			return contains( style.backgroundColor, 'rgba' ) || contains( style.backgroundColor, 'hsla' );
		}() ),
		replaceInput = [ "<div class='sp-replacer'>", "<div class='sp-preview'><div class='sp-preview-inner'></div></div>", "<div class='sp-dd'>&#9660;</div>", '</div>' ].join( '' ),
		markup = ( function() {
			var i;
			// IE does not support gradients with multiple stops, so we need to simulate
			//  that for the rainbow slider with 8 divs that each have a single gradient
			var gradientFix = '';
			if ( IE ) {
				for ( i = 1; i <= 6; i++ ) {
					gradientFix += "<div class='sp-" + i + "'></div>";
				}
			}

			return [
				"<div class='sp-container sp-hidden'>",
				"<div class='sp-palette-container'>",
				"<div class='sp-palette sp-thumb sp-cf'></div>",
				"<div class='sp-palette-button-container sp-cf'>",
				"<button type='button' class='sp-palette-toggle'></button>",
				'</div>',
				'</div>',
				"<div class='sp-picker-container'>",
				"<div class='sp-top sp-cf'>",
				"<div class='sp-fill'></div>",
				"<div class='sp-top-inner'>",
				"<div class='sp-color'>",
				"<div class='sp-sat'>",
				"<div class='sp-val'>",
				"<div class='sp-dragger'></div>",
				'</div>',
				'</div>',
				'</div>',
				"<div class='sp-clear sp-clear-display'>",
				'</div>',
				"<div class='sp-hue'>",
				"<div class='sp-slider'></div>",
				gradientFix,
				'</div>',
				'</div>',
				"<div class='sp-alpha'><div class='sp-alpha-inner'><div class='sp-alpha-handle'></div></div></div>",
				'</div>',
				"<div class='sp-input-container sp-cf'>",
				"<input class='sp-input' type='text' spellcheck='false'  />",
				'</div>',
				"<div class='sp-initial sp-thumb sp-cf'></div>",
				"<div class='sp-button-container sp-cf'>",
				"<button class='sp-cancel' href='#'></button>",
				"<button type='button' class='sp-choose'></button>",
				'</div>',
				'</div>',
				'</div>'
			].join( '' );
		}() );

	function paletteTemplate( p, color, className, opts ) {
		var html = [];
		var i;
		var current;
		var tiny;
		var c;
		var formattedString;
		var swatchStyle;
		for ( i = 0; i < p.length; i++ ) {
			current = p[ i ];
			if ( current ) {
				tiny = tinycolor( current );
				c = tiny.toHsl().l < 0.5 ? 'sp-thumb-el sp-thumb-dark' : 'sp-thumb-el sp-thumb-light';
				c += tinycolor.equals( color, current ) ? ' sp-thumb-active' : '';
				formattedString = tiny.toString( opts.preferredFormat || 'rgb' );
				swatchStyle = rgbaSupport ? 'background-color:' + tiny.toRgbString() : 'filter:' + tiny.toFilter();
				html.push( '<span title="' + formattedString + '" data-color="' + tiny.toRgbString() + '" class="' + c + '"><span class="sp-thumb-inner" style="' + swatchStyle + ';" /></span>' );
			} else {
				html.push( '<span class="sp-thumb-el sp-clear-display" ><span class="sp-clear-palette-only" style="background-color: transparent;" /></span>' );
			}
		}
		return "<div class='sp-cf " + className + "'>" + html.join( '' ) + '</div>';
	}

	function hideAll() {
		var i;
		for ( i = 0; i < spectrums.length; i++ ) {
			if ( spectrums[ i ] ) {
				spectrums[ i ].hide();
			}
		}
	}

	function instanceOptions( o, callbackContext ) {
		var opts;
		o.locale = o.locale || window.navigator.language;
		if ( o.locale ) {
			o.locale = o.locale.split( '-' )[ 0 ].toLowerCase();
		} // handle locale like "fr-FR"
		if ( o.locale !== 'en' && $.spectrum.localization[ o.locale ] ) {
			o = $.extend( {}, $.spectrum.localization[ o.locale ], o );
		}
		opts = $.extend( {}, defaultOpts, o );

		opts.callbacks = {
			move: bind( opts.move, callbackContext ),
			change: bind( opts.change, callbackContext ),
			show: bind( opts.show, callbackContext ),
			hide: bind( opts.hide, callbackContext ),
			beforeShow: bind( opts.beforeShow, callbackContext )
		};

		return opts;
	}

	function spectrum( element, o ) {
		var spect;

		var opts = instanceOptions( o, element ),
			type = opts.type,
			flat = type === 'flat',
			showSelectionPalette = opts.showSelectionPalette,
			localStorageKey = opts.localStorageKey,
			theme = opts.theme,
			callbacks = opts.callbacks,
			resize = throttle( reflow, 10 ),
			visible = false,
			isDragging = false,
			dragWidth = 0,
			dragHeight = 0,
			dragHelperHeight = 0,
			slideHeight = 0,
			alphaWidth = 0,
			alphaSlideHelperWidth = 0,
			slideHelperHeight = 0,
			currentHue = 0,
			currentSaturation = 0,
			currentValue = 0,
			currentAlpha = 1,
			palette = [],
			paletteArray = [],
			paletteLookup = {},
			selectionPalette = opts.selectionPalette.slice( 0 ),
			maxSelectionSize = opts.maxSelectionSize,
			draggingClass = 'sp-dragging',
			abortNextInputChange = false,
			shiftMovementDirection = null;

		var doc = element.ownerDocument,
			boundElement = $( element ),
			disabled = false,
			container = $( markup, doc ).addClass( theme ),
			pickerContainer = container.find( '.sp-picker-container' ),
			dragger = container.find( '.sp-color' ),
			dragHelper = container.find( '.sp-dragger' ),
			slider = container.find( '.sp-hue' ),
			slideHelper = container.find( '.sp-slider' ),
			alphaSliderInner = container.find( '.sp-alpha-inner' ),
			alphaSlider = container.find( '.sp-alpha' ),
			alphaSlideHelper = container.find( '.sp-alpha-handle' ),
			textInput = container.find( '.sp-input' ),
			paletteContainer = container.find( '.sp-palette' ),
			initialColorContainer = container.find( '.sp-initial' ),
			cancelButton = container.find( '.sp-cancel' ),
			clearButton = container.find( '.sp-clear' ),
			chooseButton = container.find( '.sp-choose' ),
			toggleButton = container.find( '.sp-palette-toggle' ),
			isInput = boundElement.is( 'input' ),
			shouldReplace = isInput && type === 'color',
			replacer = shouldReplace ? $( replaceInput ).addClass( theme ).addClass( opts.className ).addClass( opts.replacerClassName ) : $( [] ),
			offsetElement = shouldReplace ? replacer : boundElement,
			previewElement = replacer.find( '.sp-preview-inner' ),
			initialColor = opts.color || ( isInput && boundElement.val() ),
			colorOnShow = false,
			currentPreferredFormat = opts.preferredFormat,
			clickoutFiresChange = ! opts.showButtons || opts.clickoutFiresChange,
			isEmpty = ! initialColor,
			allowEmpty = opts.allowEmpty;

		// Element to be updated with the input color. Populated in initialize method
		var originalInputContainer = null,
			colorizeElement = null,
			colorizeElementInitialColor = null,
			colorizeElementInitialBackground = null;

		//If there is a label for this element, when clicked on, show the colour picker
		var thisId = boundElement.attr( 'id' );
		var label;

		if ( thisId !== undefined && thisId.length > 0 ) {
			label = $( 'label[for="' + thisId + '"]' );
			if ( label.length ) {
				label.on( 'click', function( e ) {
					e.preventDefault();
					boundElement.spectrum( 'show' );
					return false;
				} );
			}
		}

		function applyOptions() {
			var i;
			var j;
			var rgb;

			if ( opts.showPaletteOnly ) {
				opts.showPalette = true;
			}

			toggleButton.text( opts.showPaletteOnly ? opts.togglePaletteMoreText : opts.togglePaletteLessText );

			if ( opts.palette ) {
				palette = opts.palette.slice( 0 );
				paletteArray = $.isArray( palette[ 0 ] ) ? palette : [ palette ];
				paletteLookup = {};
				for ( i = 0; i < paletteArray.length; i++ ) {
					for ( j = 0; j < paletteArray[ i ].length; j++ ) {
						rgb = tinycolor( paletteArray[ i ][ j ] ).toRgbString();
						paletteLookup[ rgb ] = true;
					}
				}

				// if showPaletteOnly and didn't set initialcolor
				// set initialcolor to first palette
				if ( opts.showPaletteOnly && ! opts.color ) {
					initialColor = palette[ 0 ][ 0 ] === '' ? palette[ 0 ][ 0 ] : Object.keys( paletteLookup )[ 0 ];
				}
			}

			container.toggleClass( 'sp-flat', flat );
			container.toggleClass( 'sp-input-disabled', ! opts.showInput );
			container.toggleClass( 'sp-alpha-enabled', opts.showAlpha );
			container.toggleClass( 'sp-clear-enabled', allowEmpty );
			container.toggleClass( 'sp-buttons-disabled', ! opts.showButtons );
			container.toggleClass( 'sp-palette-buttons-disabled', ! opts.togglePaletteOnly );
			container.toggleClass( 'sp-palette-disabled', ! opts.showPalette );
			container.toggleClass( 'sp-palette-only', opts.showPaletteOnly );
			container.toggleClass( 'sp-initial-disabled', ! opts.showInitial );
			container.addClass( opts.className ).addClass( opts.containerClassName );

			reflow();
		}

		function initialize() {
			var addOn;
			var appendTo;
			var paletteEvent;

			if ( IE ) {
				container.find( '*:not(input)' ).attr( 'unselectable', 'on' );
			}

			applyOptions();

			originalInputContainer = $( '<span class="sp-original-input-container"></span>' );
			[ 'margin' ].forEach( function( cssProp ) {
				originalInputContainer.css( cssProp, boundElement.css( cssProp ) );
			} );
			// inline-flex by default, switching to flex if needed
			if ( boundElement.css( 'display' ) === 'block' ) {
				originalInputContainer.css( 'display', 'flex' );
			}

			if ( shouldReplace ) {
				boundElement.after( replacer ).hide();
			} else if ( type === 'text' ) {
				originalInputContainer.addClass( 'sp-colorize-container' );
				boundElement.addClass( 'spectrum sp-colorize' ).wrap( originalInputContainer );
			} else if ( type === 'component' ) {
				boundElement.addClass( 'spectrum' ).wrap( originalInputContainer );
				addOn = $( [ "<div class='sp-colorize-container sp-add-on'>", "<div class='sp-colorize'></div> ", '</div>' ].join( '' ) );
				addOn
					.width( boundElement.outerHeight() + 'px' )
					.css( 'border-radius', boundElement.css( 'border-radius' ) )
					.css( 'border', boundElement.css( 'border' ) );
				boundElement.addClass( 'with-add-on' ).before( addOn );
			}

			colorizeElement = boundElement.parent().find( '.sp-colorize' );
			colorizeElementInitialColor = colorizeElement.css( 'color' );
			colorizeElementInitialBackground = colorizeElement.css( 'background-color' );

			if ( ! allowEmpty ) {
				clearButton.hide();
			}

			if ( flat ) {
				boundElement.after( container ).hide();
			} else {
				appendTo = opts.appendTo === 'parent' ? boundElement.parent() : $( opts.appendTo );
				if ( appendTo.length !== 1 ) {
					appendTo = $( 'body' );
				}

				appendTo.append( container );
			}

			updateSelectionPaletteFromStorage();

			offsetElement.on( 'click.spectrum touchstart.spectrum', function( e ) {
				if ( ! disabled ) {
					toggle();
				}

				e.stopPropagation();

				if ( ! $( e.target ).is( 'input' ) ) {
					e.preventDefault();
				}
			} );

			if ( boundElement.is( ':disabled' ) || opts.disabled === true ) {
				disable();
			}

			// Prevent clicks from bubbling up to document.  This would cause it to be hidden.
			container.click( stopPropagation );

			// Handle user typed input
			[ textInput, boundElement ].forEach( function( input ) {
				input.change( function() {
					setFromTextInput( input.val() );
				} );
				input.on( 'paste', function() {
					setTimeout( function() {
						setFromTextInput( input.val() );
					}, 1 );
				} );
				input.keydown( function( e ) {
					if ( e.keyCode === 13 ) {
						setFromTextInput( $( input ).val() );
						// eslint-disable-next-line eqeqeq
						if ( input == boundElement ) {
							hide();
						}
					}
				} );
			} );

			cancelButton.text( opts.cancelText );
			cancelButton.on( 'click.spectrum', function( e ) {
				e.stopPropagation();
				e.preventDefault();
				revert();
				hide();
			} );

			clearButton.attr( 'title', opts.clearText );
			clearButton.on( 'click.spectrum', function( e ) {
				e.stopPropagation();
				e.preventDefault();
				isEmpty = true;
				move();

				if ( flat ) {
					//for the flat style, this is a change event
					updateOriginalInput( true );
				}
			} );

			chooseButton.text( opts.chooseText );
			chooseButton.on( 'click.spectrum', function( e ) {
				e.stopPropagation();
				e.preventDefault();

				if ( IE && textInput.is( ':focus' ) ) {
					textInput.trigger( 'change' );
				}

				if ( isValid() ) {
					updateOriginalInput( true );
					hide();
				}
			} );

			toggleButton.text( opts.showPaletteOnly ? opts.togglePaletteMoreText : opts.togglePaletteLessText );
			toggleButton.on( 'click.spectrum', function( e ) {
				e.stopPropagation();
				e.preventDefault();

				opts.showPaletteOnly = ! opts.showPaletteOnly;

				// To make sure the Picker area is drawn on the right, next to the
				// Palette area (and not below the palette), first move the Palette
				// to the left to make space for the picker, plus 5px extra.
				// The 'applyOptions' function puts the whole container back into place
				// and takes care of the button-text and the sp-palette-only CSS class.
				if ( ! opts.showPaletteOnly && ! flat ) {
					container.css( 'left', '-=' + ( pickerContainer.outerWidth( true ) + 5 ) );
				}
				applyOptions();
			} );

			draggable(
				alphaSlider,
				function( dragX, dragY, e ) {
					currentAlpha = dragX / alphaWidth;
					isEmpty = false;
					if ( e.shiftKey ) {
						currentAlpha = Math.round( currentAlpha * 10 ) / 10;
					}

					move();
				},
				dragStart,
				dragStop
			);

			draggable(
				slider,
				function( dragX, dragY ) {
					currentHue = parseFloat( dragY / slideHeight );
					isEmpty = false;
					if ( ! opts.showAlpha ) {
						currentAlpha = 1;
					}
					move();
				},
				dragStart,
				dragStop
			);

			draggable(
				dragger,
				function( dragX, dragY, e ) {
					var oldDragX;
					var oldDragY;
					var furtherFromX;
					var setSaturation;
					var setValue;
					// shift+drag should snap the movement to either the x or y axis.
					if ( ! e.shiftKey ) {
						shiftMovementDirection = null;
					} else if ( ! shiftMovementDirection ) {
						oldDragX = currentSaturation * dragWidth;
						oldDragY = dragHeight - ( currentValue * dragHeight );
						furtherFromX = Math.abs( dragX - oldDragX ) > Math.abs( dragY - oldDragY );

						shiftMovementDirection = furtherFromX ? 'x' : 'y';
					}

					setSaturation = ! shiftMovementDirection || shiftMovementDirection === 'x';
					setValue = ! shiftMovementDirection || shiftMovementDirection === 'y';

					if ( setSaturation ) {
						currentSaturation = parseFloat( dragX / dragWidth );
					}
					if ( setValue ) {
						currentValue = parseFloat( ( dragHeight - dragY ) / dragHeight );
					}

					isEmpty = false;
					if ( ! opts.showAlpha ) {
						currentAlpha = 1;
					}

					move();
				},
				dragStart,
				dragStop
			);

			if ( initialColor !== false && initialColor !== '' ) {
				set( initialColor );

				// In case color was black - update the preview UI and set the format
				// since the set function will not run (default color is black).
				updateUI();
				currentPreferredFormat = tinycolor( initialColor ).format || opts.preferredFormat;
				addColorToSelectionPalette( initialColor );
			} else if ( initialColor === '' ) {
				set( initialColor );
				updateUI();
			} else {
				updateUI();
			}

			if ( flat ) {
				show();
			}

			function paletteElementClick( e ) {
				if ( e.data && e.data.ignore ) {
					set( $( e.target ).closest( '.sp-thumb-el' ).data( 'color' ) );
					move();
				} else {
					set( $( e.target ).closest( '.sp-thumb-el' ).data( 'color' ) );
					move();

					// If the picker is going to close immediately, a palette selection
					// is a change.  Otherwise, it's a move only.
					if ( opts.hideAfterPaletteSelect ) {
						updateOriginalInput( true );
						hide();
					} else {
						updateOriginalInput();
					}
				}

				return false;
			}

			paletteEvent = IE ? 'mousedown.spectrum' : 'click.spectrum touchstart.spectrum';
			paletteContainer.on( paletteEvent, '.sp-thumb-el', paletteElementClick );
			initialColorContainer.on( paletteEvent, '.sp-thumb-el:nth-child(1)', { ignore: true }, paletteElementClick );
		}

		function updateSelectionPaletteFromStorage() {
			var localStorage;
			var oldPalette;

			if ( localStorageKey ) {
				// Migrate old palettes over to new format.  May want to remove this eventually.
				try {
					localStorage = window.localStorage;
					oldPalette = localStorage[ localStorageKey ].split( ',#' );
					if ( oldPalette.length > 1 ) {
						delete localStorage[ localStorageKey ];
						$.each( oldPalette, function( i, c ) {
							addColorToSelectionPalette( c );
						} );
					}
				} catch ( e ) {
					window.console.log( e );
				}

				try {
					selectionPalette = window.localStorage[ localStorageKey ].split( ';' );
				} catch ( e ) {
					window.console.log( e );
				}
			}
		}

		function addColorToSelectionPalette( color ) {
			var rgb;
			if ( showSelectionPalette ) {
				rgb = tinycolor( color ).toRgbString();
				if ( ! paletteLookup[ rgb ] && $.inArray( rgb, selectionPalette ) === -1 ) {
					selectionPalette.push( rgb );
					while ( selectionPalette.length > maxSelectionSize ) {
						selectionPalette.shift();
					}
				}

				if ( localStorageKey ) {
					try {
						window.localStorage[ localStorageKey ] = selectionPalette.join( ';' );
					} catch ( e ) {
						window.console.log( e );
					}
				}
			}
		}

		function getUniqueSelectionPalette() {
			var unique = [];
			var i;
			var rgb;
			if ( opts.showPalette ) {
				for ( i = 0; i < selectionPalette.length; i++ ) {
					rgb = tinycolor( selectionPalette[ i ] ).toRgbString();

					if ( ! paletteLookup[ rgb ] ) {
						unique.push( selectionPalette[ i ] );
					}
				}
			}

			return unique.reverse().slice( 0, opts.maxSelectionSize );
		}

		function drawPalette() {
			var currentColor = get();

			var html = $.map( paletteArray, function( thispalette, i ) {
				return paletteTemplate( thispalette, currentColor, 'sp-palette-row sp-palette-row-' + i, opts );
			} );

			updateSelectionPaletteFromStorage();

			if ( selectionPalette ) {
				html.push( paletteTemplate( getUniqueSelectionPalette(), currentColor, 'sp-palette-row sp-palette-row-selection', opts ) );
			}

			paletteContainer.html( html.join( '' ) );
		}

		function drawInitial() {
			var initial;
			var current;
			if ( opts.showInitial ) {
				initial = colorOnShow;
				current = get();
				initialColorContainer.html( paletteTemplate( [ initial, current ], current, 'sp-palette-row-initial', opts ) );
			}
		}

		function dragStart() {
			if ( dragHeight <= 0 || dragWidth <= 0 || slideHeight <= 0 ) {
				reflow();
			}
			isDragging = true;
			container.addClass( draggingClass );
			shiftMovementDirection = null;
			boundElement.trigger( 'dragstart.spectrum', [ get() ] );
		}

		function dragStop() {
			isDragging = false;
			container.removeClass( draggingClass );
			boundElement.trigger( 'dragstop.spectrum', [ get() ] );
		}

		function setFromTextInput( value ) {
			var tiny;
			if ( abortNextInputChange ) {
				abortNextInputChange = false;
				return;
			}
			if ( ( value === null || value === '' ) && allowEmpty ) {
				set( null );
				move();
				updateOriginalInput();
			} else {
				tiny = tinycolor( value );
				if ( tiny.isValid() ) {
					set( tiny );
					move();
					updateOriginalInput();
				} else {
					textInput.addClass( 'sp-validation-error' );
				}
			}
		}

		function toggle() {
			if ( visible ) {
				hide();
			} else {
				show();
			}
		}

		function show() {
			// debugger;
			var event = $.Event( 'beforeShow.spectrum' );

			if ( visible ) {
				reflow();
				return;
			}

			boundElement.trigger( event, [ get() ] );

			if ( callbacks.beforeShow( get() ) === false || event.isDefaultPrevented() ) {
				return;
			}

			hideAll();
			visible = true;

			$( doc ).on( 'keydown.spectrum', onkeydown );
			$( doc ).on( 'click.spectrum', clickout );
			$( window ).on( 'resize.spectrum', resize );
			replacer.addClass( 'sp-active' );
			container.removeClass( 'sp-hidden' );

			reflow();
			updateUI();

			colorOnShow = get();

			drawInitial();
			callbacks.show( colorOnShow );
			boundElement.trigger( 'show.spectrum', [ colorOnShow ] );
		}

		function onkeydown( e ) {
			// Close on ESC
			if ( e.keyCode === 27 ) {
				hide();
			}
		}

		function clickout( e ) {
			// Return on right click.
			if ( e.button === 2 ) {
				return;
			}

			// If a drag event was happening during the mouseup, don't hide
			// on click.
			if ( isDragging ) {
				return;
			}

			if ( clickoutFiresChange ) {
				updateOriginalInput( true );
			} else {
				revert();
			}
			hide();
		}

		function hide() {
			// Return if hiding is unnecessary
			if ( ! visible || flat ) {
				return;
			}
			visible = false;

			$( doc ).off( 'keydown.spectrum', onkeydown );
			$( doc ).off( 'click.spectrum', clickout );
			$( window ).off( 'resize.spectrum', resize );

			replacer.removeClass( 'sp-active' );
			container.addClass( 'sp-hidden' );

			callbacks.hide( get() );
			boundElement.trigger( 'hide.spectrum', [ get() ] );
		}

		function revert() {
			set( colorOnShow, true );
			updateOriginalInput( true );
		}

		function set( color, ignoreFormatChange ) {
			var newColor, newHsv;
			if ( tinycolor.equals( color, get() ) ) {
				// Update UI just in case a validation error needs
				// to be cleared.
				updateUI();
				return;
			}

			if ( ( ! color || color === undefined ) && allowEmpty ) {
				isEmpty = true;
			} else {
				isEmpty = false;
				newColor = tinycolor( color );
				newHsv = newColor.toHsv();

				currentHue = ( newHsv.h % 360 ) / 360;
				currentSaturation = newHsv.s;
				currentValue = newHsv.v;
				currentAlpha = newHsv.a;
			}
			updateUI();

			if ( newColor && newColor.isValid() && ! ignoreFormatChange ) {
				currentPreferredFormat = opts.preferredFormat || newColor.getFormat();
			}
		}

		function get( thisopts ) {
			thisopts = thisopts || {};

			if ( allowEmpty && isEmpty ) {
				return null;
			}

			return tinycolor.fromRatio(
				{
					h: currentHue,
					s: currentSaturation,
					v: currentValue,
					a: Math.round( currentAlpha * 1000 ) / 1000
				},
				{ format: thisopts.format || currentPreferredFormat }
			);
		}

		function isValid() {
			return ! textInput.hasClass( 'sp-validation-error' );
		}

		function move() {
			updateUI();

			callbacks.move( get() );
			boundElement.trigger( 'move.spectrum', [ get() ] );
		}

		function updateUI() {
			var flatColor;
			var format;
			var realColor;
			var displayColor;
			var realHex;
			var realRgb;
			var rgb;
			var realAlpha;
			var gradient;
			var color;
			var textColor;

			textInput.removeClass( 'sp-validation-error' );

			updateHelperLocations();

			// Update dragger background color (gradients take care of saturation and value).
			flatColor = tinycolor.fromRatio( { h: currentHue, s: 1, v: 1 } );
			dragger.css( 'background-color', flatColor.toHexString() );

			// Get a format that alpha will be included in (hex and names ignore alpha)
			format = currentPreferredFormat;
			if ( currentAlpha < 1 && ! ( currentAlpha === 0 && format === 'name' ) ) {
				if ( format === 'hex' || format === 'hex3' || format === 'hex6' || format === 'name' ) {
					format = 'rgb';
				}
			}

			realColor = get( { format: format } );
			displayColor = '';

			//reset background info for preview element
			previewElement.removeClass( 'sp-clear-display' );
			previewElement.css( 'background-color', 'transparent' );

			if ( ! realColor && allowEmpty ) {
				// Update the replaced elements background with icon indicating no color selection
				previewElement.addClass( 'sp-clear-display' );
			} else {
				realHex = realColor.toHexString();
				realRgb = realColor.toRgbString();

				// Update the replaced elements background color (with actual selected color)
				if ( rgbaSupport || realColor.alpha === 1 ) {
					previewElement.css( 'background-color', realRgb );
				} else {
					previewElement.css( 'background-color', 'transparent' );
					previewElement.css( 'filter', realColor.toFilter() );
				}

				if ( opts.showAlpha ) {
					rgb = realColor.toRgb();
					rgb.a = 0;
					realAlpha = tinycolor( rgb ).toRgbString();
					gradient = 'linear-gradient(left, ' + realAlpha + ', ' + realHex + ')';

					if ( IE ) {
						alphaSliderInner.css( 'filter', tinycolor( realAlpha ).toFilter( { gradientType: 1 }, realHex ) );
					} else {
						alphaSliderInner.css( 'background', '-webkit-' + gradient );
						alphaSliderInner.css( 'background', '-moz-' + gradient );
						alphaSliderInner.css( 'background', '-ms-' + gradient );
						// Use current syntax gradient on unprefixed property.
						alphaSliderInner.css( 'background', 'linear-gradient(to right, ' + realAlpha + ', ' + realHex + ')' );
					}
				}

				displayColor = realColor.toString( format );
			}

			// Update the text entry input as it changes happen
			if ( opts.showInput ) {
				textInput.val( displayColor );
			}
			boundElement.val( displayColor );
			if ( opts.type === 'text' || opts.type === 'component' ) {
				color = realColor;
				if ( color && colorizeElement ) {
					textColor = color.isLight() || color.getAlpha() < 0.4 ? 'black' : 'white';
					colorizeElement.css( 'background-color', color.toRgbString() ).css( 'color', textColor );
				} else {
					colorizeElement.css( 'background-color', colorizeElementInitialBackground ).css( 'color', colorizeElementInitialColor );
				}
			}

			if ( opts.showPalette ) {
				drawPalette();
			}

			drawInitial();
		}

		function updateHelperLocations() {
			var s = currentSaturation;
			var v = currentValue;
			var dragX;
			var dragY;
			var alphaX;
			var slideY;

			if ( allowEmpty && isEmpty ) {
				//if selected color is empty, hide the helpers
				alphaSlideHelper.hide();
				slideHelper.hide();
				dragHelper.hide();
			} else {
				//make sure helpers are visible
				alphaSlideHelper.show();
				slideHelper.show();
				dragHelper.show();

				// Where to show the little circle in that displays your current selected color
				dragX = s * dragWidth;
				dragY = dragHeight - ( v * dragHeight );
				dragX = Math.max( -dragHelperHeight, Math.min( dragWidth - dragHelperHeight, dragX - dragHelperHeight ) );
				dragY = Math.max( -dragHelperHeight, Math.min( dragHeight - dragHelperHeight, dragY - dragHelperHeight ) );
				dragHelper.css( {
					top: dragY + 'px',
					left: dragX + 'px'
				} );

				alphaX = currentAlpha * alphaWidth;
				alphaSlideHelper.css( {
					left: alphaX - ( alphaSlideHelperWidth / 2 ) + 'px'
				} );

				// Where to show the bar that displays your current selected hue
				slideY = currentHue * slideHeight;
				slideHelper.css( {
					top: slideY - slideHelperHeight + 'px'
				} );
			}
		}

		function updateOriginalInput( fireCallback ) {
			var color = get(),
				hasChanged = ! tinycolor.equals( color, colorOnShow );

			if ( color ) {
				// Update the selection palette with the current color
				addColorToSelectionPalette( color );
			}

			if ( fireCallback && hasChanged ) {
				callbacks.change( color );
				// we trigger the change event or input, but the input change event is also binded
				// to some spectrum processing, that we do no need
				abortNextInputChange = true;
				boundElement.trigger( 'change', [ color ] );
			}
		}

		function reflow() {
			if ( ! visible ) {
				return; // Calculations would be useless and wouldn't be reliable anyways
			}
			dragWidth = dragger.width();
			dragHeight = dragger.height();
			dragHelperHeight = dragHelper.height();
			slideHeight = slider.height();
			slideHelperHeight = slideHelper.height();
			alphaWidth = alphaSlider.width();
			alphaSlideHelperWidth = alphaSlideHelper.width();

			if ( ! flat ) {
				container.css( 'position', 'absolute' );
				if ( opts.offset ) {
					container.offset( opts.offset );
				} else {
					container.offset( getOffset( container, offsetElement ) );
				}
			}

			updateHelperLocations();

			if ( opts.showPalette ) {
				drawPalette();
			}

			boundElement.trigger( 'reflow.spectrum' );
		}

		function destroy() {
			var oInputContainer;
			boundElement.show().removeClass( 'spectrum with-add-on sp-colorize' );
			offsetElement.off( 'click.spectrum touchstart.spectrum' );
			container.remove();
			replacer.remove();
			if ( colorizeElement ) {
				colorizeElement.css( 'background-color', colorizeElementInitialBackground ).css( 'color', colorizeElementInitialColor );
			}
			oInputContainer = boundElement.closest( '.sp-original-input-container' );
			if ( oInputContainer.length > 0 ) {
				oInputContainer.after( boundElement ).remove();
			}
			spectrums[ spect.id ] = null;
		}

		function option( optionName, optionValue ) {
			if ( optionName === undefined ) {
				return $.extend( {}, opts );
			}
			if ( optionValue === undefined ) {
				return opts[ optionName ];
			}

			opts[ optionName ] = optionValue;

			if ( optionName === 'preferredFormat' ) {
				currentPreferredFormat = opts.preferredFormat;
			}
			applyOptions();
		}

		function enable() {
			disabled = false;
			boundElement.attr( 'disabled', false );
			offsetElement.removeClass( 'sp-disabled' );
		}

		function disable() {
			hide();
			disabled = true;
			boundElement.attr( 'disabled', true );
			offsetElement.addClass( 'sp-disabled' );
		}

		function setOffset( coord ) {
			opts.offset = coord;
			reflow();
		}

		initialize();

		spect = {
			show: show,
			hide: hide,
			toggle: toggle,
			reflow: reflow,
			option: option,
			enable: enable,
			disable: disable,
			offset: setOffset,
			set: function( c ) {
				set( c );
				updateOriginalInput();
			},
			get: get,
			destroy: destroy,
			container: container
		};

		spect.id = spectrums.push( spect ) - 1;

		return spect;
	}

	/**
	 * checkOffset - get the offset below/above and left/right element depending on screen position
	 * Thanks https://github.com/jquery/jquery-ui/blob/master/ui/jquery.ui.datepicker.js
	 */
	function getOffset( picker, input ) {
		var extraY = 0;
		var dpWidth = picker.outerWidth();
		var dpHeight = picker.outerHeight();
		var inputHeight = input.outerHeight();
		var doc = picker[ 0 ].ownerDocument;
		var docElem = doc.documentElement;
		var viewWidth = docElem.clientWidth + $( doc ).scrollLeft();
		var viewHeight = docElem.clientHeight + $( doc ).scrollTop();
		var offset = input.offset();
		var offsetLeft = offset.left;
		var offsetTop = offset.top;

		offsetTop += inputHeight;

		offsetLeft -= Math.min( offsetLeft, offsetLeft + dpWidth > viewWidth && viewWidth > dpWidth ? Math.abs( offsetLeft + dpWidth - viewWidth ) : 0 );

		offsetTop -= Math.min( offsetTop, offsetTop + dpHeight > viewHeight && viewHeight > dpHeight ? Math.abs( dpHeight + inputHeight - extraY ) : extraY );

		return {
			top: offsetTop,
			bottom: offset.bottom,
			left: offsetLeft,
			right: offset.right,
			width: offset.width,
			height: offset.height
		};
	}

	/**
	 * noop - do nothing
	 */
	function noop() {}

	/**
	 * stopPropagation - makes the code only doing this a little easier to read in line
	 */
	function stopPropagation( e ) {
		e.stopPropagation();
	}

	/**
	 * Create a function bound to a given object
	 * Thanks to underscore.js
	 */
	function bind( func, obj ) {
		var slice = Array.prototype.slice;
		var args = slice.call( arguments, 2 );
		return function() {
			return func.apply( obj, args.concat( slice.call( arguments ) ) );
		};
	}

	/**
	 * Lightweight drag helper.  Handles containment within the element, so that
	 * when dragging, the x is within [0,element.width] and y is within [0,element.height]
	 */
	function draggable( element, onmove, onstart, onstop ) {
		var doc = document;
		var dragging = false;
		var offset = {};
		var maxHeight = 0;
		var maxWidth = 0;
		var hasTouch = 'ontouchstart' in window;
		var duringDragEvents = {};

		onmove = onmove || function() {};
		onstart = onstart || function() {};
		onstop = onstop || function() {};

		duringDragEvents.selectstart = prevent;
		duringDragEvents.dragstart = prevent;
		duringDragEvents[ 'touchmove mousemove' ] = move;
		duringDragEvents[ 'touchend mouseup' ] = stop;

		function prevent( e ) {
			if ( e.stopPropagation ) {
				e.stopPropagation();
			}
			if ( e.preventDefault ) {
				e.preventDefault();
			}
			e.returnValue = false;
		}

		function move( e ) {
			var t0;
			var pageX;
			var pageY;
			var dragX;
			var dragY;
			if ( dragging ) {
				// Mouseup happened outside of window
				if ( IE && doc.documentMode < 9 && ! e.button ) {
					return stop();
				}

				t0 = e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[ 0 ];
				pageX = ( t0 && t0.pageX ) || e.pageX;
				pageY = ( t0 && t0.pageY ) || e.pageY;

				dragX = Math.max( 0, Math.min( pageX - offset.left, maxWidth ) );
				dragY = Math.max( 0, Math.min( pageY - offset.top, maxHeight ) );

				if ( hasTouch ) {
					// Stop scrolling in iOS
					prevent( e );
				}

				onmove.apply( element, [ dragX, dragY, e ] );
			}
		}

		function start( e ) {
			var rightclick = e.which ? e.which === 3 : e.button === 2;

			if ( ! rightclick && ! dragging ) {
				if ( onstart.apply( element, arguments ) !== false ) {
					dragging = true;
					maxHeight = $( element ).height();
					maxWidth = $( element ).width();
					offset = $( element ).offset();

					$( doc ).on( duringDragEvents );
					$( doc.body ).addClass( 'sp-dragging' );

					move( e );

					prevent( e );
				}
			}
		}

		function stop() {
			if ( dragging ) {
				$( doc ).off( duringDragEvents );
				$( doc.body ).removeClass( 'sp-dragging' );

				// Wait a tick before notifying observers to allow the click event
				// to fire in Chrome.
				setTimeout( function() {
					onstop.apply( element, arguments );
				}, 0 );
			}
			dragging = false;
		}

		$( element ).on( 'touchstart mousedown', start );
	}

	function throttle( func, wait, debounce ) {
		var timeout;
		return function() {
			var context = this,
				args = arguments;
			var throttler = function() {
				timeout = null;
				func.apply( context, args );
			};
			if ( debounce ) {
				clearTimeout( timeout );
			}
			if ( debounce || ! timeout ) {
				timeout = setTimeout( throttler, wait );
			}
		};
	}

	function inputTypeColorSupport() {
		return $.fn.spectrum.inputTypeColorSupport();
	}

	/**
	 * Define a jQuery plugin
	 */
	$.fn.spectrum = function( opts ) {
		var returnValue;
		var args;
		var spect;
		var method;

		if ( typeof opts === 'string' ) {
			returnValue = this;
			args = Array.prototype.slice.call( arguments, 1 );

			this.each( function() {
				spect = spectrums[ $( this ).data( dataID ) ];
				if ( spect ) {
					method = spect[ opts ];
					if ( ! method ) {
						throw new Error( "Spectrum: no such method: '" + opts + "'" );
					}

					if ( opts === 'get' ) {
						returnValue = spect.get();
					} else if ( opts === 'container' ) {
						returnValue = spect.container;
					} else if ( opts === 'option' ) {
						returnValue = spect.option.apply( spect, args );
					} else if ( opts === 'destroy' ) {
						spect.destroy();
						$( this ).removeData( dataID );
					} else {
						method.apply( spect, args );
					}
				}
			} );

			return returnValue;
		}

		// Initializing a new instance of spectrum
		return this.spectrum( 'destroy' ).each( function() {
			var options = $.extend( {}, $( this ).data(), opts );
			var thisspect;
			// Infer default type from input params and deprecated options
			if ( ! $( this ).is( 'input' ) ) {
				options.type = 'noInput';
			} else if ( options.flat || options.type === 'flat' ) {
				options.type = 'flat';
			} else if ( $( this ).attr( 'type' ) === 'color' ) {
				options.type = 'color';
			}

			thisspect = spectrum( this, options );
			$( this ).data( dataID, thisspect.id );
		} );
	};

	$.fn.spectrum.load = true;
	$.fn.spectrum.loadOpts = {};
	$.fn.spectrum.draggable = draggable;
	$.fn.spectrum.defaults = defaultOpts;
	$.fn.spectrum.inputTypeColorSupport = function() {
		var colorInput;
		if ( typeof inputTypeColorSupport._cachedResult === 'undefined' ) {
			colorInput = $( "<input type='color'/>" )[ 0 ]; // if color element is supported, value will default to not null
			inputTypeColorSupport._cachedResult = colorInput.type === 'color' && colorInput.value !== '';
		}
		return inputTypeColorSupport._cachedResult;
	};

	$.spectrum = {};
	$.spectrum.localization = {};
	$.spectrum.palettes = {};

	$.fn.spectrum.processNativeColorInputs = function() {
		var colorInputs = $( 'input[type=color]' );
		if ( colorInputs.length && ! inputTypeColorSupport() ) {
			colorInputs.spectrum( {
				preferredFormat: 'hex6'
			} );
		}
	};

	$( function() {
		if ( $.fn.spectrum.load ) {
			$.fn.spectrum.processNativeColorInputs();
		}
	} );
} ) );
