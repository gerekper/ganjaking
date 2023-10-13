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

$assignment = $product->get_resource_assignment();
$templates  = array(
	'assign-all'           => 'booking-form/resources-assign-all.php',
	'customer-select-one'  => 'booking-form/resources-customer-select.php',
	'customer-select-more' => 'booking-form/resources-customer-select.php',
);

$template = $templates[ $assignment ] ?? '';

if ( $template ) {
	yith_wcbk_get_module_template( 'resources', $template, compact( 'product' ), 'single-product/add-to-cart/' );
}
