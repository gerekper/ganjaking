<?php
/**
 * YITH WCWTL Importer Step: Map CSV columns
 *
 * @since   1.6.0
 * @package YITH WooCommerce Waiting List
 */

defined( 'YITH_WCWTL' ) || exit;
?>

<form id="yith-wcwtl-choose-product" enctype="multipart/form-data" method="POST">
	<header>
		<h2><?php esc_html_e( 'Map CSV fields', 'yith-woocommerce-waiting-list' ); ?></h2>
		<p><?php esc_html_e( 'Select the field in your CSV file that you want to use as customer emails', 'yith-woocommerce-waiting-list' ); ?></p>
	</header>
	<section>
		<label for="column_map_index"
			class="label_select"><?php esc_html_e( 'Column name', 'yith-woocommerce-waiting-list' ); ?></label><br>
		<select id="column_map_index" name="column_map_index">
			<option value="-1"><?php esc_html_e( 'Select a column...', 'yith-woocommerce-waiting-list' ); ?></option>
			<?php foreach ( $headers as $index => $name ) : ?>
				<option value="<?php echo esc_attr( $index ); ?>"><?php echo esc_html( $name ); ?></option>
			<?php endforeach ?>
		</select>
	</section>
	<footer>
		<?php wp_nonce_field( 'yith-wcwtl-importer-action', '__wpnonce' ); ?>
		<input type="submit" id="next_step" class="button button-primary button-hero" name="next_step"
			value="<?php esc_attr_e( 'Run the importer', 'yith-woocommerce-waiting-list' ); ?>"/>
		<input type="hidden" name="product" value="<?php echo (int) $this->chosen_product; ?>"/>
		<input type="hidden" name="file" value="<?php echo esc_attr( $this->file ); ?>"/>
		<input type="hidden" name="delimiter" value="<?php echo esc_attr( $this->delimiter ); ?>"/>
		<input type="hidden" name="enclosure" value="<?php echo esc_attr( $this->enclosure ); ?>"/>
		<input type="hidden" name="overwrite_existing" value="<?php echo (int) $this->overwrite_existing; ?>"/>
	</footer>
</form>
