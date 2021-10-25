/**
 * Gets the closest ancestor that matches a selector string
 *
 * @param el
 * @param selector
 * @return {*}
 */

export default function( el, selector ) {
	let matchesFn;
	let parent;

	[
		'matches',
		'webkitMatchesSelector',
		'mozMatchesSelector',
		'msMatchesSelector',
		'oMatchesSelector',
	].some( ( fn ) => {
		if ( typeof document.body[ fn ] === 'function' ) {
			matchesFn = fn;
			return true;
		}
		/* istanbul ignore next */
		return false;
	} );

	while ( el ) {
		parent = el.parentElement;
		if ( parent && parent[ matchesFn ]( selector ) ) {
			return parent;
		}

		el = parent; // eslint-disable-line
	}

	return null;
};
