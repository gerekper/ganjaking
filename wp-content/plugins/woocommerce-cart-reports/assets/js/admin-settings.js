jQuery(document).ready(function() {

	var expiration_row     = jQuery('#wc_cart_reports_expiration').closest('tr');
	var expiration_checked = jQuery("#wc_cart_reports_expiration_opt_in:checked").length;

	var expiration_opt_in = jQuery("#wc_cart_reports_expiration_opt_in");

	
	if (expiration_checked == 0) {
		expiration_row.hide();
	}

	jQuery('#wc_cart_reports_expiration_opt_in').click(function() {
		expiration_row.toggle();
	});

});
