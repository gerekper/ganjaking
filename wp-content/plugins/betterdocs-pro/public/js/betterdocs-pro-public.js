(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).ready(function() {
		$('.betterdocs-feelings').on( 'click', function( e ) {
			e.preventDefault();
			
			var feelings = e.currentTarget.dataset.feelings;
			if( betterdocs != undefined && 
				betterdocs.FEEDBACK != undefined && 
				betterdocs.FEEDBACK.DISPLAY != undefined && 
				betterdocs.FEEDBACK.DISPLAY == true ) {
				var URL = betterdocs.FEEDBACK.URL + '/' + betterdocspublic.post_id + '&feelings=' + feelings;
				jQuery.ajax({
					url : URL,
					method : 'POST',
					success : function( res ){
						if(res === true) {
							$('.betterdocs-article-reactions-heading,.betterdocs-article-reaction-links').fadeOut(1000);
							$('.betterdocs-article-reactions').html('<p>'+betterdocs.FEEDBACK.SUCCESS+'</p>').fadeIn(1000);
						}
					}
				});
			}
		});
	});

})( jQuery );
