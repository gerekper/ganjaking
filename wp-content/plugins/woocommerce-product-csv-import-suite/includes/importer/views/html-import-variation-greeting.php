<div>
	<p><?php esc_html_e( 'Hi there! Upload a CSV file containing product variation data to import the contents into your shop.', 'woocommerce-product-csv-import-suite' ); ?></p>
	<p><?php esc_html_e( 'Choose a CSV (.csv) file to upload, then click Upload file and import.', 'woocommerce-product-csv-import-suite' ); ?></p>

	<?php if ( ! empty( $upload_dir['error'] ) ) : ?>
		<div class="error"><p><?php esc_html_e( 'Before you can upload your import file, you will need to fix the following error:', 'woocommerce-product-csv-import-suite' ); ?></p>
		<p><strong><?php echo esc_html( $upload_dir['error'] ); ?></strong></p></div>
	<?php else : ?>
		<form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr( wp_nonce_url( $action, 'import-upload' ) ); ?>">
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="upload"><?php esc_html_e( 'Choose a file from your computer:', 'woocommerce-product-csv-import-suite' ); ?></label>
						</th>
						<td>
							<input type="file" id="upload" name="import" size="25" />
							<input type="hidden" name="action" value="save" />
							<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
							<small>
								<?php
								// translators: %s is file size.
								echo sprintf( esc_html__('Maximum size: %s', 'woocommerce-product-csv-import-suite' ), esc_html( $size ) );
								?>
							</small>
						</td>
					</tr>
					<?php if ( $this->file_url_import_enabled ) : ?>
					<tr>
						<th>
							<label for="file_url"><?php esc_html_e( 'OR enter path to file:', 'woocommerce-product-csv-import-suite' ); ?></label>
						</th>
						<td>
							<?php echo ' ' . esc_html( wp_normalize_path( WP_CONTENT_DIR ) ) . ' '; ?><input type="text" id="file_url" name="file_url" size="50" />
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<th><label><?php esc_html_e( 'Delimiter', 'woocommerce-product-csv-import-suite' ); ?></label><br/></th>
						<td><input type="text" name="delimiter" placeholder="," /></td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" class="button" value="<?php esc_attr_e( 'Upload file and import', 'woocommerce-product-csv-import-suite' ); ?>" />
			</p>
		</form>
	<?php endif; ?>
</div>
