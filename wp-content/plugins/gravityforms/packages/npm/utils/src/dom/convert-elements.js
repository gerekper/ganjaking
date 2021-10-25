/**
 * Highly efficient function to convert a nodelist into a standard array. Allows you to run Array.forEach
 *
 * @param {Element|NodeList} elements to convert
 * @return {Array} Of converted elements
 */

export default function( elements = [] ) {
	const converted = [];
	let i = elements.length;
	for ( i; i--; converted.unshift( elements[ i ] ) ); // eslint-disable-line

	return converted;
};
