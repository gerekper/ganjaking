<?php
/**
 * Subscription form template tags (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

wp_enqueue_style( 'yith-wcac-subscription-form-style', WC()->plugin_url() . '/assets/css/select2.css', array(), WC()->version );


$selected = isset( $_REQUEST['yith_wcac_shortcode_items']['default'][ $id ] ) ? wc_clean( $_REQUEST['yith_wcac_shortcode_items']['default'][ $id ] ) : array(); // phpcs:ignore
?>

<?php if ( ! empty( $active_campaign_data['options'] ) ) : ?>
	<span class="forminp">
		<select multiple="multiple" name="yith_wcac_shortcode_items[default][<?php echo esc_attr( $id ); ?>][]" id="yith_wcac_shortcode_items_<?php echo esc_attr( $id ); ?>" class="chosen_select yith_wcac_listbox">
			<?php foreach ( $active_campaign_data['options'] as $id_option => $option ) : ?>
				<option value="<?php echo esc_html( $id_option ); ?>" <?php selected( in_array( $id_option, $selected ) ); ?> ><?php echo esc_html( $option ); ?></option>
			<?php endforeach; ?>
		</select>
	</span>
<?php endif; ?>
