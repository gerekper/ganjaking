<div class="tool-box">

	<h3 class="title"><?php esc_html_e( 'Export Product CSV', 'woocommerce-product-csv-import-suite' ); ?></h3>
	<p><?php esc_html_e( 'Export your products using this tool. This exported CSV will be in an importable format.', 'woocommerce-product-csv-import-suite' ); ?></p>
	<p class="description"><?php esc_html_e( 'Click export to save your products to your computer.', 'woocommerce-product-csv-import-suite' ); ?></p>

	<form action="<?php echo esc_url( admin_url( 'admin.php?page=woocommerce_csv_import_suite&action=export' ) ); ?>" method="post">

		<table class="form-table">
			<tr>
				<th>
					<label for="v_limit"><?php esc_html_e( 'Limit', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<input type="text" name="limit" id="v_limit" placeholder="<?php esc_attr_e( 'Unlimited', 'woocommerce-product-csv-import-suite' ); ?>" class="input-text" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="v_offset"><?php esc_html_e( 'Offset', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<input type="text" name="offset" id="v_offset" placeholder="<?php esc_attr_e( '0', 'woocommerce-product-csv-import-suite' ); ?>" class="input-text" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="v_columns"><?php esc_html_e( 'Columns', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<select id="v_columns" name="columns[]" data-placeholder="<?php esc_html_e( 'All Columns', 'woocommerce-product-csv-import-suite' ); ?>" class="wc-enhanced-select" multiple="multiple">
						<?php
							foreach ( $post_columns as $key => $column ) {
								echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</option>';
							}
							echo '<option value="images">' . esc_html__( 'Images (featured and gallery)', 'woocommerce-product-csv-import-suite' ) . '</option>';
							echo '<option value="file_paths">' . esc_html__( 'Downloadable file paths', 'woocommerce-product-csv-import-suite' ) . '</option>';
							echo '<option value="taxonomies">' . esc_html__( 'Taxonomies (cat/tags/shipping-class)', 'woocommerce-product-csv-import-suite' ) . '</option>';
							echo '<option value="attributes">' . esc_html__( 'Attributes', 'woocommerce-product-csv-import-suite' ) . '</option>';
							echo '<option value="meta">' . esc_html__( 'Meta (custom fields)', 'woocommerce-product-csv-import-suite' ) . '</option>';

							if ( function_exists( 'woocommerce_gpf_install' ) ) {
								echo '<option value="gpf">' . esc_html__( 'Google Product Feed fields', 'woocommerce-product-csv-import-suite' ) . '</option>';
							}
						?>
						</select>
				</td>
			</tr>
			<tr>
				<th>
					<label for="v_include_hidden_meta"><?php esc_html_e( 'Include hidden meta data', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="include_hidden_meta" id="v_include_hidden_meta" class="checkbox" />
				</td>
			</tr>
		</table>

		<p class="submit"><input type="submit" class="button" value="<?php esc_attr_e( 'Export Products', 'woocommerce-product-csv-import-suite' ); ?>" /></p>

	</form>
</div>
