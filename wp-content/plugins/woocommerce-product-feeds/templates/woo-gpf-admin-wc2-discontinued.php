<div class="notice notice-error is-dismissible" id="gpf_wc2_discontinued_notice">
	<p>
		<?php _e( 'The <strong>WooCommerce Product Feeds</strong> plugin does not support WooCommerce versions older than version 3.0.', 'woocommerce_gpf' ); ?>
	</p>
</div>

<script type="text/javascript">
	jQuery( function() {
	   jQuery( '#gpf_wc2_discontinued_notice' ).on( 'click', function() {
           var data = {
               'action': 'gpf_dismiss_admin_notice',
	           'notice': 'wc2_discontinued_notice',
           };
           jQuery.post( ajaxurl, data );
	   } );
	} );
</script>
