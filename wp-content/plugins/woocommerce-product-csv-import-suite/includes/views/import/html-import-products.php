<div class="tool-box">

	<h3 class="title"><?php _e('Import Product CSV', 'woocommerce-product-csv-import-suite'); ?></h3>
	<p><?php _e('Import simple, grouped, external and variable products into WooCommerce using this tool.', 'woocommerce-product-csv-import-suite'); ?></p>
	<p class="description"><?php _e('Upload a CSV from your computer. Click import to import your CSV as new products (existing products will be skipped), or click merge to merge products, ninja style. Importing requires the <code>post_title</code> column, whilst merging requires <code>sku</code> or <code>id</code>.', 'woocommerce-product-csv-import-suite'); ?></p>

	<p class="submit"><a class="button" href="<?php echo admin_url('admin.php?import=woocommerce_csv'); ?>"><?php _e('Import Products', 'woocommerce-product-csv-import-suite'); ?></a> <a class="button" href="<?php echo admin_url('admin.php?import=woocommerce_csv&merge=1'); ?>"><?php _e('Merge Products', 'woocommerce-product-csv-import-suite'); ?></a></p>

</div>