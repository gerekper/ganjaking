<?php
/**
 * YITH WCWTL Importer Step: Upload Form
 *
 * @since   1.6.0
 * @package YITH WooCommerce Waiting List
 */

defined( 'YITH_WCWTL' ) || exit;

?>
<form id="yith-wcwtl-upload-csv" enctype="multipart/form-data" method="POST">
	<header>
		<h2><?php esc_html_e( 'Upload a CSV file', 'yith-woocommerce-waiting-list' ); ?></h2>
	</header>
	<section>
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					<label for="upload"><?php esc_html_e( 'Choose a CSV file', 'yith-woocommerce-waiting-list' ); ?></label>
				</th>
				<td>
					<?php
					if ( ! empty( $upload_dir['error'] ) ) {
						?>
						<div class="inline error">
							<p><?php esc_html_e( 'Before you can upload your import file, you have to fix the following error:', 'yith-woocommerce-waiting-list' ); ?></p>
							<p><strong><?php echo esc_html( $upload_dir['error'] ); ?></strong></p>
						</div>
						<?php
					} else {
						?>
						<input type="file" id="upload" name="import" size="25"/>
						<input type="hidden" name="action" value="save"/>
						<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>"/>
						<br>
						<small><?php printf( esc_html_x( 'Maximum size: %s', 'The maximum upload size', 'woocommerce' ), esc_html( $size ) ); ?></small>
						<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<th><label
						for="overwrite_existing"><?php esc_html_e( 'Overwrite existing waiting list', 'yith-woocommerce-waiting-list' ); ?></label>
				</th>
				<td><input type="checkbox" id="overwrite_existing" name="overwrite_existing" value="1"/></td>
			</tr>
			<tr>
				<th><label for="delimiter"><?php esc_html_e( 'CSV Delimiter', 'yith-woocommerce-waiting-list' ); ?></label></th>
				<td><input type="text" name="delimiter" placeholder="," size="2"/></td>
			</tr>
			<tr>
				<th><label for="enclosure"><?php esc_html_e( 'CSV Enclosure', 'yith-woocommerce-waiting-list' ); ?></label></th>
				<td><input type="text" name="'enclosure" placeholder="<?php echo esc_attr( '"' ) ?>" size="2"/></td>
			</tr>
			</tbody>
		</table>
	</section>
	<footer>
		<?php wp_nonce_field( 'yith-wcwtl-importer-action', '__wpnonce' ); ?>
		<input type="hidden" name="product" value="<?php echo (int) $this->chosen_product; ?>"/>
		<input type="submit" value="<?php esc_attr_e( 'Continue', 'yith-woocommerce-waiting-list' ); ?>" id="next_step"
			class="button button-primary button-hero" name="next_step">
	</footer>
</form>

