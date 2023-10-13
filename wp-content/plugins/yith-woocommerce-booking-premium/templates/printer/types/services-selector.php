<?php
/**
 * Services selector field.
 *
 * @var string              $id          The ID.
 * @var string              $name        The name.
 * @var string              $class       The name.
 * @var array               $value       The value.
 * @var string              $placeholder The placeholder.
 * @var YITH_WCBK_Service[] $services    The services.
 *
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! yith_wcbk_is_services_module_active() ) {
	return;
}

$value       = ! empty( $value ) && is_array( $value ) ? $value : array();
$placeholder = $placeholder ?? '';
$services    = $services ?? array();

$selected_names = '';
if ( $value ) {
	$selected_names = implode(
		', ',
		array_filter(
			array_map(
				function ( $service ) use ( $value ) {
					return in_array( $service->get_id(), $value, true ) ? $service->get_name() : '';
				},
				$services
			)
		)
	);
}

$label_class = ! ! $value ? 'yith-wcbk-services-selector__label--selected' : 'yith-wcbk-services-selector__label--placeholder';
?>

<div id="<?php echo esc_attr( $id ); ?>"
		class="yith-wcbk-services-selector <?php echo esc_attr( $class ); ?>"
		data-placeholder="<?php echo esc_attr( $placeholder ); ?>"
>
	<div class="yith-wcbk-services-selector__toggle-handler">
		<div class="yith-wcbk-services-selector__label__fake"></div>
		<div class="yith-wcbk-services-selector__label <?php echo esc_attr( $label_class ); ?>">
			<?php echo esc_html( ! ! $value ? $selected_names : $placeholder ); ?>
		</div>

	</div>
	<div class="yith-wcbk-services-selector__content">
		<div class="yith-wcbk-services-selector__services">
			<?php foreach ( $services as $service ) : ?>
				<?php
				$field = array(
					'type'           => 'checkbox-alt',
					'label'          => esc_html( $service->get_name() ),
					'checkbox_value' => $service->get_id(),
					'value'          => in_array( $service->get_id(), $value, true ) ? $service->get_id() : 'no',
					'class'          => 'yith-wcbk-services-selector__service',
					'data'           => array(
						'service-id' => $service->get_id(),
					),
				);

				if ( $id ) {
					$field['id'] = $id . '__' . $service->get_id();
				}

				if ( $name ) {
					$field['name'] = $name . '[]';
				}

				yith_wcbk_print_field( $field );
				?>
			<?php endforeach; ?>
		</div>
		<div class="yith-wcbk-services-selector__content__footer">
			<span class="yith-wcbk-services-selector__close"><?php esc_html_e( 'Save', 'yith-booking-for-woocommerce' ); ?></span>
		</div>
	</div>
</div>
