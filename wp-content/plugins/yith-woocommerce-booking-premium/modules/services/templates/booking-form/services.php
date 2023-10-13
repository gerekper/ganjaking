<?php
/**
 * Booking form services template.
 *
 * @var WC_Product_Booking $product
 *
 * @package YITH\Booking\Modules\Services\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$default_services = yith_wcbk_get_query_string_param( 'booking_services' );
$default_services = ! ! $default_services && is_string( $default_services ) ? explode( ',', $default_services ) : array();
$default_services = array_filter( array_map( 'absint', $default_services ) );

$services_labels = yith_wcbk_get_service_type_labels();
$services_labels = apply_filters( 'yith_wcbk_booking_form_services_labels', $services_labels, $product );

$service_info_layout = get_option( 'yith-wcbk-service-info-layout', 'tooltip' );
$service_info_layout = apply_filters( 'yith_wcbk_booking_form_service_info_layout', $service_info_layout, $product );
?>
<div class="yith-wcbk-form-section-services-wrapper yith-wcbk-form-section-wrapper">
	<?php if ( $product->has_services() ) : ?>
		<?php
		$services = $product->get_service_ids();
		?>
		<?php if ( ! ! $services && is_array( $services ) ) : ?>
			<?php
			$services_to_display = yith_wcbk_split_services_by_type( $services, false );
			$services_to_display = apply_filters( 'yith_wcbk_booking_form_services_to_display', $services_to_display, $services, $product );

			$show_included_services = yith_wcbk()->settings->show_included_services();
			?>
			<?php foreach ( $services_to_display as $key => $current_services ) : ?>
				<?php
				$show           = 'included' !== $key || $show_included_services;
				$css_type_class = 'yith-wcbk-booking-service--type-' . sanitize_key( $key );
				?>
				<div class='yith-wcbk-form-section yith-wcbk-form-section-services'>
					<?php if ( $show && ! empty( $services_labels[ $key ] ) && ! ! $current_services ) : ?>
						<label class='yith-wcbk-form-section__label yith-wcbk-booking-form__label'><?php echo esc_html( $services_labels[ $key ] ); ?></label>
					<?php endif; ?>
					<div class='yith-wcbk-form-section__content'>
						<?php foreach ( $current_services as $service ) : ?>
							<?php
							/**
							 * The service.
							 *
							 * @var YITH_WCBK_Service $service
							 */
							$field = array(
								'id'             => "yith-wcbk-booking-services-{$service->get_id()}",
								'name'           => 'booking_services[]',
								'checkbox_value' => $service->get_id(),
								'value'          => in_array( $service->get_id(), $default_services, true ) ? $service->get_id() : 'no',
								'class'          => "yith-wcbk-booking-service {$css_type_class}",
								'data'           => array(
									'service-id' => $service->get_id(),
								),
							);

							if ( $service->is_optional() ) {
								$field['type']  = 'checkbox-alt';
								$field['label'] = $service->get_name();
							} else {
								$field['type'] = 'hidden';
								if ( $show ) {
									$field['title'] = $service->get_name();
								}
							}

							$service_class = 'yith-wcbk-form-section-service';
							if ( ! $show ) {
								$service_class .= ' yith-wcbk-form-section-service--hidden';
							}

							$info      = $service->get_info_html(
								array(
									'product' => $product,
									'layout'  => $service_info_layout,
								)
							);
							$info_html = '';
							if ( $show ) {
								$info_html = ! ! $info ? yith_wcbk_print_field(
									array(
										'type'  => 'tooltip' === $service_info_layout ? 'help-tip-alt' : 'html',
										'value' => $info,
									),
									false
								) : '';
								$info_html = apply_filters( 'yith_wcbk_booking_form_service_info_html', $info_html, $info, $service, $product );
							}

							?>

							<div class='<?php echo esc_attr( $service_class ); ?>'>
								<?php
								yith_wcbk_print_field( $field );

								echo $info_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

								if ( $service->is_quantity_enabled() ) {
									$max_qty = $service->get_max_quantity();
									$max_qty = ! ! $max_qty ? $max_qty : '';

									// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
									yith_wcbk_print_field(
										array(
											'type'   => 'section',
											'class'  => 'yith-wcbk-booking-service-quantity__container',
											'fields' => array(
												'id'                => "yith-wcbk-booking-service-quantity-{$service->get_id()}",
												'name'              => "booking_service_quantities[{$service->get_id()}]",
												'type'              => $show ? 'number' : 'hidden',
												'value'             => $service->get_min_quantity(),
												'class'             => 'yith-wcbk-booking-service-quantity',
												'custom_attributes' => array(
													'min'  => $service->get_min_quantity(),
													'max'  => $max_qty,
													'step' => 1,
												),
											),
										)
									);
									// phpcs:enable
								}
								?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	<?php endif; ?>
</div>
