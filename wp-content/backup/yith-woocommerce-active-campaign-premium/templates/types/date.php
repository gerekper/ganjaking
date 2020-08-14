<?php
/**
 * Subscription form template date input (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */
?>

<input
	type="text"
	class="_field_datepicker"
	name="yith_wcac_shortcode_items[fields][<?php echo esc_attr( $id ); ?>]"
	id="yith_wcac_shortcode_items_<?php echo esc_attr( $id ); ?>"
	value="<?php echo isset( $_REQUEST['yith_wcac_shortcode_items']['fields'][ $id ] ) ? wc_clean( $_REQUEST['yith_wcac_shortcode_items']['fields'][ $id ] ) : ''; // phpcs:ignore ?>"
	placeholder="<?php echo esc_attr( $placeholder ); ?>" />
