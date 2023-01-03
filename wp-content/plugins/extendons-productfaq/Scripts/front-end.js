jQuery( document ).ready(function() {
    
	jQuery("#adding-qu" ).click(function() {     
		jQuery('#extendons-add-new-question ').toggle();
	});

	jQuery("#adding-qu").on("click", function() {
		var el = jQuery(this);
		if (el.text() == el.data("text-swap")) {
		    el.text(el.data("text-original"));
		} else {
		    el.data("text-original", el.text());
		    el.text(el.data("text-swap"));
		}
	});


});
