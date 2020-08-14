<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

$custom_attributes = defined( 'YITH_WCBM_PREMIUM' ) ? '' : array( 'disabled' => 'disabled' );

// get shipping classes
$shipping_classes = get_terms( 'product_shipping_class', array(
		'orderby' => 'name',
		'hide_empty' => false
) );

$options = array(
	'shipping-class-badge-options' => array(
		'title' => __( 'Shipping Class Badges', 'yith-woocommerce-badges-management' ),
		'type' => 'title',
		'desc' => '',
		'id' => 'yith-wcbm-shipping-class-badge-options'
	)
);

foreach ($shipping_classes as $shipping_class) {
	$id 	= $shipping_class->term_id;
	$name 	= $shipping_class->name;

	$options['shipping-class-badge-' . $id ] = array(
		'name'              => $name,
		'type'              => 'yith-field',
		'yith-type'         => 'custom',
		'action'            => 'yith_wcbm_print_badges_select',
		'desc'              => sprintf( __( 'Select the Badge for all products of shipping class "%s"', 'yith-woocommerce-badges-management' ), $name) ,
		'id'                => 'yith-wcbm-shipping-class-badge-' . $id,
		'custom_attributes' => $custom_attributes,
		'default'           => 'none'
	);
}

$options['shipping-class-badge-options-end'] = array(
	'type'      => 'sectionend',
	'id'        => 'yith-wcbm-shipping-class-badge-options'
);

$settings = array(
	'shipping-class'  => $options
);

return $settings;