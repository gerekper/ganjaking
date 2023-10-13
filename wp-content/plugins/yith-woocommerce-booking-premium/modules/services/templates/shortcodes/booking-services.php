<?php
/**
 * Booking services shortcode Template
 *
 * @var WC_Product_Booking $product
 * @var string             $type
 * @var string             $show_title
 * @var string             $show_prices
 * @var string             $show_descriptions
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$services_labels = array(
	'additional' => yith_wcbk_get_label( 'additional-services' ),
	'included'   => yith_wcbk_get_label( 'included-services' ),
);
$services_labels = apply_filters( 'yith_wcbk_shortcode_services_labels', $services_labels, $product );

?>
<div class="yith-wcbk-shortcode-services-wrapper">
	<?php
	if ( $product->has_services() ) {
		$services = $product->get_service_ids();
		if ( ! ! $services && is_array( $services ) ) {

			$services_to_display = array(
				'additional' => array(),
				'included'   => array(),
			);

			foreach ( $services as $service_id ) {
				$service = yith_wcbk_get_service( $service_id );

				if ( ! $service || $service->is_hidden() ) {
					continue;
				}

				if ( $service->is_optional() ) {
					$services_to_display['additional'][] = $service;
				} else {
					$services_to_display['included'][] = $service;
				}
			}

			if ( 'all' !== $type ) {
				if ( isset( $services_to_display[ $type ] ) ) {
					$filtered_services            = $services_to_display[ $type ];
					$services_to_display          = array();
					$services_to_display[ $type ] = $filtered_services;
				} else {
					$services_to_display = array();
				}
			}

			foreach ( $services_to_display as $key => $current_services ) {
				if ( ! ! $current_services ) {
					$_key = sanitize_key( $key );
					echo '<div class="yith-wcbk-shortcode-services yith-wcbk-shortcode-services-' . esc_attr( $_key ) . '">';

					if ( 'yes' === $show_title && ! empty( $services_labels[ $key ] ) ) {
						echo '<h3 class="yith-wcbk-shortcode-services__title">' . wp_kses_post( $services_labels[ $key ] ) . '</h3>';
					}

					foreach ( $current_services as $service ) {
						/**
						 * The service.
						 *
						 * @var YITH_WCBK_Service $service
						 */
						$help_tip = '';
						$info     = '';

						if ( 'yes' === $show_descriptions ) {
							$description = $service->get_description_html();
							if ( $description ) {
								$info .= '<div class="yith-wcbk-booking-service__description">' . wp_kses_post( $description ) . '</div>';
							}
						}

						if ( 'yes' === $show_prices ) {
							$pricing = $service->get_pricing_html( $product );

							$info .= '<div class="yith-wcbk-booking-service__pricing">' . wp_kses_post( $pricing ) . '</div>';
						}

						if ( $info ) {
							$help_tip = yith_wcbk_print_field(
								array(
									'type'  => 'help-tip-alt',
									'value' => $info,
								),
								false
							);

							$help_tip = apply_filters( 'yith_wcbk_shortcode_services_info_html', $help_tip, $info, $service, $product );
						}

						echo '<div class="yith-wcbk-shortcode-service yith-wcbk-shortcode-service--' . esc_attr( $service->get_slug() ) . '">';
						echo '<span class="yith-wcbk-shortcode-service__title">' . wp_kses_post( $service->get_name() ) . '</span>';
						echo $help_tip; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '</div>';
					}

					echo '</div>';
				}
			}
		}
	}
	?>
</div>
