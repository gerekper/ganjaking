/**
 * Util to see if we should load a given chunk on a page. Just add data-load-chunk-UNIQUE to load that particular
 * one on any element on the page.
 *
 * @param name chunk name
 */

export default function( name = '' ) {
	const node = document.querySelectorAll( `[data-load-chunk-${ name }]` );
	return node.length > 0;
};
