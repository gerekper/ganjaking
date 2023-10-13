<?php
/**
 * Booking form resources template.
 *
 * @var WC_Product_Booking $product
 *
 * @package YITH\Booking\Modules\Resources\Templates
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! $product->has_resources() ) {
	return;
}

$resources_data = $product->get_resources_data();
foreach ( $resources_data as $resource_data ) {
	yith_wcbk_print_field(
		array(
			'type'  => 'hidden',
			'id'    => 'yith-wcbk-booking-resource-' . $resource_data->get_resource_id() . '-for-' . $product->get_id(),
			'name'  => 'resource_ids[]',
			'class' => 'yith-wcbk-booking-form-additional-data',
			'value' => $resource_data->get_resource_id(),
		)
	);
}
