<?php
/**
 * Admin subscription scheme view.
 *
 * @package  WooCommerce All Products For Subscriptions
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div class="subscription_scheme wc-metabox open" rel="<?php echo isset( $scheme_data[ 'position' ] ) ? esc_attr( $scheme_data[ 'position' ] ) : ''; ?>">
	<div class="handle">
		<span class="scheme-handle" aria-label="<?php esc_attr_e( 'Drag to sort', 'woocommerce-all-products-for-subscriptions' ); ?>"></span>
		<span class="scheme-remove"><?php esc_html_e( 'Remove', 'woocommerce-all-products-for-subscriptions' ); ?></span>
	</div>
	<div class="data subscription_scheme_data"><?php

		// Subscription Scheme Options.
		do_action( 'wcsatt_subscription_scheme_content', $index, $scheme_data, $post_id );

	?></div>
	<?php

	if ( isset( $scheme_data[ 'id' ] ) ) {
		?><input type="hidden" name="wcsatt_schemes[<?php echo absint( $index ); ?>][id]" class="scheme_id" value="<?php echo esc_attr( $scheme_data[ 'id' ] ); ?>" /><?php
	}
	?><input type="hidden" name="wcsatt_schemes[<?php echo absint( $index ); ?>][position]" class="position" value="<?php echo isset( $scheme_data[ 'position' ] ) ? absint( $index ) : ''; ?>"/>
</div>
