<?php
/**
 * Meta box view: Subscription delivery
 *
 * @package WC_OD/Admin/Meta Boxes
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Variables.
 *
 * @var array           $fields
 * @var WC_Subscription $subscription
 */
?>
<div class="wc-od-subscription-delivery wc-od-metabox">
	<div class="wc-od-subscription-delivery__next-order">
		<?php
		foreach ( $fields as $key => $field ) :
			wc_od_admin_field( $field );
		endforeach;
		?>
	</div>

	<?php if ( wc_od_subscription_has_delivery_preferences( $subscription ) ) : ?>
		<div class="wc-od-subscription-delivery__preferences">
			<label class="wc-od-subscription-delivery__preferences-toggle"><?php esc_html_e( 'Customer preferences', 'woocommerce-order-delivery' ); ?> <span class="toggle-indicator" aria-hidden="true"></span></label>

			<div class="wc-od-subscription-delivery__preferences-content">
				<?php
					/**
					 * Allows to include the delivery preferences in the subscription delivery meta box.
					 *
					 * @hocked wc_od_admin_subscription_delivery_preferences - 10
					 *
					 * @since 1.3.0
					 *
					 * @param WC_Subscription $subscription The subscription instance.
					 */
					do_action( 'wc_od_admin_subscription_delivery_preferences', $subscription );
				?>
			</div>
		</div>
	<?php endif; ?>
</div>
