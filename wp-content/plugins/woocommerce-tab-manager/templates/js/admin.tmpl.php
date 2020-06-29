<script type="text/html" id="tmpl-batch-product-update-message">
	<# if ( ! data ) { #>
		<?php esc_html_e( 'Updating your products and tabs...', 'woocommerce-tab-manager' ); ?>
	<# } else { #>
		<# if ( data.complete ) { #>
			<?php esc_html_e( 'All done!', 'woocommerce-tab-manager' ); ?>
		<# } else { #>
			<?php /*  translators: {{ data.current }} - current product count, {{ data.total }} - total product count (eg. 2 of 168 products updated) */
			esc_html_e( '{{ data.current }} of {{ data.total }} products updated.', 'woocommerce-tab-manager' ); ?>
		<# } #>
	<# } #>
</script>
