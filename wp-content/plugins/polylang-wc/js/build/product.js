var __webpack_exports__ = {};
/**
 * Ajax action when changing the product language.
 *
 * @package Polylang-WC
 */

jQuery(
	function( $ ) {
		$( document ).ajaxSuccess(
			function( event, xhr, dataFromAjaxRequest ){
				if ( 'string' === typeof dataFromAjaxRequest.data) {
					var data = wpAjax.unserialize( dataFromAjaxRequest.data ); // what were the data sent by the ajax request?
					if (
						'undefined' !== typeof( data['action'] ) && 'post_lang_choice' === data['action'] &&
						'undefined' !== typeof( data['post_type'] ) && 'product' === data['post_type'] &&
						'undefined' !== typeof( data['lang'] ) && 'undefined' !== typeof( data['post_id'] && 'undefined' !== typeof( data['_pll_nonce'] ) )
						) {
						updateProductAttributes( data['post_id'], data['lang'], data['_pll_nonce'] );
					}
				}
			}
		);
		function updateProductAttributes( post_id, lang, _pll_nonce ) {
			var attributes = new Array();

			// Get the attributes name and index.
			$( 'input[name*="attribute_names"]' ).each(
				function() {
					var selectFielName = $( this ).attr( 'name' );
					n = selectFielName.substring( 16, selectFielName.length - 1 );
					attributes[n] = $( this ).val();
				}
			);

			if ( attributes.length ) {
				var data = {
					action: 'product_lang_choice',
					lang: lang,
					post_id: post_id,
					attributes: attributes,
					_pll_nonce: _pll_nonce,
				}

				$.post(
					ajaxurl,
					data ,
					function( response ) {
						// Target a non existing WP HTML id to avoid a conflict with WP ajax requests.
						var res = wpAjax.parseAjaxResponse( response, 'pll-ajax-response' );
						$.each(
							res.responses,
							function() {
								switch ( this.what ) {
									case 'attributes':
										// Replace only options to avoid loosing the bind with the select2.
										$.each(
											this.supplemental,
											function( i, value ) {
												$( 'select[name="attribute_values[' + i.substring( 6 ) + '][]"]' ).html( value ).trigger( 'change' ); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.html
											}
										);
										break;
								}
							}
						);
					}
				);
			}
		}
	}
);

