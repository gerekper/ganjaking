<?php
/**
 * Extra costs in "Costs" product tabs
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 *
 * @package YITH\Booking\Modules\Costs
 */

defined( 'YITH_WCBK' ) || exit;

$breadcrumb = sprintf(
	'YITH > Booking > %s > %s',
	_x( 'Configuration', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
	_x( 'Costs', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' )
);
$url        = add_query_arg( array( 'post_type' => YITH_WCBK_Post_Types::EXTRA_COST ), admin_url( 'edit.php' ) );
?>
<div class="yith-wcbk-extra-costs yith-wcbk-settings-section">
	<div class="yith-wcbk-settings-section__title">
		<h3><?php esc_html_e( 'Extra Costs', 'yith-booking-for-woocommerce' ); ?></h3>
		<span class="yith-wcbk-settings-section__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
	</div>
	<div class="yith-wcbk-settings-section__content">
		<div class="yith-wcbk-settings-section__description">

			<?php
			echo sprintf(
			// translators: %s is the settings path (YITH > Booking > Configuration > Costs).
				esc_html__( "Set the price for the global costs (created in %s) or leave the fields empty if you don't want to apply extra costs to this bookable product.", 'yith-booking-for-woocommerce' ),
				'<a href="' . esc_url( $url ) . '">' . esc_html( $breadcrumb ) . '</a>'
			);
			?>
			<br/>
			<?php esc_html_e( 'You can also create specific costs only for this product.', 'yith-booking-for-woocommerce' ); ?>
		</div>
		<?php
		$extra_costs = $booking_product ? $booking_product->get_extra_costs( 'edit' ) : array();
		yith_wcbk_get_module_view( 'costs', 'product-tabs/extra-costs/extra-costs.php', compact( 'extra_costs' ) );
		?>
	</div>
</div>
