<p>
	<input type="checkbox" id="woocommerce_gpf_excluded{loop_num}" name="_woocommerce_gpf_data{loop_idx}[exclude_product]" {checked}>
	<label for="woocommerce_gpf_excluded{loop_num}">{hide_product_text}</label>
</p>
<script type="text/javascript">
	jQuery(document).on('change', '#woocommerce_gpf_excluded{loop_num}', function() {
		if ( jQuery( '#woocommerce_gpf_excluded{loop_num}' ).is( ':checked' ) ) {
			jQuery( '#woocommerce_gpf_options{loop_num}' ).slideUp( 'fast' );
		} else {
			jQuery( '#woocommerce_gpf_options{loop_num}' ).slideDown( 'fast' );
		}
	});
	jQuery(document).ready(function() {
        jQuery('#woocommerce_gpf_excluded{loop_num}').change();
    });
</script>
