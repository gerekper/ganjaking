/** global ywsbsSettings */

function number_format( number, decimals, decPoint, thousandsSep ) {
	number   = ( number + '' ).replace( /[^0-9+\-Ee.]/g, '' );
	var n    = !isFinite( +number ) ? 0 : +number;
	var prec = !isFinite( +decimals ) ? 0 : Math.abs( decimals );
	var sep  = ( typeof thousandsSep === 'undefined' ) ? ',' : thousandsSep;
	var dec  = ( typeof decPoint === 'undefined' ) ? '.' : decPoint;
	var s    = '';

	var toFixedFix = function ( n, prec ) {
		if ( ( '' + n ).indexOf( 'e' ) === -1 ) {
			return +( Math.round( n + 'e+' + prec ) + 'e-' + prec );
		} else {
			var arr = ( '' + n ).split( 'e' );
			var sig = '';
			if ( +arr[ 1 ] + prec > 0 ) {
				sig = '+';
			}
			return ( +( Math.round( +arr[ 0 ] + 'e' + sig + ( +arr[ 1 ] + prec ) ) + 'e-' + prec ) ).toFixed( prec );
		}
	};

	// @todo: for IE parseFloat(0.55).toFixed(0) = 0;
	s = ( prec ? toFixedFix( n, prec ).toString() : '' + Math.round( n ) ).split( '.' );
	if ( s[ 0 ].length > 3 ) {
		s[ 0 ] = s[ 0 ].replace( /\B(?=(?:\d{3})+(?!\d))/g, sep );
	}
	if ( ( s[ 1 ] || '' ).length < prec ) {
		s[ 1 ] = s[ 1 ] || '';
		s[ 1 ] += new Array( prec - s[ 1 ].length + 1 ).join( '0' );
	}

	return s.join( dec );
}


/**
 * Formats a number using site's current locale
 *
 * @see http://locutus.io/php/strings/number_format/
 * @param {Number|String} number number to format
 * @param {int|null} [precision=null] optional decimal precision
 * @returns {?String} A formatted string.
 */
export function numberFormat( number, precision = null ) {
	if ( 'number' !== typeof number ) {
		number = parseFloat( number );
	}

	if ( isNaN( number ) ) {
		return '';
	}

	const decimalSeparator  = ywsbsSettings.wc.currency.decimal_separator;
	const thousandSeparator = ywsbsSettings.wc.currency.thousand_separator;
	precision               = parseInt( precision );

	if ( isNaN( precision ) ) {
		const [, decimals] = number.toString().split( '.' );
		precision          = decimals ? decimals.length : 0;
	}

	return number_format( number, precision, decimalSeparator, thousandSeparator );
}

/**
 * Formats money with a given currency code. Uses site's currency settings for formatting.
 *
 * @param   {Number|String} number number to format
 * @param   {String}        currencySymbol currency code e.g. '$'
 * @returns {?String} A formatted string.
 */
export function formatCurrency( number, currencySymbol = false ) {
	// default to wcSettings (and then to $) if currency symbol is not passed in
	if ( !currencySymbol ) {
		currencySymbol = ywsbsSettings.wc.currency.symbol;
	}

	const precision       = ywsbsSettings.wc.currency.precision;

	const formattedNumber = numberFormat( number, precision );
	const priceFormat     = ywsbsSettings.wc.currency.price_format;

	if ( '' === formattedNumber ) {
		return formattedNumber;
	}

	return sprintf( priceFormat, currencySymbol, formattedNumber );
}

