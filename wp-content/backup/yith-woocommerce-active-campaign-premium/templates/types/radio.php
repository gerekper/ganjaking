<?php
/**
 * Subscription form template dropdown input (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

$selected = isset( $_REQUEST['yith_wcac_shortcode_items']['fields'][ $id ] ) ? wc_clean( $_REQUEST['yith_wcac_shortcode_items']['fields'][ $id ] ) : ''; // phpcs:ignore
?>

<?php if ( ! empty( $active_campaign_data['options'] ) ) : ?>
	<?php foreach ( $active_campaign_data['options'] as $id_option => $option ) : ?>
		<input type="radio"
			   value="<?php echo esc_attr( $option->value ); ?>"
			   id="yith_wcac_shortcode_items_<?php echo esc_attr( $id ); ?>_<?php echo esc_attr( $id_option ); ?>"
			   name="yith_wcac_shortcode_items[fields][<?php echo esc_attr( $id ); ?>]"
			   <?php checked( esc_attr( $option->value ), $selected ); ?> />
		<label class="label_field_item" for="yith_wcac_shortcode_items_<?php echo esc_attr( $id ); ?>_<?php echo esc_attr( $id_option ); ?>"><?php echo esc_html( $option->label ); ?></label>
		<br/>
	<?php endforeach; ?>
<?php endif; ?>