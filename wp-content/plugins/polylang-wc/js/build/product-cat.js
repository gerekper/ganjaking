var __webpack_exports__ = {};
/**
 * Filters the media list when adding an image to a product category.
 *
 * @package Polylang-WC
 */

jQuery(
	function( $ ) {
		$.ajaxPrefilter(
			function ( options, originalOptions, jqXHR ) {
				if ( options.data?.includes( 'action=query-attachments' ) ) {
					options.data = 'lang=' + $( '#term_lang_choice' ).val() + '&' + options.data;
				}
			}
		);
	}
);

