<?php
/**
 * Subscription form template text input (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */
?>

<textarea name="yith_wcac_shortcode_items[fields][<?php echo esc_attr( $id ); ?>]" id="yith_wcac_shortcode_items_<?php echo esc_attr( $id ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"><?php echo isset( $_REQUEST['yith_wcac_shortcode_items']['fields'][ $id ] ) ? esc_html( sanitize_textarea_field( wp_unslash( $_REQUEST['yith_wcac_shortcode_items']['fields'][ $id ] ) ) ) : ''; ?></textarea>
