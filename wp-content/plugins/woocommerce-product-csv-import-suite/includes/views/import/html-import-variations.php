<div class="tool-box">

	<h3 class="title"><?php esc_html_e( 'Import Product Variations CSV', 'woocommerce-product-csv-import-suite' ); ?></h3>
	<p><?php esc_html_e( 'Import and add variations to your variable products using this tool.', 'woocommerce-product-csv-import-suite' ); ?></p>
	<p class="description">
		<?php
		// translators: $1 and $2: opening and closing code tags.
		printf( esc_html__( 'Each row must be mapped to a variable product via a %1$spost_parent%2$s or %1$sparent_sku%2$s column in order to import successfully. Merging also requires a %1$ssku%2$s or %1$sid%2$s column.', 'woocommerce-product-csv-import-suite' ), '<code>', '</code>' );
		?>
	</p>
	<p class="submit"><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?import=woocommerce_variation_csv' ) ); ?>"><?php esc_html_e( 'Import Variations', 'woocommerce-product-csv-import-suite' ); ?></a> <a class="button" href="<?php echo esc_url( admin_url( 'admin.php?import=woocommerce_variation_csv&merge=1' ) ); ?>"><?php esc_html_e( 'Merge Variations', 'woocommerce-product-csv-import-suite' ); ?></a></p>

</div>
