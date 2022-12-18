/**
 * Porto Dependent Plugin - ProductVideoThumbnail
 *
 */

'use strict';

window.theme || ( window.theme = {} );

( function ( $ ) {

	var ProductVideoThumbnail = {
		openVideoPopup: function ( e ) {
			if ( $(this).hasClass( 'popup-youtube' ) || $(this).hasClass( 'popup-vimeo' ) ) {
				return;
			}
			e.preventDefault();

			var data = $( this ).siblings( '.porto-video-thumbnail-data' ).html();

			if ( $.fn.magnificPopup ) {
				$.magnificPopup.open(
					{
						type: 'inline',
						mainClass: "porto-video-popup-wrapper mfp-fade",
						preloader: false,
						items: {
							src: '<div class="porto-video-popup-wrapper mx-auto" style="max-width: 50rem;">' + data + '</div>'
						},
						callbacks: {
							beforeClose: function () {
								this.container.empty();
							}
						}
					}
				);
			}
		}
	};

	theme.ProductVideoThumbnail = ProductVideoThumbnail;

	$( document ).ready( function () {
		$( '.product-thumbnails .porto-video-thumbnail-viewer' ).on( 'click', theme.ProductVideoThumbnail.openVideoPopup );
	} );
} )( jQuery );
