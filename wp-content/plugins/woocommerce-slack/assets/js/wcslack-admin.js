(function ( $ ) {

	"use script";

	$(function () {

		// Hide double error (@todo investigate more with php solution)
		$( document ).ready(function() {

			var wcslack_top_title = $( ".wcslack-section-title-main" );
			var wcslack_form_wrap = wcslack_top_title.parents( "#mainform" );
			wcslack_form_wrap.children( ".error:eq(1)").hide();

		});

		// JSON Array of Option IDs / Classes
		var json_notif = [ 
		    { "#woocommerce_wcslack_notif-post-new": ".wcslack-post-new-field" },
		    { "#woocommerce_wcslack_notif-order-new": ".wcslack-order-new-field" },
		    { "#woocommerce_wcslack_notif-back-order": ".wcslack-back-order-field" },
		    { "#woocommerce_wcslack_notif-low-stock": ".wcslack-low-stock-field" },
		    { "#woocommerce_wcslack_notif-out-stock": ".wcslack-out-stock-field" },
		    { "#woocommerce_wcslack_notif-new-review": ".wcslack-review-new-field" },
		    { "#woocommerce_wcslack_notif-new-customer": ".wcslack-customer-new-field" },
		];

		// All Settings
		var fields_all = ['#woocommerce_wcslack_debug', '#woocommerce_wcslack_channel', '#woocommerce_wcslack_emoji', '#woocommerce_wcslack_color', '#woocommerce_wcslack_notif-post-new', '#woocommerce_wcslack_notif-order-new', '#woocommerce_wcslack_notif-back-order', '#woocommerce_wcslack_notif-low-stock', '#woocommerce_wcslack_notif-out-stock', '#woocommerce_wcslack_notif-new-review', '#woocommerce_wcslack_notif-new-customer', '#wcslack-test-button', '#wcslack-reload-channel-button' ];

		// API Field
		var api = '#woocommerce_wcslack_api_key';
		var is_api_set = jQuery( api ).filter(function() { return jQuery(this).val(); }).length > 0;

		var auth = '#wc_slack_authenticated';
		var auth_applicable  = 1 == jQuery( auth ).length;
		var is_authenticated = '1' === jQuery( auth ).val();

		var hideFields = function() {
			jQuery.each(fields_all, function(index, item) {

				var item_each = $( item );
				var item_parent = jQuery( item_each.parents("tr") );

				$( document ).ready(function() {

					// Initial Hiding
					$( item_parent ).hide();

				});

			});

			$( '.wcslack-section-title' ).next( 'p' ).hide();
			$( '.wcslack-section-title' ).hide();
		};

		if ( auth_applicable ) {
			if ( ! is_authenticated ) {
				hideFields();
			}
		} else if ( ! is_api_set ) { // Backwards compatibility support
			hideFields();
		}

		jQuery.each(json_notif, function() {

		  	jQuery.each(this, function(name, value) {

			  	var checkbox_var = $( name );
			  	var child = $( value );
			  	var child_var = $( child.parents("tr") );

			  	$( document ).ready(function() {

					// Initial Hiding

					if ( $( checkbox_var ).prop('checked') !== true ) {
						$( child_var ).hide();
					}

				});

				// On Check Hiding

				$( checkbox_var ).click(function() {
					if ( $(this).is(':checked') ) {
						$( child_var ).show();
					} else {
						$( child_var ).hide();
					}
				});

		  	});

		});

	});

}(jQuery));

// jQuery Extension / Function for Select Text (used with custom message template tag selection)
jQuery.fn.wchcnselectText = function(){
    var doc = document
        , element = this[0]
        , range, selection
    ;
    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(element);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();        
        range = document.createRange();
        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }
};

// Highlight text on sleect for template tags
jQuery(function() {
	jQuery('span.wchcn-tag').click(function() {
    	jQuery(this).wchcnselectText();
	});
});

jQuery( document ).ready( function( $ ) {
    $( '.wc-slack-connect' ).click( function() {
        $( '#wc_slack_redirect' ).val( '1' );
        $( '#mainform' ) .submit();
    } );
} );
