<form action="<?php echo admin_url( 'admin.php?import=' . $this->import_page . '&step=2&merge=' . $merge ); ?>" method="post">
	<?php wp_nonce_field( 'import-woocommerce' ); ?>
	<input type="hidden" name="import_id" value="<?php echo $this->id; ?>" />
	<?php if ( $this->file_url_import_enabled ) : ?>
	<input type="hidden" name="import_url" value="<?php echo $this->file_url; ?>" />
	<?php endif; ?>

	<h3><?php _e( 'Map Fields', 'woocommerce-product-csv-import-suite' ); ?></h3>
	<p><?php _e( 'Here you can map your imported columns to product data fields.', 'woocommerce-product-csv-import-suite' ); ?></p>

	<table class="widefat widefat_importer">
		<thead>
			<tr>
				<th><?php _e( 'Map to', 'woocommerce-product-csv-import-suite' ); ?></th>
				<th><?php _e( 'Column Header', 'woocommerce-product-csv-import-suite' ); ?></th>
				<th><?php _e( 'Example Column Value', 'woocommerce-product-csv-import-suite' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $row as $key => $value ) : ?>
			<tr>
				<td width="25%">
					<?php
						if ( strstr( $key, 'tax:' ) ) {

							$column = trim( str_replace( 'tax:', '', $key ) );
							printf(__('Taxonomy: <strong>%s</strong>', 'woocommerce-product-csv-import-suite'), $column);

						} elseif ( strstr( $key, 'meta:' ) ) {

							$column = trim( str_replace( 'meta:', '', $key ) );
							printf(__('Custom Field: <strong>%s</strong>', 'woocommerce-product-csv-import-suite'), $column);

						} elseif ( strstr( $key, 'attribute:' ) ) {

							$column = trim( str_replace( 'attribute:', '', $key ) );
							printf(__('Product Attribute: <strong>%s</strong>', 'woocommerce-product-csv-import-suite'), sanitize_title( $column ) );

						} elseif ( strstr( $key, 'attribute_data:' ) ) {

							$column = trim( str_replace( 'attribute_data:', '', $key ) );
							printf(__('Product Attribute Data: <strong>%s</strong>', 'woocommerce-product-csv-import-suite'), sanitize_title( $column ) );

						} elseif ( strstr( $key, 'attribute_default:' ) ) {

							$column = trim( str_replace( 'attribute_default:', '', $key ) );
							printf(__('Product Attribute default value: <strong>%s</strong>', 'woocommerce-product-csv-import-suite'), sanitize_title( $column ) );

						} else {
							?>
							<select name="map_to[<?php echo $key; ?>]">
								<option value=""><?php _e( 'Do not import', 'woocommerce-product-csv-import-suite' ); ?></option>
								<option value="import_as_images" <?php selected( $key, 'images' ); ?>><?php _e( 'Images/Gallery', 'woocommerce-product-csv-import-suite' ); ?></option>
								<option value="import_as_meta"><?php _e( 'Custom Field with column name', 'woocommerce-product-csv-import-suite' ); ?></option>
								<optgroup label="<?php _e( 'Taxonomies', 'woocommerce-product-csv-import-suite' ); ?>">
									<?php
										foreach ($taxonomies as $taxonomy ) {
											if ( substr( $taxonomy, 0, 3 ) == 'pa_' ) continue;
											echo '<option value="tax:' . $taxonomy . '" ' . selected( $key, 'tax:' . $taxonomy, true ) . '>' . $taxonomy . '</option>';
										}
									?>
								</optgroup>
								<optgroup label="<?php _e( 'Attributes', 'woocommerce-product-csv-import-suite' ); ?>">
									<?php
										foreach ($taxonomies as $taxonomy ) {
											if ( substr( $taxonomy, 0, 3 ) == 'pa_' )
												echo '<option value="attribute:' . $taxonomy . '" ' . selected( $key, 'attribute:' . $taxonomy, true ) . '>' . $taxonomy . '</option>';
										}
									?>
								</optgroup>
								<optgroup label="<?php _e( 'Map to parent (variations and grouped products)', 'woocommerce-product-csv-import-suite' ); ?>">
									<option value="post_parent" <?php selected( $key, 'post_parent' ); ?>><?php _e( 'By ID', 'woocommerce-product-csv-import-suite' ); ?>: post_parent</option>
									<option value="parent_sku" <?php selected( $key, 'parent_sku' ); ?>><?php _e( 'By SKU', 'woocommerce-product-csv-import-suite' ); ?>: parent_sku</option>
								</optgroup>
								<optgroup label="<?php _e( 'Post data', 'woocommerce-product-csv-import-suite' ); ?>">
									<option <?php selected( $key, 'post_id' ); selected( $key, 'id' ); ?>>post_id</option>
									<option <?php selected( $key, 'post_type' ); ?>>post_type</option>
									<option <?php selected( $key, 'menu_order' ); ?>>menu_order</option>
									<option <?php selected( $key, 'post_status' ); ?>>post_status</option>
									<option <?php selected( $key, 'post_title' ); ?>>post_title</option>
									<option <?php selected( $key, 'post_name' ); ?>>post_name</option>
									<option <?php selected( $key, 'post_date' ); ?>>post_date</option>
									<option <?php selected( $key, 'post_date_gmt' ); ?>>post_date_gmt</option>
									<option <?php selected( $key, 'post_content' ); ?>>post_content</option>
									<option <?php selected( $key, 'post_excerpt' ); ?>>post_excerpt</option>
									<option <?php selected( $key, 'post_author' ); ?>>post_author</option>
									<option <?php selected( $key, 'post_password' ); ?>>post_password</option>
									<option <?php selected( $key, 'comment_status' ); ?>>comment_status</option>
									<option <?php selected( $key, 'variation_description' ); ?>>variation_description</option>
								</optgroup>
								<optgroup label="<?php _e( 'Product data', 'woocommerce-product-csv-import-suite' ); ?>">
									<option value="tax:product_type" <?php selected( $key, 'tax:product_type' ); ?>><?php _e( 'Type', 'woocommerce-product-csv-import-suite' ); ?>: product_type</option>
									<option value="downloadable" <?php selected( $key, 'downloadable' ); ?>><?php _e( 'Type', 'woocommerce-product-csv-import-suite' ); ?>: downloadable</option>
									<option value="virtual" <?php selected( $key, 'virtual' ); ?>><?php _e( 'Type', 'woocommerce-product-csv-import-suite' ); ?>: virtual</option>
									<option value="sku" <?php selected( $key, 'sku' ); ?>><?php _e( 'SKU', 'woocommerce-product-csv-import-suite' ); ?>: sku</option>
									<option value="visibility" <?php selected( $key, 'visibility' ); ?>><?php _e( 'Visibility', 'woocommerce-product-csv-import-suite' ); ?>: visibility</option>
									<option value="featured" <?php selected( $key, 'featured' ); ?>><?php _e( 'Visibility', 'woocommerce-product-csv-import-suite' ); ?>: featured</option>
									<option value="stock" <?php selected( $key, 'stock' ); ?>><?php _e( 'Inventory', 'woocommerce-product-csv-import-suite' ); ?>: stock</option>
									<option value="stock_status" <?php selected( $key, 'stock_status' ); ?>><?php _e( 'Inventory', 'woocommerce-product-csv-import-suite' ); ?>: stock_status</option>
									<option value="backorders" <?php selected( $key, 'backorders' ); ?>><?php _e( 'Inventory', 'woocommerce-product-csv-import-suite' ); ?>: backorders</option>
									<option value="manage_stock" <?php selected( $key, 'manage_stock' ); ?>><?php _e( 'Inventory', 'woocommerce-product-csv-import-suite' ); ?>: manage_stock</option>
									<option value="regular_price" <?php selected( $key, 'regular_price' ); ?>><?php _e( 'Price', 'woocommerce-product-csv-import-suite' ); ?>: regular_price</option>
									<option value="sale_price" <?php selected( $key, 'sale_price' ); ?>><?php _e( 'Price', 'woocommerce-product-csv-import-suite' ); ?>: sale_price</option>
									<option value="sale_price_dates_from" <?php selected( $key, 'sale_price_dates_from' ); ?>><?php _e( 'Price', 'woocommerce-product-csv-import-suite' ); ?>: sale_price_dates_from</option>
									<option value="sale_price_dates_to" <?php selected( $key, 'sale_price_dates_to' ); ?>><?php _e( 'Price', 'woocommerce-product-csv-import-suite' ); ?>: sale_price_dates_to</option>
									<option value="weight" <?php selected( $key, 'weight' ); ?>><?php _e( 'Dimensions', 'woocommerce-product-csv-import-suite' ); ?>: weight</option>
									<option value="length" <?php selected( $key, 'length' ); ?>><?php _e( 'Dimensions', 'woocommerce-product-csv-import-suite' ); ?>: length</option>
									<option value="width" <?php selected( $key, 'width' ); ?>><?php _e( 'Dimensions', 'woocommerce-product-csv-import-suite' ); ?>: width</option>
									<option value="height" <?php selected( $key, 'height' ); ?>><?php _e( 'Dimensions', 'woocommerce-product-csv-import-suite' ); ?>: height</option>
									<option value="tax_status" <?php selected( $key, 'tax_status' ); ?>><?php _e( 'Tax', 'woocommerce-product-csv-import-suite' ); ?>: tax_status</option>
									<option value="tax_class" <?php selected( $key, 'tax_class' ); ?>><?php _e( 'Tax', 'woocommerce-product-csv-import-suite' ); ?>: tax_class</option>
									<option value="upsell_ids" <?php selected( $key, 'upsell_ids' ); ?>><?php _e( 'Related Products', 'woocommerce-product-csv-import-suite' ); ?>: upsell_ids</option>
									<option value="crosssell_ids" <?php selected( $key, 'crosssell_ids' ); ?>><?php _e( 'Related Products', 'woocommerce-product-csv-import-suite' ); ?>: crosssell_ids</option>
									<option value="upsell_skus" <?php selected( $key, 'upsell_skus' ); ?>><?php _e( 'Related Products', 'woocommerce-product-csv-import-suite' ); ?>: upsell_skus</option>
									<option value="crosssell_skus" <?php selected( $key, 'crosssell_skus' ); ?>><?php _e( 'Related Products', 'woocommerce-product-csv-import-suite' ); ?>: crosssell_skus</option>
									<option value="file_paths" <?php selected( $key, 'file_paths' ); ?>><?php _e( 'Downloads', 'woocommerce-product-csv-import-suite' ); ?>: file_paths <?php _e( '(WC 2.0.x)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="downloadable_files" <?php selected( $key, 'downloadable_files' ); ?>><?php _e( 'Downloads', 'woocommerce-product-csv-import-suite' ); ?>: downloadable_files <?php _e( '(WC 2.1+)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="download_limit" <?php selected( $key, 'download_limit' ); ?>><?php _e( 'Downloads', 'woocommerce-product-csv-import-suite' ); ?>: download_limit</option>
									<option value="download_expiry" <?php selected( $key, 'download_expiry' ); ?>><?php _e( 'Downloads', 'woocommerce-product-csv-import-suite' ); ?>: download_expiry</option>
									<option value="product_url" <?php selected( $key, 'product_url' ); ?>><?php _e( 'External', 'woocommerce-product-csv-import-suite' ); ?>: product_url</option>
									<option value="button_text" <?php selected( $key, 'button_text' ); ?>><?php _e( 'External', 'woocommerce-product-csv-import-suite' ); ?>: button_text</option>
									<?php do_action( 'woocommerce_csv_product_data_mapping', $key ); ?>
								</optgroup>
								<?php if( function_exists( 'woocommerce_gpf_install' ) ) : ?>
								<optgroup label="<?php _e( 'Google Product Feed', 'woocommerce-product-csv-import-suite' ); ?>">
									<option value="gpf:adwords_grouping" <?php selected( $key, 'gpf:adwords_grouping' ); ?>><?php _e( 'Adwords grouping', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:adwords_labels" <?php selected( $key, 'gpf:adwords_labels' ); ?>><?php _e( 'Adwords labels', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:adult" <?php selected( $key, 'gpf:adult' ); ?>><?php _e( 'Adult content', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:age_group" <?php selected( $key, 'gpf:age_group' ); ?>><?php _e( 'Age Group', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:availability" <?php selected( $key, 'gpf:availability' ); ?>><?php _e( 'Availability', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:availability_date" <?php selected( $key, 'gpf:availability_date' ); ?>><?php _e( 'Availability date', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:brand" <?php selected( $key, 'gpf:brand' ); ?>><?php _e( 'Brand', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:is_bundle" <?php selected( $key, 'gpf:is_bundle' ); ?>><?php _e( 'Bundle indicator', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:color" <?php selected( $key, 'gpf:color' ); ?>><?php _e( 'Color', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:condition" <?php selected( $key, 'gpf:condition' ); ?>><?php _e( 'Condition', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:cost_of_goods_sold" <?php selected( $key, 'gpf:cost_of_goods_sold' ); ?>><?php _e( 'Cost of goods sold', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:custom_label_0" <?php selected( $key, 'gpf:custom_label_0' ); ?>><?php _e( 'Custom label 0', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:custom_label_1" <?php selected( $key, 'gpf:custom_label_1' ); ?>><?php _e( 'Custom label 1', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:custom_label_2" <?php selected( $key, 'gpf:custom_label_2' ); ?>><?php _e( 'Custom label 2', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:custom_label_3" <?php selected( $key, 'gpf:custom_label_3' ); ?>><?php _e( 'Custom label 3', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:custom_label_4" <?php selected( $key, 'gpf:custom_label_4' ); ?>><?php _e( 'Custom label 4', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:delivery_label" <?php selected( $key, 'gpf:delivery_label' ); ?>><?php _e( 'Delivery label', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:energy_efficiency_class" <?php selected( $key, 'gpf:energy_efficiency_class' ); ?>><?php _e( 'Energy efficiency class', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:min_energy_efficiency_class" <?php selected( $key, 'gpf:min_energy_efficiency_class' ); ?>><?php _e( 'Energy efficiency class (min)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:max_energy_efficiency_class" <?php selected( $key, 'gpf:max_energy_efficiency_class' ); ?>><?php _e( 'Energy efficiency class (max)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:energy_label_image_link" <?php selected( $key, 'gpf:energy_label_image_link' ); ?>><?php _e( 'Energy label image link', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:excluded_destination" <?php selected( $key, 'gpf:excluded_destination' ); ?>><?php _e( 'Excluded destination', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:exclude_product" <?php selected( $key, 'gpf:exclude_product' ); ?>><?php _e( 'Exclude Product', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:gender" <?php selected( $key, 'gpf:gender' ); ?>><?php _e( 'Gender', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:google_funded_promotion_eligibility" <?php selected( $key, 'gpf:google_funded_promotion_eligibility' ); ?>><?php _e( 'Google-funded promotion eligibility', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:google_product_category" <?php selected( $key, 'gpf:google_product_category' ); ?>><?php _e('Google Product Category', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:gtin" <?php selected( $key, 'gpf:gtin' ); ?>><?php _e( 'Global Trade Item Number (GTIN)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:included_destination" <?php selected( $key, 'gpf:included_destination' ); ?>><?php _e( 'Included destination', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:min_handling_time" <?php selected( $key, 'gpf:min_handling_time' ); ?>><?php _e( 'Handling time (min)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:max_handling_time" <?php selected( $key, 'gpf:max_handling_time' ); ?>><?php _e( 'Handling time (max)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:mpn" <?php selected( $key, 'gpf:mpn' ); ?>><?php _e( 'Manufacturer Part Number (MPN)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:material" <?php selected( $key, 'gpf:material' ); ?>><?php _e( 'Material', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:multipack" <?php selected( $key, 'gpf:multipack' ); ?>><?php _e( 'Multipack', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:pattern" <?php selected( $key, 'gpf:pattern' ); ?>><?php _e( 'Pattern', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:product_type" <?php selected( $key, 'gpf:product_type' ); ?>><?php _e( 'Product Type', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:promotion_id" <?php selected( $key, 'gpf:promotion_id' ); ?>><?php _e( 'Promotion ID', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:purchase_quantity_limit" <?php selected( $key, 'gpf:purchase_quantity_limit' ); ?>><?php _e( 'Purchase quantity limit', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:return_address_label" <?php selected( $key, 'gpf:return_address_label' ); ?>><?php _e( 'Return address label identifier', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:return_policy_label" <?php selected( $key, 'gpf:return_policy_label' ); ?>><?php _e( 'Return policy label identifier', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:sell_on_google_quantity" <?php selected( $key, 'gpf:sell_on_google_quantity' ); ?>><?php _e( 'Sell on Google quantity', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:size" <?php selected( $key, 'gpf:size' ); ?>><?php _e( 'Size', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:size_type" <?php selected( $key, 'gpf:size_type' ); ?>><?php _e( 'Size type', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:size_system" <?php selected( $key, 'gpf:size_system' ); ?>><?php _e( 'Size system', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:unit_pricing_measure" <?php selected( $key, 'gpf:unit_pricing_measure' ); ?>><?php _e( 'Unit pricing measure', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:unit_pricing_base_measure" <?php selected( $key, 'gpf:unit_pricing_base_measure' ); ?>><?php _e( 'Unit pricing base measure', 'woocommerce-product-csv-import-suite' ); ?></option>
								</optgroup>
								<?php endif; ?>
							</select>
							<?php
						}
					?>
				</td>
				<td width="25%"><?php echo $raw_headers[$key]; ?></td>
				<td><code><?php if ( $value != '' ) echo esc_html( $value ); else echo '-'; ?></code></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<p class="submit">
		<input type="submit" class="button" value="<?php esc_attr_e( 'Submit', 'woocommerce-product-csv-import-suite' ); ?>" />
		<input type="hidden" name="delimiter" value="<?php echo $this->delimiter ?>" />
		<input type="hidden" name="merge_empty_cells" value="<?php echo $this->merge_empty_cells ?>" />
	</p>
</form>
