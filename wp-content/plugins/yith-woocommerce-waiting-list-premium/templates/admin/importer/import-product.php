<?php
/**
 * YITH WCWTL Importer Step: Choose a Product
 *
 * @since   1.6.0
 * @package YITH WooCommerce Waiting List
 */

defined( 'YITH_WCWTL' ) || exit;

?>
<form id="yith-wcwtl-choose-product" enctype="multipart/form-data" method="POST">
	<header>
		<h2><?php esc_html_e( 'Waiting List Import Tool', 'yith-woocommerce-waiting-list' ); ?></h2>
		<p><?php esc_html_e( 'This tool allows you to import (or merge) customer emails from a CSV file into an existing waiting list.', 'yith-woocommerce-waiting-list' ); ?></p>
	</header>
	<section>
		<label for="product_id"
			class="label_select"><?php esc_html_e( 'Choose a product', 'yith-woocommerce-waiting-list' ); ?></label><br>
		<?php yit_add_select2_fields( array(
			'class'            => 'wc-product-search',
			'data-placeholder' => __( 'Select a product', 'yith-woocommerce-waiting-list' ),
			'data-multiple'    => false,
			'id'               => 'product_id',
			'name'             => 'product_id',
		) ); ?>
	</section>
	<footer>
		<?php wp_nonce_field( 'yith-wcwtl-importer-action', '__wpnonce' ); ?>
		<input type="submit" value="<?php esc_html_e( 'Continue', 'yith-woocommerce-waiting-list' ); ?>" id="next_step"
			class="button button-primary button-hero" name="next_step">
	</footer>
</form>
