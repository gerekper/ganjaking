/**
 * @function visible
 * @description Determine if an element is visible in the dom.
 *
 * @param {HTMLElement} elem The element to check
 * @return {boolean}
 */

export default function( elem ) {
	return !! (
		elem.offsetWidth ||
		elem.offsetHeight ||
		elem.getClientRects().length
	);
};
