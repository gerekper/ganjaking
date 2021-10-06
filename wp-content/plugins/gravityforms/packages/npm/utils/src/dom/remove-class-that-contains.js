/**
 * Remove a class from an element that contains a string
 *
 * @param el
 * @param string
 */

export default function( el, string = '' ) {
	for ( let i = 0; i < el.classList.length; i++ ) {
		if ( el.classList.item( i ).indexOf( string ) !== -1 ) {
			el.classList.remove( el.classList.item( i ) );
		}
	}
};
