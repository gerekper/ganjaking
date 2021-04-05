/**
 * jscroll-init.js
 *
 * Initialize the jscroll script
 */
( function() {
	jQuery( document ).ready( function() {
		jQuery( 'div[class^="columns"] + .storefront-sorting' ).hide();

		jQuery( '.site-main' ).jscroll({
		    loadingHtml: '<div class="sp-loader"></div>',
		    nextSelector: 'a.next',
		    contentSelector: '.scroll-wrap'
		});
	});
} )();
