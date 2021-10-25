/**
 * @module
 * @exports ready
 * @description The core dispatcher for the dom ready event in javascript.
 */

import common from 'common';
import { ready } from '@gravityforms/utils';

/**
 * @function bindEvents
 * @description Bind global event listeners here,
 */

const bindEvents = () => {};

/**
 * @function init
 * @description The core dispatcher for init across the codebase.
 */

const init = () => {
	// initialize global events

	bindEvents();

	// initialize common modules

	common();

	// if ( document.getElementById( 'gf_toolbar_buttons_container' ) ) {
	// 	import( '../react' /* webpackChunkName:"admin-example" */ ).then(
	// 		( module ) => {
	// 			module.default( document.getElementById( 'gf_toolbar_buttons_container' ) );
	// 		}
	// 	);
	// }

	// initialize admin modules

	console.info(
		'Gravity Forms Admin: Initialized all javascript that targeted document ready.'
	);
};

/**
 * @function domReady
 * @description Export our dom ready enabled init.
 */

const domReady = () => {
	ready( init );
};

export default domReady;
