<?php
/**
 * Active Campaign data metabox
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

?>

<div class="checkout-preferences">
	<h4><?php _e( 'Checkout preferences', 'yith-woocommerce-active-campaign' ) ?></h4>
	<p class="description">
		<?php _e( 'In this section you\'ll find data about customer consent to Active Campaign subscription', 'yith-woocommerce-active-campaign' ) ?>
	</p>
	<p class="option">
		<span class="option-label"><?php _e( 'Customer subscribed:', 'yith-woocommerce-active-campaign' ) ?></span>
		<span class="option-value"><?php echo $customer_subscribed ? __( 'yes', 'yith-woocommerce-active-campaign' ) : __( 'no', 'yith-woocommerce-active-campaign' ) ?></span>
	</p>

	<p class="option">
		<span class="option-label"><?php _e( 'Checkbox shown at checkout:', 'yith-woocommerce-active-campaign' ) ?></span>
		<span class="option-value"><?php echo $show_checkbox ? __( 'yes', 'yith-woocommerce-active-campaign' ) : __( 'no', 'yith-woocommerce-active-campaign' ) ?></span>
	</p>

	<p class="option">
		<span class="option-label"><?php _e( 'Submitted value:', 'yith-woocommerce-active-campaign' ) ?></span>
		<span class="option-value"><?php echo $submitted_value == 'yes' ? __( 'yes', 'yith-woocommerce-active-campaign' ) : __( 'no', 'yith-woocommerce-active-campaign' ) ?></span>
	</p>
</div>

<?php if( ! empty( $personal_data ) ): ?>
	<div class="active-campaign-personal-data">
		<h4><?php _e( 'Personal data', 'yith-woocommerce-active-campaign' ) ?></h4>
		<p class="description">
			<?php _e( 'In this section you\'ll find customer personal data that was sent to Active Campaign servers', 'yith-woocommerce-active-campaign' ) ?>
		</p>
		<?php foreach( $personal_data as $data ): ?>
			<p class="option">
				<span class="option-label"><?php echo $data['label'] ?>:</span>
				<span class="option-value"><?php echo $data['value'] ?></span>
			</p>
		<?php endforeach; ?>
	</div>
<?php endif; ?>