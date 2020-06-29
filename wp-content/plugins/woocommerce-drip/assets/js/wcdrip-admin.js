(function ( $ ) {

	"use script";

	$(function () {

		// Hide double error (@todo investigate more with php solution)
		$( document ).ready(function() {

			var wcdrip_top_title = $( ".wcdrip-section-title-main" );
			var wcdrip_form_wrap = wcdrip_top_title.parents( "#mainform" );
			wcdrip_form_wrap.children( ".error" ).hide(); // Hide all
			wcdrip_form_wrap.children( ".error:eq(0)" ).show(); // Show the first one

		});

		// JSON Array of Option IDs / Classes
		var json_notif = [ 
		    { "#woocommerce_wcdrip_subscribe-enable": ".wcdrip-subscribe-field" },
		];

		var fields_all = ['#woocommerce_wcdrip_account', '#woocommerce_wcdrip_subscribe-enable', '#woocommerce_wcdrip_subscribe-campaign', '#woocommerce_wcdrip_subscribe-text', '#woocommerce_wcdrip_event-sale-name', '#wcdrip-reload-data-button' ];

		var api = '#woocommerce_wcdrip_api_key';

		// Hide Everything
		if ( jQuery( api ).filter(function() { return jQuery(this).val(); }).length <= 0 ) {
			
			jQuery.each(fields_all, function(index, item) {

				var item_each = $( item );
			  	var item_parent = jQuery( item_each.parents("tr") );

			  	$( document ).ready(function() {

					// Initial Hiding
					$( item_parent ).hide();

				});

			});

			$( '.wcdrip-section-title' ).next( 'p' ).hide();
			$( '.wcdrip-section-title' ).hide();

		}

		// Hide Based on Checkbox (main notifications)
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
jQuery.fn.wcdripselectText = function(){
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
	jQuery('span.wcdrip-tag').click(function() {
    	jQuery(this).wcdripselectText();
	});
});