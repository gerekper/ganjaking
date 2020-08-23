<div class="tool-box">

	<h3 class="title"><?php esc_html_e( 'Export Product Variations CSV', 'woocommerce-product-csv-import-suite' ); ?></h3>
	<p><?php esc_html_e( 'Export your product variations using this tool. This exported CSV will be in an importable format.', 'woocommerce-product-csv-import-suite' ); ?></p>
	<p class="description"><?php esc_html_e( 'Click export to save your products variations to your computer.', 'woocommerce-product-csv-import-suite' ); ?></p>

	<form action="<?php echo esc_url( admin_url( 'admin.php?page=woocommerce_csv_import_suite&action=export_variations' ) ); ?>" method="post">

		<table class="form-table">
			<tr>
				<th>
					<label for="limit"><?php esc_html_e( 'Limit', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<input type="text" name="limit" id="limit" placeholder="<?php esc_attr_e( 'Unlimited', 'woocommerce-product-csv-import-suite' ); ?>" class="input-text" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="offset"><?php esc_html_e( 'Offset', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<input type="text" name="offset" id="offset" placeholder="<?php esc_attr_e( '0', 'woocommerce-product-csv-import-suite' ); ?>" class="input-text" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="product"><?php esc_html_e( 'Limit to parent ID(s)', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<input type="text" name="product_limit" id="product_limit" placeholder="<?php esc_attr_e( 'N/A', 'woocommerce-product-csv-import-suite' ); ?>" class="input-text" /> <span class="description"><?php _e( 'Comma separate IDs', 'woocommerce-product-csv-import-suite' ); ?></span>
				</td>
			</tr>
			<tr>
				<th>
					<label for="columns"><?php esc_html_e( 'Columns', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<select id="columns" name="columns[]" data-placeholder="<?php esc_attr_e( 'All Columns', 'woocommerce-product-csv-import-suite' ); ?>" class="wc-enhanced-select" multiple="multiple">
						<?php
							foreach ( $variation_columns as $key => $column ) {
								echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</option>';
							}
							echo '<option value="images">' . esc_html__( 'Images (featured and gallery)', 'woocommerce-product-csv-import-suite' ) . '</option>';
							echo '<option value="taxonomies">' . esc_html__( 'Taxonomies (cat/tags/shipping-class)', 'woocommerce-product-csv-import-suite' ) . '</option>';
							echo '<option value="meta">' . esc_html__( 'Meta (custom fields)', 'woocommerce-product-csv-import-suite' ) . '</option>';
						?>
						</select>
				</td>
			</tr>
			<tr>
				<th>
					<label for="include_hidden_meta"><?php esc_html_e( 'Include hidden meta data', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="include_hidden_meta" id="include_hidden_meta" class="checkbox" />
				</td>
			</tr>
		</table>

		<p class="submit"><input type="submit" class="button" value="<?php esc_attr_e( 'Export Variations', 'woocommerce-product-csv-import-suite' ); ?>" /></p>

	</form>
</div>
