<div class="tool-box">

	<h3 class="title"><?php _e('Import Product Variations CSV', 'woocommerce-product-csv-import-suite'); ?></h3>
	<p><?php _e('Import and add variations to your variable products using this tool.', 'woocommerce-product-csv-import-suite'); ?></p>
	<p class="description"><?php _e('Each row must be mapped to a variable product via a <code>post_parent</code> or <code>parent_sku</code> column in order to import successfully. Merging also requires a <code>sku</code> or <code>id</code> column.', 'woocommerce-product-csv-import-suite'); ?></p>
	<p class="submit"><a class="button" href="<?php echo admin_url('admin.php?import=woocommerce_variation_csv'); ?>"><?php _e('Import Variations', 'woocommerce-product-csv-import-suite'); ?></a> <a class="button" href="<?php echo admin_url('admin.php?import=woocommerce_variation_csv&merge=1'); ?>"><?php _e('Merge Variations', 'woocommerce-product-csv-import-suite'); ?></a></p>

</div>