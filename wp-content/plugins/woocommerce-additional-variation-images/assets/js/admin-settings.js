document.addEventListener('DOMContentLoaded',  function() {
	'use strict';

	if ( typeof jQuery !== 'function' ) {
		console.error( 'jQuery is required for WooCommerce Additional Variation Images' );
		return;
	}

	var $ = jQuery;
	
	// create namespace to avoid any possible conflicts
	$.wc_additional_variation_images_admin = {
		getVariationIDs: function() {
			var ids = [];

			$.each( $( '#variable_product_options .woocommerce_variation' ), function() {
				ids.push( $( this ).find( '.upload_image .upload_image_button' ).prop( 'rel' ) );
			});

			return ids;
		},

		runSortable: function() {
			$.each( $( '#variable_product_options .woocommerce_variation' ), function() {
				var id = $( this ).find( 'a.upload_image_button' ).prop( 'rel' );

				$( this ).find( 'ul.wc-additional-variations-images-list' ).sortable({
					update: function() {
						$.wc_additional_variation_images_admin.prepThumbs( id );
					},
					placeholder: 'sortable-placeholder',
					cursor: 'move'
				});
			});
		},

		prepThumbs: function( id ) {
			if ( id.length <= 0 ) {
				return;
			}

			var order = [],
				container = $( '#variable_product_options .woocommerce_variation a.upload_image_button[rel="' + id + '"]' ).parent( '.upload_image' );
			
			if ( container.find( 'ul.wc-additional-variations-images-list li' ).length ) {
				// collect the attachment id from each thumb
				$.each( container.find( 'ul.wc-additional-variations-images-list li' ), function() {
					order.push( $( this ).find( 'a.wc-additional-variations-images-thumb' ).data( 'id' ) );
				});

				container.find( 'input.wc-additional-variations-images-thumbs-save' ).val( order );
			} else {
				container.find( 'input.wc-additional-variations-images-thumbs-save' ).val( '' );
			}

			order.join( ',' );

			// just to trigger a change so the save button enables
			$( '#variable_product_options' ).find( 'input' ).eq( 0 ).change();

			// add proper update class so WC knows to trigger a save for specific variation
			container.parents( '.woocommerce_variation' ).eq( 0 ).addClass( 'variation-needs-update' );
		},

		init: function() {
			var i18n = wc_additional_variation_images_local,
				$data = {
					action: 'wc_additional_variation_images_load_images_ajax',
					ajaxAdminLoadImageNonce: wc_additional_variation_images_local.ajaxAdminLoadImageNonce,
					variation_ids: $.wc_additional_variation_images_admin.getVariationIDs()
				};

			if ( $.wc_additional_variation_images_admin.getVariationIDs().length ) {
				$.post( wc_additional_variation_images_local.ajaxurl, $data, function( response ) {

					for ( var id in response.images ) {
						if ( response.images.hasOwnProperty( id ) ) {
							var html = '<h4 class="wc-additional-variation-images-title">' + i18n.adminTitleText + ' <a href="#" class="wc-additional-variations-images-tip" data-tip="' + i18n.adminAddImagesTip + '">[?]</a></h4>' + response.images[ id ] + '<a href="#" class="add-additional-images">' + 
									i18n.adminAddImagesText + '</a>';

							// add only if not exists
							if ( ! $( '#variable_product_options .woocommerce_variation a.upload_image_button[rel="' + id + '"]' ).parents( '.upload_image' ).find( 'a.wc-additional-variations-images-tip' ).length ) {
								
								$( '#variable_product_options .woocommerce_variation a.upload_image_button[rel="' + id + '"]' ).after( html );
							}
						}
					}

					// add tool tip
					$( '.wc-additional-variations-images-tip' ).tipTip({
						'attribute': 'data-tip',
						'fadeIn': 50,
						'fadeOut': 50
					});	

					$.wc_additional_variation_images_admin.runSortable();				
				});
			}	

			// add images
			$( '#variable_product_options .woocommerce_variation' ).on( 'click', 'a.add-additional-images', function( e ) {
				e.preventDefault();

				var id = $( this ).parents( '.upload_image' ).find( 'a.upload_image_button' ).prop( 'rel' ),
					thumbs = $( this ).parents( '.upload_image' ).find( 'ul.wc-additional-variations-images-list' ),
					mediaFrame;

				// create the media frame
				mediaFrame = wp.media.frames.mediaFrame = wp.media({

					title: i18n.adminMediaTitle,

					button: {
						text: i18n.adminMediaAddImageText
					},

					// only images
					library: {
						type: 'image'
					},

					multiple: true
				});

				// after a file has been selected
				mediaFrame.on( 'select', function() {
					var selection = mediaFrame.state().get( 'selection' );

					selection.map( function( attachment ) {
	
						attachment = attachment.toJSON();

						if ( attachment.id ) {
							var url = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

							thumbs.append( '<li><a href="#" class="wc-additional-variations-images-thumb" data-id="' + attachment.id + '"><img src="' + url + '" /><span class="overlay"></span></a></li>' );
						}
					});

					// make sure attachments are link to the variation post id instead of parent post id
					wp.media.model.settings.post.id = id;

					$.wc_additional_variation_images_admin.runSortable();
					$.wc_additional_variation_images_admin.prepThumbs( id );
				});

				// open the modal frame
				mediaFrame.open();
			});
			
			// delete images
			$( '#variable_product_options .woocommerce_variation' ).on( 'click', 'a.wc-additional-variations-images-thumb', function( e ) {
				e.preventDefault();
				
				var id = $( this ).parents( '.upload_image' ).find( 'a.upload_image_button' ).prop( 'rel' );

				$( this ).parent( 'li' ).remove();

				$.wc_additional_variation_images_admin.prepThumbs( id );
			});
		}
	}; // close namespace
	
	// run init
	$.wc_additional_variation_images_admin.init();

	// run init on add variation event trigger
	$( 'body' ).on( 'woocommerce_variations_added woocommerce_variations_loaded', function() {
		$.wc_additional_variation_images_admin.init();
	});
// end document ready
});