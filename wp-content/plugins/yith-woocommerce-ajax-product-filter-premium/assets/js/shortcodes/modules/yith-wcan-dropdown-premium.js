'use strict';

/* global globalThis, jQuery, yith_wcan_shortcodes, accounting */

import { $ } from '../config.js';
import YITH_WCAN_Dropdown from './yith-wcan-dropdown';

export default class YITH_WCAN_Dropdown_Premium extends YITH_WCAN_Dropdown {
	// preset DOM
	$preset = false;

	// retrieve preset containing, if any
	getPreset() {
		if ( this.preset ) {
			return this.preset;
		}

		this.$preset = this.$originalSelect.closest( '.yith-wcan-filters' );

		if ( ! this.$preset.length ) {
			return false;
		}

		return this.$preset;
	}

	// create dropdown
	_initTemplate() {
		if ( ! this.getPreset()?.hasClass( 'horizontal' ) ) {
			super._initTemplate();
		} else {
			const $dropdownSpan = $( '<div>', {
					class: 'dropdown-wrapper',
				} ),
				$matchingItemsList = $( '<ul/>', {
					class: 'matching-items filter-items',
				} );

			$dropdownSpan.append( $matchingItemsList );

			if ( this.options.showSearch ) {
				this._initSearchTemplate( $dropdownSpan );
			}

			if ( this.options.paginate ) {
				this._initShowMoreTemplate( $dropdownSpan );
			}

			this.$originalSelect.after( $dropdownSpan );
			this.$_main = null;
			this.$_label = null;
			this.$_dropdown = $dropdownSpan;
			this.$_items = $matchingItemsList;

			this._populateItems();
		}
	}
}
