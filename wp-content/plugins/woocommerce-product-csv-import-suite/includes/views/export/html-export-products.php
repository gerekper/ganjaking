<div class="tool-box">

	<h3 class="title"><?php _e('Export Product CSV', 'woocommerce-product-csv-import-suite'); ?></h3>
	<p><?php _e('Export your products using this tool. This exported CSV will be in an importable format.', 'woocommerce-product-csv-import-suite'); ?></p>
	<p class="description"><?php _e('Click export to save your products to your computer.', 'woocommerce-product-csv-import-suite'); ?></p>

	<form action="<?php echo admin_url('admin.php?page=woocommerce_csv_import_suite&action=export'); ?>" method="post">

		<table class="form-table">
			<tr>
				<th>
					<label for="v_limit"><?php _e( 'Limit', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<input type="text" name="limit" id="v_limit" placeholder="<?php _e('Unlimited', 'woocommerce-product-csv-import-suite'); ?>" class="input-text" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="v_offset"><?php _e( 'Offset', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<input type="text" name="offset" id="v_offset" placeholder="<?php _e('0', 'woocommerce-product-csv-import-suite'); ?>" class="input-text" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="v_columns"><?php _e( 'Columns', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<select id="v_columns" name="columns[]" data-placeholder="<?php _e('All Columns', 'woocommerce-product-csv-import-suite'); ?>" class="wc-enhanced-select" multiple="multiple">
						<?php
							foreach ($post_columns as $key => $column) {
								echo '<option value="'.$key.'">'.$column.'</option>';
							}
							echo '<option value="images">'.__('Images (featured and gallery)', 'woocommerce-product-csv-import-suite').'</option>';
							echo '<option value="file_paths">'.__('Downloadable file paths', 'woocommerce-product-csv-import-suite').'</option>';
							echo '<option value="taxonomies">'.__('Taxonomies (cat/tags/shipping-class)', 'woocommerce-product-csv-import-suite').'</option>';
							echo '<option value="attributes">'.__('Attributes', 'woocommerce-product-csv-import-suite').'</option>';
							echo '<option value="meta">'.__('Meta (custom fields)', 'woocommerce-product-csv-import-suite').'</option>';

							if ( function_exists( 'woocommerce_gpf_install' ) )
								echo '<option value="gpf">'.__('Google Product Feed fields', 'woocommerce-product-csv-import-suite').'</option>';
						?>
						</select>
				</td>
			</tr>
			<tr>
				<th>
					<label for="v_include_hidden_meta"><?php _e( 'Include hidden meta data', 'woocommerce-product-csv-import-suite' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="include_hidden_meta" id="v_include_hidden_meta" class="checkbox" />
				</td>
			</tr>
		</table>

		<p class="submit"><input type="submit" class="button" value="<?php _e('Export Products', 'woocommerce-product-csv-import-suite'); ?>" /></p>

	</form>
</div>