jQuery(
	function( $ ) {
		$.fn.prettyPhoto(
			{
				social_tools: false,
				theme: 'pp_woocommerce pp_woocommerce_quick_view',
				opacity: 0.8,
				modal: false,
				horizontal_padding: 50,
				default_width: '90%',
				default_height: '90%',
				changepicturecallback: function() {
					$( '.quick-view .woocommerce-product-gallery' ).wc_product_gallery();
					$( '.quick-view .variations_form' ).wc_variation_form();
					$( '.quick-view .variations_form' ).trigger( 'wc_variation_form' );
					$( '.quick-view .variations_form .variations select' ).change();
					$( 'body' )
						.trigger( 'quick-view-displayed' )
						.trigger( 'wc_currency_converter_calculate' );
				}
			}
		);
		$( document ).on(
			'click',
			quickview_options.selector,
			function() {
				var product_id = $( this ).data( 'product_id' );

				if ( product_id ) {
					$.prettyPhoto.open(
						decodeURIComponent(
							quickview_options.link.replace(
								'product_id_placeholder',
								product_id
							)
						)
					);

					return false;
				}

				return true;
			}
		);
	}
);
