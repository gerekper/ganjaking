<?php
/**
 * Subscription form template email input (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */
?>

<input
	type="email"
	name="yith_wcac_shortcode_items[default][<?php echo esc_attr( $id ); ?>]"
	id="yith_wcac_shortcode_items_<?php echo esc_attr( $id ); ?>"
	value="<?php echo isset( $_REQUEST['yith_wcac_shortcode_items']['default'][ $id ] ) ? wc_clean( $_REQUEST['yith_wcac_shortcode_items']['default'][ $id ] ) : ''; // phpcs:ignore ?>"
	placeholder="<?php echo esc_attr( $placeholder ); ?>" />