<?php
/**
 * Subscription form template dropdown input (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

$selected = isset( $_REQUEST['yith_wcac_shortcode_items']['fields'][ $id ] ) ? wc_clean( $_REQUEST['yith_wcac_shortcode_items']['fields'][ $id ] ) : ''; // phphcs:ignore
?>

<?php if ( ! empty( $active_campaign_data['options'] ) ) : ?>

	<select name="yith_wcac_shortcode_items[fields][<?php echo esc_attr( $id ); ?>]" id="yith_wcac_shortcode_items_<?php echo esc_attr( $id ); ?>">
		<?php foreach ( $active_campaign_data['options'] as $id_option => $option ) : ?>
			<option value="<?php echo esc_attr( $option->value ); ?>" <?php selected( esc_attr( $option->value ), $selected ); ?> ><?php echo esc_html( $option->label ); ?></option>
		<?php endforeach; ?>
	</select>

<?php endif; ?>
