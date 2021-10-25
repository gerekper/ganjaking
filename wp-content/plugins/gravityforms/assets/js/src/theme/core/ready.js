/**
 * @module
 * @exports ready
 * @description The core dispatcher for the dom ready event in javascript.
 */

import common from '../../common';
import { ready } from '@gravityforms/utils';

/**
 * @function bindEvents
 * @description Bind global event listeners here,
 */

const bindEvents = () => {
	console.log( 'Binding theme events' );
};

/**
 * @function init
 * @description The core dispatcher for init across the codebase.
 */

const init = () => {
	// initialize global events

	bindEvents();

	// initialize modules

	common();

	console.info(
		'Gravity Forms Theme: Initialized all javascript that targeted document ready.'
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
