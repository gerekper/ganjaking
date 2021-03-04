(function () {
	const callback = () => {
		const endpoint = wc_additional_variation_images_local.ajax_url.toString().replace( '%%endpoint%%', 'wc_additional_variation_images_get_images' )
		const endpointNonce = wc_additional_variation_images_local.ajaxImageSwapNonce;
		const galleryStatus = {};

		const jqueryFunctionExists = function (name) {
			return typeof jQuery.fn[name] === 'function';
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

		const blockUiParams = {
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		};

		const loadGallery = function ( variationId = 0, callback ) {
			const loadingGallery = jQuery.Deferred( callback );

			if ( galleryStatus[ variationId ] && galleryStatus[ variationId ].promise.state() === 'resolved' ) {
				return loadingGallery.resolve( variationId, galleryStatus[ variationId ].result );
			}

			if ( ! galleryStatus[ variationId ] || galleryStatus[ variationId ].promise.state() === 'rejected' ) {
				const promise = getImages( variationId );

				galleryStatus[ variationId ] = {
					promise: promise,
					result: '',
				}
			}

			jQuery.when( galleryStatus[ variationId ].promise ).then( function( response ) {
				galleryStatus[ variationId ].result = response.main_images || '';
				loadingGallery.resolve( variationId, galleryStatus[ variationId ].result );
			}, function() {
				loadingGallery.reject();
			} );

			return loadingGallery.promise();
		};

		const showGallery = function ( variationId = 0, variationForm ) {
			const $container = jQuery( variationForm ).closest( '.product' );

			if ( $container.length === 0 ) {
				return;
			}

			const $allGalleries    = $container.find( wc_additional_variation_images_local.main_images_class );
			const $originalGallery = $container.find( wc_additional_variation_images_local.main_images_class + ':not(.woocommerce-product-gallery--wcavi)' );

			const showSelectedGallery = function ( $galleryToShow ) {
				const $visible = $container.find( wc_additional_variation_images_local.main_images_class + ':visible' );

				if ( $galleryToShow.is( ':hidden' ) || $visible.length > 1 ) {
					$visible.hide();
					$galleryToShow.show();
				}
			}

			const getGallery = function( variationId ) {
				return $container.find( '.woocommerce-product-gallery--variation-' + variationId );
			};

			const galleryExists = function( variationId ) {
				return getGallery( variationId ).length > 0;
			};

			const initGallery = function( galleryVariationId ) {
				const gallery = getGallery( galleryVariationId );

				if ( gallery.length ) {
					gallery.wc_product_gallery();
					initLightbox();
				}
			};

			const createGallery = function( galleryVariationId, galleryHtml ) {
				$allGalleries.first().after( galleryHtml );
				initGallery( galleryVariationId );
			};

			// Store the selected variation ID at container level so it can be looked up once promises resolve.
			$container.data( 'currentVariationId', variationId );

			// Show original if no variation is defined.
			if ( variationId === 0 ) {
				return showSelectedGallery( $originalGallery );
			}

			// Load the gallery using a promise, and when resolved, show the variation gallery.
			loadGallery( variationId, function() {
				$allGalleries.block( blockUiParams );
			} ).then( function( galleryVariationId, galleryHtml ) {
				if ( ! galleryExists( galleryVariationId ) ) {
					createGallery( galleryVariationId, galleryHtml );
				}
				if ( galleryVariationId === $container.data( 'currentVariationId' ) ) {
					showSelectedGallery( getGallery( galleryVariationId ) );
				}
			} ).always( function() {
				$allGalleries.unblock();
			} );
		};

		// Core triggers events through jQuery.
		jQuery( 'form.variations_form' )
			.on( 'reset_data', function( event, variation ) {
				showGallery( 0, event.target );
			})
			.on( 'show_variation', function( event, variation ) {
				jQueryTriggerEvent( 'wc_additional_variation_images_frontend_before_show_variation' );
				showGallery( parseInt( variation.variation_id, 10 ), event.target );
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