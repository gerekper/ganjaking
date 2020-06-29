<?php
/**
 * Subscription form template text input (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

$value = '';
if ( is_numeric( $id ) ) {
	$name  = 'yith_wcac_shortcode_items[fields][' . esc_attr( $id ) . ']';
	$value = isset( $_REQUEST['yith_wcac_shortcode_items']['fields'][ $id ] ) ? wc_clean( $_REQUEST['yith_wcac_shortcode_items']['fields'][ $id ] ) : $value; // phpcs:ignore
} else {
	$name  = 'yith_wcac_shortcode_items[default][' . esc_attr( $id ) . ']';
	$value = isset( $_REQUEST['yith_wcac_shortcode_items']['default'][ $id ] ) ? wc_clean( $_REQUEST['yith_wcac_shortcode_items']['default'][ $id ] ) : $value; // phpcs:ignore

}

?>

<input type="text" name="<?php echo esc_attr( $name ); ?>" id="yith_wcac_shortcode_items_<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" />
