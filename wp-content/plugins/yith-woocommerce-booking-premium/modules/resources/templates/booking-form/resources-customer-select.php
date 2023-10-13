<?php
/**
 * Booking form resources template.
 *
 * @var WC_Product_Booking $product
 * @package YITH\Booking\Modules\Resources\Templates
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! $product->has_resources() ) {
	return;
}

$field_label    = $product->get_resources_field_label();
$field_label    = ! ! $field_label ? $field_label : $product->get_resources_label();
$field_label    = ! ! $field_label ? $field_label : __( 'Resource', 'yith-booking-for-woocommerce' );
$layout         = $product->get_resources_layout();
$layout         = 'default' !== $layout ? $layout : yith_wcbk()->settings->get_resources_default_layout();
$placeholder    = $product->get_resources_field_placeholder();
$assignment     = $product->get_resource_assignment();
$resources_data = $product->get_resources_data();
$options        = array();
$use_images     = false;

foreach ( $resources_data as $resource_data ) {
	$resource = $resource_data->get_resource();
	if ( $resource ) {
		$pricing = $resource_data->get_pricing_html();
		if ( ! ! $pricing ) {
			$pricing = '+ ' . $pricing;
		}

		$options[ $resource->get_id() ] = array(
			'label'       => $resource->get_name(),
			'image_id'    => $resource->get_image_id(),
			'description' => $pricing,
		);

		if ( $resource->get_image_id() ) {
			$use_images = true;
		}
	}
}

$is_multiple = 'customer-select-more' === $assignment;
$selected    = yith_wcbk_get_query_string_param( 'resources' );
if ( $is_multiple ) {
	$selected = ! ! $selected && is_string( $selected ) ? explode( ',', $selected ) : array();
	$selected = array_filter( array_map( 'absint', $selected ) );
} else {
	$selected = ! ! $selected ? absint( $selected ) : '';
}

?>

<div class="yith-wcbk-form-section-resources-wrapper yith-wcbk-form-section-wrapper">
	<div class='yith-wcbk-form-section yith-wcbk-form-section-resources'>
		<label class='yith-wcbk-form-section__label yith-wcbk-booking-form__label'><?php echo esc_html( $field_label ); ?></label>
		<div class='yith-wcbk-form-section__content'>
			<?php
			yith_wcbk_print_field(
				array(
					'id'          => 'yith-wcbk-booking-resources-' . $product->get_id(),
					'name'        => 'resource_ids[]',
					'type'        => 'selector',
					'class'       => 'yith-wcbk-booking-resource',
					'field_class' => 'yith-wcbk-booking-form-additional-data',
					'use_images'  => $use_images,
					'multiple'    => $is_multiple,
					'allow_clear' => $is_multiple || ! $product->get_resource_is_required(),
					'options'     => $options,
					'placeholder' => $placeholder,
					'layout'      => $layout,
					'value'       => $selected,
				)
			);
			?>
		</div>
	</div>
</div>
