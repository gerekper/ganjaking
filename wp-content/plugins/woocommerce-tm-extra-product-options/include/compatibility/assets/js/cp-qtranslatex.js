( function( $ ) {
	'use strict';

	var TMEPOQTRANSLATEXJS = window.TMEPOQTRANSLATEXJS || null;

	if ( ! TMEPOQTRANSLATEXJS ) {
		return;
	}

	// Duplicated from q-translate-x
	Object.defineProperty( String.prototype, 'tm_xsplit', {
		value: function( _regEx ) {
			var start = 0;
			var arr = [];
			var result;

			// Most browsers can do this properly, so let them work, they'll do it faster
			if ( 'a~b'.split( /(~)/ ).length === 3 ) {
				return this.split( _regEx );
			}

			if ( ! _regEx.global ) {
				_regEx = new RegExp( _regEx.source, 'g' + ( _regEx.ignoreCase ? 'i' : '' ) );
			}

			// IE (and any other browser that can't capture the delimiter)
			// will, unfortunately, have to be slowed down
			while ( ( result = _regEx.exec( this ) ) !== null ) {
				arr.push( this.slice( start, result.index ) );
				if ( result.length > 1 ) {
					arr.push( result[ 1 ] );
				}
				start = _regEx.lastIndex;
			}
			if ( start < this.length ) {
				arr.push( this.slice( start ) );
			}
			if ( start === this.length ) {
				arr.push( '' );
			}

			return arr;
		}
	} );

	function qtranxj_get_split_blocks( text ) {
		var split_regex = /(<!--:[a-z]{2}-->|<!--:-->|\[:[a-z]{2}\]|\[:\])/gi;
		return text.tm_xsplit( split_regex );
	}

	function qtranxj_split_blocks( blocks ) {
		var result = {};
		var i;
		var lang;
		var clang_regex = /<!--:([a-z]{2})-->/gi;
		var blang_regex = /\[:([a-z]{2})\]/gi;
		var matches;
		var b;
		var j;

		for ( i = 0; i < TMEPOQTRANSLATEXJS.enabled_languages.length; i += 1 ) {
			lang = TMEPOQTRANSLATEXJS.enabled_languages[ i ];
			result[ lang ] = '';
		}

		if ( ! blocks || ! blocks.length ) {
			return result;
		}

		// No language separator found, enter it to all languages
		if ( blocks.length === 1 ) {
			b = blocks[ 0 ];
			for ( j = 0; j < TMEPOQTRANSLATEXJS.enabled_languages.length; j += 1 ) {
				lang = TMEPOQTRANSLATEXJS.enabled_languages[ j ];
				result[ lang ] += b;
			}
			return result;
		}

		lang = false;

		Object.keys( blocks ).forEach( function( ii ) {
			b = blocks[ ii ];

			if ( ! b.length ) {
				return;
			}
			matches = clang_regex.exec( b );
			clang_regex.lastIndex = 0;
			if ( matches !== null ) {
				lang = matches[ 1 ];
				return;
			}
			matches = blang_regex.exec( b );
			blang_regex.lastIndex = 0;
			if ( matches !== null ) {
				lang = matches[ 1 ];
				return;
			}
			if ( b === '<!--:-->' || b === '[:]' ) {
				lang = false;
				return;
			}
			if ( lang ) {
				result[ lang ] += b;
				lang = false;
			} else {
				// Keep neutral text
				Object.keys( result ).forEach( function( key ) {
					result[ key ] += b;
				} );
			}
		} );

		return result;
	}

	$.qtranxj_split = function( text ) {
		var blocks = qtranxj_get_split_blocks( text );
		return qtranxj_split_blocks( blocks );
	};
}( window.jQuery ) );
