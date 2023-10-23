'use strict';

/* global globalThis, jQuery, yith_wcan_shortcodes, accounting */

import { $ } from '../config.js';

export default class YITH_WCAN_Reset_Button {
	// current button
	$reset = null;

	// init object
	constructor( el ) {
		// current button
		this.$reset = el;

		this.$reset.on( 'click', function ( ev ) {
			ev.preventDefault();

			$( '.yith-wcan-filters' ).each( function () {
				const preset = $( this ).data( 'preset' );

				preset.deactivateAllFilters( true );
				preset.closeModal();
			} );
		} );

		this.$reset.data( 'reset', this ).addClass( 'enhanced' );
	}
}
