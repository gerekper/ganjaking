'use strict';

/* global globalThis, jQuery, yith_wcan_shortcodes, accounting */

import { $ } from '../config.js';

export default class YITH_WCAN_Filter_Labels {
	// current label set
	$label_set = null;

	// labels of current set
	$labels = false;

	// init object
	constructor( el ) {
		// current label set
		this.$label_set = el;

		this._initLabels();

		this.$label_set.data( 'filter_labels', this ).addClass( 'enhanced' );
	}

	// init labels
	_initLabels() {
		const self = this;

		this.getLabels().each( function () {
			const $label = $( this );

			self._initLabel( $label );
		} );
	}

	// init label
	_initLabel( $label ) {
		$label.on( 'click', ( ev ) => {
			if ( this.disableLabel( $label ) ) {
				ev.preventDefault();
			}
		} );
	}

	// get labels
	getLabels() {
		if ( false === this.$labels ) {
			this.$labels = this.$label_set.find( '.active-filter-label' );
		}

		return this.$labels;
	}

	// disable filter
	disableLabel( $label ) {
		const properties = $label.data( 'filters' );
		let result = false;

		// search for preset
		$( '.yith-wcan-filters' ).each( function () {
			const preset = $( this ).data( 'preset' );

			result =
				result ||
				preset.deactivateFilterByProperties( properties, true );
		} );

		return result;
	}
}
