/**
 * Enable this module in your ready function to cause all hash links to smooth scroll wherever the file is loaded!
 */

import convertElements from './convert-elements';
import scrollTo from '../dom/scroll-to';

const handleAnchorClick = ( e ) => {
	const target = document.getElementById( e.target.hash.substring( 1 ) );
	if ( ! target ) {
		return;
	}

	e.preventDefault();

	window.history.pushState( null, null, e.target.hash );

	scrollTo( {
		offset: -150,
		duration: 300,
		$target: $( target ),
	} );
};

const bindEvents = () => {
	const anchorLinks = convertElements(
		document.querySelectorAll( 'a[href^="#"]:not([href="#"])' )
	);
	if ( ! anchorLinks.length ) {
		return;
	}

	anchorLinks.forEach( ( link ) =>
		link.addEventListener( 'click', handleAnchorClick )
	);
};

const init = () => {
	bindEvents();
};

export default init;
