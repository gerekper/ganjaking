import convertElements from './convert-elements';
import visible from './visible';

/**
 * @function getFocusable
 * @description Get focusable elements inside a container and return as an array.
 *
 * @param container the parent to search for focusable elements inside of
 * @return {*[]}
 */

export default function( container = document ) {
	const focusable = convertElements(
		container.querySelectorAll(
			'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
		)
	);
	return focusable.filter( ( item ) => visible( item ) );
};
