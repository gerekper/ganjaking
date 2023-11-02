<div class="wrap woocommerce">
	<div class="icon32" id="icon-woocommerce-importer"><br></div>
	<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=woocommerce_csv_import_suite' ) ); ?>" class="nav-tab <?php echo ( 'import' === $tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Import Products', 'woocommerce-product-csv-import-suite' ); ?></a><a href="<?php echo esc_url( admin_url( 'admin.php?page=woocommerce_csv_import_suite&tab=export' ) ); ?>" class="nav-tab <?php echo ( 'export' === $tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Export Products', 'woocommerce-product-csv-import-suite' ); ?></a>
	</h2>

	<?php
	switch ( $tab ) {
		case 'export':
			$this->admin_export_page();
			break;
		default:
			$this->admin_import_page();
			break;
	}
	?>
</div>
