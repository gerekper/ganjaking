/**
 *
 * Get immediate child nodes and return an array of them
 *
 * @param el
 * @return {Array} Iterable array of dom nodes
 */

export default function( el ) {
	const children = [];
	let i = el.children.length;
	for ( i; i --; ) { // eslint-disable-line
		if ( el.children[ i ].nodeType !== 8 ) {
			children.unshift( el.children[ i ] );
		}
	}

	return children;
};
