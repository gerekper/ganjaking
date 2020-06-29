<?php
/**
 * Subscription form template dropdown input (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

wp_enqueue_style( 'yith-wcac-subscription-form-style', YITH_WCAC_URL . '/assets/css/frontend/types/select2.css', array(), YITH_WCAC::YITH_WCAC_VERSION );

$selected = isset( $_REQUEST['yith_wcac_shortcode_items']['fields'][ $id ] ) ? wc_clean( $_REQUEST['yith_wcac_shortcode_items']['fields'][ $id ] ) : array(); // phpcs:ignore
?>

<?php if ( ! empty( $active_campaign_data['options'] ) ) : ?>
	<span class="forminp">
	<select multiple="multiple" name="yith_wcac_shortcode_items[fields][<?php echo esc_attr( $id ); ?>][]" id="yith_wcac_shortcode_items_<?php echo esc_attr( $id ); ?>" class="chosen_select yith_wcac_listbox">
		<?php foreach ( $active_campaign_data['options'] as $id_option => $option ) : ?>
			<option value="<?php echo esc_attr( $option->value ); ?>" <?php selected( in_array( esc_attr( $option->value ), $selected ) ); ?> ><?php echo esc_html( $option->label ); ?></option>
		<?php endforeach; ?>
	</select>
</span>
<?php endif; ?>
