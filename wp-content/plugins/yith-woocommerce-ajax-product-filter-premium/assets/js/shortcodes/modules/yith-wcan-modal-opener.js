'use strict';

/* global globalThis, jQuery, yith_wcan_shortcodes, accounting */

import { $ } from '../config.js';

export default class YITH_WCAN_Modal_Opener {
	// current button
	$button = null;

	// YITH_WCAN_Preset object
	preset = null;

	// preset dom node
	$preset = null;

	// is mobile flag
	isMobile = false;

	// init object
	constructor( el ) {
		// current button
		this.$button = el;

		this._initPreset();
		this._initResponsive();
		this._initActions();

		this.$button.data( 'modalOpener', this ).addClass( 'enhanced' );
	}

	// search for related preset
	_initPreset() {
		let target = this.$button.data( 'target' ),
			$target;

		if ( target ) {
			$target = $( `#${ target }` );
		} else {
			$target = $( '.yith-wcan-filters' );
		}

		if ( ! $target.length ) {
			return;
		}

		this.$preset = $target.first();
		this.preset = this.$preset.data( 'preset' );
	}

	// init responsive
	_initResponsive() {
		if ( ! yith_wcan_shortcodes.modal_on_mobile || ! this.preset ) {
			this.$button.hide();
			return;
		}

		const media = window.matchMedia(
			`(max-width: ${ yith_wcan_shortcodes.mobile_media_query }px)`
		);

		$( window )
			.on( 'resize', () => {
				const isMobile = !! media.matches;

				if ( isMobile !== this.isMobile ) {
					this.isMobile = isMobile;
					this._afterLayoutChange();
				}
			} )
			.resize();
	}

	// init actions
	_initActions() {
		if ( ! this.$preset?.length ) {
			return;
		}

		const self = this;

		this.$button.on( 'click', function ( ev ) {
			ev.preventDefault();

			self.preset.openModal();
		} );
	}

	// hide/show button when needed
	_afterLayoutChange() {
		this.isMobile ? this.$button.show() : this.$button.hide();
	}
}
