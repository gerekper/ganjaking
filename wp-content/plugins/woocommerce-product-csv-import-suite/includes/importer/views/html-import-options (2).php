<form action="<?php echo esc_url( admin_url( 'admin.php?import=' . $this->import_page . '&step=2&merge=' . $merge ) ); ?>" method="post">
	<?php wp_nonce_field( 'import-woocommerce' ); ?>
	<input type="hidden" name="import_id" value="<?php echo esc_attr( $this->id ); ?>" />
	<?php if ( $this->file_url_import_enabled ) : ?>
	<input type="hidden" name="import_url" value="<?php echo esc_attr( $this->file_url ); ?>" />
	<?php endif; ?>

	<h3><?php esc_html_e( 'Map Fields', 'woocommerce-product-csv-import-suite' ); ?></h3>
	<p><?php esc_html_e( 'Here you can map your imported columns to product data fields.', 'woocommerce-product-csv-import-suite' ); ?></p>

	<table class="widefat widefat_importer">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Map to', 'woocommerce-product-csv-import-suite' ); ?></th>
				<th><?php esc_html_e( 'Column Header', 'woocommerce-product-csv-import-suite' ); ?></th>
				<th><?php esc_html_e( 'Example Column Value', 'woocommerce-product-csv-import-suite' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $row as $key => $value ) : ?>
			<tr>
				<td width="25%">
					<?php
					if ( strstr( $key, 'tax:' ) ) {

						$column = trim( str_replace( 'tax:', '', $key ) );
						// translators: $1 and $2: opening and closing strong tags, $3: taxonomy name.
						printf( esc_html__( 'Taxonomy: %1$s%3$s%2$s', 'woocommerce-product-csv-import-suite' ), '<strong>', '</strong>', esc_html( $column ) );

					} elseif ( strstr( $key, 'meta:' ) ) {

						$column = trim( str_replace( 'meta:', '', $key ) );
						// translators: $1 and $2: opening and closing strong tags, $3: custom field.
						printf( esc_html__( 'Custom Field: %1$s%3$s%2$s', 'woocommerce-product-csv-import-suite' ), '<strong>', '</strong>', esc_html( $column ) );

					} elseif ( strstr( $key, 'attribute:' ) ) {

						$column = trim( str_replace( 'attribute:', '', $key ) );
							// translators: $1 and $2: opening and closing strong tags, $3: attribute name.
						printf( esc_html__( 'Product Attribute: %1$s%3$s%2$s', 'woocommerce-product-csv-import-suite' ), '<strong>', '</strong>', esc_html( $column ) );

					} elseif ( strstr( $key, 'attribute_data:' ) ) {

						$column = trim( str_replace( 'attribute_data:', '', $key ) );
						// translators: $1 and $2: opening and closing strong tags, $3: Attribute data.
						printf( esc_html__( 'Product Attribute Data: %1$s%3$s%2$s', 'woocommerce-product-csv-import-suite' ), '<strong>', '</strong>', esc_html( sanitize_title( $column ) ) );

					} elseif ( strstr( $key, 'attribute_default:' ) ) {

						$column = trim( str_replace( 'attribute_default:', '', $key ) );
						// translators: $1 and $2: opening and closing strong tags, $3: Attribute default value.
						printf( esc_html__( 'Product Attribute default value: %1$s%3$s%2$s', 'woocommerce-product-csv-import-suite' ), '<strong>', '</strong>', esc_html( sanitize_title( $column ) ) );

					} else {
						?>
							<select name="map_to[<?php echo esc_attr( $key ); ?>]">
								<option value=""><?php esc_html_e( 'Do not import', 'woocommerce-product-csv-import-suite' ); ?></option>
								<option value="import_as_images" <?php selected( $key, 'images' ); ?>><?php esc_html_e( 'Images/Gallery', 'woocommerce-product-csv-import-suite' ); ?></option>
								<option value="import_as_meta"><?php esc_html_e( 'Custom Field with column name', 'woocommerce-product-csv-import-suite' ); ?></option>
								<optgroup label="<?php esc_html_e( 'Taxonomies', 'woocommerce-product-csv-import-suite' ); ?>">
									<?php
									foreach ( $taxonomies as $taxonomy ) {
										if ( substr( $taxonomy, 0, 3 ) === 'pa_' ) continue;
										echo '<option value="tax:' . esc_attr( $taxonomy ) . '" ' . selected( $key, 'tax:' . $taxonomy, true ) . '>' . esc_html( $taxonomy ) . '</option>';
									}
									?>
								</optgroup>
								<optgroup label="<?php esc_html_e( 'Attributes', 'woocommerce-product-csv-import-suite' ); ?>">
									<?php
									foreach ( $taxonomies as $taxonomy ) {
										if ( substr( $taxonomy, 0, 3 ) === 'pa_' )
											echo '<option value="attribute:' . esc_attr( $taxonomy ) . '" ' . selected( $key, 'attribute:' . $taxonomy, true ) . '>' . esc_html( $taxonomy ) . '</option>';
									}
									?>
								</optgroup>
								<optgroup label="<?php esc_html_e( 'Map to parent (variations and grouped products)', 'woocommerce-product-csv-import-suite' ); ?>">
									<option value="post_parent" <?php selected( $key, 'post_parent' ); ?>><?php esc_html_e( 'By ID', 'woocommerce-product-csv-import-suite' ); ?>: post_parent</option>
									<option value="parent_sku" <?php selected( $key, 'parent_sku' ); ?>><?php esc_html_e( 'By SKU', 'woocommerce-product-csv-import-suite' ); ?>: parent_sku</option>
								</optgroup>
								<optgroup label="<?php esc_html_e( 'Post data', 'woocommerce-product-csv-import-suite' ); ?>">
									<option <?php selected( $key, 'post_id' ); ?><?php selected( $key, 'id' ); ?>>post_id</option>
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
								<optgroup label="<?php esc_html_e( 'Product data', 'woocommerce-product-csv-import-suite' ); ?>">
									<option value="tax:product_type" <?php selected( $key, 'tax:product_type' ); ?>><?php esc_html_e( 'Type', 'woocommerce-product-csv-import-suite' ); ?>: product_type</option>
									<option value="downloadable" <?php selected( $key, 'downloadable' ); ?>><?php esc_html_e( 'Type', 'woocommerce-product-csv-import-suite' ); ?>: downloadable</option>
									<option value="virtual" <?php selected( $key, 'virtual' ); ?>><?php esc_html_e( 'Type', 'woocommerce-product-csv-import-suite' ); ?>: virtual</option>
									<option value="sku" <?php selected( $key, 'sku' ); ?>><?php esc_html_e( 'SKU', 'woocommerce-product-csv-import-suite' ); ?>: sku</option>
									<option value="visibility" <?php selected( $key, 'visibility' ); ?>><?php esc_html_e( 'Visibility', 'woocommerce-product-csv-import-suite' ); ?>: visibility</option>
									<option value="featured" <?php selected( $key, 'featured' ); ?>><?php esc_html_e( 'Visibility', 'woocommerce-product-csv-import-suite' ); ?>: featured</option>
									<option value="stock" <?php selected( $key, 'stock' ); ?>><?php esc_html_e( 'Inventory', 'woocommerce-product-csv-import-suite' ); ?>: stock</option>
									<option value="stock_status" <?php selected( $key, 'stock_status' ); ?>><?php esc_html_e( 'Inventory', 'woocommerce-product-csv-import-suite' ); ?>: stock_status</option>
									<option value="backorders" <?php selected( $key, 'backorders' ); ?>><?php esc_html_e( 'Inventory', 'woocommerce-product-csv-import-suite' ); ?>: backorders</option>
									<option value="manage_stock" <?php selected( $key, 'manage_stock' ); ?>><?php esc_html_e( 'Inventory', 'woocommerce-product-csv-import-suite' ); ?>: manage_stock</option>
									<option value="regular_price" <?php selected( $key, 'regular_price' ); ?>><?php esc_html_e( 'Price', 'woocommerce-product-csv-import-suite' ); ?>: regular_price</option>
									<option value="sale_price" <?php selected( $key, 'sale_price' ); ?>><?php esc_html_e( 'Price', 'woocommerce-product-csv-import-suite' ); ?>: sale_price</option>
									<option value="sale_price_dates_from" <?php selected( $key, 'sale_price_dates_from' ); ?>><?php esc_html_e( 'Price', 'woocommerce-product-csv-import-suite' ); ?>: sale_price_dates_from</option>
									<option value="sale_price_dates_to" <?php selected( $key, 'sale_price_dates_to' ); ?>><?php esc_html_e( 'Price', 'woocommerce-product-csv-import-suite' ); ?>: sale_price_dates_to</option>
									<option value="weight" <?php selected( $key, 'weight' ); ?>><?php esc_html_e( 'Dimensions', 'woocommerce-product-csv-import-suite' ); ?>: weight</option>
									<option value="length" <?php selected( $key, 'length' ); ?>><?php esc_html_e( 'Dimensions', 'woocommerce-product-csv-import-suite' ); ?>: length</option>
									<option value="width" <?php selected( $key, 'width' ); ?>><?php esc_html_e( 'Dimensions', 'woocommerce-product-csv-import-suite' ); ?>: width</option>
									<option value="height" <?php selected( $key, 'height' ); ?>><?php esc_html_e( 'Dimensions', 'woocommerce-product-csv-import-suite' ); ?>: height</option>
									<option value="tax_status" <?php selected( $key, 'tax_status' ); ?>><?php esc_html_e( 'Tax', 'woocommerce-product-csv-import-suite' ); ?>: tax_status</option>
									<option value="tax_class" <?php selected( $key, 'tax_class' ); ?>><?php esc_html_e( 'Tax', 'woocommerce-product-csv-import-suite' ); ?>: tax_class</option>
									<option value="upsell_ids" <?php selected( $key, 'upsell_ids' ); ?>><?php esc_html_e( 'Related Products', 'woocommerce-product-csv-import-suite' ); ?>: upsell_ids</option>
									<option value="crosssell_ids" <?php selected( $key, 'crosssell_ids' ); ?>><?php esc_html_e( 'Related Products', 'woocommerce-product-csv-import-suite' ); ?>: crosssell_ids</option>
									<option value="upsell_skus" <?php selected( $key, 'upsell_skus' ); ?>><?php esc_html_e( 'Related Products', 'woocommerce-product-csv-import-suite' ); ?>: upsell_skus</option>
									<option value="crosssell_skus" <?php selected( $key, 'crosssell_skus' ); ?>><?php esc_html_e( 'Related Products', 'woocommerce-product-csv-import-suite' ); ?>: crosssell_skus</option>
									<option value="file_paths" <?php selected( $key, 'file_paths' ); ?>><?php esc_html_e( 'Downloads', 'woocommerce-product-csv-import-suite' ); ?>: file_paths <?php esc_html_e( '(WC 2.0.x)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="downloadable_files" <?php selected( $key, 'downloadable_files' ); ?>><?php esc_html_e( 'Downloads', 'woocommerce-product-csv-import-suite' ); ?>: downloadable_files <?php esc_html_e( '(WC 2.1+)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="download_limit" <?php selected( $key, 'download_limit' ); ?>><?php esc_html_e( 'Downloads', 'woocommerce-product-csv-import-suite' ); ?>: download_limit</option>
									<option value="download_expiry" <?php selected( $key, 'download_expiry' ); ?>><?php esc_html_e( 'Downloads', 'woocommerce-product-csv-import-suite' ); ?>: download_expiry</option>
									<option value="product_url" <?php selected( $key, 'product_url' ); ?>><?php esc_html_e( 'External', 'woocommerce-product-csv-import-suite' ); ?>: product_url</option>
									<option value="button_text" <?php selected( $key, 'button_text' ); ?>><?php esc_html_e( 'External', 'woocommerce-product-csv-import-suite' ); ?>: button_text</option>
									<?php do_action( 'woocommerce_csv_product_data_mapping', $key ); ?>
								</optgroup>
								<?php if( function_exists( 'woocommerce_gpf_install' ) ) : ?>
								<optgroup label="<?php esc_html_e( 'Google Product Feed', 'woocommerce-product-csv-import-suite' ); ?>">
									<option value="gpf:adwords_grouping" <?php selected( $key, 'gpf:adwords_grouping' ); ?>><?php esc_html_e( 'Adwords grouping', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:adwords_labels" <?php selected( $key, 'gpf:adwords_labels' ); ?>><?php esc_html_e( 'Adwords labels', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:adult" <?php selected( $key, 'gpf:adult' ); ?>><?php esc_html_e( 'Adult content', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:age_group" <?php selected( $key, 'gpf:age_group' ); ?>><?php esc_html_e( 'Age Group', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:availability" <?php selected( $key, 'gpf:availability' ); ?>><?php esc_html_e( 'Availability', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:availability_date" <?php selected( $key, 'gpf:availability_date' ); ?>><?php esc_html_e( 'Availability date', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:brand" <?php selected( $key, 'gpf:brand' ); ?>><?php esc_html_e( 'Brand', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:is_bundle" <?php selected( $key, 'gpf:is_bundle' ); ?>><?php esc_html_e( 'Bundle indicator', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:color" <?php selected( $key, 'gpf:color' ); ?>><?php esc_html_e( 'Color', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:condition" <?php selected( $key, 'gpf:condition' ); ?>><?php esc_html_e( 'Condition', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:cost_of_goods_sold" <?php selected( $key, 'gpf:cost_of_goods_sold' ); ?>><?php esc_html_e( 'Cost of goods sold', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:custom_label_0" <?php selected( $key, 'gpf:custom_label_0' ); ?>><?php esc_html_e( 'Custom label 0', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:custom_label_1" <?php selected( $key, 'gpf:custom_label_1' ); ?>><?php esc_html_e( 'Custom label 1', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:custom_label_2" <?php selected( $key, 'gpf:custom_label_2' ); ?>><?php esc_html_e( 'Custom label 2', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:custom_label_3" <?php selected( $key, 'gpf:custom_label_3' ); ?>><?php esc_html_e( 'Custom label 3', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:custom_label_4" <?php selected( $key, 'gpf:custom_label_4' ); ?>><?php esc_html_e( 'Custom label 4', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:delivery_label" <?php selected( $key, 'gpf:delivery_label' ); ?>><?php esc_html_e( 'Delivery label', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:energy_efficiency_class" <?php selected( $key, 'gpf:energy_efficiency_class' ); ?>><?php esc_html_e( 'Energy efficiency class', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:min_energy_efficiency_class" <?php selected( $key, 'gpf:min_energy_efficiency_class' ); ?>><?php esc_html_e( 'Energy efficiency class (min)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:max_energy_efficiency_class" <?php selected( $key, 'gpf:max_energy_efficiency_class' ); ?>><?php esc_html_e( 'Energy efficiency class (max)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:energy_label_image_link" <?php selected( $key, 'gpf:energy_label_image_link' ); ?>><?php esc_html_e( 'Energy label image link', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:excluded_destination" <?php selected( $key, 'gpf:excluded_destination' ); ?>><?php esc_html_e( 'Excluded destination', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:exclude_product" <?php selected( $key, 'gpf:exclude_product' ); ?>><?php esc_html_e( 'Exclude Product', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:gender" <?php selected( $key, 'gpf:gender' ); ?>><?php esc_html_e( 'Gender', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:google_funded_promotion_eligibility" <?php selected( $key, 'gpf:google_funded_promotion_eligibility' ); ?>><?php esc_html_e( 'Google-funded promotion eligibility', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:google_product_category" <?php selected( $key, 'gpf:google_product_category' ); ?>><?php esc_html_e( 'Google Product Category', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:gtin" <?php selected( $key, 'gpf:gtin' ); ?>><?php esc_html_e( 'Global Trade Item Number (GTIN)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:included_destination" <?php selected( $key, 'gpf:included_destination' ); ?>><?php esc_html_e( 'Included destination', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:min_handling_time" <?php selected( $key, 'gpf:min_handling_time' ); ?>><?php esc_html_e( 'Handling time (min)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:max_handling_time" <?php selected( $key, 'gpf:max_handling_time' ); ?>><?php esc_html_e( 'Handling time (max)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:mpn" <?php selected( $key, 'gpf:mpn' ); ?>><?php esc_html_e( 'Manufacturer Part Number (MPN)', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:material" <?php selected( $key, 'gpf:material' ); ?>><?php esc_html_e( 'Material', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:multipack" <?php selected( $key, 'gpf:multipack' ); ?>><?php esc_html_e( 'Multipack', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:pattern" <?php selected( $key, 'gpf:pattern' ); ?>><?php esc_html_e( 'Pattern', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:product_type" <?php selected( $key, 'gpf:product_type' ); ?>><?php esc_html_e( 'Product Type', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:promotion_id" <?php selected( $key, 'gpf:promotion_id' ); ?>><?php esc_html_e( 'Promotion ID', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:purchase_quantity_limit" <?php selected( $key, 'gpf:purchase_quantity_limit' ); ?>><?php esc_html_e( 'Purchase quantity limit', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:return_address_label" <?php selected( $key, 'gpf:return_address_label' ); ?>><?php esc_html_e( 'Return address label identifier', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:return_policy_label" <?php selected( $key, 'gpf:return_policy_label' ); ?>><?php esc_html_e( 'Return policy label identifier', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:sell_on_google_quantity" <?php selected( $key, 'gpf:sell_on_google_quantity' ); ?>><?php esc_html_e( 'Sell on Google quantity', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:size" <?php selected( $key, 'gpf:size' ); ?>><?php esc_html_e( 'Size', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:size_type" <?php selected( $key, 'gpf:size_type' ); ?>><?php esc_html_e( 'Size type', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:size_system" <?php selected( $key, 'gpf:size_system' ); ?>><?php esc_html_e( 'Size system', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:unit_pricing_measure" <?php selected( $key, 'gpf:unit_pricing_measure' ); ?>><?php esc_html_e( 'Unit pricing measure', 'woocommerce-product-csv-import-suite' ); ?></option>
									<option value="gpf:unit_pricing_base_measure" <?php selected( $key, 'gpf:unit_pricing_base_measure' ); ?>><?php esc_html_e( 'Unit pricing base measure', 'woocommerce-product-csv-import-suite' ); ?></option>
								</optgroup>
								<?php endif; ?>
							</select>
							<?php
						}
					?>
				</td>
				<td width="25%"><?php echo esc_html( $raw_headers[ $key ] ); ?></td>
				<td><code><?php echo '' !== $value ? esc_html( $value ) : '-'; ?></code></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<p class="submit">
		<input type="submit" class="button" value="<?php esc_attr_e( 'Submit', 'woocommerce-product-csv-import-suite' ); ?>" />
		<input type="hidden" name="delimiter" value="<?php echo esc_attr( $this->delimiter ); ?>" />
		<input type="hidden" name="merge_empty_cells" value="<?php echo esc_attr( $this->merge_empty_cells ); ?>" />
	</p>
</form>
