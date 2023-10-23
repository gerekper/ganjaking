'use strict';

/* global globalThis, jQuery, yith_wcan_shortcodes, accounting */

import YITH_WCAN_Filter from './modules/yith-wcan-filter';
import YITH_WCAN_Filter_Labels from './modules/yith-wcan-filter-labels';
import YITH_WCAN_Reset_Button from './modules/yith-wcan-reset-button';
import YITH_WCAN_Modal_Opener from './modules/yith-wcan-modal-opener';
import YITH_WCAN_Preset from './modules/yith-wcan-preset-premium';

jQuery( function ( $ ) {
	$( document )
		.on(
			'yith_wcan_init_shortcodes yith_plugin_fw_gutenberg_success_do_shortcode',
			function () {
				$( '.yith-wcan-filters' )
					.not( '.enhanced' )
					.each( function () {
						new YITH_WCAN_Preset( $( this ) );
					} );

				$( '.yith-wcan-active-filters' )
					.not( '.enhanced' )
					.each( function () {
						new YITH_WCAN_Filter_Labels( $( this ) );
					} );

				$( '.yith-wcan-reset-filters' )
					.not( '.enhanced' )
					.each( function () {
						new YITH_WCAN_Reset_Button( $( this ) );
					} );

				$( '.yith-wcan-filters-opener' )
					.not( '.enhanced' )
					.each( function () {
						new YITH_WCAN_Modal_Opener( $( this ) );
					} );
			}
		)
		.trigger( 'yith_wcan_init_shortcodes' );

	globalThis.product_filter = new YITH_WCAN_Filter();
} );
