/**
 * Compares an els classList against an array of strings to see if any match
 *
 * @param el the element to check against
 * @param arr The array of classes as strings to test against
 * @param prefix optional prefix string applied to all test strings
 * @param suffix optional suffix string
 */

export default function( el, arr = [], prefix = '', suffix = '' ) {
	return arr.some( ( c ) =>
		el.classList.contains( `${prefix}${c}${suffix}` )
	);
}
