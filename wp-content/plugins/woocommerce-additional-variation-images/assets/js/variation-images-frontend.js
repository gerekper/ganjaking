jQuery( document ).ready( function( $ ) {
	'use strict';

	var wcavi_original_gallery_images = $( wc_additional_variation_images_local.gallery_images_class ).html();
	var wcavi_original_main_images    = $( wc_additional_variation_images_local.main_images_class ).html();

	// create namespace to avoid any possible conflicts
	$.wc_additional_variation_images_frontend = {
		/**
		 * Get WC AJAX endpoint URL.
		 *
		 * @param  {String} endpoint Endpoint.
		 * @return {String}
		 */
		getAjaxURL: function( endpoint ) {
			return wc_additional_variation_images_local.ajax_url
				.toString()
				.replace( '%%endpoint%%', 'wc_additional_variation_images_' + endpoint );
		},

		isCloudZoom: function() {
			var cloudZoomClass = $( 'a.woocommerce-main-image' ).hasClass( 'cloud-zoom' );

			return cloudZoomClass;
		},

		runLightBox: function( callback ) {
			// user trigger
			$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_lightbox', wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images );

			// if cloud zoom is active
			if ( $.wc_additional_variation_images_frontend.isCloudZoom() ) {

				$( '.cloud-zoom' ).each( function() {
					$( this ).data( 'zoom' ).destroy();
				});

				$( '.cloud-zoom, .cloud-zoom-gallery' ).CloudZoom();
			} else {

				if ( $.isFunction( $.fn.prettyPhoto ) ) {
					// lightbox
					$( wc_additional_variation_images_local.lightbox_images ).prettyPhoto({
						hook: 'data-rel',
						social_tools: false,
						theme: 'pp_woocommerce',
						horizontal_padding: 20,
						opacity: 0.8,
						deeplinking: false
					});
				}
			}

			$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_lightbox_done', [ wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images ] );

			if ( callback ) {
				callback();
			}
		},

		reset: function( callback ) {

			if ( wc_additional_variation_images_local.custom_reset_swap == true ) {
				var response = '';

				$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_on_reset', [ response, wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images ] );

			} else {
				// replace the original gallery images
				$( wc_additional_variation_images_local.gallery_images_class ).fadeOut( 50, function() {
					$( this ).html( wcavi_original_gallery_images ).hide().fadeIn( 100, function() {
						$.wc_additional_variation_images_frontend.runLightBox();
					});
				});
			}

			$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_on_reset_done', [ wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images ] );

			$.wc_additional_variation_images_frontend.initProductGallery();

			if ( callback ) {
				callback();
			}
		},

		imageSwap: function( response, callback ) {
			if ( wc_additional_variation_images_local.custom_swap == true ) {
				$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_image_swap_callback', [ response, wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images ] );

			} else {
				if ( ! wc_additional_variation_images_local.bwc ) {
					var parent = $( wc_additional_variation_images_local.main_images_class ).parent();

					// we subtract the (inner) dimensions by 92 to get the outer dimensions
					var width  = $( response.main_images ).find( 'img' ).first().attr( 'width' ) - 92;
					var height = $( response.main_images ).find( 'img' ).first().attr( 'height' ) - 92;

					$( wc_additional_variation_images_local.main_images_class ).remove();
					$.when( parent.prepend( response.main_images ) ).then( function() {
						$( '.woocommerce-product-gallery__image' ).width( width );
						$( '.woocommerce-product-gallery__wrapper' ).width( width ).height( height );
					});
				} else {
					$( wc_additional_variation_images_local.gallery_images_class ).fadeOut( 50, function() {
						$( this ).html( response.gallery_images ).hide().fadeIn( 100, function() {
							$.wc_additional_variation_images_frontend.runLightBox();
						});
					});
				}
			}

			$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_image_swap_done_callback', [ wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images ] );


			$.wc_additional_variation_images_frontend.initProductGallery();

			if ( callback ) {
				callback();
			}
		},

		imageSwapOriginal: function( callback ) {

			if ( wc_additional_variation_images_local.custom_original_swap == true ) {
				var response = '';

				$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_ajax_default_image_swap_callback', [ response, wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images ] );

			} else {
				$( wc_additional_variation_images_local.gallery_images_class ).fadeOut( 50, function() {
					$( this ).html( wcavi_original_gallery_images ).hide().fadeIn( 100, function() {
						$.wc_additional_variation_images_frontend.runLightBox();
					});
				});
			}

			$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_ajax_default_image_swap_done_callback', [ wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images ] );

			$.wc_additional_variation_images_frontend.initProductGallery();

			if ( callback ) {
				callback();
			}
		},

		hideGallery: function() {
			$( wc_additional_variation_images_local.gallery_images_class ).hide().css( 'visibility', 'hidden' );
		},

		showGallery: function() {
			$( wc_additional_variation_images_local.gallery_images_class ).css( 'visibility', 'visible' ).fadeIn( 'fast' );
		},

		initProductGallery: function() {
			$( '.woocommerce-product-gallery' ).each( function() {
				$( this ).wc_product_gallery();
			} );
		},

		getImages: function( data ) {
			return $.ajax({
				type:    'POST',
				data:    data,
				url:     $.wc_additional_variation_images_frontend.getAjaxURL( 'get_images' )
			});
		},

		init: function() {
			// when variation changes trigger. this is used for WC 3.0 only.
			if ( ! wc_additional_variation_images_local.bwc ) {
				$( 'form.variations_form' ).on( 'reset_image', function( event, variation ) {
					$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_reset_variation' );

					$( wc_additional_variation_images_local.gallery_images_class ).block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});

					var data = {
						security: wc_additional_variation_images_local.ajaxImageSwapNonce,
						post_id: $( 'form.variations_form' ).data( 'product_id' )
					};

					$.when( $.wc_additional_variation_images_frontend.getImages( data ) ).then( function( response ) {
						if ( response ) {

							$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_ajax_response_callback', [ response, wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images ] );
	
							// replace with new image set
							$.wc_additional_variation_images_frontend.imageSwap( response );

						} else {
	
							$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_ajax_response_callback', [ response, wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images ] );
	
							// replace with original image set
							$.wc_additional_variation_images_frontend.imageSwapOriginal();
						}
						
						$( wc_additional_variation_images_local.gallery_images_class ).unblock();
					});
				});
			}

			// when variation changes trigger
			$( 'form.variations_form' ).on( 'show_variation', function( event, variation ) {
				$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_before_show_variation' );

				$( wc_additional_variation_images_local.gallery_images_class ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});

				var data = {
					security: wc_additional_variation_images_local.ajaxImageSwapNonce,
					variation_id: variation.variation_id,
					post_id: $( 'form.variations_form' ).data( 'product_id' )
				};

				$.when( $.wc_additional_variation_images_frontend.getImages( data ) ).then( function( response ) {
					if ( response ) {

						$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_ajax_response_callback', [ response, wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images ] );

						// replace with new image set
						$.wc_additional_variation_images_frontend.imageSwap( response );

					} else {

						$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_ajax_response_callback', [ response, wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images ] );

						// replace with original image set
						$.wc_additional_variation_images_frontend.imageSwapOriginal();
					}

					$( wc_additional_variation_images_local.gallery_images_class ).unblock();
				});
			});

			// on reset click
			$( 'form.variations_form' ).on( 'click', '.reset_variations', function() {
				$.wc_additional_variation_images_frontend.reset();
			});

			// on reset select trigger
			$( 'form.variations_form' ).on( 'reset_image', function() {
				$.wc_additional_variation_images_frontend.reset();
			});

			// add support for swatches and photos plugin
			$( '#variations_clear' ).on( 'click', function() {
				$.wc_additional_variation_images_frontend.reset();
			});

			$( '.swatch-anchor' ).on( 'click', function() {
				var option = $( this ).parent( '.select-option' );

				if ( option.hasClass( 'selected' ) ) {
					$.wc_additional_variation_images_frontend.reset();
				}
			});

			$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_init', [ wc_additional_variation_images_local.gallery_images_class, wc_additional_variation_images_local.main_images_class, wcavi_original_gallery_images, wcavi_original_main_images ] );
		}
	}; // close namespace

	$.wc_additional_variation_images_frontend.init();

// end document ready
});
