<div class="tool-box">

	<h3 class="title"><?php esc_html_e( 'Import Product CSV', 'woocommerce-product-csv-import-suite' ); ?></h3>
	<p><?php esc_html_e( 'Import simple, grouped, external and variable products into WooCommerce using this tool.', 'woocommerce-product-csv-import-suite' ); ?></p>
	<p class="description">
		<?php
		// translators: $1 and $2: opening and closing code tags.
		printf( esc_html__( 'Upload a CSV from your computer. Click import to import your CSV as new products (existing products will be skipped), or click merge to merge products. Importing requires the %1$spost_title%2$s column, whilst merging requires %1$ssku%2$s or %1$sid%2$s.', 'woocommerce-product-csv-import-suite' ), '<code>', '</code>' );
		?>
	</p>

	<p class="submit"><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?import=woocommerce_csv' ) ); ?>"><?php esc_html_e( 'Import Products', 'woocommerce-product-csv-import-suite' ); ?></a> <a class="button" href="<?php echo esc_url( admin_url( 'admin.php?import=woocommerce_csv&merge=1' ) ); ?>"><?php esc_html_e( 'Merge Products', 'woocommerce-product-csv-import-suite' ); ?></a></p>

</div>
