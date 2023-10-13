<?php
/**
 * Services Tab in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$services         = yith_wcbk_get_services( array( 'return' => 'id=>name' ) );
$product_services = $booking_product ? $booking_product->get_service_ids( 'edit' ) : array();
?>

<div class="yith-wcbk-product-metabox-options-panel yith-plugin-ui options_group show_if_<?php echo esc_attr( $prod_type ); ?>">
	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php esc_html_e( 'Services', 'yith-booking-for-woocommerce' ); ?></h3>
		</div>
		<div class="yith-wcbk-settings-section__content yith-wcbk-services__list">
			<?php if ( current_user_can( 'manage_' . YITH_WCBK_Post_Types::SERVICE_TAX . 's' ) ) : ?>
				<div class="yith-wcbk-settings-section__description">
					<?php
					$settings_path = sprintf(
						'YITH > Booking > %s > %s',
						_x( 'Configuration', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
						_x( 'Services', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' )
					);
					echo sprintf(
					// translators: %s is the settings path (YITH > Booking > Configuration > Services).
						esc_html__( 'You can create services in %s', 'yith-booking-for-woocommerce' ),
						'<a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=yith_booking_service' ) ) . '">' . esc_html( $settings_path ) . '</a>'
					);
					?>
				</div>
			<?php endif; ?>
			<?php
			$service_field_html = "<select id='_yith_wcbk_booking_services' name='_yith_booking_services[]' class='multiselect attribute_values wc-enhanced-select' multiple='multiple'
                        placeholder='" . __( 'select one or more services...', 'yith-booking-for-woocommerce' ) . "' style='width:400px;'>";
			foreach ( $services as $service_id => $service_name ) {
				$service_id   = absint( $service_id );
				$service_name = esc_html( apply_filters( 'yith_wcbk_product_tabs_service_name', $service_name, $service_id ) );

				$service_field_html .= "<option value='{$service_id}' " . selected( in_array( $service_id, $product_services, true ), true, false ) . ">{$service_name}</option>";
			}
			$service_field_html .= '</select>';
			$service_field_html .= "<div class='yith-wcbk-booking-services__actions'>";
			$service_field_html .= "<span class='yith-plugin-fw__button--secondary yith-wcbk-select2-select-all' data-select-id='_yith_wcbk_booking_services'>" . esc_html__( 'Select all', 'yith-booking-for-woocommerce' ) . '</span>';
			$service_field_html .= "<span class='yith-plugin-fw__button--secondary yith-wcbk-select2-deselect-all' data-select-id='_yith_wcbk_booking_services'>" . esc_html__( 'Deselect all', 'yith-booking-for-woocommerce' ) . '</span>';
			$service_field_html .= '</div>';

			yith_wcbk_form_field(
				array(
					'class'  => 'yith_booking_multi_fields',
					'title'  => __( 'Insert services available for this product', 'yith-booking-for-woocommerce' ),
					'desc'   => __( "Click on the field to add a service available for this product or click on 'Select all' to add all services", 'yith-booking-for-woocommerce' ),
					'fields' => array(
						'yith-field' => true,
						'type'       => 'html',
						'html'       => $service_field_html,
					),
				)
			);
			?>
		</div>
	</div>
</div>
