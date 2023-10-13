<?php
/**
 * Availability tab in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

?>
<div class="yith-wcbk-product-metabox-options-panel yith-plugin-ui options_group show_if_<?php echo esc_attr( $prod_type ); ?>">

	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Availability', 'yith-booking-for-woocommerce' ); ?></h3>
			<span class="yith-wcbk-settings-section__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<div class="yith-wcbk-product-settings__availability__default-availability">
				<?php
				$default_availabilities = $booking_product ? $booking_product->get_default_availabilities( 'edit' ) : array();
				$default_availabilities = ! ! $default_availabilities ? $default_availabilities : array( new YITH_WCBK_Availability() );
				$field_name             = '_yith_booking_default_availabilities';
				ob_start();
				yith_wcbk_get_view( 'product-tabs/utility/html-default-availabilities.php', compact( 'default_availabilities', 'field_name' ) );
				$default_availabilities_html = ob_get_clean();

				yith_wcbk_form_field(
					array(
						'title'  => __( 'Set default availability', 'yith-booking-for-woocommerce' ),
						'desc'   => implode(
							'<br />',
							array(
								esc_html__( 'Set the default availability for this product.', 'yith-booking-for-woocommerce' ),
								esc_html__( 'You can override these options by using the additional availability rules below.', 'yith-booking-for-woocommerce' ),
							)
						),
						'class'  => 'yith_booking_default_availabilities_wrapper',
						'fields' => array(
							'type'  => 'html',
							'value' => $default_availabilities_html,
						),
					)
				);

				?>
			</div>
			<div class="yith-wcbk-product-settings__availability__availability-rules">
				<div class="yith-wcbk-product-settings__availability__availability-rules__title">
					<h3><?php esc_html_e( 'Additional availability rules', 'yith-booking-for-woocommerce' ); ?></h3>
					<div class="yith-wcbk-availability-rules__expand-collapse">
						<span class="yith-wcbk-availability-rules__expand"><?php esc_html_e( 'Expand all', 'yith-booking-for-woocommerce' ); ?></span>
						<span class="yith-wcbk-availability-rules__collapse"><?php esc_html_e( 'Collapse all', 'yith-booking-for-woocommerce' ); ?></span>
					</div>
				</div>
				<div class="yith-wcbk-settings-section__description"><?php esc_html_e( 'You can create advanced rules to enable/disable booking availability for specific dates or months', 'yith-booking-for-woocommerce' ); ?></div>
				<?php
				$availability_rules = $booking_product ? $booking_product->get_availability_rules( 'edit' ) : array();
				$field_name         = '_yith_booking_availability_range';
				yith_wcbk_get_view( 'product-tabs/utility/html-availability-rules.php', compact( 'availability_rules', 'field_name' ) );
				?>
			</div>
		</div>
	</div>

</div>
