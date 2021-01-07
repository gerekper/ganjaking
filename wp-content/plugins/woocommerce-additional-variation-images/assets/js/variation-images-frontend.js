(function () {
	const callback = () => {
		const endpoint = wc_additional_variation_images_local.ajax_url.toString().replace( '%%endpoint%%', 'wc_additional_variation_images_get_images' )
		const endpointNonce = wc_additional_variation_images_local.ajaxImageSwapNonce;
		const loadedGalleries = [];

		const jqueryFunctionExists = function (name) {
			return jQuery.isFunction( jQuery.fn[name] );
		};

		const jQueryTriggerEvent = function (name, params = []) {
			jQuery( 'form.variations_form' ).trigger( name, params );
		};

		const getImages = function ( variationId ) {
			return jQuery.ajax({
				type: 'POST',
				data: {
					security: endpointNonce,
					variation_id: variationId,
				},
				url: endpoint
			});
		}

		const initLightbox = function () {
			jQueryTriggerEvent( 'wc_additional_variation_images_frontend_lightbox' );
			if ( jqueryFunctionExists( 'prettyPhoto' ) ) {
				jQuery( wc_additional_variation_images_local.lightbox_images ).prettyPhoto({
					hook: 'data-rel',
					social_tools: false,
					theme: 'pp_woocommerce',
					horizontal_padding: 20,
					opacity: 0.8,
					deeplinking: false
				});
			}
			jQueryTriggerEvent( 'wc_additional_variation_images_frontend_lightbox_done' );
		};

		const loadGallery = function ( variationId = 0, callback = null ) {
			jQuery( wc_additional_variation_images_local.gallery_images_class ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			jQuery.when( getImages( variationId ) ).then( function( response ) {
				loadedGalleries.push( variationId );

				if ( response && response.main_images.length ) {
					const variationSelector = '.woocommerce-product-gallery--variation-' + variationId;

					// Append gallery HTML and init.
					jQuery( wc_additional_variation_images_local.main_images_class ).first().before( response.main_images );
					jQuery( variationSelector ).wc_product_gallery();
					initLightbox();
				}

				jQuery( wc_additional_variation_images_local.gallery_images_class ).unblock();
				showGallery( variationId );
			} );
		};

		const showOriginalGallery = function () {
			const mainNotVariationSelector = wc_additional_variation_images_local.main_images_class + ':not(.woocommerce-product-gallery--wcavi)';

			if ( jQuery( mainNotVariationSelector ).is(':visible') ) {
				return;
			}

			jQuery( wc_additional_variation_images_local.main_images_class + ':visible' ).hide();
			jQuery( mainNotVariationSelector ).show();
		};

		const showGallery = function ( variationId = 0 ) {
			const variationSelector = '.woocommerce-product-gallery--variation-' + variationId;
			const variationGallery = document.querySelector(variationSelector);

			// Gallery does not exist so query it.
			if ( variationGallery === null ) {
				showOriginalGallery();
			} else {
				jQuery( wc_additional_variation_images_local.main_images_class + ':visible' ).hide();
				jQuery( variationSelector ).show();
			}
		};

		// Core triggers events through jQuery.
		jQuery( 'form.variations_form' )
			.on( 'reset_data', function( event, variation ) {
				showOriginalGallery();
			})
			.on( 'show_variation', function( event, variation ) {
				jQueryTriggerEvent( 'wc_additional_variation_images_frontend_before_show_variation' );

				const variationId = parseInt( variation.variation_id, 10 );

				if ( loadedGalleries.indexOf( variationId ) === -1 ) {
					loadGallery( variationId );
				} else {
					showGallery( variationId );
				}
			});

		jQueryTriggerEvent( 'wc_additional_variation_images_frontend_init' );
	};
	if (
		document.readyState === 'complete' ||
		( document.readyState !== 'loading' &&
			! document.documentElement.doScroll )
	) {
		callback();
	} else {
		document.addEventListener('DOMContentLoaded', callback);
	}
})();