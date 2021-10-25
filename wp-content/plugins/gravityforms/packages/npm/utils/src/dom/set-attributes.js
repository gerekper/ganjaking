/**
 * Set multiple element attributes at once
 *
 * @param el
 * @param attrs
 */

export default function( el, attrs ) {
	for ( const key in attrs ) {
		el.setAttribute( key, attrs[ key ] );
	}
};
